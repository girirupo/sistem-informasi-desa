<?php

namespace App\Models;

use CodeIgniter\Model as CI_Model;

use App\Libraries\Paging;

class Mandiri_model extends CI_Model
{
    public function autocomplete()
    {
        $sql   = 'SELECT nik FROM tweb_penduduk_mandiri';
        $query = $this->db->query($sql);
        $data  = $query->getResultArray();

        $i    = 0;
        $outp = '';

        while ($i < count($data)) {
            $outp .= ",'" . $data[$i]['nik'] . "'";
            $i++;
        }
        $outp = substr($outp, 1);

        return '[' . $outp . ']';
    }

    public function search_sql()
    {
        if (isset($_SESSION['cari'])) {
            $cari       = $_SESSION['cari'];
            $kw         = $this->db->escape_like_str($cari);
            $kw         = '%' . $kw . '%';
            $search_sql = " AND (u.nik LIKE '{$kw}' OR n.nama LIKE '{$kw}')";

            return $search_sql;
        }
    }

    public function filter_sql()
    {
        if (isset($_SESSION['nik'])) {
            $kf = $_SESSION['nik'];
            if ($kf === '0') {
                $filter_sql = '';
            } else {
                $filter_sql = " AND n.id = '" . $kf . "'";
            }

            return $filter_sql;
        }
    }

    public function filterku_sql($nik = 0)
    {
        $kf = $nik;
        if ($kf === 0) {
            $filterku_sql = '';
        } else {
            $filterku_sql = " AND u.id_pend = '" . $kf . "'";
        }

        return $filterku_sql;
    }

    public function paging($p = 1, $o = 0)
    {
        $paging = new Paging();

        $sql = 'SELECT COUNT(id) AS id FROM tweb_penduduk_mandiri u
			LEFT JOIN tweb_penduduk n ON u.nik = n.nik
			WHERE 1';
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
            case 1: $order_sql = ' ORDER BY u.last_login';
                break;

            case 2: $order_sql = ' ORDER BY u.last_login DESC';
                break;

            default:$order_sql = ' ORDER BY u.tanggal_buat';
        }

        $paging_sql = ' LIMIT ' . $offset . ',' . $limit;

        $sql = 'SELECT u.*,n.nama AS nama, n.nik AS nik
			FROM tweb_penduduk_mandiri u
			LEFT JOIN tweb_penduduk n ON u.nik = n.nik
			WHERE 1 ';

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

    public function generate_pin($pin = '')
    {
        if ($pin === '') {
            $pin = mt_rand(100000, 999999);
            $pin = strrev($pin);
        }

        return $pin;
    }

    public function insert()
    {
        if ($_POST['nik'] === '') {
            redirect('mandiri');
        }

        $sql  = 'DELETE FROM tweb_penduduk_mandiri WHERE nik=?';
        $outp = $this->db->query($sql, [$_POST['nik']]);

        $rpin        = $this->generate_pin($_POST['pin']);
        $hash_pin    = hash_pin($rpin);
        $data['pin'] = $hash_pin;
        $data['nik'] = $_POST['nik'];

        $outp = $this->db->insert('tweb_penduduk_mandiri', $data);

        if ($_POST['pin'] !== '') {
            return $_POST['pin'];
        }

        return $rpin;
    }

    public function delete($id = '')
    {
        $sql  = 'DELETE FROM tweb_penduduk_mandiri WHERE id=?';
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
                $sql  = 'DELETE FROM tweb_penduduk_mandiri WHERE id=?';
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

    public function list_penduduk()
    {
        $sql   = "SELECT nik AS id,nik,nama FROM tweb_penduduk WHERE status = 1 AND nik<>'' ";
        $query = $this->db->query($sql);
        $data  = $query->getResultArray();

        $i = 0;

        while ($i < count($data)) {
            $data[$i]['alamat'] = 'Alamat :' . $data[$i]['nama'];
            $i++;
        }

        return $data;
    }

    public function update_setting($id = 0)
    {
        $password   = md5($this->input->post('pass_lama'));
        $pass_baru  = $this->input->post('pass_baru');
        $pass_baru1 = $this->input->post('pass_baru1');
        $nama       = $this->input->post('nama');

        $sql   = 'SELECT password,id_grup,session FROM user WHERE id=?';
        $query = $this->db->query($sql, [$id]);
        $row   = $query->row();

        if ($password === $row->password) {
            if ($pass_baru === $pass_baru1) {
                $pass_baru = md5($pass_baru);
                $sql       = 'UPDATE user SET password=?,nama=? WHERE id=?';
                $outp      = $this->db->query($sql, [$pass_baru, $nama, $id]);
            }
        }

        if ($outp) {
            $_SESSION['success'] = 1;
        } else {
            $_SESSION['success'] = -1;
        }
    }

    public function list_grup()
    {
        $sql   = 'SELECT * FROM user_grup';
        $query = $this->db->query($sql);

        return $query->getResultArray();
    }
}
