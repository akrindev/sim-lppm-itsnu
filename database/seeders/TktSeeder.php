<?php

namespace Database\Seeders;

use App\Models\TktLevel;
use App\Models\TktIndicator;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TktSeeder extends Seeder
{
    public function run(): void
    {
        // Clear existing data
        DB::table('tkt_indicators')->delete();
        DB::table('tkt_levels')->delete();

        $data = [
            [
                'type' => 'Umum',
                'levels' => [
                    [
                        'level' => 1,
                        'description' => 'Prinsip dasar dari teknologi telah diteliti',
                        'indicators' => [
                            ['code' => '1.1', 'indicator' => 'Asumsi dan hukum dasar yang akan digunakan pada teknologi (baru) telah ditentukan'],
                            ['code' => '1.2', 'indicator' => 'Studi literatur tentang prinsip dasar teknologi yang akan dikembangkan telah dilakukan'],
                            ['code' => '1.3', 'indicator' => 'Hipotesis penelitian (jika ada) telah diformulasikan'],
                        ]
                    ],
                    [
                        'level' => 2,
                        'description' => 'Formulasi konsep teknologi dan aplikasinya',
                        'indicators' => [
                            ['code' => '2.1', 'indicator' => 'Peralatan dan sistem yang akan digunakan telah teridentifikasi'],
                            ['code' => '2.2', 'indicator' => 'Studi literatur (teoritis/empiris) menunjukkan teknologi yang akan dikembangkan layak diterapkan'],
                            ['code' => '2.3', 'indicator' => 'Desain teoritis dan empiris telah teridentifikasi'],
                            ['code' => '2.4', 'indicator' => 'Elemen-elemen dasar dari teknologi telah diketahui'],
                        ]
                    ],
                    [
                        'level' => 3,
                        'description' => 'Pembuktian konsep (Proof of Concept) fungsi dan/atau karakteristik penting secara analitis dan eksperimental',
                        'indicators' => [
                            ['code' => '3.1', 'indicator' => 'Studi analitik dan eksperimental telah dilakukan untuk memvalidasi prediksi analitis'],
                            ['code' => '3.2', 'indicator' => 'Komponen teknologi telah divalidasi secara terpisah (di laboratorium)'],
                            ['code' => '3.3', 'indicator' => 'Hasil eksperimen laboratorium sesuai dengan prediksi analitis'],
                        ]
                    ],
                    [
                        'level' => 4,
                        'description' => 'Validasi komponen/sub-sistem dalam lingkungan laboratorium',
                        'indicators' => [
                            ['code' => '4.1', 'indicator' => 'Komponen teknologi telah diintegrasikan'],
                            ['code' => '4.2', 'indicator' => 'Validasi komponen dalam lingkungan laboratorium telah dilakukan'],
                        ]
                    ],
                    [
                        'level' => 5,
                        'description' => 'Validasi komponen/sub-sistem dalam lingkungan yang relevan',
                        'indicators' => [
                            ['code' => '5.1', 'indicator' => 'Komponen teknologi telah divalidasi dalam lingkungan yang relevan'],
                            ['code' => '5.2', 'indicator' => 'Akurasi/kinerja teknologi telah diuji dalam lingkungan yang relevan'],
                        ]
                    ],
                    [
                        'level' => 6,
                        'description' => 'Demonstrasi model atau prototipe sistem/sub-sistem dalam lingkungan yang relevan',
                        'indicators' => [
                            ['code' => '6.1', 'indicator' => 'Prototipe sistem telah didemonstrasikan dalam lingkungan yang relevan'],
                            ['code' => '6.2', 'indicator' => 'Fungsi sistem berjalan dengan baik dalam lingkungan yang relevan'],
                        ]
                    ],
                    [
                        'level' => 7,
                        'description' => 'Demonstrasi prototipe sistem dalam lingkungan operasional',
                        'indicators' => [
                            ['code' => '7.1', 'indicator' => 'Prototipe sistem telah didemonstrasikan dalam lingkungan operasional'],
                            ['code' => '7.2', 'indicator' => 'Sistem telah teruji dalam kondisi operasional sebenarnya'],
                        ]
                    ],
                    [
                        'level' => 8,
                        'description' => 'Sistem telah lengkap dan memenuhi syarat (qualified) melalui pengujian dan demonstrasi',
                        'indicators' => [
                            ['code' => '8.1', 'indicator' => 'Sistem akhir telah selesai dibuat dan diuji'],
                            ['code' => '8.2', 'indicator' => 'Sistem telah memenuhi standar/sertifikasi yang berlaku'],
                        ]
                    ],
                    [
                        'level' => 9,
                        'description' => 'Sistem benar-benar teruji/terbukti melalui keberhasilan pengoperasian',
                        'indicators' => [
                            ['code' => '9.1', 'indicator' => 'Sistem telah beroperasi dengan sukses dalam kondisi nyata'],
                            ['code' => '9.2', 'indicator' => 'Dokumentasi operasional dan pemeliharaan telah lengkap'],
                        ]
                    ],
                ]
            ],
            [
                'type' => 'Software',
                'levels' => [
                    [
                        'level' => 1,
                        'description' => 'Prinsip dasar dari teknologi software telah diteliti',
                        'indicators' => [
                            ['code' => '1.1', 'indicator' => 'Algoritma dasar telah didefinisikan'],
                            ['code' => '1.2', 'indicator' => 'Arsitektur awal software telah dirancang'],
                            ['code' => '1.3', 'indicator' => 'Konsep dasar penggunaan software telah diidentifikasi'],
                        ]
                    ],
                    [
                        'level' => 2,
                        'description' => 'Formulasi konsep teknologi software dan aplikasinya',
                        'indicators' => [
                            ['code' => '2.1', 'indicator' => 'Desain software secara teoritis telah dibuat'],
                            ['code' => '2.2', 'indicator' => 'Komponen-komponen software telah didefinisikan'],
                            ['code' => '2.3', 'indicator' => 'Alur kerja (workflow) software telah dirancang'],
                        ]
                    ],
                    [
                        'level' => 3,
                        'description' => 'Pembuktian konsep (Proof of Concept) fungsi penting secara analitis dan eksperimental',
                        'indicators' => [
                            ['code' => '3.1', 'indicator' => 'Algoritma kunci telah diimplementasikan dan diuji (unit testing)'],
                            ['code' => '3.2', 'indicator' => 'Fungsi-fungsi utama telah dibuktikan kelayakannya'],
                        ]
                    ],
                    // ... Add more levels for Software as needed, keeping it concise for now
                ]
            ],
            // Add other types: Pertanian, Kesehatan, Farmasi, Soshum, Seni, Pendidikan
            [
                'type' => 'Pertanian/Perikanan/Peternakan',
                'levels' => [
                    ['level' => 1, 'description' => 'Prinsip dasar teknologi pertanian/perikanan/peternakan telah diteliti', 'indicators' => [['code' => '1.1', 'indicator' => 'Identifikasi prinsip dasar']]],
                    ['level' => 2, 'description' => 'Formulasi konsep teknologi dan aplikasinya', 'indicators' => [['code' => '2.1', 'indicator' => 'Desain konsep']]],
                    ['level' => 3, 'description' => 'Pembuktian konsep (Proof of Concept)', 'indicators' => [['code' => '3.1', 'indicator' => 'Validasi konsep di lab']]],
                    ['level' => 4, 'description' => 'Validasi komponen dalam lingkungan laboratorium', 'indicators' => [['code' => '4.1', 'indicator' => 'Prototipe skala lab']]],
                    ['level' => 5, 'description' => 'Validasi komponen dalam lingkungan yang relevan', 'indicators' => [['code' => '5.1', 'indicator' => 'Validasi di lingkungan relevan']]],
                    ['level' => 6, 'description' => 'Demonstrasi model dalam lingkungan yang relevan', 'indicators' => [['code' => '6.1', 'indicator' => 'Demonstrasi prototipe']]],
                    ['level' => 7, 'description' => 'Demonstrasi prototipe dalam lingkungan operasional', 'indicators' => [['code' => '7.1', 'indicator' => 'Uji coba lapangan terbatas']]],
                    ['level' => 8, 'description' => 'Sistem lengkap dan memenuhi syarat', 'indicators' => [['code' => '8.1', 'indicator' => 'Sertifikasi dan standarisasi']]],
                    ['level' => 9, 'description' => 'Sistem benar-benar teruji', 'indicators' => [['code' => '9.1', 'indicator' => 'Adopsi teknologi oleh pengguna']]],
                ]
            ],
            [
                'type' => 'Kesehatan - Produk Vaksin/Hayati',
                'levels' => [
                    ['level' => 1, 'description' => 'Prinsip dasar', 'indicators' => [['code' => '1.1', 'indicator' => 'Review literatur ilmiah']]],
                    ['level' => 2, 'description' => 'Formulasi konsep', 'indicators' => [['code' => '2.1', 'indicator' => 'Desain eksperimen']]],
                    ['level' => 3, 'description' => 'Pembuktian konsep', 'indicators' => [['code' => '3.1', 'indicator' => 'In vitro studies']]],
                    ['level' => 4, 'description' => 'Validasi di lab', 'indicators' => [['code' => '4.1', 'indicator' => 'In vivo studies (hewan kecil)']]],
                    ['level' => 5, 'description' => 'Validasi di lingkungan relevan', 'indicators' => [['code' => '5.1', 'indicator' => 'Uji pra-klinis lanjut']]],
                    ['level' => 6, 'description' => 'Demonstrasi di lingkungan relevan', 'indicators' => [['code' => '6.1', 'indicator' => 'Uji klinis fase 1']]],
                    ['level' => 7, 'description' => 'Demonstrasi di lingkungan operasional', 'indicators' => [['code' => '7.1', 'indicator' => 'Uji klinis fase 2']]],
                    ['level' => 8, 'description' => 'Sistem lengkap', 'indicators' => [['code' => '8.1', 'indicator' => 'Uji klinis fase 3']]],
                    ['level' => 9, 'description' => 'Sistem teruji', 'indicators' => [['code' => '9.1', 'indicator' => 'Post-marketing surveillance']]],
                ]
            ],
            [
                'type' => 'Kesehatan - Alat Kesehatan',
                'levels' => [
                    ['level' => 1, 'description' => 'Prinsip dasar', 'indicators' => [['code' => '1.1', 'indicator' => 'Ide awal']]],
                    ['level' => 2, 'description' => 'Formulasi konsep', 'indicators' => [['code' => '2.1', 'indicator' => 'Desain awal']]],
                    ['level' => 3, 'description' => 'Pembuktian konsep', 'indicators' => [['code' => '3.1', 'indicator' => 'Prototipe awal']]],
                    ['level' => 4, 'description' => 'Validasi di lab', 'indicators' => [['code' => '4.1', 'indicator' => 'Uji fungsi di lab']]],
                    ['level' => 5, 'description' => 'Validasi di lingkungan relevan', 'indicators' => [['code' => '5.1', 'indicator' => 'Uji keamanan']]],
                    ['level' => 6, 'description' => 'Demonstrasi di lingkungan relevan', 'indicators' => [['code' => '6.1', 'indicator' => 'Uji klinis terbatas']]],
                    ['level' => 7, 'description' => 'Demonstrasi di lingkungan operasional', 'indicators' => [['code' => '7.1', 'indicator' => 'Uji klinis multicenter']]],
                    ['level' => 8, 'description' => 'Sistem lengkap', 'indicators' => [['code' => '8.1', 'indicator' => 'Registrasi alat kesehatan']]],
                    ['level' => 9, 'description' => 'Sistem teruji', 'indicators' => [['code' => '9.1', 'indicator' => 'Penggunaan rutin di faskes']]],
                ]
            ],
            [
                'type' => 'Farmasi/Obat',
                'levels' => [
                    ['level' => 1, 'description' => 'Prinsip dasar', 'indicators' => [['code' => '1.1', 'indicator' => 'Penelusuran literatur']]],
                    ['level' => 2, 'description' => 'Formulasi konsep', 'indicators' => [['code' => '2.1', 'indicator' => 'Desain sintesis']]],
                    ['level' => 3, 'description' => 'Pembuktian konsep', 'indicators' => [['code' => '3.1', 'indicator' => 'Sintesis skala lab']]],
                    ['level' => 4, 'description' => 'Validasi di lab', 'indicators' => [['code' => '4.1', 'indicator' => 'Karakterisasi bahan']]],
                    ['level' => 5, 'description' => 'Validasi di lingkungan relevan', 'indicators' => [['code' => '5.1', 'indicator' => 'Formulasi sediaan']]],
                    ['level' => 6, 'description' => 'Demonstrasi di lingkungan relevan', 'indicators' => [['code' => '6.1', 'indicator' => 'Uji stabilitas']]],
                    ['level' => 7, 'description' => 'Demonstrasi di lingkungan operasional', 'indicators' => [['code' => '7.1', 'indicator' => 'Scale up produksi']]],
                    ['level' => 8, 'description' => 'Sistem lengkap', 'indicators' => [['code' => '8.1', 'indicator' => 'Validasi proses produksi']]],
                    ['level' => 9, 'description' => 'Sistem teruji', 'indicators' => [['code' => '9.1', 'indicator' => 'Produksi komersial']]],
                ]
            ],
            [
                'type' => 'Seni',
                'levels' => [
                    ['level' => 1, 'description' => 'Prinsip dasar', 'indicators' => [['code' => '1.1', 'indicator' => 'Ide/gagasan seni']]],
                    ['level' => 2, 'description' => 'Formulasi konsep', 'indicators' => [['code' => '2.1', 'indicator' => 'Sketsa/rancangan awal']]],
                    ['level' => 3, 'description' => 'Pembuktian konsep', 'indicators' => [['code' => '3.1', 'indicator' => 'Eksplorasi medium/teknik']]],
                    ['level' => 4, 'description' => 'Validasi di studio', 'indicators' => [['code' => '4.1', 'indicator' => 'Pembuatan karya purwarupa']]],
                    ['level' => 5, 'description' => 'Validasi di lingkungan relevan', 'indicators' => [['code' => '5.1', 'indicator' => 'Review kurator/kritikus']]],
                    ['level' => 6, 'description' => 'Demonstrasi di lingkungan relevan', 'indicators' => [['code' => '6.1', 'indicator' => 'Pameran terbatas']]],
                    ['level' => 7, 'description' => 'Demonstrasi di lingkungan operasional', 'indicators' => [['code' => '7.1', 'indicator' => 'Pameran publik skala kecil']]],
                    ['level' => 8, 'description' => 'Sistem lengkap', 'indicators' => [['code' => '8.1', 'indicator' => 'Pameran publik skala besar/nasional']]],
                    ['level' => 9, 'description' => 'Sistem teruji', 'indicators' => [['code' => '9.1', 'indicator' => 'Pengakuan internasional/hak cipta']]],
                ]
            ],
            [
                'type' => 'Sosial Humaniora dan Pendidikan',
                'levels' => [
                    [
                        'level' => 1,
                        'description' => 'Prinsip dasar riset sosial humaniora telah diteliti',
                        'indicators' => [
                            ['code' => '1.1', 'indicator' => 'Masalah sosial/humaniora telah diidentifikasi'],
                            ['code' => '1.2', 'indicator' => 'Studi literatur terkait masalah telah dilakukan'],
                        ]
                    ],
                    // ...
                ]
            ]
        ];

        foreach ($data as $typeData) {
            foreach ($typeData['levels'] as $levelData) {
                $level = TktLevel::create([
                    'type' => $typeData['type'],
                    'level' => $levelData['level'],
                    'description' => $levelData['description'],
                ]);

                foreach ($levelData['indicators'] as $indicatorData) {
                    TktIndicator::create([
                        'tkt_level_id' => $level->id,
                        'code' => $indicatorData['code'],
                        'indicator' => $indicatorData['indicator'],
                    ]);
                }
            }
        }
    }
}
