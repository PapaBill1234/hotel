<?php

namespace App\Http\Controllers;

use App\Support\Hotel;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class GroupController extends Controller
{
    public function show(Request $request, int $id): View
    {
        $guild = $this->guild($id);
        abort_unless($guild, 404);

        $viewer = $request->attributes->get('hotelUser');
        $homes = new \App\Support\Homes((int) ($viewer?->id ?? 0));
        $members = $this->members($id);
        $memberCount = $members->count();
        $widgets = $homes->displayGroupLayouts($id);
        $placed = $homes->placedItems(0, $id);
        $stickers = array_values(array_filter($placed, fn ($i) => $i->item_type === 'sticker'));
        $stickies = array_values(array_filter($placed, fn ($i) => $i->item_type === 'stickie'));
        $isOwner = $viewer && (int) $viewer->id === (int) $guild->user_id;
        $edit = $isOwner && (int) $request->session()->get('hotel.group_page_edit', 0) === $id;
        $guestbookWidget = collect($widgets)->first(fn ($w) => $w->widget_key === 'guestbookwidget');

        return view('hotel.group', [
            'pageId' => 'home',
            'pageName' => $guild->name.' Group Page',
            'bodyId' => $edit ? 'editmode' : 'viewmode',
            'cat' => 'community',
            'hotelUser' => $viewer,
            'homes' => $homes,
            'guild' => $guild,
            'members' => $members,
            'memberCount' => $memberCount,
            'widgets' => $widgets,
            'stickers' => $stickers,
            'stickies' => $stickies,
            'background' => $homes->backgroundClass(0, $id),
            'isMember' => $viewer && $members->contains(fn ($m) => (int) $m->user_id === (int) $viewer->id),
            'isOwner' => $isOwner,
            'edit' => $edit,
            'ownerName' => $this->username((int) $guild->user_id),
            'joinNotice' => $request->session()->get('group_notice'),
            'guestbook' => $guestbookWidget ? $homes->guestbookEntriesForWidget($guestbookWidget) : [],
        ]);
    }

    public function discussions(Request $request, int $id, ?int $thread = null): View
    {
        $guild = $this->guild($id);
        abort_unless($guild, 404);

        $viewer = $request->attributes->get('hotelUser');
        $threads = collect();
        $threadRow = null;
        $posts = collect();
        $posters = [];
        $threadId = $thread;

        if (Schema::connection('holodb')->hasTable('guilds_forums_threads')) {
            if ($threadId) {
                $threadRow = DB::connection('holodb')->table('guilds_forums_threads')
                    ->where('id', $threadId)->where('guild_id', $id)->first();
                abort_unless($threadRow, 404);
                if (Schema::connection('holodb')->hasTable('guilds_forums_comments')) {
                    $posts = DB::connection('holodb')->table('guilds_forums_comments')
                        ->where('thread_id', $threadId)
                        ->orderBy('created_at')
                        ->get();
                    $ids = $posts->pluck('user_id')->unique()->filter()->all();
                    if ($ids !== [] && Schema::connection('holodb')->hasTable('users')) {
                        $posters = DB::connection('holodb')->table('users')->whereIn('id', $ids)->get()->keyBy('id')->all();
                    }
                }
            } else {
                $threads = DB::connection('holodb')->table('guilds_forums_threads')
                    ->where('guild_id', $id)
                    ->orderByDesc('pinned')
                    ->orderByDesc('updated_at')
                    ->get();
            }
        }

        return view('hotel.discussions', [
            'pageId' => 'home',
            'pageName' => $guild->name.' Group Page',
            'bodyId' => 'viewmode',
            'cat' => 'community',
            'hotelUser' => $viewer,
            'guild' => $guild,
            'threads' => $threads,
            'thread' => $threadRow,
            'posts' => $posts,
            'posters' => $posters,
            'forumNotice' => $request->session()->get('group_notice'),
        ]);
    }

    public function join(Request $request): RedirectResponse|Response
    {
        $id = (int) $request->query('groupId', $request->input('groupId', 0));
        $message = 'PolarIS guilds_members is not written from this website. Join the group from the hotel client.';
        if ($request->isMethod('get')) {
            return redirect('/groups/'.$id.'/id')->with('group_notice', $message);
        }

        return response('<p>'.e($message).'</p><p><a href="#" class="new-button" id="group-action-ok"><b>OK</b><i></i></a></p><div class="clear"></div>');
    }

    public function leave(Request $request): RedirectResponse|Response
    {
        $id = (int) $request->query('groupId', $request->input('groupId', 0));
        $message = 'PolarIS guilds_members is not written from this website. Leave the group from the hotel client.';
        if ($request->isMethod('get')) {
            return redirect('/groups/'.$id.'/id')->with('group_notice', $message);
        }

        return response('<p>'.e($message).'</p><p><a href="#" class="new-button" id="group-action-ok"><b>OK</b><i></i></a></p><div class="clear"></div>');
    }

    public function settings(Request $request): View|Response
    {
        $user = $request->attributes->get('hotelUser');
        if (! $user) {
            return response('<p>Please sign in first.</p>', 401);
        }
        $id = (int) $request->input('groupId', $request->query('id', 0));
        $guild = $this->guild($id);
        if (! $guild || (int) $guild->user_id !== (int) $user->id) {
            return response('<p>Not permitted.</p>', 403);
        }

        return view('hotel.habblet.group-settings', [
            'guild' => $guild,
            'alias' => $this->aliasFor($id),
        ]);
    }

    public function updateSettings(): Response
    {
        return Hotel::unavailable('PolarIS guilds is not written from this website. Change group settings in the hotel.');
    }

    public function badgeEditor(): Response
    {
        return Hotel::clientHandoff(
            'Edit group badges in the hotel',
            'This website cannot save group badges. The Flash editor uses a different part encoding.'
        );
    }

    public function updateBadge(): Response
    {
        return Hotel::unavailable('Use the game client to edit group badges. The legacy Flash editor uses a different part encoding from PolarIS.');
    }

    public function checkUrl(Request $request): Response
    {
        $user = $request->attributes->get('hotelUser');
        if (! $user) {
            return response('ERROR Please sign in first.', 401);
        }
        $url = strtolower(trim((string) $request->input('url', '')));
        $id = (int) $request->input('groupId', $request->query('id', 0));
        if ($url === '' || ! preg_match('/^[a-z0-9_-]{3,30}$/', $url)) {
            return response('ERROR This URL is not valid.');
        }
        if (Schema::connection('holodb')->hasTable('phpretro_group_url_aliases')) {
            $taken = DB::connection('holodb')->table('phpretro_group_url_aliases')
                ->where('alias', $url)
                ->where('guild_id', '!=', $id)
                ->exists();
            if ($taken) {
                return response('ERROR This URL is not valid.');
            }
        }

        return response('Your alias: /groups/'.e($url).'. You cannot alter it later.');
    }

    public function confirmLeave(): View
    {
        return view('hotel.habblet.group-confirm-action', [
            'message' => 'Are you sure you want to leave this group?',
        ]);
    }

    public function confirmDelete(Request $request): View
    {
        $id = (int) $request->input('groupId', $request->query('id', 0));
        $guild = $this->guild($id);
        $name = $guild->name ?? 'this group';

        return view('hotel.habblet.group-confirm-action', [
            'message' => 'Are you sure you want to delete '.$name.'?',
        ]);
    }

    public function deleteGroup(): Response
    {
        return Hotel::unavailable('PolarIS guilds is not deleted from this website. Delete the group in the hotel.');
    }

    public function confirmFavorite(Request $request): View
    {
        $id = (int) $request->input('groupId', $request->query('id', 0));
        $guild = $this->guild($id);
        $name = $guild->name ?? 'this group';

        return view('hotel.habblet.group-confirm-action', [
            'message' => 'Are you sure you want to select '.$name.' as your favorite group?',
        ]);
    }

    public function confirmDeselectFavorite(): View
    {
        return view('hotel.habblet.group-confirm-action', [
            'message' => 'Are you sure you want to remove this group as your favorite?',
        ]);
    }

    public function selectFavorite(): Response
    {
        return Hotel::unavailable('PolarIS users_settings.guild_id is not written from this website. Set your favorite group in the hotel.');
    }

    public function memberBatchConfirm(Request $request, string $op): View
    {
        $label = str_replace('_', ' ', $op);

        return view('hotel.habblet.group-confirm-action', [
            'message' => 'Are you sure you want to '.$label.' the selected members?',
        ]);
    }

    public function memberBatchRefuse(): Response
    {
        return Hotel::unavailable('PolarIS guilds_members is not written from this website. Manage members in the hotel.');
    }

    public function showAlias(Request $request, string $alias): View
    {
        $id = 0;
        try {
            if (Schema::connection('holodb')->hasTable('phpretro_group_url_aliases')) {
                $id = (int) DB::connection('holodb')->table('phpretro_group_url_aliases')->where('alias', $alias)->value('guild_id');
            }
        } catch (\Throwable) {
        }
        abort_unless($id > 0, 404);

        return $this->show($request, $id);
    }

    private function aliasFor(int $guildId): string
    {
        try {
            if (Schema::connection('holodb')->hasTable('phpretro_group_url_aliases')) {
                return (string) (DB::connection('holodb')->table('phpretro_group_url_aliases')->where('guild_id', $guildId)->value('alias') ?? '');
            }
        } catch (\Throwable) {
        }

        return '';
    }

    private function guild(int $id): ?object
    {
        try {
            if (! Schema::connection('holodb')->hasTable('guilds')) {
                return null;
            }

            return DB::connection('holodb')->table('guilds')->where('id', $id)->first();
        } catch (\Throwable) {
            return null;
        }
    }

    private function members(int $guildId)
    {
        try {
            if (! Schema::connection('holodb')->hasTable('guilds_members')) {
                return collect();
            }

            return DB::connection('holodb')->table('guilds_members as m')
                ->join('users as u', 'u.id', '=', 'm.user_id')
                ->where('m.guild_id', $guildId)
                ->whereIn('m.level_id', [0, 1, 2])
                ->orderBy('m.level_id')
                ->orderBy('u.username')
                ->get(['m.user_id', 'm.level_id', 'u.username', 'u.look', 'u.motto']);
        } catch (\Throwable) {
            return collect();
        }
    }

    private function groupWidgets(int $guildId): array
    {
        try {
            if (Schema::connection('holodb')->hasTable('phpretro_myhabbo_layouts')) {
                $rows = DB::connection('holodb')->table('phpretro_myhabbo_layouts')
                    ->where('guild_id', $guildId)
                    ->where('visible', 1)
                    ->orderBy('column_number')
                    ->orderBy('position')
                    ->get()
                    ->all();
                if ($rows !== []) {
                    return $rows;
                }
            }
        } catch (\Throwable) {
        }

        return [
            (object) ['id' => 1, 'guild_id' => $guildId, 'column_number' => 1, 'widget_key' => 'groupinfowidget', 'position' => 0],
            (object) ['id' => 2, 'guild_id' => $guildId, 'column_number' => 1, 'widget_key' => 'guestbookwidget', 'position' => 5],
            (object) ['id' => 3, 'guild_id' => $guildId, 'column_number' => 2, 'widget_key' => 'memberwidget', 'position' => 0],
        ];
    }

    private function groupStickers(int $guildId): array
    {
        try {
            if (! Schema::connection('holodb')->hasTable('phpretro_homes_items')) {
                return [];
            }

            return DB::connection('holodb')->table('phpretro_homes_items as i')
                ->join('phpretro_homes_catalogue as c', 'c.id', '=', 'i.catalogue_id')
                ->where('i.guild_id', $guildId)
                ->where('i.placed', 1)
                ->where('i.item_type', 'sticker')
                ->orderBy('i.z')
                ->get(['i.id', 'i.x', 'i.y', 'i.z', 'c.data as catalogue_data'])
                ->all();
        } catch (\Throwable) {
            return [];
        }
    }

    private function groupBackground(int $guildId): string
    {
        try {
            if (Schema::connection('holodb')->hasTable('phpretro_homes_items')) {
                $bg = DB::connection('holodb')->table('phpretro_homes_items as i')
                    ->join('phpretro_homes_catalogue as c', 'c.id', '=', 'i.catalogue_id')
                    ->where('i.guild_id', $guildId)
                    ->where('i.placed', 1)
                    ->where('i.item_type', 'background')
                    ->value('c.data');
                if (is_string($bg) && $bg !== '') {
                    return 'b_'.$bg;
                }
            }
        } catch (\Throwable) {
        }

        return 'b_bg_pattern_abstract2';
    }

    private function username(int $id): string
    {
        try {
            return (string) (DB::connection('holodb')->table('users')->where('id', $id)->value('username') ?? '');
        } catch (\Throwable) {
            return '';
        }
    }
}
