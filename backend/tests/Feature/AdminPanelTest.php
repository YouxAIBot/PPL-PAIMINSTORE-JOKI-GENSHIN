<?php

namespace Tests\Feature;

use App\Models\JokiCategory;
use App\Models\JokiPackage;
use App\Models\Order;
use App\Models\OrderGameAccount;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class AdminPanelTest extends TestCase
{
    use RefreshDatabase;

    private function makeAdmin(): User
    {
        return User::create([
            'name' => 'Admin Panel',
            'email' => 'admin@test.dev',
            'password' => 'secret123',
            'role' => 'admin',
            'status' => 'active',
        ]);
    }

    private function makeOrder(string $status = Order::PENDING_PAYMENT): array
    {
        $customer = User::create([
            'name' => 'Budi', 'email' => 'budi@test.dev', 'password' => 'secret123', 'role' => 'user',
        ]);
        $category = JokiCategory::create(['name' => 'Daily Login', 'slug' => 'daily-login']);
        $package = JokiPackage::create([
            'category_id' => $category->id,
            'name' => 'Paket Daily',
            'price' => 50000,
            'estimated_hours' => 6,
        ]);

        $order = Order::create([
            'order_no' => 'ORD-TEST-'.$status.'-'.rand(1, 99999),
            'user_id' => $customer->id,
            'package_id' => $package->id,
            'status' => $status,
            'price_snapshot' => 50000,
            'total_amount' => 50000,
            'est_hours_snapshot' => 6,
            'paid_at' => $status === Order::PENDING_PAYMENT ? null : now(),
        ]);

        $payment = Payment::create([
            'order_id' => $order->id,
            'gateway_ref' => 'TEST-'.$order->id,
            'method' => 'manual',
            'amount' => 50000,
            'status' => $status === Order::PENDING_PAYMENT ? Payment::PENDING : Payment::PAID,
            'paid_at' => $order->paid_at,
        ]);

        return [$order, $payment, $customer];
    }

    public function test_guest_redirected_to_login(): void
    {
        $this->get(route('admin.dashboard'))->assertRedirect(route('admin.login'));
    }

    public function test_non_admin_cannot_login(): void
    {
        $user = User::create([
            'name' => 'Bukan Admin', 'email' => 'bukan@test.dev', 'password' => 'secret123', 'role' => 'user',
        ]);

        $this->post(route('admin.login'), ['email' => 'bukan@test.dev', 'password' => 'secret123'])
            ->assertSessionHasErrors();

        $this->assertGuest();
    }

    public function test_admin_login_and_dashboard(): void
    {
        $admin = $this->makeAdmin();

        $this->post(route('admin.login'), ['email' => 'admin@test.dev', 'password' => 'secret123'])
            ->assertRedirect(route('admin.dashboard'));

        $this->actingAs($admin)->get(route('admin.dashboard'))->assertOk()->assertSee('Dashboard');
    }

    public function test_category_crud(): void
    {
        $admin = $this->makeAdmin();
        $this->actingAs($admin);

        $this->post(route('admin.categories.store'), ['name' => 'Quest', 'description' => 'q'])
            ->assertRedirect(route('admin.categories.index'));

        $category = JokiCategory::where('name', 'Quest')->firstOrFail();

        $this->put(route('admin.categories.update', $category), ['name' => 'Quest v2', 'is_active' => '1'])
            ->assertRedirect(route('admin.categories.index'));

        $this->assertSame('Quest v2', $category->fresh()->name);

        $this->delete(route('admin.categories.destroy', $category))->assertRedirect(route('admin.categories.index'));
        $this->assertDatabaseMissing('joki_categories', ['id' => $category->id]);
    }

    public function test_package_crud(): void
    {
        $admin = $this->makeAdmin();
        $category = JokiCategory::create(['name' => 'Domain', 'slug' => 'domain']);

        $this->actingAs($admin)->post(route('admin.packages.store'), [
            'category_id' => $category->id,
            'name' => 'Paket Domain',
            'price' => 75000,
            'estimated_hours' => 8,
        ])->assertRedirect(route('admin.packages.index'));

        $package = JokiPackage::where('name', 'Paket Domain')->firstOrFail();
        $this->assertSame('75000.00', $package->price);
    }

    public function test_manual_payment_approval_moves_order_to_queue(): void
    {
        $admin = $this->makeAdmin();
        [$order, $payment] = $this->makeOrder();

        $this->actingAs($admin)
            ->post(route('admin.payments.approve', $payment))
            ->assertRedirect();

        $this->assertSame(Payment::PAID, $payment->refresh()->status);
        $this->assertSame(Order::QUEUED, $order->refresh()->status);
        $this->assertNotNull($payment->refresh()->verified_by);
    }

    public function test_refund_partial_and_full(): void
    {
        $admin = $this->makeAdmin();
        [$order, $payment] = $this->makeOrder(Order::IN_PROGRESS);
        $payment->update(['amount' => 100000, 'refunded_amount' => 0]);

        $this->actingAs($admin)->post(route('admin.payments.refund', $payment), ['amount' => 30000])->assertRedirect();
        $this->assertSame('30000.00', $payment->refresh()->refunded_amount);
        $this->assertSame(Payment::PAID, $payment->status);

        $this->actingAs($admin)->post(route('admin.payments.refund', $payment), ['amount' => 70000])->assertRedirect();
        $this->assertSame('100000.00', $payment->refresh()->refunded_amount);
        $this->assertSame(Payment::REFUNDED, $payment->refresh()->status);
    }

    public function test_assign_booster_and_status_flow(): void
    {
        $admin = $this->makeAdmin();
        $booster = User::create([
            'name' => 'Booster', 'email' => 'booster@test.dev', 'password' => 'secret123', 'role' => 'booster',
        ]);
        [$order] = $this->makeOrder(Order::QUEUED);

        $this->actingAs($admin)
            ->post(route('admin.orders.assign', $order), ['booster_id' => $booster->id])
            ->assertRedirect();
        $this->assertSame($booster->id, $order->refresh()->booster_id);

        $this->post(route('admin.orders.status', $order), ['status' => Order::IN_PROGRESS])->assertRedirect();
        $this->assertNotNull($order->refresh()->started_at);

        $this->post(route('admin.orders.status', $order), ['status' => Order::COMPLETED])->assertRedirect();
        $completed = $order->refresh();
        $this->assertSame(Order::COMPLETED, $completed->status);
        $this->assertNotNull($completed->completed_at);
    }

    public function test_cancel_requires_reason(): void
    {
        $admin = $this->makeAdmin();
        [$order] = $this->makeOrder(Order::QUEUED);

        $this->actingAs($admin)->post(route('admin.orders.cancel', $order), ['reason' => 'stok habis'])->assertRedirect();
        $this->assertSame('stok habis', $order->refresh()->cancel_reason);
    }

    public function test_reports_page_and_csv_export(): void
    {
        $admin = $this->makeAdmin();
        [$order] = $this->makeOrder(Order::COMPLETED);

        $this->actingAs($admin)->get(route('admin.reports.index'))->assertOk()->assertSee('Laporan');

        $response = $this->actingAs($admin)->get(route('admin.reports.export-csv'));
        $response->assertOk()->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
        $this->assertStringContainsString('Laporan Paiminstore', $response->getContent());
    }

    public function test_admin_can_create_manual_order(): void
    {
        $admin = $this->makeAdmin();
        $customer = User::create([
            'name' => 'Pelanggan Baru', 'email' => 'pelanggan@test.dev', 'password' => 'secret123', 'role' => 'user',
        ]);
        $category = JokiCategory::create(['name' => 'Domain', 'slug' => 'domain']);
        $package = JokiPackage::create([
            'category_id' => $category->id, 'name' => 'Paket Domain', 'price' => 75000, 'estimated_hours' => 8,
        ]);

        $this->actingAs($admin)->post(route('admin.orders.store'), [
            'user_id' => $customer->id,
            'package_id' => $package->id,
            'uid' => '123456789',
            'server' => 'asia',
            'credential_username' => 'pelanggan@game.test',
            'credential_password' => 'rahasia-pelanggan',
            'status' => Order::QUEUED,
            'queue_priority' => 5,
        ])->assertRedirect();

        $order = Order::where('user_id', $customer->id)->firstOrFail();
        $this->assertSame(Order::QUEUED, $order->status);
        $this->assertSame('75000.00', $order->total_amount);
        $this->assertDatabaseHas('order_game_accounts', ['order_id' => $order->id, 'uid' => '123456789']);
        $this->assertSame(Payment::PAID, $order->payment->status);
    }

    public function test_booster_page_shows_assigned_orders(): void
    {
        $admin = $this->makeAdmin();
        $booster = User::create([
            'name' => 'Rizal', 'email' => 'rizal@test.dev', 'password' => 'secret123', 'role' => 'booster', 'status' => 'active',
        ]);
        [$order] = $this->makeOrder(Order::QUEUED);
        $order->update(['booster_id' => $booster->id]);

        $this->actingAs($admin)
            ->get(route('admin.boosters.index'))
            ->assertOk()
            ->assertSee('Rizal');

        $this->actingAs($admin)
            ->get(route('admin.boosters.show', $booster))
            ->assertOk()
            ->assertSee('Order yang Ditugaskan ke Booster: Rizal')
            ->assertSee($order->order_no);
    }

    public function test_seeded_demo_data_available(): void
    {
        $this->seed();

        $admin = User::where('email', 'owner@paiminstore.test')->firstOrFail();
        $this->assertTrue($admin->isAdmin());

        $this->actingAs($admin)->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Daftar Antrian Aktif');
    }

    public function test_manual_order_credential_is_stored_with_vault(): void
    {
        $admin = $this->makeAdmin();
        $customer = User::create([
            'name' => 'Pelanggan Vault', 'email' => 'vault@test.dev', 'password' => 'secret123', 'role' => 'user',
        ]);
        $category = JokiCategory::create(['name' => 'Quest', 'slug' => 'quest']);
        $package = JokiPackage::create([
            'category_id' => $category->id, 'name' => 'Paket Quest', 'price' => 50000, 'estimated_hours' => 4,
        ]);

        $this->actingAs($admin)->post(route('admin.orders.store'), [
            'user_id' => $customer->id,
            'package_id' => $package->id,
            'uid' => '800000001',
            'server' => 'asia',
            'credential_username' => 'akun@game.test',
            'credential_password' => 'pw-rahasia',
            'status' => Order::PENDING_PAYMENT,
        ])->assertRedirect();

        $order = Order::where('user_id', $customer->id)->firstOrFail();
        $raw = DB::table('order_game_accounts')->where('order_id', $order->id)->value('credential_encrypted');

        $this->assertStringStartsWith('v1:', $raw);
        $this->assertStringNotContainsString('pw-rahasia', $raw);
        $this->assertSame(
            ['username' => 'akun@game.test', 'password' => 'pw-rahasia'],
            OrderGameAccount::find($order->id)->credential_encrypted,
        );

        $this->actingAs($admin)->post(route('admin.orders.credentials', $order))
            ->assertOk()->assertSee('akun@game.test')->assertSee('pw-rahasia');
    }

    public function test_cannot_cancel_completed_order(): void
    {
        $admin = $this->makeAdmin();
        [$order] = $this->makeOrder(Order::COMPLETED);

        $this->actingAs($admin)->post(route('admin.orders.cancel', $order), ['reason' => 'iseng'])
            ->assertRedirect()->assertSessionHasErrors('status');
        $this->assertSame(Order::COMPLETED, $order->refresh()->status);
    }

    public function test_status_dropdown_cancel_does_not_redirect_to_post_route(): void
    {
        $admin = $this->makeAdmin();
        [$order] = $this->makeOrder(Order::QUEUED);

        $this->actingAs($admin)->from(route('admin.orders.show', $order))
            ->post(route('admin.orders.status', $order), ['status' => Order::CANCELLED])
            ->assertRedirect(route('admin.orders.show', $order))
            ->assertSessionHasErrors('status');
        $this->assertSame(Order::QUEUED, $order->refresh()->status);
    }

    public function test_ban_sets_status_banned(): void
    {
        $admin = $this->makeAdmin();
        $target = User::create([
            'name' => 'Nakal', 'email' => 'nakal@test.dev', 'password' => 'secret123', 'role' => 'user',
        ]);

        $this->actingAs($admin)->post(route('admin.users.ban', $target))->assertRedirect();
        $this->assertSame('banned', $target->refresh()->status);
    }
}
