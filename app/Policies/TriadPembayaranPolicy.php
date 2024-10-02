<?php

namespace App\Policies;

use App\Models\User;
use App\Models\triad_pembayaran;
use Illuminate\Auth\Access\Response;

class TriadPembayaranPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        //
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, triad_pembayaran $triadPembayaran): bool
    {
        //
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        //
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, triad_pembayaran $triadPembayaran): bool
    {
        //
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, triad_pembayaran $triadPembayaran): bool
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, triad_pembayaran $triadPembayaran): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, triad_pembayaran $triadPembayaran): bool
    {
        //
    }
}
