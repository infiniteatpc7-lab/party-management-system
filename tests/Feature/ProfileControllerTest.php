<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ProfileControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_profile_page(): void
    {
        $admin = User::factory()->admin()->create([
            'name' => 'Super Admin',
            'email' => 'infiniteatpc7@gmail.com',
        ]);

        $response = $this->actingAs($admin)->get('/profile');

        $response->assertStatus(200);
        $response->assertSee('Admin Profile');
        $response->assertSee('infiniteatpc7@gmail.com');
        $response->assertSee('Super Admin');
    }

    public function test_unauthenticated_user_cannot_access_profile_page(): void
    {
        $response = $this->get('/profile');

        $response->assertRedirect('/login');
    }

    public function test_admin_can_update_name_and_email(): void
    {
        $admin = User::factory()->admin()->create([
            'name' => 'Super Admin',
            'email' => 'infiniteatpc7@gmail.com',
        ]);

        $response = $this->actingAs($admin)->put('/profile', [
            'name' => 'Updated Admin',
            'email' => 'updatedadmin@gmail.com',
        ]);

        $response->assertRedirect('/profile');
        $response->assertSessionHas('toast_success');

        $admin->refresh();
        $this->assertSame('Updated Admin', $admin->name);
        $this->assertSame('updatedadmin@gmail.com', $admin->email);
    }

    public function test_admin_cannot_use_email_taken_by_another_user(): void
    {
        User::factory()->create([
            'email' => 'otheruser@example.com',
        ]);

        $admin = User::factory()->admin()->create([
            'email' => 'infiniteatpc7@gmail.com',
        ]);

        $response = $this->actingAs($admin)->put('/profile', [
            'name' => 'Super Admin',
            'email' => 'otheruser@example.com',
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_admin_can_update_password_with_valid_current_password(): void
    {
        $admin = User::factory()->admin()->create([
            'password' => Hash::make('Admin@123#'),
        ]);

        $response = $this->actingAs($admin)->put('/profile', [
            'name' => $admin->name,
            'email' => $admin->email,
            'current_password' => 'Admin@123#',
            'password' => 'NewSecurePass@2026',
            'password_confirmation' => 'NewSecurePass@2026',
        ]);

        $response->assertRedirect('/profile');
        $response->assertSessionHas('toast_success');

        $admin->refresh();
        $this->assertTrue(Hash::check('NewSecurePass@2026', $admin->password));
    }

    public function test_admin_cannot_update_password_with_invalid_current_password(): void
    {
        $admin = User::factory()->admin()->create([
            'password' => Hash::make('Admin@123#'),
        ]);

        $response = $this->actingAs($admin)->put('/profile', [
            'name' => $admin->name,
            'email' => $admin->email,
            'current_password' => 'WrongCurrentPassword',
            'password' => 'NewSecurePass@2026',
            'password_confirmation' => 'NewSecurePass@2026',
        ]);

        $response->assertSessionHasErrors('current_password');

        $admin->refresh();
        $this->assertTrue(Hash::check('Admin@123#', $admin->password));
    }

    public function test_admin_cannot_update_password_without_current_password(): void
    {
        $admin = User::factory()->admin()->create([
            'password' => Hash::make('Admin@123#'),
        ]);

        $response = $this->actingAs($admin)->put('/profile', [
            'name' => $admin->name,
            'email' => $admin->email,
            'password' => 'NewSecurePass@2026',
            'password_confirmation' => 'NewSecurePass@2026',
        ]);

        $response->assertSessionHasErrors('current_password');
    }
}
