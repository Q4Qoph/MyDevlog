<?php

namespace Database\Seeders;

use App\Models\Feature;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::create([
            'name'     => 'Fredie Dev',
            'email'    => 'fredie@devlog.test',
            'password' => Hash::make('password'),
        ]);

        // Sample project 1
        $pweza = $user->projects()->create([
            'name'        => 'Pweza Delivery',
            'client_name' => 'Pweza Ltd',
            'stack'       => 'React, TypeScript, .NET Web API',
            'tech_tags'   => ['react', 'typescript', 'dotnet', 'expo'],
            'brief'       => 'A full-stack delivery platform with React admin frontend and Expo React Native mobile app for drivers.',
            'status'      => 'active',
            'color'       => '#7c6af7',
        ]);

        $features = [
            ['Authentication & JWT',      'shipped',   'personal'],
            ['Order tracking map',        'shipped',   'client'],
            ['Driver dashboard',          'building',  'personal'],
            ['Push notifications',        'building',  'client'],
            ['Payment integration',       'backlog',   'client'],
            ['Admin analytics panel',     'backlog',   'personal'],
            ['Rating & review system',    'backlog',   'personal'],
        ];

        foreach ($features as [$title, $status, $type]) {
            $pweza->features()->create([
                'title'      => $title,
                'status'     => $status,
                'type'       => $type,
                'priority'   => 'medium',
                'sort_order' => array_search([$title, $status, $type], $features),
            ]);
        }

        // Sample project 2
        $aap = $user->projects()->create([
            'name'        => 'Africa Auto Parts',
            'client_name' => 'Internal',
            'stack'       => 'React, TypeScript, Vite, Laravel',
            'tech_tags'   => ['react', 'typescript', 'vite', 'laravel'],
            'brief'       => 'Cross-border spare parts sourcing platform with multi-language support (EN/FR/AR).',
            'status'      => 'active',
            'color'       => '#4CAF7D',
        ]);

        $aap->features()->createMany([
            ['title' => 'Language switcher (EN/FR)',  'status' => 'shipped',  'type' => 'personal', 'priority' => 'high',   'sort_order' => 0],
            ['title' => 'Parts catalogue search',     'status' => 'building', 'type' => 'personal', 'priority' => 'high',   'sort_order' => 1],
            ['title' => 'Supplier onboarding flow',   'status' => 'backlog',  'type' => 'client',   'priority' => 'medium', 'sort_order' => 2],
            ['title' => 'Arabic (RTL) support',       'status' => 'backlog',  'type' => 'personal', 'priority' => 'low',    'sort_order' => 3],
        ]);
    }
}