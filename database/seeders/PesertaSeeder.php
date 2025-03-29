<?php

namespace Database\Seeders;

use App\Models\Peserta;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;

class PesertaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create('id_ID');
        $data = [];

        for ($i = 0; $i < 100; $i++) {
            $tanggalLahir = $faker->date('Y-m-d', '-18 years');
            $tanggalMulaiAsuransi = $faker->dateTimeBetween('-2 years', 'now');
            $durasi = $faker->randomElement([12, 24, 36, 48]);
            $tanggalSelesaiAsuransi = (clone $tanggalMulaiAsuransi);
            $tanggalSelesaiAsuransi->modify("+$durasi months");

            $statusPeserta = $faker->randomElement(['pending', 'diterima', 'tolak']);
            $statusDokumen = $faker->randomElement(['pending', 'diterima', 'tolak']);
            $approvedPesertaAt = $statusPeserta !== 'pending' ? $faker->dateTimeBetween('-1 years', 'now') : null;
            $approvedDokumenAt = $statusDokumen !== 'pending' ? $faker->dateTimeBetween('-1 years', 'now') : null;

            $data[] = [
                'uuid' => Uuid::uuid4()->toString(),
                'nama' => $faker->name,
                'tempat_lahir' => $faker->city,
                'tanggal_lahir' => $tanggalLahir,
                'umur' => date_diff(date_create($tanggalLahir), date_create('now'))->y,
                'alamat' => $faker->address,
                'durasi_asuransi' => $durasi,
                'tanggal_mulai_asuransi' => $tanggalMulaiAsuransi,
                'tanggal_selesai_asuransi' => $tanggalSelesaiAsuransi,
                'status_peserta' => $statusPeserta,
                'approved_peserta_at' => $approvedPesertaAt,
                'approved_peserta_by' => $approvedPesertaAt ? $faker->name : null,
                'status_dokumen' => $statusDokumen,
                'approved_dokumen_at' => $approvedDokumenAt,
                'approved_dokumen_by' => $approvedDokumenAt ? $faker->name : null,
                'created_by' => $faker->name,
                'updated_by' => $faker->name,
                'deleted_at' => null,
                'deleted_by' => null,
            ];
        }

        Peserta::insert($data);
    }
}
