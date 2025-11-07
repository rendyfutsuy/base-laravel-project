<?php

namespace Modules\Hierarchy\Tests\Feature\Roles;

use Mockery;
use PHPUnit\Framework\Attributes\Test;
use Laravel\Passport\Passport;
use Tests\TestCase;
use Modules\Hierarchy\Models\Role;
use Tests\Feature\Components\MockAuthHelper;
use Modules\Hierarchy\Http\Services\Repositories\Contracts\RoleContract;

class RoleCRUDTest extends TestCase
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

    #[Test]
    public function superadmin_can_store_new_role()
    {
        $userMock = $this->mockUser(
            'superadmin@mailinator.com',
            'Superadmin',
            'user-id-123',
            ['api.hierarchy.role.store']
        );

        // Mock RoleRepository
        $roleMock = Mockery::mock(Role::class)->makePartial();
        $roleMock->id = 'role-id-123';
        $roleMock->name = 'EXAMPLE';
        $roleMock->guard_name = 'api';

        $roleRepositoryMock = Mockery::mock(RoleContract::class);
        $roleRepositoryMock->shouldReceive('store')
            ->once()
            ->with(Mockery::type('array'))
            ->andReturn($roleMock);

        $this->app->instance(RoleContract::class, $roleRepositoryMock);

        Passport::actingAs($userMock, ['*'], 'api');

        $response = $this->postJson(route('api.hierarchy.role.store'), [
            'name' => 'EXAMPLE',
        ]);

        $response->assertOk();
    }

    #[Test]
    public function staff_can_not_store_new_role()
    {
        $userMock = $this->mockUser(
            'staff@mailinator.com',
            'Staff',
            'user-id-456',
            [] // No permissions
        );

        Passport::actingAs($userMock, ['*'], 'api');

        $response = $this->postJson(route('api.hierarchy.role.store'), [
            'name' => 'EXAMPLE',
        ]);

        $response->assertForbidden();
    }

    #[Test]
    public function normal_role_can_not_store_new_role()
    {
        $userMock = $this->mockUser(
            'user.1@mailinator.com',
            'User',
            'user-id-789',
            [] // No permissions
        );

        Passport::actingAs($userMock, ['*'], 'api');

        $response = $this->postJson(route('api.hierarchy.role.store'), [
            'name' => 'EXAMPLE',
        ]);

        $response->assertForbidden();
    }

    #[Test]
    public function guest_can_not_store_new_role()
    {
        $response = $this->postJson(route('api.hierarchy.role.store'), [
            'name' => 'EXAMPLE',
        ]);

        $response->assertUnauthorized();
    }

    #[Test]
    public function superadmin_can_see_index()
    {
        $userMock = $this->mockUser(
            'superadmin@mailinator.com',
            'Superadmin',
            'user-id-123',
            ['api.hierarchy.role.index']
        );

        // Mock RoleRepository
        $roleCollection = collect([
            Mockery::mock(Role::class)->makePartial(),
        ]);

        $roleRepositoryMock = Mockery::mock(RoleContract::class);
        $roleRepositoryMock->shouldReceive('paginated')
            ->once()
            ->andReturn($roleCollection);

        $this->app->instance(RoleContract::class, $roleRepositoryMock);

        Passport::actingAs($userMock, ['*'], 'api');

        $response = $this->getJson(route('api.hierarchy.role.index'));

        $response->assertOk();
    }

    #[Test]
    public function staff_can_not_see_index()
    {
        $userMock = $this->mockUser(
            'staff@mailinator.com',
            'Staff',
            'user-id-456',
            [] // No permissions
        );

        Passport::actingAs($userMock, ['*'], 'api');

        $response = $this->getJson(route('api.hierarchy.role.index'));

        $response->assertForbidden();
    }

    #[Test]
    public function normal_role_can_not_see_index()
    {
        $userMock = $this->mockUser(
            'user.1@mailinator.com',
            'User',
            'user-id-789',
            [] // No permissions
        );

        Passport::actingAs($userMock, ['*'], 'api');

        $response = $this->getJson(route('api.hierarchy.role.index'));

        $response->assertForbidden();
    }

    #[Test]
    public function guest_can_not_see_index()
    {
        $response = $this->getJson(route('api.hierarchy.role.index'));

        $response->assertUnauthorized();
    }

    #[Test]
    public function superadmin_can_sync_new_permission()
    {
        $userMock = $this->mockUser(
            'superadmin@mailinator.com',
            'Superadmin',
            'user-id-123',
            ['api.hierarchy.role.permission.sync']
        );

        // Mock Role for route model binding
        $roleMock = Mockery::mock(Role::class)->makePartial();
        $roleMock->id = 'role-id-123';
        $roleMock->name = 'SUPER_ADMIN';
        $roleMock->permissions = collect([]);

        // Mock route model binding
        \Route::bind('role', function ($value) use ($roleMock) {
            return $roleMock;
        });

        // Mock DB facade for validation exists check
        $dbTableMock = Mockery::mock();
        $dbTableMock->shouldReceive('whereIn')
            ->with('id', Mockery::type('array'))
            ->andReturnSelf();
        $dbTableMock->shouldReceive('where')
            ->andReturnSelf();
        $dbTableMock->shouldReceive('count')
            ->andReturn(2);
        $dbTableMock->shouldReceive('exists')
            ->andReturn(true);
        $dbTableMock->shouldReceive('useWritePdo')
            ->andReturnSelf();

        $dbConnectionMock = Mockery::mock();
        $dbConnectionMock->shouldReceive('table')
            ->with('permissions')
            ->andReturn($dbTableMock);

        \Illuminate\Support\Facades\DB::shouldReceive('connection')
            ->andReturn($dbConnectionMock);

        // Mock DB::transaction for controller
        \Illuminate\Support\Facades\DB::shouldReceive('transaction')
            ->andReturnUsing(function ($callback) {
                return $callback();
            });

        // Mock RoleRepository
        $roleRepositoryMock = Mockery::mock(RoleContract::class);
        $roleRepositoryMock->shouldReceive('sync')
            ->once()
            ->with(Mockery::type('array'), Mockery::type(Role::class))
            ->andReturn($roleMock);

        $this->app->instance(RoleContract::class, $roleRepositoryMock);

        Passport::actingAs($userMock, ['*'], 'api');

        $response = $this->postJson(route('api.hierarchy.role.permission.sync', 'role-id-123'), [
            'permissions' => ['permission-id-1', 'permission-id-2'],
        ]);

        $response->assertOk();
    }

    #[Test]
    public function staff_can_not_sync_new_permission()
    {
        $userMock = $this->mockUser(
            'staff@mailinator.com',
            'Staff',
            'user-id-456',
            [] // No permissions
        );

        // Mock Role for route model binding
        $roleMock = Mockery::mock(Role::class)->makePartial();
        $roleMock->id = 'role-id-123';

        // Mock route model binding
        \Route::bind('role', function ($value) use ($roleMock) {
            return $roleMock;
        });

        Passport::actingAs($userMock, ['*'], 'api');

        $response = $this->postJson(route('api.hierarchy.role.permission.sync', 'role-id-123'), [
            'permissions' => ['permission-id-1', 'permission-id-2'],
        ]);

        $response->assertForbidden();
    }

    #[Test]
    public function normal_permission_can_not_sync_new_permission()
    {
        $userMock = $this->mockUser(
            'user.1@mailinator.com',
            'User',
            'user-id-789',
            [] // No permissions
        );

        // Mock Role for route model binding
        $roleMock = Mockery::mock(Role::class)->makePartial();
        $roleMock->id = 'role-id-123';

        // Mock route model binding
        \Route::bind('role', function ($value) use ($roleMock) {
            return $roleMock;
        });

        Passport::actingAs($userMock, ['*'], 'api');

        $response = $this->postJson(route('api.hierarchy.role.permission.sync', 'role-id-123'), [
            'permissions' => ['permission-id-1', 'permission-id-2'],
        ]);

        $response->assertForbidden();
    }

    #[Test]
    public function guest_can_not_sync_new_permission()
    {
        // Mock Role for route model binding
        $roleMock = Mockery::mock(Role::class)->makePartial();
        $roleMock->id = 'role-id-123';

        // Mock route model binding
        \Route::bind('role', function ($value) use ($roleMock) {
            return $roleMock;
        });

        $response = $this->postJson(route('api.hierarchy.role.permission.sync', 'role-id-123'), [
            'permissions' => ['permission-id-1', 'permission-id-2'],
        ]);

        $response->assertUnauthorized();
    }
}
