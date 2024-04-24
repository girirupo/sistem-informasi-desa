<?php

namespace App\Models;

use CodeIgniter\Model as CI_Model;

class First_m extends CI_Model
{
    public function siteman()
    {
        $_SESSION['mandiri'] = -1;
        $nik                 = $this->input->post('nik');
        $pin                 = $this->input->post('pin');
        $hash_pin            = hash_pin($pin);

        $sql   = 'SELECT pin,last_login FROM tweb_penduduk_mandiri WHERE nik=?';
        $query = $this->db->query($sql, [$nik]);
        $row   = $query->row();
        $lg    = $row->last_login;

        if ($hash_pin === $row->pin) {
            $_SESSION['mandiri'] = 1;

            $sql   = 'SELECT nama,nik,id FROM tweb_penduduk WHERE nik=?';
            $query = $this->db->query($sql, [$nik]);
            $row   = $query->row();

            if ($lg === '0000-00-00 00:00:00') {
                $_SESSION['lg'] = 1;
            }

            $_SESSION['nama'] = $row->nama;
            $_SESSION['nik']  = $row->nik;
            $_SESSION['id']   = $row->id;
        }

        if ($_SESSION['mandiri_try'] > 2) {
            $_SESSION['mandiri_try']--;
        } else {
            $_SESSION['mandiri_wait'] = 1;
        }
    }

    public function m_siteman($nik = '', $pin = '')
    {
        $hash_pin = hash_pin($pin);

        $sql   = 'SELECT pin,last_login FROM tweb_penduduk_mandiri WHERE nik=?';
        $query = $this->db->query($sql, [$nik]);
        $row   = $query->row();
        $lg    = $row->last_login;

        if ($hash_pin === $row->pin) {
            $sql = 'UPDATE tweb_penduduk_mandiri SET last_login=NOW() WHERE nik=?';
            $this->db->query($sql, $nik);

            return $token;
        }
    }

    public function logout()
    {
        if (isset($_SESSION['nik'])) {
            $id  = $_SESSION['nik'];
            $sql = 'UPDATE tweb_penduduk_mandiri SET last_login=NOW() WHERE nik=?';
            $this->db->query($sql, $id);
        }

        unset($_SESSION['mandiri'], $_SESSION['id'], $_SESSION['nik'], $_SESSION['nama']);
    }

    public function ganti()
    {
        if ($_POST['pin1'] === $_POST['pin2']) {
            $hash_pin = hash_pin($_POST['pin1']);

            $data['pin'] = $hash_pin;
            $this->db->where('nik', $_SESSION['nik']);
            $outp = $this->db->update('tweb_penduduk_mandiri', $data);
        }
        $_SESSION['lg'] = 2;
    }
}
