<?php

namespace App\Policies;

use App\Models\StudentPpdb;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class StudentPpdbPolicy
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
     * @param  \App\Models\StudentPpdb  $studentPpdb
     * @return mixed
     */
    public function view(User $user, StudentPpdb $studentPpdb)
    {
        if (in_array($studentPpdb->school_id, $user->adminschools->pluck('id')->toArray())) {
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
     * @param  \App\Models\StudentPpdb  $studentPpdb
     * @return mixed
     */
    public function update(User $user, StudentPpdb $studentPpdb)
    {
        //
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\StudentPpdb  $studentPpdb
     * @return mixed
     */
    public function delete(User $user, StudentPpdb $studentPpdb)
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\StudentPpdb  $studentPpdb
     * @return mixed
     */
    public function restore(User $user, StudentPpdb $studentPpdb)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\StudentPpdb  $studentPpdb
     * @return mixed
     */
    public function forceDelete(User $user, StudentPpdb $studentPpdb)
    {
        //
    }
}
