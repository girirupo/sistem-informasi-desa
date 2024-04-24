<?php

namespace App\Models;

use CodeIgniter\Model as CI_Model;

class KategoriModel extends CI_Model
{
    protected $table = 'kategori';

    /**
     * Ambil nama kategori berdasarkan ID kategori
     *
     * @param int $id ID kategori
     *
     * @return array|false False jika tidak ditemukan
     */
    public function get(int $id)
    {
        $x = $this->builder()
            ->select('kategori')
            ->where('id', $id);

        if ($x->get()->getNumRows() > 0) {
            return $x->get()->getFirstRow('array')['kategori'];
        }

        return false;
    }

    /**
     * Ambil data semua kategori dan diurutkan berdasarkan 'urut'
     *
     * @return array
     */
    public function getList()
    {
        $query = $this->db->order_by('urut', 'ASC')->get($this->table);

        return $query->getResultArray();
    }

    /**
     * Ambil data kategori berdasarkan tipe
     *
     * @param int $tipe Tipe Kategori
     *
     * @return array
     */
    public function getByType(int $tipe = 1)
    {
        $query = $this->db->where('tipe', $tipe)->get($this->table);

        return $query->getResultArray();
    }

    /**
     * Hapus kategori
     *
     * @param int $id ID Kategori
     *
     * @return void
     */
    public function hapus(int $id)
    {
        $this->db->delete($this->table, ['id' => $id]);

        if ($this->db->affected_rows() > -1) {
            $_SESSION['success'] = 1;
        } else {
            $_SESSION['success'] = -1;
        }
    }
}
