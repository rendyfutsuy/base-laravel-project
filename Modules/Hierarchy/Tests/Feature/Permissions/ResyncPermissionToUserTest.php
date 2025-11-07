<?php

namespace Modules\Hierarchy\Tests\Feature\Permissions;

use Mockery;
use PHPUnit\Framework\Attributes\Test;
use Illuminate\Support\Facades\DB;
use Laravel\Passport\Passport;
use Tests\TestCase;
use App\Models\User;
use Modules\Hierarchy\Models\Permission;
use Tests\Feature\Components\MockAuthHelper;
use Modules\Hierarchy\Http\Services\Repositories\Contracts\PermissionContract;

class ResyncPermissionToUserTest extends TestCase
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
    public function users_is_required()
    {
        $userMock = $this->mockUser(
            'superadmin@mailinator.com',
            'Superadmin',
            'user-id-123',
            ['api.hierarchy.permission.resync.to.users']
        );

        // Mock Permission for route model binding
        $permissionMock = Mockery::mock(Permission::class)->makePartial();
        $permissionMock->id = 'permission-id-123';

        // Mock route model binding
        \Route::bind('permission', function ($value) use ($permissionMock) {
            return $permissionMock;
        });

        Passport::actingAs($userMock, ['*'], 'api');

        $response = $this->postJson(route('api.hierarchy.permission.resync.to.users', 'permission-id-123'), [
            'users' => [],
        ]);

        $response->assertStatus(400);
    }

    #[Test]
    public function users_is_must_be_array()
    {
        $userMock = $this->mockUser(
            'superadmin@mailinator.com',
            'Superadmin',
            'user-id-123',
            ['api.hierarchy.permission.resync.to.users']
        );

        // Mock Permission for route model binding
        $permissionMock = Mockery::mock(Permission::class)->makePartial();
        $permissionMock->id = 'permission-id-123';

        // Mock route model binding
        \Route::bind('permission', function ($value) use ($permissionMock) {
            return $permissionMock;
        });

        Passport::actingAs($userMock, ['*'], 'api');

        $response = $this->postJson(route('api.hierarchy.permission.resync.to.users', 'permission-id-123'), [
            'users' => 'not-array',
        ]);

        $response->assertStatus(400);
    }

    #[Test]
    public function users_is_must_be_exists()
    {
        $userMock = $this->mockUser(
            'superadmin@mailinator.com',
            'Superadmin',
            'user-id-123',
            ['api.hierarchy.permission.resync.to.users']
        );

        // Mock Permission for route model binding
        $permissionMock = Mockery::mock(Permission::class)->makePartial();
        $permissionMock->id = 'permission-id-123';

        // Mock route model binding
        \Route::bind('permission', function ($value) use ($permissionMock) {
            return $permissionMock;
        });

        // Mock DB facade for validation exists check
        $dbTableMock = Mockery::mock();
        $dbTableMock->shouldReceive('whereIn')
            ->with('id', Mockery::type('array'))
            ->andReturnSelf();
        $dbTableMock->shouldReceive('where')
            ->andReturnSelf();
        $dbTableMock->shouldReceive('count')
            ->andReturn(0);
        $dbTableMock->shouldReceive('exists')
            ->andReturn(false);
        $dbTableMock->shouldReceive('useWritePdo')
            ->andReturnSelf();

        $dbConnectionMock = Mockery::mock();
        $dbConnectionMock->shouldReceive('table')
            ->with('users')
            ->andReturn($dbTableMock);

        DB::shouldReceive('connection')
            ->andReturn($dbConnectionMock);

        Passport::actingAs($userMock, ['*'], 'api');

        $response = $this->postJson(route('api.hierarchy.permission.resync.to.users', 'permission-id-123'), [
            'users' => [
                'not-exists-id',
            ],
        ]);

        $response->assertStatus(400);
    }

    #[Test]
    public function permission_can_sync_personally_to_users()
    {
        $userMock = $this->mockUser(
            'superadmin@mailinator.com',
            'Superadmin',
            'user-id-123',
            ['api.hierarchy.permission.resync.to.users']
        );

        // Mock Permission for route model binding
        $permissionMock = Mockery::mock(Permission::class)->makePartial();
        $permissionMock->id = 'permission-id-123';
        $permissionMock->name = 'api.user-management.staff.index';

        // Mock route model binding
        \Route::bind('permission', function ($value) use ($permissionMock) {
            return $permissionMock;
        });

        // Mock User for validation exists check
        $userMock2 = Mockery::mock(User::class)->makePartial();
        $userMock2->id = 'user-id-456';

        // Mock DB facade for validation exists check
        $dbTableMock = Mockery::mock();
        $dbTableMock->shouldReceive('whereIn')
            ->with('id', Mockery::type('array'))
            ->andReturnSelf();
        $dbTableMock->shouldReceive('where')
            ->andReturnSelf();
        $dbTableMock->shouldReceive('count')
            ->andReturn(1);
        $dbTableMock->shouldReceive('exists')
            ->andReturn(true);
        $dbTableMock->shouldReceive('useWritePdo')
            ->andReturnSelf();
        $dbTableMock->shouldReceive('get')
            ->andReturn(collect([$userMock2]));

        $dbConnectionMock = Mockery::mock();
        $dbConnectionMock->shouldReceive('table')
            ->with('users')
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
        $permissionRepositoryMock->shouldReceive('getUsersByIds')
            ->once()
            ->with(['user-id-456'])
            ->andReturn(collect([$userMock2]));

        $permissionRepositoryMock->shouldReceive('resyncToUser')
            ->once()
            ->with(Mockery::type(User::class), Mockery::type(Permission::class))
            ->andReturn($permissionMock);

        $this->app->instance(PermissionContract::class, $permissionRepositoryMock);

        Passport::actingAs($userMock, ['*'], 'api');

        $response = $this->postJson(route('api.hierarchy.permission.resync.to.users', 'permission-id-123'), [
            'users' => ['user-id-456'],
        ]);

        $response->assertOk();
    }

    #[Test]
    public function trial_user_can_access_permissable_api()
    {
        $userMock = $this->mockUser(
            'trial.user.that.only.have.permissions@mailinator.com',
            'Trial User',
            'user-id-456',
            ['api.user-management.staff.index']
        );

        // Mock repository for index
        $staffCollection = collect([
            Mockery::mock(User::class)->makePartial(),
        ]);

        $userRepositoryMock = Mockery::mock(\Modules\Authentication\Http\Repositories\Contracts\StaffContract::class);
        $userRepositoryMock->shouldReceive('paginated')
            ->once()
            ->andReturn($staffCollection);

        $this->app->instance(\Modules\Authentication\Http\Repositories\Contracts\StaffContract::class, $userRepositoryMock);

        Passport::actingAs($userMock, ['*'], 'api');

        $response = $this->getJson(route('api.user-management.staff.index'));

        $response->assertOk();
    }
}
