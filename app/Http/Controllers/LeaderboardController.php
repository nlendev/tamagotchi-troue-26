<?php

namespace App\Http\Controllers;

use App\Models\Creature;
use Inertia\Inertia;
use Inertia\Response;

class LeaderboardController extends Controller
{
    public function __invoke(): Response
    {
        return Inertia::render('Leaderboard', [
            'creatures' => Creature::with('user')
                ->orderByRaw('food + mood + energy desc')
                ->take(10)
                ->get()
                ->map(fn (Creature $creature) => [
                    ...$creature->only('id', 'name', 'species', 'food', 'mood', 'energy'),
                    'user' => $creature->user->only('id', 'name'),
                ]),
        ]);
    }
}
