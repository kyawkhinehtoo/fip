<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\Activitylog\Models\Activity;

class ActivityLogController extends Controller
{
    //
    public function index()
    {

        $activities = Activity::orderBy('id', 'desc')->paginate(15);
        return Inertia::render('Setup/ActivityLog', [
            'activities' => $activities->through(function ($activity) {
                return [
                    'description' => $activity->description,
                    'causer' => optional($activity->causer)->name ?? 'System',
                    'date' => $activity->created_at->format('d-m-Y H:i:s'),
                    'changes' => $activity->properties['changes'] ?? [],
                ];
            }),
        ]);
    }
}
