<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class MinimailController extends Controller
{
    public function loadMessages(Request $request): Response
    {
        $user = $request->attributes->get('hotelUser');
        abort_unless($user, 401);
        $label = (string) $request->input('label', 'inbox');
        if (! in_array($label, ['inbox', 'sent', 'trash', 'conversation'], true)) {
            $label = 'inbox';
        }
        $offset = max(0, (int) $request->input('start', 0));
        $conversationId = (int) $request->input('conversationId', 0);
        $unreadOnly = (string) $request->input('unreadOnly', '') === 'true';
        $total = $this->folderCount((int) $user->id, $label, $conversationId, $unreadOnly);
        $rows = $this->list((int) $user->id, $label, $offset, $conversationId, $unreadOnly);

        $html = view('hotel.habblet.minimail-list', [
            'label' => $label,
            'rows' => $rows,
            'total' => $total,
            'offset' => $offset,
            'unread' => $this->unreadCount((int) $user->id),
            'unreadOnly' => $unreadOnly,
            'hotelUser' => $user,
        ])->render();

        return response($html)->header('X-JSON', json_encode(['totalMessages' => $this->folderCount((int) $user->id, $label === 'inbox' ? 'inbox' : $label, $conversationId, false)]));
    }

    public function loadMessage(Request $request): View
    {
        $user = $request->attributes->get('hotelUser');
        abort_unless($user, 401);
        $id = (int) $request->input('messageId', $request->input('id', 0));
        $row = $this->message((int) $user->id, $id);
        abort_unless($row, 404);
        if ((int) $row->recipient_id === (int) $user->id && $row->read_at === null) {
            DB::connection('holodb')->table('phpretro_minimail')->where('id', $id)->where('recipient_id', $user->id)->whereNull('read_at')->update(['read_at' => time()]);
            $row->read_at = time();
        }

        return view('hotel.habblet.minimail-message', ['row' => $row, 'hotelUser' => $user]);
    }

    public function sendMessage(Request $request): Response
    {
        $user = $request->attributes->get('hotelUser');
        abort_unless($user, 401);
        $toName = trim((string) $request->input('recipient', $request->input('recipients', '')));
        $subject = trim((string) $request->input('subject', ''));
        $body = trim((string) $request->input('body', $request->input('message', '')));
        if ($toName === '' || $subject === '' || $body === '') {
            return response('Missing fields.', 400);
        }
        if (mb_strlen($subject, 'UTF-8') > 100 || mb_strlen($body, 'UTF-8') > 4096) {
            return response('Message too long.', 400);
        }
        $to = DB::connection('holodb')->table('users')->where('username', $toName)->first(['id']);
        if (! $to) {
            return response('Unknown recipient.', 404);
        }
        $id = DB::connection('holodb')->table('phpretro_minimail')->insertGetId([
            'sender_id' => $user->id,
            'recipient_id' => $to->id,
            'subject' => $subject,
            'body' => $body,
            'conversation_id' => 0,
            'sent_at' => time(),
            'read_at' => null,
            'deleted' => 0,
            'deleted_at' => null,
        ]);

        return response('OK')->header('X-JSON', json_encode(['messageId' => $id]));
    }

    public function deleteMessage(Request $request): Response
    {
        $user = $request->attributes->get('hotelUser');
        abort_unless($user, 401);
        $id = (int) $request->input('messageId', $request->input('id', 0));
        DB::connection('holodb')->table('phpretro_minimail')
            ->where('id', $id)->where('recipient_id', $user->id)
            ->update(['deleted' => 1, 'deleted_at' => time()]);

        return response('OK');
    }

    public function undeleteMessage(Request $request): Response
    {
        $user = $request->attributes->get('hotelUser');
        abort_unless($user, 401);
        $id = (int) $request->input('messageId', $request->input('id', 0));
        DB::connection('holodb')->table('phpretro_minimail')
            ->where('id', $id)->where('recipient_id', $user->id)
            ->update(['deleted' => 0, 'deleted_at' => null]);

        return response('OK');
    }

    public function emptyTrash(Request $request): Response
    {
        $user = $request->attributes->get('hotelUser');
        abort_unless($user, 401);
        DB::connection('holodb')->table('phpretro_minimail')
            ->where('recipient_id', $user->id)->where('deleted', 1)->delete();

        return response('OK');
    }

    public function preview(Request $request): View
    {
        return view('hotel.habblet.minimail-preview', [
            'subject' => (string) $request->input('subject', ''),
            'body' => (string) $request->input('body', ''),
            'hotelUser' => $request->attributes->get('hotelUser'),
        ]);
    }

    public function recipients(Request $request): Response
    {
        $user = $request->attributes->get('hotelUser');
        abort_unless($user, 401);
        $q = trim((string) $request->input('query', ''));
        if ($q === '') {
            return response('');
        }
        $rows = DB::connection('holodb')->table('messenger_friendships as f')
            ->join('users as u', 'u.id', '=', 'f.user_two_id')
            ->where('f.user_one_id', $user->id)
            ->where('u.username', 'like', $q.'%')
            ->orderBy('u.username')
            ->limit(10)
            ->get(['u.username']);
        $html = '';
        foreach ($rows as $row) {
            $html .= '<li>'.e($row->username).'</li>';
        }

        return response($html);
    }

    public function reportRefuse(): Response
    {
        return response('Reporting is handled by PolarIS, not this website.', 501);
    }

    private function unreadCount(int $userId): int
    {
        if (! $this->hasTable()) {
            return 0;
        }

        return (int) DB::connection('holodb')->table('phpretro_minimail')
            ->where('recipient_id', $userId)->where('deleted', 0)->whereNull('read_at')->count();
    }

    private function folderCount(int $userId, string $label, int $conversationId, bool $unreadOnly): int
    {
        if (! $this->hasTable()) {
            return 0;
        }
        $q = DB::connection('holodb')->table('phpretro_minimail');

        return match ($label) {
            'sent' => (int) $q->where('sender_id', $userId)->count(),
            'trash' => (int) $q->where('recipient_id', $userId)->where('deleted', 1)->count(),
            'conversation' => (int) $q->where('conversation_id', $conversationId)->where('conversation_id', '>', 0)
                ->where(fn ($inner) => $inner->where('sender_id', $userId)->orWhere('recipient_id', $userId))->count(),
            default => (int) ($unreadOnly
                ? $q->where('recipient_id', $userId)->where('deleted', 0)->whereNull('read_at')->count()
                : $q->where('recipient_id', $userId)->where('deleted', 0)->count()),
        };
    }

    private function list(int $userId, string $label, int $offset, int $conversationId, bool $unreadOnly): array
    {
        if (! $this->hasTable()) {
            return [];
        }
        $q = DB::connection('holodb')->table('phpretro_minimail as m');
        if ($label === 'sent') {
            $q->join('users as u', 'u.id', '=', 'm.recipient_id')->where('m.sender_id', $userId);
        } elseif ($label === 'trash') {
            $q->join('users as u', 'u.id', '=', 'm.sender_id')->where('m.recipient_id', $userId)->where('m.deleted', 1);
        } elseif ($label === 'conversation') {
            $q->join('users as u', 'u.id', '=', 'm.sender_id')
                ->where('m.conversation_id', $conversationId)->where('m.conversation_id', '>', 0)
                ->where(fn ($inner) => $inner->where('m.sender_id', $userId)->orWhere('m.recipient_id', $userId));
        } else {
            $q->join('users as u', 'u.id', '=', 'm.sender_id')->where('m.recipient_id', $userId)->where('m.deleted', 0);
            if ($unreadOnly) {
                $q->whereNull('m.read_at');
            }
        }

        return $q->orderByDesc('m.id')->offset($offset)->limit(10)
            ->get(['m.id', 'm.sender_id', 'm.recipient_id', 'm.subject', 'm.body', 'm.conversation_id', 'm.sent_at', 'm.read_at', 'm.deleted', 'u.username', 'u.look'])
            ->all();
    }

    private function message(int $userId, int $id): ?object
    {
        if ($id < 1 || ! $this->hasTable()) {
            return null;
        }

        $row = DB::connection('holodb')->table('phpretro_minimail as m')
            ->join('users as u', 'u.id', '=', 'm.sender_id')
            ->where('m.id', $id)
            ->first(['m.*', 'u.username', 'u.look']);
        if (! $row) {
            return null;
        }
        if ((int) $row->sender_id !== $userId && (int) $row->recipient_id !== $userId) {
            return null;
        }

        return $row;
    }

    private function hasTable(): bool
    {
        try {
            return Schema::connection('holodb')->hasTable('phpretro_minimail');
        } catch (\Throwable) {
            return false;
        }
    }
}
