<?php

namespace App\Models;

use CodeIgniter\Model as CI_Model;

use App\Libraries\Paging;

class User_model extends CI_Model
{
    public function siteman()
    {
        $username = $this->input->post('username');
        $password = hash_password($this->input->post('password'));

        $sql   = 'SELECT id,password,id_grup,session FROM user WHERE username=?';
        $query = $this->db->query($sql, [$username]);
        $row   = $query->getRow();
        if ($row) {
            if ($password === $row->password) {
                $this->reset_timer();
                $data['session'] = hash_password(time() . $password);

                $builder = $this->db->table('user');
                $builder->where('id', $row->id);
                $builder->update($data);

                $_SESSION['siteman'] = 1;
                $_SESSION['sesi']    = $data['session'];
                // $_SESSION['sesi'] = $row->session;
                $_SESSION['user']     = $row->id;
                $_SESSION['grup']     = $row->id_grup;
                $_SESSION['per_page'] = 10;
            } else {
                $_SESSION['siteman'] = -1;
            }
        } else {
            $_SESSION['siteman'] = -1;
        }
    }

    public function sesi_grup($sesi = '')
    {
        $sql   = "SELECT id_grup FROM user WHERE session=? AND session <> ''";
        $query = $this->db->query($sql, [$sesi]);
        $row   = $query->getRowArray();
        if ($this->cek_login()) {
            if (isset($row['id_grup'])) {
                return $row['id_grup'];
            }
        } else {
            $_SESSION['siteman'] = -2;
            $this->logout();

            return null;
        }
    }

    // time out
    public function reset_timer()
    {
        $time                = 3600; // 15menit
        $_SESSION['timeout'] = time() + $time;
    }

    public function cek_login()
    {
        $timeout = $_SESSION['timeout'];
        if (time() < $timeout) {
            $this->reset_timer();

            return true;
        }
        unset($_SESSION['timeout']);

        return false;
    }

    public function login()
    {
        $username = $this->input->post('username');
        $password = hash_password($this->input->post('password'));

        $sql   = 'SELECT id,password,id_grup,session FROM user WHERE id_grup=1 LIMIT 1';
        $query = $this->db->query($sql);
        $row   = $query->getRow();

        if ($password !== $row->password) {
            $_SESSION['siteman']  = 1;
            $_SESSION['sesi']     = $row->session;
            $_SESSION['user']     = $row->id;
            $_SESSION['grup']     = $row->id_grup;
            $_SESSION['per_page'] = 10;
        } else {
            $_SESSION['siteman'] = -1;
        }
    }

    public function logout()
    {
        if (isset($_SESSION['user'])) {
            $id  = $_SESSION['user'];
            $sql = "UPDATE user SET last_login=NOW(),session='' WHERE id=?";
            $this->db->query($sql, $id);
        }

        $sql   = 'SELECT (SELECT COUNT(id) FROM tweb_penduduk WHERE status_dasar =1) AS pend,(SELECT COUNT(id) FROM tweb_penduduk WHERE status_dasar =1 AND sex =1) AS lk,(SELECT COUNT(id) FROM tweb_penduduk WHERE status_dasar =1 AND sex =2) AS pr,(SELECT COUNT(id) FROM tweb_keluarga) AS kk';
        $query = $this->db->query($sql);
        $data  = $query->getRowArray();

        $bln = date('m');
        $thn = date('Y');

        $sql   = "SELECT * FROM log_bulanan WHERE month(tgl) = {$bln} AND year(tgl) = {$thn}";
        $query = $this->db->query($sql);
        $ada   = $query->getResultArray();

        if (! $ada) {
            $this->db->table('log_bulanan')->insert($data);
        } else {
            $sql = "UPDATE log_bulanan SET pend={$data['pend']}, lk = {$data['lk']},pr={$data['pr']},kk = {$data['kk']} WHERE month(tgl) = {$bln} AND year(tgl) = {$thn}";
            $this->db->query($sql);
        }

        unset($_SESSION['user'], $_SESSION['sesi'], $_SESSION['cari'], $_SESSION['filter']);
    }

    public function autocomplete()
    {
        $sql = 'SELECT username FROM user
					UNION SELECT nama FROM user';
        $query = $this->db->query($sql);
        $data  = $query->getResultArray();
        $i     = 0;
        $outp  = '';

        while ($i < count($data)) {
            $outp .= ",'" . $data[$i]['username'] . "'";
            $i++;
        }
        $outp = strtolower(substr($outp, 1));

        return '[' . $outp . ']';
    }

    public function search_sql()
    {
        if (isset($_SESSION['cari'])) {
            $cari       = $_SESSION['cari'];
            $kw         = $this->db->escapeLikeString($cari);
            $kw         = '%' . $kw . '%';
            $search_sql = " AND (u.username LIKE '{$kw}' OR u.nama LIKE '{$kw}')";

            return $search_sql;
        }
    }

    public function filter_sql()
    {
        if (isset($_SESSION['filter'])) {
            $kf         = $_SESSION['filter'];
            $filter_sql = " AND u.id_grup = {$kf}";

            return $filter_sql;
        }
    }

    public function paging($p = 1, $o = 0)
    {
        $paging = new Paging();

        $sql = 'SELECT COUNT(id) AS id FROM user u WHERE 1';
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
            case 1: $order_sql = ' ORDER BY u.username';
                break;

            case 2: $order_sql = ' ORDER BY u.username DESC';
                break;

            case 3: $order_sql = ' ORDER BY u.nama';
                break;

            case 4: $order_sql = ' ORDER BY u.nama DESC';
                break;

            case 5: $order_sql = ' ORDER BY g.nama';
                break;

            case 6: $order_sql = ' ORDER BY g.nama DESC';
                break;

            default:$order_sql = ' ORDER BY u.username';
        }
        $paging_sql = ' LIMIT ' . $offset . ',' . $limit;
        $sql        = 'SELECT u.*,g.nama as grup
					FROM user u, user_grup g
					WHERE u.id_grup = g.id';
        $sql .= $this->search_sql();
        $sql .= $this->filter_sql();
        $sql .= $order_sql;
        $sql .= $paging_sql;

        $query = $this->db->query($sql);
        $data  = $query->getResultArray();
        $i     = 0;
        $j     = $offset;

        while ($i < count($data)) {
            $data[$i]['no'] = $j + 1;
            $i++;
            $j++;
        }

        return $data;
    }

    public function insert_()
    {
        $data             = $_POST;
        $data['password'] = hash_password($data['password']);
        unset($data['old_foto'], $data['foto']);

        $lokasi_file = $_FILES['foto']['tmp_name'];
        $tipe_file   = $_FILES['foto']['type'];
        $nama_file   = $_FILES['foto']['name'];
        $old_foto    = $this->input->post('old_foto');
        if (! empty($lokasi_file)) {
            if ($tipe_file !== 'image/jpeg' && $tipe_file !== 'image/pjpeg' && $tipe_file !== 'image/png') {
                $_SESSION['success'] = -1;
            } else {
                UploadFoto($nama_file, $old_foto);
                $data['foto'] = $nama_file;
            }
        }

        $data['session'] = hash_password(now());

        $outp = $this->db->table('user')->insert($data);

        if ($outp) {
            $_SESSION['success'] = 1;
        } else {
            $_SESSION['success'] = -1;
        }
    }

    public function update_($id = 0)
    {
        $data = $_POST;
        unset($data['old_foto'], $data['foto']);

        $lokasi_file = $_FILES['foto']['tmp_name'];
        $tipe_file   = $_FILES['foto']['type'];
        $nama_file   = $_FILES['foto']['name'];
        $old_foto    = $this->input->post('old_foto');
        if (! empty($lokasi_file)) {
            if ($tipe_file !== 'image/jpeg' && $tipe_file !== 'image/pjpeg' && $tipe_file !== 'image/png') {
                $_SESSION['success'] = -1;
            } else {
                UploadFoto($nama_file, $old_foto);
                $data['foto'] = $nama_file;
            }
        }

        if ($data['password'] === 'radiisi') {
            unset($data['password']);
        } else {
            $data['password'] = hash_password($data['password']);
        }

        $outp = $this->db->table('user')->update($data, ['id' => $id]);

        if ($outp) {
            $_SESSION['success'] = 1;
        } else {
            $_SESSION['success'] = -1;
        }
    }

    public function delete_($id = '')
    {
        $sql  = 'DELETE FROM user WHERE id=?';
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
                $sql  = 'DELETE FROM user WHERE id=?';
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

    public function user_lock($id = '', $val = 0)
    {
        $sql  = 'UPDATE user SET active=? WHERE id=?';
        $outp = $this->db->query($sql, [$val, $id]);

        if ($outp) {
            $_SESSION['success'] = 1;
        } else {
            $_SESSION['success'] = -1;
        }
    }

    public function get_user($id = 0)
    {
        $sql   = 'SELECT * FROM user WHERE id=?';
        $query = $this->db->query($sql, $id);
        $data  = $query->getRowArray();

        $data['password'] = 'radiisi';

        return $data;
    }

    public function update_setting($id = 0)
    {
        $password   = hash_password($this->input->post('pass_lama'));
        $pass_baru  = $this->input->post('pass_baru');
        $pass_baru1 = $this->input->post('pass_baru1');
        $nama       = $this->input->post('nama');

        $data = $_POST;
        unset($data['old_foto'], $data['foto']);

        $lokasi_file = $_FILES['foto']['tmp_name'];
        $tipe_file   = $_FILES['foto']['type'];
        $nama_file   = $_FILES['foto']['name'];
        $old_foto    = $this->input->post('old_foto');
        if (! empty($lokasi_file)) {
            if ($tipe_file !== 'image/jpeg' && $tipe_file !== 'image/pjpeg' && $tipe_file !== 'image/png') {
                $_SESSION['success'] = -1;
            } else {
                UploadFoto($nama_file, $old_foto);
                $data['foto'] = $nama_file;
            }
        }
        $sql = "UPDATE user SET foto = '{$nama_file}' WHERE id=?";
        $this->db->query($sql, [$id]);

        $sql   = 'SELECT password,id_grup,session FROM user WHERE id=?';
        $query = $this->db->query($sql, [$id]);
        $row   = $query->getRow();

        if ($password === $row->password) {
            if ($pass_baru !== '') {
                if ($pass_baru === $pass_baru1) {
                    $pass_baru = hash_password($pass_baru);
                    $sql       = 'UPDATE user SET password=? WHERE id=?';
                    $outp      = $this->db->query($sql, [$pass_baru, $id]);
                }
            }
        }

        $sql  = 'UPDATE user SET nama=? WHERE id=?';
        $outp = $this->db->query($sql, [$nama, $id]);

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
