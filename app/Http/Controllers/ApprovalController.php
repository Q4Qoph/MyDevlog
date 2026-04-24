<?php

namespace App\Http\Controllers;

use App\Models\Feature;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ApprovalController extends Controller
{
    /**
     * Public approval page — no auth required, token only.
     */
    public function show(string $token): View
    {
        $feature = Feature::where('approval_token', $token)
            ->where('status', 'awaiting_approval')
            ->with(['project', 'activityLogs'])
            ->firstOrFail();

        return view('approval.show', compact('feature'));
    }

    /**
     * Client submits their decision.
     */
    public function decide(Request $request, string $token): RedirectResponse
    {
        $feature = Feature::where('approval_token', $token)
            ->where('status', 'awaiting_approval')
            ->firstOrFail();

        $data = $request->validate([
            'decision'     => 'required|in:approved,changes_requested',
            'client_note'  => 'nullable|string|max:1000',
            'client_name'  => 'nullable|string|max:120',
            'client_email' => 'nullable|email',
        ]);

        $approval = $feature->recordApproval(
            $data['decision'],
            $data['client_note'] ?? null,
            $data['client_email'] ?? null,
        );

        // Notify the developer
        $feature->project->user->notify(
            new \App\Notifications\ApprovalDecisionNotification($feature, $approval)
        );

        $message = $data['decision'] === 'approved'
            ? 'Great — approved! The developer has been notified.'
            : 'Your feedback has been sent to the developer.';

        return redirect()->route('approval.done', $token)->with('message', $message);
    }

    public function done(string $token): View
    {
        $feature = Feature::where('approval_token', $token)
            ->with('project', 'latestApproval')
            ->firstOrFail();

        return view('approval.done', compact('feature'));
    }
}
