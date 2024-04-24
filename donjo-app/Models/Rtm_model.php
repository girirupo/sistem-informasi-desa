<?php

namespace App\Models;

use CodeIgniter\Model as CI_Model;

use App\Libraries\Paging;

class Rtm_model extends CI_Model
{
    public function autocomplete()
    {
        $sql   = 'SELECT t.nama,t.kk_level FROM tweb_rtm u LEFT JOIN tweb_penduduk t ON u.nik_kepala = t.id LEFT JOIN tweb_wil_clusterdesa c ON t.id_cluster = c.id WHERE 1 ';
        $query = $this->db->query($sql);
        $data  = $query->getResultArray();

        $i    = 0;
        $outp = '';

        while ($i < count($data)) {
            $outp .= ',"' . $data[$i]['nama'] . ' - ' . $data[$i]['kk_level'] . '"';
            $i++;
        }
        $outp = strtolower(substr($outp, 1));

        return '[' . $outp . ']';
    }

    public function dusun_sql()
    {
        if (isset($_SESSION['dusun'])) {
            $kf        = $_SESSION['dusun'];
            $dusun_sql = " AND c.dusun = '{$kf}'";

            return $dusun_sql;
        }
    }

    public function rw_sql()
    {
        if (isset($_SESSION['rw'])) {
            $kf     = $_SESSION['rw'];
            $rw_sql = " AND c.rw = '{$kf}'";

            return $rw_sql;
        }
    }

    public function rt_sql()
    {
        if (isset($_SESSION['rt'])) {
            $kf     = $_SESSION['rt'];
            $rt_sql = " AND c.rt = '{$kf}'";

            return $rt_sql;
        }
    }

    public function search_sql()
    {
        if (isset($_SESSION['cari'])) {
            $cari       = $_SESSION['cari'];
            $kw         = penetration($this->db->escape_like_str($cari));
            $kw         = '%' . $kw . '%';
            $search_sql = " AND (t.nama LIKE '{$kw}' OR u.no_kk LIKE '{$kw}') ";

            return $search_sql;
        }
    }

    public function jenis_sql()
    {
        if (isset($_SESSION['jenis'])) {
            $kh        = $_SESSION['jenis'];
            $jenis_sql = " AND jenis = {$kh}";

            return $jenis_sql;
        }
    }

    public function kelas_sql()
    {
        if (isset($_SESSION['kelas'])) {
            $kh        = $_SESSION['kelas'];
            $kelas_sql = " AND kelas_sosial= {$kh}";

            return $kelas_sql;
        }
    }

    public function raskin_sql()
    {
        if (isset($_SESSION['raskin'])) {
            $kh         = $_SESSION['raskin'];
            $raskin_sql = " AND raskin= {$kh}";

            return $raskin_sql;
        }
    }

    public function blt_sql()
    {
        if (isset($_SESSION['id_blt'])) {
            $kh      = $_SESSION['id_blt'];
            $blt_sql = " AND id_blt= {$kh}";

            return $blt_sql;
        }
    }

    public function bos_sql()
    {
        if (isset($_SESSION['id_bos'])) {
            $kh      = $_SESSION['id_bos'];
            $bos_sql = " AND id_bos= {$kh}";

            return $bos_sql;
        }
    }

    public function pkh_sql()
    {
        if (isset($_SESSION['id_pkh'])) {
            $kh      = $_SESSION['id_pkh'];
            $pkh_sql = " AND id_pkh= {$kh}";

            return $pkh_sql;
        }
    }

    public function jampersal_sql()
    {
        if (isset($_SESSION['id_jampersal'])) {
            $kh            = $_SESSION['id_jampersal'];
            $jampersal_sql = " AND id_jampersal= {$kh}";

            return $jampersal_sql;
        }
    }

    public function bedah_rumah_sql()
    {
        if (isset($_SESSION['id_bedah_rumah'])) {
            $kh              = $_SESSION['id_bedah_rumah'];
            $bedah_rumah_sql = " AND id_bedah_rumah= {$kh}";

            return $bedah_rumah_sql;
        }
    }

    public function paging($p = 1, $o = 0)
    {
        $paging = new Paging();

        $sql = 'SELECT COUNT(u.id) AS id FROM tweb_rtm u LEFT JOIN tweb_penduduk t ON t.id_rtm = u.id LEFT JOIN tweb_wil_clusterdesa c ON t.id_cluster = c.id WHERE t.rtm_level = 1 ';
        $sql .= $this->search_sql();
        $sql .= $this->dusun_sql();
        $sql .= $this->rw_sql();
        $sql .= $this->rt_sql();
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
            case 1: $order_sql = ' ORDER BY u.no_kk';
                break;

            case 2: $order_sql = ' ORDER BY u.no_kk DESC';
                break;

            case 3: $order_sql = ' ORDER BY kepala_kk';
                break;

            case 4: $order_sql = ' ORDER BY kepala_kk DESC';
                break;

            case 5: $order_sql = ' ORDER BY g.nama';
                break;

            case 6: $order_sql = ' ORDER BY g.nama DESC';
                break;

            default:$order_sql = ' ';
        }

        $paging_sql = ' LIMIT ' . $offset . ',' . $limit;

        $sql = 'SELECT u.*,t.nama AS kepala_kk,(SELECT COUNT(id) FROM tweb_penduduk WHERE id_rtm = u.id ) AS jumlah_anggota,c.dusun,c.rw,c.rt FROM tweb_rtm u LEFT JOIN tweb_penduduk t ON u.id = t.id_rtm AND t.rtm_level = 1 LEFT JOIN tweb_wil_clusterdesa c ON t.id_cluster = c.id WHERE 1 ';

        $sql .= $this->search_sql();

        $sql .= $this->dusun_sql();
        $sql .= $this->rw_sql();
        $sql .= $this->rt_sql();
        $sql .= $order_sql;
        $sql .= $paging_sql;

        $query = $this->db->query($sql);
        $data  = $query->getResultArray();

        $i = 0;
        $j = $offset;

        while ($i < count($data)) {
            $data[$i]['no'] = $j + 1;
            if ($data[$i]['jumlah_anggota'] === 0) {
                $data[$i]['jumlah_anggota'] = '-';
            }

            $i++;
            $j++;
        }

        return $data;
    }

    public function list_data_pbdt($o = 0, $offset = 0, $limit = 500)
    {
        switch ($o) {
            case 1: $order_sql = ' ORDER BY u.no_kk';
                break;

            case 2: $order_sql = ' ORDER BY u.no_kk DESC';
                break;

            case 3: $order_sql = ' ORDER BY kepala_kk';
                break;

            case 4: $order_sql = ' ORDER BY kepala_kk DESC';
                break;

            case 5: $order_sql = ' ORDER BY g.nama';
                break;

            case 6: $order_sql = ' ORDER BY g.nama DESC';
                break;

            default:$order_sql = ' ';
        }

        $paging_sql = ' LIMIT ' . $offset . ',' . $limit;

        $sql = 'SELECT u.*,t.nama AS kepala_kk,(SELECT COUNT(id) FROM tweb_penduduk WHERE id_rtm = u.id ) AS jumlah_anggota,c.dusun,c.rw,c.rt FROM tweb_rtm u LEFT JOIN tweb_penduduk t ON u.id = t.id_rtm AND t.rtm_level = 1 LEFT JOIN tweb_wil_clusterdesa c ON t.id_cluster = c.id WHERE 1 ';

        $sql .= $this->search_sql();

        $sql .= $this->dusun_sql();
        $sql .= $this->rw_sql();
        $sql .= $this->rt_sql();
        $sql .= $order_sql;
        $sql .= $paging_sql;

        $query = $this->db->query($sql);
        $data  = $query->getResultArray();

        $i = 0;
        $j = $offset;

        while ($i < count($data)) {
            $data[$i]['no'] = $j + 1;
            if ($data[$i]['jumlah_anggota'] === 0) {
                $data[$i]['jumlah_anggota'] = '-';
            }

            $sqlp                = 'SELECT nama FROM tweb_penduduk WHERE id_rtm = ? AND rtm_level <> 1';
            $query               = $this->db->query($sqlp, $data[$i]['id']);
            $data[$i]['anggota'] = $query->getResultArray();

            $i++;
            $j++;
        }

        return $data;
    }

    public function insert()
    {
        $nik = $_POST['nik_kepala'];

        $data['no_kk'] = '0';
        $outp          = $this->db->insert('tweb_rtm', $data);

        $sql   = 'SELECT id FROM tweb_rtm ORDER by id DESC LIMIT 1';
        $query = $this->db->query($sql);
        $kk    = $query->getRowArray();

        $kw                = $this->get_kode_wilayah();
        $nortm             = 100000 + $kk['id'];
        $nortm             = substr($nortm, 1, 5);
        $rtm['no_kk']      = $kw . '' . $nortm;
        $rtm['nik_kepala'] = $nik;
        $this->db->where('id', $kk['id']);
        $this->db->update('tweb_rtm', $rtm);

        $default['id_rtm']    = $kk['id'];
        $default['rtm_level'] = 1;
        $this->db->where('id', $nik);
        $this->db->update('tweb_penduduk', $default);
        if ($outp) {
            $_SESSION['success'] = 1;
        } else {
            $_SESSION['success'] = -1;
        }
    }

    public function delete($id = '')
    {
        $temp['id_rtm']    = 0;
        $temp['rtm_level'] = 0;
        $this->db->where('id_rtm', $id);
        $outp = $this->db->update('tweb_penduduk', $temp);

        $sql  = 'DELETE FROM tweb_rtm WHERE id=?';
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
            foreach ($_POST['id_cb'] as $id_rtm) {
                $sql  = 'DELETE FROM tweb_rtm WHERE id=?';
                $outp = $this->db->query($sql, [$id_rtm]);

                $default['id_rtm']    = '';
                $default['rtm_level'] = '';

                $this->db->where('id_rtm', $id_rtm);
                $this->db->update('tweb_penduduk', $default);
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

    public function add_anggota($id = 0)
    {
        $data              = $_POST;
        $temp['id_rtm']    = $id;
        $temp['rtm_level'] = 2;
        $this->db->where('id', $data['nik']);
        $outp = $this->db->update('tweb_penduduk', $temp);

        if ($temp['rtm_level'] === '1') {
            $temp2['nik_kepala'] = $data['nik'];
            $this->db->where('id', $temp['id_rtm']);
            $outp = $this->db->update('tweb_rtm', $temp2);
        }

        if ($outp) {
            $_SESSION['success'] = 1;
        } else {
            $_SESSION['success'] = -1;
        }
    }

    public function update_anggota($id = 0)
    {
        $data = $_POST;

        if ($data['rtm_level'] === 1) {
            $sql    = 'SELECT id_rtm FROM tweb_penduduk WHERE id=?';
            $query  = $this->db->query($sql, $id);
            $r      = $query->getRowArray();
            $id_rtm = $r['id_rtm'];

            $del['rtm_level'] = 2;
            $this->db->where('id_rtm', $id_rtm);

            $this->db->update('tweb_penduduk', $del);
            $rtm['nik_kepala'] = $id;
            $this->db->where('id', $id_rtm);
            $outp = $this->db->update('tweb_rtm', $rtm);
        }

        $this->db->where('id', $id);
        $this->db->update('tweb_penduduk', $data);

        if ($outp) {
            $_SESSION['success'] = 1;
        } else {
            $_SESSION['success'] = -1;
        }
    }

    public function rem_anggota($kk = 0, $id = 0)
    {
        $temp['id_rtm']    = 0;
        $temp['rtm_level'] = 0;

        $pend = $this->rtm_model->get_anggota($id);
        $this->db->where('id', $id);
        $outp = $this->db->update('tweb_penduduk', $temp);
        if ($pend['rtm_level'] === '1') {
            $temp2['nik_kepala'] = 0;
            $this->db->where('id', $pend['id_rtm']);
            $outp = $this->db->update('tweb_rtm', $temp2);
        }

        if ($outp) {
            $_SESSION['success'] = 1;
        } else {
            $_SESSION['success'] = -1;
        }
    }

    public function rem_all_anggota($kk)
    {
        $id_cb             = $_POST['id_cb'];
        $temp['id_rtm']    = 0;
        $temp['rtm_level'] = 0;

        if (count($id_cb)) {
            foreach ($id_cb as $id) {
                $this->db->where('id', $id);
                $outp = $this->db->update('tweb_penduduk', $temp);
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

    public function get_dusun($id = 0)
    {
        $sql   = 'SELECT * FROM tweb_rtm WHERE dusun_id=?';
        $query = $this->db->query($sql, $id);

        return $query->getRowArray();
    }

    public function get_rtm($id = 0)
    {
        $sql   = 'SELECT * FROM tweb_rtm WHERE id=?';
        $query = $this->db->query($sql, $id);

        return $query->getRowArray();
    }

    public function get_anggota($id = 0)
    {
        $sql   = 'SELECT * FROM tweb_penduduk WHERE id_rtm=?';
        $query = $this->db->query($sql, $id);

        return $query->getRowArray();
    }

    public function get_kode_wilayah()
    {
        $this->load->model('config_model');
        $d = $this->config_model->get_data();

        return $d['kode_kabupaten'] . $d['kode_kecamatan'] . $d['kode_desa'];
    }

    public function list_penduduk_lepas()
    {
        $sql   = 'SELECT p.id,p.nik,p.nama,h.nama as kk_level FROM tweb_penduduk p LEFT JOIN tweb_penduduk_hubungan h ON p.kk_level=h.id WHERE (status = 1 OR status = 3) AND id_rtm = 0';
        $query = $this->db->query($sql);
        $data  = $query->getResultArray();

        $i = 0;

        while ($i < count($data)) {
            $data[$i]['alamat'] = 'Alamat :' . $data[$i]['nama'];
            $data[$i]['nama']   = '' . $data[$i]['nama'] . ' - ' . $data[$i]['kk_level'] . '';
            $i++;
        }

        return $data;
    }

    public function list_anggota($id = 0)
    {
        $sql = 'SELECT b.dusun,b.rw,b.rt,u.id,nik,x.nama as sex,k.no_kk,u.rtm_level,tempatlahir,tanggallahir,a.nama as agama, d.nama as pendidikan,j.nama as pekerjaan,w.nama as status_kawin,f.nama as warganegara,nama_ayah,nama_ibu,g.nama as golongan_darah,u.nama,status,h.nama AS hubungan FROM tweb_penduduk u LEFT JOIN tweb_keluarga k ON u.id_kk = k.id LEFT JOIN tweb_penduduk_agama a ON u.agama_id = a.id LEFT JOIN tweb_penduduk_pekerjaan j ON u.pekerjaan_id = j.id LEFT JOIN tweb_penduduk_pendidikan_kk d ON u.pendidikan_kk_id = d.id LEFT JOIN tweb_penduduk_warganegara f ON u.warganegara_id = f.id LEFT JOIN tweb_golongan_darah g ON u.golongan_darah_id = g.id LEFT JOIN tweb_penduduk_kawin w ON u.status_kawin = w.id LEFT JOIN tweb_penduduk_sex x ON u.sex = x.id LEFT JOIN tweb_rtm_hubungan h ON u.rtm_level = h.id LEFT JOIN tweb_wil_clusterdesa b ON u.id_cluster = b.id WHERE id_rtm = ? ORDER BY rtm_level';

        $query = $this->db->query($sql, [$id]);
        $data  = $query->getResultArray();

        $i = 0;

        while ($i < count($data)) {
            $data[$i]['no']           = $i + 1;
            $data[$i]['alamat']       = 'Dusun ' . ununderscore($data[$i]['dusun']) . ', RW ' . $data[$i]['rw'] . ', RT ' . $data[$i]['rt'];
            $data[$i]['tanggallahir'] = tgl_indo($data[$i]['tanggallahir']);

            $i++;
        }

        return $data;
    }

    public function get_kepala_kk($id)
    {
        $sql   = 'SELECT nik,u.nama,r.no_kk,c.dusun,c.rw,c.rt FROM tweb_penduduk u LEFT JOIN tweb_rtm r ON u.id_rtm = r.id LEFT JOIN tweb_wil_clusterdesa c ON u.id_cluster = c.id WHERE r.id = ? AND u.rtm_level =1 LIMIT 1';
        $query = $this->db->query($sql, [$id]);

        return $query->getRowArray();
    }

    public function list_hubungan()
    {
        $sql   = 'SELECT id,nama as hubungan FROM tweb_rtm_hubungan WHERE 1';
        $query = $this->db->query($sql);

        return $query->getResultArray();
    }

    public function update_nokk($id = 0)
    {
        $data = $_POST;

        $this->db->where('id', $id);
        $outp = $this->db->update('tweb_rtm', $data);

        if ($outp) {
            $_SESSION['success'] = 1;
        } else {
            $_SESSION['success'] = -1;
        }
    }
}
