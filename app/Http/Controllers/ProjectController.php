<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Services\AiService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class ProjectController extends Controller
{
    public function __construct(private AiService $ai) {}

    public function index(): View
    {
        $projects = auth()->user()
            ->projects()
            ->withCount([
                'features',
                'features as shipped_count' => fn ($q) => $q->where('status', 'shipped'),
            ])
            ->orderBy('updated_at', 'desc')
            ->get();

        return view('dashboard.index', compact('projects'));
    }

    public function show(Project $project): View
    {
        Gate::authorize('view', $project);

        $features    = $project->features()->with('latestApproval')->get();
        $suggestions = $project->aiSuggestions()->where('status', 'pending')->get();

        return view('projects.show', compact('project', 'features', 'suggestions'));
    }

    public function create(): View
    {
        return view('projects.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name'         => 'required|string|max:120',
            'client_name'  => 'nullable|string|max:120',
            'client_email' => 'nullable|email',
            'brief'        => 'nullable|string|max:2000',
            'stack'        => 'nullable|string|max:255',
            'tech_tags'    => 'nullable|array',
            'tech_tags.*'  => 'string|max:30',
            'color'        => 'nullable|string|max:7',
            'deadline'     => 'nullable|date|after:today',
        ]);

        $project = auth()->user()->projects()->create($data);

        if ($project->brief || $project->stack) {
            $this->ai->suggestFeatures($project);
        }

        return redirect()->route('projects.show', $project)
            ->with('success', 'Project created. AI is analysing your context…');
    }

    public function edit(Project $project): View
    {
        Gate::authorize('update', $project);

        return view('projects.edit', compact('project'));
    }

    public function update(Request $request, Project $project): RedirectResponse
    {
        Gate::authorize('update', $project);

        $data = $request->validate([
            'name'         => 'required|string|max:120',
            'client_name'  => 'nullable|string|max:120',
            'client_email' => 'nullable|email',
            'brief'        => 'nullable|string|max:2000',
            'stack'        => 'nullable|string|max:255',
            'tech_tags'    => 'nullable|array',
            'color'        => 'nullable|string|max:7',
            'deadline'     => 'nullable|date',
            'status'       => 'nullable|in:planning,active,review,shipped,archived',
        ]);

        if (
            isset($data['status']) &&
            in_array($data['status'], ['shipped', 'archived']) &&
            !in_array($project->status, ['shipped', 'archived'])
        ) {
            $data['shipped_at'] = now();
            $this->ai->archiveProjectMemory($project);
        }

        $project->update($data);

        return redirect()->route('projects.show', $project)->with('success', 'Project updated.');
    }

    public function destroy(Project $project): RedirectResponse
    {
        Gate::authorize('delete', $project);

        $project->delete();

        return redirect()->route('dashboard')->with('success', 'Project deleted.');
    }

    // ── Sharing ───────────────────────────────────────────────────

    public function toggleShare(Project $project): RedirectResponse
    {
        Gate::authorize('update', $project);

        if ($project->share_enabled) {
            $project->disableSharing();
            $msg = 'Sharing disabled.';
        } else {
            $project->enableSharing();
            $msg = 'Share link generated.';
        }

        return back()->with('success', $msg);
    }

    // ── AI ────────────────────────────────────────────────────────

    public function regenerateSuggestions(Project $project): RedirectResponse
    {
        Gate::authorize('update', $project);

        $project->aiSuggestions()->where('status', 'pending')->delete();
        $this->ai->suggestFeatures($project);

        return back()->with('success', 'AI is generating new suggestions…');
    }
}