<?php

namespace Modules\Mobile\Tests\Feature\UserManagement\Superadmin;

use Mockery;
use Carbon\Carbon;
use Laravel\Passport\Passport;
use Tests\TestCase;
use Tests\Feature\Components\MockAuthHelper;
use Modules\Authentication\Http\Repositories\Contracts\SuperadminContract;

class SuperadminCRUDTest extends TestCase
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
    public function superadmin_can_store_new_superadmin()
    {
        $userMock = $this->mockUser(
            'superadmin@mailinator.com',
            'Superadmin',
            'user-id-123',
            ['api.user-management.mobile.superadmin.store']
        );

        // Mock SuperadminRepository
        $newSuperadminMock = Mockery::mock(\App\Models\User::class)->makePartial();
        $newSuperadminMock->id = 'superadmin-id-789';

        $superadminRepositoryMock = Mockery::mock(SuperadminContract::class);
        $superadminRepositoryMock->shouldReceive('store')
            ->once()
            ->andReturn($newSuperadminMock);

        $this->app->instance(SuperadminContract::class, $superadminRepositoryMock);

        // Mock DB::transaction for controller
        \Illuminate\Support\Facades\DB::shouldReceive('transaction')
            ->andReturnUsing(function ($callback) {
                return $callback();
            });

        Passport::actingAs($userMock, ['*'], 'api');

        $response = $this->postJson(route('api.user-management.mobile.superadmin.store'), [
            'name' => 'My Name',
            'email' => 'my.name.'.random_str(10).'@mailinator.com',
            'password' => '12345',
        ]);

        $response->assertOk();
    }

    /** @test */
    public function staff_can_not_store_new_superadmin()
    {
        $userMock = $this->mockUser(
            'staff@mailinator.com',
            'Staff',
            'user-id-456',
            [] // No permissions
        );

        Passport::actingAs($userMock, ['*'], 'api');

        $response = $this->postJson(route('api.user-management.mobile.superadmin.store'), [
            'name' => 'My Name',
            'email' => 'my.name.'.random_str(10).'@mailinator.com',
            'password' => '12345',
        ]);

        $response->assertForbidden();
    }

    /** @test */
    public function normal_superadmin_can_not_store_new_superadmin()
    {
        $userMock = $this->mockUser(
            'user.1@mailinator.com',
            'User',
            'user-id-789',
            [] // No permissions
        );

        Passport::actingAs($userMock, ['*'], 'api');

        $response = $this->postJson(route('api.user-management.mobile.superadmin.store'), [
            'name' => 'My Name',
            'email' => 'my.name.'.Carbon::now()->format('hms').'@mailinator.com',
            'password' => '12345',
        ]);

        $response->assertForbidden();
    }

    /** @test */
    public function guest_can_not_store_new_superadmin()
    {
        $response = $this->postJson(route('api.user-management.mobile.superadmin.store'), [
            'name' => 'My Name',
            'email' => 'my.name.'.Carbon::now()->format('hms').'@mailinator.com',
            'password' => '12345',
        ]);

        $response->assertUnauthorized();
    }

    /** @test */
    public function superadmin_can_update_new_superadmin()
    {
        $userMock = $this->mockUser(
            'superadmin@mailinator.com',
            'Superadmin',
            'user-id-123',
            ['api.user-management.mobile.superadmin.update']
        );

        // Mock SuperadminRepository
        $existingSuperadminMock = Mockery::mock(\App\Models\User::class)->makePartial();
        $existingSuperadminMock->id = 'superadmin-id-999';
        $existingSuperadminMock->email = 'existing@mailinator.com';
        $existingSuperadminMock->shouldReceive('toArray')->andReturn([
            'id' => 'superadmin-id-999',
            'email' => 'existing@mailinator.com',
        ]);

        $superadminRepositoryMock = Mockery::mock(SuperadminContract::class);
        $superadminRepositoryMock->shouldReceive('find')
            ->with('superadmin-id-999')
            ->andReturn($existingSuperadminMock);

        $superadminRepositoryMock->shouldReceive('validateUserRole')
            ->with(Mockery::type(\App\Models\User::class))
            ->andReturn(true);

        $superadminRepositoryMock->shouldReceive('update')
            ->once()
            ->andReturn(true);

        $this->app->instance(SuperadminContract::class, $superadminRepositoryMock);

        // Mock DB::transaction for controller
        \Illuminate\Support\Facades\DB::shouldReceive('transaction')
            ->andReturnUsing(function ($callback) {
                return $callback();
            });

        Passport::actingAs($userMock, ['*'], 'api');

        $response = $this->putJson(route('api.user-management.mobile.superadmin.update', 'superadmin-id-999'), [
            'name' => 'My Name',
            'email' => 'my.name.'.random_str(10).'@mailinator.com',
            'password' => '12345',
        ]);

        $response->assertOk();
    }

    /** @test */
    public function staff_can_not_update_new_superadmin()
    {
        $userMock = $this->mockUser(
            'staff@mailinator.com',
            'Staff',
            'user-id-456',
            [] // No permissions
        );

        Passport::actingAs($userMock, ['*'], 'api');

        $response = $this->putJson(route('api.user-management.mobile.superadmin.update', 1), [
            'name' => 'My Name',
            'email' => 'my.name.'.random_str(10).'@mailinator.com',
            'password' => '12345',
        ]);

        $response->assertForbidden();
    }

    /** @test */
    public function normal_superadmin_can_not_update_new_superadmin()
    {
        $userMock = $this->mockUser(
            'user.1@mailinator.com',
            'User',
            'user-id-789',
            [] // No permissions
        );

        Passport::actingAs($userMock, ['*'], 'api');

        $response = $this->putJson(route('api.user-management.mobile.superadmin.update', 1), [
            'name' => 'My Name',
            'email' => 'my.name.'.Carbon::now()->format('hms').'@mailinator.com',
            'password' => '12345',
        ]);

        $response->assertForbidden();
    }

    /** @test */
    public function guest_can_not_update_new_superadmin()
    {
        $response = $this->putJson(route('api.user-management.mobile.superadmin.update', 1), [
            'name' => 'My Name',
            'email' => 'my.name.'.Carbon::now()->format('hms').'@mailinator.com',
            'password' => '12345',
        ]);

        $response->assertUnauthorized();
    }

    /** @test */
    public function superadmin_can_destroy_new_superadmin()
    {
        $userMock = $this->mockUser(
            'superadmin@mailinator.com',
            'Superadmin',
            'user-id-123',
            ['api.user-management.mobile.superadmin.destroy']
        );

        // Mock SuperadminRepository
        $existingSuperadminMock = Mockery::mock(\App\Models\User::class)->makePartial();
        $existingSuperadminMock->id = 'superadmin-id-999';
        $existingSuperadminMock->email = 'existing@mailinator.com';

        $superadminRepositoryMock = Mockery::mock(SuperadminContract::class);
        $superadminRepositoryMock->shouldReceive('find')
            ->with('superadmin-id-999')
            ->andReturn($existingSuperadminMock);

        $superadminRepositoryMock->shouldReceive('delete')
            ->once()
            ->andReturn(true);

        $this->app->instance(SuperadminContract::class, $superadminRepositoryMock);

        // Mock DB::transaction for controller
        \Illuminate\Support\Facades\DB::shouldReceive('transaction')
            ->andReturnUsing(function ($callback) {
                return $callback();
            });

        Passport::actingAs($userMock, ['*'], 'api');

        $response = $this->deleteJson(route('api.user-management.mobile.superadmin.destroy', 'superadmin-id-999'));

        $response->assertOk();
    }

    /** @test */
    public function staff_can_not_destroy_new_superadmin()
    {
        $userMock = $this->mockUser(
            'staff@mailinator.com',
            'Staff',
            'user-id-456',
            [] // No permissions
        );

        Passport::actingAs($userMock, ['*'], 'api');

        $response = $this->deleteJson(route('api.user-management.mobile.superadmin.destroy', 1));

        $response->assertForbidden();
    }

    /** @test */
    public function normal_superadmin_can_not_destroy_new_superadmin()
    {
        $userMock = $this->mockUser(
            'user.1@mailinator.com',
            'User',
            'user-id-789',
            [] // No permissions
        );

        Passport::actingAs($userMock, ['*'], 'api');

        $response = $this->deleteJson(route('api.user-management.mobile.superadmin.destroy', 1));

        $response->assertForbidden();
    }

    /** @test */
    public function guest_can_not_destroy_new_superadmin()
    {
        $response = $this->deleteJson(route('api.user-management.mobile.superadmin.destroy', 1));

        $response->assertUnauthorized();
    }

    /** @test */
    public function superadmin_can_see_index()
    {
        $userMock = $this->mockUser(
            'superadmin@mailinator.com',
            'Superadmin',
            'user-id-123',
            ['api.user-management.mobile.superadmin.index']
        );

        // Mock SuperadminRepository
        $superadminCollection = collect([
            Mockery::mock(\App\Models\User::class)->makePartial(),
        ]);

        $superadminRepositoryMock = Mockery::mock(SuperadminContract::class);
        $superadminRepositoryMock->shouldReceive('paginated')
            ->once()
            ->andReturn($superadminCollection);

        $this->app->instance(SuperadminContract::class, $superadminRepositoryMock);

        Passport::actingAs($userMock, ['*'], 'api');

        $response = $this->getJson(route('api.user-management.mobile.superadmin.index'));

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

        $response = $this->getJson(route('api.user-management.mobile.superadmin.index'));

        $response->assertForbidden();
    }

    /** @test */
    public function normal_superadmin_can_not_see_index()
    {
        $userMock = $this->mockUser(
            'user.1@mailinator.com',
            'User',
            'user-id-789',
            [] // No permissions
        );

        Passport::actingAs($userMock, ['*'], 'api');

        $response = $this->getJson(route('api.user-management.mobile.superadmin.index'));

        $response->assertForbidden();
    }

    /** @test */
    public function guest_can_not_see_index()
    {
        $response = $this->getJson(route('api.user-management.mobile.superadmin.index'));

        $response->assertUnauthorized();
    }
}
