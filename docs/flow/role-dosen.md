# Alur Kerja Peran: Dosen (Lecturer)

Dosen adalah peran utama yang mengajukan usulan penelitian atau pengabdian masyarakat.

## 1. Penyusunan Usulan (Draft)
*   **Membuat Usulan**: Memilih jenis usulan (Penelitian atau PKM).
*   **Mengisi Identitas**: Judul, tema, topik, rumpun ilmu, dan tingkat kesiapan teknologi (TKT).
*   **Mengisi Substansi**: Mengunggah dokumen usulan, mengisi luaran wajib dan tambahan.
*   **Rencana Anggaran (SBK)**: Mengisi rincian biaya sesuai Standar Biaya Keluaran.
*   **Jadwal Kegiatan**: Mengatur linimasa pelaksanaan kegiatan.

## 2. Pengelolaan Tim
*   **Mengundang Anggota**: Menambahkan dosen lain sebagai anggota tim.
*   **Persetujuan Anggota**: Menunggu semua anggota menerima undangan (status berubah dari `PENDING` menjadi `ACCEPTED`).
*   **Submit**: Usulan hanya bisa diajukan (`SUBMITTED`) jika **semua** anggota telah menyetujui.

## 3. Proses Pengajuan & Persetujuan
*   **Diajukan**: Status berubah menjadi `SUBMITTED`.
*   **Persetujuan Dekan**: Menunggu Dekan fakultas menyetujui.
*   **Persetujuan Kepala LPPM**: Menunggu Kepala LPPM memberikan persetujuan awal untuk proses review.

## 4. Proses Review & Revisi
*   **Proses Review**: Usulan sedang dinilai oleh reviewer yang ditugaskan.
*   **Perlu Revisi**: Jika status menjadi `REVISION_NEEDED`, Dosen harus memperbaiki usulan berdasarkan catatan dari reviewer/LPPM.
*   **Resubmit**: Mengajukan kembali usulan yang telah direvisi. Usulan akan melewati alur persetujuan dari awal lagi.

## 5. Hasil Akhir
*   **Selesai**: Usulan disetujui sepenuhnya (`COMPLETED`).
*   **Ditolak**: Usulan tidak diterima (`REJECTED`).

---
**Status Terkait:** `DRAFT`, `SUBMITTED`, `NEED_ASSIGNMENT`, `REVISION_NEEDED`, `COMPLETED`, `REJECTED`.
