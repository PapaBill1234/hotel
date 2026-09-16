<?php

namespace App\Http\Controllers;

use App\Support\Homes;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;
use RuntimeException;

class HomesController extends Controller
{
    public function startSession(Request $request, int $id): RedirectResponse
    {
        $user = $this->user($request);
        abort_unless((int) $user->id === $id, 403, 'Not permitted.');
        $request->session()->put('hotel.page_edit', 'home');

        return redirect('/home/'.$user->username);
    }

    public function cancel(Request $request, int $id): RedirectResponse
    {
        $user = $this->user($request);
        $request->session()->forget('hotel.page_edit');

        return redirect('/home/'.$user->username);
    }

    public function save(Request $request): Response
    {
        $user = $this->user($request);
        $homes = new Homes((int) $user->id);
        try {
            $homes->saveLayout($request->all());
        } catch (RuntimeException $e) {
            return response('<p>'.e($e->getMessage()).'</p>', $e->getCode() ?: 400);
        }
        $request->session()->forget('hotel.page_edit');

        return response('<script language="JavaScript" type="text/javascript">waitAndGo('.json_encode('/home/'.$user->username).');</script>');
    }

    public function groupStart(Request $request, ?int $id = null): RedirectResponse
    {
        $user = $this->user($request);
        $id = $id ?: (int) $request->query('id', $request->input('groupId', 0));
        $homes = new Homes((int) $user->id);
        abort_unless($homes->canEditGroup($id), 403);
        $request->session()->put('hotel.group_page_edit', $id);

        return redirect('/groups/'.$id.'/id');
    }

    public function groupCancel(Request $request): RedirectResponse
    {
        $id = (int) $request->session()->get('hotel.group_page_edit', $request->query('id', 0));
        $request->session()->forget('hotel.group_page_edit');

        return redirect('/groups/'.$id.'/id');
    }

    public function groupSave(Request $request): Response
    {
        $user = $this->user($request);
        $id = (int) $request->session()->get('hotel.group_page_edit', 0);
        $homes = new Homes((int) $user->id);
        try {
            $homes->saveLayout($request->all());
        } catch (RuntimeException $e) {
            return response('<p>'.e($e->getMessage()).'</p>', $e->getCode() ?: 400);
        }
        $request->session()->forget('hotel.group_page_edit');

        return response('<script language="JavaScript" type="text/javascript">waitAndGo('.json_encode('/groups/'.$id.'/id').');</script>');
    }

    public function storeMain(Request $request): Response|View
    {
        $homes = $this->homes($request);
        $stickers = $homes->storeCategories('sticker');
        $backgrounds = $homes->storeCategories('background');
        $notes = $homes->storeCategories('note');
        $firstCategory = (int) (($stickers[0]->category_id ?? 0));
        $items = $homes->storeItems('sticker', $firstCategory);
        $first = $items[0] ?? null;
        $header = $first
            ? [['Inventory', 'Web Store'], [['itemCount' => (int) $first->amount, 'previewCssClass' => $homes->itemCss('sticker', $first->data, true), 'titleKey' => '']]]
            : [['Inventory', 'Web Store'], []];

        return $this->jsonView('hotel.habblet.store-main', [
            'homes' => $homes,
            'stickers' => $stickers,
            'backgrounds' => $backgrounds,
            'notes' => $notes,
            'items' => $items,
            'selectedSub' => $firstCategory,
            'preview' => $first,
        ], $header);
    }

    public function storeItems(Request $request): View
    {
        $homes = $this->homes($request);
        $type = $homes->storeType((string) $request->input('categoryId', '1'));
        $categoryId = (int) $request->input('subCategoryId', 0);

        return view('hotel.habblet.store-items', [
            'homes' => $homes,
            'items' => $homes->storeItems($type, $categoryId),
        ]);
    }

    public function storePreview(Request $request): Response|View
    {
        $homes = $this->homes($request);
        $row = $homes->catalogue((int) $request->input('productId', 0));
        if (! $row) {
            return response('<p>Unknown product.</p>');
        }
        $payload = [
            'itemCount' => (int) $row->amount,
            'previewCssClass' => $homes->itemCss($row->type, $row->data, true),
            'titleKey' => $row->name,
        ];
        if ($row->type === 'background') {
            $payload['bgCssClass'] = $homes->itemCss('background', $row->data);
        }

        return $this->jsonView('hotel.habblet.store-preview', [
            'homes' => $homes,
            'row' => $row,
            'credits' => $homes->credits(),
            'inHotel' => $homes->inHotel(),
        ], [$payload]);
    }

    public function storePurchaseConfirm(Request $request): View
    {
        $homes = $this->homes($request);
        $row = $homes->catalogue((int) $request->input('productId', 0));
        abort_unless($row, 404);

        return view('hotel.habblet.store-purchase-confirm', ['homes' => $homes, 'row' => $row]);
    }

    public function storePurchase(Request $request): Response
    {
        $homes = $this->homes($request);
        try {
            $id = $homes->purchase((int) $request->input('selectedId', 0));
        } catch (RuntimeException $e) {
            return response(
                '<p>'.e($e->getMessage()).'<br /></p><p><a href="#" class="new-button" id="webstore-confirm-cancel"><b>Cancel</b><i></i></a></p><div class="clear"></div>',
                $e->getCode() >= 400 ? $e->getCode() : 400
            );
        }

        return response('OK')->header('X-JSON', json_encode($id));
    }

    public function inventory(Request $request): Response|View
    {
        $homes = $this->homes($request);
        $type = $homes->storeType((string) $request->input('type', 'stickers'));
        $items = $homes->inventory($type);
        $first = $items[0] ?? null;
        $header = ($first && ($first->type ?? '') !== 'widget')
            ? [['Inventory', 'Web Store'], [$homes->itemCss($first->type, $first->data, true), $homes->itemCss($first->type, $first->data), $first->name, 'stickers', null, (int) ($first->quantity ?? 1)]]
            : [['Inventory', 'Web Store'], []];

        return $this->jsonView('hotel.habblet.store-inventory', [
            'homes' => $homes,
            'stickers' => $homes->storeCategories('sticker'),
            'backgrounds' => $homes->storeCategories('background'),
            'notes' => $homes->storeCategories('note'),
        ], $header);
    }

    public function inventoryItems(Request $request): View
    {
        $homes = $this->homes($request);
        $type = $homes->storeType((string) $request->input('type', 'stickers'));

        return view('hotel.habblet.inventory-items', [
            'homes' => $homes,
            'type' => $type,
            'items' => $homes->inventory($type),
        ]);
    }

    public function inventoryPreview(Request $request): Response|View
    {
        $homes = $this->homes($request);
        $type = $homes->storeType((string) $request->input('type', 'stickers'));
        $id = (int) $request->input('itemId', 0);
        if ($type === 'widget') {
            $row = $homes->catalogue($id);
            if (! $row) {
                return response('<p>Unknown item.</p>');
            }
            $css = $homes->itemCss('widget', $row->data, true);
            $header = [$css, null, $row->description, 'Widget', 'true', 1];
        } else {
            $row = $homes->inventoryItem($id);
            if (! $row) {
                return response('<p>Unknown item.</p>');
            }
            $css = $homes->itemCss($row->type, $row->catalogue_data, true);
            $preview = $type === 'note' ? null : $homes->itemCss($row->type, $row->catalogue_data);
            $jsonType = $type === 'background' ? 'Background' : ($type === 'note' ? 'WebCommodity' : 'Sticker');
            $header = [$css, $preview, $row->description ?: $row->name, $jsonType, null, (int) ($row->quantity ?? 1)];
        }

        return $this->jsonView('hotel.habblet.inventory-preview', ['homes' => $homes, 'type' => $type], $header);
    }

    public function placeSticker(Request $request): Response
    {
        $homes = $this->homes($request);
        try {
            $placed = $homes->placeSticker(
                (int) $request->input('selectedStickerId', 0),
                (int) $request->input('zindex', 1),
                (string) $request->input('placeAll', '') === 'true'
            );
        } catch (RuntimeException $e) {
            return response('<p>'.e($e->getMessage()).'</p>', $e->getCode() ?: 400);
        }
        $ids = [];
        $html = '';
        foreach ($placed as $item) {
            if (! $item) {
                continue;
            }
            $ids[] = (string) $item->id;
            $html .= view('hotel.habblet.sticker', ['item' => $item, 'edit' => true, 'homes' => $homes])->render();
        }

        return response($html)->header('X-JSON', json_encode($ids));
    }

    public function removeSticker(Request $request): Response
    {
        try {
            $this->homes($request)->removeSticker((int) $request->input('stickerId', 0));
        } catch (RuntimeException $e) {
            return response('<p>'.e($e->getMessage()).'</p>', $e->getCode() ?: 400);
        }

        return response('');
    }

    public function noteEditor(Request $request): View
    {
        return view('hotel.habblet.note-editor', [
            'note' => (string) $request->input('noteText', ''),
            'skin' => (int) $request->input('skin', 1),
            'rank' => $this->homes($request)->rank(),
            'hasClub' => $this->homes($request)->hasClub(),
        ]);
    }

    public function notePlace(Request $request): Response
    {
        $homes = $this->homes($request);
        try {
            $item = $homes->placeNote((string) $request->input('noteText', ''), (int) $request->input('skin', 1));
        } catch (RuntimeException $e) {
            return response('<p>'.e($e->getMessage()).'</p>', $e->getCode() ?: 400);
        }

        return response(view('hotel.habblet.stickie', ['item' => $item, 'edit' => true, 'homes' => $homes])->render())
            ->header('X-JSON', json_encode((int) $item->id));
    }

    public function notePreview(Request $request): View
    {
        return view('hotel.habblet.note-preview', [
            'note' => (string) $request->input('noteText', ''),
            'skin' => $this->homes($request)->skinName((int) $request->input('skin', 1)),
        ]);
    }

    public function stickieEdit(Request $request): Response
    {
        $homes = $this->homes($request);
        try {
            $item = $homes->editNote((int) $request->input('stickieId', 0), (int) $request->input('skinId', 1));
        } catch (RuntimeException $e) {
            return response('<p>'.e($e->getMessage()).'</p>', $e->getCode() ?: 400);
        }

        return response('')->header('X-JSON', json_encode(['type' => 'stickie', 'id' => (int) $item->id]));
    }

    public function stickieDelete(Request $request): Response
    {
        try {
            $this->homes($request)->deleteNote((int) $request->input('stickieId', 0));
        } catch (RuntimeException $e) {
            return response('<p>'.e($e->getMessage()).'</p>', $e->getCode() ?: 400);
        }

        return response('');
    }

    public function widgetAdd(Request $request): Response
    {
        $homes = $this->homes($request);
        $key = (string) ($request->input('widgetType') ?: $request->input('widget_key') ?: $request->input('widgetId', ''));
        try {
            $widget = $homes->add($key, (int) $request->input('column_number', 1));
        } catch (RuntimeException $e) {
            return response('<p>'.e($e->getMessage()).'</p>', $e->getCode() ?: 400);
        }

        return response(view('hotel.partials.home-widget', $this->widgetViewData($request, $homes, $widget, true))->render())
            ->header('X-JSON', json_encode([(string) $widget->id]));
    }

    public function widgetDelete(Request $request): Response
    {
        try {
            $this->homes($request)->deleteWidget((int) $request->input('widgetId', 0));
        } catch (RuntimeException $e) {
            return response('<p>'.e($e->getMessage()).'</p>', $e->getCode() ?: 400);
        }

        return response('');
    }

    public function widgetEdit(Request $request): Response
    {
        return response('')->header('X-JSON', json_encode([
            'type' => 'widget',
            'id' => (int) $request->input('widgetId', 0),
        ]));
    }

    public function guestbookAdd(Request $request): Response
    {
        $homes = $this->homes($request);
        try {
            $entry = $homes->addGuestbook((int) $request->input('widgetId', 0), (string) $request->input('message', ''));
        } catch (RuntimeException $e) {
            return response('<p>'.e($e->getMessage()).'</p>', $e->getCode() ?: 400);
        }

        return response(view('hotel.habblet.guestbook-entry', [
            'entry' => $entry,
            'hotelUser' => $request->attributes->get('hotelUser'),
        ])->render());
    }

    public function guestbookList(Request $request): View
    {
        $homes = $this->homes($request);
        $widget = $homes->widget((int) $request->input('widgetId', 0));
        $entries = $widget ? $homes->guestbookEntriesForWidget($widget) : [];

        return view('hotel.habblet.guestbook-list', [
            'entries' => $entries,
            'hotelUser' => $request->attributes->get('hotelUser'),
        ]);
    }

    public function guestbookPreview(Request $request): View
    {
        return view('hotel.habblet.guestbook-preview', [
            'message' => (string) $request->input('message', ''),
            'hotelUser' => $request->attributes->get('hotelUser'),
        ]);
    }

    public function guestbookRemove(Request $request): Response
    {
        try {
            $this->homes($request)->removeGuestbook((int) $request->input('entryId', 0), (int) $request->input('widgetId', 0) ?: null);
        } catch (RuntimeException $e) {
            return response('<p>'.e($e->getMessage()).'</p>', $e->getCode() ?: 400);
        }

        return response('OK');
    }

    public function guestbookConfigure(Request $request): Response
    {
        try {
            $this->homes($request)->configureGuestbook((int) $request->input('widgetId', 0));
        } catch (RuntimeException $e) {
            return response('<p>'.e($e->getMessage()).'</p>', $e->getCode() ?: 400);
        }

        return response('');
    }

    public function friendSearch(Request $request): View
    {
        $homes = $this->homes($request);
        $ownerId = (int) ($request->input('_mypage.requested.account') ?: $request->input('_mypage_requested_account', 0));
        if ($ownerId < 1) {
            $ownerId = (int) $request->attributes->get('hotelUser')->id;
        }

        return view('hotel.habblet.friend-list', [
            'friends' => $homes->friends($ownerId, (string) $request->input('searchString', ''), max(0, ((int) $request->input('pageNumber', 1) - 1) * 20), 20),
        ]);
    }

    public function avatarInfo(Request $request): View
    {
        $id = (int) ($request->input('accountId') ?: $request->input('anAccountId', 0));
        $row = \Illuminate\Support\Facades\DB::connection('holodb')->table('users')->where('id', $id)->first();

        return view('hotel.habblet.avatar-info', ['row' => $row]);
    }

    public function memberSearch(Request $request): View
    {
        $homes = $this->homes($request);
        $groupId = (int) ($request->input('_groupspage.requested.group') ?: $request->input('_groupspage_requested_group') ?: $request->input('groupId', 0));
        $members = [];
        if ($groupId > 0) {
            $members = \Illuminate\Support\Facades\DB::connection('holodb')->table('guilds_members as m')
                ->join('users as u', 'u.id', '=', 'm.user_id')
                ->where('m.guild_id', $groupId)->whereIn('m.level_id', [0, 1, 2])
                ->orderBy('m.level_id')->orderBy('u.username')
                ->get(['u.id', 'u.username', 'u.look', 'm.level_id']);
        }

        return view('hotel.habblet.member-list', ['members' => $members, 'homes' => $homes]);
    }

    public function groupInfo(Request $request): View
    {
        $id = (int) $request->input('groupId', 0);
        $row = \Illuminate\Support\Facades\DB::connection('holodb')->table('guilds')->where('id', $id)->first();

        return view('hotel.habblet.group-info', ['guild' => $row]);
    }

    public function tagRefuse(): Response
    {
        return response('invalidtag');
    }

    public function friendAddRefuse(): Response
    {
        return response('PolarIS messenger_friendrequests is not written from this website.');
    }

    public function rate(Request $request): View|Response
    {
        $user = $this->user($request);
        $homes = new Homes((int) $user->id);
        $ownerId = (int) $request->input('ownerId', $request->query('ownerId', 0));
        $widgetId = (int) $request->input('ratingId', $request->query('ratingId', $request->input('widgetId', 0)));
        $given = (int) $request->input('givenRate', $request->query('givenRate', 0));
        try {
            $summary = $given >= 1
                ? $homes->rate($ownerId, $widgetId, $given)
                : $homes->ratingSummary($ownerId);
        } catch (RuntimeException $e) {
            return response('<p>'.e($e->getMessage()).'</p>', $e->getCode() ?: 400);
        }

        return view('hotel.habblet.rating', [
            'ownerId' => $ownerId,
            'widgetId' => $widgetId,
            'summary' => $summary,
            'hotelUser' => $user,
        ]);
    }

    public function resetRatings(Request $request): View|Response
    {
        $user = $this->user($request);
        $homes = new Homes((int) $user->id);
        $ownerId = (int) $request->input('ownerId', $user->id);
        $widgetId = (int) $request->input('ratingId', $request->input('widgetId', 0));
        try {
            $summary = $homes->resetRatings($ownerId, $widgetId);
        } catch (RuntimeException $e) {
            return response('<p>'.e($e->getMessage()).'</p>', $e->getCode() ?: 400);
        }

        return view('hotel.habblet.rating', [
            'ownerId' => $ownerId,
            'widgetId' => $widgetId,
            'summary' => $summary,
            'hotelUser' => $user,
        ]);
    }

    public function linktool(Request $request): Response
    {
        $query = trim((string) $request->query('query', $request->input('query', '')));
        $scope = (int) $request->query('scope', $request->input('scope', 1));
        $type = [1 => 'habbo', 2 => 'room', 3 => 'group'][$scope] ?? '';
        $rows = [];
        if ($type !== '' && $query !== '') {
            try {
                $rows = match ($scope) {
                    1 => DB::connection('holodb')->table('users')->where('username', 'like', '%'.$query.'%')->orderBy('username')->limit(5)->get(['id', 'username as name']),
                    2 => Schema::connection('holodb')->hasTable('rooms')
                        ? DB::connection('holodb')->table('rooms')->where('name', 'like', '%'.$query.'%')->orderBy('name')->limit(5)->get(['id', 'name'])
                        : collect(),
                    3 => Schema::connection('holodb')->hasTable('guilds')
                        ? DB::connection('holodb')->table('guilds')->where('name', 'like', '%'.$query.'%')->orderBy('name')->limit(5)->get(['id', 'name'])
                        : collect(),
                    default => collect(),
                };
            } catch (\Throwable) {
                $rows = [];
            }
        }
        $html = '<ul><li>Add a link</li>';
        foreach ($rows as $row) {
            $html .= '<li><a href="#" class="linktool-result" type="'.$type.'" value="'.(int) $row->id.'" title="'.e($row->name).'">'.e($row->name).'</a></li>';
        }
        $html .= '</ul>';

        return response($html);
    }

    public function backgroundWarning(): View
    {
        return view('hotel.habblet.background-warning');
    }

    private function homes(Request $request): Homes
    {
        return new Homes((int) $this->user($request)->id);
    }

    private function user(Request $request)
    {
        $user = $request->attributes->get('hotelUser');
        abort_unless($user, 401, 'Please sign in first.');

        return $user;
    }

    private function jsonView(string $view, array $data, mixed $json): Response
    {
        return response(view($view, $data)->render())->header('X-JSON', json_encode($json, JSON_UNESCAPED_UNICODE));
    }

    private function widgetViewData(Request $request, Homes $homes, object $widget, bool $edit): array
    {
        $ownerId = (int) $widget->user_id;
        $profile = \Illuminate\Support\Facades\DB::connection('holodb')->table('users')->where('id', $ownerId)->first();

        return [
            'homes' => $homes,
            'widget' => $widget,
            'profile' => $profile,
            'edit' => $edit,
            'hotelUser' => $request->attributes->get('hotelUser'),
            'friends' => $homes->friends($ownerId),
            'friendCount' => $homes->friendCount($ownerId),
            'groups' => $homes->groups($ownerId),
            'rooms' => $homes->rooms($ownerId),
            'guestbook' => $homes->guestbookEntriesForWidget($widget),
        ];
    }
}
