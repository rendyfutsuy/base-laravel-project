<?php

namespace Modules\Notification\Http\Services\Features\Traits;

use App\Models\User;
use Modules\Hierarchy\Models\Role;

trait RoleModuleTrait
{
    public function roleSyncedToUser(Role $role, User $user, User $receiver)
    {
        return $this->add(
            'Role Sync to User',
            $role->name.' Sync to '.$user->name,
            'role_synced_to_user',
            $role,
            $receiver, // Receiver
            $role->toArray()
        );
    }
}
