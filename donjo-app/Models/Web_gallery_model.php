<?php

namespace App\Models;

use CodeIgniter\Model as CI_Model;

use App\Libraries\Paging;

class Web_gallery_model extends CI_Model
{
    public function autocomplete()
    {
        $sql = 'SELECT gambar FROM gambar_gallery
					UNION SELECT nama FROM gambar_gallery';
        $query = $this->db->query($sql);
        $data  = $query->getResultArray();

        $i    = 0;
        $outp = '';

        while ($i < count($data)) {
            $outp .= ",'" . $data[$i]['gambar'] . "'";
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
            $search_sql = " AND (gambar LIKE '{$kw}' OR nama LIKE '{$kw}')";

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

        $sql = 'SELECT COUNT(id) AS id FROM gambar_gallery WHERE tipe = 0 ';
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

        $sql = 'SELECT * FROM gambar_gallery WHERE tipe = 0 ';

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
        $lokasi_file = $_FILES['gambar']['tmp_name'];
        $tipe_file   = $_FILES['gambar']['type'];
        $nama_file   = $_FILES['gambar']['name'];
        if (! empty($lokasi_file)) {
            if ($tipe_file === 'image/jpeg' || $tipe_file === 'image/pjpeg') {
                UploadGallery($nama_file);
                $data           = $_POST;
                $data['gambar'] = $nama_file;

                if ($_SESSION['grup'] === 4) {
                    $data['enabled'] = 2;
                }

                $outp = $this->db->insert('gambar_gallery', $data);
                if ($outp) {
                    $_SESSION['success'] = 1;
                }
            } else {
                $_SESSION['success'] = -1;
            }
        }
    }

    public function update($id = 0)
    {
        $x           = $_POST;
        $lokasi_file = $_FILES['gambar']['tmp_name'];
        $tipe_file   = $_FILES['gambar']['type'];
        $nama_file   = $_FILES['gambar']['name'];
        $old_gambar  = $x['old_gambar'];
        if (! empty($lokasi_file)) {
            if ($tipe_file === 'image/jpeg' || $tipe_file === 'image/pjpeg') {
                UploadGallery($nama_file);
                unset($x['old_gambar']);
            }
        } else {
            $_SESSION['success'] = -1;
            $nama_file           = $x['old_gambar'];
        }

        $data['gambar'] = $nama_file;
        $data['nama']   = $_POST['nama'];
        $this->db->where('id', $id);
        $outp = $this->db->update('gambar_gallery', $data);
        if ($outp) {
            $_SESSION['success'] = 1;
        } else {
            $_SESSION['success'] = -1;
        }
    }

    public function delete($id = '')
    {
        $sql  = 'DELETE FROM gambar_gallery WHERE id=?';
        $outp = $this->db->query($sql, [$id]);

        $sql  = 'DELETE FROM gambar_gallery WHERE parrent=?';
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
                $sql  = 'DELETE FROM gambar_gallery WHERE id=?';
                $outp = $this->db->query($sql, [$id]);

                $sql  = 'DELETE FROM gambar_gallery WHERE parrent=?';
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

    public function gallery_lock($id = '', $val = 0)
    {
        $sql  = 'UPDATE gambar_gallery SET enabled=? WHERE id=?';
        $outp = $this->db->query($sql, [$val, $id]);

        if ($outp) {
            $_SESSION['success'] = 1;
        } else {
            $_SESSION['success'] = -1;
        }
    }

    public function get_gallery($id = 0)
    {
        $sql   = 'SELECT * FROM gambar_gallery WHERE id=?';
        $query = $this->db->query($sql, $id);

        return $query->getRowArray();
    }

    public function gallery_show()
    {
        $sql   = 'SELECT * FROM gambar_gallery WHERE enabled=?';
        $query = $this->db->query($sql, 1);

        return $query->getResultArray();
    }

    public function paging2($gal = 0, $p = 1)
    {
        $paging = new Paging();

        $sql = 'SELECT COUNT(id) AS id FROM gambar_gallery WHERE parrent = ? AND tipe = 2 ';
        $sql .= $this->search_sql();
        $query    = $this->db->query($sql, $gal);
        $row      = $query->getRowArray();
        $jml_data = $row['id'];

        $cfg['page']     = $p;
        $cfg['per_page'] = $_SESSION['per_page'];
        $cfg['num_rows'] = $jml_data;

        $paging->init($cfg);

        return $paging;
    }

    public function list_sub_gallery($gal = 1, $offset = 0, $limit = 500)
    {
        $paging_sql = ' LIMIT ' . $offset . ',' . $limit;
        $sql        = 'SELECT * FROM gambar_gallery WHERE parrent = ? AND tipe = 2 ';

        $sql .= $paging_sql;
        $query = $this->db->query($sql, $gal);
        $data  = $query->getResultArray();

        $i = 0;

        while ($i < count($data)) {
            $data[$i]['no'] = $i + 1;

            if ($data[$i]['enabled'] === 1) {
                $data[$i]['aktif'] = 'Yes';
            } else {
                $data[$i]['aktif'] = 'No';
            }

            $i++;
        }

        return $data;
    }

    public function insert_sub_gallery($parrent = 0)
    {
        $lokasi_file = $_FILES['gambar']['tmp_name'];
        $tipe_file   = $_FILES['gambar']['type'];
        $nama_file   = $_FILES['gambar']['name'];
        if (! empty($lokasi_file)) {
            if ($tipe_file === 'image/jpeg' || $tipe_file === 'image/pjpeg' || $tipe_file === 'image/png') {
                UploadGallery($nama_file);
                $data            = $_POST;
                $data['gambar']  = $nama_file;
                $data['parrent'] = $parrent;
                $data['tipe']    = 2;

                if ($_SESSION['grup'] === 4) {
                    $data['enabled'] = 2;
                }

                $outp = $this->db->insert('gambar_gallery', $data);
                if ($outp) {
                    $_SESSION['success'] = 1;
                }
            } else {
                $_SESSION['success'] = -1;
            }
        } else {
            $data = $_POST;
            unset($data['gambar']);
            $data['parrent'] = $parrent;
            $data['tipe']    = 2;
            $outp            = $this->db->insert('gambar_gallery', $data);
        }
        if ($outp) {
            $_SESSION['success'] = 1;
        } else {
            $_SESSION['success'] = -1;
        }
    }

    public function update_sub_gallery($id = 0)
    {
        $x           = $_POST;
        $lokasi_file = $_FILES['gambar']['tmp_name'];
        $tipe_file   = $_FILES['gambar']['type'];
        $nama_file   = $_FILES['gambar']['name'];
        $old_gambar  = $x['old_gambar'];
        if (! empty($nama_file)) {
            if ($tipe_file === 'image/jpeg' || $tipe_file === 'image/pjpeg') {
                UploadGallery($nama_file);
                unset($x['old_gambar']);
            }
        } else {
            $_SESSION['success'] = -1;
            $nama_file           = $x['old_gambar'];
        }

        $data['gambar'] = $nama_file;
        $data['nama']   = $_POST['nama'];
        $this->db->where('id', $id);
        $outp = $this->db->update('gambar_gallery', $data);
        if ($outp) {
            $_SESSION['success'] = 1;
        } else {
            $_SESSION['success'] = -1;
        }
    }
}
