<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Department;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TicketStoreBugTest extends TestCase
{
    use RefreshDatabase;

    private function seedData(): array
    {
        $it = Department::create(['name' => 'Dept A', 'description' => 'TI']);
        $hr = Department::create(['name' => 'Dept B', 'description' => 'HRD']);
        $hw = Category::create(['name' => 'Hardware', 'description' => 'HW', 'department_id' => $it->id]);
        $cuti = Category::create(['name' => 'Cuti', 'description' => 'Cuti', 'department_id' => $hr->id]);

        $user = User::create([
            'name' => 'Andi', 'email' => 'andi@test.test', 'password' => 'password',
            'role' => 'requester', 'email_verified_at' => now(),
        ]);
        $agent = User::create([
            'name' => 'Budi', 'email' => 'budi@test.test', 'password' => 'password',
            'role' => 'agent', 'department_id' => $it->id, 'email_verified_at' => now(),
        ]);

        return compact('it', 'hr', 'hw', 'cuti', 'user', 'agent');
    }

    /** BUG 1: kode tiket meledak setelah 9999 -> kembali ke 0001 (duplikat). */
    public function test_next_ticket_code_overflow_after_9999(): void
    {
        $d = $this->seedData();

        Ticket::create([
            'ticket_code' => 'TKT-'.now()->format('y').'-9999',
            'title' => 'T', 'description' => 'D', 'department_id' => $d['it']->id,
            'category_id' => $d['hw']->id, 'priority' => 'rendah', 'status' => 'baru',
            'requester_id' => $d['user']->id,
        ]);

        // Kode berikutnya harusnya 10000, tapi implementasi menghasilkan 0001 yang sudah dipakai.
        $this->assertSame('TKT-'.now()->format('y').'-10000', Ticket::nextTicketCode());
    }

    /** BUG 2: tiket dibuat namun TIDAK ada di DB -> race condition ticket_code (unique). */
    public function test_duplicate_ticket_code_between_two_requests(): void
    {
        $d = $this->seedData();

        // Simulasi dua request bersamaan: keduanya menghitung nextTicketCode sebelum insert.
        $codeA = Ticket::nextTicketCode();
        $codeB = Ticket::nextTicketCode();

        // nextTicketCode() bersifat deterministik tanpa perubahan state.
        // Ini HANYA menunjukkan bahwa tidak ada penjaga unik di tingkat aplikasi:
        // dua pembuatan tiket bersamaan akan menghitung kode yang sama.
        $this->assertSame($codeA, $codeB, 'nextTicketCode() tidak mengunci/menghindari duplikasi antar-request bersamaan.');
    }

    /** Non-staf TIDAK bisa menyuntik estimated_duration (perilaku benar, guard). */
    public function test_non_staff_cannot_inject_estimated_duration(): void
    {
        $d = $this->seedData();

        $response = $this->actingAs($d['user'])->post(route('tickets.store'), [
            'title' => 'Printer macet',
            'description' => 'Tidak bisa cetak',
            'department_id' => $d['it']->id,
            'category_id' => $d['hw']->id,
            'priority' => 'sedang',
            'estimated_duration' => '1-4h', // harus diabaikan untuk requester
        ]);

        $response->assertRedirect();
        $ticket = Ticket::latest('id')->first();
        $this->assertNull($ticket->estimated_duration);
    }

    /** BUG 4: estimated_duration string kosong dari staf gagal validasi (bukan null). */
    public function test_staff_empty_estimate_is_stored_as_null(): void
    {
        $d = $this->seedData();

        $response = $this->actingAs($d['agent'])->post(route('tickets.store'), [
            'title' => 'Printer macet',
            'description' => 'Tidak bisa cetak',
            'department_id' => $d['it']->id,
            'category_id' => $d['hw']->id,
            'priority' => 'sedang',
            'estimated_duration' => '',
        ]);

        $response->assertRedirect();
        $ticket = Ticket::latest('id')->first();
        $this->assertNull($ticket->estimated_duration);
    }

    /** Estimasi pengerjaan tidak diisi saat pembuatan tiket, walau oleh staf. */
    public function test_staff_cannot_set_estimate_on_create(): void
    {
        $d = $this->seedData();

        $response = $this->actingAs($d['agent'])->post(route('tickets.store'), [
            'title' => 'Printer macet',
            'description' => 'Tidak bisa cetak',
            'department_id' => $d['it']->id,
            'category_id' => $d['hw']->id,
            'priority' => 'sedang',
            'estimated_duration' => '1-4h', // harus diabaikan saat create
        ]);

        $response->assertRedirect();
        $ticket = Ticket::latest('id')->first();
        $this->assertNull($ticket->estimated_duration);
    }

    /** BUG 1: setelah kode mencapai 10000, orderBy string membuat kode berikutnya jadi duplikat 10000. */
    public function test_next_ticket_code_duplicates_after_10000(): void
    {
        $d = $this->seedData();

        // Simulasi tiket ke-9999 dan ke-10000 sudah ada di DB.
        Ticket::create([
            'ticket_code' => 'TKT-'.now()->format('y').'-9999',
            'title' => 'T', 'description' => 'D', 'department_id' => $d['it']->id,
            'category_id' => $d['hw']->id, 'priority' => 'rendah', 'status' => 'baru',
            'requester_id' => $d['user']->id,
        ]);
        Ticket::create([
            'ticket_code' => 'TKT-'.now()->format('y').'-10000',
            'title' => 'T', 'description' => 'D', 'department_id' => $d['it']->id,
            'category_id' => $d['hw']->id, 'priority' => 'rendah', 'status' => 'baru',
            'requester_id' => $d['user']->id,
        ]);

        // orderBy('ticket_code','desc') adalah string sort: 'TKT-26-9999' > 'TKT-26-10000',
        // sehingga "last" = 9999, +1 = 10000 -> menghasilkan kode yang SUDAH ADA (duplikat).
        // Seharusnya menghasilkan TKT-26-10001.
        $this->assertSame('TKT-'.now()->format('y').'-10001', Ticket::nextTicketCode());
    }

    /** BUG 5: kategori dari departemen lain ditolak. */
    public function test_category_of_wrong_department_rejected(): void
    {
        $d = $this->seedData();

        $response = $this->actingAs($d['user'])->post(route('tickets.store'), [
            'title' => 'Ajukan cuti',
            'description' => 'Mohon cuti',
            'department_id' => $d['it']->id,
            'category_id' => $d['cuti']->id, // kategori milik HR
            'priority' => 'sedang',
        ]);

        $response->assertSessionHasErrors('category_id');
        $this->assertSame(0, Ticket::count());
    }
}
