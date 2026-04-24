<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\View\View;

class ClientViewController extends Controller
{
    /**
     * Read-only project overview for clients.
     * No login required — share token only.
     */
    public function show(string $token): View
    {
        $project = Project::where('share_token', $token)
            ->where('share_enabled', true)
            ->with(['features' => function ($q) {
                $q->whereNotIn('status', ['cancelled'])
                  ->orderBy('sort_order');
            }])
            ->firstOrFail();

        $grouped = $project->features->groupBy('status');

        return view('client.show', compact('project', 'grouped'));
    }
}
