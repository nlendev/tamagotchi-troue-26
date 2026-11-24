<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateEnclosureRequest;
use App\Models\Creature;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['name', 'email', 'password', 'motto', 'balance'])]
class EnclosureController extends Controller
{
    public function show(Request $request, User $user): Response
    {
        return Inertia::render('enclosures/Show', [
            'owner' => $user->only('id', 'name', 'motto'),
            'isOwner' => $request->user()->is($user),
            'creatures' => $user->creatures()
                ->orderBy('name')
                ->get()
                ->map(fn (Creature $creature) => [
                    ...$creature->only('id', 'name', 'species', 'food', 'mood', 'energy'),
                    'can' => [
                        'update' => $request->user()->can('update', $creature),
                    ],
                ]),
            'messages' => $user->receivedMessages()
                ->with('author:id,name')
                ->latest()
                ->get()
                ->map(fn (Message $message) => [
                    'id' => $message->id,
                    'body' => $message->body,
                    'author' => $message->author->name,
                    'date' => $message->created_at->format('d/m/Y H:i'),
                ]),
        ]);
    }

    public function update(UpdateEnclosureRequest $request): RedirectResponse
    {
        $request->user()->update($request->validated());user

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Enclos mis à jour.']);

        return to_route('enclosures.show', $request->user());
    }
}
