<?php

class GajiKaryawan {
    private $modelFile = 'model/gaji.php';
    private $karyawan = [];

    public function __construct() {
        if (!file_exists('model')) {
            mkdir('model');
        }
        $this->loadKaryawan();
    }

    private function loadKaryawan() {
        if (file_exists($this->modelFile)) {
            $data = file_get_contents($this->modelFile);
            $this->karyawan = unserialize($data) ?: [];
        }
    }

    private function saveKaryawan() {
        file_put_contents($this->modelFile, serialize($this->karyawan));
    }

    public function tampilkanMenu() {
        while (true) {
            echo "\n Sistem manajemen gaji karyawan \n";
            echo "1. Lihat Karyawan\n";
            echo "2. Tambah Karyawan\n";
            echo "3. Update Karyawan\n";
            echo "4. Hapus Karyawan\n";
            echo "5. Hitung Gaji Karyawan\n";
            echo "6. Keluar Aplikasi\n";
            echo "Pilih menu (1-6): ";
            
            $pilihan = trim(fgets(STDIN));
            
            switch ($pilihan) {
                case '1':
                    $this->lihatKaryawan();
                    break;
                case '2':
                    $this->tambahKaryawan();
                    break;
                case '3':
                    $this->updateKaryawan();
                    break;
                case '4':
                    $this->hapusKaryawan();
                    break;
                case '5':
                    $this->hitungGaji();
                    break;
                case '6':
                    echo "Terima kasih, sampai jumpa!\n";
                    exit;
                default:
                    echo "Menu tidak valid! Silakan pilih menu 1-6.\n";
            }
        }
    }

    public function lihatKaryawan() {
        if (empty($this->karyawan)) {
            echo "Belum ada data karyawan.\n";
            return;
        }

        echo "\nDaftar Karyawan:\n";
        foreach ($this->karyawan as $id => $data) {
            echo "{$id}. Nama: {$data['nama']}, Jabatan: {$data['jabatan']}\n";
        }
    }

    public function tambahKaryawan() {
        echo "\nMasukkan nama karyawan: ";
        $nama = trim(fgets(STDIN));

        echo "Pilih jabatan:\n";
        echo "1.Manajer\n2.Supervisor\n3.Staf\n";
        echo "Pilih (1-3): ";
        $pilihanJabatan = trim(fgets(STDIN));

        switch ($pilihanJabatan) {
            case '1':
                $jabatan = 'Manajer';
                break;
            case '2':
                $jabatan = 'Supervisor';
                break;
            case '3':
                $jabatan = 'Staf';
                break;
            default:
                echo "Jabatan tidak valid!\n";
                return;
        }

        $id = count($this->karyawan) + 1;
        $this->karyawan[$id] = [
            'nama' => $nama,
            'jabatan' => $jabatan
        ];
        $this->saveKaryawan();
        echo "Karyawan berhasil ditambahkan!\n";
    }

    public function updateKaryawan() {
        $this->lihatKaryawan();
        if (empty($this->karyawan)) return;

        echo "Masukkan nomor karyawan yang akan diupdate: ";
        $id = trim(fgets(STDIN));

        if (!isset($this->karyawan[$id])) {
            echo "Karyawan tidak ditemukan!\n";
            return;
        }

        echo "Masukkan nama baru (kosongkan jika tidak diubah): ";
        $nama = trim(fgets(STDIN));
        if ($nama !== "") {
            $this->karyawan[$id]['nama'] = $nama;
        }

        echo "Update jabatan? (y/t): ";
        if (trim(fgets(STDIN)) === 'y') {
            echo "Pilih jabatan baru:\n";
            echo "1. Manajer\n2. Supervisor\n3. Staf\n";
            echo "Pilih (1-3): ";
            $pilihanJabatan = trim(fgets(STDIN));

            switch ($pilihanJabatan) {
                case '1':
                    $this->karyawan[$id]['jabatan'] = 'Manajer';
                    break;
                case '2':
                    $this->karyawan[$id]['jabatan'] = 'Supervisor';
                    break;
                case '3':
                    $this->karyawan[$id]['jabatan'] = 'Staf';
                    break;
                default:
                    echo "Jabatan tidak valid!\n";
                    return;
            }
        }

        $this->saveKaryawan();
        echo "Data karyawan berhasil diupdate!\n";
    }

    public function hapusKaryawan() {
        $this->lihatKaryawan();
        if (empty($this->karyawan)) return;

        echo "Masukkan nomor karyawan yang akan dihapus: ";
        $id = trim(fgets(STDIN));

        if (!isset($this->karyawan[$id])) {
            echo "Karyawan tidak ditemukan!\n";
            return;
        }

        echo "Karyawan yang akan dihapus:\n";
        echo "Nama: {$this->karyawan[$id]['nama']}\n";
        echo "Jabatan: {$this->karyawan[$id]['jabatan']}\n";
        echo "Konfirmasi penghapusan (y/t): ";

        if (trim(fgets(STDIN)) === 'y') {
            unset($this->karyawan[$id]);
            $this->saveKaryawan();
            echo "Karyawan berhasil dihapus!\n";
        } else {
            echo "Penghapusan dibatalkan.\n";
        }
    }

    public function hitungGaji() {
        $this->lihatKaryawan();
        if (empty($this->karyawan)) return;

        echo "Masukkan nomor karyawan: ";
        $id = trim(fgets(STDIN));

        if (!isset($this->karyawan[$id])) {
            echo "Karyawan tidak ditemukan!\n";
            return;
        }

        echo "Masukkan jumlah jam kerja: ";
        $jamKerja = (int)trim(fgets(STDIN));

        if ($jamKerja < 0) {
            echo "Jumlah jam kerja tidak valid!\n";
            return;
        }

        $tarif = [
            'Manajer' => 100000,
            'Supervisor' => 75000,
            'Staf' => 50000
        ];

        $jabatan = $this->karyawan[$id]['jabatan'];
        $gajiPokok = $jamKerja * $tarif[$jabatan];

        echo "Masukkan rating kinerja (1-5): ";
        $rating = (int)trim(fgets(STDIN));

        if ($rating < 1 || $rating > 5) {
            echo "Rating tidak valid!\n";
            return;
        }

        $bonus = $gajiPokok * ($rating / 10);
        $totalGaji = $gajiPokok + $bonus;

        echo "\nHasil Perhitungan Gaji:\n";
        echo "Nama: {$this->karyawan[$id]['nama']}\n";
        echo "Jabatan: {$jabatan}\n";
        echo "Jam Kerja: {$jamKerja}\n";
        echo "Rating: {$rating}\n";
        echo "Gaji Pokok: Rp " . number_format($gajiPokok, 0, ',', '.') . "\n";
        echo "Bonus: Rp " . number_format($bonus, 0, ',', '.') . "\n";
        echo "Total Gaji: Rp " . number_format($totalGaji, 0, ',', '.') . "\n";
    }
}

// Menjalankan aplikasi
$app = new GajiKaryawan();
$app->tampilkanMenu();
