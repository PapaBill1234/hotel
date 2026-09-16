<?php

namespace App\Http\Controllers;

use App\Support\Hotel;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class HabbletController extends Controller
{
    public function namecheck(Request $request): Response
    {
        $name = trim((string) $request->input('name', ''));
        $filter = preg_replace('/[^a-z\d\-=\?!@:\.]/i', '', $name) ?? '';

        $error = null;
        if ($name === '') {
            $error = 'Please enter a username.';
        } elseif ($filter !== $name) {
            $error = 'Sorry, but this username contains invalid characters.';
        } elseif (strlen($name) > 24) {
            $error = 'Sorry, but this username is too long.';
        } elseif (strncasecmp($name, 'MOD-', 4) === 0) {
            $error = 'This name is not allowed.';
        } elseif ($this->usernameTaken($name)) {
            $error = 'Sorry, but this username is taken. Please choose another one.';
        }

        $payload = $error ? ['registration_name' => $error] : (object) [];

        return response('')->header('X-JSON', json_encode($payload, JSON_UNESCAPED_UNICODE));
    }

    public function groupCreateForm(Request $request): View|Response
    {
        $user = $request->attributes->get('hotelUser');
        if (! $user) {
            return response('<p>Please sign in first.</p>', 401);
        }

        if ($user->credits < 10) {
            return view('hotel.habblet.group-not-enough');
        }

        return view('hotel.habblet.group-create-form', ['hotelUser' => $user]);
    }

    public function groupConfirm(Request $request): View|Response
    {
        $user = $request->attributes->get('hotelUser');
        if (! $user) {
            return response('<p>Please sign in first.</p>', 401);
        }

        return view('hotel.habblet.group-confirm', [
            'hotelUser' => $user,
            'name' => trim((string) $request->input('name', '')),
            'description' => trim((string) $request->input('description', '')),
        ]);
    }

    public function groupPurchase(Request $request): View|Response
    {
        if (! $request->attributes->get('hotelUser')) {
            return response('<p>Please sign in first.</p>', 401);
        }

        return view('hotel.habblet.group-club-required');
    }

    public function quickmenu(Request $request, string $key): Response
    {
        $user = $request->attributes->get('hotelUser');
        if (! $user) {
            return response('<ul><li>Please sign in first.</li></ul>', 401);
        }

        $html = match ($key) {
            'friends_all' => $this->quickFriends((int) $user->id),
            'groups' => $this->quickGroups((int) $user->id),
            'rooms' => $this->quickRooms((int) $user->id),
            default => null,
        };

        if ($html === null) {
            return response('Unknown menu.', 400);
        }

        return response($html);
    }

    public function habboSearch(Request $request): Response
    {
        if (! $request->attributes->get('hotelUser')) {
            return response('<ul class="habblet-list"></ul>', 401);
        }

        $search = trim((string) $request->input('searchString', ''));
        if ($search === '' || ! Schema::connection('holodb')->hasTable('users')) {
            return response('<p>No results.</p>');
        }

        $rows = DB::connection('holodb')->table('users')
            ->where('username', 'like', '%'.$search.'%')
            ->orderBy('username')
            ->limit(10)
            ->get(['id', 'username', 'look', 'online']);

        $html = '<ul class="habblet-list">';
        $i = 0;
        foreach ($rows as $row) {
            $i++;
            $even = $i % 2 ? 'even' : 'odd';
            $online = ((string) $row->online !== '0') ? 'online' : 'offline';
            $html .= '<li class="'.$even.' '.$online.'" homeurl="/home/'.e($row->username).'">'
                .'<img src="'.e(\App\Support\Hotel::avatarUrl((string) $row->look, 's,2,2,sml,1,0')).'" alt="" /> '
                .'<a href="/home/'.e($row->username).'">'.e($row->username).'</a></li>';
        }
        $html .= '</ul>';
        if ($i === 0) {
            $html = '<p>No results.</p>';
        }

        return response($html);
    }

    public function habboCount(): Response
    {
        $count = \App\Support\Hotel::onlineCount();

        return response('')->header('X-JSON', json_encode(['habboCountText' => $count.' members online']));
    }

    public function promoHabbos(): Response
    {
        $rows = [];
        try {
            if (Schema::connection('holodb')->hasTable('users')) {
                $rows = DB::connection('holodb')->table('users')->orderByDesc('id')->limit(20)->get(['id', 'username', 'motto', 'look']);
            }
        } catch (\Throwable) {
            $rows = [];
        }
        $xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n<habbos>\n";
        foreach ($rows as $row) {
            $xml .= sprintf(
                '<habbo id="%d" name="%s" motto="%s" url="/home/%s" image="%s" badge="" status="0" />'."\n",
                (int) $row->id,
                htmlspecialchars((string) $row->username, ENT_QUOTES, 'UTF-8'),
                htmlspecialchars((string) $row->motto, ENT_QUOTES, 'UTF-8'),
                htmlspecialchars((string) $row->username, ENT_QUOTES, 'UTF-8'),
                htmlspecialchars(\App\Support\Hotel::avatarUrl((string) $row->look, 'b,4,3,sml,1,0'), ENT_QUOTES, 'UTF-8')
            );
        }
        $xml .= '</habbos>';

        return response($xml, 200)->header('Content-Type', 'text/xml; charset=UTF-8');
    }

    public function emailcheck(Request $request): Response
    {
        $email = trim((string) $request->input('email', ''));
        if (strlen($email) < 6 || ! preg_match('/^[a-z0-9_\.-]+@([a-z0-9]+([\-]+[a-z0-9]+)*\.)+[a-z]{2,7}$/i', $email)) {
            return response('register.message.invalid_email');
        }

        return response('register.message.email_chars_ok')->header('X-JSON', '"emailOk"');
    }

    public function passwordcheck(Request $request): Response
    {
        $password = (string) $request->input('password', '');
        if (strlen($password) < 6) {
            return response('register.tooltip.passwordtooshort');
        }

        return response('register.tooltip.passwordsuccess')->header('X-JSON', '"charOk"');
    }

    public function updateMotto(Request $request): Response
    {
        $user = $request->attributes->get('hotelUser');
        if (! $user) {
            return response('Please sign in first.', 401);
        }
        $motto = trim((string) $request->input('motto', ''));
        if (strlen($motto) > 38) {
            $motto = substr($motto, 0, 38);
        }
        DB::connection('holodb')->update(
            'UPDATE users SET motto = ?, look = ?, gender = ? WHERE id = ?',
            [$motto, (string) $user->look, (string) $user->gender, (int) $user->id]
        );
        $fresh = (string) DB::connection('holodb')->table('users')->where('id', $user->id)->value('motto');

        return response(e($fresh));
    }

    public function redeemVoucher(Request $request): View|Response
    {
        $user = $request->attributes->get('hotelUser');
        if (! $user) {
            return response('<p>Please sign in first.</p>', 401);
        }

        return view('hotel.habblet.voucher', [
            'credits' => (int) $user->credits,
            'code' => trim((string) $request->input('voucherCode', $request->query('voucherCode', ''))),
        ]);
    }

    public function collectiblesConfirm(Request $request): View|Response
    {
        if (! $request->attributes->get('hotelUser')) {
            return response('<p>Please sign in first.</p>', 401);
        }
        $item = null;
        try {
            if (Schema::connection('holodb')->hasTable('phpretro_collectibles')) {
                $item = DB::connection('holodb')->table('phpretro_collectibles')->orderByDesc('id')->first();
            }
        } catch (\Throwable) {
        }

        return view('hotel.habblet.collectibles-confirm', [
            'item' => $item,
            'name' => $item->name ?? 'No collectible this month.',
        ]);
    }

    public function collectiblesPurchase(Request $request): Response
    {
        if (! $request->attributes->get('hotelUser')) {
            return response('<p>Please sign in first.</p>', 401);
        }

        return Hotel::clientHandoff(
            'Buy collectables in the hotel',
            'This website does not grant PolarIS furniture. Claim collectables from the hotel catalog.'
        );
    }

    public function confirmAddFriend(Request $request): Response
    {
        $user = $request->attributes->get('hotelUser');
        if (! $user) {
            return response('<p>Please sign in first.</p>', 401);
        }
        $id = (int) $request->input('accountId', 0);
        $name = '';
        try {
            $name = (string) (DB::connection('holodb')->table('users')->where('id', $id)->value('username') ?? '');
        } catch (\Throwable) {
        }
        if ($name === '') {
            return response('Account not found.', 404);
        }

        return response('<p>Are you sure you want to add '.e($name).' to your friend list?</p>'
            .'<p><a href="#" class="new-button done"><b>Cancel</b><i></i></a>'
            .'<a href="#" class="new-button add-continue"><b>Continue</b><i></i></a></p>');
    }

    public function addFriend(Request $request): Response
    {
        if (! $request->attributes->get('hotelUser')) {
            return response('<p>Please sign in first.</p>', 401);
        }

        return response('<div id="avatar-habblet-dialog-body" class="topdialog-body"><ul>'
            .'<li>PolarIS messenger_friendrequests is not written from this website. Add friends in the hotel.</li></ul>'
            .'<p><a href="#" class="new-button done"><b>Done</b><i></i></a></p></div>');
    }

    public function loadEvents(Request $request): View|Response
    {
        if (! $request->attributes->get('hotelUser')) {
            return response('<ul class="habblet-list"></ul>', 401);
        }
        $category = (int) $request->input('eventTypeId', 1);
        if ($category < 1 || $category > 11) {
            $category = 1;
        }
        $rows = collect();
        try {
            if (Schema::connection('holodb')->hasTable('room_promotions')) {
                $now = time();
                $rows = DB::connection('holodb')->table('room_promotions as p')
                    ->join('rooms as r', 'r.id', '=', 'p.room_id')
                    ->where('p.category', $category)
                    ->where('p.start_timestamp', '<=', $now)
                    ->where('p.end_timestamp', '>', $now)
                    ->orderByDesc('p.start_timestamp')
                    ->get(['p.room_id', 'p.title', 'p.description', 'p.start_timestamp', 'r.owner_name', 'r.users', 'r.users_max']);
            }
        } catch (\Throwable) {
        }

        return view('hotel.habblet.events', ['rows' => $rows]);
    }

    public function clubEnddate(Request $request): Response
    {
        $user = $request->attributes->get('hotelUser');
        if (! $user) {
            return response('<p>Please sign in first.</p>', 401);
        }
        $days = 0;
        try {
            if (Schema::connection('holodb')->hasTable('users_settings')) {
                $expires = (int) DB::connection('holodb')->table('users_settings')->where('user_id', $user->id)->value('club_expire_timestamp');
                if ($expires > time()) {
                    $days = (int) ceil(($expires - time()) / 86400);
                }
            }
        } catch (\Throwable) {
        }
        if ($days < 1) {
            return response('<p>You are not a member of '.e(Hotel::shortname()).' Club</p>');
        }

        return response('<p>You Currently Have '.$days.' club days left</p>');
    }

    public function clubSubscribe(): Response
    {
        return Hotel::clientHandoff(
            'Buy club in the hotel',
            'This website cannot take coins or grant club membership. Buy it from the hotel catalog.'
        );
    }

    public function clubGift(): Response
    {
        return response('<div id="hc-gift-catalog"><p>Preview only. PolarIS club gifts are claimed in the hotel client.</p></div>');
    }

    public function tagSearch(Request $request): View
    {
        $search = trim((string) $request->input('tag', $request->query('tag', '')));
        $count = Hotel::tagCount($search);
        $rows = collect();
        if ($count > 0 && Schema::connection('holodb')->hasTable('users_settings')) {
            $rows = DB::connection('holodb')->table('users_settings as s')
                ->join('users as u', 'u.id', '=', 's.user_id')
                ->whereRaw("INSTR(';' || s.tags || ';', ?) > 0", [';'.$search.';'])
                ->orderByDesc('u.id')
                ->limit(10)
                ->get(['u.id', 'u.username', 'u.look', 'u.motto']);
        }

        return view('hotel.habblet.tag-search', [
            'search' => $search,
            'count' => $count,
            'rows' => $rows,
        ]);
    }

    public function tagMatch(Request $request): Response
    {
        $user = $request->attributes->get('hotelUser');
        if (! $user) {
            return response('<div class="tag-match-error">Please sign in first.</div>', 401);
        }
        $name = trim((string) $request->input('friendName', ''));
        $id = 0;
        try {
            $id = (int) DB::connection('holodb')->table('users')->where('username', $name)->value('id');
        } catch (\Throwable) {
        }
        if ($id < 1) {
            return response('<div class="tag-match-error">Friend not found.</div>');
        }
        $mine = array_map('strtolower', Hotel::userTags((int) $user->id));
        $theirs = array_map('strtolower', Hotel::userTags($id));
        $percent = count($mine) ? (int) ceil(count(array_intersect($mine, $theirs)) * 100 / count($mine)) : 0;

        return response('<div id="tag-match-value" style="display:none">'.$percent.'</div>'
            .'<div id="tag-match-value-display">'.$percent.' %</div>'
            .'<div id="tag-match-slogan" style="display:none">Match result</div>');
    }

    public function tagFight(Request $request): Response
    {
        $tag1 = trim((string) $request->input('tag1', ''));
        $tag2 = trim((string) $request->input('tag2', ''));
        $count1 = Hotel::tagCount($tag1);
        $count2 = Hotel::tagCount($tag2);
        $end = $count1 === $count2 ? 0 : ($count1 > $count2 ? 2 : 1);
        $winner = $end === 0 ? 'Tie' : 'Winner';

        return response('<div id="fightResultCount" class="fight-result-count">'
            .$winner.'<br />'
            .e($tag1).' ('.$count1.') hits<br />'
            .e($tag2).' ('.$count2.') hits<p>User tags only; group tags are unavailable.</p></div>'
            .'<div class="fight-image"><img src="/web-gallery/images/tagfight/tagfight_end_'.$end.'.gif" alt="" name="fightanimation" id="fightanimation" />'
            .'<a id="tag-fight-button-new" href="#" class="new-button" onclick="TagFight.newFight(); return false;"><b>Again</b><i></i></a>'
            .'<a id="tag-fight-button" href="#" style="display:none" class="new-button" onclick="TagFight.init(); return false;"><b>Fight</b><i></i></a></div>');
    }

    public function inviteLink(Request $request): Response
    {
        $user = $request->attributes->get('hotelUser');
        if (! $user) {
            return response('<p>Please sign in first.</p>', 401);
        }

        return response('<h3>Enjoy more</h3><div class="copytext"><p>Send this link to a friend.</p>'
            .'<textarea cols="50" rows="2" onclick="this.focus();this.select()" readonly="readonly" style="width:100%">/register?referral='.(int) $user->id.'</textarea></div>');
    }

    public function removeFeedItem(): Response
    {
        return response('');
    }

    public function wardrobeStore(): Response
    {
        return Hotel::unavailable('PolarIS users_wardrobe is not written from this website. Save outfits in the hotel.');
    }

    public function nullReturn(Request $request): Response
    {
        return response((string) $request->query('return', ''));
    }

    public function localizations(): Response
    {
        $json = '{
 "success" : {
		"title" : "Success",
		"message" : "The report was sent.",
		"btnText" : "Close"
	},
 "error" : {
		"title" : "Error",
		"message" : "The report could not be sent.",
		"btnText" : "Close"
	},
 "spam" : {
		"title" : "Spam",
		"message" : "Please wait before sending another report.",
		"btnText" : "Close"
	},
 "name" : {
		"title" : "Name",
		"message" : "Report this name?",
		"btnCancelText" : "Cancel",
		"btnReportText" : "Report"
	},
 "room" : {
		"title" : "Room",
		"message" : "Report this room?",
		"btnCancelText" : "Cancel",
		"btnReportText" : "Report"
	},
 "motto" : {
		"title" : "Motto",
		"message" : "Report this motto?",
		"btnCancelText" : "Cancel",
		"btnReportText" : "Report"
	},
 "stickie" : {
		"title" : "Note",
		"message" : "Report this note?",
		"btnCancelText" : "Cancel",
		"btnReportText" : "Report"
	},
 "guestbook" : {
		"title" : "Guestbook",
		"message" : "Report this guestbook entry?",
		"btnCancelText" : "Cancel",
		"btnReportText" : "Report"
	},
 "discussionpost" : {
		"title" : "Post",
		"message" : "Report this discussion post?",
		"btnCancelText" : "Cancel",
		"btnReportText" : "Report"
	}
}';

        return response($json)->header('Content-Type', 'application/json');
    }

    public function addReport(Request $request): Response
    {
        $user = $request->attributes->get('hotelUser');
        if (! $user) {
            return response('ERROR', 401);
        }
        if (Schema::connection('holodb')->hasTable('phpretro_object_reports')) {
            DB::connection('holodb')->table('phpretro_object_reports')->insert([
                'reporter_id' => (int) $user->id,
                'object_type' => substr((string) $request->query('type', $request->input('type', 'name')), 0, 32),
                'object_id' => (int) $request->input('objectId', 0),
                'reason' => substr((string) $request->input('reason', ''), 0, 255),
                'created_at' => time(),
            ]);
        }

        return response('SUCCESS');
    }

    public function reportUser(Request $request): Response
    {
        $user = $request->attributes->get('hotelUser');
        if (! $user) {
            return response('Invalid report', 401);
        }
        $reported = (int) $request->input('reported_user_id', 0);
        $reason = trim((string) $request->input('reason', ''));
        $evidence = trim((string) $request->input('evidence', ''));
        if ($reported < 1 || $reported === (int) $user->id || $reason === '' || $evidence === '') {
            return response('Invalid report', 422);
        }
        if (! Schema::connection('holodb')->hasTable('phpretro_user_reports')) {
            return response('{"ok":true}')->header('Content-Type', 'application/json');
        }
        $exists = DB::connection('holodb')->table('users')->where('id', $reported)->exists();
        if (! $exists) {
            return response('User not found', 404);
        }
        DB::connection('holodb')->table('phpretro_user_reports')->insert([
            'reporter_id' => (int) $user->id,
            'reported_user_id' => $reported,
            'reason' => $reason,
            'evidence' => $evidence,
            'status' => 'open',
            'action_notes' => '',
            'created_at' => time(),
        ]);

        return response(json_encode(['ok' => true]))->header('Content-Type', 'application/json');
    }

    public function myTags(Request $request): Response
    {
        $user = $request->attributes->get('hotelUser');
        $tags = $user ? Hotel::userTags((int) $user->id) : [];
        $html = '<div class="habblet" id="my-tags-list"><ul class="tag-list">';
        foreach ($tags as $tag) {
            $html .= '<li><a class="tag" href="/tag/'.rawurlencode($tag).'">'.e($tag).'</a></li>';
        }
        $html .= '</ul><p>Manage your tags in the hotel client.</p></div>';

        return response($html);
    }

    public function traxUnavailable(): Response
    {
        return Hotel::unavailable('Homes Trax playback is unavailable.');
    }

    public function friendManagement(Request $request): View|Response
    {
        $user = $request->attributes->get('hotelUser');
        if (! $user) {
            return response('<p>Please sign in first.</p>', 401);
        }
        $pageSize = (int) $request->query('pageSize', 30);
        if (! in_array($pageSize, [30, 50, 100], true)) {
            $pageSize = 30;
        }
        $search = trim((string) $request->input('searchString', ''));
        $friends = collect();
        try {
            if (Schema::connection('holodb')->hasTable('messenger_friendships')) {
                $q = DB::connection('holodb')->table('messenger_friendships as f')
                    ->join('users as u', 'u.id', '=', 'f.user_two_id')
                    ->where('f.user_one_id', $user->id);
                if ($search !== '') {
                    $q->where('u.username', 'like', '%'.$search.'%');
                }
                $friends = $q->orderBy('u.username')->limit($pageSize)->get(['u.id', 'u.username', 'u.last_online']);
            }
        } catch (\Throwable) {
        }

        return view('hotel.habblet.friend-management', [
            'friends' => $friends,
            'pageSize' => $pageSize,
        ]);
    }

    public function friendManagementRefuse(): Response
    {
        return Hotel::unavailable('PolarIS messenger_friendships is not written from this website. Manage friends in the hotel.');
    }

    public function discussionsNewtopic(): View
    {
        return view('hotel.habblet.forum-newtopic');
    }

    public function discussionsPreview(Request $request): View|Response
    {
        $user = $request->attributes->get('hotelUser');
        if (! $user) {
            return response('<p>Please sign in first.</p>', 401);
        }
        $message = nl2br(e((string) $request->input('message', '')));
        $name = e((string) $request->input('topicName', ''));
        $online = ((string) $user->online !== '0') ? 'online_anim' : 'offline';

        return view('hotel.habblet.forum-preview', [
            'author' => $user,
            'online' => $online,
            'name' => $name,
            'message' => $message,
        ]);
    }

    public function discussionsConfirmDelete(): Response
    {
        return response('<p>Confirm delete?</p><p>'
            .'<a href="#" class="new-button" id="discussion-action-cancel"><b>Cancel</b><i></i></a>'
            .'<a href="#" class="new-button" id="discussion-action-ok"><b>OK</b><i></i></a></p><div class="clear"></div>');
    }

    public function discussionsPing(): Response
    {
        return response('')->header('X-JSON', json_encode(['privilegeLevel' => '1']));
    }

    public function discussionsRefuse(): Response
    {
        return Hotel::unavailable('PolarIS guilds_forums is not written from this website. Post in the hotel or from a later PolarIS-backed forum.');
    }

    private function quickFriends(int $userId): string
    {
        $rows = [];
        try {
            if (Schema::connection('holodb')->hasTable('messenger_friendships')) {
                $rows = DB::connection('holodb')->table('messenger_friendships as f')
                    ->join('users as u', 'u.id', '=', 'f.user_two_id')
                    ->where('f.user_one_id', $userId)
                    ->orderBy('u.username')
                    ->get(['u.id', 'u.username', 'u.online']);
            }
        } catch (\Throwable) {
            $rows = [];
        }

        if ($rows === [] || (is_countable($rows) && count($rows) === 0)) {
            return '<ul id="quickmenu-friends"><li>You don\'t have any friends yet</li></ul>';
        }

        $html = '';
        foreach (['online', 'offline'] as $status) {
            $html .= '<ul id="'.$status.'-friends">';
            $i = 0;
            foreach ($rows as $row) {
                $online = (string) $row->online !== '0';
                if ($online !== ($status === 'online')) {
                    continue;
                }
                $html .= '<li class="'.($i++ % 2 ? 'odd' : 'even').'"><a href="/home/'.e($row->username).'">'.e($row->username).'</a></li>';
            }
            $html .= '</ul>';
        }

        return $html;
    }

    private function quickGroups(int $userId): string
    {
        $rows = [];
        try {
            if (Schema::connection('holodb')->hasTable('guilds_members')) {
                $rows = DB::connection('holodb')->table('guilds_members as m')
                    ->join('guilds as g', 'g.id', '=', 'm.guild_id')
                    ->where('m.user_id', $userId)
                    ->whereIn('m.level_id', [0, 1, 2])
                    ->orderBy('g.name')
                    ->get(['g.id', 'g.name', 'g.room_id', 'g.user_id', 'm.level_id']);
            }
        } catch (\Throwable) {
            $rows = [];
        }

        $html = '<ul id="quickmenu-groups">';
        $any = false;
        foreach ($rows as $row) {
            $any = true;
            $html .= '<li>';
            if ((int) $row->room_id > 0) {
                $html .= '<a class="group-room" href="/client?forwardId=2&roomId='.(int) $row->room_id.'"></a>';
            }
            if ((int) $row->user_id === $userId) {
                $html .= '<div class="owned-group"></div>';
            } elseif ((int) $row->level_id === 1) {
                $html .= '<div class="admin-group"></div>';
            }
            $html .= '<a href="/groups/'.(int) $row->id.'/id">'.e($row->name).'</a></li>';
        }
        if (! $any) {
            $html .= '<li>You don\'t belong to any groups yet</li>';
        }
        $html .= '</ul>';

        return $html;
    }

    private function quickRooms(int $userId): string
    {
        $rows = [];
        try {
            if (Schema::connection('holodb')->hasTable('rooms')) {
                $rows = DB::connection('holodb')->table('rooms')->where('owner_id', $userId)->orderBy('name')->get(['id', 'name']);
            }
        } catch (\Throwable) {
            $rows = [];
        }

        $html = '<ul id="quickmenu-rooms">';
        $any = false;
        foreach ($rows as $row) {
            $any = true;
            $html .= '<li><a id="room-navigation-link_'.(int) $row->id.'" href="/client?forwardId=2&roomId='.(int) $row->id.'">'.e($row->name).'</a></li>';
        }
        if (! $any) {
            $html .= '<li>You don\'t have any rooms yet</li>';
        }
        $html .= '</ul>';

        return $html;
    }

    private function usernameTaken(string $name): bool
    {
        try {
            if (! Schema::connection('holodb')->hasTable('users')) {
                return false;
            }

            return DB::connection('holodb')->table('users')->where('username', $name)->exists();
        } catch (\Throwable) {
            return false;
        }
    }
}
