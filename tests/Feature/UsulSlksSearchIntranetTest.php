<?php

namespace Tests\Feature;

use App\Models\SnapshotPegawai;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

/**
 * Tes pencarian pegawai SLKS dengan sumber intranet Penataran (on-demand per NIP).
 *
 * Koneksi `intranet_db` diganti sqlite :memory: di dalam tes dengan skema tabel
 * `pegawai` manual. Koneksi default juga sqlite :memory: (lihat phpunit.xml).
 *
 * Skema snapshot_pegawai dibentuk lewat migrasi asli repo via `migrate --path`,
 * bukan RefreshDatabase, karena salah satu migrasi lain memakai fungsi MySQL
 * CONCAT sehingga gagal di sqlite.
 */
class UsulSlksSearchIntranetTest extends TestCase
{
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->jalankanMigrasiSnapshot();
        $this->siapkanTabelPendukung();
        $this->siapkanKoneksiIntranet();

        $this->user = User::create([
            'name' => 'Admin SIPUT',
            'email' => 'admin@example.test',
            'password' => bcrypt('secret'),
            'role' => 'admin',
            'modules' => ['siput'],
        ]);
    }

    private function jalankanMigrasiSnapshot(): void
    {
        $this->artisan('migrate', [
            '--path' => [
                base_path('database/migrations/2026_01_08_034845_create_snapshot_pegawai_table.php'),
                base_path('database/migrations/2026_01_09_025041_add_no_hp_to_snapshot_pegawai_table.php'),
                base_path('database/migrations/2026_01_21_000002_add_tgl_lahir_to_snapshot_pegawai_table.php'),
                base_path('database/migrations/2026_01_23_032218_add_golongan_to_snapshot_pegawai_table.php'),
                base_path('database/migrations/2026_11_05_220716_add_unor_id_to_snapshot_pegawai_table.php'),
                base_path('database/migrations/2026_11_19_000000_add_intranet_fields_to_snapshot_pegawai_table.php'),
            ],
            '--realpath' => true,
        ])->assertExitCode(0);
    }

    private function siapkanTabelPendukung(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('role')->default('user');
            $table->json('modules')->nullable();
            $table->string('nip')->nullable();
            $table->string('pd_scope')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('stg_pegawai_import', function (Blueprint $table) {
            $table->id();
            $table->string('nip_baru')->nullable();
            $table->string('nama')->nullable();
            $table->string('gelar_depan')->nullable();
            $table->string('gelar_belakang')->nullable();
            $table->string('kedudukan_hukum_id')->nullable();
            $table->string('kedudukan_hukum')->nullable();
            $table->string('gol_akhir')->nullable();
            $table->string('jabatan')->nullable();
            $table->integer('mk_tahun')->nullable();
            $table->integer('mk_bulan')->nullable();
            $table->string('jenis_pegawai')->nullable();
            $table->timestamps();
        });

        Schema::create('usul_slks', function (Blueprint $table) {
            $table->id();
            $table->string('nip', 20)->index();
            $table->string('nama');
            $table->string('pangkat')->nullable();
            $table->string('jabatan')->nullable();
            $table->string('no_sk_hukdis')->nullable();
            $table->date('tmt_hukdis')->nullable();
            $table->string('no_sk_cltn')->nullable();
            $table->date('tmt_cltn')->nullable();
            $table->string('kabkota')->nullable();
            $table->string('provinsi')->nullable();
            $table->string('kd_wil')->nullable();
            $table->string('slks_ada')->nullable();
            $table->string('no_slks')->nullable();
            $table->date('tgl_slks')->nullable();
            $table->string('usul_slks')->nullable();
            $table->integer('masa_kerja_tahun')->nullable();
            $table->integer('masa_kerja_bulan')->nullable();
            $table->string('bulanp')->nullable();
            $table->string('tahunp')->nullable();
            $table->string('ms_tms')->nullable();
            $table->text('ket_tms')->nullable();
            $table->string('status')->default('draft_usulan');
            $table->string('jenis_pegawai')->nullable();
            $table->string('kedudukan_hukum_id')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->string('no_kepres')->nullable();
            $table->date('tanggal_kepres')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    private function siapkanKoneksiIntranet(string $password = 'testing'): void
    {
        config([
            'database.connections.intranet_db' => [
                'driver' => 'sqlite',
                'database' => ':memory:',
                'prefix' => '',
                'password' => $password,
            ],
        ]);

        DB::purge('intranet_db');

        Schema::connection('intranet_db')->create('pegawai', function (Blueprint $table) {
            $table->id();
            $table->string('nip')->nullable();
            $table->string('nip_baru')->nullable();
            $table->string('nip_lama')->nullable();
            $table->string('nama')->nullable();
            $table->string('nama_lengkap')->nullable();
            $table->string('gelar_depan')->nullable();
            $table->string('gelar_belakang')->nullable();
            $table->date('tgl_lahir')->nullable();
            $table->string('no_hp')->nullable();
            $table->string('jenis_kelamin')->nullable();
            $table->string('status_pegawai')->nullable();
            $table->string('tk_pendidikan_terakhir')->nullable();
            $table->string('unor_id')->nullable();
            $table->string('unor_nama')->nullable();
            $table->string('unor_induk_nama')->nullable();
            $table->string('jabatan_nama')->nullable();
            $table->string('eselon')->nullable();
            $table->string('gol_ruang_akhir')->nullable();
            $table->integer('mk_tahun')->nullable();
            $table->integer('mk_bulan')->nullable();
            $table->string('kedudukan_pns_id')->nullable();
            $table->string('kedudukan_pns_nama')->nullable();
            $table->string('jenis_pegawai_nama')->nullable();
        });
    }

    private function barisPegawai(string $nip, array $override = []): array
    {
        return array_merge([
            'nip' => $nip,
            'nip_baru' => $nip,
            'nip_lama' => null,
            'nama' => 'Budi Santoso',
            'nama_lengkap' => 'Dr. Budi Santoso, S.Kom., M.T.',
            'gelar_depan' => 'Dr.',
            'gelar_belakang' => 'S.Kom., M.T.',
            'tgl_lahir' => '1985-03-12',
            'no_hp' => '081234567890',
            'jenis_kelamin' => 'L',
            'status_pegawai' => 'PNS',
            'tk_pendidikan_terakhir' => 'S2',
            'unor_id' => 'U-001',
            'unor_nama' => 'Bidang Sekolah Dasar',
            'unor_induk_nama' => 'Dinas Pendidikan',
            'jabatan_nama' => 'Analis Kepegawaian',
            'eselon' => 'III/a',
            'gol_ruang_akhir' => 'III/c',
            'mk_tahun' => 12,
            'mk_bulan' => 4,
            'kedudukan_pns_id' => '01',
            'kedudukan_pns_nama' => 'Aktif',
            'jenis_pegawai_nama' => 'PNS',
        ], $override);
    }

    private function isiIntranet(array $rows): void
    {
        DB::connection('intranet_db')->table('pegawai')->insert($rows);
    }

    private function cari(string $nip)
    {
        return $this->actingAs($this->user)->getJson(route('siput.usul-slks.search', ['nip' => $nip]));
    }

    public function test_nip_belum_ada_di_snapshot_ditarik_dari_intranet_dan_disimpan_dengan_pemetaan_benar(): void
    {
        $nip = '198503122010011001';
        $this->isiIntranet([$this->barisPegawai($nip)]);

        $response = $this->cari($nip);

        $response->assertOk()->assertJson([
            'found' => true,
            'nama' => 'Dr. Budi Santoso, S.Kom., M.T.',
            'pangkat' => 'Penata',
            'jabatan' => 'Analis Kepegawaian',
            'mk_tahun' => 12,
            'mk_bulan' => 4,
            'kedudukan_hukum_id' => '01',
            'kedudukan_hukum' => 'Aktif',
            'jenis_pegawai' => 'PNS',
            'source' => 'intranet_penataran',
        ]);

        $snapshot = SnapshotPegawai::where('nip_baru', $nip)->firstOrFail();
        $this->assertSame('Budi Santoso', $snapshot->nama_pegawai);
        $this->assertSame('Dr.', $snapshot->gelar_depan);
        $this->assertSame('S.Kom., M.T.', $snapshot->gelar_belakang);
        $this->assertSame('III/c', $snapshot->golongan);
        $this->assertSame('Analis Kepegawaian', $snapshot->jabatan);
        $this->assertSame('III/a', $snapshot->eselon);
        $this->assertSame('Dinas Pendidikan', $snapshot->pd);
        $this->assertSame('Bidang Sekolah Dasar', $snapshot->sub_pd);
        $this->assertSame('L', $snapshot->jenikel);
        $this->assertSame('PNS', $snapshot->sts_peg);
        $this->assertSame('S2', $snapshot->tk_pend);
        $this->assertSame('U-001', $snapshot->unor_id);
        $this->assertSame(12, $snapshot->mk_tahun);
        $this->assertSame(4, $snapshot->mk_bulan);
        $this->assertSame('01', $snapshot->kedudukan_hukum_id);
        $this->assertSame('Aktif', $snapshot->kedudukan_hukum);
        $this->assertSame('PNS', $snapshot->jenis_pegawai);
        $this->assertSame('intranet_penataran', $snapshot->sumber);
        $this->assertSame('1985-03-12', $snapshot->tgl_lahir->format('Y-m-d'));
        $this->assertNotNull($snapshot->last_sync_at);
    }

    public function test_pencarian_lewat_nip_lama_menyimpan_satu_baris_dengan_nip_baru_kanonis(): void
    {
        $nipLama = '197001011990031001';
        $nipBaru = '198503122010011005';
        $this->isiIntranet([
            $this->barisPegawai($nipBaru, ['nip_lama' => $nipLama]),
        ]);

        // Pencarian pertama kena lewat nip_lama (bukan nip_baru baris)
        $this->cari($nipLama)->assertJson([
            'found' => true,
            'source' => 'intranet_penataran',
        ]);

        // Hanya satu baris, berkunci nip_baru kanonis (bukan NIP yang dicari)
        $this->assertSame(1, SnapshotPegawai::count());
        $this->assertDatabaseHas('snapshot_pegawai', ['nip_baru' => $nipBaru]);
        $this->assertDatabaseMissing('snapshot_pegawai', ['nip_baru' => $nipLama]);

        // Pencarian kedua lewat nip_baru kanonis harus menjawab dari snapshot
        $this->cari($nipBaru)->assertJson([
            'found' => true,
            'source' => 'snapshot',
        ]);

        $this->assertSame(1, SnapshotPegawai::count());

        // Regresi: mengulang pencarian lewat nip_lama tidak boleh membuat baris
        // duplikat / melanggar unique nip_baru
        $this->cari($nipLama)->assertOk()->assertJson(['found' => true]);
        $this->assertSame(1, SnapshotPegawai::count());
    }

    public function test_pencarian_kedua_memakai_snapshot_walau_baris_intranet_dihapus(): void
    {
        $nip = '198503122010011002';
        $this->isiIntranet([$this->barisPegawai($nip)]);

        $this->cari($nip)->assertJson(['found' => true, 'source' => 'intranet_penataran']);

        // Hapus seluruh baris intranet: pencarian kedua tetap harus dari snapshot
        DB::connection('intranet_db')->table('pegawai')->delete();

        $this->cari($nip)
            ->assertOk()
            ->assertJson([
                'found' => true,
                'nama' => 'Dr. Budi Santoso, S.Kom., M.T.',
                'source' => 'snapshot',
            ]);

        $this->assertSame(1, SnapshotPegawai::where('nip_baru', $nip)->count());
    }

    public function test_nip_tidak_ada_di_intranet_mengembalikan_not_found_tanpa_menulis(): void
    {
        $this->cari('199901012020011999')
            ->assertOk()
            ->assertJson(['found' => false, 'reason' => 'not_found']);

        $this->assertSame(0, SnapshotPegawai::count());
    }

    public function test_intranet_belum_terkonfigurasi_mengembalikan_source_error(): void
    {
        $this->siapkanKoneksiIntranet('');

        $this->cari('198503122010011003')
            ->assertOk()
            ->assertJson(['found' => false, 'reason' => 'source_error']);

        $this->assertArrayHasKey('message', $this->cari('198503122010011003')->json());
        $this->assertSame(0, SnapshotPegawai::count());
    }

    public function test_pegawai_berkedudukan_tidak_aktif_mengembalikan_not_active(): void
    {
        $nip = '198503122010011004';
        $this->isiIntranet([
            $this->barisPegawai($nip, [
                'kedudukan_pns_id' => '17',
                'kedudukan_pns_nama' => 'Non Aktif',
            ]),
        ]);

        $this->cari($nip)
            ->assertOk()
            ->assertJson(['found' => false, 'reason' => 'not_active']);
    }
}
