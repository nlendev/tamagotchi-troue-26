<?php

namespace App\Http\Controllers;

use App\Models\GiftCode;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;

class GiftCodeController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'code' => ['required', 'string'],
        ]);

        $giftCode = GiftCode::where('code', $data['code'])
            ->whereNull('redeemed_at')
            ->first();

        if (! $giftCode) {
            return back()->withErrors(['code' => 'Ce code est inconnu ou déjà utilisé.']);
        }

        $user = $request->user();

        $giftCode->update([
            'redeemed_by' => $user->id,
            'redeemed_at' => now(),
        ]);

        $user->balance += $giftCode->amount;
        $user->save();

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Code accepté, votre solde est crédité.']);

        return to_route('shop.index');
    }
}
