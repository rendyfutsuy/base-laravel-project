<?php

namespace Modules\Mobile\Tests\Feature\UserManagement\Staff;

use Mockery;
use Carbon\Carbon;
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

    /** @test */
    public function superadmin_can_store_new_staff()
    {
        $userMock = $this->mockUser(
            'superadmin@mailinator.com',
            'Superadmin',
            'user-id-123',
            ['api.user-management.mobile.staff.store']
        );

        // Mock StaffRepository
        $newStaffMock = Mockery::mock(\App\Models\User::class)->makePartial();
        $newStaffMock->id = 'staff-id-789';

        // StaffRepository extends UserRepository, so we need to mock methods from BaseRepository
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

        $response = $this->postJson(route('api.user-management.mobile.staff.store'), [
            'name' => 'My Name',
            'email' => 'my.name.'.random_str(10).'@mailinator.com',
            'password' => '12345',
        ]);

        $response->assertOk();
    }

    /** @test */
    public function staff_can_not_store_new_staff()
    {
        $userMock = $this->mockUser(
            'staff@mailinator.com',
            'Staff',
            'user-id-456',
            [] // No permissions
        );

        Passport::actingAs($userMock, ['*'], 'api');

        $response = $this->postJson(route('api.user-management.mobile.staff.store'), [
            'name' => 'My Name',
            'email' => 'my.name.'.random_str(10).'@mailinator.com',
            'password' => '12345',
        ]);

        $response->assertForbidden();
    }

    /** @test */
    public function normal_staff_can_not_store_new_staff()
    {
        $userMock = $this->mockUser(
            'user.1@mailinator.com',
            'User',
            'user-id-789',
            [] // No permissions
        );

        Passport::actingAs($userMock, ['*'], 'api');

        $response = $this->postJson(route('api.user-management.mobile.staff.store'), [
            'name' => 'My Name',
            'email' => 'my.name.'.Carbon::now()->format('hms').'@mailinator.com',
            'password' => '12345',
        ]);

        $response->assertForbidden();
    }

    /** @test */
    public function guest_can_not_store_new_staff()
    {
        $response = $this->postJson(route('api.user-management.mobile.staff.store'), [
            'name' => 'My Name',
            'email' => 'my.name.'.Carbon::now()->format('hms').'@mailinator.com',
            'password' => '12345',
        ]);

        $response->assertUnauthorized();
    }

    /** @test */
    public function superadmin_can_update_new_staff()
    {
        $userMock = $this->mockUser(
            'superadmin@mailinator.com',
            'Superadmin',
            'user-id-123',
            ['api.user-management.mobile.staff.update']
        );

        // Mock StaffRepository
        $existingStaffMock = Mockery::mock(\App\Models\User::class)->makePartial();
        $existingStaffMock->id = 'staff-id-999';
        $existingStaffMock->email = 'existing@mailinator.com';
        $existingStaffMock->shouldReceive('toArray')->andReturn([
            'id' => 'staff-id-999',
            'email' => 'existing@mailinator.com',
        ]);

        // StaffRepository extends UserRepository, so we need to mock methods from BaseRepository
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

        $response = $this->putJson(route('api.user-management.mobile.staff.update', 'staff-id-999'), [
            'name' => 'My Name',
            'email' => 'my.name.'.random_str(10).'@mailinator.com',
            'password' => '12345',
        ]);

        $response->assertOk();
    }

    /** @test */
    public function staff_can_not_update_new_staff()
    {
        $userMock = $this->mockUser(
            'staff@mailinator.com',
            'Staff',
            'user-id-456',
            [] // No permissions
        );

        Passport::actingAs($userMock, ['*'], 'api');

        $response = $this->putJson(route('api.user-management.mobile.staff.update', 1), [
            'name' => 'My Name',
            'email' => 'my.name.'.random_str(10).'@mailinator.com',
            'password' => '12345',
        ]);

        $response->assertForbidden();
    }

    /** @test */
    public function normal_staff_can_not_update_new_staff()
    {
        $userMock = $this->mockUser(
            'user.1@mailinator.com',
            'User',
            'user-id-789',
            [] // No permissions
        );

        Passport::actingAs($userMock, ['*'], 'api');

        $response = $this->putJson(route('api.user-management.mobile.staff.update', 1), [
            'name' => 'My Name',
            'email' => 'my.name.'.Carbon::now()->format('hms').'@mailinator.com',
            'password' => '12345',
        ]);

        $response->assertForbidden();
    }

    /** @test */
    public function guest_can_not_update_new_staff()
    {
        $response = $this->putJson(route('api.user-management.mobile.staff.update', 1), [
            'name' => 'My Name',
            'email' => 'my.name.'.Carbon::now()->format('hms').'@mailinator.com',
            'password' => '12345',
        ]);

        $response->assertUnauthorized();
    }

    /** @test */
    public function superadmin_can_destroy_new_staff()
    {
        $userMock = $this->mockUser(
            'superadmin@mailinator.com',
            'Superadmin',
            'user-id-123',
            ['api.user-management.mobile.staff.destroy']
        );

        // Mock StaffRepository
        $existingStaffMock = Mockery::mock(\App\Models\User::class)->makePartial();
        $existingStaffMock->id = 'staff-id-999';
        $existingStaffMock->email = 'existing@mailinator.com';

        // StaffRepository extends UserRepository, so we need to mock methods from BaseRepository
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

        $response = $this->deleteJson(route('api.user-management.mobile.staff.destroy', 'staff-id-999'));

        $response->assertOk();
    }

    /** @test */
    public function staff_can_not_destroy_new_staff()
    {
        $userMock = $this->mockUser(
            'staff@mailinator.com',
            'Staff',
            'user-id-456',
            [] // No permissions
        );

        Passport::actingAs($userMock, ['*'], 'api');

        $response = $this->deleteJson(route('api.user-management.mobile.staff.destroy', 1));

        $response->assertForbidden();
    }

    /** @test */
    public function normal_staff_can_not_destroy_new_staff()
    {
        $userMock = $this->mockUser(
            'user.1@mailinator.com',
            'User',
            'user-id-789',
            [] // No permissions
        );

        Passport::actingAs($userMock, ['*'], 'api');

        $response = $this->deleteJson(route('api.user-management.mobile.staff.destroy', 1));

        $response->assertForbidden();
    }

    /** @test */
    public function guest_can_not_destroy_new_staff()
    {
        $response = $this->deleteJson(route('api.user-management.mobile.staff.destroy', 1));

        $response->assertUnauthorized();
    }

    /** @test */
    public function superadmin_can_see_index()
    {
        $userMock = $this->mockUser(
            'superadmin@mailinator.com',
            'Superadmin',
            'user-id-123',
            ['api.user-management.mobile.staff.index']
        );

        // Mock StaffRepository
        $staffCollection = collect([
            Mockery::mock(\App\Models\User::class)->makePartial(),
        ]);

        // StaffRepository extends UserRepository, so we need to mock methods from BaseRepository
        $staffRepositoryMock = Mockery::mock(StaffContract::class);
        $staffRepositoryMock->shouldReceive('paginated')
            ->once()
            ->andReturn($staffCollection);

        $this->app->instance(StaffContract::class, $staffRepositoryMock);

        Passport::actingAs($userMock, ['*'], 'api');

        $response = $this->getJson(route('api.user-management.mobile.staff.index'));

        $response->assertOk();
    }

    /** @test */
    public function staff_can_not_see_index()
    {
        $userMock = $this->mockUser(
            'staff@mailinator.com',
            'Staff',
            'user-id-456',
            [] // No permissions
        );

        Passport::actingAs($userMock, ['*'], 'api');

        $response = $this->getJson(route('api.user-management.mobile.staff.index'));

        $response->assertForbidden();
    }

    /** @test */
    public function normal_staff_can_not_see_index()
    {
        $userMock = $this->mockUser(
            'user.1@mailinator.com',
            'User',
            'user-id-789',
            [] // No permissions
        );

        Passport::actingAs($userMock, ['*'], 'api');

        $response = $this->getJson(route('api.user-management.mobile.staff.index'));

        $response->assertForbidden();
    }

    /** @test */
    public function guest_can_not_see_index()
    {
        $response = $this->getJson(route('api.user-management.mobile.staff.index'));

        $response->assertUnauthorized();
    }
}
