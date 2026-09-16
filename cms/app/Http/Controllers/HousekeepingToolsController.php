<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HousekeepingToolsController extends Controller
{
    use RendersHousekeeping;

    public function campaigns(Request $request): View|RedirectResponse
    {
        $action = (string) $request->query('do', 'list');
        if ($request->isMethod('post')) {
            $id = (int) $request->input('id', 0);
            if ($action === 'delete') {
                $n = $this->holodbHas('phpretro_campaigns')
                    ? $this->holodb()->table('phpretro_campaigns')->where('id', $id)->delete()
                    : 0;

                return redirect('/housekeeping/campaigns')->with('hk_notice', $n > 0 ? 'Campaign removed.' : 'Campaign not found.');
            }

            $visible = $request->boolean('visible') ? '1' : '0';
            $name = trim((string) $request->input('name', ''));
            $image = trim((string) $request->input('image', ''));
            if ($name === '' || $image === '') {
                return redirect('/housekeeping/campaigns?do='.($id > 0 ? 'edit&id='.$id : 'create'))
                    ->with('hk_error', 'Name and image are required.')->withInput();
            }

            $payload = [
                'name' => $name,
                'desc' => trim((string) $request->input('desc', '')),
                'image' => $image,
                'url' => trim((string) $request->input('url', '')),
                'visible' => $visible,
                'sort_order' => max(1, (int) $request->input('sort_order', 1)),
            ];
            if ($id > 0) {
                $this->holodb()->table('phpretro_campaigns')->where('id', $id)->update($payload);
                $notice = 'Campaign updated.';
            } else {
                $this->holodb()->table('phpretro_campaigns')->insert($payload);
                $notice = 'Campaign created.';
            }

            return redirect('/housekeeping/campaigns')->with('hk_notice', $notice);
        }

        $item = (object) ['id' => 0, 'name' => '', 'desc' => '', 'image' => '', 'url' => '', 'visible' => '1', 'sort_order' => 1];
        if ($action === 'edit') {
            $loaded = $this->holodbHas('phpretro_campaigns')
                ? $this->holodb()->table('phpretro_campaigns')->where('id', (int) $request->query('id', 0))->first()
                : null;
            if ($loaded) {
                $item = $loaded;
            }
        }

        $rows = $this->holodbHas('phpretro_campaigns')
            ? $this->holodb()->table('phpretro_campaigns')->orderBy('sort_order')->orderBy('id')->get()
            : collect();

        return $this->hk($request, 'Campaigns', 'tools', 'housekeeping.campaigns', [
            'action' => $action,
            'item' => $item,
            'rows' => $rows,
        ]);
    }

    public function news(Request $request): View|RedirectResponse
    {
        $action = (string) $request->query('do', 'list');
        if ($request->isMethod('post')) {
            $id = (int) $request->input('id', 0);
            if ($action === 'delete') {
                $n = $this->holodbHas('phpretro_news')
                    ? $this->holodb()->table('phpretro_news')->where('id', $id)->delete()
                    : 0;

                return redirect('/housekeeping/news')->with('hk_notice', $n > 0 ? 'Article removed.' : 'Article not found.');
            }

            $title = trim((string) $request->input('title', ''));
            $summary = trim((string) $request->input('summary', ''));
            $story = trim((string) $request->input('story', ''));
            $author = trim((string) $request->input('author', ''));
            if ($title === '' || $summary === '' || $story === '' || $author === '') {
                return redirect('/housekeeping/news?do='.($id > 0 ? 'edit&id='.$id : 'create'))
                    ->with('hk_error', 'Title, summary, story, and author are required.')->withInput();
            }

            $payload = [
                'title' => $title,
                'summary' => $summary,
                'story' => $story,
                'author' => $author,
                'categories' => trim((string) $request->input('categories', '')),
                'images' => trim((string) $request->input('images', '')),
            ];
            if ($id > 0) {
                $this->holodb()->table('phpretro_news')->where('id', $id)->update($payload);
                $notice = 'Article updated.';
            } else {
                $payload['time'] = time();
                $this->holodb()->table('phpretro_news')->insert($payload);
                $notice = 'Article created.';
            }

            return redirect('/housekeeping/news')->with('hk_notice', $notice);
        }

        $item = (object) ['id' => 0, 'title' => '', 'summary' => '', 'story' => '', 'author' => '', 'categories' => '', 'images' => ''];
        if ($action === 'edit' && $this->holodbHas('phpretro_news')) {
            $loaded = $this->holodb()->table('phpretro_news')->where('id', (int) $request->query('id', 0))->first();
            if ($loaded) {
                $item = $loaded;
            }
        }

        $rows = $this->holodbHas('phpretro_news')
            ? $this->holodb()->table('phpretro_news')->orderByDesc('time')->orderByDesc('id')->get()
            : collect();

        return $this->hk($request, 'News', 'tools', 'housekeeping.news', [
            'action' => $action,
            'item' => $item,
            'rows' => $rows,
        ]);
    }

    public function banners(Request $request): View|RedirectResponse
    {
        $action = (string) $request->query('do', 'list');
        if ($request->isMethod('post')) {
            $id = (int) $request->input('id', 0);
            if ($action === 'delete') {
                $n = $this->holodbHas('phpretro_banners')
                    ? $this->holodb()->table('phpretro_banners')->where('id', $id)->delete()
                    : 0;

                return redirect('/housekeeping/banners')->with('hk_notice', $n > 0 ? 'Banner removed.' : 'Banner not found.');
            }

            $html = (string) $request->input('html', '');
            $text = trim((string) $request->input('text', ''));
            $banner = trim((string) $request->input('banner', ''));
            if ($text === '' && $banner === '' && $html === '') {
                return redirect('/housekeeping/banners?do='.($id > 0 ? 'edit&id='.$id : 'create'))
                    ->with('hk_error', 'Text, image, or HTML is required.')->withInput();
            }

            $payload = [
                'text' => $text,
                'banner' => $banner,
                'url' => trim((string) $request->input('url', '')),
                'status' => $request->boolean('status') ? '1' : '0',
                'advanced' => $html !== '' ? '1' : '0',
                'html' => $html,
                'sort_order' => max(1, (int) $request->input('sort_order', 1)),
            ];
            if ($id > 0) {
                $this->holodb()->table('phpretro_banners')->where('id', $id)->update($payload);
                $notice = 'Banner updated.';
            } else {
                $this->holodb()->table('phpretro_banners')->insert($payload);
                $notice = 'Banner created.';
            }

            return redirect('/housekeeping/banners')->with('hk_notice', $notice);
        }

        $item = (object) ['id' => 0, 'text' => '', 'banner' => '', 'url' => '', 'status' => '1', 'advanced' => '0', 'html' => '', 'sort_order' => 1];
        if ($action === 'edit' && $this->holodbHas('phpretro_banners')) {
            $loaded = $this->holodb()->table('phpretro_banners')->where('id', (int) $request->query('id', 0))->first();
            if ($loaded) {
                $item = $loaded;
            }
        }

        $rows = $this->holodbHas('phpretro_banners')
            ? $this->holodb()->table('phpretro_banners')->orderBy('sort_order')->orderBy('id')->get()
            : collect();

        return $this->hk($request, 'Banners', 'tools', 'housekeeping.banners', [
            'action' => $action,
            'item' => $item,
            'rows' => $rows,
        ]);
    }

    public function catalogue(Request $request): View|RedirectResponse
    {
        $types = ['1' => 'Sticker', '4' => 'Background'];
        $placements = ['-1' => 'Groups only', '0' => 'Anywhere', '1' => 'Homes only'];
        $action = (string) $request->query('do', 'list');

        if ($request->isMethod('post')) {
            $id = (int) $request->input('id', 0);
            if ($action === 'delete') {
                $n = $this->holodbHas('phpretro_homes_catalogue')
                    ? $this->holodb()->table('phpretro_homes_catalogue')->where('id', $id)->delete()
                    : 0;

                return redirect('/housekeeping/catalogue')->with('hk_notice', $n > 0 ? 'Item removed.' : 'Item not found.');
            }

            $type = (string) $request->input('type', '1');
            if (! isset($types[$type])) {
                $type = '1';
            }
            $where = (string) $request->input('where', '0');
            if (! isset($placements[$where])) {
                $where = '0';
            }
            $data = trim((string) $request->input('data', ''));
            $price = (int) $request->input('price', 0);
            $amount = (int) $request->input('amount', 1);
            $minrank = (int) $request->input('minrank', 1);
            if ($data === '' || $price < 0 || $amount < 1 || $minrank < 1) {
                return redirect('/housekeeping/catalogue?do='.($id > 0 ? 'edit&id='.$id : 'create'))
                    ->with('hk_error', 'Data, a numeric price, amount, and min rank are required.')->withInput();
            }

            $payload = [
                'name' => trim((string) $request->input('name', '')),
                'desc' => trim((string) $request->input('desc', '')),
                'type' => $type,
                'data' => $data,
                'price' => $price,
                'amount' => $amount,
                'category' => trim((string) $request->input('category', '')),
                'minrank' => $minrank,
                'where' => $where,
            ];
            if ($id > 0) {
                $this->holodb()->table('phpretro_homes_catalogue')->where('id', $id)->update($payload);
                $notice = 'Item updated.';
            } else {
                $this->holodb()->table('phpretro_homes_catalogue')->insert($payload);
                $notice = 'Item created.';
            }

            return redirect('/housekeeping/catalogue')->with('hk_notice', $notice);
        }

        $item = (object) ['id' => 0, 'name' => '', 'desc' => '', 'type' => '1', 'data' => '', 'price' => 2, 'amount' => 1, 'category' => '', 'minrank' => 1, 'where' => '0'];
        if ($action === 'edit' && $this->holodbHas('phpretro_homes_catalogue')) {
            $loaded = $this->holodb()->table('phpretro_homes_catalogue')->where('id', (int) $request->query('id', 0))->first();
            if ($loaded) {
                $item = $loaded;
            }
        }

        $rows = $this->holodbHas('phpretro_homes_catalogue')
            ? $this->holodb()->table('phpretro_homes_catalogue')->orderBy('type')->orderBy('category')->orderBy('name')->get()
            : collect();

        return $this->hk($request, 'Catalogue', 'tools', 'housekeeping.catalogue', [
            'action' => $action,
            'item' => $item,
            'rows' => $rows,
            'types' => $types,
            'placements' => $placements,
        ]);
    }

    public function collectables(Request $request): View|RedirectResponse
    {
        $action = (string) $request->query('do', 'list');
        if ($request->isMethod('post')) {
            $id = (int) $request->input('id', 0);
            if ($action === 'delete') {
                $n = $this->holodbHas('phpretro_collectibles')
                    ? $this->holodb()->table('phpretro_collectibles')->where('id', $id)->delete()
                    : 0;

                return redirect('/housekeeping/collectables')->with('hk_notice', $n > 0 ? 'Collectible removed.' : 'Collectible not found.');
            }

            $name = trim((string) $request->input('name', ''));
            $description = trim((string) $request->input('description', ''));
            $image = trim((string) $request->input('image', ''));
            $time = (int) $request->input('time', 0);
            if ($name === '' || $description === '' || $image === '' || $time <= 0) {
                return redirect('/housekeeping/collectables?do='.($id > 0 ? 'edit&id='.$id : 'create'))
                    ->with('hk_error', 'Name, description, image, and month timestamp are required.')->withInput();
            }

            $payload = compact('name', 'description', 'image', 'time');
            if ($id > 0) {
                $this->holodb()->table('phpretro_collectibles')->where('id', $id)->update($payload);
                $notice = 'Collectible updated.';
            } else {
                $this->holodb()->table('phpretro_collectibles')->insert($payload);
                $notice = 'Collectible created.';
            }

            return redirect('/housekeeping/collectables')->with('hk_notice', $notice);
        }

        $item = (object) ['id' => 0, 'name' => '', 'description' => '', 'image' => '', 'time' => strtotime(date('Y-m-01 00:00:00'))];
        if ($action === 'edit' && $this->holodbHas('phpretro_collectibles')) {
            $loaded = $this->holodb()->table('phpretro_collectibles')->where('id', (int) $request->query('id', 0))->first();
            if ($loaded) {
                $item = $loaded;
            }
        }

        $rows = $this->holodbHas('phpretro_collectibles')
            ? $this->holodb()->table('phpretro_collectibles')->orderByDesc('time')->get()
            : collect();

        return $this->hk($request, 'Collectibles', 'tools', 'housekeeping.collectables', [
            'action' => $action,
            'item' => $item,
            'rows' => $rows,
        ]);
    }

    public function faq(Request $request): View|RedirectResponse
    {
        $action = (string) $request->query('do', 'list');
        if ($request->isMethod('post')) {
            $id = (int) $request->input('id', 0);
            if ($action === 'delete') {
                $n = $this->holodbHas('phpretro_faq')
                    ? $this->holodb()->table('phpretro_faq')->where('id', $id)->delete()
                    : 0;

                return redirect('/housekeeping/faq')->with('hk_notice', $n > 0 ? 'FAQ entry removed.' : 'FAQ entry not found.');
            }

            $question = trim((string) $request->input('question', ''));
            $answer = trim((string) $request->input('answer', ''));
            if ($question === '' || $answer === '') {
                return redirect('/housekeeping/faq?do='.($id > 0 ? 'edit&id='.$id : 'create'))
                    ->with('hk_error', 'Question and answer are required.')->withInput();
            }

            $payload = [
                'category' => trim((string) $request->input('category', 'general')),
                'question' => $question,
                'answer' => $answer,
                'sort_order' => (int) $request->input('sort_order', 0),
                'active' => $request->boolean('active') ? 1 : 0,
            ];
            if ($id > 0) {
                $this->holodb()->table('phpretro_faq')->where('id', $id)->update($payload);
                $notice = 'FAQ entry updated.';
            } else {
                $this->holodb()->table('phpretro_faq')->insert($payload);
                $notice = 'FAQ entry created.';
            }

            return redirect('/housekeeping/faq')->with('hk_notice', $notice);
        }

        $entry = (object) ['id' => 0, 'category' => 'general', 'question' => '', 'answer' => '', 'sort_order' => 0, 'active' => 1];
        if ($action === 'edit' && $this->holodbHas('phpretro_faq')) {
            $loaded = $this->holodb()->table('phpretro_faq')->where('id', (int) $request->query('id', 0))->first();
            if ($loaded) {
                $entry = $loaded;
            }
        }

        $entries = $this->holodbHas('phpretro_faq')
            ? $this->holodb()->table('phpretro_faq')->orderBy('category')->orderBy('sort_order')->orderBy('id')->get()
            : collect();

        return $this->hk($request, 'FAQ', 'tools', 'housekeeping.faq', [
            'action' => $action,
            'entry' => $entry,
            'entries' => $entries,
        ]);
    }

    public function newsletter(Request $request): View|RedirectResponse
    {
        if ($request->isMethod('post')) {
            return redirect('/housekeeping/newsletter')->with(
                'hk_error',
                'This website does not send newsletters. PolarIS has no mailer and this conversion will not fake a send.'
            );
        }

        $template = '<h1>[MESSAGE HEADER]</h1><p>[NEWSLETTER MESSAGE]</p>';

        return $this->hk($request, 'Newsletter', 'tools', 'housekeeping.newsletter', [
            'defaultMessage' => $template,
        ]);
    }

    public function recommended(Request $request): View|RedirectResponse
    {
        $action = (string) $request->query('do', 'list');
        $searchResults = [];

        if ($request->isMethod('post') && $request->has('search')) {
            $query = trim((string) $request->input('query', ''));
            if ($query !== '' && $this->holodbHas('rooms')) {
                $like = '%'.$query.'%';
                $searchResults = $this->holodb()->table('rooms')
                    ->where('name', 'like', $like)
                    ->orWhere('description', 'like', $like)
                    ->orderByDesc('id')
                    ->limit(50)
                    ->get(['id', 'name']);
            }
        } elseif ($request->isMethod('post')) {
            $id = (int) $request->input('id', 0);
            if ($action === 'delete') {
                $n = $this->holodbHas('phpretro_recommended')
                    ? $this->holodb()->table('phpretro_recommended')->where('id', $id)->delete()
                    : 0;

                return redirect('/housekeeping/recommended')->with('hk_notice', $n > 0 ? 'Recommended entry removed.' : 'Recommended entry not found.');
            }

            $type = (string) $request->input('type', 'group');
            if (! in_array($type, ['group', 'room'], true)) {
                $type = 'group';
            }
            $sponsored = $request->input('sponsered') === '1' ? '1' : '0';
            $recId = (int) $request->input('rec_id', 0);
            if ($recId < 1) {
                return redirect('/housekeeping/recommended?do='.($id > 0 ? 'edit&id='.$id : 'create'))
                    ->with('hk_error', 'A valid room or group id is required.')->withInput();
            }
            if ($type === 'room' && $sponsored !== '0') {
                return redirect('/housekeeping/recommended?do=create')->with('hk_error', 'Rooms can only be staff picks.');
            }

            $payload = ['rec_id' => $recId, 'type' => $type, 'sponsered' => $sponsored];
            if ($id > 0) {
                $this->holodb()->table('phpretro_recommended')->where('id', $id)->update($payload);
                $notice = 'Recommended entry updated.';
            } else {
                $this->holodb()->table('phpretro_recommended')->insert($payload);
                $notice = 'Recommended entry created.';
            }

            return redirect('/housekeeping/recommended')->with('hk_notice', $notice);
        }

        $item = (object) [
            'id' => 0,
            'rec_id' => (int) $request->query('recid', 0),
            'type' => $request->query('recid') ? 'room' : 'group',
            'sponsered' => '0',
        ];
        if ($action === 'edit' && $this->holodbHas('phpretro_recommended')) {
            $loaded = $this->holodb()->table('phpretro_recommended')->where('id', (int) $request->query('id', 0))->first();
            if ($loaded) {
                $item = $loaded;
            }
        }

        $rows = $this->holodbHas('phpretro_recommended')
            ? $this->holodb()->table('phpretro_recommended')->orderBy('sponsered')->orderBy('id')->get()
            : collect();

        return $this->hk($request, 'Recommended', 'tools', 'housekeeping.recommended', [
            'action' => $action,
            'item' => $item,
            'rows' => $rows,
            'searchResults' => $searchResults,
        ]);
    }

    public function vouchers(Request $request): View|RedirectResponse
    {
        if ($request->isMethod('post')) {
            return redirect('/housekeeping/vouchers')->with(
                'hk_error',
                'PolarIS vouchers are not written from this website. This page will not fake a voucher grant.'
            );
        }

        $action = (string) $request->query('do', 'list');
        $voucher = (object) ['id' => 0, 'code' => '', 'credits' => 0, 'points' => 0, 'points_type' => 0, 'catalog_item_id' => 0, 'amount' => 1, 'redemption_limit' => -1];
        if ($action === 'edit' && $this->holodbHas('vouchers')) {
            $loaded = $this->holodb()->table('vouchers')->where('id', (int) $request->query('id', 0))->first();
            if ($loaded) {
                $voucher = $loaded;
                $voucher->redemption_limit = $loaded->limit ?? $loaded->redemption_limit ?? -1;
            }
        }

        $rows = $this->holodbHas('vouchers')
            ? $this->holodb()->table('vouchers')->orderByDesc('id')->get()
            : collect();

        return $this->hk($request, 'Vouchers', 'tools', 'housekeeping.vouchers', [
            'action' => $action,
            'voucher' => $voucher,
            'rows' => $rows,
        ]);
    }
}
