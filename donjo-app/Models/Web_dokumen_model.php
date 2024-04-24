<?php

namespace App\Models;

use CodeIgniter\Model as CI_Model;

use App\Libraries\Paging;

class Web_dokumen_model extends CI_Model
{
    public function autocomplete()
    {
        $sql = 'SELECT satuan FROM dokumen WHERE id_pend = 0
					UNION SELECT nama FROM dokumen WHERE id_pend = 0';
        $query = $this->db->query($sql);
        $data  = $query->getResultArray();

        $i    = 0;
        $outp = '';

        while ($i < count($data)) {
            $outp .= ",'" . $data[$i]['satuan'] . "'";
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
            $search_sql = " AND (satuan LIKE '{$kw}' OR nama LIKE '{$kw}')";

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

        $sql = 'SELECT COUNT(id) AS id FROM dokumen WHERE id_pend = 0 ';
        $sql .= $this->search_sql();
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
            case 1: $order_sql = ' ORDER BY nama';
                break;

            case 2: $order_sql = ' ORDER BY nama DESC';
                break;

            case 3: $order_sql = ' ORDER BY enabled';
                break;

            case 4: $order_sql = ' ORDER BY enabled DESC';
                break;

            case 5: $order_sql = ' ORDER BY tgl_upload';
                break;

            case 6: $order_sql = ' ORDER BY tgl_upload DESC';
                break;

            default:$order_sql = ' ORDER BY id';
        }
        $paging_sql = ' LIMIT ' . $offset . ',' . $limit;

        $sql = 'SELECT * FROM dokumen WHERE id_pend = 0 ';

        $sql .= $this->search_sql();
        $sql .= $this->filter_sql();
        $sql .= $order_sql;
        $sql .= $paging_sql;

        $query = $this->db->query($sql);
        $data  = null;
        if ($query) {
            $data = $query->getResultArray();
        }

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
        $lokasi_file = $_FILES['satuan']['tmp_name'];
        $nama_file   = $_FILES['satuan']['name'];
        if (! empty($lokasi_file)) {
            UploadDocument(underscore($nama_file));
            $data           = $_POST;
            $data['satuan'] = underscore($nama_file);
            $outp           = $this->db->insert('dokumen', $data);
            if ($outp) {
                $_SESSION['success'] = 1;
            }
        } else {
            $_SESSION['success'] = -1;
        }
    }

    public function update($id = 0)
    {
        $data        = $_POST;
        $lokasi_file = $_FILES['satuan']['tmp_name'];
        $nama_file   = $_FILES['satuan']['name'];
        $old_file    = $data['old_file'];
        if (! empty($lokasi_file)) {
            UploadDocument($nama_file);
            unset($data['old_file']);
        } else {
            $_SESSION['success'] = -1;
            $nama_file           = $data['old_file'];
        }
        $data['satuan'] = underscore($nama_file);
        $this->db->where('id', $id);
        $outp = $this->db->update('dokumen', $data);
        if ($outp) {
            $_SESSION['success'] = 1;
        } else {
            $_SESSION['success'] = -1;
        }
    }

    public function delete($id = '')
    {
        $sql  = 'DELETE FROM dokumen WHERE id=?';
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
                $sql  = 'DELETE FROM dokumen WHERE id=?';
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

    public function dokumen_lock($id = '', $val = 0)
    {
        $sql  = 'UPDATE dokumen SET enabled=? WHERE id=?';
        $outp = $this->db->query($sql, [$val, $id]);

        if ($outp) {
            $_SESSION['success'] = 1;
        } else {
            $_SESSION['success'] = -1;
        }
    }

    public function get_dokumen($id = 0)
    {
        $sql   = 'SELECT * FROM dokumen WHERE id=?';
        $query = $this->db->query($sql, $id);

        return $query->getRowArray();
    }

    public function dokumen_show()
    {
        $sql   = 'SELECT * FROM dokumen WHERE enabled=?';
        $query = $this->db->query($sql, 1);

        return $query->getResultArray();
    }
}
