<?php

namespace App\Constants;

class ProposalConstants
{
    public const OUTPUT_CATEGORIES = ['Wajib', 'Tambahan'];

    public const OUTPUT_STATUSES = [
        'draft',
        'submit',
        'sedang direview',
        'accepted',
        'published',
    ];

    public const RESEARCH_OUTPUT_GROUPS = [
        'jurnal',
        'prosiding',
        'buku',
        'hki',
        'lainnya',
    ];

    public const RESEARCH_OUTPUT_TYPES = [
        'jurnal' => [
            'Jurnal Int. Bereputasi (Q1-Q2)',
            'Jurnal Int. Bereputasi (Q3-Q4)',
            'Jurnal Internasional (Terindeks)',
            'Jurnal Nas. Terakreditasi (Sinta 1-2)',
            'Jurnal Nas. Terakreditasi (Sinta 3-4)',
            'Jurnal Nas. Terakreditasi (Sinta 5-6)',
        ],
        'prosiding' => [
            'Prosiding Sem. Int. Terindeks (Scopus/WoS)',
            'Prosiding Seminar Nasional',
        ],
        'buku' => [
            'Buku Referensi (ISBN)',
            'Buku Monograf (ISBN)',
            'Chapter dalam Buku Internasional',
        ],
        'hki' => [
            'Paten (Granted/Terdaftar)',
            'Paten Sederhana (Granted/Terdaftar)',
            'Hak Cipta',
            'Desain Industri',
            'PVT',
            'DTLST',
        ],
        'lainnya' => [
            'Naskah Kebijakan (Policy Brief)',
            'Visiting Lecturer',
            'Keynote Speaker',
        ],
    ];

    public const PKM_OUTPUT_GROUPS = [
        'pemberdayaan',
        'jurnal',
        'media',
        'video',
        'produk',
        'hki_buku',
    ];

    public const PKM_OUTPUT_TYPES = [
        'pemberdayaan' => [
            'Peningkatan Omzet (Rp/%)',
            'Peningkatan Kualitas (Sertifikasi/PIRT)',
            'Perbaikan Tata Kelola/Manajemen',
            'Peningkatan Kompetensi SDM',
        ],
        'jurnal' => [
            'Jurnal PKM (Sinta 1-2)',
            'Jurnal PKM (Sinta 3-4)',
            'Jurnal PKM (Sinta 5-6)',
            'Jurnal PKM (Ber-ISSN/Non-Sinta)',
        ],
        'media' => [
            'Media Massa Nasional (Cetak/Elektronik)',
            'Media Massa Lokal (Cetak/Elektronik)',
        ],
        'video' => [
            'Video Kegiatan (Publikasi Youtube/Medsos)',
        ],
        'produk' => [
            'Teknologi Tepat Guna (TTG)',
            'Model/Sistem/Rekayasa Sosial',
            'Produk Tersertifikasi',
        ],
        'hki_buku' => [
            'Hak Cipta (Modul/Panduan)',
            'Buku Pedoman/Panduan Penerapan',
        ],
    ];

    public const PARTNER_TYPES = [
        'Pemerintah',
        'Swasta',
        'UMKM',
        'Yayasan',
        'Organisasi Masyarakat',
    ];
}
