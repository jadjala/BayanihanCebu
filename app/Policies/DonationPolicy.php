<?php

namespace App\Policies;

use App\Models\Donation;
use App\Models\User;

class DonationPolicy
{
    /**
     * Determine if the user can view any donations.
     */
    public function viewAny(User $user): bool
    {
        // All authenticated users can view donations (filtered in controller)
        return true;
    }

    /**
     * Determine if the user can view the donation.
     */
    public function view(User $user, Donation $donation): bool
    {
        // Officials and admins can view all donations
        // Donors can only view their own donations
        return $user->isOfficial() || $user->isAdmin() || $donation->donor_id === $user->id;
    }

    /**
     * Determine if the user can create donations.
     */
    public function create(User $user): bool
    {
        // All authenticated users can create donations
        return true;
    }

    /**
     * Determine if the user can update the donation.
     */
    public function update(User $user, Donation $donation): bool
    {
        // Only donor can update, and only if status is Pending
        return $donation->donor_id === $user->id && $donation->status === 'Pending';
    }

    /**
     * Determine if the user can delete the donation.
     */
    public function delete(User $user, Donation $donation): bool
    {
        // Donor or admin can delete pending donations
        return ($donation->donor_id === $user->id || $user->isAdmin()) 
            && $donation->status === 'Pending';
    }

    /**
     * Determine if the user can verify the donation.
     */
    public function verify(User $user, Donation $donation): bool
    {
        // Only officials and admins can verify donations
        return $user->isOfficial() || $user->isAdmin();
    }

    /**
     * Determine if the user can distribute the donation.
     */
    public function distribute(User $user, Donation $donation): bool
    {
        // Only officials and admins can distribute donations
        // Donation must be verified first
        return ($user->isOfficial() || $user->isAdmin()) && $donation->status === 'Verified';
    }
}