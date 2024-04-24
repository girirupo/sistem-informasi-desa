<?php

namespace App\Controllers;

class Keluarga extends BaseController
{
    public function initController(
        \CodeIgniter\HTTP\RequestInterface $request,
        \CodeIgniter\HTTP\ResponseInterface $response,
        \Psr\Log\LoggerInterface $logger
    ) {
        parent::initController($request, $response, $logger);

        $this->load->model('config_model');
        $this->load->model('keluarga_model');
        $this->load->model('penduduk_model');
        $this->load->model('user_model');
        $grup = $this->user_model->sesi_grup($_SESSION['sesi']);
        if ($grup !== '1' && $grup !== '2') {
            return redirect()->to('siteman');
        }
        $this->load->model('header_model');
    }

    public function clear()
    {
        unset($_SESSION['cari'], $_SESSION['filter'], $_SESSION['dusun'], $_SESSION['rw'], $_SESSION['rt'], $_SESSION['sex'], $_SESSION['raskin'], $_SESSION['id_blt'], $_SESSION['id_bos'], $_SESSION['id_pkh'], $_SESSION['id_jampersal'], $_SESSION['id_bedah_rumah']);

        $_SESSION['per_page'] = 100;
        return redirect()->to('keluarga');
    }

    public function index($p = 1, $o = 0)
    {
        $data = [];
        $nav  = [];

        $data['p'] = $p;
        $data['o'] = $o;

        if (isset($_SESSION['cari'])) {
            $data['cari'] = $_SESSION['cari'];
        } else {
            $data['cari'] = '';
        }

        if (isset($_SESSION['filter'])) {
            $data['filter'] = $_SESSION['filter'];
        } else {
            $data['filter'] = '';
        }
        if (isset($_SESSION['sex'])) {
            $data['sex'] = $_SESSION['sex'];
        } else {
            $data['sex'] = '';
        }
        if (isset($_SESSION['raskin'])) {
            $data['raskin'] = $_SESSION['raskin'];
        } else {
            $data['raskin'] = '';
        }
        if (isset($_SESSION['id_blt'])) {
            $data['id_blt'] = $_SESSION['id_blt'];
        } else {
            $data['id_blt'] = '';
        }

        if (isset($_SESSION['id_bos'])) {
            $data['id_bos'] = $_SESSION['id_bos'];
        } else {
            $data['id_bos'] = '';
        }

        if (isset($_SESSION['id_pkh'])) {
            $data['id_pkh'] = $_SESSION['id_pkh'];
        } else {
            $data['id_pkh'] = '';
        }

        if (isset($_SESSION['id_jampersal'])) {
            $data['id_jampersal'] = $_SESSION['id_jampersal'];
        } else {
            $data['id_jampersal'] = '';
        }
        if (isset($_SESSION['id_bedah_rumah'])) {
            $data['id_bedah_rumah'] = $_SESSION['id_bedah_rumah'];
        } else {
            $data['id_bedah_rumah'] = '';
        }
        if (isset($_POST['per_page'])) {
            $_SESSION['per_page'] = $_POST['per_page'];
        }
        $data['per_page'] = $_SESSION['per_page'];

        if (isset($_SESSION['dusun'])) {
            $data['dusun']   = $_SESSION['dusun'];
            $data['list_rw'] = $this->penduduk_model->list_rw($data['dusun']);

            if (isset($_SESSION['rw'])) {
                $data['rw']      = $_SESSION['rw'];
                $data['list_rt'] = $this->penduduk_model->list_rt($data['dusun'], $data['rw']);

                if (isset($_SESSION['rt'])) {
                    $data['rt'] = $_SESSION['rt'];
                } else {
                    $data['rt'] = '';
                }
            } else {
                $data['rw'] = '';
            }
        } else {
            $data['dusun'] = '';
            $data['rw']    = '';
            $data['rt']    = '';
        }
        $data['grup']       = $this->user_model->sesi_grup($_SESSION['sesi']);
        $data['paging']     = $this->keluarga_model->paging($p, $o);
        $data['main']       = $this->keluarga_model->list_data($o, $data['paging']->offset, $data['paging']->per_page);
        $data['keyword']    = $this->keluarga_model->autocomplete();
        $data['list_dusun'] = $this->penduduk_model->list_dusun();

        $nav['act'] = 1;
        $header     = $this->header_model->get_data();

        echo view('header', $header);
        echo view('sid/nav', $nav);
        echo view('sid/kependudukan/keluarga', $data);
        echo view('footer');
    }

    public function sosial($p = 1, $o = 0)
    {
        $data = [];
        $nav  = [];

        $data['p'] = $p;
        $data['o'] = $o;

        if (isset($_SESSION['cari'])) {
            $data['cari'] = $_SESSION['cari'];
        } else {
            $data['cari'] = '';
        }

        if (isset($_SESSION['filter'])) {
            $data['filter'] = $_SESSION['filter'];
        } else {
            $data['filter'] = '';
        }
        if (isset($_SESSION['raskin'])) {
            $data['raskin'] = $_SESSION['raskin'];
        } else {
            $data['raskin'] = '';
        }

        if (isset($_SESSION['id_blt'])) {
            $data['id_blt'] = $_SESSION['id_blt'];
        } else {
            $data['id_blt'] = '';
        }
        if (isset($_SESSION['id_pkh'])) {
            $data['id_pkh'] = $_SESSION['id_pkh'];
        } else {
            $data['id_pkh'] = '';
        }
        if (isset($_SESSION['id_bos'])) {
            $data['id_bos'] = $_SESSION['id_bos'];
        } else {
            $data['id_bos'] = '';
        }

        if (isset($_SESSION['id_jampersal'])) {
            $data['id_jampersal'] = $_SESSION['id_jampersal'];
        } else {
            $data['id_jampersal'] = '';
        }
        if (isset($_SESSION['id_bedah_rumah'])) {
            $data['id_bedah_rumah'] = $_SESSION['id_bedah_rumah'];
        } else {
            $data['id_bedah_rumah'] = '';
        }
        if (isset($_POST['per_page'])) {
            $_SESSION['per_page'] = $_POST['per_page'];
        }
        $data['per_page'] = $_SESSION['per_page'];

        if (isset($_SESSION['dusun'])) {
            $data['dusun']   = $_SESSION['dusun'];
            $data['list_rw'] = $this->penduduk_model->list_rw($data['dusun']);

            if (isset($_SESSION['rw'])) {
                $data['rw']      = $_SESSION['rw'];
                $data['list_rt'] = $this->penduduk_model->list_rt($data['dusun'], $data['rw']);

                if (isset($_SESSION['rt'])) {
                    $data['rt'] = $_SESSION['rt'];
                } else {
                    $data['rt'] = '';
                }
            } else {
                $data['rw'] = '';
            }
        } else {
            $data['dusun'] = '';
            $data['rw']    = '';
            $data['rt']    = '';
        }

        $data['paging']     = $this->keluarga_model->paging($p, $o);
        $data['main']       = $this->keluarga_model->list_raskin();
        $data['keyword']    = $this->keluarga_model->autocomplete();
        $data['list_dusun'] = $this->penduduk_model->list_dusun();
        $nav['act']         = 1;
        $header             = $this->header_model->get_data();

        echo view('header', $header);
        echo view('sid/nav', $nav);
        echo view('sid/kependudukan/keluarga_sosial', $data);
        echo view('footer');
    }

    public function raskin_graph($p = 1, $o = 0)
    {
        $data = [];
        $nav  = [];

        $data['p'] = $p;
        $data['o'] = $o;

        if (isset($_SESSION['cari'])) {
            $data['cari'] = $_SESSION['cari'];
        } else {
            $data['cari'] = '';
        }

        if (isset($_SESSION['filter'])) {
            $data['filter'] = $_SESSION['filter'];
        } else {
            $data['filter'] = '';
        }
        if (isset($_SESSION['raskin'])) {
            $data['raskin'] = $_SESSION['raskin'];
        } else {
            $data['raskin'] = '';
        }
        if (isset($_SESSION['id_blt'])) {
            $data['id_blt'] = $_SESSION['id_blt'];
        } else {
            $data['id_blt'] = '';
        }
        if (isset($_SESSION['id_bos'])) {
            $data['id_bos'] = $_SESSION['id_bos'];
        } else {
            $data['id_bos'] = '';
        }
        if (isset($_SESSION['id_pkh'])) {
            $data['id_pkh'] = $_SESSION['id_pkh'];
        } else {
            $data['id_pkh'] = '';
        }
        if (isset($_SESSION['id_jampersal'])) {
            $data['id_jampersal'] = $_SESSION['id_jampersal'];
        } else {
            $data['id_jampersal'] = '';
        }
        if (isset($_SESSION['id_bedah_rumah'])) {
            $data['id_bedah_rumah'] = $_SESSION['id_bedah_rumah'];
        } else {
            $data['id_bedah_rumah'] = '';
        }
        if (isset($_POST['per_page'])) {
            $_SESSION['per_page'] = $_POST['per_page'];
        }
        $data['per_page'] = $_SESSION['per_page'];

        if (isset($_SESSION['dusun'])) {
            $data['dusun']   = $_SESSION['dusun'];
            $data['list_rw'] = $this->penduduk_model->list_rw($data['dusun']);

            if (isset($_SESSION['rw'])) {
                $data['rw']      = $_SESSION['rw'];
                $data['list_rt'] = $this->penduduk_model->list_rt($data['dusun'], $data['rw']);

                if (isset($_SESSION['rt'])) {
                    $data['rt'] = $_SESSION['rt'];
                } else {
                    $data['rt'] = '';
                }
            } else {
                $data['rw'] = '';
            }
        } else {
            $data['dusun'] = '';
            $data['rw']    = '';
            $data['rt']    = '';
        }

        $data['paging']     = $this->keluarga_model->paging($p, $o);
        $data['main']       = $this->keluarga_model->list_raskin();
        $data['keyword']    = $this->keluarga_model->autocomplete();
        $data['list_dusun'] = $this->penduduk_model->list_dusun();
        $nav['act']         = 1;
        $header             = $this->header_model->get_data();

        echo view('header', $header);
        echo view('sid/nav', $nav);
        echo view('sid/kependudukan/keluarga_raskin', $data);
        echo view('footer');
    }

    public function jamkesmas_graph($p = 1, $o = 0)
    {
        $data = [];
        $nav  = [];

        $data['p'] = $p;
        $data['o'] = $o;

        if (isset($_SESSION['cari'])) {
            $data['cari'] = $_SESSION['cari'];
        } else {
            $data['cari'] = '';
        }

        if (isset($_SESSION['filter'])) {
            $data['filter'] = $_SESSION['filter'];
        } else {
            $data['filter'] = '';
        }
        if (isset($_SESSION['raskin'])) {
            $data['raskin'] = $_SESSION['raskin'];
        } else {
            $data['raskin'] = '';
        }
        if (isset($_SESSION['id_blt'])) {
            $data['id_blt'] = $_SESSION['id_blt'];
        } else {
            $data['id_blt'] = '';
        }
        if (isset($_SESSION['id_bos'])) {
            $data['id_bos'] = $_SESSION['id_bos'];
        } else {
            $data['id_bos'] = '';
        }
        if (isset($_SESSION['id_pkh'])) {
            $data['id_pkh'] = $_SESSION['id_pkh'];
        } else {
            $data['id_pkh'] = '';
        }
        if (isset($_SESSION['id_jampersal'])) {
            $data['id_jampersal'] = $_SESSION['id_jampersal'];
        } else {
            $data['id_jampersal'] = '';
        }
        if (isset($_SESSION['id_bedah_rumah'])) {
            $data['id_bedah_rumah'] = $_SESSION['id_bedah_rumah'];
        } else {
            $data['id_bedah_rumah'] = '';
        }
        if (isset($_POST['per_page'])) {
            $_SESSION['per_page'] = $_POST['per_page'];
        }
        $data['per_page'] = $_SESSION['per_page'];

        if (isset($_SESSION['dusun'])) {
            $data['dusun']   = $_SESSION['dusun'];
            $data['list_rw'] = $this->penduduk_model->list_rw($data['dusun']);

            if (isset($_SESSION['rw'])) {
                $data['rw']      = $_SESSION['rw'];
                $data['list_rt'] = $this->penduduk_model->list_rt($data['dusun'], $data['rw']);

                if (isset($_SESSION['rt'])) {
                    $data['rt'] = $_SESSION['rt'];
                } else {
                    $data['rt'] = '';
                }
            } else {
                $data['rw'] = '';
            }
        } else {
            $data['dusun'] = '';
            $data['rw']    = '';
            $data['rt']    = '';
        }

        $data['paging']     = $this->keluarga_model->paging($p, $o);
        $data['main']       = $this->keluarga_model->list_raskin();
        $data['keyword']    = $this->keluarga_model->autocomplete();
        $data['list_dusun'] = $this->penduduk_model->list_dusun();
        $nav['act']         = 1;
        $header             = $this->header_model->get_data();

        echo view('header', $header);
        echo view('sid/nav', $nav);
        echo view('sid/kependudukan/keluarga_jamkesmas', $data);
        echo view('footer');
    }

    public function pentagon()
    {
        $data = [];

        $data['main'] = $this->keluarga_model->list_raskin();

        echo view('sid/kependudukan/pentagon/pentagon', $data);
    }

    public function cetak($o = 0)
    {
        $data = [];

        $data['main'] = $this->keluarga_model->list_data($o, 0, 10000);

        echo view('sid/kependudukan/keluarga_print', $data);
    }

    public function excel($o = 0)
    {
        $data = [];

        $data['main'] = $this->keluarga_model->list_data($o, 0, 10000);

        echo view('sid/kependudukan/keluarga_excel', $data);
    }

    public function form($id_keluarga = 0, $new = 1)
    {
        $data = [];
        $nav  = [];

        if ($new === 1) {
            if (isset($_POST['dusun'])) {
                $data['dus_sel'] = $_POST['dusun'];
            } else {
                $data['dus_sel'] = '';
            }

            if (isset($_POST['rw'])) {
                $data['rw_sel'] = $_POST['rw'];
            } else {
                $data['rw_sel'] = '';
            }

            if (isset($_POST['rt'])) {
                $data['rt_sel'] = $_POST['rt'];
            } else {
                $data['rt_sel'] = '';
            }

            $data['new'] = $new;
        } else {
            $data['new']     = 0;
            $data['dus_sel'] = '';
            $data['rw_sel']  = '';
            $data['rt_sel']  = '';
        }

        if ($id_keluarga > 0) {
            $data['kk']          = $this->keluarga_model->get_keluarga($id_keluarga);
            $data['form_action'] = site_url("keluarga/update/{$id_keluarga}");
        } elseif ($new > 0) {
            $data['kk']          = null;
            $data['form_action'] = site_url('keluarga/insert_new');
        } else {
            $data['kk']          = null;
            $data['form_action'] = site_url('keluarga/insert');
        }

        $data['penduduk'] = $this->keluarga_model->list_penduduk_lepas();

        $nav['act'] = 1;

        $data['list_no_kk']     = $this->keluarga_model->duplikasi();
        $data['dusun']          = $this->penduduk_model->list_dusun();
        $data['rw']             = $this->penduduk_model->list_rw($data['dus_sel']);
        $data['rt']             = $this->penduduk_model->list_rt($data['dus_sel'], $data['rw_sel']);
        $data['agama']          = $this->penduduk_model->list_agama();
        $data['pendidikan']     = $this->penduduk_model->list_pendidikan();
        $data['pendidikan_kk']  = $this->penduduk_model->list_pendidikan_kk();
        $data['pekerjaan']      = $this->penduduk_model->list_pekerjaan();
        $data['warganegara']    = $this->penduduk_model->list_warganegara();
        $data['hubungan']       = $this->penduduk_model->list_hubungan();
        $data['kawin']          = $this->penduduk_model->list_status_kawin();
        $data['golongan_darah'] = $this->penduduk_model->list_golongan_darah();
        $data['cacat']          = $this->penduduk_model->list_cacat();

        $header = $this->header_model->get_data();

        echo view('header', $header);
        echo view('sid/nav', $nav);
        echo view('sid/kependudukan/keluarga_form', $data);
        echo view('footer');
    }

    public function form_a($p = 1, $o = 0, $id = 0)
    {
        $data = [];
        $nav  = [];

        $data['id_kk']       = $id;
        $data['kk']          = $this->keluarga_model->get_kepala_a($id);
        $data['form_action'] = site_url('keluarga/insert_a');

        $nav['act'] = 2;

        $data['agama']          = $this->penduduk_model->list_agama();
        $data['pendidikan_kk']  = $this->penduduk_model->list_pendidikan_kk();
        $data['pendidikan']     = $this->penduduk_model->list_pendidikan();
        $data['pekerjaan']      = $this->penduduk_model->list_pekerjaan();
        $data['warganegara']    = $this->penduduk_model->list_warganegara();
        $data['hubungan']       = $this->penduduk_model->list_hubungan();
        $data['kawin']          = $this->penduduk_model->list_status_kawin();
        $data['golongan_darah'] = $this->penduduk_model->list_golongan_darah();
        $data['cacat']          = $this->penduduk_model->list_cacat();

        $header = $this->header_model->get_data();

        echo view('header', $header);
        echo view('sid/nav', $nav);
        echo view('sid/kependudukan/keluarga_form_a', $data);
        echo view('footer');
    }

    public function edit_nokk($p = 1, $o = 0, $id = 0)
    {
        $data = [];

        $data['kk']          = $this->keluarga_model->get_keluarga($id);
        $data['form_action'] = site_url("keluarga/update_nokk/{$id}");

        echo view('sid/kependudukan/ajax_edit_nokk', $data);
    }

    public function form_old($p = 1, $o = 0, $id = 0)
    {
        $data = [];

        $data['penduduk']    = $this->keluarga_model->list_penduduk_lepas();
        $data['form_action'] = site_url("keluarga/insert/{$id}");

        echo view('sid/kependudukan/ajax_add_keluarga', $data);
    }

    public function dusun($s = 0)
    {
        $dusun = $this->request->getPost('dusun');
        if ($dusun !== '') {
            $_SESSION['dusun'] = $dusun;
        } else {
            unset($_SESSION['dusun'], $_SESSION['rw'], $_SESSION['rt']);
        }
        return redirect()->to('keluarga');
    }

    public function rw($s = 0)
    {
        $rw = $this->request->getPost('rw');
        if ($rw !== '') {
            $_SESSION['rw'] = $rw;
        } else {
            unset($_SESSION['rw']);
        }
        if ($s === 1) {
            return redirect()->to('keluarga/sosial');
        } elseif ($s === 2) {
            return redirect()->to('keluarga/raskin_graph');
        } else {
            return redirect()->to('keluarga');
        }
    }

    public function rt($s = 0)
    {
        $rt = $this->request->getPost('rt');
        if ($rt !== '') {
            $_SESSION['rt'] = $rt;
        } else {
            unset($_SESSION['rt']);
        }
        if ($s === 1) {
            return redirect()->to('keluarga/sosial');
        } elseif ($s === 2) {
            return redirect()->to('keluarga/raskin_graph');
        } else {
            return redirect()->to('keluarga');
        }
    }

    public function raskin()
    {
        $raskin = $this->request->getPost('raskin');
        if ($raskin !== '') {
            $_SESSION['raskin'] = $raskin;
        } else {
            unset($_SESSION['raskin']);
        }
        return redirect()->to('keluarga');
    }

    public function sex()
    {
        $sex = $this->request->getPost('sex');
        if ($sex !== '') {
            $_SESSION['sex'] = $sex;
        } else {
            unset($_SESSION['sex']);
        }
        return redirect()->to('keluarga');
    }

    public function blt()
    {
        $id_blt = $this->request->getPost('id_blt');
        if ($id_blt !== '') {
            $_SESSION['id_blt'] = $id_blt;
        } else {
            unset($_SESSION['id_blt']);
        }
        return redirect()->to('keluarga');
    }

    public function bos()
    {
        $id_bos = $this->request->getPost('id_bos');
        if ($id_bos !== '') {
            $_SESSION['id_bos'] = $id_bos;
        } else {
            unset($_SESSION['id_bos']);
        }
        return redirect()->to('keluarga');
    }

    public function search()
    {
        $cari = $this->request->getPost('cari');
        if ($cari !== '') {
            $_SESSION['cari'] = $cari;
        } else {
            unset($_SESSION['cari']);
        }
        return redirect()->to('keluarga');
    }

    public function insert()
    {
        $this->keluarga_model->insert();
        return redirect()->to('keluarga');
    }

    public function insert_a()
    {
        $data = $this->keluarga_model->dn();

        $i    = 0;
        $dp   = 0;
        $link = site_url('keluarga');

        while ($i < count($data)) {
            if ($_POST['nik'] === $data[$i]['nik']) {
                $dp = 1;
                $nk = $data[$i]['nik'];
            }

            $i++;
        }
        if ($dp === 1) {
            echo "<h3>TERJADI KESALAHAN</h3><br><h4>Data Tidak Tersimpan</h><br>
			Sudah terdapat Penduduk dengan nomor NIK {$nk}, Silahkan periksa kembali dan ulangi proses memasukkan data.</br>
			Klik disini untuk <a href='{$link}'> Kembali</a>";
        } else {
            $this->keluarga_model->insert_a();
            return redirect()->to('keluarga');
        }
    }

    public function insert_new()
    {
        $data = $this->keluarga_model->dp();

        $i    = 0;
        $dp   = 0;
        $link = site_url('keluarga/form/0/1');

        while ($i < count($data)) {
            if ($_POST['no_kk'] === $data[$i]['no_kk']) {
                $dp = 1;
                $nk = $data[$i]['no_kk'];
            }

            $i++;
        }
        if ($dp === 1) {
            echo "<h3>TERJADI KESALAHAN</h3><br><h4>Data Tidak Tersimpan</h><br>
			Sudah terdapat keluarga dengan nomor KK {$nk}, Silahkan periksa kembali dan ulangi proses memasukkan data.</br>
			Klik disini untuk <a href='{$link}'> Kembali</a>";
        } else {
            $this->keluarga_model->insert_new();
            return redirect()->to('keluarga');
        }
    }

    public function update($id = '')
    {
        $this->keluarga_model->update($id);
        return redirect()->to('keluarga');
    }

    public function update_nokk($id = '')
    {
        $this->keluarga_model->update_nokk($id);
        return redirect()->to('keluarga');
    }

    public function delete($p = 1, $o = 0, $id = '')
    {
        $this->keluarga_model->delete($id);
        return redirect()->to('keluarga');
    }

    public function delete_all($p = 1, $o = 0)
    {
        $this->keluarga_model->delete_all();
        return redirect()->to('keluarga');
    }

    public function anggota($p = 1, $o = 0, $id = 0)
    {
        $data['p']  = $p;
        $data['o']  = $o;
        $data['kk'] = $id;

        $data['main']      = $this->keluarga_model->list_anggota($id);
        $data['kepala_kk'] = $this->keluarga_model->get_kepala_kk($id);
        $nav['act']        = 1;
        $header            = $this->header_model->get_data();
        echo view('header', $header);
        echo view('sid/nav', $nav);
        echo view('sid/kependudukan/keluarga_anggota', $data);
        echo view('footer');
    }

    public function ajax_add_anggota($p = 1, $o = 0, $id = 0)
    {
        $data = [];

        $data['p']        = $p;
        $data['o']        = $o;
        $data['hubungan'] = $this->keluarga_model->list_hubungan();
        $data['main']     = $this->keluarga_model->list_anggota($id);
        $kk               = $this->keluarga_model->get_kepala_kk($id);
        if ($kk) {
            $data['kepala_kk'] = $kk;
        } else {
            $data['kepala_kk'] = null;
        }
        $data['penduduk']    = $this->keluarga_model->list_penduduk_lepas();
        $data['form_action'] = site_url("keluarga/add_anggota/{$p}/{$o}/{$id}");

        echo view('sid/kependudukan/ajax_add_anggota_form', $data);
    }

    public function edit_anggota($p = 1, $o = 0, $id_kk = 0, $id = 0)
    {
        $data = [];

        $data['p']        = $p;
        $data['o']        = $o;
        $data['hubungan'] = $this->keluarga_model->list_hubungan();
        $data['main']     = $this->keluarga_model->get_anggota($id);

        $kk = $this->keluarga_model->get_kepala_kk($id);
        if ($kk) {
            $data['kepala_kk'] = $kk;
        } else {
            $data['kepala_kk'] = null;
        }

        $data['form_action'] = site_url("keluarga/update_anggota/{$p}/{$o}/{$id_kk}/{$id}");

        echo view('sid/kependudukan/ajax_edit_anggota_form', $data);
    }

    public function kartu_keluarga($p = 1, $o = 0, $id = 0)
    {
        $data = [];
        $nav  = [];

        $data['p']        = $p;
        $data['o']        = $o;
        $data['id_kk']    = $id;
        $data['hubungan'] = $this->keluarga_model->list_hubungan();
        $data['main']     = $this->keluarga_model->list_anggota($id);
        $kk               = $this->keluarga_model->get_kepala_kk($id);
        $data['desa']     = $this->config_model->get_data();

        if ($kk) {
            $data['kepala_kk'] = $kk;
        } else {
            $data['kepala_kk'] = null;
        }

        $data['penduduk']    = $this->keluarga_model->list_penduduk_lepas();
        $data['form_action'] = site_url('keluarga/print');

        $nav['act'] = 1;
        $header     = $this->header_model->get_data();

        echo view('header', $header);
        echo view('sid/nav', $nav);

        echo view('sid/kependudukan/kartu_keluarga', $data);
        echo view('footer');
    }

    public function cetak_kk($id = 0)
    {
        $data = [];
        $nav  = [];

        $data['id_kk']     = $id;
        $data['main']      = $this->keluarga_model->list_anggota($id);
        $kk                = $this->keluarga_model->get_kepala_kk($id);
        $data['desa']      = $this->config_model->get_data();
        $data['kepala_kk'] = $kk;
        $nav['act']        = 1;

        echo view('sid/kependudukan/cetak_kk', $data);
    }

    public function doc_kk($id = 0)
    {
        $data = [];

        $data['desa'] = $this->config_model->get_data();

        $data['id_kk']     = $id;
        $data['main']      = $this->keluarga_model->list_anggota($id);
        $data['kepala_kk'] = $this->keluarga_model->get_kepala_kk($id);

        $this->keluarga_model->coba($data);
    }

    public function coba2($id = 0)
    {
        $this->keluarga_model->coba2();
    }

    public function add_anggota($p = 1, $o = 0, $id = 0)
    {
        $this->keluarga_model->add_anggota($id);
        return redirect()->to("keluarga/anggota/{$p}/{$o}/{$id}");
    }

    public function update_anggota($p = 1, $o = 0, $id_kk = 0, $id = 0)
    {
        $this->keluarga_model->update_anggota($id);
        return redirect()->to("keluarga/anggota/{$p}/{$o}/{$id_kk}");
    }

    public function delete_anggota($p = 1, $o = 0, $kk = 0, $id = '')
    {
        $this->keluarga_model->rem_anggota($kk, $id);
        return redirect()->to("keluarga/anggota/{$p}/{$o}/{$kk}");
    }

    public function delete_all_anggota($p = 1, $o = 0, $kk = 0)
    {
        $this->keluarga_model->rem_all_anggota($kk);
        return redirect()->to("keluarga/anggota/{$p}/{$o}/{$kk}");
    }

    public function pindah_proses($id = 0)
    {
        $id_cluster = $_POST['id_cluster'];
        $this->keluarga_model->pindah_proses($id, $id_cluster);
        return redirect()->to('keluarga');
    }

    public function ajax_penduduk_pindah($id = 0)
    {
        $data['dusun'] = $this->penduduk_model->list_dusun();

        $data['form_action'] = site_url("keluarga/pindah_proses/{$id}");
        echo view('sid/kependudukan/ajax_pindah_form', $data);
    }

    public function ajax_penduduk_pindah_rw($dusun = '')
    {
        $rw = $this->penduduk_model->list_rw($dusun);

        echo "<td>RW</td>
		<td><select name='rw' onchange=RWSel('" . $dusun . "',this.value)>
		<option value=''>Pilih RW&nbsp;</option>";

        foreach ($rw as $data) {
            echo '<option>' . $data['rw'] . '</option>';
        }
        echo '</select>
		</td>';
    }

    public function ajax_penduduk_pindah_rt($dusun = '', $rw = '')
    {
        $rt = $this->penduduk_model->list_rt($dusun, $rw);

        echo "<td>RT</td>
		<td><select name='rt'>
		<option value=''>Pilih RT&nbsp;</option>";

        foreach ($rt as $data) {
            echo '<option value=' . $data['rt'] . '>' . $data['rt'] . '</option>';
        }
        echo '</select>
		</td>';
    }

    public function statistik($tipe = 0, $nomor = 0, $p = 1, $o = 0)
    {
        $data = [];
        $nav  = [];

        $data['p']    = $p;
        $data['o']    = $o;
        $data['tipe'] = $tipe;
        if (isset($_SESSION['cari'])) {
            $data['cari'] = $_SESSION['cari'];
        } else {
            $data['cari'] = '';
        }

        if (isset($_SESSION['filter'])) {
            $data['filter'] = $_SESSION['filter'];
        } else {
            $data['filter'] = '';
        }
        if (isset($_SESSION['raskin'])) {
            $data['raskin'] = $_SESSION['raskin'];
        } else {
            $data['raskin'] = '';
        }
        if (isset($_SESSION['id_blt'])) {
            $data['id_blt'] = $_SESSION['id_blt'];
        } else {
            $data['id_blt'] = '';
        }

        if (isset($_SESSION['id_bos'])) {
            $data['id_bos'] = $_SESSION['id_bos'];
        } else {
            $data['id_bos'] = '';
        }

        if (isset($_SESSION['id_pkh'])) {
            $data['id_pkh'] = $_SESSION['id_pkh'];
        } else {
            $data['id_pkh'] = '';
        }

        if (isset($_SESSION['id_jampersal'])) {
            $data['id_jampersal'] = $_SESSION['id_jampersal'];
        } else {
            $data['id_jampersal'] = '';
        }
        if (isset($_SESSION['id_bedah_rumah'])) {
            $data['id_bedah_rumah'] = $_SESSION['id_bedah_rumah'];
        } else {
            $data['id_bedah_rumah'] = '';
        }
        if (isset($_POST['per_page'])) {
            $_SESSION['per_page'] = $_POST['per_page'];
        }
        $data['per_page'] = $_SESSION['per_page'];

        if (isset($_SESSION['dusun'])) {
            $data['dusun']   = $_SESSION['dusun'];
            $data['list_rw'] = $this->penduduk_model->list_rw($data['dusun']);

            if (isset($_SESSION['rw'])) {
                $data['rw']      = $_SESSION['rw'];
                $data['list_rt'] = $this->penduduk_model->list_rt($data['dusun'], $data['rw']);

                if (isset($_SESSION['rt'])) {
                    $data['rt'] = $_SESSION['rt'];
                } else {
                    $data['rt'] = '';
                }
            } else {
                $data['rw'] = '';
            }
        } else {
            $data['dusun'] = '';
            $data['rw']    = '';
            $data['rt']    = '';
        }

        switch ($tipe) {
            case 21: $_SESSION['kelas'] = $nomor;
                $pre                    = 'KELAS SOSIAL : ';
                break;

            case 22: $_SESSION['raskin'] = $nomor;
                $pre                     = 'RASKIN : ';
                break;

            case 23: $_SESSION['id_blt'] = $nomor;
                $pre                     = 'BLT : ';
                break;

            case 24: $_SESSION['id_bos'] = $nomor;
                $pre                     = 'BOS : ';
                break;

            case 25: $_SESSION['id_pkh'] = $nomor;
                $pre                     = 'PKH : ';
                break;

            case 26: $_SESSION['id_jampersal'] = $nomor;
                $pre                           = 'JAMPERSAL : ';
                break;

            case 27: $_SESSION['id_bedah_rumah'] = $nomor;
                $pre                             = 'BEDAH RUMAH : ';
                break;
        }
        $data['grup']       = $this->user_model->sesi_grup($_SESSION['sesi']);
        $data['paging']     = $this->keluarga_model->paging_statistik($p, $o);
        $data['main']       = $this->keluarga_model->list_data_statistik($tipe, $o, $data['paging']->offset, $data['paging']->per_page);
        $data['keyword']    = $this->keluarga_model->autocomplete();
        $data['list_dusun'] = $this->penduduk_model->list_dusun();
        $judul              = $this->keluarga_model->get_judul_statistik($tipe, $nomor);
        if ($judul['nama']) {
            $_SESSION['judul_statistik'] = $pre . $judul['nama'];
        } else {
            unset($_SESSION['judul_statistik']);
        }
        $nav['act'] = 1;
        $header     = $this->header_model->get_data();

        echo view('header', $header);
        echo view('sid/nav', $nav);
        echo view('sid/kependudukan/keluarga_statistik', $data);
        echo view('footer');
        // return redirect()->to('keluarga');
    }

    public function cetak_statistik($tipe = 0)
    {
        $data = [];

        $data['main'] = $this->keluarga_model->list_data_statistik($tipe);
        echo view('sid/kependudukan/keluarga_print', $data);
    }
}
