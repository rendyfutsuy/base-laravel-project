<?php

namespace Modules\Hierarchy\Tests\Feature\Roles;

use Mockery;
use Laravel\Passport\Passport;
use Tests\TestCase;
use Modules\Hierarchy\Models\Role;
use Tests\Feature\Components\MockAuthHelper;
use Modules\Authentication\Http\Repositories\Contracts\UserContract;
use Modules\Hierarchy\Http\Services\Repositories\Contracts\RoleContract;

class UpdateUserRoleAndLimitTheirAccessTest extends TestCase
{
    use MockAuthHelper;

    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /** @test */
    public function staff_user_still_can_access_all_registered_feature()
    {
        $userMock = $this->mockUser(
            'staff@mailinator.com',
            'Staff',
            'user-id-456',
            [
                'api.user-management.user.index',
                'api.user-management.user.store',
                'api.user-management.user.show',
                'api.user-management.user.update',
                'api.user-management.user.destroy',
            ]
        );

        // Mock UserRepository for index
        $userCollection = collect([
            Mockery::mock(\App\Models\User::class)->makePartial(),
        ]);

        $userRepositoryMock = Mockery::mock(UserContract::class);
        $userRepositoryMock->shouldReceive('paginated')
            ->once()
            ->andReturn($userCollection);

        $this->app->instance(UserContract::class, $userRepositoryMock);

        // Mock UserRepository for store
        $newUserMock = Mockery::mock(\App\Models\User::class)->makePartial();
        $newUserMock->id = 'user-id-789';

        $userRepositoryMock->shouldReceive('store')
            ->once()
            ->andReturn($newUserMock);

        // Mock UserRepository for show, update, delete
        $existingUserMock = Mockery::mock(\App\Models\User::class)->makePartial();
        $existingUserMock->id = 'user-id-999';
        $existingUserMock->email = 'existing@mailinator.com';
        $existingUserMock->shouldReceive('toArray')->andReturn([
            'id' => 'user-id-999',
            'email' => 'existing@mailinator.com',
        ]);

        $userRepositoryMock->shouldReceive('find')
            ->with('user-id-999')
            ->andReturn($existingUserMock);

        $userRepositoryMock->shouldReceive('validateUserRole')
            ->with(Mockery::type(\App\Models\User::class))
            ->andReturn(true);

        $userRepositoryMock->shouldReceive('update')
            ->once()
            ->andReturn(true);

        $userRepositoryMock->shouldReceive('delete')
            ->once()
            ->andReturn(true);

        // Mock DB::transaction for controller
        \Illuminate\Support\Facades\DB::shouldReceive('transaction')
            ->andReturnUsing(function ($callback) {
                return $callback();
            });

        Passport::actingAs($userMock, ['*'], 'api');

        // user index
        $this->getJson(route('api.user-management.user.index'))->assertOk();

        // store user
        $this->postJson(route('api.user-management.user.store'), [
            'name' => 'My Name',
            'email' => 'user.'.random_str(10).'@mailinator.com',
            'password' => '12345',
        ])->assertOk();

        // show user detail
        $this->getJson(route('api.user-management.user.show', 'user-id-999'))->assertOk();

        // edit user
        $this->putJson(route('api.user-management.user.update', 'user-id-999'), [
            'name' => 'My Name',
            'email' => 'user.'.random_str(10).'@mailinator.com',
            'password' => '12345',
        ])->assertOk();

        // destroy user
        $this->deleteJson(route('api.user-management.user.destroy', 'user-id-999'))->assertOk();
    }

    /** @test */
    public function super_admin_change_staff_user_roles_ad__unvalidate_d__user()
    {
        $userMock = $this->mockUser(
            'superadmin@mailinator.com',
            'Superadmin',
            'user-id-123',
            ['api.hierarchy.role.user.resync']
        );

        // Mock User for route model binding
        $staffUserMock = Mockery::mock(\App\Models\User::class)->makePartial();
        $staffUserMock->id = 'staff-user-id-456';
        $staffUserMock->email = 'staff@mailinator.com';

        // Mock Role for route model binding
        $roleMock = Mockery::mock(Role::class)->makePartial();
        $roleMock->id = 'role-id-unvalidated';
        $roleMock->name = 'UNVALIDATED_USER';

        // Mock route model binding
        \Route::bind('user', function ($value) use ($staffUserMock) {
            return $staffUserMock;
        });

        \Route::bind('role', function ($value) use ($roleMock) {
            return $roleMock;
        });

        // Mock DB::transaction for controller
        \Illuminate\Support\Facades\DB::shouldReceive('transaction')
            ->andReturnUsing(function ($callback) {
                return $callback();
            });

        // Mock RoleRepository
        $roleRepositoryMock = Mockery::mock(RoleContract::class);
        $roleRepositoryMock->shouldReceive('resync')
            ->once()
            ->with(Mockery::type(\App\Models\User::class), Mockery::type(Role::class))
            ->andReturn($staffUserMock);

        $this->app->instance(RoleContract::class, $roleRepositoryMock);

        Passport::actingAs($userMock, ['*'], 'api');

        $response = $this->postJson(route('api.hierarchy.role.user.resync', [
            'user' => 'staff-user-id-456',
            'role' => 'role-id-unvalidated',
        ]));

        $response->assertOk();
    }

    /** @test */
    public function staff_now_can_not_access_all_staff_registered_feature()
    {
        $userMock = $this->mockUser(
            'staff@mailinator.com',
            'Staff',
            'user-id-456',
            [] // No permissions
        );

        Passport::actingAs($userMock, ['*'], 'api');

        // user index
        $this->getJson(route('api.user-management.user.index'))->assertForbidden();

        // store user
        $this->postJson(route('api.user-management.user.store'), [
            'name' => 'My Name',
            'email' => 'user.'.random_str(10).'@mailinator.com',
            'password' => '12345',
        ])->assertForbidden();

        // show user detail
        $this->getJson(route('api.user-management.user.show', 1))->assertForbidden();

        // edit user
        $this->putJson(route('api.user-management.user.update', 1), [
            'name' => 'My Name',
            'email' => 'user.'.random_str(10).'@mailinator.com',
            'password' => '12345',
        ])->assertForbidden();

        // destroy user
        $this->deleteJson(route('api.user-management.user.destroy', 1))->assertForbidden();

        // superadmin index
        $this->getJson(route('api.user-management.superadmin.index'))->assertForbidden();

        // store superadmin
        $this->postJson(route('api.user-management.superadmin.store'), [
            'name' => 'My Name',
            'email' => 'superadmin.'.random_str(10).'@mailinator.com',
            'password' => '12345',
        ])->assertForbidden();

        // show superadmin detail
        $this->getJson(route('api.user-management.superadmin.show', 1))->assertForbidden();

        // edit superadmin
        $this->putJson(route('api.user-management.superadmin.update', 1), [
            'name' => 'My Name',
            'email' => 'my.name.'.random_str(10).'@mailinator.com',
            'password' => '12345',
        ])->assertForbidden();

        // destroy superadmin
        $this->deleteJson(route('api.user-management.superadmin.destroy', 1))->assertForbidden();

        // staff index
        $this->getJson(route('api.user-management.staff.index'))->assertForbidden();

        // store staff
        $this->postJson(route('api.user-management.staff.store'), [
            'name' => 'My Name',
            'email' => 'staff.'.random_str(10).'@mailinator.com',
            'password' => '12345',
        ])->assertForbidden();

        // show staff detail
        $this->getJson(route('api.user-management.staff.show', 1))->assertForbidden();

        // edit staff
        $this->putJson(route('api.user-management.staff.update', 1), [
            'name' => 'My Name',
            'email' => 'my.name.'.random_str(10).'@mailinator.com',
            'password' => '12345',
        ])->assertForbidden();

        // destroy staff
        $this->deleteJson(route('api.user-management.staff.destroy', 1))->assertForbidden();

        // role index
        $this->getJson(route('api.hierarchy.role.index'))->assertForbidden();

        // store role
        $this->postJson(route('api.hierarchy.role.store'), [
            'name' => 'My Name',
        ])->assertForbidden();

        // show role detail
        $this->getJson(route('api.hierarchy.role.show', 1))->assertForbidden();

        // Mock Role for route model binding
        $roleMock = Mockery::mock(Role::class)->makePartial();
        $roleMock->id = 'role-id-staff';
        $roleMock->name = 'STAFF';

        // Mock route model binding
        \Route::bind('role', function ($value) use ($roleMock) {
            return $roleMock;
        });

        // sync role
        $this->postJson(route('api.hierarchy.role.permission.sync', 'role-id-staff'), [
            'permissions' => [],
        ])->assertForbidden();
    }

    /** @test */
    public function super_admin_change_back_staff_user_roles_to__staff()
    {
        $userMock = $this->mockUser(
            'superadmin@mailinator.com',
            'Superadmin',
            'user-id-123',
            ['api.hierarchy.role.user.resync']
        );

        // Mock User for route model binding
        $staffUserMock = Mockery::mock(\App\Models\User::class)->makePartial();
        $staffUserMock->id = 'staff-user-id-456';
        $staffUserMock->email = 'staff@mailinator.com';

        // Mock Role for route model binding
        $roleMock = Mockery::mock(Role::class)->makePartial();
        $roleMock->id = 'role-id-staff';
        $roleMock->name = 'STAFF';

        // Mock route model binding
        \Route::bind('user', function ($value) use ($staffUserMock) {
            return $staffUserMock;
        });

        \Route::bind('role', function ($value) use ($roleMock) {
            return $roleMock;
        });

        // Mock DB::transaction for controller
        \Illuminate\Support\Facades\DB::shouldReceive('transaction')
            ->andReturnUsing(function ($callback) {
                return $callback();
            });

        // Mock RoleRepository
        $roleRepositoryMock = Mockery::mock(RoleContract::class);
        $roleRepositoryMock->shouldReceive('resync')
            ->once()
            ->with(Mockery::type(\App\Models\User::class), Mockery::type(Role::class))
            ->andReturn($staffUserMock);

        $this->app->instance(RoleContract::class, $roleRepositoryMock);

        Passport::actingAs($userMock, ['*'], 'api');

        $response = $this->postJson(route('api.hierarchy.role.user.resync', [
            'user' => 'staff-user-id-456',
            'role' => 'role-id-staff',
        ]));

        $response->assertOk();
    }

    /** @test */
    public function staff_user_can_access_all_registered_feature_again()
    {
        $userMock = $this->mockUser(
            'staff@mailinator.com',
            'Staff',
            'user-id-456',
            [
                'api.user-management.user.index',
                'api.user-management.user.store',
                'api.user-management.user.show',
                'api.user-management.user.update',
                'api.user-management.user.destroy',
            ]
        );

        // Mock UserRepository for index
        $userCollection = collect([
            Mockery::mock(\App\Models\User::class)->makePartial(),
        ]);

        $userRepositoryMock = Mockery::mock(UserContract::class);
        $userRepositoryMock->shouldReceive('paginated')
            ->once()
            ->andReturn($userCollection);

        $this->app->instance(UserContract::class, $userRepositoryMock);

        // Mock UserRepository for store
        $newUserMock = Mockery::mock(\App\Models\User::class)->makePartial();
        $newUserMock->id = 'user-id-789';

        $userRepositoryMock->shouldReceive('store')
            ->once()
            ->andReturn($newUserMock);

        // Mock UserRepository for show, update, delete
        $existingUserMock = Mockery::mock(\App\Models\User::class)->makePartial();
        $existingUserMock->id = 'user-id-999';
        $existingUserMock->email = 'existing@mailinator.com';
        $existingUserMock->shouldReceive('toArray')->andReturn([
            'id' => 'user-id-999',
            'email' => 'existing@mailinator.com',
        ]);

        $userRepositoryMock->shouldReceive('find')
            ->with('user-id-999')
            ->andReturn($existingUserMock);

        $userRepositoryMock->shouldReceive('validateUserRole')
            ->with(Mockery::type(\App\Models\User::class))
            ->andReturn(true);

        $userRepositoryMock->shouldReceive('update')
            ->once()
            ->andReturn(true);

        $userRepositoryMock->shouldReceive('delete')
            ->once()
            ->andReturn(true);

        // Mock DB::transaction for controller
        \Illuminate\Support\Facades\DB::shouldReceive('transaction')
            ->andReturnUsing(function ($callback) {
                return $callback();
            });

        Passport::actingAs($userMock, ['*'], 'api');

        // user index
        $this->getJson(route('api.user-management.user.index'))->assertOk();

        // store user
        $this->postJson(route('api.user-management.user.store'), [
            'name' => 'My Name',
            'email' => 'user.'.random_str(10).'@mailinator.com',
            'password' => '12345',
        ])->assertOk();

        // show user detail
        $this->getJson(route('api.user-management.user.show', 'user-id-999'))->assertOk();

        // edit user
        $this->putJson(route('api.user-management.user.update', 'user-id-999'), [
            'name' => 'My Name',
            'email' => 'user.'.random_str(10).'@mailinator.com',
            'password' => '12345',
        ])->assertOk();

        // destroy user
        $this->deleteJson(route('api.user-management.user.destroy', 'user-id-999'))->assertOk();
    }
}
