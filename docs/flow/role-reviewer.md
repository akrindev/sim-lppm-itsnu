# Alur Kerja Peran: Reviewer (Evaluator)

Reviewer adalah pakar yang bertugas memberikan evaluasi substansi terhadap usulan yang diajukan.

## 1. Menerima Tugas Review
*   **Notifikasi**: Reviewer menerima pemberitahuan saat ditugaskan oleh Admin LPPM.
*   **Daftar Tugas**: Melihat daftar usulan yang perlu dinilai pada dashboard Reviewer.

## 2. Proses Evaluasi
*   **Mulai Review (`IN_PROGRESS`)**: Membuka form detail usulan. Status pengerjaan review internal akan berubah menjadi `IN_PROGRESS`.
*   **Penilaian Substansi**: Memeriksa dokumen usulan, luaran, dan anggaran.
*   **Memberikan Catatan**: Mengisi komentar/masukan untuk setiap bagian usulan (jika diperlukan) dan catatan umum.
*   **Rekomendasi**: Memberikan rekomendasi akhir berupa:
    *   Setujui (Approved)
    *   Revisi (Revision Needed)
    *   Tolak (Rejected)

## 3. Penyelesaian Review (`COMPLETED`)
*   **Submit Review**: Mengirimkan hasil penilaian ke sistem. 
*   **Hasil Kolektif**: Jika semua reviewer yang ditugaskan telah selesai, status usulan utama akan berubah menjadi `REVIEWED` untuk diputuskan oleh Kepala LPPM.

---
**Status Terkait (Usulan):** `UNDER_REVIEW`, `REVIEWED`.
**Status Terkait (Reviewer):** `PENDING`, `IN_PROGRESS`, `COMPLETED`, `RE_REVIEW_REQUESTED`.
