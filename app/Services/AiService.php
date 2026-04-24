<?php

namespace App\Services;

use App\Models\AiSuggestion;
use App\Models\Project;
use App\Models\ProjectMemory;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiService
{
    private string $apiKey;
    private string $model = 'claude-opus-4-5';

    public function __construct()
    {
        $this->apiKey = config('services.anthropic.key');
    }

    /**
     * Generate feature suggestions for a project.
     * Reads the project context + user's past project memory.
     */
    public function suggestFeatures(Project $project): Collection
    {
        $context = $project->buildAiContext();
        $memory  = $this->buildMemoryContext($project->user_id, $project->tech_tags ?? []);

        $prompt = <<<PROMPT
        You are a software project assistant for a frontend developer who builds websites, web apps and mobile apps.

        Here is the current project context:
        {$context}

        {$memory}

        Based on this, suggest 4-6 features this project is likely missing or will need.
        For each suggestion, explain your reasoning briefly (1-2 sentences), referencing past projects if relevant.

        Respond ONLY with valid JSON array. No markdown, no preamble. Format:
        [
          {
            "title": "Feature name (short, e.g. 'Email notifications')",
            "rationale": "Why this is needed, referencing the project or past work",
            "source_project": "Pweza Delivery" or null
          }
        ]
        PROMPT;

        try {
            $response = Http::withHeaders([
                'x-api-key'         => $this->apiKey,
                'anthropic-version' => '2023-06-01',
                'content-type'      => 'application/json',
            ])->post('https://api.anthropic.com/v1/messages', [
                'model'      => $this->model,
                'max_tokens' => 1024,
                'messages'   => [
                    ['role' => 'user', 'content' => $prompt],
                ],
            ]);

            $text        = $response->json('content.0.text', '[]');
            $suggestions = json_decode($text, true);

            if (! is_array($suggestions)) {
                Log::warning('AI suggestion response was not valid JSON', ['raw' => $text]);
                return collect();
            }

            // Persist and return
            return collect($suggestions)->map(function (array $s) use ($project) {
                return AiSuggestion::create([
                    'project_id'     => $project->id,
                    'title'          => $s['title'],
                    'rationale'      => $s['rationale'],
                    'source_project' => $s['source_project'] ?? null,
                    'status'         => 'pending',
                    'generated_at'   => now(),
                ]);
            });

        } catch (\Exception $e) {
            Log::error('AI suggestion failed', ['error' => $e->getMessage()]);
            return collect();
        }
    }

    /**
     * After archiving a project, store a memory snapshot for future AI context.
     */
    public function archiveProjectMemory(Project $project): ProjectMemory
    {
        $shipped = $project->features()
            ->where('status', 'shipped')
            ->pluck('title')
            ->toArray();

        $cut = $project->features()
            ->where('status', 'cancelled')
            ->pluck('title')
            ->toArray();

        $aiIgnored = $project->aiSuggestions()
            ->where('status', 'dismissed')
            ->pluck('title')
            ->toArray();

        $daysToShip = $project->created_at && $project->shipped_at
            ? (int) $project->created_at->diffInDays($project->shipped_at)
            : null;

        return ProjectMemory::create([
            'user_id'          => $project->user_id,
            'project_id'       => $project->id,
            'project_name'     => $project->name,
            'stack_tags'       => $project->tech_tags ?? [],
            'shipped_features' => $shipped,
            'cut_features'     => $cut,
            'ai_ignored'       => $aiIgnored,
            'days_to_ship'     => $daysToShip,
        ]);
    }

    /**
     * Build a memory context string from past projects with similar stack.
     */
    private function buildMemoryContext(int $userId, array $currentTags): string
    {
        if (empty($currentTags)) return '';

        $memories = ProjectMemory::where('user_id', $userId)
            ->get()
            ->filter(function (ProjectMemory $m) use ($currentTags) {
                return count(array_intersect($m->stack_tags, $currentTags)) > 0;
            })
            ->take(3);

        if ($memories->isEmpty()) return '';

        $lines = $memories->map(function (ProjectMemory $m) {
            $shipped = implode(', ', array_slice($m->shipped_features, 0, 5));
            $cut     = implode(', ', array_slice($m->cut_features, 0, 3));
            $days    = $m->days_to_ship ? "Shipped in {$m->days_to_ship} days." : '';
            return "- {$m->project_name} (stack: " . implode(', ', $m->stack_tags) . "): Shipped: {$shipped}. Cut: {$cut}. {$days}";
        })->join("\n");

        return "The developer's past similar projects:\n{$lines}\n";
    }
}
