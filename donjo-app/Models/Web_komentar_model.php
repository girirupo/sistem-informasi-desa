<?php

namespace App\Models;

use CodeIgniter\Model as CI_Model;

use App\Libraries\Paging;

class Web_komentar_model extends CI_Model
{
    public function autocomplete()
    {
        $sql   = 'SELECT tgl_upload, owner, email, komentar FROM komentar';
        $query = $this->db->query($sql);
        $data  = $query->getResultArray();

        $i    = 0;
        $outp = '';

        while ($i < count($data)) {
            $outp .= ",'" . $data[$i]['komentar'] . "'";
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
            $search_sql = " AND (komentar LIKE '{$kw}' OR komentar LIKE '{$kw}')";

            return $search_sql;
        }
    }

    public function filter_sql()
    {
        if (isset($_SESSION['filter'])) {
            $kf         = $_SESSION['filter'];
            $filter_sql = " AND enabled = {$kf}";

            return $filter_sql;
        }
    }

    public function paging($p = 1, $o = 0)
    {
        $paging = new Paging();

        $sql = 'SELECT COUNT(id) AS id FROM komentar WHERE 1';
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

    public function list_data($o = 0, $offset = 0, $limit = 500, $cas = 0)
    {
        switch ($o) {
            case 1: $order_sql = ' ORDER BY tgl_upload DESC';
                break;

            case 2: $order_sql = ' ORDER BY owner';
                break;

            case 3: $order_sql = ' ORDER BY email';
                break;

            case 4: $order_sql = ' ORDER BY komentar';
                break;

            default:$order_sql = ' ORDER BY tgl_upload DESC';
        }
        $paging_sql = ' LIMIT ' . $offset . ',' . $limit;

        $sql = 'SELECT * FROM komentar WHERE 1 ';
        if ($cas === 2) {
            $sql .= ' AND id_artikel = 775';
        } else {
            $sql .= ' AND id_artikel <> 775';
        }

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

            if ($data[$i]['enabled'] === 1) {
                $data[$i]['aktif'] = 'Yes';
            } else {
                $data[$i]['aktif'] = 'No';
            }

            $i++;
            $j++;
        }

        return $data;
    }

    public function insert()
    {
        $data            = $_POST;
        $data['id_user'] = $_SESSION['user'];
        $outp            = $this->db->insert('komentar', $data);
        if ($outp) {
            $_SESSION['success'] = 1;
        } else {
            $_SESSION['success'] = -1;
        }
    }

    public function update($id = 0)
    {
        $data = $_POST;

        $this->db->where('id', $id);
        $outp = $this->db->update('komentar', $data);
        if ($outp) {
            $_SESSION['success'] = 1;
        } else {
            $_SESSION['success'] = -1;
        }
    }

    public function delete($id = '')
    {
        $sql  = 'DELETE FROM komentar WHERE id=?';
        $outp = $this->db->query($sql, [$id]);

        if ($outp) {
            $_SESSION['success'] = 1;
        } else {
            $_SESSION['success'] = -1;
        }
    }

    public function delete_all()
    {
        $id_cb = $_POST['id_cb'];

        if (count($id_cb)) {
            foreach ($id_cb as $id) {
                $sql  = 'DELETE FROM komentar WHERE id=?';
                $outp = $this->db->query($sql, [$id]);
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

    public function komentar_lock($id = '', $val = 0)
    {
        $sql  = 'UPDATE komentar SET enabled=? WHERE id=?';
        $outp = $this->db->query($sql, [$val, $id]);

        if ($outp) {
            $_SESSION['success'] = 1;
        } else {
            $_SESSION['success'] = -1;
        }
    }

    public function get_komentar($id = 0)
    {
        $sql   = 'SELECT a.* FROM komentar a WHERE a.id=?';
        $query = $this->db->query($sql, $id);

        return $query->getRowArray();
    }

    public function komentar_show()
    {
        $sql   = 'SELECT a.*,u.nama AS owner FROM komentar a LEFT JOIN user u ON a.id_user = u.id WHERE enabled=? ORDER BY a.tgl_upload DESC LIMIT 6';
        $query = $this->db->query($sql, 1);
        $data  = $query->getResultArray();

        $i = 0;

        while ($i < count($data)) {
            $id = $data[$i]['id'];

            $pendek                = str_split($data[$i]['isi'], 100);
            $data[$i]['isi_short'] = $pendek[0];
            $panjang               = str_split($data[$i]['isi'], 150);
            $data[$i]['isi']       = '<label>' . $panjang[0] . "...</label><a href='" . site_url("first/komentar/{$id}") . "'>Baca Selengkapnya</a>";
            $i++;
        }

        return $data;
    }

    public function insert_comment($id = 0)
    {
        $data = $_POST;

        $data['id_komentar'] = $id;
        $outp                = $this->db->insert('komentar', $data);

        if ($outp) {
            $_SESSION['success'] = 1;
        } else {
            $_SESSION['success'] = -1;
        }
    }

    public function list_komentar($id = 0)
    {
        $sql   = 'SELECT * FROM komentar WHERE id_komentar = ? ORDER BY tgl_upload DESC';
        $query = $this->db->query($sql, $id);
        $data  = $query->getResultArray();

        $i = 0;

        while ($i < count($data)) {
            $i++;
        }

        return $data;
    }
}
