<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy extends Policy
{
    /**
     * 只有用户自己能修改自己的信息
     *
     * @param User $currentUser
     * @param User $user
     *
     * @return bool
     */
    public function canEdit(User $currentUser, User $user)
    {
        return $currentUser->id === $user->id;
    }
}
