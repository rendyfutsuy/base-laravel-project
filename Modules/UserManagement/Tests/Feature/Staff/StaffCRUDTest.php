<?php

namespace Modules\UserManagement\Tests\Feature\Staff;

use PHPUnit\Framework\Attributes\Test;
use Mockery;
use Laravel\Passport\Passport;
use Tests\TestCase;
use Tests\Feature\Components\MockAuthHelper;
use Modules\Authentication\Http\Repositories\Contracts\StaffContract;

class StaffCRUDTest extends TestCase
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
    public function superadmin_can_store_new_staff()
    {
        $userMock = $this->mockUser(
            'superadmin@mailinator.com',
            'Superadmin',
            'user-id-123',
            ['api.user-management.staff.store']
        );

        // Mock StaffRepository
        $newStaffMock = Mockery::mock(\App\Models\User::class)->makePartial();
        $newStaffMock->id = 'staff-id-789';

        $staffRepositoryMock = Mockery::mock(StaffContract::class);
        $staffRepositoryMock->shouldReceive('store')
            ->once()
            ->andReturn($newStaffMock);

        $this->app->instance(StaffContract::class, $staffRepositoryMock);

        // Mock DB::transaction for controller
        \Illuminate\Support\Facades\DB::shouldReceive('transaction')
            ->andReturnUsing(function ($callback) {
                return $callback();
            });

        Passport::actingAs($userMock, ['*'], 'api');

        $response = $this->postJson(route('api.user-management.staff.store'), [
            'name' => 'My Name',
            'email' => 'my.name.'.random_str(10).'@mailinator.com',
            'password' => '12345',
        ]);

        $response->assertOk();
    }

    #[Test]
    public function staff_can_not_store_new_staff()
    {
        $userMock = $this->mockUser(
            'staff@mailinator.com',
            'Staff',
            'user-id-456',
            [] // No permissions
        );

        Passport::actingAs($userMock, ['*'], 'api');

        $response = $this->postJson(route('api.user-management.staff.store'), [
            'name' => 'My Name',
            'email' => 'my.name.'.random_str(10).'@mailinator.com',
            'password' => '12345',
        ]);

        $response->assertForbidden();
    }

    #[Test]
    public function normal_staff_can_not_store_new_staff()
    {
        $userMock = $this->mockUser(
            'user.1@mailinator.com',
            'User',
            'user-id-789',
            [] // No permissions
        );

        Passport::actingAs($userMock, ['*'], 'api');

        $response = $this->postJson(route('api.user-management.staff.store'), [
            'name' => 'My Name',
            'email' => 'my.name.'.random_str(10).'@mailinator.com',
            'password' => '12345',
        ]);

        $response->assertForbidden();
    }

    #[Test]
    public function guest_can_not_store_new_staff()
    {
        $response = $this->postJson(route('api.user-management.staff.store'), [
            'name' => 'My Name',
            'email' => 'my.name.'.random_str(10).'@mailinator.com',
            'password' => '12345',
        ]);

        $response->assertUnauthorized();
    }

    #[Test]
    public function superadmin_can_update_new_staff()
    {
        $userMock = $this->mockUser(
            'superadmin@mailinator.com',
            'Superadmin',
            'user-id-123',
            ['api.user-management.staff.update']
        );

        // Mock StaffRepository
        $existingStaffMock = Mockery::mock(\App\Models\User::class)->makePartial();
        $existingStaffMock->id = 'staff-id-999';
        $existingStaffMock->email = 'existing@mailinator.com';
        $existingStaffMock->shouldReceive('toArray')->andReturn([
            'id' => 'staff-id-999',
            'email' => 'existing@mailinator.com',
        ]);

        $staffRepositoryMock = Mockery::mock(StaffContract::class);
        $staffRepositoryMock->shouldReceive('find')
            ->with('staff-id-999')
            ->andReturn($existingStaffMock);

        $staffRepositoryMock->shouldReceive('validateUserRole')
            ->with(Mockery::type(\App\Models\User::class))
            ->andReturn(true);

        $staffRepositoryMock->shouldReceive('update')
            ->once()
            ->andReturn(true);

        $this->app->instance(StaffContract::class, $staffRepositoryMock);

        // Mock DB::transaction for controller
        \Illuminate\Support\Facades\DB::shouldReceive('transaction')
            ->andReturnUsing(function ($callback) {
                return $callback();
            });

        Passport::actingAs($userMock, ['*'], 'api');

        $response = $this->putJson(route('api.user-management.staff.update', 'staff-id-999'), [
            'name' => 'My Name',
            'email' => 'my.name.'.random_str(10).'@mailinator.com',
            'password' => '12345',
        ]);

        $response->assertOk();
    }

    #[Test]
    public function staff_can_update_new_staff()
    {
        $userMock = $this->mockUser(
            'staff@mailinator.com',
            'Staff',
            'user-id-456',
            ['api.user-management.staff.update']
        );

        // Mock StaffRepository
        $existingStaffMock = Mockery::mock(\App\Models\User::class)->makePartial();
        $existingStaffMock->id = 'staff-id-999';
        $existingStaffMock->email = 'existing@mailinator.com';
        $existingStaffMock->shouldReceive('toArray')->andReturn([
            'id' => 'staff-id-999',
            'email' => 'existing@mailinator.com',
        ]);

        $staffRepositoryMock = Mockery::mock(StaffContract::class);
        $staffRepositoryMock->shouldReceive('find')
            ->with('staff-id-999')
            ->andReturn($existingStaffMock);

        $staffRepositoryMock->shouldReceive('validateUserRole')
            ->with(Mockery::type(\App\Models\User::class))
            ->andReturn(true);

        $staffRepositoryMock->shouldReceive('update')
            ->once()
            ->andReturn(true);

        $this->app->instance(StaffContract::class, $staffRepositoryMock);

        // Mock DB::transaction for controller
        \Illuminate\Support\Facades\DB::shouldReceive('transaction')
            ->andReturnUsing(function ($callback) {
                return $callback();
            });

        Passport::actingAs($userMock, ['*'], 'api');

        $response = $this->putJson(route('api.user-management.staff.update', 'staff-id-999'), [
            'name' => 'My Name',
            'email' => 'my.name.'.random_str(10).'@mailinator.com',
            'password' => '12345',
        ]);

        $response->assertOk();
    }

    #[Test]
    public function normal_staff_can_not_update_new_staff()
    {
        $userMock = $this->mockUser(
            'user.1@mailinator.com',
            'User',
            'user-id-789',
            [] // No permissions
        );

        Passport::actingAs($userMock, ['*'], 'api');

        $response = $this->putJson(route('api.user-management.staff.update', 1), [
            'name' => 'My Name',
            'email' => 'my.name.'.random_str(10).'@mailinator.com',
            'password' => '12345',
        ]);

        $response->assertForbidden();
    }

    #[Test]
    public function guest_can_not_update_new_staff()
    {
        $response = $this->putJson(route('api.user-management.staff.update', 1), [
            'name' => 'My Name',
            'email' => 'my.name.'.random_str(10).'@mailinator.com',
            'password' => '12345',
        ]);

        $response->assertUnauthorized();
    }

    #[Test]
    public function superadmin_can_destroy_new_staff()
    {
        $userMock = $this->mockUser(
            'superadmin@mailinator.com',
            'Superadmin',
            'user-id-123',
            ['api.user-management.staff.destroy']
        );

        // Mock StaffRepository
        $existingStaffMock = Mockery::mock(\App\Models\User::class)->makePartial();
        $existingStaffMock->id = 'staff-id-999';
        $existingStaffMock->email = 'existing@mailinator.com';

        $staffRepositoryMock = Mockery::mock(StaffContract::class);
        $staffRepositoryMock->shouldReceive('find')
            ->with('staff-id-999')
            ->andReturn($existingStaffMock);

        $staffRepositoryMock->shouldReceive('delete')
            ->once()
            ->andReturn(true);

        $this->app->instance(StaffContract::class, $staffRepositoryMock);

        // Mock DB::transaction for controller
        \Illuminate\Support\Facades\DB::shouldReceive('transaction')
            ->andReturnUsing(function ($callback) {
                return $callback();
            });

        Passport::actingAs($userMock, ['*'], 'api');

        $response = $this->deleteJson(route('api.user-management.staff.destroy', 'staff-id-999'));

        $response->assertOk();
    }

    #[Test]
    public function staff_can_not_destroy_new_staff()
    {
        $userMock = $this->mockUser(
            'staff@mailinator.com',
            'Staff',
            'user-id-456',
            [] // No permissions
        );

        Passport::actingAs($userMock, ['*'], 'api');

        $response = $this->deleteJson(route('api.user-management.staff.destroy', 1));

        $response->assertForbidden();
    }

    #[Test]
    public function normal_staff_can_not_destroy_new_staff()
    {
        $userMock = $this->mockUser(
            'user.1@mailinator.com',
            'User',
            'user-id-789',
            [] // No permissions
        );

        Passport::actingAs($userMock, ['*'], 'api');

        $response = $this->deleteJson(route('api.user-management.staff.destroy', 1));

        $response->assertForbidden();
    }

    #[Test]
    public function guest_can_not_destroy_new_staff()
    {
        $response = $this->deleteJson(route('api.user-management.staff.destroy', 1));

        $response->assertUnauthorized();
    }

    #[Test]
    public function superadmin_can_see_index()
    {
        $userMock = $this->mockUser(
            'superadmin@mailinator.com',
            'Superadmin',
            'user-id-123',
            ['api.user-management.staff.index']
        );

        // Mock StaffRepository
        $staffCollection = collect([
            Mockery::mock(\App\Models\User::class)->makePartial(),
        ]);

        $staffRepositoryMock = Mockery::mock(StaffContract::class);
        $staffRepositoryMock->shouldReceive('paginated')
            ->once()
            ->andReturn($staffCollection);

        $this->app->instance(StaffContract::class, $staffRepositoryMock);

        Passport::actingAs($userMock, ['*'], 'api');

        $response = $this->getJson(route('api.user-management.staff.index'));

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

        $response = $this->getJson(route('api.user-management.staff.index'));

        $response->assertForbidden();
    }

    #[Test]
    public function normal_staff_can_not_see_index()
    {
        $userMock = $this->mockUser(
            'user.1@mailinator.com',
            'User',
            'user-id-789',
            [] // No permissions
        );

        Passport::actingAs($userMock, ['*'], 'api');

        $response = $this->getJson(route('api.user-management.staff.index'));

        $response->assertForbidden();
    }

    #[Test]
    public function guest_can_not_see_index()
    {
        $response = $this->getJson(route('api.user-management.staff.index'));

        $response->assertUnauthorized();
    }
}
