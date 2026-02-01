<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class DashboardPolicy
{

    use HandlesAuthorization;

    public function access(User $user): bool 
    {
        return $user->isAdmin();
    }

    public function viewRecentOrders(User $user): bool 
    {
        return $user->isAdmin();
    }

    public function viewStatistics(User $user): bool 
    {
        return $user->isAdmin();
    }

    public function viewTopProducts(User $user): bool 
    {
        return $user->isAdmin();
    }

    public function viewSalesProducts(User $user): bool 
    {
        return $user->isAdmin();
    }

    public function exportProducts(User $user): bool 
    {
        return $user->isAdmin();
    }


}
