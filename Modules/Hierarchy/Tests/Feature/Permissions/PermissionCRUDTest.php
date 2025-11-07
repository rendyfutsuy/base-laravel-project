<?php

namespace Modules\Hierarchy\Tests\Feature\Permissions;

use Mockery;
use PHPUnit\Framework\Attributes\Test;
use Illuminate\Support\Facades\DB;
use Laravel\Passport\Passport;
use Tests\TestCase;
use Modules\Hierarchy\Models\Permission;
use Tests\Feature\Components\MockAuthHelper;
use Modules\Hierarchy\Http\Services\Repositories\Contracts\PermissionContract;

class PermissionCRUDTest extends TestCase
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
    public function superadmin_can_store_new_permission()
    {
        $userMock = $this->mockUser(
            'superadmin@mailinator.com',
            'Superadmin',
            'user-id-123',
            ['api.hierarchy.permission.store']
        );

        // Mock PermissionRepository
        $permissionMock = Mockery::mock(Permission::class)->makePartial();
        $permissionMock->id = 'permission-id-123';
        $permissionMock->name = 'api.example.store';
        $permissionMock->roles = collect([]);

        $permissionRepositoryMock = Mockery::mock(PermissionContract::class);
        $permissionRepositoryMock->shouldReceive('store')
            ->once()
            ->with(Mockery::type('array'))
            ->andReturn($permissionMock);

        $this->app->instance(PermissionContract::class, $permissionRepositoryMock);

        Passport::actingAs($userMock, ['*'], 'api');

        $response = $this->postJson(route('api.hierarchy.permission.store'), [
            'name' => 'api.example.store',
            'roles' => ['role-id-123'],
        ]);

        $response->assertOk();
    }

    #[Test]
    public function staff_can_not_store_new_permission()
    {
        $userMock = $this->mockUser(
            'staff@mailinator.com',
            'Staff',
            'user-id-456',
            [] // No permissions
        );

        Passport::actingAs($userMock, ['*'], 'api');

        $response = $this->postJson(route('api.hierarchy.permission.store'), [
            'name' => 'api.example.store',
            'roles' => ['role-id-123'],
        ]);

        $response->assertForbidden();
    }

    #[Test]
    public function normal_permission_can_not_store_new_permission()
    {
        $userMock = $this->mockUser(
            'user.1@mailinator.com',
            'User',
            'user-id-789',
            [] // No permissions
        );

        Passport::actingAs($userMock, ['*'], 'api');

        $response = $this->postJson(route('api.hierarchy.permission.store'), [
            'name' => 'api.example.store',
            'roles' => ['role-id-123'],
        ]);

        $response->assertForbidden();
    }

    #[Test]
    public function guest_can_not_store_new_permission()
    {
        $response = $this->postJson(route('api.hierarchy.permission.store'), [
            'name' => 'api.example.store',
            'roles' => ['role-id-123'],
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
            ['api.hierarchy.permission.index']
        );

        // Mock PermissionRepository
        $permissionCollection = collect([
            Mockery::mock(Permission::class)->makePartial(),
        ]);

        $permissionRepositoryMock = Mockery::mock(PermissionContract::class);
        $permissionRepositoryMock->shouldReceive('paginated')
            ->once()
            ->andReturn($permissionCollection);

        $this->app->instance(PermissionContract::class, $permissionRepositoryMock);

        Passport::actingAs($userMock, ['*'], 'api');

        $response = $this->getJson(route('api.hierarchy.permission.index'));

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

        $response = $this->getJson(route('api.hierarchy.permission.index'));

        $response->assertForbidden();
    }

    #[Test]
    public function normal_permission_can_not_see_index()
    {
        $userMock = $this->mockUser(
            'user.1@mailinator.com',
            'User',
            'user-id-789',
            [] // No permissions
        );

        Passport::actingAs($userMock, ['*'], 'api');

        $response = $this->getJson(route('api.hierarchy.permission.index'));

        $response->assertForbidden();
    }

    #[Test]
    public function guest_can_not_see_index()
    {
        $response = $this->getJson(route('api.hierarchy.permission.index'));

        $response->assertUnauthorized();
    }

    #[Test]
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

    #[Test]
    public function normal_permission_can_not_resync_permission()
    {
        $userMock = $this->mockUser(
            'user.1@mailinator.com',
            'User',
            'user-id-789',
            [] // No permissions
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
            'roles' => ['role-id-1', 'role-id-2', 'role-id-3'],
        ]);

        $response->assertForbidden();
    }

    #[Test]
    public function guest_can_not_resync_permission()
    {
        // Mock Permission for route model binding
        $permissionMock = Mockery::mock(Permission::class)->makePartial();
        $permissionMock->id = 'permission-id-123';

        // Mock route model binding
        \Route::bind('permission', function ($value) use ($permissionMock) {
            return $permissionMock;
        });

        $response = $this->postJson(route('api.hierarchy.permission.resync', 'permission-id-123'), [
            'roles' => ['role-id-1', 'role-id-2', 'role-id-3'],
        ]);

        $response->assertUnauthorized();
    }

    #[Test]
    public function staff_can_not_resync_permission()
    {
        $userMock = $this->mockUser(
            'staff@mailinator.com',
            'Staff',
            'user-id-456',
            [] // No permissions
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
            'roles' => ['role-id-1', 'role-id-2', 'role-id-3'],
        ]);

        $response->assertForbidden();
    }
}
