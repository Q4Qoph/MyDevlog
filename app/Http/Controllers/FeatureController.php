<?php

namespace App\Http\Controllers;

use App\Models\Feature;
use App\Models\Project;
use App\Notifications\ApprovalRequestedNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class FeatureController extends Controller
{
    public function store(Request $request, Project $project): RedirectResponse
    {
        Gate::authorize('update', $project);

        $data = $request->validate([
            'title'          => 'required|string|max:150',
            'description'    => 'nullable|string|max:3000',
            'type'           => 'required|in:personal,client,collaborative',
            'priority'       => 'required|in:low,medium,high,critical',
            'needs_approval' => 'nullable|boolean',
            'external_url'   => 'nullable|url',
        ]);

        $data['needs_approval'] = $request->boolean('needs_approval');
        $data['sort_order']     = $project->features()->max('sort_order') + 1;

        $feature = $project->features()->create($data);
        $feature->logActivity('created');

        return back()->with('success', "'{$feature->title}' added.");
    }

    public function update(Request $request, Feature $feature): RedirectResponse
    {
        Gate::authorize('update', $feature->project);

        $data = $request->validate([
            'title'          => 'required|string|max:150',
            'description'    => 'nullable|string|max:3000',
            'type'           => 'required|in:personal,client,collaborative',
            'priority'       => 'required|in:low,medium,high,critical',
            'needs_approval' => 'nullable|boolean',
            'external_url'   => 'nullable|url',
        ]);

        $data['needs_approval'] = $request->boolean('needs_approval');
        $feature->update($data);

        return back()->with('success', 'Feature updated.');
    }

    public function updateStatus(Request $request, Feature $feature): RedirectResponse
    {
        Gate::authorize('update', $feature->project);

        $request->validate([
            'status' => 'required|in:backlog,approved,building,review,shipped,cancelled',
        ]);

        $feature->updateStatus($request->status);

        return back()->with('success', 'Status updated.');
    }

    public function sendApproval(Feature $feature): RedirectResponse
    {
        Gate::authorize('update', $feature->project);

        $feature->sendForApproval();

        $clientEmail = $feature->project->client_email;
        if ($clientEmail) {
            $feature->project->user->notify(
                new ApprovalRequestedNotification($feature, $clientEmail)
            );
        }

        $approvalUrl = route('approval.show', $feature->approval_token);

        return back()
            ->with('approval_url', $approvalUrl)
            ->with('success', 'Feature sent for approval.');
    }

    public function acceptSuggestion(Request $request, Project $project): RedirectResponse
    {
        Gate::authorize('update', $project);

        $request->validate(['suggestion_id' => 'required|exists:ai_suggestions,id']);

        $suggestion = $project->aiSuggestions()->findOrFail($request->suggestion_id);
        $suggestion->accept();

        return back()->with('success', "'{$suggestion->title}' added to backlog.");
    }

    public function dismissSuggestion(Request $request, Project $project): RedirectResponse
    {
        Gate::authorize('update', $project);

        $request->validate(['suggestion_id' => 'required|exists:ai_suggestions,id']);

        $project->aiSuggestions()->findOrFail($request->suggestion_id)->dismiss();

        return back()->with('success', 'Suggestion dismissed.');
    }

    public function destroy(Feature $feature): RedirectResponse
    {
        Gate::authorize('update', $feature->project);

        $feature->delete();

        return back()->with('success', 'Feature removed.');
    }
}