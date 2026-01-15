# Alur Kerja Peran: Kepala LPPM (Head of LPPM)

Kepala LPPM adalah otoritas tertinggi dalam penentuan kelayakan usulan penelitian dan pengabdian masyarakat.

## 1. Persetujuan Awal (Pra-Review)
*   **Validasi Akhir**: Memeriksa usulan yang telah disetujui oleh Dekan (`APPROVED`).
*   **Lanjut ke Review (`WAITING_REVIEWER`)**: Memberikan persetujuan agar usulan dapat ditugaskan kepada reviewer oleh Admin LPPM.
*   **Tolak (`REJECTED`)**: Jika usulan dianggap tidak memenuhi syarat administratif atau kebijakan LPPM secara keseluruhan.

## 2. Keputusan Final (Pasca-Review)
*   **Monitoring Hasil Review**: Melihat hasil evaluasi dari semua reviewer pada usulan yang berstatus `REVIEWED`.
*   **Keputusan Akhir**:
    *   **Setujui (`COMPLETED`)**: Usulan dinyatakan lolos dan siap dilaksanakan.
    *   **Perlu Revisi (`REVISION_NEEDED`)**: Mengembalikan usulan ke Dosen untuk diperbaiki sesuai saran reviewer.
    *   **Tolak (`REJECTED`)**: Menghentikan proses usulan berdasarkan rekomendasi reviewer.

---
**Status Terkait:** `APPROVED`, `WAITING_REVIEWER`, `REVIEWED`, `COMPLETED`, `REVISION_NEEDED`, `REJECTED`.
