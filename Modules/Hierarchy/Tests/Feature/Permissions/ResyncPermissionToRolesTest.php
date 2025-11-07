<?php

namespace Modules\Hierarchy\Tests\Feature\Permissions;

use Mockery;
use Illuminate\Support\Facades\DB;
use Laravel\Passport\Passport;
use Tests\TestCase;
use Modules\Hierarchy\Models\Role;
use Modules\Hierarchy\Models\Permission;
use Tests\Feature\Components\MockAuthHelper;
use Modules\Hierarchy\Http\Services\Repositories\Contracts\PermissionContract;

class ResyncPermissionToRolesTest extends TestCase
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
    public function permissions_in_permission_sync_is_required()
    {
        $userMock = $this->mockUser(
            'superadmin@mailinator.com',
            'Superadmin',
            'user-id-123',
            ['api.hierarchy.permission.resync']
        );

        // Mock Permission for route model binding
        $permissionMock = Mockery::mock(Permission::class)->makePartial();
        $permissionMock->id = 'permission-id-123';

        // Mock route model binding
        \Route::bind('permission', function ($value) use ($permissionMock) {
            return $permissionMock;
        });

        Passport::actingAs($userMock, ['*'], 'api');

        $response = $this->postJson(route('api.hierarchy.permission.resync', 'permission-id-123'), [
            'roles' => [],
        ]);

        $response->assertStatus(400);
    }

    /** @test */
    public function permissions_in_permission_sync_is_must_exists()
    {
        $userMock = $this->mockUser(
            'superadmin@mailinator.com',
            'Superadmin',
            'user-id-123',
            ['api.hierarchy.permission.resync']
        );

        // Mock Permission for route model binding
        $permissionMock = Mockery::mock(Permission::class)->makePartial();
        $permissionMock->id = 'permission-id-123';

        // Mock route model binding
        \Route::bind('permission', function ($value) use ($permissionMock) {
            return $permissionMock;
        });

        Passport::actingAs($userMock, ['*'], 'api');

        $response = $this->postJson(route('api.hierarchy.permission.resync', 'permission-id-123'), [
            'roles' => ['try', 'my', 'logic', 'here'],
        ]);

        $response->assertStatus(400);
    }

    /** @test */
    public function superadmin_can_resync_permission()
    {
        $userMock = $this->mockUser(
            'superadmin@mailinator.com',
            'Superadmin',
            'user-id-123',
            ['api.hierarchy.permission.resync']
        );

        // Mock Permission for route model binding
        $permissionMock = Mockery::mock(Permission::class)->makePartial();
        $permissionMock->id = 'permission-id-123';
        $permissionMock->name = 'api.example.permission';
        $permissionMock->roles = collect([]);

        // Mock route model binding
        \Route::bind('permission', function ($value) use ($permissionMock) {
            return $permissionMock;
        });

        // Mock DB facade for validation exists check
        // Laravel validation exists rule uses DB::table()->whereIn()->exists()
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
            ->with('roles')
            ->andReturn($dbTableMock);

        DB::shouldReceive('connection')
            ->andReturn($dbConnectionMock);

        // Mock DB::transaction for controller
        DB::shouldReceive('transaction')
            ->andReturnUsing(function ($callback) {
                return $callback();
            });

        // Mock PermissionRepository
        $permissionRepositoryMock = Mockery::mock(PermissionContract::class);
        $permissionRepositoryMock->shouldReceive('resync')
            ->once()
            ->with(Mockery::type('array'), Mockery::type(Permission::class))
            ->andReturn($permissionMock);

        $this->app->instance(PermissionContract::class, $permissionRepositoryMock);

        Passport::actingAs($userMock, ['*'], 'api');

        $response = $this->postJson(route('api.hierarchy.permission.resync', 'permission-id-123'), [
            'roles' => ['role-id-1', 'role-id-2'],
        ]);

        $response->assertOk();
    }

    /** @test */
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

        $roleRepositoryMock = Mockery::mock(\Modules\Hierarchy\Http\Services\Repositories\Contracts\RoleContract::class);
        $roleRepositoryMock->shouldReceive('paginated')
            ->once()
            ->andReturn($roleCollection);

        $this->app->instance(\Modules\Hierarchy\Http\Services\Repositories\Contracts\RoleContract::class, $roleRepositoryMock);

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
