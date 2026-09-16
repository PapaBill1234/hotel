<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HousekeepingUsersController extends Controller
{
    use RendersHousekeeping;

    public function reports(Request $request): View|RedirectResponse
    {
        if ($request->isMethod('post')) {
            $id = (int) $request->input('id', 0);
            $status = (string) $request->input('status', 'open');
            if (in_array($status, ['open', 'assigned', 'resolved', 'dismissed'], true) && $this->holodbHas('phpretro_user_reports')) {
                $staffId = $request->attributes->get('hotelUser')->id;
                $payload = [
                    'status' => $status,
                    'assigned_to' => (int) $request->input('assigned_to', 0) ?: null,
                    'action_notes' => trim((string) $request->input('action_notes', '')),
                ];
                if (in_array($status, ['resolved', 'dismissed'], true)) {
                    $payload['resolved_by'] = $staffId;
                    $payload['resolved_at'] = time();
                }
                $this->holodb()->table('phpretro_user_reports')->where('id', $id)->update($payload);
            }

            return redirect('/housekeeping/reports')->with('hk_notice', 'Report updated.');
        }

        $rows = collect();
        if ($this->holodbHas('phpretro_user_reports')) {
            $rows = $this->holodb()->table('phpretro_user_reports as r')
                ->join('users as reporter', 'reporter.id', '=', 'r.reporter_id')
                ->join('users as reported', 'reported.id', '=', 'r.reported_user_id')
                ->orderByDesc('r.created_at')
                ->limit(200)
                ->get([
                    'r.id', 'r.reason', 'r.evidence', 'r.status', 'r.created_at',
                    'reporter.username as reporter', 'reported.username as reported',
                ]);
        }

        return $this->hk($request, 'User reports', 'users', 'housekeeping.reports', [
            'rows' => $rows,
        ]);
    }

    public function bans(Request $request): View|RedirectResponse
    {
        if ($request->isMethod('post')) {
            return redirect('/housekeeping/bans')->with(
                'hk_error',
                'PolarIS bans are not written from this website. Ban and unban from the hotel emulator.'
            );
        }

        $rows = $this->holodbHas('bans')
            ? $this->holodb()->table('bans')->orderByDesc('timestamp')->limit(200)->get()
            : collect();

        return $this->hk($request, 'Bans', 'users', 'housekeeping.bans', [
            'rows' => $rows,
        ]);
    }

    public function alerts(Request $request): View|RedirectResponse
    {
        $type = $request->query('type') === 'mass' ? 'mass' : 'single';
        $do = (string) $request->query('do', '');
        $userid = (int) $request->input('userid', $request->query('userid', 0));
        $alert = trim((string) $request->input('alert', $request->query('alert', '')));
        $searchResults = collect();
        $error = '';
        $message = '';

        if ($request->isMethod('post') && $request->has('search')) {
            $query = trim((string) $request->input('query', ''));
            if ($query !== '' && $this->holodbHas('users')) {
                $searchResults = $this->holodb()->table('users')
                    ->where('username', 'like', '%'.$query.'%')
                    ->limit(50)
                    ->get(['id', 'username']);
            }
            $do = $do ?: '';
        } elseif ($request->isMethod('post') && $request->has('save')) {
            if ($alert === '') {
                $error = 'Alert is blank!';
                $do = 'create';
            } elseif ($type === 'single' && $userid < 1) {
                $error = 'Please type in the id of the user you want to alert.';
                $do = 'create';
            } elseif (! $this->polarisRconConfigured()) {
                $error = 'PolarIS CMS/RCON is not configured. Set POLARIS_CMS_URL/KEY/SECRET or POLARIS_RCON_HOST/PORT.';
                $do = 'create';
            } else {
                $error = 'PolarIS rejected the alert.';
                $do = 'create';
            }
        }

        return $this->hk($request, 'Alerts', 'users', 'housekeeping.alerts', [
            'type' => $type,
            'do' => $do,
            'userid' => $userid,
            'alert' => $alert,
            'searchResults' => $searchResults,
            'formError' => $error,
            'message' => $message,
        ]);
    }

    public function search(Request $request): View
    {
        $q = trim((string) $request->query('q', ''));
        $users = collect();
        $catalog = collect();
        $reports = collect();
        if ($q !== '') {
            $like = '%'.$q.'%';
            if ($this->holodbHas('users')) {
                $users = $this->holodb()->table('users')
                    ->where('username', 'like', $like)
                    ->orWhere('mail', 'like', $like)
                    ->orWhere('ip_current', 'like', $like)
                    ->limit(50)
                    ->get(['id', 'username', 'mail', 'ip_current']);
            }
            if ($this->holodbHas('catalog_items')) {
                $catalog = $this->holodb()->table('catalog_items')
                    ->where('catalog_name', 'like', $like)
                    ->orWhere('id', (int) $q)
                    ->limit(50)
                    ->get(['id', 'catalog_name']);
            }
            if ($this->holodbHas('phpretro_user_reports')) {
                $reports = $this->holodb()->table('phpretro_user_reports')
                    ->where('reason', 'like', $like)
                    ->orWhere('evidence', 'like', $like)
                    ->limit(50)
                    ->get(['id', 'reason', 'status']);
            }
        }

        return $this->hk($request, 'Admin search', 'users', 'housekeeping.search', [
            'q' => $q,
            'users' => $users,
            'catalog' => $catalog,
            'reports' => $reports,
        ]);
    }

    public function help(Request $request): View|RedirectResponse
    {
        $action = (string) $request->query('do', 'list');
        $id = (int) $request->query('id', $request->input('id', 0));

        if ($request->isMethod('post') && $action === 'remove' && $this->holodbHas('phpretro_helpdesk_tickets')) {
            $this->holodb()->table('phpretro_helpdesk_tickets')->where('id', $id)->delete();

            return redirect('/housekeeping/help')->with('hk_notice', 'Help removed.');
        }

        if ($action === 'pickup' && $id > 0 && $this->holodbHas('phpretro_helpdesk_tickets')) {
            $this->holodb()->table('phpretro_helpdesk_tickets')->where('id', $id)->update([
                'status' => 'picked',
                'picked_by' => $request->attributes->get('hotelUser')->id,
            ]);

            return redirect('/housekeeping/help')->with('hk_notice', 'Help picked up.');
        }

        $rows = $this->holodbHas('phpretro_helpdesk_tickets')
            ? $this->holodb()->table('phpretro_helpdesk_tickets')->orderByDesc('created_at')->get()
            : collect();

        return $this->hk($request, 'Help', 'users', 'housekeeping.help', [
            'action' => $action,
            'removeId' => $id,
            'rows' => $rows,
        ]);
    }

    public function users(Request $request): View|RedirectResponse
    {
        if ($request->isMethod('post')) {
            return redirect('/housekeeping/users')->with(
                'hk_error',
                'PolarIS users.rank / credits / mail / pixels / points are not written from this website. Change them in the emulator.'
            );
        }

        $term = trim((string) $request->query('q', ''));
        $editId = (int) $request->query('id', 0);
        $edit = null;
        $users = collect();
        if ($this->holodbHas('users')) {
            if ($editId > 0 && $request->query('do') === 'edit') {
                $edit = $this->holodb()->table('users')->where('id', $editId)->first(['id', 'username', 'mail', 'rank', 'credits', 'pixels', 'points', 'online']);
            }
            $q = $this->holodb()->table('users')->orderByDesc('id')->limit(100);
            if ($term !== '') {
                $q = $this->holodb()->table('users')->where('username', 'like', '%'.$term.'%')->orderBy('username')->limit(100);
            }
            $users = $q->get(['id', 'username', 'mail', 'rank', 'credits', 'pixels', 'points', 'online']);
        }

        return $this->hk($request, 'Users', 'users', 'housekeeping.users', [
            'users' => $users,
            'editUser' => $edit,
            'term' => $term,
        ]);
    }
}
