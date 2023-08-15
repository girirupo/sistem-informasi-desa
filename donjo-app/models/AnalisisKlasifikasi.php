<?php

namespace App\Models;

use App\Models\BaseModel as Model;

class AnalisisKlasifikasi extends Model
{
    protected $table = 'analisis_klasifikasi';

    /**
     * Fungsi ini digunakan untuk menghasilkan data untuk proses autocomplete.
     */
    public function autocomplete(): string
    {
        // Mengambil data nama dari tabel
        $query = $this->db->select('nama')->get($this->table);
        $data  = $query->result_array();

        $i    = 0;
        $outp = '';

        // Menggabungkan nama-nama dalam format autocomplete
        while ($i < count($data)) {
            $outp .= ',"' . $data[$i]['nama'] . '"';
            $i++;
        }
        $outp = strtolower(substr($outp, 1));

        return '[' . $outp . ']';
    }
}
