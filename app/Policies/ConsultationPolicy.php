<?php

namespace App\Policies;

use App\Models\Consultation;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ConsultationPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        //
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Consultation  $consultation
     * @return mixed
     */
    public function view(User $user, Consultation $consultation)
    {
        if ($user->roles == User::ADMIN || $user->id == $consultation->user_id || $user->id == $consultation->consultant_id) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        //
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Consultation  $consultation
     * @return mixed
     */
    public function update(User $user, Consultation $consultation)
    {
        //
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Consultation  $consultation
     * @return mixed
     */
    public function delete(User $user, Consultation $consultation)
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Consultation  $consultation
     * @return mixed
     */
    public function restore(User $user, Consultation $consultation)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Consultation  $consultation
     * @return mixed
     */
    public function forceDelete(User $user, Consultation $consultation)
    {
        //
    }
}
