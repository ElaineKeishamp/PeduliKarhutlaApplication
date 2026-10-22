<?php

if (! function_exists('rupiah')) {
    /** Tampilkan nilai uang bulat dalam format rupiah Indonesia. */
    function rupiah(int|float|string|null $angka): string
    {
        return 'Rp '.number_format((float) ($angka ?? 0), 0, ',', '.');
    }
}
