<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCreatureRequest;
use App\Http\Requests\UpdateCreatureRequest;
use App\Models\Creature;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class CreatureController extends Controller
{
    public function index(Request $request): Response
    {
        return Inertia::render('creatures/Index', [
            'creatures' => $request->user()->creatures()
                ->orderBy('name')
                ->get(['id', 'name', 'species', 'food', 'mood', 'energy']),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('creatures/Create', [
            'species' => Creature::SPECIES,
        ]);
    }

    public function store(StoreCreatureRequest $request): RedirectResponse
    {
        $request->user()->creatures()->create($request->validated());

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Créature adoptée.']);

        return to_route('dashboard');
    }

public function edit(Creature $creature): Response
{
    Gate::authorize('update', $creature);

    return Inertia::render('creatures/Edit', [
        'creature' => $creature->only('id', 'name', 'species', 'food', 'mood', 'energy'),
    ]);
}


 public function update(UpdateCreatureRequest $request, Creature $creature): RedirectResponse
{
    Gate::authorize('update', $creature);

    $creature->update($request->validated());

    Inertia::flash('toast', ['type' => 'success', 'message' => 'Créature renommée.']);

    return to_route('creatures.edit', $creature);
}

public function destroy(Creature $creature): RedirectResponse
{
    Gate::authorize('delete', $creature);

    $creature->delete();

    Inertia::flash('toast', ['type' => 'success', 'message' => 'Créature relâchée.']);

    return to_route('dashboard');
}

public function play(Creature $creature): RedirectResponse
{
    Gate::authorize('update', $creature);

    $creature->update([
        'mood' => min(100, $creature->mood + 15),
        'food' => max(0, $creature->food - 10),
        'energy' => max(0, $creature->energy - 10),
    ]);

    return back();
}

public function sleep(Creature $creature): RedirectResponse
{
    Gate::authorize('update', $creature);

    $creature->update([
        'energy' => min(100, $creature->energy + 25),
        'food' => max(0, $creature->food - 5),
    ]);

    return back();
}
}
