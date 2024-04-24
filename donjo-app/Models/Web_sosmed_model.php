<?php

namespace App\Models;

use CodeIgniter\Model as CI_Model;

class Web_sosmed_model extends CI_Model
{
    public function get_sosmed($id = 0)
    {
        $sql   = 'SELECT * FROM media_sosial WHERE id = ?';
        $query = $this->db->query($sql, $id);

        return $query->getRowArray();
    }

    public function list_sosmed()
    {
        $sql   = 'SELECT * FROM media_sosial WHERE 1';
        $query = $this->db->query($sql);

        return $query->getResultArray();
    }

    public function update($id = 0)
    {
        $data = $_POST;

        $sql   = 'SELECT * FROM media_sosial WHERE id =? ';
        $query = $this->db->query($sql, $id);
        $hasil = $query->getResultArray();

        if ($hasil) {
            $this->db->where('id', $id);
            $outp = $this->db->update('media_sosial', $data);
        } else {
            $outp = $this->db->insert('media_sosial', $data);
        }

        if ($outp) {
            $_SESSION['success'] = 1;
        } else {
            $_SESSION['success'] = -1;
        }
    }
}
