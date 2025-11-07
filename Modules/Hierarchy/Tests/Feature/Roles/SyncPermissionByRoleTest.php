<?php

namespace Modules\Hierarchy\Tests\Feature\Roles;

use Mockery;
use PHPUnit\Framework\Attributes\Test;
use Illuminate\Support\Facades\DB;
use Laravel\Passport\Passport;
use Tests\TestCase;
use Modules\Hierarchy\Models\Role;
use Tests\Feature\Components\MockAuthHelper;
use Modules\Hierarchy\Http\Services\Repositories\Contracts\RoleContract;

class SyncPermissionByRoleTest extends TestCase
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
    public function permissions_in_permission_sync_is_required()
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

        // Mock route model binding
        \Route::bind('role', function ($value) use ($roleMock) {
            return $roleMock;
        });

        Passport::actingAs($userMock, ['*'], 'api');

        $response = $this->postJson(route('api.hierarchy.role.permission.sync', 'role-id-123'), [
            'permissions' => [],
        ]);

        $response->assertStatus(400);
    }

    #[Test]
    public function permissions_in_permission_sync_is_must_be_array_numeric()
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

        // Mock route model binding
        \Route::bind('role', function ($value) use ($roleMock) {
            return $roleMock;
        });

        Passport::actingAs($userMock, ['*'], 'api');

        $response = $this->postJson(route('api.hierarchy.role.permission.sync', 'role-id-123'), [
            'permissions' => ['not-numeric', 'also-not-numeric'],
        ]);

        $response->assertStatus(400);
    }

    #[Test]
    public function role_must_be_exists()
    {
        $userMock = $this->mockUser(
            'superadmin@mailinator.com',
            'Superadmin',
            'user-id-123',
            ['api.hierarchy.role.permission.sync']
        );

        // Mock route model binding to return null (role not found)
        \Route::bind('role', function ($value) {
            return null;
        });

        Passport::actingAs($userMock, ['*'], 'api');

        $response = $this->postJson(route('api.hierarchy.role.permission.sync', 9999), [
            'permissions' => [],
        ]);

        $response->assertNotFound();
    }

    #[Test]
    public function superadmin_sync_role_index_to_staff_role()
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
        $roleMock->name = 'STAFF';
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

        DB::shouldReceive('connection')
            ->andReturn($dbConnectionMock);

        // Mock DB::transaction for controller
        DB::shouldReceive('transaction')
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
    public function staff_role_now_can_access_role_index_after_edit()
    {
        // First request - with permission
        $userMock1 = $this->mockUser(
            'staff@mailinator.com',
            'Staff',
            'user-id-456',
            ['api.hierarchy.role.index']
        );

        // Mock RoleRepository for index
        $roleCollection = collect([
            Mockery::mock(Role::class)->makePartial(),
        ]);

        $roleRepositoryMock = Mockery::mock(RoleContract::class);
        $roleRepositoryMock->shouldReceive('paginated')
            ->once()
            ->andReturn($roleCollection);

        $this->app->instance(RoleContract::class, $roleRepositoryMock);

        Passport::actingAs($userMock1, ['*'], 'api');

        $response = $this->getJson(route('api.hierarchy.role.index'));

        $response->assertOk();

        // Second request - without permission
        $userMock2 = $this->mockUser(
            'staff@mailinator.com',
            'Staff',
            'user-id-456',
            [] // No permissions
        );

        Passport::actingAs($userMock2, ['*'], 'api');

        $response = $this->getJson(route('api.hierarchy.role.index'));

        $response->assertForbidden();
    }
}
