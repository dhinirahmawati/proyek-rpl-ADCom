<?php

class Flasher {
    public static function setFlash($arg1, $arg2 = '', $arg3 = '') {
        if ($arg3 === '') {
            // Dipanggil dengan 2 parameter: setFlash($pesan, $tipe)
            $pesan = $arg1;
            $aksi  = '';
            $tipe  = !empty($arg2) ? $arg2 : 'info';
        } else {
            // Dipanggil dengan 3 parameter: setFlash($pesan, $aksi, $tipe)
            $pesan = $arg1;
            $aksi  = $arg2;
            $tipe  = $arg3;
        }

        $_SESSION['flash'] = [
            'pesan' => $pesan,
            'aksi'  => $aksi,
            'tipe'  => $tipe
        ];
    }

    public static function flash() {
        if (isset($_SESSION['flash'])) {
            $pesan = $_SESSION['flash']['pesan'];
            $aksi  = $_SESSION['flash']['aksi'];
            $tipe  = $_SESSION['flash']['tipe'];

            $messageText = '';
            if (!empty($aksi)) {
                if (strtolower($pesan) === 'berhasil' || strtolower($pesan) === 'gagal') {
                    $messageText = 'Data <strong>' . htmlspecialchars($pesan) . '</strong> ' . htmlspecialchars($aksi);
                } else {
                    $messageText = '<strong>' . htmlspecialchars($pesan) . '</strong> ' . htmlspecialchars($aksi);
                }
            } else {
                $messageText = htmlspecialchars($pesan);
            }

            echo '<div class="alert alert-' . htmlspecialchars($tipe) . ' alert-dismissible fade show mt-3 shadow-sm" role="alert">
                    ' . $messageText . '
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                  </div>';
            unset($_SESSION['flash']);
        }
    }
}
