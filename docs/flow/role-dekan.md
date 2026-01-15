# Alur Kerja Peran: Dekan (Dean)

Dekan memiliki tanggung jawab untuk melakukan validasi awal terhadap usulan yang berasal dari fakultasnya.

## 1. Monitoring Usulan Fakultas
*   **Daftar Usulan**: Melihat semua usulan (Penelitian & PKM) yang diajukan oleh dosen di lingkup fakultasnya.
*   **Detail Usulan**: Memeriksa dokumen, anggota tim, dan kesesuaian usulan dengan visi fakultas.

## 2. Persetujuan Awal
*   **Verifikasi**: Dekan memeriksa usulan yang berstatus `SUBMITTED`.
*   **Keputusan**:
    *   **Setujui (`APPROVED`)**: Meneruskan usulan ke Kepala LPPM untuk diproses lebih lanjut.
    *   **Kembalikan (`NEED_ASSIGNMENT`)**: Jika ada masalah pada komposisi tim atau persetujuan anggota, usulan dikembalikan ke Dosen.
    *   **Tolak (`REJECTED`)**: Menolak usulan secara langsung jika dianggap tidak layak.

## 3. Batasan Akses
*   Dekan hanya dapat melihat dan memproses usulan yang diajukan oleh dosen dari fakultas yang sama dengan fakultas tempat Dekan tersebut bertugas.

---
**Status Terkait:** `SUBMITTED`, `APPROVED`, `NEED_ASSIGNMENT`, `REJECTED`.
