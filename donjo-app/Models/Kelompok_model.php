<?php

namespace App\Models;

use CodeIgniter\Model as CI_Model;

use App\Libraries\Paging;

class Kelompok_model extends CI_Model
{
    public function autocomplete()
    {
        $sql   = 'SELECT nama FROM kelompok';
        $query = $this->db->query($sql);
        $data  = $query->getResultArray();

        $i    = 0;
        $outp = '';

        while ($i < count($data)) {
            $outp .= ",'" . $data[$i]['nama'] . "'";
            $i++;
        }
        $outp = strtolower(substr($outp, 1));

        return '[' . $outp . ']';
    }

    public function search_sql()
    {
        if (isset($_SESSION['cari'])) {
            $cari       = $_SESSION['cari'];
            $kw         = $this->db->escape_like_str($cari);
            $kw         = '%' . $kw . '%';
            $search_sql = " AND (u.nama LIKE '{$kw}' OR u.nama LIKE '{$kw}')";

            return $search_sql;
        }
    }

    public function filter_sql()
    {
        if (isset($_SESSION['filter'])) {
            $kf         = $_SESSION['filter'];
            $filter_sql = " AND u.id_master = {$kf}";

            return $filter_sql;
        }
    }

    public function paging($p = 1, $o = 0)
    {
        $paging = new Paging();

        $sql = 'SELECT COUNT(id) AS id FROM kelompok u WHERE 1';
        $sql .= $this->search_sql();
        $sql .= $this->filter_sql();

        $query    = $this->db->query($sql);
        $row      = $query->getRowArray();
        $jml_data = $row['id'];

        $cfg['page']     = $p;
        $cfg['per_page'] = $_SESSION['per_page'];
        $cfg['num_rows'] = $jml_data;

        $paging->init($cfg);

        return $paging;
    }

    public function list_data($o = 0, $offset = 0, $limit = 500)
    {
        switch ($o) {
            case 1: $order_sql = ' ORDER BY u.nama';
                break;

            case 2: $order_sql = ' ORDER BY u.nama DESC';
                break;

            case 3: $order_sql = ' ORDER BY u.nama';
                break;

            case 4: $order_sql = ' ORDER BY u.nama DESC';
                break;

            case 5: $order_sql = ' ORDER BY g.nama';
                break;

            case 6: $order_sql = ' ORDER BY g.nama DESC';
                break;

            default:$order_sql = ' ORDER BY u.nama';
        }

        $paging_sql = ' LIMIT ' . $offset . ',' . $limit;

        $sql = 'SELECT u.*,s.kelompok AS master,c.nama AS ketua,(SELECT COUNT(id) FROM kelompok_anggota WHERE id_kelompok = u.id) AS jml_anggota FROM kelompok u LEFT JOIN kelompok_master s ON u.id_master = s.id LEFT JOIN tweb_penduduk c ON u.id_ketua = c.id WHERE 1 ';

        $sql .= $this->search_sql();
        $sql .= $this->filter_sql();

        $sql .= $order_sql;
        $sql .= $paging_sql;

        $query = $this->db->query($sql);
        $data  = $query->getResultArray();

        $i = 0;
        $j = $offset;

        while ($i < count($data)) {
            $data[$i]['no'] = $j + 1;
            $i++;
            $j++;
        }

        return $data;
    }

    public function insert()
    {
        $data  = $_POST;
        $datax = [];

        $outpa     = $this->db->insert('kelompok', $data);
        $insert_id = $this->db->insert_id();

        $datax['id_kelompok'] = $insert_id;
        $datax['id_penduduk'] = $data['id_ketua'];
        $outpb                = $this->db->insert('kelompok_anggota', $datax);

        if ($outpa && $outpb) {
            $_SESSION['success'] = 1;
        } else {
            $_SESSION['success'] = -1;
        }
    }

    public function insert_a($id = 0)
    {
        $data                = $_POST;
        $data['id_kelompok'] = $id;

        $sql   = 'SELECT id FROM kelompok_anggota WHERE id_kelompok = ? AND id_penduduk = ?';
        $query = $this->db->query($sql, [$data['id_kelompok'], $data['id_penduduk']]);
        $kel   = $query->getRowArray();

        if (! $kel) {
            $outp = $this->db->insert('kelompok_anggota', $data);
        }

        if ($outp) {
            $_SESSION['success'] = 1;
        } else {
            $_SESSION['success'] = -1;
        }
    }

    public function update($id = 0)
    {
        $data = $_POST;
        if ($data['id_ketua'] === '') {
            unset($data['id_ketua']);
        }

        $this->db->where('id', $id);
        $outp = $this->db->update('kelompok', $data);
        if ($outp) {
            $_SESSION['success'] = 1;
        } else {
            $_SESSION['success'] = -1;
        }
    }

    public function update_a($id = 0, $id_a = 0)
    {
        $data = $_POST;

        $this->db->where('id_kelompok', $id);
        $this->db->where('id_penduduk', $id_a);
        $outp = $this->db->update('kelompok_anggota', $data);
        if ($outp) {
            $_SESSION['success'] = 1;
        } else {
            $_SESSION['success'] = -1;
        }
    }

    public function delete($id = '')
    {
        $sql  = 'DELETE FROM kelompok WHERE id=?';
        $outp = $this->db->query($sql, [$id]);

        if ($outp) {
            $_SESSION['success'] = 1;
        } else {
            $_SESSION['success'] = -1;
        }
    }

    public function delete_a($id = '')
    {
        $sql  = 'DELETE FROM kelompok_anggota WHERE id=?';
        $outp = $this->db->query($sql, [$id]);

        if ($outp) {
            $_SESSION['success'] = 1;
        } else {
            $_SESSION['success'] = -1;
        }
    }

    public function delete_all()
    {
        if (isset($_POST['id_cb']) && count($_POST['id_cb']) > 0) {
            foreach ($_POST['id_cb'] as $id_kelompok) {
                $sql  = 'DELETE FROM kelompok WHERE id=?';
                $outp = $this->db->query($sql, [$id_kelompok]);
            }
        } else {
            $outp = false;
        }

        if ($outp) {
            $_SESSION['success'] = 1;
        } else {
            $_SESSION['success'] = -1;
        }
    }

    public function get_kelompok($id = 0)
    {
        $sql   = 'SELECT * FROM kelompok WHERE id=?';
        $query = $this->db->query($sql, $id);

        return $query->getRowArray();
    }

    public function get_anggota($id = 0, $id_a = 0)
    {
        $sql   = 'SELECT * FROM kelompok_anggota WHERE id_kelompok=? AND id_penduduk = ?';
        $query = $this->db->query($sql, [$id, $id_a]);

        return $query->getRowArray();
    }

    public function list_master()
    {
        $sql   = 'SELECT * FROM kelompok_master';
        $query = $this->db->query($sql);

        return $query->getResultArray();
    }

    public function list_penduduk()
    {
        $sql   = 'SELECT id,nik,nama FROM tweb_penduduk WHERE status_dasar = 1';
        $query = $this->db->query($sql);
        $data  = $query->getResultArray();

        $i = 0;

        while ($i < count($data)) {
            $data[$i]['alamat'] = 'Alamat :' . $data[$i]['nama'];
            $i++;
        }

        return $data;
    }

    public function list_anggota($id = 0)
    {
        $sql   = "SELECT u.*,p.nik,p.nama,p.sex,(SELECT DATE_FORMAT(FROM_DAYS(TO_DAYS(NOW())-TO_DAYS(tanggallahir)), '%Y')+0 FROM tweb_penduduk WHERE id = p.id) AS umur,a.dusun,a.rw,a.rt FROM kelompok_anggota u LEFT JOIN tweb_penduduk p ON u.id_penduduk = p.id LEFT JOIN tweb_wil_clusterdesa a ON p.id_cluster = a.id WHERE id_kelompok = ?";
        $query = $this->db->query($sql, $id);
        $data  = $query->getResultArray();

        $i = 0;

        while ($i < count($data)) {
            $data[$i]['no']     = $i + 1;
            $data[$i]['alamat'] = 'Dusun ' . $data[$i]['dusun'] . ' RW' . $data[$i]['rw'] . ' RT' . $data[$i]['rt'];
            $i++;
        }

        return $data;
    }
}
