<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Comment;
use App\Models\Department;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $it = Department::create(['name' => 'IT', 'description' => 'Departemen Teknologi Informasi']);
        $hr = Department::create(['name' => 'HR', 'description' => 'Departemen Sumber Daya Manusia']);
        $ga = Department::create(['name' => 'GA', 'description' => 'Departemen General Affairs']);

        $admin = User::create([
            'name' => 'Admin Helpdesk',
            'email' => 'admin@helpdesk.test',
            'password' => 'password',
            'role' => 'admin',
            'department_id' => null,
            'email_verified_at' => now(),
        ]);

        $budi = User::create([
            'name' => 'Budi Santoso',
            'email' => 'budi@helpdesk.test',
            'password' => 'password',
            'role' => 'agent',
            'department_id' => $it->id,
            'email_verified_at' => now(),
        ]);

        $sari = User::create([
            'name' => 'Sari Wulandari',
            'email' => 'sari@helpdesk.test',
            'password' => 'password',
            'role' => 'agent',
            'department_id' => $it->id,
            'email_verified_at' => now(),
        ]);

        $hana = User::create([
            'name' => 'Hana Rahma',
            'email' => 'hr@helpdesk.test',
            'password' => 'password',
            'role' => 'agent',
            'department_id' => $hr->id,
            'email_verified_at' => now(),
        ]);

        $andi = User::create([
            'name' => 'Andi Pratama',
            'email' => 'andi@helpdesk.test',
            'password' => 'password',
            'role' => 'requester',
            'email_verified_at' => now(),
        ]);

        $dewi = User::create([
            'name' => 'Dewi Lestari',
            'email' => 'dewi@helpdesk.test',
            'password' => 'password',
            'role' => 'requester',
            'email_verified_at' => now(),
        ]);

        $hardware = Category::create(['name' => 'Hardware', 'description' => 'Perangkat keras: PC, laptop, printer, scanner', 'department_id' => $it->id]);
        $software = Category::create(['name' => 'Software', 'description' => 'Aplikasi dan sistem operasi', 'department_id' => $it->id]);
        $jaringan = Category::create(['name' => 'Jaringan', 'description' => 'Internet, WiFi, VPN', 'department_id' => $it->id]);
        $akun = Category::create(['name' => 'Akun & Akses', 'description' => 'Akun user, email, hak akses sistem', 'department_id' => $it->id]);
        $email = Category::create(['name' => 'Email', 'description' => 'Masalah email kantor', 'department_id' => $it->id]);
        $lainnyaIt = Category::create(['name' => 'Lainnya', 'description' => 'Laporan lain di luar kategori', 'department_id' => $it->id]);

        $cuti = Category::create(['name' => 'Cuti & Izin', 'description' => 'Pengajuan cuti, izin, dan absensi', 'department_id' => $hr->id]);
        $gaji = Category::create(['name' => 'Penggajian', 'description' => 'Slip gaji, data rekening, tunjangan', 'department_id' => $hr->id]);
        $rekrutmen = Category::create(['name' => 'Rekrutmen', 'description' => 'Lamaran dan proses seleksi karyawan', 'department_id' => $hr->id]);

        $tickets = [
            [
                'ticket_code' => 'TKT-26-0001',
                'title' => 'Laptop tidak bisa menyala',
                'description' => "Laptop ThinkPad saya tidak menyala sejak pagi. Sudah dicoba charge selama 30 menit tetapi tidak ada respon indikator sama sekali.",
                'department_id' => $it->id,
                'category_id' => $hardware->id,
                'priority' => 'tinggi',
                'status' => 'diproses',
                'requester_id' => $andi->id,
                'agent_id' => $budi->id,
                'started_at' => now()->subHours(6),
                'created_at' => now()->subDays(3),
                'updated_at' => now()->subHours(6),
            ],
            [
                'ticket_code' => 'TKT-26-0002',
                'title' => 'Tidak bisa akses aplikasi ERP',
                'description' => "Saat login aplikasi ERP selalu muncul pesan 'Akses ditolak'. Padahal kemarin masih normal.",
                'department_id' => $it->id,
                'category_id' => $akun->id,
                'priority' => 'urgent',
                'status' => 'baru',
                'requester_id' => $dewi->id,
                'agent_id' => null,
                'created_at' => now()->subHours(2),
                'updated_at' => now()->subHours(2),
            ],
            [
                'ticket_code' => 'TKT-26-0003',
                'title' => 'WiFi kantor sering terputus',
                'description' => 'Koneksi WiFi di lantai 2 sering sekali terputus setiap sekitar 10 menit, mengganggu rapat online.',
                'department_id' => $it->id,
                'category_id' => $jaringan->id,
                'priority' => 'sedang',
                'status' => 'baru',
                'requester_id' => $andi->id,
                'agent_id' => null,
                'created_at' => now()->subDay(),
                'updated_at' => now()->subDay(),
            ],
            [
                'ticket_code' => 'TKT-26-0004',
                'title' => 'Printer di ruang meeting paper jam terus',
                'description' => 'Printer Epson di ruang meeting selalu macet di tengah cetak. Sudah dicoba ganti kertas tetap sama.',
                'department_id' => $it->id,
                'category_id' => $hardware->id,
                'priority' => 'rendah',
                'status' => 'selesai',
                'requester_id' => $dewi->id,
                'agent_id' => $sari->id,
                'started_at' => now()->subDays(4),
                'resolved_at' => now()->subDays(1),
                'created_at' => now()->subDays(5),
                'updated_at' => now()->subDays(1),
            ],
            [
                'ticket_code' => 'TKT-26-0005',
                'title' => 'Reset password email kantor',
                'description' => 'Mohon bantuan reset password email saya, saya lupa password lama.',
                'department_id' => $it->id,
                'category_id' => $email->id,
                'priority' => 'sedang',
                'status' => 'ditutup',
                'requester_id' => $andi->id,
                'agent_id' => $sari->id,
                'started_at' => now()->subDays(6),
                'resolved_at' => now()->subDays(2),
                'created_at' => now()->subDays(7),
                'updated_at' => now()->subDays(2),
            ],
            [
                'ticket_code' => 'TKT-26-0006',
                'title' => 'Instalasi Microsoft Teams untuk karyawan baru',
                'description' => 'Mohon dibantu instalasi Microsoft Teams dan akses SharePoint untuk 2 karyawan baru divisi keuangan.',
                'department_id' => $it->id,
                'category_id' => $software->id,
                'priority' => 'sedang',
                'status' => 'diproses',
                'requester_id' => $dewi->id,
                'agent_id' => $budi->id,
                'started_at' => now()->subHours(20),
                'created_at' => now()->subDays(2),
                'updated_at' => now()->subHours(20),
            ],
            [
                'ticket_code' => 'TKT-26-0007',
                'title' => 'Perubahan data rekening untuk penggajian',
                'description' => 'Saya ingin memperbarui nomor rekening bank yang digunakan untuk penggajian. Bagaimana prosedurnya?',
                'department_id' => $hr->id,
                'category_id' => $gaji->id,
                'priority' => 'sedang',
                'status' => 'baru',
                'requester_id' => $dewi->id,
                'agent_id' => null,
                'created_at' => now()->subHours(4),
                'updated_at' => now()->subHours(4),
            ],
        ];

        foreach ($tickets as $data) {
            Ticket::create($data);
        }

        Comment::create([
            'ticket_id' => 1,
            'user_id' => $budi->id,
            'body' => 'Halo Pak Andi, besok pagi saya berkunjung ke meja Bapak untuk cek langsung laptopnya ya.',
            'is_internal' => false,
        ]);

        Comment::create([
            'ticket_id' => 1,
            'user_id' => $budi->id,
            'body' => 'Dugaan awal: kerusakan adaptor atau mainboard. Siapkan unit cadangan untuk user sementara.',
            'is_internal' => true,
        ]);

        Comment::create([
            'ticket_id' => 4,
            'user_id' => $sari->id,
            'body' => 'Sudah dibersihkan roller dan diganti kertas dengan gramur yang sesuai. Silakan dicoba lagi, bu.',
            'is_internal' => false,
        ]);
    }
}
