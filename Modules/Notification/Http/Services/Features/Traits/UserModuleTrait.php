<?php

namespace Modules\Notification\Http\Services\Features\Traits;

use App\Models\User;

trait UserModuleTrait
{
    public function userCreated(User $user, User $receiver)
    {
        return $this->add(
            'New User Created',
            'Super Admin has created a new user named '.$user->name.'.',
            'user_created',
            $user,
            $receiver, // Receiver
            $user->toArray()
        );
    }

    public function userUpdated(User $user, User $receiver, $before = [], $after = [])
    {
        return $this->add(
            $user->name.' Info Updated',
            'Super Admin has made change to user '.$user->name.'.',
            'user_updated',
            $user,
            $receiver, // Receiver
            [
                'before' => $before,
                'after' => $after,
            ]
        );
    }

    public function userDeleted(User $user, User $receiver)
    {
        return $this->add(
            $user->name.' Deleted',
            'Super Admin has deleted user '.$user->name.'.',
            'user_deleted',
            $user,
            $receiver, // Receiver
            $user->toArray()
        );
    }
}
