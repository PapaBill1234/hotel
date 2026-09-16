<?php

namespace App\Http\Controllers;

use App\Services\PolarisAuthService;
use App\Support\PolarisUser;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class HousekeepingController extends Controller
{
    use RendersHousekeeping;

    public function __construct(private PolarisAuthService $auth) {}

    public function index(Request $request): View|RedirectResponse
    {
        $user = $this->sessionUser($request);
        if ($user?->isStaff()) {
            return redirect('/housekeeping/dashboard');
        }

        return view('housekeeping.login', [
            'pageName' => 'Login',
            'category' => 'login',
            'username' => old('username', ''),
            'error' => $request->session()->get('hk_error'),
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
            return redirect('/housekeeping/')->with('hk_error', 'Incorrect username or password')->withInput();
        }

        $request->session()->regenerate();
        $request->session()->put('hotel.user_id', $user->id);

        if (! $user->isStaff()) {
            return redirect('/housekeeping/')->with('hk_error', 'You do not have permissions to access this page.');
        }

        $this->recordStaffSession($request, $user->id);

        return redirect('/housekeeping/dashboard');
    }

    public function dashboard(Request $request): View
    {
        $now = time();
        $users = $this->countTable('users');
        $rooms = $this->countTable('rooms');
        $bans = 0;
        $reports = 0;
        $dau = 0;
        try {
            if (Schema::connection('holodb')->hasTable('users')) {
                $dau = (int) DB::connection('holodb')->table('users')->where('last_online', '>=', $now - 86400)->count();
            }
            if (Schema::connection('holodb')->hasTable('bans')) {
                $bans = (int) DB::connection('holodb')->table('bans')
                    ->where(function ($q) use ($now) {
                        $q->where('ban_expire', 0)->orWhere('ban_expire', '>', $now);
                    })->count();
            }
            if (Schema::connection('holodb')->hasTable('phpretro_user_reports')) {
                $reports = (int) DB::connection('holodb')->table('phpretro_user_reports')
                    ->whereIn('status', ['open', 'assigned'])->count();
            }
        } catch (\Throwable) {
        }

        return $this->hk($request, 'Dashboard', 'dashboard', 'housekeeping.dashboard', [
            'stats' => [
                'users' => $users,
                'rooms' => $rooms,
                'bans' => $bans,
                'reports' => $reports,
                'dau' => $dau,
            ],
            'days' => $this->registrations(),
        ]);
    }

    public function about(Request $request): View
    {
        return $this->hk($request, 'About', 'dashboard', 'housekeeping.about', [
            'version' => '4.0.10',
            'status' => 'BETA',
            'revision' => 'laravel-cms',
            'build' => '2026-09-16',
        ]);
    }

    public function cache(Request $request): View
    {
        $driver = config('cache.default', 'file');
        Cache::store($driver)->get('hk-cache-probe', true);

        return $this->hk($request, 'Cache', 'settings', 'housekeeping.cache', [
            'cacheMessage' => 'Your cache is up to date. ('.$driver.')',
            'cacheCode' => 'ok',
        ]);
    }

    public function logs(Request $request): View
    {
        $roomId = (int) $request->query('roomid', 0);
        $searchResults = [];
        $rows = [];

        if ($request->isMethod('post')) {
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
        }

        if ($this->holodbHas('chatlogs_room')) {
            $q = $this->holodb()->table('chatlogs_room as c')
                ->leftJoin('users as u', 'u.id', '=', 'c.user_from_id')
                ->orderByDesc('c.timestamp')
                ->limit(1000)
                ->select(['u.username', 'c.room_id', 'c.timestamp', 'c.message']);
            if ($roomId > 0) {
                $q->where('c.room_id', $roomId);
            }
            $rows = $q->get();
        }

        return $this->hk($request, 'Logs', 'dashboard', 'housekeeping.logs', [
            'searchResults' => $searchResults,
            'rows' => $rows,
            'roomId' => $roomId,
        ]);
    }

    public function settings(Request $request): View|RedirectResponse
    {
        $existing = [];
        if ($this->holodbHas('phpretro_site_settings')) {
            foreach ($this->holodb()->table('phpretro_site_settings')->orderBy('setting_key')->get() as $row) {
                $existing[$row->setting_key] = $row->setting_value;
            }
        }

        if ($request->isMethod('post')) {
            $changed = 0;
            foreach ($existing as $key => $current) {
                if (! $request->exists($key)) {
                    continue;
                }
                $value = (string) $request->input($key);
                if ($value === (string) $current) {
                    continue;
                }
                $changed += $this->holodb()->table('phpretro_site_settings')
                    ->where('setting_key', $key)
                    ->update([
                        'setting_value' => $value,
                        'updated_by' => $this->hotelUser($request)->id,
                        'updated_at' => time(),
                    ]);
            }

            return redirect('/housekeeping/settings')->with(
                'hk_notice',
                $changed > 0 ? 'Settings saved.' : 'No settings changed.'
            );
        }

        return $this->hk($request, 'Site settings', 'settings', 'housekeeping.settings', [
            'settingsRows' => $existing,
        ]);
    }

    public function updates(Request $request): View
    {
        return $this->hk($request, 'Updates', 'dashboard', 'housekeeping.updates');
    }

    public function auditlog(Request $request): View
    {
        $rows = collect();
        if ($this->holodbHas('phpretro_admin_action_log')) {
            $q = $this->holodb()->table('phpretro_admin_action_log as l')
                ->leftJoin('users as u', 'u.id', '=', 'l.admin_id')
                ->orderByDesc('l.created_at')
                ->limit(250)
                ->select(['l.*', 'u.username']);
            if ($request->query('admin_id') !== null && $request->query('admin_id') !== '') {
                $q->where('l.admin_id', (int) $request->query('admin_id'));
            }
            if (($action = trim((string) $request->query('action', ''))) !== '') {
                $q->where('l.action_type', $action);
            }
            if (($target = trim((string) $request->query('target', ''))) !== '') {
                $q->where('l.target_type', $target);
            }
            $rows = $q->get();
        }

        return $this->hk($request, 'Admin audit log', 'dashboard', 'housekeeping.auditlog', [
            'rows' => $rows,
            'filters' => [
                'admin_id' => (string) $request->query('admin_id', ''),
                'action' => (string) $request->query('action', ''),
                'target' => (string) $request->query('target', ''),
            ],
        ]);
    }

    public function staffsessions(Request $request): View|RedirectResponse
    {
        if ($request->isMethod('post') && $this->holodbHas('phpretro_staff_sessions')) {
            $id = (int) $request->input('id', 0);
            $this->holodb()->table('phpretro_staff_sessions')->where('id', $id)->whereNull('revoked_at')->update(['revoked_at' => time()]);
            \App\Support\AdminAudit::log($this->hotelUser($request)->id, 'staff_session_revoked', 'staff_session', $id);

            return redirect('/housekeeping/staffsessions')->with('hk_notice', 'Session revoked.');
        }

        $rows = $this->holodbHas('phpretro_staff_sessions')
            ? $this->holodb()->table('phpretro_staff_sessions as s')
                ->join('users as u', 'u.id', '=', 's.user_id')
                ->whereNull('s.revoked_at')
                ->orderByDesc('s.last_activity')
                ->get(['s.id', 's.user_id', 's.ip', 's.created_at', 's.last_activity', 'u.username'])
            : collect();

        return $this->hk($request, 'Staff sessions', 'settings', 'housekeeping.staffsessions', ['rows' => $rows]);
    }

    public function twofactor(Request $request): View
    {
        $user = $this->hotelUser($request);
        $row = null;
        if ($this->holodbHas('phpretro_staff_totp')) {
            $row = $this->holodb()->table('phpretro_staff_totp')->where('user_id', $user->id)->first();
        }

        return $this->hk($request, 'Staff 2FA', 'settings', 'housekeeping.twofactor', [
            'totp' => $row,
        ]);
    }

    public function maintenance(Request $request): View|RedirectResponse
    {
        if ($request->isMethod('post') && $this->holodbHas('phpretro_site_settings')) {
            $enabled = $request->boolean('enabled') ? '1' : '0';
            $exists = $this->holodb()->table('phpretro_site_settings')->where('setting_key', 'maintenance_mode')->exists();
            if ($exists) {
                $this->holodb()->table('phpretro_site_settings')->where('setting_key', 'maintenance_mode')->update([
                    'setting_value' => $enabled,
                    'updated_by' => $this->hotelUser($request)->id,
                    'updated_at' => time(),
                ]);
            } else {
                $this->holodb()->table('phpretro_site_settings')->insert([
                    'setting_key' => 'maintenance_mode',
                    'setting_value' => $enabled,
                    'updated_by' => $this->hotelUser($request)->id,
                    'updated_at' => time(),
                ]);
            }
            \App\Support\AdminAudit::log($this->hotelUser($request)->id, 'maintenance_mode', 'site_setting', null, $enabled === '1' ? 'enabled' : 'disabled');

            return redirect('/housekeeping/maintenance')->with('hk_notice', $enabled === '1' ? 'Maintenance mode enabled.' : 'Maintenance mode disabled.');
        }

        return $this->hk($request, 'Maintenance mode', 'settings', 'housekeeping.maintenance', [
            'enabled' => \App\Support\Hotel::setting('maintenance_mode', '0') === '1',
        ]);
    }

    public function permissions(Request $request): View
    {
        return $this->hk($request, 'Access Denied', 'dashboard', 'housekeeping.permissions');
    }

    public function logout(Request $request): RedirectResponse
    {
        return redirect('/me');
    }

    private function recordStaffSession(Request $request, int $userId): void
    {
        if (! $this->holodbHas('phpretro_staff_sessions')) {
            return;
        }

        try {
            $this->holodb()->table('phpretro_staff_sessions')->insert([
                'user_id' => $userId,
                'session_hash' => hash('sha256', (string) $request->session()->getId()),
                'ip' => (string) ($request->ip() ?? ''),
                'created_at' => time(),
                'last_activity' => time(),
                'revoked_at' => null,
            ]);
        } catch (\Throwable) {
        }
    }

    private function hotelUser(Request $request): PolarisUser
    {
        return $request->attributes->get('hotelUser');
    }

    private function sessionUser(Request $request): ?PolarisUser
    {
        $id = (int) $request->session()->get('hotel.user_id', 0);
        if ($id < 1) {
            return null;
        }

        return $this->auth->findById($id);
    }

    private function countTable(string $table): int
    {
        try {
            if (! Schema::connection('holodb')->hasTable($table)) {
                return 0;
            }

            return (int) DB::connection('holodb')->table($table)->count();
        } catch (\Throwable) {
            return 0;
        }
    }

    /**
     * @return array<int, object>
     */
    private function registrations(): array
    {
        try {
            if (! Schema::connection('holodb')->hasTable('users')) {
                return [];
            }

            $since = time() - (86400 * 14);
            $rows = DB::connection('holodb')->table('users')
                ->where('account_created', '>=', $since)
                ->orderBy('account_created')
                ->get(['account_created']);

            $days = [];
            foreach ($rows as $row) {
                $day = date('Y-m-d', (int) $row->account_created);
                $days[$day] = ($days[$day] ?? 0) + 1;
            }

            $out = [];
            foreach ($days as $day => $total) {
                $out[] = (object) ['day' => $day, 'total' => $total];
            }

            return $out;
        } catch (\Throwable) {
            return [];
        }
    }
}
