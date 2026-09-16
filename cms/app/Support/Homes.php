<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use RuntimeException;

final class Homes
{
    public const USER_WIDGETS = ['profilewidget', 'guestbookwidget', 'highscoreswidget', 'badgeswidget', 'friendswidget', 'groupswidget', 'roomswidget', 'ratingwidget'];

    public const GROUP_WIDGETS = ['groupinfowidget', 'guestbookwidget', 'memberwidget'];

    public const BLOCKED_WIDGETS = ['traxplayerwidget'];

    public const ALIASES = [
        'profile' => 'profilewidget', 'guestbook' => 'guestbookwidget', 'highscores' => 'highscoreswidget',
        'badges' => 'badgeswidget', 'friends' => 'friendswidget', 'groups' => 'groupswidget', 'rooms' => 'roomswidget',
        'traxplayer' => 'traxplayerwidget', 'rating' => 'ratingwidget',
        'groupinfo' => 'groupinfowidget', 'members' => 'memberwidget', 'member' => 'memberwidget',
    ];

    public const STORE_TYPES = [
        'stickers' => 'sticker', 'backgrounds' => 'background', 'notes' => 'note',
        'stickie_notes' => 'note', 'widgets' => 'widget',
    ];

    public const SKINS = [
        1 => 'defaultskin', 2 => 'speechbubbleskin', 3 => 'metalskin', 4 => 'noteitskin',
        5 => 'notepadskin', 6 => 'goldenskin', 7 => 'hc_machineskin', 8 => 'hc_pillowskin', 9 => 'default',
    ];

    public function __construct(public int $actor) {}

    public function need(bool $ok, string $message, int $status = 400): void
    {
        if (! $ok) {
            throw new RuntimeException($message, $status);
        }
    }

    public function key(string $key): string
    {
        $key = strtolower(trim($key));

        return self::ALIASES[$key] ?? $key;
    }

    public function editingGuild(): int
    {
        return (int) session('hotel.group_page_edit', 0);
    }

    public function placement(): string
    {
        return $this->editingGuild() > 0 ? 'groups' : 'homes';
    }

    public function rank(): int
    {
        try {
            return (int) (DB::connection('holodb')->table('users')->where('id', $this->actor)->value('rank') ?: 0);
        } catch (\Throwable) {
            return 0;
        }
    }

    public function hasClub(): bool
    {
        try {
            if (! Schema::connection('holodb')->hasTable('users_settings')) {
                return false;
            }
            $expires = DB::connection('holodb')->table('users_settings')->where('user_id', $this->actor)->value('club_expire_timestamp');

            return $expires !== null && (int) $expires > time();
        } catch (\Throwable) {
            return false;
        }
    }

    public function inHotel(): bool
    {
        try {
            $online = (string) DB::connection('holodb')->table('users')->where('id', $this->actor)->value('online');

            return $online === '1' || $online === '2';
        } catch (\Throwable) {
            return false;
        }
    }

    public function credits(): int
    {
        try {
            return (int) (DB::connection('holodb')->table('users')->where('id', $this->actor)->value('credits') ?: 0);
        } catch (\Throwable) {
            return 0;
        }
    }

    public function layouts(int $userId): array
    {
        return $this->queryRows(
            fn () => DB::connection('holodb')->table('phpretro_myhabbo_layouts')
                ->where('user_id', $userId)->where('guild_id', 0)->where('visible', 1)
                ->orderBy('column_number')->orderBy('position')->orderBy('id')->get()->all()
        );
    }

    public function displayLayouts(int $userId): array
    {
        $rows = $this->layouts($userId);
        if ($rows !== []) {
            return $rows;
        }

        return [(object) ['id' => 0, 'user_id' => $userId, 'guild_id' => 0, 'column_number' => 1, 'widget_key' => 'profilewidget', 'position' => 0, 'visible' => 1, 'privacy' => 'public']];
    }

    public function groupLayouts(int $guildId): array
    {
        return $this->queryRows(
            fn () => DB::connection('holodb')->table('phpretro_myhabbo_layouts')
                ->where('guild_id', $guildId)->where('visible', 1)
                ->orderBy('column_number')->orderBy('position')->orderBy('id')->get()->all()
        );
    }

    public function displayGroupLayouts(int $guildId): array
    {
        $rows = $this->groupLayouts($guildId);
        if ($rows !== []) {
            return $rows;
        }

        return [(object) ['id' => 0, 'guild_id' => $guildId, 'column_number' => 1, 'widget_key' => 'groupinfowidget', 'position' => 0, 'visible' => 1, 'privacy' => 'public']];
    }

    public function widget(int $id): ?object
    {
        if ($id < 1) {
            return null;
        }

        try {
            return DB::connection('holodb')->table('phpretro_myhabbo_layouts')->where('id', $id)->first();
        } catch (\Throwable) {
            return null;
        }
    }

    public function canEditGroup(int $guildId): bool
    {
        if ($guildId < 1 || $this->actor < 1) {
            return false;
        }
        try {
            $owner = (int) DB::connection('holodb')->table('guilds')->where('id', $guildId)->value('user_id');
            if ($owner === $this->actor) {
                return true;
            }
            $level = DB::connection('holodb')->table('guilds_members')->where('guild_id', $guildId)->where('user_id', $this->actor)->value('level_id');

            return $level !== null && (int) $level === 1;
        } catch (\Throwable) {
            return false;
        }
    }

    public function groupMember(int $guildId, int $userId): bool
    {
        try {
            $level = DB::connection('holodb')->table('guilds_members')->where('guild_id', $guildId)->where('user_id', $userId)->value('level_id');

            return $level !== null && in_array((int) $level, [0, 1, 2], true);
        } catch (\Throwable) {
            return false;
        }
    }

    public function areFriends(int $a, int $b): bool
    {
        if ($a === $b) {
            return true;
        }
        try {
            return DB::connection('holodb')->table('messenger_friendships')
                ->where('user_one_id', $a)->where('user_two_id', $b)->exists();
        } catch (\Throwable) {
            return false;
        }
    }

    public function storeType(string $raw): string
    {
        $raw = strtolower(trim($raw));

        return self::STORE_TYPES[$raw] ?? match ($raw) {
            '1', 'sticker' => 'sticker',
            '2', 'widget' => 'widget',
            '3', 'note' => 'note',
            '4', 'background' => 'background',
            default => 'sticker',
        };
    }

    public function catalogue(int $id): ?object
    {
        if ($id < 1) {
            return null;
        }
        try {
            return DB::connection('holodb')->table('phpretro_homes_catalogue')->where('id', $id)->first();
        } catch (\Throwable) {
            return null;
        }
    }

    public function storeCategories(string $type): array
    {
        $placement = $this->placement();
        try {
            return DB::connection('holodb')->table('phpretro_homes_catalogue')
                ->select('category_id', 'category')
                ->where('type', $type)
                ->where('min_rank', '<=', $this->rank())
                ->where(function ($q) use ($placement) {
                    $q->where('placement', $placement)->orWhere('placement', 'anywhere');
                })
                ->groupBy('category_id', 'category')
                ->orderBy('category')
                ->orderBy('category_id')
                ->get()
                ->all();
        } catch (\Throwable) {
            return [];
        }
    }

    public function storeItems(string $type, int $categoryId): array
    {
        $placement = $this->placement();
        try {
            $q = DB::connection('holodb')->table('phpretro_homes_catalogue')
                ->where('type', $type)
                ->where('min_rank', '<=', $this->rank())
                ->where(function ($inner) use ($placement) {
                    $inner->where('placement', $placement)->orWhere('placement', 'anywhere');
                });
            if ($categoryId > 0) {
                $q->where('category_id', $categoryId);
            }

            return $q->orderByDesc('id')->get()->all();
        } catch (\Throwable) {
            return [];
        }
    }

    public function itemCss(string $type, string $data, bool $preview = false): string
    {
        $prefix = match ($type) {
            'sticker', '1' => 's_',
            'widget', '2' => 'w_',
            'note', '3' => 'commodity_',
            'background', '4' => 'b_',
            default => '',
        };

        return $prefix.$data.($preview ? '_pre' : '');
    }

    public function inventory(string $type): array
    {
        $placement = $this->placement();
        try {
            if ($type === 'widget') {
                return DB::connection('holodb')->table('phpretro_homes_catalogue')
                    ->where('type', 'widget')
                    ->where('min_rank', '<=', $this->rank())
                    ->where(function ($q) use ($placement) {
                        $q->where('placement', $placement)->orWhere('placement', 'anywhere');
                    })
                    ->orderByDesc('id')
                    ->get()
                    ->all();
            }
            $q = DB::connection('holodb')->table('phpretro_homes_items as i')
                ->join('phpretro_homes_catalogue as c', 'c.id', '=', 'i.catalogue_id')
                ->where('i.user_id', $this->actor)
                ->where('c.type', $type)
                ->where(function ($inner) use ($placement) {
                    $inner->where('c.placement', $placement)->orWhere('c.placement', 'anywhere');
                });
            if ($type !== 'background') {
                $q->where('i.placed', 0);
            }

            return $q->selectRaw('MIN(i.id) as id, i.catalogue_id, c.data, c.name, c.type, COUNT(i.id) as quantity')
                ->groupBy('i.catalogue_id', 'c.data', 'c.name', 'c.type')
                ->orderByDesc('id')
                ->get()
                ->all();
        } catch (\Throwable) {
            return [];
        }
    }

    public function inventoryItem(int $id): ?object
    {
        if ($id < 1) {
            return null;
        }
        try {
            return DB::connection('holodb')->table('phpretro_homes_items as i')
                ->join('phpretro_homes_catalogue as c', 'c.id', '=', 'i.catalogue_id')
                ->where('i.id', $id)
                ->first([
                    'i.id', 'i.user_id', 'i.guild_id', 'i.catalogue_id', 'i.item_type', 'i.skin', 'i.data',
                    'i.x', 'i.y', 'i.z', 'i.placed', 'c.name', 'c.type', 'c.data as catalogue_data', 'c.description',
                ]);
        } catch (\Throwable) {
            return null;
        }
    }

    public function widgetPlaced(string $key): bool
    {
        $guildId = $this->editingGuild();
        try {
            $q = DB::connection('holodb')->table('phpretro_myhabbo_layouts')->where('widget_key', $key);
            if ($guildId > 0) {
                return $q->where('guild_id', $guildId)->exists();
            }

            return $q->where('user_id', $this->actor)->where('guild_id', 0)->exists();
        } catch (\Throwable) {
            return false;
        }
    }

    public function placedItems(int $userId = 0, int $guildId = 0): array
    {
        try {
            $q = DB::connection('holodb')->table('phpretro_homes_items as i')
                ->join('phpretro_homes_catalogue as c', 'c.id', '=', 'i.catalogue_id')
                ->where('i.placed', 1);
            if ($guildId > 0) {
                $q->where('i.guild_id', $guildId);
            } else {
                $q->where('i.user_id', $userId)->where('i.guild_id', 0);
            }

            return $q->orderBy('i.z')->orderBy('i.id')
                ->get(['i.id', 'i.user_id', 'i.guild_id', 'i.catalogue_id', 'i.item_type', 'i.skin', 'i.data', 'i.x', 'i.y', 'i.z', 'c.data as catalogue_data', 'c.type'])
                ->all();
        } catch (\Throwable) {
            return [];
        }
    }

    public function backgroundClass(int $userId = 0, int $guildId = 0): string
    {
        foreach ($this->placedItems($userId, $guildId) as $item) {
            if ($item->item_type === 'background') {
                return $this->itemCss('background', (string) $item->catalogue_data);
            }
        }

        return $guildId > 0 ? 'b_bg_colour_08' : 'b_bg_pattern_abstract2';
    }

    public function widgetStyle(object $widget): string
    {
        $left = ((int) ($widget->column_number ?? 1) === 2) ? 450 : 25;
        $top = max(0, (int) ($widget->position ?? 0)) * 50 + 10;
        $z = max(1, (int) ($widget->position ?? 0) + 1);

        return 'left: '.$left.'px; top: '.$top.'px; z-index: '.$z;
    }

    public function purchase(int $catalogueId): int
    {
        $this->need($this->actor > 0, 'Sign in required.', 401);
        $item = $this->catalogue($catalogueId);
        $this->need((bool) $item, 'Unknown product.', 404);
        $this->need($item->type !== 'widget', "You're not allowed to purchase this.", 400);
        $this->need((int) $item->min_rank <= $this->rank(), "You're not allowed to purchase this.", 400);
        $placement = $this->placement();
        $this->need($item->placement === 'anywhere' || $item->placement === $placement, "You're not allowed to purchase this.", 400);
        $this->need(! $this->inHotel(), 'Leave the hotel first. PolarIS has no take-credits command, so buying while online would be overwritten when you disconnect.', 409);
        $price = (int) $item->price;
        $this->need($price === 0, 'PolarIS users.credits is not written from this website. Only free catalogue items can be added here.', 501);
        $itemType = $item->type === 'note' ? 'stickie' : $item->type;
        if (in_array($item->type, ['background', 'widget'], true)) {
            $owned = DB::connection('holodb')->table('phpretro_homes_items')->where('user_id', $this->actor)->where('catalogue_id', $item->id)->exists();
            $this->need(! $owned, 'You already own this item.', 409);
        }
        $amount = max(1, (int) $item->amount);
        $firstId = 0;
        for ($i = 0; $i < $amount; $i++) {
            $id = DB::connection('holodb')->table('phpretro_homes_items')->insertGetId([
                'user_id' => $this->actor,
                'guild_id' => 0,
                'catalogue_id' => $item->id,
                'item_type' => $itemType,
                'skin' => '',
                'data' => '',
                'x' => 0,
                'y' => 0,
                'z' => 0,
                'placed' => 0,
            ]);
            if ($firstId === 0) {
                $firstId = $id;
            }
        }

        return $firstId;
    }

    public function placeSticker(int $itemId, int $z, bool $all = false): array
    {
        $this->need($this->actor > 0, 'Sign in required.', 401);
        $first = DB::connection('holodb')->table('phpretro_homes_items')->where('id', $itemId)->first();
        $this->need((bool) $first && (int) $first->user_id === $this->actor && $first->item_type === 'sticker' && (int) $first->placed === 0, 'Sticker not found.', 404);
        $ids = [$itemId];
        if ($all) {
            $rest = DB::connection('holodb')->table('phpretro_homes_items')
                ->where('user_id', $this->actor)->where('catalogue_id', $first->catalogue_id)
                ->where('item_type', 'sticker')->where('placed', 0)->where('id', '<>', $itemId)
                ->orderBy('id')->pluck('id')->all();
            foreach ($rest as $id) {
                $ids[] = (int) $id;
            }
        }
        $guildId = $this->editingGuild();
        $placed = [];
        $left = 20;
        $top = 30;
        foreach ($ids as $index => $id) {
            DB::connection('holodb')->table('phpretro_homes_items')->where('id', $id)->where('user_id', $this->actor)->update([
                'placed' => 1,
                'guild_id' => $guildId,
                'x' => $left,
                'y' => $top + ($index * 4),
                'z' => $z + $index,
            ]);
            $placed[] = $this->inventoryItem((int) $id);
        }

        return $placed;
    }

    public function removeSticker(int $itemId): void
    {
        $item = DB::connection('holodb')->table('phpretro_homes_items')->where('id', $itemId)->first();
        $this->need((bool) $item && $item->item_type === 'sticker', 'Sticker not found.', 404);
        $this->need((int) $item->user_id === $this->actor || ((int) $item->guild_id > 0 && $this->canEditGroup((int) $item->guild_id)), 'Not permitted.', 403);
        DB::connection('holodb')->table('phpretro_homes_items')->where('id', $itemId)->update([
            'placed' => 0, 'guild_id' => 0, 'x' => 0, 'y' => 0, 'z' => 0,
        ]);
    }

    public function skinName(int $skinId): string
    {
        $name = self::SKINS[$skinId] ?? 'defaultskin';
        if (in_array($skinId, [7, 8], true) && ! $this->hasClub()) {
            return 'defaultskin';
        }
        if ($skinId === 9 && $this->rank() <= 5) {
            return 'defaultskin';
        }

        return $name;
    }

    public function placeNote(string $text, int $skinId): object
    {
        $this->need($this->actor > 0, 'Sign in required.', 401);
        $text = trim($text);
        $max = $this->rank() > 5 ? 20000 : 500;
        $this->need($text !== '' && mb_strlen($text, 'UTF-8') <= $max, 'Note must be between 1 and '.$max.' characters.', 400);
        $skin = $this->skinName($skinId);
        $item = DB::connection('holodb')->table('phpretro_homes_items as i')
            ->join('phpretro_homes_catalogue as c', 'c.id', '=', 'i.catalogue_id')
            ->where('i.user_id', $this->actor)->where('i.placed', 0)->where('i.item_type', 'stickie')->where('c.data', 'stickienote')
            ->orderBy('i.id')->first(['i.id']);
        $this->need((bool) $item, 'You have no notes in inventory.', 404);
        $z = (int) (DB::connection('holodb')->table('phpretro_homes_items')->where('user_id', $this->actor)->where('placed', 1)->max('z') ?: 0) + 2;
        $guildId = $this->editingGuild();
        DB::connection('holodb')->table('phpretro_homes_items')->where('id', $item->id)->update([
            'placed' => 1, 'guild_id' => $guildId, 'skin' => $skin, 'data' => $text, 'x' => 10, 'y' => 10, 'z' => $z,
        ]);

        return $this->inventoryItem((int) $item->id);
    }

    public function editNote(int $itemId, int $skinId): object
    {
        $skin = $this->skinName($skinId);
        $item = DB::connection('holodb')->table('phpretro_homes_items')->where('id', $itemId)->first();
        $this->need((bool) $item && $item->item_type === 'stickie', 'Note not found.', 404);
        $this->need((int) $item->user_id === $this->actor || ((int) $item->guild_id > 0 && $this->canEditGroup((int) $item->guild_id)), 'Not permitted.', 403);
        DB::connection('holodb')->table('phpretro_homes_items')->where('id', $itemId)->update(['skin' => $skin]);

        return $this->inventoryItem($itemId);
    }

    public function deleteNote(int $itemId): void
    {
        $item = DB::connection('holodb')->table('phpretro_homes_items')->where('id', $itemId)->first();
        $this->need((bool) $item && $item->item_type === 'stickie', 'Note not found.', 404);
        $this->need((int) $item->user_id === $this->actor || ((int) $item->guild_id > 0 && $this->canEditGroup((int) $item->guild_id)), 'Not permitted.', 403);
        DB::connection('holodb')->table('phpretro_homes_items')->where('id', $itemId)->update([
            'placed' => 0, 'guild_id' => 0, 'data' => '', 'skin' => '', 'x' => 0, 'y' => 0, 'z' => 0,
        ]);
    }

    public function add(string $key, int $column = 1): object
    {
        $guildId = $this->editingGuild();
        if ($guildId > 0) {
            return $this->addGroup($guildId, $key, $column);
        }
        $key = $this->resolveWidgetKey($key, 'homes');
        $this->need(! in_array($key, self::BLOCKED_WIDGETS, true), 'This widget is unavailable.', 501);
        $this->need(in_array($key, self::USER_WIDGETS, true), 'Unknown widget.', 400);
        $column = $column === 2 ? 2 : 1;
        $existing = DB::connection('holodb')->table('phpretro_myhabbo_layouts')
            ->where('user_id', $this->actor)->where('guild_id', 0)->where('widget_key', $key)->exists();
        $this->need(! $existing, 'Widget already placed.', 409);
        $position = (int) (DB::connection('holodb')->table('phpretro_myhabbo_layouts')
            ->where('user_id', $this->actor)->where('guild_id', 0)->where('column_number', $column)->max('position') ?? -1) + 1;
        $id = DB::connection('holodb')->table('phpretro_myhabbo_layouts')->insertGetId([
            'user_id' => $this->actor, 'guild_id' => 0, 'column_number' => $column, 'widget_key' => $key,
            'position' => $position, 'visible' => 1, 'privacy' => 'public',
        ]);

        return $this->widget($id);
    }

    public function addGroup(int $guildId, string $key, int $column = 1): object
    {
        $this->need($this->canEditGroup($guildId), 'Not permitted.', 403);
        $key = $this->resolveWidgetKey($key, 'groups');
        $this->need(! in_array($key, self::BLOCKED_WIDGETS, true), 'This widget is unavailable.', 501);
        $this->need(in_array($key, self::GROUP_WIDGETS, true), 'Unknown widget.', 400);
        $column = $column === 2 ? 2 : 1;
        $ownerId = (int) DB::connection('holodb')->table('guilds')->where('id', $guildId)->value('user_id');
        $this->need($ownerId > 0, 'Group not found.', 404);
        $existing = DB::connection('holodb')->table('phpretro_myhabbo_layouts')->where('guild_id', $guildId)->where('widget_key', $key)->exists();
        $this->need(! $existing, 'Widget already placed.', 409);
        $position = (int) (DB::connection('holodb')->table('phpretro_myhabbo_layouts')->where('guild_id', $guildId)->where('column_number', $column)->max('position') ?? -1) + 1;
        $id = DB::connection('holodb')->table('phpretro_myhabbo_layouts')->insertGetId([
            'user_id' => $ownerId, 'guild_id' => $guildId, 'column_number' => $column, 'widget_key' => $key,
            'position' => $position, 'visible' => 1, 'privacy' => 'public',
        ]);

        return $this->widget($id);
    }

    public function resolveWidgetKey(string $key, string $placement): string
    {
        $key = trim($key);
        if ($key !== '' && ctype_digit($key)) {
            $row = $this->catalogue((int) $key);
            $this->need((bool) $row && $row->type === 'widget', 'Unknown widget.', 400);
            $this->need($row->placement === $placement || $row->placement === 'anywhere', 'Unknown widget.', 400);
            $key = $row->data;
        }

        return $this->key($key);
    }

    public function deleteWidget(int $id): void
    {
        $widget = $this->widget($id);
        $this->need((bool) $widget, 'Widget not found.', 404);
        $this->requireOwner($widget);
        $locked = (int) $widget->guild_id > 0 ? 'groupinfowidget' : 'profilewidget';
        $this->need($widget->widget_key !== $locked, 'This widget cannot be removed.', 403);
        DB::connection('holodb')->table('phpretro_myhabbo_layouts')->where('id', $id)->delete();
    }

    public function requireOwner(object $widget): void
    {
        if ((int) $widget->guild_id > 0) {
            $this->need($this->canEditGroup((int) $widget->guild_id), 'Not permitted.', 403);

            return;
        }
        $this->need((int) $widget->user_id === $this->actor, 'Not permitted.', 403);
    }

    public function saveLayout(array $post): void
    {
        $guildId = $this->editingGuild();
        $this->saveWidgetCoords((string) ($post['widgets'] ?? ''), $guildId);
        $this->saveItemCoords((string) ($post['stickers'] ?? ''), 'sticker', $guildId);
        $this->saveItemCoords((string) ($post['stickienotes'] ?? ''), 'stickie', $guildId);
        $background = trim((string) ($post['background'] ?? ''));
        if ($background !== '') {
            $this->saveBackground($background, $guildId);
        }
    }

    public function guestbookEntries(int $profileUserId, int $offset = 0, int $limit = 20): array
    {
        try {
            if (! Schema::connection('holodb')->hasTable('phpretro_myhabbo_guestbook')) {
                return [];
            }

            return DB::connection('holodb')->table('phpretro_myhabbo_guestbook as g')
                ->join('users as u', 'u.id', '=', 'g.author_user_id')
                ->where('g.profile_user_id', $profileUserId)
                ->orderByDesc('g.id')
                ->offset(max(0, $offset))
                ->limit(min(50, max(1, $limit)))
                ->get(['g.id', 'g.profile_user_id', 'g.author_user_id', 'g.message', 'g.created_at', 'u.username', 'u.look', 'u.online'])
                ->all();
        } catch (\Throwable) {
            return [];
        }
    }

    public function guestbookEntriesForWidget(object $widget, int $offset = 0, int $limit = 20): array
    {
        if ((int) ($widget->guild_id ?? 0) > 0) {
            try {
                if (! Schema::connection('holodb')->hasTable('phpretro_group_guestbook')) {
                    return [];
                }

                return DB::connection('holodb')->table('phpretro_group_guestbook as g')
                    ->join('users as u', 'u.id', '=', 'g.author_user_id')
                    ->where('g.guild_id', (int) $widget->guild_id)
                    ->orderByDesc('g.id')
                    ->offset(max(0, $offset))
                    ->limit(min(50, max(1, $limit)))
                    ->get(['g.id', 'g.guild_id', 'g.author_user_id', 'g.message', 'g.created_at', 'u.username', 'u.look', 'u.online'])
                    ->all();
            } catch (\Throwable) {
                return [];
            }
        }

        return $this->guestbookEntries((int) $widget->user_id, $offset, $limit);
    }

    public function addGuestbook(int $widgetId, string $message): object
    {
        $widget = $this->widget($widgetId);
        $this->need((bool) $widget && $widget->widget_key === 'guestbookwidget', 'Not a guestbook.', 400);
        $message = trim($message);
        $this->need($this->actor > 0, 'Sign in required.', 401);
        $this->need($message !== '' && mb_strlen($message, 'UTF-8') <= 1000, 'Message must be between 1 and 1000 characters.', 400);
        $privacy = ($widget->privacy ?? 'public') === 'private' ? 'private' : 'public';
        if ((int) $widget->guild_id > 0) {
            if ($privacy === 'private') {
                $this->need($this->groupMember((int) $widget->guild_id, $this->actor), 'Only group members can post to this guestbook.', 403);
            }
            $id = DB::connection('holodb')->table('phpretro_group_guestbook')->insertGetId([
                'guild_id' => (int) $widget->guild_id, 'author_user_id' => $this->actor, 'message' => $message, 'created_at' => time(),
            ]);

            return DB::connection('holodb')->table('phpretro_group_guestbook as g')
                ->join('users as u', 'u.id', '=', 'g.author_user_id')
                ->where('g.id', $id)
                ->first(['g.id', 'g.guild_id', 'g.author_user_id', 'g.message', 'g.created_at', 'u.username', 'u.look', 'u.online']);
        }
        if ($privacy === 'private') {
            $this->need($this->areFriends((int) $widget->user_id, $this->actor), 'Only friends can post to this guestbook.', 403);
        }
        $id = DB::connection('holodb')->table('phpretro_myhabbo_guestbook')->insertGetId([
            'profile_user_id' => (int) $widget->user_id, 'author_user_id' => $this->actor, 'message' => $message, 'created_at' => time(),
        ]);

        return DB::connection('holodb')->table('phpretro_myhabbo_guestbook as g')
            ->join('users as u', 'u.id', '=', 'g.author_user_id')
            ->where('g.id', $id)
            ->first(['g.id', 'g.profile_user_id', 'g.author_user_id', 'g.message', 'g.created_at', 'u.username', 'u.look', 'u.online']);
    }

    public function removeGuestbook(int $entryId, ?int $widgetId = null): void
    {
        $this->need($entryId > 0, 'Invalid entry.', 400);
        if ($widgetId) {
            $widget = $this->widget($widgetId);
            if ($widget && (int) $widget->guild_id > 0) {
                $row = DB::connection('holodb')->table('phpretro_group_guestbook')->where('id', $entryId)->first();
                $this->need((bool) $row, 'Entry not found.', 404);
                $this->need((int) $row->author_user_id === $this->actor || $this->canEditGroup((int) $row->guild_id), 'Not permitted.', 403);
                DB::connection('holodb')->table('phpretro_group_guestbook')->where('id', $entryId)->delete();

                return;
            }
        }
        $row = DB::connection('holodb')->table('phpretro_myhabbo_guestbook')->where('id', $entryId)->first();
        if (! $row) {
            $row = DB::connection('holodb')->table('phpretro_group_guestbook')->where('id', $entryId)->first();
            $this->need((bool) $row, 'Entry not found.', 404);
            $this->need((int) $row->author_user_id === $this->actor || $this->canEditGroup((int) $row->guild_id), 'Not permitted.', 403);
            DB::connection('holodb')->table('phpretro_group_guestbook')->where('id', $entryId)->delete();

            return;
        }
        $this->need((int) $row->author_user_id === $this->actor || (int) $row->profile_user_id === $this->actor, 'Not permitted.', 403);
        DB::connection('holodb')->table('phpretro_myhabbo_guestbook')->where('id', $entryId)->delete();
    }

    public function configureGuestbook(int $widgetId): string
    {
        $widget = $this->widget($widgetId);
        $this->need((bool) $widget && $widget->widget_key === 'guestbookwidget', 'Not a guestbook.', 400);
        $this->requireOwner($widget);
        $next = ($widget->privacy ?? 'public') === 'private' ? 'public' : 'private';
        DB::connection('holodb')->table('phpretro_myhabbo_layouts')->where('id', $widgetId)->update(['privacy' => $next]);

        return $next;
    }

    public function friends(int $userId, string $search = '', int $offset = 0, int $limit = 20): array
    {
        try {
            $q = DB::connection('holodb')->table('messenger_friendships as f')
                ->join('users as u', 'u.id', '=', 'f.user_two_id')
                ->where('f.user_one_id', $userId);
            if (trim($search) !== '') {
                $q->where('u.username', 'like', '%'.$search.'%');
            }

            return $q->orderBy('u.username')->offset(max(0, $offset))->limit(min(50, max(1, $limit)))
                ->get(['u.id', 'u.username', 'u.look', 'u.account_created', 'u.online'])->all();
        } catch (\Throwable) {
            return [];
        }
    }

    public function friendCount(int $userId): int
    {
        try {
            return (int) DB::connection('holodb')->table('messenger_friendships')->where('user_one_id', $userId)->count();
        } catch (\Throwable) {
            return 0;
        }
    }

    public function groups(int $userId): array
    {
        try {
            return DB::connection('holodb')->table('guilds_members as m')
                ->join('guilds as g', 'g.id', '=', 'm.guild_id')
                ->where('m.user_id', $userId)->whereIn('m.level_id', [0, 1, 2])
                ->orderBy('g.name')
                ->get(['g.id', 'g.name', 'g.badge', 'm.level_id'])
                ->all();
        } catch (\Throwable) {
            return [];
        }
    }

    public function rooms(int $userId): array
    {
        try {
            return DB::connection('holodb')->table('rooms')->where('owner_id', $userId)->orderBy('id')->get(['id', 'name', 'description'])->all();
        } catch (\Throwable) {
            return [];
        }
    }

    public function padList(int $count, int $size = 20): int
    {
        return max(0, $size - $count);
    }

    public function rate(int $ownerId, int $widgetId, int $rating): array
    {
        $this->need($this->actor > 0, 'Sign in required.', 401);
        $this->need($ownerId > 0 && $ownerId !== $this->actor, 'You cannot vote for yourself.', 400);
        $this->need($rating >= 1 && $rating <= 5, 'Rating must be between 1 and 5.', 400);
        $widget = $this->widget($widgetId);
        $this->need((bool) $widget && $widget->widget_key === 'ratingwidget' && (int) $widget->user_id === $ownerId, 'Not a rating widget.', 400);
        if (Schema::connection('holodb')->hasTable('phpretro_home_ratings')) {
            $existing = DB::connection('holodb')->table('phpretro_home_ratings')
                ->where('profile_user_id', $ownerId)
                ->where('rater_id', $this->actor)
                ->exists();
            if (! $existing) {
                DB::connection('holodb')->table('phpretro_home_ratings')->insert([
                    'profile_user_id' => $ownerId,
                    'rater_id' => $this->actor,
                    'rating' => $rating,
                    'created_at' => time(),
                ]);
            }
        }

        return $this->ratingSummary($ownerId);
    }

    public function resetRatings(int $ownerId, int $widgetId): array
    {
        $this->need($ownerId === $this->actor, 'Not permitted.', 403);
        $widget = $this->widget($widgetId);
        $this->need((bool) $widget && $widget->widget_key === 'ratingwidget' && (int) $widget->user_id === $ownerId, 'Not a rating widget.', 400);
        if (Schema::connection('holodb')->hasTable('phpretro_home_ratings')) {
            DB::connection('holodb')->table('phpretro_home_ratings')->where('profile_user_id', $ownerId)->delete();
        }

        return $this->ratingSummary($ownerId);
    }

    public function ratingSummary(int $ownerId): array
    {
        $total = 0;
        $tally = 0;
        $high = 0;
        $mine = false;
        try {
            if (Schema::connection('holodb')->hasTable('phpretro_home_ratings')) {
                $row = DB::connection('holodb')->table('phpretro_home_ratings')
                    ->where('profile_user_id', $ownerId)
                    ->selectRaw('COUNT(*) as total, COALESCE(SUM(rating), 0) as tally, SUM(CASE WHEN rating > 3 THEN 1 ELSE 0 END) as high')
                    ->first();
                $total = (int) ($row->total ?? 0);
                $tally = (int) ($row->tally ?? 0);
                $high = (int) ($row->high ?? 0);
                if ($this->actor > 0) {
                    $mine = DB::connection('holodb')->table('phpretro_home_ratings')
                        ->where('profile_user_id', $ownerId)
                        ->where('rater_id', $this->actor)
                        ->exists();
                }
            }
        } catch (\Throwable) {
        }
        $average = $total === 0 ? 0.0 : round($tally / $total, 1);

        return [
            'total' => $total,
            'high' => $high,
            'average' => $average,
            'px' => (int) ceil(($average * 150) / 5),
            'mine' => $mine,
            'owner' => $ownerId === $this->actor,
        ];
    }

    private function saveWidgetCoords(string $raw, int $guildId): void
    {
        foreach ($this->parseCoords($raw) as $id => $coords) {
            $column = $coords['x'] >= 450 ? 2 : 1;
            $position = max(0, (int) floor($coords['y'] / 50));
            $q = DB::connection('holodb')->table('phpretro_myhabbo_layouts')->where('id', $id);
            if ($guildId > 0) {
                $q->where('guild_id', $guildId);
            } else {
                $q->where('user_id', $this->actor)->where('guild_id', 0);
            }
            $q->update(['column_number' => $column, 'position' => $position]);
        }
    }

    private function saveItemCoords(string $raw, string $type, int $guildId): void
    {
        foreach ($this->parseCoords($raw) as $id => $coords) {
            $item = DB::connection('holodb')->table('phpretro_homes_items')->where('id', $id)->where('item_type', $type)->first();
            if (! $item || (int) $item->placed !== 1) {
                continue;
            }
            if ($guildId > 0) {
                if ((int) $item->guild_id !== $guildId) {
                    continue;
                }
            } elseif ((int) $item->user_id !== $this->actor || (int) $item->guild_id !== 0) {
                continue;
            }
            DB::connection('holodb')->table('phpretro_homes_items')->where('id', $id)->update([
                'x' => $coords['x'], 'y' => $coords['y'], 'z' => $coords['z'],
            ]);
        }
    }

    private function saveBackground(string $raw, int $guildId): void
    {
        $bits = explode(':', $raw, 2);
        $id = (int) $bits[0];
        if ($id < 1) {
            return;
        }
        $item = DB::connection('holodb')->table('phpretro_homes_items')->where('id', $id)->first();
        if (! $item || $item->item_type !== 'background' || (int) $item->user_id !== $this->actor) {
            return;
        }
        $q = DB::connection('holodb')->table('phpretro_homes_items')->where('item_type', 'background')->where('placed', 1);
        if ($guildId > 0) {
            $q->where('guild_id', $guildId);
        } else {
            $q->where('user_id', $this->actor)->where('guild_id', 0);
        }
        $q->update(['placed' => 0, 'guild_id' => 0]);
        DB::connection('holodb')->table('phpretro_homes_items')->where('id', $id)->where('user_id', $this->actor)->update([
            'placed' => 1, 'guild_id' => $guildId, 'x' => 0, 'y' => 0, 'z' => 0,
        ]);
    }

    private function parseCoords(string $raw): array
    {
        $out = [];
        foreach (explode('/', $raw) as $piece) {
            if ($piece === '') {
                continue;
            }
            $bits = explode(':', $piece, 2);
            $id = (int) $bits[0];
            if ($id < 1 || ! isset($bits[1])) {
                continue;
            }
            $coords = explode(',', $bits[1]);
            $out[$id] = ['x' => (int) ($coords[0] ?? 0), 'y' => (int) ($coords[1] ?? 0), 'z' => (int) ($coords[2] ?? 0)];
        }

        return $out;
    }

    private function queryRows(callable $fn): array
    {
        try {
            if (! Schema::connection('holodb')->hasTable('phpretro_myhabbo_layouts')) {
                return [];
            }

            return $fn();
        } catch (\Throwable) {
            return [];
        }
    }
}
