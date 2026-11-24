<?php

namespace App\Policies;

use App\Models\Creature;
use App\Models\User;

class CreaturePolicy
{
    /**
     * Rename the creature or play with it.
     */
    public function update(User $user, Creature $creature): bool
    {
        return $creature->user_id === $user->id;
    }

    /**
     * Release the creature.
     */
    public function delete(User $user, Creature $creature): bool
    {
        return $creature->user_id === $user->id;
    }
}
