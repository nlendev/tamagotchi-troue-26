<?php

namespace App\Http\Controllers;

use App\Models\Creature;
use App\Models\Item;
use App\Models\Purchase;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PurchaseController extends Controller
{
    public function store(Request $request, Item $item): RedirectResponse
    {
        $user = $request->user();
        $creature = Creature::findOrFail($request->integer('creature_id'));

        $quantity = $request->integer('quantity', 1);
        $price = $request->float('price');
        $total = $price * $quantity;

        if ($item->stock < $quantity) {
            return back()->withErrors(['quantity' => 'Il n\'en reste pas assez en stock.']);
        }

        if ($user->balance < $total) {
            return back()->withErrors(['quantity' => 'Votre solde est insuffisant.']);
        }

        $item->update(['stock' => $item->stock - $quantity]);

        $user->balance -= $total;
        $user->save();

        Purchase::create([
            'user_id' => $user->id,
            'item_id' => $item->id,
            'creature_id' => $creature->id,
            'quantity' => $quantity,
            'total' => $total,
        ]);

        $gauge = $item->effect;

        $creature->update([
            $gauge => min(100, $creature->getAttribute($gauge) + $item->effect_value * $quantity),
        ]);

        Inertia::flash('toast', ['type' => 'success', 'message' => "Achat effectué pour {$creature->name}."]);

        return to_route('shop.index');
    }
}
