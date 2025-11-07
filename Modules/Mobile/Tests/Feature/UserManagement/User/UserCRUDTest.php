<?php

namespace Modules\Mobile\Tests\Feature\UserManagement\User;

use Mockery;
use Carbon\Carbon;
use Laravel\Passport\Passport;
use Tests\TestCase;
use Tests\Feature\Components\MockAuthHelper;
use Modules\Authentication\Http\Repositories\Contracts\UserContract;

class UserCRUDTest extends TestCase
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
    public function superadmin_can_store_new_user()
    {
        $userMock = $this->mockUser(
            'superadmin@mailinator.com',
            'Superadmin',
            'user-id-123',
            ['api.user-management.mobile.user.store']
        );

        // Mock UserRepository
        $newUserMock = Mockery::mock(\App\Models\User::class)->makePartial();
        $newUserMock->id = 'user-id-789';

        $userRepositoryMock = Mockery::mock(UserContract::class);
        $userRepositoryMock->shouldReceive('store')
            ->once()
            ->andReturn($newUserMock);

        $this->app->instance(UserContract::class, $userRepositoryMock);

        // Mock DB::transaction for controller
        \Illuminate\Support\Facades\DB::shouldReceive('transaction')
            ->andReturnUsing(function ($callback) {
                return $callback();
            });

        Passport::actingAs($userMock, ['*'], 'api');

        $response = $this->postJson(route('api.user-management.mobile.user.store'), [
            'name' => 'My Name',
            'email' => 'my.name.'.random_str(10).'@mailinator.com',
            'password' => '12345',
        ]);

        $response->assertOk();
    }

    /** @test */
    public function staff_can_store_new_user()
    {
        $userMock = $this->mockUser(
            'staff@mailinator.com',
            'Staff',
            'staff-user-id-456',
            ['api.user-management.mobile.user.store']
        );

        // Mock UserRepository
        $newUserMock = Mockery::mock(\App\Models\User::class)->makePartial();
        $newUserMock->id = 'user-id-789';

        $userRepositoryMock = Mockery::mock(UserContract::class);
        $userRepositoryMock->shouldReceive('store')
            ->once()
            ->andReturn($newUserMock);

        $this->app->instance(UserContract::class, $userRepositoryMock);

        // Mock DB::transaction for controller
        \Illuminate\Support\Facades\DB::shouldReceive('transaction')
            ->andReturnUsing(function ($callback) {
                return $callback();
            });

        Passport::actingAs($userMock, ['*'], 'api');

        $response = $this->postJson(route('api.user-management.mobile.user.store'), [
            'name' => 'My Name',
            'email' => 'my.name.'.random_str(10).'@mailinator.com',
            'password' => '12345',
        ]);

        $response->assertOk();
    }

    /** @test */
    public function normal_user_can_not_store_new_user()
    {
        $userMock = $this->mockUser(
            'user.1@mailinator.com',
            'User',
            'user-id-789',
            [] // No permissions
        );

        Passport::actingAs($userMock, ['*'], 'api');

        $response = $this->postJson(route('api.user-management.mobile.user.store'), [
            'name' => 'My Name',
            'email' => 'my.name.'.Carbon::now()->format('hms').'@mailinator.com',
            'password' => '12345',
        ]);

        $response->assertForbidden();
    }

    /** @test */
    public function guest_can_not_store_new_user()
    {
        $response = $this->postJson(route('api.user-management.mobile.user.store'), [
            'name' => 'My Name',
            'email' => 'my.name.'.Carbon::now()->format('hms').'@mailinator.com',
            'password' => '12345',
        ]);

        $response->assertUnauthorized();
    }

    /** @test */
    public function superadmin_can_update_new_user()
    {
        $userMock = $this->mockUser(
            'superadmin@mailinator.com',
            'Superadmin',
            'user-id-123',
            ['api.user-management.mobile.user.update']
        );

        // Mock UserRepository
        $existingUserMock = Mockery::mock(\App\Models\User::class)->makePartial();
        $existingUserMock->id = 'user-id-999';
        $existingUserMock->email = 'existing@mailinator.com';
        $existingUserMock->shouldReceive('toArray')->andReturn([
            'id' => 'user-id-999',
            'email' => 'existing@mailinator.com',
        ]);

        $userRepositoryMock = Mockery::mock(UserContract::class);
        $userRepositoryMock->shouldReceive('find')
            ->with('user-id-999')
            ->andReturn($existingUserMock);

        $userRepositoryMock->shouldReceive('validateUserRole')
            ->with(Mockery::type(\App\Models\User::class))
            ->andReturn(true);

        $userRepositoryMock->shouldReceive('update')
            ->once()
            ->andReturn(true);

        $this->app->instance(UserContract::class, $userRepositoryMock);

        // Mock DB::transaction for controller
        \Illuminate\Support\Facades\DB::shouldReceive('transaction')
            ->andReturnUsing(function ($callback) {
                return $callback();
            });

        Passport::actingAs($userMock, ['*'], 'api');

        $response = $this->putJson(route('api.user-management.mobile.user.update', 'user-id-999'), [
            'name' => 'My Name',
            'email' => 'my.name.'.random_str(10).'@mailinator.com',
            'password' => '12345',
        ]);

        $response->assertOk();
    }

    /** @test */
    public function staff_can_update_new_user()
    {
        $userMock = $this->mockUser(
            'staff@mailinator.com',
            'Staff',
            'staff-user-id-456',
            ['api.user-management.mobile.user.update']
        );

        // Mock UserRepository
        $existingUserMock = Mockery::mock(\App\Models\User::class)->makePartial();
        $existingUserMock->id = 'user-id-999';
        $existingUserMock->email = 'existing@mailinator.com';
        $existingUserMock->shouldReceive('toArray')->andReturn([
            'id' => 'user-id-999',
            'email' => 'existing@mailinator.com',
        ]);

        $userRepositoryMock = Mockery::mock(UserContract::class);
        $userRepositoryMock->shouldReceive('find')
            ->with('user-id-999')
            ->andReturn($existingUserMock);

        $userRepositoryMock->shouldReceive('validateUserRole')
            ->with(Mockery::type(\App\Models\User::class))
            ->andReturn(true);

        $userRepositoryMock->shouldReceive('update')
            ->once()
            ->andReturn(true);

        $this->app->instance(UserContract::class, $userRepositoryMock);

        // Mock DB::transaction for controller
        \Illuminate\Support\Facades\DB::shouldReceive('transaction')
            ->andReturnUsing(function ($callback) {
                return $callback();
            });

        Passport::actingAs($userMock, ['*'], 'api');

        $response = $this->putJson(route('api.user-management.mobile.user.update', 'user-id-999'), [
            'name' => 'My Name',
            'email' => 'my.name.'.random_str(10).'@mailinator.com',
            'password' => '12345',
        ]);

        $response->assertOk();
    }

    /** @test */
    public function normal_user_can_not_update_new_user()
    {
        $userMock = $this->mockUser(
            'user.1@mailinator.com',
            'User',
            'user-id-789',
            [] // No permissions
        );

        Passport::actingAs($userMock, ['*'], 'api');

        $response = $this->putJson(route('api.user-management.mobile.user.update', 1), [
            'name' => 'My Name',
            'email' => 'my.name.'.Carbon::now()->format('hms').'@mailinator.com',
            'password' => '12345',
        ]);

        $response->assertForbidden();
    }

    /** @test */
    public function guest_can_not_update_new_user()
    {
        $response = $this->putJson(route('api.user-management.mobile.user.update', 1), [
            'name' => 'My Name',
            'email' => 'my.name.'.Carbon::now()->format('hms').'@mailinator.com',
            'password' => '12345',
        ]);

        $response->assertUnauthorized();
    }

    /** @test */
    public function superadmin_can_destroy_new_user()
    {
        $userMock = $this->mockUser(
            'superadmin@mailinator.com',
            'Superadmin',
            'user-id-123',
            ['api.user-management.mobile.user.destroy']
        );

        // Mock UserRepository
        $existingUserMock = Mockery::mock(\App\Models\User::class)->makePartial();
        $existingUserMock->id = 'user-id-999';
        $existingUserMock->email = 'existing@mailinator.com';

        $userRepositoryMock = Mockery::mock(UserContract::class);
        $userRepositoryMock->shouldReceive('find')
            ->with('user-id-999')
            ->andReturn($existingUserMock);

        $userRepositoryMock->shouldReceive('delete')
            ->once()
            ->andReturn(true);

        $this->app->instance(UserContract::class, $userRepositoryMock);

        // Mock DB::transaction for controller
        \Illuminate\Support\Facades\DB::shouldReceive('transaction')
            ->andReturnUsing(function ($callback) {
                return $callback();
            });

        Passport::actingAs($userMock, ['*'], 'api');

        $response = $this->deleteJson(route('api.user-management.mobile.user.destroy', 'user-id-999'));

        $response->assertOk();
    }

    /** @test */
    public function staff_can_destroy_new_user()
    {
        $userMock = $this->mockUser(
            'staff@mailinator.com',
            'Staff',
            'staff-user-id-456',
            ['api.user-management.mobile.user.destroy']
        );

        // Mock UserRepository
        $existingUserMock = Mockery::mock(\App\Models\User::class)->makePartial();
        $existingUserMock->id = 'user-id-999';
        $existingUserMock->email = 'existing@mailinator.com';

        $userRepositoryMock = Mockery::mock(UserContract::class);
        $userRepositoryMock->shouldReceive('find')
            ->with('user-id-999')
            ->andReturn($existingUserMock);

        $userRepositoryMock->shouldReceive('delete')
            ->once()
            ->andReturn(true);

        $this->app->instance(UserContract::class, $userRepositoryMock);

        // Mock DB::transaction for controller
        \Illuminate\Support\Facades\DB::shouldReceive('transaction')
            ->andReturnUsing(function ($callback) {
                return $callback();
            });

        Passport::actingAs($userMock, ['*'], 'api');

        $response = $this->deleteJson(route('api.user-management.mobile.user.destroy', 'user-id-999'));

        $response->assertOk();
    }

    /** @test */
    public function normal_user_can_not_destroy_new_user()
    {
        $userMock = $this->mockUser(
            'user.1@mailinator.com',
            'User',
            'user-id-789',
            [] // No permissions
        );

        Passport::actingAs($userMock, ['*'], 'api');

        $response = $this->deleteJson(route('api.user-management.mobile.user.destroy', 1));

        $response->assertForbidden();
    }

    /** @test */
    public function guest_can_not_destroy_new_user()
    {
        $response = $this->deleteJson(route('api.user-management.mobile.user.destroy', 1));

        $response->assertUnauthorized();
    }

    /** @test */
    public function superadmin_can_see_index()
    {
        $userMock = $this->mockUser(
            'superadmin@mailinator.com',
            'Superadmin',
            'user-id-123',
            ['api.user-management.mobile.user.index']
        );

        // Mock UserRepository
        $userCollection = collect([
            Mockery::mock(\App\Models\User::class)->makePartial(),
        ]);

        $userRepositoryMock = Mockery::mock(UserContract::class);
        $userRepositoryMock->shouldReceive('paginated')
            ->once()
            ->andReturn($userCollection);

        $this->app->instance(UserContract::class, $userRepositoryMock);

        Passport::actingAs($userMock, ['*'], 'api');

        $response = $this->getJson(route('api.user-management.mobile.user.index'));

        $response->assertOk();
    }

    /** @test */
    public function staff_can_see_index()
    {
        $userMock = $this->mockUser(
            'staff@mailinator.com',
            'Staff',
            'staff-user-id-456',
            ['api.user-management.mobile.user.index']
        );

        // Mock UserRepository
        $userCollection = collect([
            Mockery::mock(\App\Models\User::class)->makePartial(),
        ]);

        $userRepositoryMock = Mockery::mock(UserContract::class);
        $userRepositoryMock->shouldReceive('paginated')
            ->once()
            ->andReturn($userCollection);

        $this->app->instance(UserContract::class, $userRepositoryMock);

        Passport::actingAs($userMock, ['*'], 'api');

        $response = $this->getJson(route('api.user-management.mobile.user.index'));

        $response->assertOk();
    }

    /** @test */
    public function normal_user_can_not_see_index()
    {
        $userMock = $this->mockUser(
            'user.1@mailinator.com',
            'User',
            'user-id-789',
            [] // No permissions
        );

        Passport::actingAs($userMock, ['*'], 'api');

        $response = $this->getJson(route('api.user-management.mobile.user.index'));

        $response->assertForbidden();
    }

    /** @test */
    public function guest_can_not_see_index()
    {
        $response = $this->getJson(route('api.user-management.mobile.user.index'));

        $response->assertUnauthorized();
    }
}
