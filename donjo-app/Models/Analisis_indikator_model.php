<?php

namespace App\Models;

use CodeIgniter\Model as CI_Model;

use App\Libraries\Paging;

class Analisis_indikator_model extends CI_Model
{
    public function autocomplete()
    {
        $sql   = 'SELECT pertanyaan FROM analisis_indikator';
        $query = $this->db->query($sql);
        $data  = $query->getResultArray();

        $i    = 0;
        $outp = '';

        while ($i < count($data)) {
            $outp .= ",'" . $data[$i]['pertanyaan'] . "'";
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
            $search_sql = " AND (u.pertanyaan LIKE '{$kw}' OR u.pertanyaan LIKE '{$kw}')";

            return $search_sql;
        }
    }

    public function filter_sql()
    {
        if (isset($_SESSION['filter'])) {
            $kf         = $_SESSION['filter'];
            $filter_sql = " AND u.act_analisis = {$kf}";

            return $filter_sql;
        }
    }

    public function master_sql()
    {
        if (isset($_SESSION['analisis_master'])) {
            $kf         = $_SESSION['analisis_master'];
            $filter_sql = " AND u.id_master = {$kf}";

            return $filter_sql;
        }
    }

    public function tipe_sql()
    {
        if (isset($_SESSION['tipe'])) {
            $kf         = $_SESSION['tipe'];
            $filter_sql = " AND u.id_tipe = {$kf}";

            return $filter_sql;
        }
    }

    public function kategori_sql()
    {
        if (isset($_SESSION['kategori'])) {
            $kf         = $_SESSION['kategori'];
            $filter_sql = " AND u.id_kategori = {$kf}";

            return $filter_sql;
        }
    }

    public function paging($p = 1, $o = 0)
    {
        $paging = new Paging();

        $sql = 'SELECT COUNT(id) AS id FROM analisis_indikator u WHERE 1';
        $sql .= $this->search_sql();
        $sql .= $this->filter_sql();
        $sql .= $this->master_sql();
        $sql .= $this->tipe_sql();
        $sql .= $this->kategori_sql();
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
            case 1: $order_sql = ' ORDER BY u.nomor';
                break;

            case 2: $order_sql = ' ORDER BY u.nomor DESC';
                break;

            case 3: $order_sql = ' ORDER BY u.pertanyaan';
                break;

            case 4: $order_sql = ' ORDER BY u.pertanyaan DESC';
                break;

            case 5: $order_sql = ' ORDER BY u.id_kategori';
                break;

            case 6: $order_sql = ' ORDER BY u.id_kategori DESC';
                break;

            default:$order_sql = ' ORDER BY u.nomor';
        }

        $paging_sql = ' LIMIT ' . $offset . ',' . $limit;

        $sql = 'SELECT u.*,t.tipe AS tipe_indikator,k.kategori AS kategori FROM analisis_indikator u LEFT JOIN analisis_tipe_indikator t ON u.id_tipe = t.id LEFT JOIN analisis_kategori_indikator k ON u.id_kategori = k.id WHERE 1 ';

        $sql .= $this->search_sql();
        $sql .= $this->filter_sql();
        $sql .= $this->master_sql();
        $sql .= $this->tipe_sql();
        $sql .= $this->kategori_sql();
        $sql .= $order_sql;
        $sql .= $paging_sql;

        $query = $this->db->query($sql);
        $data  = $query->getResultArray();

        $i = 0;
        $j = $offset;

        while ($i < count($data)) {
            $data[$i]['no'] = $j + 1;

            if ($data[$i]['act_analisis'] === 1) {
                $data[$i]['act_analisis'] = 'Ya';
            } else {
                $data[$i]['act_analisis'] = 'Tidak
			';
            }
            $i++;
            $j++;
        }

        return $data;
    }

    public function insert()
    {
        $data = $_POST;
        if ($data['id_tipe'] !== 1) {
            $data['act_analisis'] = 2;
            $data['bobot']        = 0;
        }

        $data['id_master'] = $_SESSION['analisis_master'];
        $outp              = $this->db->insert('analisis_indikator', $data);

        if ($outp) {
            $_SESSION['success'] = 1;
        } else {
            $_SESSION['success'] = -1;
        }
    }

    public function update($id = 0)
    {
        $data = $_POST;
        if ($data['id_tipe'] !== 1) {
            $data['act_analisis'] = 2;
            $data['bobot']        = 0;
        }

        if ($data['id_tipe'] === 3 || $data['id_tipe'] === 4) {
            $sql = 'DELETE FROM analisis_parameter WHERE id_indikator=?';
            $this->db->query($sql, $id);
        }

        $data['id_master'] = $_SESSION['analisis_master'];
        $this->db->where('id', $id);
        $outp = $this->db->update('analisis_indikator', $data);
        if ($outp) {
            $_SESSION['success'] = 1;
        } else {
            $_SESSION['success'] = -1;
        }
    }

    public function delete($id = '')
    {
        $sql  = 'DELETE FROM analisis_indikator WHERE id=?';
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
                $sql  = 'DELETE FROM analisis_indikator WHERE id=?';
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

    public function p_insert($in = '')
    {
        $data                 = $_POST;
        $data['id_indikator'] = $in;
        $outp                 = $this->db->insert('analisis_parameter', $data);

        if ($outp) {
            $_SESSION['success'] = 1;
        } else {
            $_SESSION['success'] = -1;
        }
    }

    public function p_update($id = 0)
    {
        $data = $_POST;
        $this->db->where('id', $id);
        $outp = $this->db->update('analisis_parameter', $data);
        if ($outp) {
            $_SESSION['success'] = 1;
        } else {
            $_SESSION['success'] = -1;
        }
    }

    public function p_delete($id = '')
    {
        $sql  = 'DELETE FROM analisis_parameter WHERE id=?';
        $outp = $this->db->query($sql, [$id]);

        if ($outp) {
            $_SESSION['success'] = 1;
        } else {
            $_SESSION['success'] = -1;
        }
    }

    public function p_delete_all()
    {
        $id_cb = $_POST['id_cb'];

        if (count($id_cb)) {
            foreach ($id_cb as $id) {
                $sql  = 'DELETE FROM analisis_parameter WHERE id=?';
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

    public function list_indikator($id = 0)
    {
        $sql   = 'SELECT * FROM analisis_parameter WHERE id_indikator = ?';
        $query = $this->db->query($sql, $id);
        $data  = $query->getResultArray();

        $i = 0;

        while ($i < count($data)) {
            $data[$i]['no'] = $i + 1;

            $i++;
        }

        return $data;
    }

    public function get_analisis_indikator($id = 0)
    {
        $sql   = 'SELECT * FROM analisis_indikator WHERE id=?';
        $query = $this->db->query($sql, $id);

        return $query->getRowArray();
    }

    public function get_analisis_master()
    {
        $sql   = 'SELECT * FROM analisis_master WHERE id=?';
        $query = $this->db->query($sql, $_SESSION['analisis_master']);

        return $query->getRowArray();
    }

    public function get_analisis_parameter($id = '')
    {
        $sql   = 'SELECT * FROM analisis_parameter WHERE id=?';
        $query = $this->db->query($sql, $id);

        return $query->getRowArray();
    }

    public function list_tipe()
    {
        $sql   = 'SELECT * FROM analisis_tipe_indikator';
        $query = $this->db->query($sql);

        return $query->getResultArray();
    }

    public function list_kategori()
    {
        $sql = 'SELECT u.* FROM analisis_kategori_indikator u WHERE 1';
        $sql .= $this->master_sql();
        $query = $this->db->query($sql);

        return $query->getResultArray();
    }
}
