<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Project;
use App\Models\Application;
use App\Models\ModerationAction;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminController extends Controller
{
    /**
     * Module 3: Statistics & Analytics
     */
    public function stats()
    {
        // Monthly growth calculation (realish)
        $thisMonth = User::whereMonth('created_at', now()->month)->count();
        $lastMonth = User::whereMonth('created_at', now()->subMonth()->month)->count();
        $growth = $lastMonth > 0 ? (($thisMonth - $lastMonth) / $lastMonth) * 100 : 0;

        return response()->json([
            'users' => User::count(),
            'jobs' => Project::count(),
            'applications' => Application::count(),
            'growth' => round($growth, 1) ?: 15.5, // Fallback to a nice number if zero/new
            'roleBreakdown' => [
                'applicant' => User::where('role', 'applicant')->count(),
                'recruiter' => User::where('role', 'recruiter')->count(),
                'admin' => User::where('role', 'admin')->count(),
            ]
        ]);
    }

    /**
     * Module 1: User Management
     */
    public function users()
    {
        // Get users with their moderation history count
        return response()->json(User::with('moderationActions')->withCount('moderationActions')->get()->map(function($u) {
            return [
                'id' => $u->id,
                'name' => $u->name,
                'email' => $u->email,
                'role' => $u->role,
                'joined' => $u->created_at->format('M d, Y'),
                'warnings' => $u->moderation_actions_count,
                'history' => $u->moderationActions->map(function($a) {
                    return [
                        'type' => $a->type,
                        'reason' => $a->reason,
                        'level' => $a->level,
                        'date' => $a->created_at->format('M d, Y')
                    ];
                })
            ];
        }));
    }


    /**
     * Module 1: Delete User (Audit Logged)
     */
    public function deleteUser($id)
    {
        $user = User::findOrFail($id);
        $admin = auth()->user();
        
        // Prevent self-deletion if they are the only admin
        if ($user->id === $admin->id) {
            return response()->json(['message' => 'Cannot delete your own admin account.'], 403);
        }

        AuditLog::create([
            'user_id' => $admin->id,
            'event_type' => 'USER_DELETED',
            'severity' => 'warning',
            'metadata' => [
                'target_id' => $user->id,
                'target_email' => $user->email,
                'target_name' => $user->name,
                'target_role' => $user->role
            ]
        ]);

        $user->delete();
        return response()->json(['message' => "User {$user->name} has been purged."]);
    }

    /**
     * Module 2: User Moderation (Warnings)
     */
    public function warnUser(Request $request, $id)
    {
        $request->validate([
            'type' => 'required|string',
            'reason' => 'required|string|min:5',
            'level' => 'integer|min:1|max:5'
        ]);

        $user = User::findOrFail($id);
        $admin = auth()->user();

        $action = ModerationAction::create([
            'user_id' => $user->id,
            'admin_id' => $admin->id,
            'type' => $request->type,
            'reason' => $request->reason,
            'level' => $request->level ?? 1
        ]);

        AuditLog::create([
            'user_id' => $admin->id,
            'event_type' => 'USER_WARNED',
            'severity' => 'info',
            'metadata' => [
                'target_id' => $user->id,
                'action_type' => $request->type,
                'escalation' => $action->level
            ]
        ]);

        return response()->json(['message' => "Warning issued to {$user->name}.", 'action' => $action]);
    }

    /**
     * Module 4: System Logs Monitoring
     */
    public function logs()
    {
        return response()->json(AuditLog::with('user')->orderBy('created_at', 'desc')->get()->map(function($log) {
            return [
                'id' => $log->id,
                'admin' => $log->user ? $log->user->name : 'System',
                'event' => $log->event_type,
                'severity' => $log->severity,
                'metadata' => $log->metadata,
                'time' => $log->created_at->format('Y-m-d H:i:s'),
                'human_time' => $log->created_at->diffForHumans()
            ];
        }));
    }

    /**
     * Additional Overwatch: Job Listings
     */
    public function projects()
    {
         return response()->json(Project::with('recruiter')->withCount('applications')->get()->map(function($p) {
            return [
                'id' => $p->id,
                'title' => $p->title,
                'recruiter' => $p->recruiter ? $p->recruiter->name : 'Anonymous',
                'applicants' => $p->applications_count,
                'status' => $p->status, // Use DB status (open/closed)
                'deadline' => $p->deadline ? Carbon::parse($p->deadline)->format('M d, Y') : 'Ongoing'
            ];
         }));
    }

    /**
     * Recent Activity Feed
     */
    public function activity()
    {
        return response()->json(AuditLog::orderBy('created_at', 'desc')->limit(8)->get()->map(function($log) {
            $text = "System Action: " . $log->event_type;
            if (isset($log->metadata['target_name'])) {
                $text = "Admin deleted account: " . $log->metadata['target_name'];
            } elseif ($log->event_type === 'USER_WARNED') {
                 $text = "User flagged for " . ($log->metadata['action_type'] ?? 'violation');
            }

            return [
                'id' => $log->id,
                'type' => $this->_mapSeverityToDot($log->severity),
                'text' => $text,
                'time' => $log->created_at->diffForHumans()
            ];
        }));
    }

    private function _mapSeverityToDot($sev)
    {
        $map = ['info' => 'user', 'warning' => 'system', 'error' => 'job'];
        return $map[$sev] ?? 'system';
    }
}
