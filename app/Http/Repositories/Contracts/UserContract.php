<?php

namespace App\Http\Repositories\Contracts;

use App\Models\User;

interface UserContract
{
	public function paginated();
	public function validateUserRole(User $user): bool;
}