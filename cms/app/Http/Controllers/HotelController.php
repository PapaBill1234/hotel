<?php

namespace App\Http\Controllers;

use App\Services\PolarisAuthService;
use App\Support\Hotel;
use App\Support\PolarisUser;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class HotelController extends Controller
{
    public function __construct(private PolarisAuthService $auth) {}

    public function landing(Request $request): View
    {
        return view('hotel.landing', [
            'error' => $request->session()->get('login_error'),
            'username' => old('username', $request->query('username', '')),
            'pageName' => 'Home',
            'bodyId' => 'landing',
        ]);
    }

    public function login(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'username' => ['required', 'string', 'max:25'],
            'password' => ['required', 'string', 'max:64'],
        ]);

        $user = $this->auth->attempt($validated['username'], $validated['password']);
        if (! $user) {
            return redirect()->to('/?username='.rawurlencode($validated['username']).'&rememberme=false')
                ->with('login_error', 'Incorrect username or password');
        }

        $request->session()->regenerate();
        $request->session()->put('hotel.user_id', $user->id);

        $next = $request->input('page');
        if (is_string($next) && str_starts_with($next, '/') && ! str_starts_with($next, '//')) {
            return redirect($next);
        }

        return redirect('/me');
    }

    public function logout(Request $request): View
    {
        $request->session()->forget('hotel.user_id');
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return view('hotel.logout', [
            'pageName' => 'Logged out',
            'bodyId' => 'logout',
        ]);
    }

    public function me(Request $request): View
    {
        $user = $this->hotelUser($request);
        $news = $this->newsList(5);
        $campaigns = $this->campaigns();

        return view('hotel.me', [
            'pageId' => 'me',
            'pageName' => 'Home',
            'bodyId' => 'home',
            'cat' => 'home',
            'hotelUser' => $user,
            'news' => $news,
            'campaigns' => $campaigns,
            'avatar' => Hotel::avatarUrl($user->look),
            'hotelView' => Hotel::setting('site_hotel_image', 'htlview_gb.png'),
        ]);
    }

    public function profile(Request $request): View
    {
        $user = $this->hotelUser($request);

        return view('hotel.profile', [
            'pageId' => 'profile',
            'pageName' => 'Account Settings',
            'bodyId' => 'home',
            'cat' => 'home',
            'hotelUser' => $user,
            'notice' => $request->session()->get('profile_notice'),
        ]);
    }

    public function updateProfile(Request $request): RedirectResponse
    {
        $user = $this->hotelUser($request);
        $data = $request->validate([
            'motto' => ['nullable', 'string', 'max:127'],
            'look' => ['required', 'string', 'max:256'],
            'gender' => ['required', 'in:M,F'],
        ]);

        DB::connection('holodb')->update(
            'UPDATE users SET motto = ?, look = ?, gender = ? WHERE id = ?',
            [$data['motto'] ?? '', $data['look'], $data['gender'], $user->id]
        );

        return redirect('/profile')->with('profile_notice', 'Your changes have been saved.');
    }

    public function community(Request $request): View
    {
        $user = $request->attributes->get('hotelUser');
        $promo = $this->hotelViewNews(5);
        $random = [];
        try {
            $random = DB::connection('holodb')->table('users')
                ->orderByRaw(Hotel::randomOrderSql())
                ->limit(18)
                ->get();
        } catch (\Throwable) {
            $random = [];
        }

        $rooms = [];
        try {
            if (Schema::connection('holodb')->hasTable('rooms')) {
                $rooms = DB::connection('holodb')->table('rooms')
                    ->where('is_staff_picked', '1')
                    ->orderByDesc('users')
                    ->limit(5)
                    ->get();
            }
        } catch (\Throwable) {
            $rooms = [];
        }

        return view('hotel.community', [
            'pageId' => 'community',
            'pageName' => 'Community',
            'bodyId' => 'home',
            'cat' => 'community',
            'hotelUser' => $user,
            'promo' => $promo,
            'randomUsers' => $random,
            'rooms' => $rooms,
        ]);
    }

    public function articles(Request $request, ?string $article = null): View
    {
        $user = $request->attributes->get('hotelUser');
        $list = $this->newsList(20);
        $id = (int) $request->query('id', 0);
        if ($article && preg_match('/^(\d+)/', $article, $m)) {
            $id = (int) $m[1];
        }

        $current = $id > 0
            ? collect($list)->firstWhere('id', $id) ?? ($list[0] ?? null)
            : ($list[0] ?? null);

        return view('hotel.articles', [
            'pageId' => 'news',
            'pageName' => 'News'.($current ? ' - '.$current->title : ''),
            'bodyId' => 'news',
            'cat' => 'community',
            'hotelUser' => $user,
            'newsList' => $list,
            'current' => $current,
        ]);
    }

    public function forgot(): View
    {
        return view('hotel.forgot', [
            'pageName' => 'Forgotten password',
            'bodyId' => '',
            'notice' => session('forgot_notice'),
        ]);
    }

    public function forgotSubmit(Request $request): RedirectResponse
    {
        $request->validate([
            'forgottenpw-username' => ['nullable', 'string', 'max:25'],
            'forgottenpw-email' => ['nullable', 'email', 'max:255'],
            'ownerEmailAddress' => ['nullable', 'email', 'max:255'],
        ]);

        return redirect('/account/password/forgot')->with(
            'forgot_notice',
            'This website does not email passwords or account lists. Ask hotel staff if you need the account recovered.'
        );
    }

    public function register(): View
    {
        return view('hotel.register', [
            'pageName' => 'Register',
            'bodyId' => 'landing',
            'figures' => $this->registerFigures(),
            'error' => session('register_error'),
        ]);
    }

    public function registerSubmit(Request $request): RedirectResponse
    {
        return redirect('/register')->with(
            'register_error',
            'Website registration is not enabled. This conversion will not insert PolarIS users_settings. Use an existing hotel account.'
        );
    }

    public function home(Request $request, string $name): View
    {
        $profile = $this->findHomeProfile($name);
        abort_unless($profile, 404);

        $viewer = $request->attributes->get('hotelUser');
        $homes = new \App\Support\Homes((int) ($viewer?->id ?? 0));
        $edit = $viewer && (int) $viewer->id === (int) $profile->id && $request->session()->get('hotel.page_edit') === 'home';
        $widgets = $homes->displayLayouts((int) $profile->id);
        $placed = $homes->placedItems((int) $profile->id);
        $stickers = array_values(array_filter($placed, fn ($i) => $i->item_type === 'sticker'));
        $stickies = array_values(array_filter($placed, fn ($i) => $i->item_type === 'stickie'));
        $guestbookWidget = collect($widgets)->first(fn ($w) => $w->widget_key === 'guestbookwidget');

        return view('hotel.home', [
            'pageId' => 'home',
            'pageName' => $profile->username,
            'bodyId' => $edit ? 'editmode' : 'viewmode',
            'cat' => 'home',
            'hotelUser' => $viewer,
            'profile' => $profile,
            'homes' => $homes,
            'widgets' => $widgets,
            'stickers' => $stickers,
            'stickies' => $stickies,
            'background' => $homes->backgroundClass((int) $profile->id),
            'isOwner' => $viewer && (int) $viewer->id === (int) $profile->id,
            'edit' => $edit,
            'friends' => $homes->friends((int) $profile->id),
            'friendCount' => $homes->friendCount((int) $profile->id),
            'groups' => $homes->groups((int) $profile->id),
            'rooms' => $homes->rooms((int) $profile->id),
            'guestbook' => $guestbookWidget ? $homes->guestbookEntriesForWidget($guestbookWidget) : $homes->guestbookEntries((int) $profile->id),
            'ratingSummary' => $homes->ratingSummary((int) $profile->id),
        ]);
    }

    public function papers(Request $request, string $page = 'privacy'): View
    {
        $key = $page === 'disclaimer' ? 'paper_disclaimer' : 'paper_privacy';
        $title = $page === 'disclaimer' ? 'Disclaimer' : 'Privacy Policy';

        return view('hotel.papers', [
            'pageId' => 'papers',
            'pageName' => $title,
            'bodyId' => 'home',
            'cat' => 'community',
            'hotelUser' => $request->attributes->get('hotelUser'),
            'paperTitle' => $title,
            'paperBody' => Hotel::setting($key, $title.' is not configured yet.'),
            'hotelPolicy' => Hotel::setting('paper_hotel_policy', '<ol><li>Keep it clean</li><li>Be nice</li><li>Don\'t scam</li><li>Don\'t use bots</li><li>Have fun</li></ol>'),
        ]);
    }

    public function help(Request $request, ?string $id = null): View
    {
        $groups = [];
        $search = trim((string) $request->input('query', $request->query('query', '')));
        $selectedId = (int) ($id ?? 0);
        $selectedCategory = null;

        try {
            if (Schema::connection('holodb')->hasTable('phpretro_faq')) {
                $q = DB::connection('holodb')->table('phpretro_faq')->where('active', 1);
                if ($search !== '') {
                    $q->where(function ($inner) use ($search) {
                        $inner->where('question', 'like', '%'.$search.'%')
                            ->orWhere('answer', 'like', '%'.$search.'%')
                            ->orWhere('category', 'like', '%'.$search.'%');
                    });
                }
                $rows = $q->orderBy('category')->orderBy('sort_order')->orderBy('id')->get();
                foreach ($rows as $row) {
                    $groups[$row->category][] = $row;
                    if ($selectedId > 0 && (int) $row->id === $selectedId) {
                        $selectedCategory = $row->category;
                    }
                }
            }
        } catch (\Throwable) {
            $groups = [];
        }

        if ($selectedCategory === null && $groups !== []) {
            $selectedCategory = array_key_first($groups);
        }

        return view('hotel.help', [
            'pageId' => 'help',
            'pageName' => 'Help',
            'bodyId' => 'faq',
            'cat' => 'community',
            'hotelUser' => $request->attributes->get('hotelUser'),
            'faqGroups' => $groups,
            'selectedCategory' => $selectedCategory,
            'search' => $search,
            'helpNotice' => $request->session()->get('help_notice'),
            'helpError' => $request->session()->get('help_error'),
        ]);
    }

    public function helpSearch(Request $request): View
    {
        return $this->help($request);
    }

    public function iot(): View
    {
        return view('hotel.iot', [
            'pageName' => 'PHPRetro Help Tool',
            'sent' => false,
            'error' => session('iot_error'),
        ]);
    }

    public function iotSubmit(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'username' => ['nullable', 'string', 'max:25'],
            'email' => ['nullable', 'email', 'max:255'],
            'subject' => ['required', 'string', 'max:50'],
            'message' => ['required', 'string', 'max:4000'],
        ]);

        $user = $request->attributes->get('hotelUser');
        if (Schema::connection('holodb')->hasTable('phpretro_helpdesk_tickets')) {
            DB::connection('holodb')->table('phpretro_helpdesk_tickets')->insert([
                'user_id' => $user?->id,
                'username' => $data['username'] ?? ($user?->username ?? ''),
                'email' => $data['email'] ?? ($user?->mail ?? ''),
                'ip' => substr((string) $request->ip(), 0, 45),
                'subject' => $data['subject'],
                'message' => $data['message'],
                'room_id' => 0,
                'status' => 'open',
                'created_at' => time(),
            ]);
        }

        return redirect('/help')->with('help_notice', 'Thanks. Hotel staff can read this ticket in housekeeping. This website does not email PolarIS.');
    }

    public function housekeeping(): View
    {
        return view('hotel.housekeeping', [
            'pageName' => 'Housekeeping',
            'bodyId' => 'landing',
        ]);
    }

    public function credits(Request $request): View
    {
        return view('hotel.credits', $this->creditsPage($request, 'credits', 'Coins'));
    }

    public function club(Request $request): View
    {
        return view('hotel.club', $this->creditsPage($request, 'club', 'PHPRetro Club'));
    }

    public function collectables(Request $request): View
    {
        $currentTime = mktime(0, 0, 0, (int) date('m'), 1, (int) date('Y'));
        $current = null;
        $showroom = [];
        try {
            if (Schema::connection('holodb')->hasTable('phpretro_collectibles')) {
                $current = DB::connection('holodb')->table('phpretro_collectibles')
                    ->where('time', $currentTime)
                    ->first();
                $showroom = DB::connection('holodb')->table('phpretro_collectibles')
                    ->where('time', '<', $currentTime)
                    ->orderByDesc('time')
                    ->get()
                    ->all();
            }
        } catch (\Throwable) {
        }

        return view('hotel.collectables', array_merge($this->creditsPage($request, 'collectables', 'Collectables'), [
            'currentCollectable' => $current,
            'showroom' => $showroom,
            'monthLabel' => date('F Y', $currentTime ?: time()),
        ]));
    }

    public function pixels(Request $request): View
    {
        return view('hotel.pixels', $this->creditsPage($request, 'pixels', 'Pixels'));
    }

    public function tag(Request $request): View
    {
        $user = $request->attributes->get('hotelUser');
        $tag = trim((string) $request->query('tag', $request->query('tagName', '')));

        return view('hotel.tag', [
            'pageId' => 'tags',
            'pageName' => 'Tag Search',
            'bodyId' => 'tags',
            'cat' => 'community',
            'hotelUser' => $user,
            'tagQuery' => $tag,
        ]);
    }

    public function welcome(Request $request): View
    {
        $user = $this->hotelUser($request);

        return view('hotel.welcome', [
            'pageId' => 'welcome',
            'pageName' => 'Welcome',
            'bodyId' => 'welcome',
            'cat' => 'home',
            'hotelUser' => $user,
            'avatar' => Hotel::avatarUrl($user->look),
            'welcomeText' => Hotel::setting('site_welcome_text', 'Welcome to '.Hotel::shortname().'!'),
        ]);
    }

    public function history(Request $request): View
    {
        $user = $this->hotelUser($request);
        $rows = [];
        try {
            if (Schema::connection('holodb')->hasTable('phpretro_transactions')) {
                $rows = DB::connection('holodb')->table('phpretro_transactions')
                    ->where('user_id', $user->id)
                    ->orderByDesc('created_at')
                    ->orderByDesc('id')
                    ->limit(100)
                    ->get()
                    ->all();
            }
        } catch (\Throwable) {
            $rows = [];
        }

        return view('hotel.history', [
            'pageId' => 'credits',
            'pageName' => 'Transaction history',
            'bodyId' => 'home',
            'cat' => 'credits',
            'hotelUser' => $user,
            'transactions' => $rows,
        ]);
    }

    public function loginPopup(Request $request): View|RedirectResponse
    {
        if ($request->session()->has('hotel.user_id')) {
            return redirect('/client');
        }

        return view('hotel.login-popup', [
            'pageName' => 'Sign in',
            'bodyId' => 'popup',
            'error' => $request->session()->get('login_error'),
            'username' => old('username', ''),
        ]);
    }

    public function rss()
    {
        $items = $this->newsList(10);
        $short = Hotel::shortname();
        $xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
        $xml .= '<rss xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:taxo="http://purl.org/rss/1.0/modules/taxonomy/" version="2.0">'."\n";
        $xml .= "  <channel>\n    <title>".htmlspecialchars($short, ENT_XML1).' ~</title>'."\n";
        $xml .= '    <link>'.htmlspecialchars(url('/'), ENT_XML1)."</link>\n    <description />\n";
        foreach ($items as $row) {
            $link = url('/articles/'.$row->id.'-'.Hotel::newsSlug($row->title));
            $xml .= "    <item>\n";
            $xml .= '      <title>'.htmlspecialchars((string) $row->title, ENT_XML1)."</title>\n";
            $xml .= '      <link>'.htmlspecialchars($link, ENT_XML1)."</link>\n";
            $xml .= '      <description>'.htmlspecialchars((string) $row->summary, ENT_XML1)."</description>\n";
            $xml .= '      <pubDate>'.date('D, j M Y H:i:s e', (int) $row->time)."</pubDate>\n";
            $xml .= '      <guid isPermaLink="false">'.htmlspecialchars($link, ENT_XML1)."</guid>\n";
            $xml .= '      <dc:date>'.date('c', (int) $row->time)."</dc:date>\n";
            $xml .= "    </item>\n";
        }
        $xml .= "  </channel>\n</rss>\n";

        return response($xml, 200)->header('Content-Type', 'text/xml; charset=UTF-8');
    }

    public function notFound(): View
    {
        return view('hotel.error', [
            'pageId' => 'error',
            'pageName' => 'Page not found',
            'bodyId' => 'home',
            'cat' => 'community',
            'hotelUser' => request()->attributes->get('hotelUser'),
        ]);
    }

    public function client(Request $request, PolarisAuthService $auth): View
    {
        $user = $this->hotelUser($request);
        $ticket = $auth->issueAuthTicket($user->id);

        return view('hotel.client', [
            'hotelUser' => $user,
            'ticket' => $ticket,
            'nitroUrl' => env('NITRO_CLIENT_URL', ''),
            'nitroHost' => env('NITRO_WS_HOST', ''),
            'nitroPort' => env('NITRO_WS_PORT', ''),
        ]);
    }

    public function tryout(Request $request): View
    {
        return view('hotel.tryout', [
            'pageId' => 'tryout',
            'pageName' => Hotel::shortname().' Club',
            'bodyId' => 'home',
            'cat' => 'credits',
            'hotelUser' => $request->attributes->get('hotelUser'),
        ]);
    }

    public function intermediate(): View
    {
        return view('hotel.intermediate', [
            'pageName' => 'Choose your destination',
            'bodyId' => 'intermediate',
        ]);
    }

    public function reauthenticate(Request $request): View|RedirectResponse
    {
        $user = $request->attributes->get('hotelUser');
        if (! $user) {
            return redirect('/');
        }

        return view('hotel.reauthenticate', [
            'pageName' => 'Log in to '.Hotel::shortname(),
            'bodyId' => 'reauthenticate',
            'hotelUser' => $user,
            'nextPage' => (string) $request->session()->get('hotel.page', $request->query('page', '')),
            'error' => $request->session()->get('login_error'),
        ]);
    }

    public function reauthenticateSubmit(Request $request): RedirectResponse
    {
        $user = $request->attributes->get('hotelUser');
        if (! $user) {
            return redirect('/');
        }
        $password = (string) $request->input('password', '');
        $ok = $this->auth->attempt($user->username, $password);
        $page = (string) $request->input('page', '');
        if (! $ok) {
            return redirect('/?page='.rawurlencode($page).'&username='.rawurlencode($user->username).'&error=1')
                ->with('login_error', 'Incorrect username or password');
        }
        $request->session()->forget('hotel.reauthenticate');
        $request->session()->put('hotel.page', $page);

        return redirect('/security_check');
    }

    public function securityCheck(Request $request): View|RedirectResponse
    {
        $user = $request->attributes->get('hotelUser');
        if (! $user) {
            return redirect('/');
        }
        $target = (string) $request->query('page', $request->session()->pull('hotel.page', ''));
        if ($target === '' || str_contains($target, 'security_check')) {
            $target = '/me';
        }
        if (! str_starts_with($target, '/') || str_starts_with($target, '//')) {
            $target = '/me';
        }
        $limit = (int) Hotel::setting('site_highload', '0');
        if ($limit > 0 && Hotel::onlineCount() > $limit && ! str_contains($target, 'client')) {
            $target = '/intermediate';
        }

        return view('hotel.security-check', ['target' => $target]);
    }

    public function emailVerify(Request $request): View
    {
        $token = (string) $request->query('token', '');
        $ok = false;
        $message = 'The verification code is invalid or the action has already been done.';
        if (preg_match('/^[a-f0-9]{64}$/', $token) && Schema::connection('holodb')->hasTable('phpretro_email_verification_tokens')) {
            $hash = hash('sha256', $token);
            $row = DB::connection('holodb')->table('phpretro_email_verification_tokens')
                ->where('token_hash', $hash)
                ->where('expires_at', '>', time())
                ->whereNull('used_at')
                ->first();
            if ($row) {
                DB::connection('holodb')->table('phpretro_email_verification_tokens')
                    ->where('id', $row->id)
                    ->whereNull('used_at')
                    ->update(['used_at' => time()]);
                $ok = true;
                $message = 'The verification token was recorded. PolarIS users.mail_verified is not written from this website.';
            }
        }

        return view('hotel.email', [
            'pageName' => 'Email Verification',
            'bodyId' => '',
            'ok' => $ok,
            'message' => $message,
        ]);
    }

    public function clientUtils(Request $request, string $key = ''): View
    {
        $key = $key !== '' ? $key : (string) $request->query('key', '');
        if ($key === 'error' && Hotel::setting('client_log_errors', '0') === '1' && Schema::connection('holodb')->hasTable('phpretro_client_errors')) {
            $user = $request->attributes->get('hotelUser');
            DB::connection('holodb')->table('phpretro_client_errors')->insert([
                'user_id' => $user?->id,
                'ip' => substr((string) $request->ip(), 0, 45),
                'error_type' => substr((string) $request->query('error_type', $request->query('error', 'unknown')), 0, 50),
                'message' => (string) $request->query('message', $request->query('error_message', 'Client error page displayed.')),
                'stack_trace' => $request->query('stack_trace', $request->query('stack')),
                'user_agent' => substr((string) $request->userAgent(), 0, 255),
                'url' => substr((string) $request->query('url', $request->getRequestUri()), 0, 255),
                'client_version' => substr((string) $request->query('client_version', ''), 0, 50) ?: null,
                'created_at' => time(),
            ]);
        }
        $title = match ($key) {
            'install_shockwave', 'upgrade_shockwave' => 'Shockwave detection',
            'error' => 'Oops',
            default => 'Connection failed',
        };

        return view('hotel.client-error', [
            'pageName' => 'Page error',
            'key' => $key,
            'title' => $title,
            'hotelUser' => $request->attributes->get('hotelUser'),
        ]);
    }

    public function cacheCheck(): \Illuminate\Http\Response
    {
        return response('true');
    }

    public function clientLog(): \Illuminate\Http\Response
    {
        return response('', 204);
    }

    public function unloadClient(): \Illuminate\Http\Response
    {
        return response('', 200);
    }

    public function homeById(Request $request, int $id): \Illuminate\Http\RedirectResponse
    {
        $name = '';
        try {
            $name = (string) (DB::connection('holodb')->table('users')->where('id', $id)->value('username') ?? '');
        } catch (\Throwable) {
        }
        abort_unless($name !== '', 404);

        return redirect('/home/'.$name);
    }

    private function hotelUser(Request $request): PolarisUser
    {
        /** @var PolarisUser $user */
        $user = $request->attributes->get('hotelUser');

        return $user;
    }

    private function creditsPage(Request $request, string $id, string $name): array
    {
        $user = $request->attributes->get('hotelUser');

        return [
            'pageId' => $id,
            'pageName' => $name,
            'bodyId' => 'home',
            'cat' => 'credits',
            'hotelUser' => $user,
        ];
    }

    /**
     * @return array<int, object>
     */
    private function newsList(int $limit): array
    {
        try {
            if (! Schema::connection('holodb')->hasTable('phpretro_news')) {
                return [];
            }

            return DB::connection('holodb')->table('phpretro_news')
                ->orderByDesc('time')->orderByDesc('id')->limit($limit)->get()->all();
        } catch (\Throwable) {
            return [];
        }
    }

    /**
     * @return array<int, object>
     */
    private function hotelViewNews(int $limit): array
    {
        try {
            if (Schema::connection('holodb')->hasTable('hotelview_news')) {
                return DB::connection('holodb')->table('hotelview_news')
                    ->orderByDesc('id')->limit($limit)->get()->all();
            }
        } catch (\Throwable) {
        }

        return [];
    }

    /**
     * @return array<int, object>
     */
    private function campaigns(): array
    {
        try {
            if (! Schema::connection('holodb')->hasTable('phpretro_campaigns')) {
                return [];
            }

            return DB::connection('holodb')->table('phpretro_campaigns')
                ->where('visible', '1')->orderBy('sort_order')->get()->all();
        } catch (\Throwable) {
            return [];
        }
    }

    /**
     * @return array<int, array{look: string, gender: string}>
     */
    private function registerFigures(): array
    {
        return [
            ['gender' => 'F', 'look' => 'hr-515-45.hd-600-1.ch-635-64.lg-715-64.sh-725-62'],
            ['gender' => 'F', 'look' => 'hr-545-45.hd-600-1.ch-665-76.lg-720-64.sh-735-62'],
            ['gender' => 'F', 'look' => 'hr-890-45.hd-605-1.ch-660-64.lg-725-76.sh-730-64'],
            ['gender' => 'M', 'look' => 'hr-165-45.hd-190-1.ch-255-82.lg-285-64.sh-290-64'],
            ['gender' => 'M', 'look' => 'hr-115-42.hd-190-1.ch-215-62.lg-285-91.sh-290-62'],
            ['gender' => 'M', 'look' => 'hr-828-45.hd-180-1.ch-255-62.lg-275-62.sh-295-62'],
        ];
    }

    private function findHomeProfile(string $name): ?object
    {
        try {
            if (! Schema::connection('holodb')->hasTable('users')) {
                return null;
            }

            if (ctype_digit($name)) {
                return DB::connection('holodb')->table('users')->where('id', (int) $name)->first();
            }

            return DB::connection('holodb')->table('users')->where('username', $name)->first();
        } catch (\Throwable) {
            return null;
        }
    }

    /**
     * @return array<int, object>
     */
    private function homeWidgets(int $userId): array
    {
        try {
            if (Schema::connection('holodb')->hasTable('phpretro_myhabbo_layouts')) {
                $rows = DB::connection('holodb')->table('phpretro_myhabbo_layouts')
                    ->where('user_id', $userId)
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
            (object) ['id' => 1, 'user_id' => $userId, 'column_number' => 1, 'widget_key' => 'profilewidget', 'position' => 0],
            (object) ['id' => 2, 'user_id' => $userId, 'column_number' => 1, 'widget_key' => 'guestbookwidget', 'position' => 5],
            (object) ['id' => 3, 'user_id' => $userId, 'column_number' => 2, 'widget_key' => 'friendswidget', 'position' => 0],
            (object) ['id' => 4, 'user_id' => $userId, 'column_number' => 2, 'widget_key' => 'groupswidget', 'position' => 4],
            (object) ['id' => 5, 'user_id' => $userId, 'column_number' => 2, 'widget_key' => 'roomswidget', 'position' => 8],
        ];
    }

    /**
     * @return array<int, object>
     */
    private function homeStickers(int $userId): array
    {
        try {
            if (! Schema::connection('holodb')->hasTable('phpretro_homes_items')) {
                return [];
            }

            return DB::connection('holodb')->table('phpretro_homes_items as i')
                ->join('phpretro_homes_catalogue as c', 'c.id', '=', 'i.catalogue_id')
                ->where('i.user_id', $userId)
                ->where('i.placed', 1)
                ->where('i.item_type', 'sticker')
                ->orderBy('i.z')
                ->get(['i.id', 'i.x', 'i.y', 'i.z', 'c.data as catalogue_data'])
                ->all();
        } catch (\Throwable) {
            return [];
        }
    }

    private function homeBackground(int $userId): string
    {
        try {
            if (Schema::connection('holodb')->hasTable('phpretro_homes_items')) {
                $bg = DB::connection('holodb')->table('phpretro_homes_items as i')
                    ->join('phpretro_homes_catalogue as c', 'c.id', '=', 'i.catalogue_id')
                    ->where('i.user_id', $userId)
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
}
