<?php

namespace App\Http\Middleware;

use App\Support\AuditLogger;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuditApiActivity
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (!$this->shouldLog($request)) {
            return $response;
        }

        $actor = AuditLogger::actorFromRequest($request);

        AuditLogger::logActivity([
            'access_log_id' => $actor['access_log_id'],
            'actor_username' => $actor['username'],
            'actor_role' => $actor['role'],
            'activity_name' => AuditLogger::activityName($request->method(), $request->path(), $response->getStatusCode()),
            'related_data' => AuditLogger::relatedData($request),
            'description' => AuditLogger::description($request, $response->getStatusCode()),
        ]);

        return $response;
    }

    private function shouldLog(Request $request): bool
    {
        if (!$request->is('api/*')) {
            return false;
        }

        if ($request->is('api/login', 'api/logout', 'api/user', 'api/logs/*')) {
            return false;
        }

        return $request->hasHeader('X-Actor-Username')
            || $request->hasHeader('X-Access-Log-Id')
            || $request->filled('actor_username')
            || $request->filled('nip_karyawan')
            || $request->filled('id_siswa_tetap')
            || $request->filled('identifier');
    }
}
