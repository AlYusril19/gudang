<?php
// Daftarkan helper di composer.json
// dan jalankan menggunakan composer dump-autoload
if (!function_exists('formatRupiah')) {
    function formatRupiah($amount)
    {
        return 'Rp ' . number_format($amount, 0, ',', '.');
    }
}
