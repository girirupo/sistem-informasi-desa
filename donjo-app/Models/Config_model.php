<?php

namespace App\Models;

use CodeIgniter\Model as CI_Model;

class Config_model extends CI_Model
{
    public function gawe_surat()
    {
        $sql   = 'SELECT kunci,favorit FROM tweb_surat_format WHERE 1;';
        $query = $this->db->query($sql);

        // if(!$query){
        $sql   = 'SELECT * FROM tweb_surat_format WHERE 1';
        $query = $this->db->query($sql);
        $data  = $query->getResultArray();

        foreach ($data as $dat) {
            $string = $dat['url_surat'];
            $mypath = 'surat\\' . $dat['url_surat'] . '\\';
            $path   = '' . str_replace('\\', '/', $mypath) . '/';

            if (! file_exists($mypath)) {
                mkdir($mypath, 0777, true);
            }

            if (! file_exists($path)) {
                mkdir($path);
            }
            $raw      = 'surat\\raw\\';
            $raw_path = '' . str_replace('\\', '/', $raw);
            $file     = $raw_path . 'template.rtf';
            $handle   = fopen($file, 'rb');

            $buffer = stream_get_contents($handle);

            $handle = fopen($path . $dat['url_surat'] . '.rtf', 'w+b');
            fwrite($handle, $buffer);
            fclose($handle);
        }
        // }
    }

    public function initsurat()
    {
        $sql   = 'SELECT kunci,favorit FROM tweb_surat_format WHERE 1;';
        $query = $this->db->query($sql);
        if (! $query) {
            $sql = "ALTER TABLE tweb_surat_format ADD kunci TINYINT(1) NOT NULL DEFAULT '0', ADD favorit TINYINT( 1 ) NOT NULL DEFAULT '0'";
            $this->db->query($sql);
        }
        $sql   = 'SELECT id_pend FROM dokumen WHERE 1;';
        $query = $this->db->query($sql);
        if (! $query) {
            $sql = "ALTER TABLE dokumen ADD id_pend INT NOT NULL DEFAULT '0' AFTER id";
            $this->db->query($sql);
        }
    }

    public function get_data(bool $return_array = false)
    {
        $sql   = 'SELECT * FROM config WHERE 1';
        $query = $this->db->query($sql);

        if ($return_array) {
            return $query->getResultArray();
        }

        return $query->getRowArray();
    }

    public function insert_()
    {
        $outp = $this->db->insert('config', $_POST);
        if ($outp) {
            $_SESSION['success'] = 1;
        } else {
            $_SESSION['success'] = -1;
        }
    }

    public function update_($id = 0)
    {
        $data        = $_POST;
        $lokasi_file = $_FILES['logo']['tmp_name'];
        $tipe_file   = $_FILES['logo']['type'];
        $nama_file   = $_FILES['logo']['name'];
        $old_logo    = $data['old_logo'];
        if (! empty($lokasi_file)) {
            if ($tipe_file !== 'image/jpeg' && $tipe_file !== 'image/pjpeg' && $tipe_file !== 'image/png') {
                unset($data['logo']);
            } else {
                UploadLogo($nama_file, $old_logo, $tipe_file);
                $data['logo'] = $nama_file;
            }
        } else {
            unset($data['logo']);
        }
        unset($data['file_logo'], $data['old_logo']);

        $this->db->where('id', $id);
        $outp                  = $this->db->update('config', penetration($data));
        $pamong['pamong_nama'] = $data['nama_kepala_desa'];
        $pamong['pamong_nip']  = $data['nip_kepala_desa'];
        $this->db->where('pamong_id', '707');
        $outp = $this->db->update('tweb_desa_pamong', $pamong);
        if ($outp) {
            $_SESSION['success'] = 1;
        } else {
            $_SESSION['success'] = -1;
        }
    }

    public function update_kantor()
    {
        $data = $_POST;
        $id   = '1';
        $this->db->where('id', $id);
        $outp = $this->db->update('config', $data);

        if ($outp) {
            $_SESSION['success'] = 1;
        } else {
            $_SESSION['success'] = -1;
        }
    }

    public function update_wilayah()
    {
        $data = $_POST;
        $id   = '1';
        $this->db->where('id', $id);
        $outp = $this->db->update('config', $data);

        if ($outp) {
            $_SESSION['success'] = 1;
        } else {
            $_SESSION['success'] = -1;
        }
    }

    public function kosong_pend()
    {
        $a = 'TRUNCATE tweb_wil_clusterdesa';
        $this->db->query($a);
        $a = 'TRUNCATE tweb_keluarga';
        $this->db->query($a);
        $a = 'TRUNCATE tweb_rtm';
        $this->db->query($a);

        $a = 'TRUNCATE tweb_penduduk';
        $this->db->query($a);

        $a = 'TRUNCATE log_penduduk';
        $this->db->query($a);

        $a = 'TRUNCATE log_surat';
        $this->db->query($a);

        $a = 'TRUNCATE log_perubahan_penduduk';
        $this->db->query($a);

        $a = 'TRUNCATE log_bulanan';
        $this->db->query($a);

        $a = 'TRUNCATE garis';
        $this->db->query($a);

        $a = 'TRUNCATE lokasi';
        $this->db->query($a);

        $a = 'TRUNCATE area';
        $this->db->query($a);

        $a = 'TRUNCATE point';
        $this->db->query($a);

        $a = 'TRUNCATE line';
        $this->db->query($a);

        $a = 'TRUNCATE polygon';
        $this->db->query($a);

        $a = 'TRUNCATE analisis_master';
        $this->db->query($a);

        $a = 'TRUNCATE analisis_indikator';
        $this->db->query($a);

        $a = 'TRUNCATE analisis_parameter';
        $this->db->query($a);

        $a = 'TRUNCATE analisis_periode';
        $this->db->query($a);

        $a = 'TRUNCATE analisis_respon';
        $this->db->query($a);

        $a = 'TRUNCATE analisis_respon_hasil';
        $this->db->query($a);

        $a = 'TRUNCATE analisis_klasifikasi';
        $this->db->query($a);

        $a = 'TRUNCATE analisis_kategori_indikator';
        $this->db->query($a);

        $a = 'TRUNCATE analisis_respon_bukti';
        $this->db->query($a);

        $a = 'TRUNCATE tweb_penduduk_mandiri';
        $this->db->query($a);
        $a = 'TRUNCATE kelompok';
        $this->db->query($a);

        $a = 'TRUNCATE kelompok_anggota';
        $this->db->query($a);
        $a = 'TRUNCATE data_persil';
        $this->db->query($a);
        $a = 'TRUNCATE tweb_penduduk_map';
        $this->db->query($a);
        $a = 'TRUNCATE sys_traffic';
        $this->db->query($a);
    }

    public function opt()
    {
        $a = 'OPTIMIZE TABLE analisis_indikator, analisis_kategori_indikator, analisis_klasifikasi, analisis_master, analisis_parameter, analisis_partisipasi, analisis_periode, analisis_ref_state, analisis_ref_subjek, analisis_respon, analisis_respon_hasil, analisis_tipe_indikator, area, artikel, config, data_persil, data_persil_jenis, data_persil_log, data_persil_peruntukan, detail_log_penduduk, dokumen, gambar_gallery, garis, gis_simbol, inbox, kategori, kelompok, kelompok_anggota, kelompok_master, komentar, kontak, kontak_grup, line, log_bulanan, log_penduduk, log_perubahan_penduduk, log_surat, lokasi, media_sosial, menu, outbox, point, polygon, program, program_peserta, recent_status, ref_bedah_rumah, ref_blt, ref_jamkesmas, ref_kelas_sosial, ref_pkh, ref_raskin, sentitems, setting_modul, setting_sms, sys_traffic, tweb_alamat_sekarang, tweb_desa_pamong, tweb_keluarga, tweb_penduduk, tweb_penduduk_mandiri, tweb_penduduk_map, tweb_penduduk_umur, tweb_rtm, tweb_surat_atribut, tweb_surat_format, tweb_wil_clusterdesa, user;';
        $this->db->query($a);
    }

    public function cls()
    {
        $sql   = 'SELECT * FROM analisis_parameter WHERE asign = 1 ORDER BY id_indikator';
        $query = $this->db->query($sql);
        $data  = $query->getResultArray();

        $i = 0;
        $m = 0;

        while ($i < count($data)) {
            $jwb = $data[$i]['jawaban'];
            $id  = $data[$i]['id'];

            $sql1   = 'SELECT max(kode_jawaban) AS nil FROM analisis_parameter WHERE id_indikator = ?';
            $query1 = $this->db->query($sql1, $data[$i]['id_indikator']);
            $m      = $query1->getRowArray();
            $n      = ($m['nil'] + 1) - $data[$i]['kode_jawaban'];

            $up['nilai'] = $n;
            $this->db->where('id', $id);
            $outp = $this->db->update('analisis_parameter', $up);
            $j    = explode('. ', $jwb);
            if (count($j) > 1) {
                $upd['jawaban'] = $j[1];
                $this->db->where('id', $id);
                $outp = $this->db->update('analisis_parameter', $upd);
            }
            $i++;
        }
    }
}
