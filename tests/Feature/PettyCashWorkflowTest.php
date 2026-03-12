<?php

use App\Models\Category;
use App\Models\User;
use App\Models\PettyCash;
use App\Notifications\PettyCashNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    // Setup Roles and Permissions
    $permission1 = Permission::create(['name' => 'Petty Cash Update Status', 'guard_name' => 'api']);
    $permission2 = Permission::create(['name' => 'Petty Cash Update Payment Status', 'guard_name' => 'api']);

    $role = Role::create(['name' => 'Admin', 'guard_name' => 'api']);
    $role->givePermissionTo([$permission1, $permission2]);
});

it('can process the entire petty cash workflow', function () {
    Notification::fake();

    // 1. Setup Categorization
    $category = Category::create([
        'name' => 'Travel',
        'slug' => 'travel',
        'description' => 'Travel expenses'
    ]);

    // 2. Setup Users for Notifications
    $approver = User::factory()->create([
        'notify_petty_cash_request' => true,
    ]);
    $approver->assignRole('Admin');

    $payer = User::factory()->create([
        'notify_petty_cash_payment' => true,
    ]);
    $payer->assignRole('Admin');

    // 3. Create Petty Cash (Public)
    $payload = [
        'full_name' => 'Test User',
        'branch_location' => 'Colombo',
        'amount' => 1000,
        'category_id' => $category->id,
        'type' => 'new_purchase',
        'date_needed' => now()->addDays(2)->format('Y-m-d'),
    ];

    $response = $this->postJson('/api/v1/petty-cashes', $payload);

    $response->assertStatus(201);
    $this->assertDatabaseHas('petty_cashes', [
        'full_name' => 'Test User',
        'amount' => 1000,
        'status' => 'pending'
    ]);

    // Verify Approver received notification
    Notification::assertSentTo($approver, PettyCashNotification::class, function ($notification) use ($approver) {
        return $notification->toArray($approver)['type'] === 'created';
    });

    $pettyCash = PettyCash::first();

    // 4. Approve Petty Cash (as Admin)
    $this->actingAs($approver, 'api');
    $response = $this->patchJson("/api/v1/petty-cashes/{$pettyCash->id}/status", [
        'status' => 'approved'
    ]);

    $response->assertStatus(200);
    expect($pettyCash->fresh()->status)->toBe('approved');

    // Verify Payer received notification
    Notification::assertSentTo($payer, PettyCashNotification::class, function ($notification) use ($payer) {
        return $notification->toArray($payer)['type'] === 'ready_for_payment';
    });

    // 5. Pay Petty Cash (as Admin)
    $this->actingAs($payer, 'api');
    $response = $this->patchJson("/api/v1/petty-cashes/{$pettyCash->id}/payment-status", [
        'payment_status' => 'paid'
    ]);

    $response->assertStatus(200);
    expect($pettyCash->fresh()->payment_status)->toBe('paid');

    // Verify Requester/Reviewer notification (as implemented)
    Notification::assertSentTo($approver, PettyCashNotification::class, function ($notification) use ($approver) {
        return $notification->toArray($approver)['type'] === 'paid';
    });
});
