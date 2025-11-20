<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('non-admin user cannot access admin dashboard', function () {
    $user = User::factory()->create([
        'is_admin' => false,
    ]);

    $response = $this->actingAs($user)->get('/admin');

    $response->assertForbidden();
});

test('admin user can access admin dashboard', function () {
    $admin = User::factory()->create([
        'is_admin' => true,
    ]);

    $response = $this->actingAs($admin)->get('/admin');

    $response->assertOk();
    $response->assertSee('Dashboard');
});

test('guest cannot access admin dashboard', function () {
    $response = $this->get('/admin');

    $response->assertForbidden();
});

test('admin can access users management page', function () {
    $admin = User::factory()->create([
        'is_admin' => true,
    ]);

    $response = $this->actingAs($admin)->get('/admin/users');

    $response->assertOk();
});

test('non-admin cannot access users management page', function () {
    $user = User::factory()->create([
        'is_admin' => false,
    ]);

    $response = $this->actingAs($user)->get('/admin/users');

    $response->assertForbidden();
});

test('admin can access logs page', function () {
    $admin = User::factory()->create([
        'is_admin' => true,
    ]);

    $response = $this->actingAs($admin)->get('/admin/logs');

    $response->assertOk();
});

test('non-admin cannot access logs page', function () {
    $user = User::factory()->create([
        'is_admin' => false,
    ]);

    $response = $this->actingAs($user)->get('/admin/logs');

    $response->assertForbidden();
});
