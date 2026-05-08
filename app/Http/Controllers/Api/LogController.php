<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LogController extends Controller
{
    public function roles(Request $request)
    {
        $type = $request->query('type', 'access');

        if ($type === 'activity') {
            $activityRoles = DB::table('activity_log')
                ->whereNotNull('ACTOR_ROLE')
                ->where('ACTOR_ROLE', '!=', '')
                ->pluck('ACTOR_ROLE');

            $sessionRoles = DB::table('activity_log')
                ->leftJoin('access_log', 'activity_log.ID_ACCESS_LOG', '=', 'access_log.ID_ACCESS_LOG')
                ->whereNotNull('access_log.ROLE')
                ->where('access_log.ROLE', '!=', '')
                ->pluck('access_log.ROLE');

            return response()->json([
                'data' => $activityRoles
                    ->merge($sessionRoles)
                    ->map(fn ($role) => trim((string) $role))
                    ->filter()
                    ->unique()
                    ->sort(fn ($a, $b) => strcasecmp($a, $b))
                    ->values(),
            ]);
        }

        $roles = DB::table('access_log')
            ->whereNotNull('ROLE')
            ->where('ROLE', '!=', '')
            ->pluck('ROLE')
            ->map(fn ($role) => trim((string) $role))
            ->filter()
            ->unique()
            ->sort(fn ($a, $b) => strcasecmp($a, $b))
            ->values();

        return response()->json(['data' => $roles]);
    }

    public function access(Request $request)
    {
        $perPage = min((int) $request->query('per_page', 15), 50);

        $query = DB::table('access_log')
            ->select([
                'ID_ACCESS_LOG as id',
                'START_LOGIN as start_login',
                'END_LOGIN as end_login',
                'USERNAME as username',
                'ROLE as role',
            ]);

        $this->applyCommonFilters($query, $request, 'START_LOGIN', ['USERNAME', 'ROLE'], ['ROLE']);

        return response()->json(
            $query->orderByDesc('ID_ACCESS_LOG')->paginate($perPage)
        );
    }

    public function activity(Request $request)
    {
        $perPage = min((int) $request->query('per_page', 15), 50);

        $query = DB::table('activity_log')
            ->leftJoin('access_log', 'activity_log.ID_ACCESS_LOG', '=', 'access_log.ID_ACCESS_LOG')
            ->select([
                'activity_log.ID_ACTIVITY_LOG as id',
                'activity_log.ID_ACCESS_LOG as access_log_id',
                'activity_log.EVENT_TIME as event_time',
                'activity_log.ACTOR_USERNAME as actor_username',
                'activity_log.ACTOR_ROLE as actor_role',
                'activity_log.ACTIVITY_NAME as activity_name',
                'activity_log.RELATED_DATA as related_data',
                'activity_log.ACTIVITY_DESCRIPTION as activity_description',
                'access_log.START_LOGIN as session_start',
                'access_log.END_LOGIN as session_end',
            ]);

        $this->applyCommonFilters($query, $request, 'activity_log.EVENT_TIME', [
            'activity_log.ACTOR_USERNAME',
            'activity_log.ACTOR_ROLE',
            'activity_log.ACTIVITY_NAME',
            'activity_log.RELATED_DATA',
            'activity_log.ACTIVITY_DESCRIPTION',
        ], ['activity_log.ACTOR_ROLE', 'access_log.ROLE']);

        if ($request->filled('activity_name')) {
            $query->where('activity_log.ACTIVITY_NAME', $request->query('activity_name'));
        }

        return response()->json(
            $query->orderByDesc('activity_log.ID_ACTIVITY_LOG')->paginate($perPage)
        );
    }

    private function applyCommonFilters($query, Request $request, string $dateColumn, array $searchColumns, array $roleColumns): void
    {
        if ($request->filled('search')) {
            $search = trim((string) $request->query('search'));
            $query->where(function ($inner) use ($search, $searchColumns) {
                foreach ($searchColumns as $column) {
                    $inner->orWhere($column, 'like', "%{$search}%");
                }
            });
        }

        if ($request->filled('role')) {
            $role = trim((string) $request->query('role'));
            $query->where(function ($inner) use ($role, $roleColumns) {
                foreach ($roleColumns as $column) {
                    $inner->orWhere($column, $role);
                }
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate($dateColumn, '>=', $request->query('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate($dateColumn, '<=', $request->query('date_to'));
        }
    }
}
