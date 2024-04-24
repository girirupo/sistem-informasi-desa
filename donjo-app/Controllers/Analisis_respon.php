<?php

namespace App\Controllers;

class Analisis_respon extends BaseController
{
    public function initController(
        \CodeIgniter\HTTP\RequestInterface $request,
        \CodeIgniter\HTTP\ResponseInterface $response,
        \Psr\Log\LoggerInterface $logger
    ) {
        parent::initController($request, $response, $logger);

        unset($_SESSION['delik']);
        $this->load->model('analisis_respon_model');
        $this->load->model('user_model');
        $this->load->model('header_model');
        $grup = $this->user_model->sesi_grup($_SESSION['sesi']);
        if ($grup !== '1') {
            return redirect()->to('siteman');
        }
        $_SESSION['submenu']  = 'Input Data';
        $_SESSION['asubmenu'] = 'analisis_respon';
    }

    public function clear()
    {
        unset($_SESSION['cari'], $_SESSION['dusun'], $_SESSION['rw'], $_SESSION['rt'], $_SESSION['isi']);

        $_SESSION['per_page'] = 50;
        return redirect()->to('analisis_respon');
    }

    public function leave()
    {
        $id = $_SESSION['analisis_master'];
        unset($_SESSION['analisis_master']);
        return redirect()->to("analisis_master/menu/{$id}");
    }

    public function index($p = 1, $o = 0)
    {
        unset($_SESSION['cari2']);
        $data['p'] = $p;
        $data['o'] = $o;

        if (isset($_SESSION['cari'])) {
            $data['cari'] = $_SESSION['cari'];
        } else {
            $data['cari'] = '';
        }

        if (isset($_SESSION['isi'])) {
            $data['isi'] = $_SESSION['isi'];
        } else {
            $data['isi'] = '';
        }

        if (isset($_SESSION['dusun'])) {
            $data['dusun']   = $_SESSION['dusun'];
            $data['list_rw'] = $this->analisis_respon_model->list_rw($data['dusun']);

            if (isset($_SESSION['rw'])) {
                $data['rw']      = $_SESSION['rw'];
                $data['list_rt'] = $this->analisis_respon_model->list_rt($data['dusun'], $data['rw']);

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

        if (isset($_POST['per_page'])) {
            $_SESSION['per_page'] = $_POST['per_page'];
        }
        $data['per_page'] = $_SESSION['per_page'];

        $data['list_dusun']       = $this->analisis_respon_model->list_dusun();
        $data['paging']           = $this->analisis_respon_model->paging($p, $o);
        $data['main']             = $this->analisis_respon_model->list_data($o, $data['paging']->offset, $data['paging']->per_page);
        $data['keyword']          = $this->analisis_respon_model->autocomplete();
        $data['analisis_master']  = $this->analisis_respon_model->get_analisis_master();
        $data['analisis_periode'] = $this->analisis_respon_model->get_periode();

        $header = $this->header_model->get_data();

        echo view('header', $header);
        echo view('analisis_master/nav');
        echo view('analisis_respon/table', $data);
        echo view('footer');
    }

    public function kuisioner($p = 1, $o = 0, $id = '', $fs = 0)
    {
        if ($fs === 1) {
            $_SESSION['fullscreen'] = 1;
        }

        if ($fs === 2) {
            unset($_SESSION['fullscreen']);
        }

        if ($fs !== 0) {
            return redirect()->to("analisis_respon/kuisioner/{$p}/{$o}/{$id}");
        }

        $data['p']  = $p;
        $data['o']  = $o;
        $data['id'] = $id;

        $data['analisis_master'] = $this->analisis_respon_model->get_analisis_master();
        $data['subjek']          = $this->analisis_respon_model->get_subjek($id);
        $data['list_jawab']      = $this->analisis_respon_model->list_indikator($id);
        $data['list_bukti']      = $this->analisis_respon_model->list_bukti($id);
        $data['list_anggota']    = $this->analisis_respon_model->list_anggota($id);
        $data['form_action']     = site_url("analisis_respon/update_kuisioner/{$p}/{$o}/{$id}");

        $header = $this->header_model->get_data();
        if (isset($_SESSION['fullscreen'])) {
            echo view('header-min', $header);
        } else {
            echo view('header', $header);
            echo view('analisis_master/nav');
        }

        echo view('analisis_respon/form', $data);

        echo view('footer');
    }

    public function update_kuisioner($p = 1, $o = 0, $id = '')
    {
        $this->analisis_respon_model->update_kuisioner($id);
        return redirect()->to("analisis_respon/kuisioner/{$p}/{$o}/{$id}");
    }

    // CHILD--------------------
    public function kuisioner_child($p = 1, $o = 0, $id = '', $idc = '')
    {
        $data['p'] = $p;
        $data['o'] = $o;

        $data['list_jawab']  = $this->analisis_respon_model->list_indikator_child($idc);
        $data['form_action'] = site_url("analisis_respon/update_kuisioner_child/{$p}/{$o}/{$id}/{$idc}");

        echo view('analisis_respon/form_ajax', $data);
    }

    public function update_kuisioner_child($p = 1, $o = 0, $id = '', $idc = '')
    {
        $per = $this->analisis_respon_model->get_periode_child();
        $this->analisis_respon_model->update_kuisioner($idc, $per);
        return redirect()->to("analisis_respon/kuisioner/{$p}/{$o}/{$id}");
    }

    public function aturan_ajax()
    {
        echo view('analisis_respon/import/aturan_ajax');
    }

    public function aturan_unduh()
    {
        $data['main'] = $this->analisis_respon_model->aturan_unduh();
        echo view('analisis_respon/import/aturan_unduh', $data);
    }

    public function data_ajax()
    {
        echo view('analisis_respon/import/data_ajax');
    }

    public function data_unduh($p = 0, $o = 0)
    {
        $data['main']      = $this->analisis_respon_model->data_unduh($p, $o);
        $data['periode']   = $this->analisis_respon_model->get_aktif_periode();
        $data['indikator'] = $this->analisis_respon_model->indikator_unduh($p, $o);
        echo view('analisis_respon/import/data_unduh', $data);
    }

    public function import($op = 0)
    {
        $data['form_action'] = site_url("analisis_respon/import_proses/{$op}");
        echo view('analisis_respon/import/import', $data);
    }

    public function satu_jiwa($op = 0)
    {
        $this->analisis_respon_model->satu_jiwa($op);
        return redirect()->to('analisis_respon');
    }

    public function dua_dunia($op = 0)
    {
        $this->analisis_respon_model->dua_dunia($op);
        return redirect()->to('analisis_respon');
    }

    public function import_proses($op = 0)
    {
        $this->analisis_respon_model->import_respon($op);
        return redirect()->to('analisis_respon');
    }

    public function search()
    {
        $cari = $this->request->getPost('cari');
        if ($cari !== '') {
            $_SESSION['cari'] = $cari;
        } else {
            unset($_SESSION['cari']);
        }
        return redirect()->to('analisis_respon');
    }

    public function isi()
    {
        $isi = $this->request->getPost('isi');
        if ($isi !== '') {
            $_SESSION['isi'] = $isi;
        } else {
            unset($_SESSION['isi']);
        }
        return redirect()->to('analisis_respon');
    }

    public function dusun()
    {
        unset($_SESSION['rw'], $_SESSION['rt']);

        $dusun = $this->request->getPost('dusun');
        if ($dusun !== '') {
            $_SESSION['dusun'] = $dusun;
        } else {
            unset($_SESSION['dusun']);
        }
        return redirect()->to('analisis_respon');
    }

    public function rw()
    {
        unset($_SESSION['rt']);
        $rw = $this->request->getPost('rw');
        if ($rw !== '') {
            $_SESSION['rw'] = $rw;
        } else {
            unset($_SESSION['rw']);
        }
        return redirect()->to('analisis_respon');
    }

    public function rt()
    {
        $rt = $this->request->getPost('rt');
        if ($rt !== '') {
            $_SESSION['rt'] = $rt;
        } else {
            unset($_SESSION['rt']);
        }
        return redirect()->to('analisis_respon');
    }
}
