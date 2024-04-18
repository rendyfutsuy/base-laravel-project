<?php

namespace App\Http\Repositories;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Model;
use App\Http\Repositories\Contracts\UserContract;
use Modules\Hierarchy\Models\Role;

class UserRepository extends BaseRepository implements UserContract
{
    /** @var User */
    protected $user;

    protected $role = 'NORMAL_USER';

    public function __construct(User $user)
    {
        parent::__construct($user);
        $this->user = $user;
    }

    public function validateUserRole(User $user): bool
    {
        return $this->user->query()->whereHas('roles', function ($roles) {
            $role = Role::findByName($this->role, 'api');
            $roles->where('id', $role->id);
        })->exists();
    }

    public function paginated()
    {
        return $this->user->query()->paginate(request('per_page', 10));
    }

    public function store(array $attributes): Model
    {
        $user = $this->user->create([
            'name' => $attributes['name'],
            'email' => $attributes['email'],
            'password' => Hash::make($attributes['password']),
        ]);

        $role = Role::findByName($this->role, 'api');
        $user->syncRoles($role);

        return $user;
    }

    /**
     * @return void
     */
    public function update(array $attributes, $id)
    {
        $fillables = [];

        if (empty($attributes)) {
            return;
        }

        if (array_key_exists('name', $attributes)) {
            $fillables['name'] = $attributes['name'];
        }

        if (array_key_exists('email', $attributes)) {
            $fillables['email'] = $attributes['email'];
        }

        if (array_key_exists('password', $attributes)) {
            $fillables['password'] = Hash::make($attributes['password']);
        }

        $this->user->where('id', $id)
            ->update($fillables);
    }
}
