<?php

namespace App\Controllers;

class Keluar extends BaseController
{
    public function initController(
        \CodeIgniter\HTTP\RequestInterface $request,
        \CodeIgniter\HTTP\ResponseInterface $response,
        \Psr\Log\LoggerInterface $logger
    ) {
        parent::initController($request, $response, $logger);

        $this->load->model('user_model');
        $this->load->model('surat_keluar_model');
        $this->load->model('surat_model');
        $grup = $this->user_model->sesi_grup($_SESSION['sesi']);
        if ($grup !== '1' && $grup !== '2' && $grup !== '3') {
            return redirect()->to('siteman');
        }
        $this->load->model('header_model');
    }

    public function clear()
    {
        unset($_SESSION['cari'], $_SESSION['filter']);

        return redirect()->to('keluar');
    }

    public function index($p = 1, $o = 0)
    {
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
        if (isset($_POST['per_page'])) {
            $_SESSION['per_page'] = $_POST['per_page'];
        }
        $data['per_page'] = $_SESSION['per_page'];
        $data['paging']   = $this->surat_keluar_model->paging($p, $o);
        $data['main']     = $this->surat_keluar_model->list_data($o, $data['paging']->offset, $data['paging']->per_page);
        $data['keyword']  = $this->surat_keluar_model->autocomplete();
        $header           = $this->header_model->get_data();
        $nav['act']       = 2;
        echo view('header', $header);

        echo view('surat/nav', $nav);
        echo view('surat/surat_keluar', $data);
        echo view('footer');
    }

    public function search()
    {
        $cari = $this->request->getPost('cari');
        if ($cari !== '') {
            $_SESSION['cari'] = $cari;
        } else {
            unset($_SESSION['cari']);
        }
        return redirect()->to('keluar');
    }

    public function perorangan($nik = 0, $p = 1, $o = 0)
    {
        if (isset($_POST['nik'])) {
            $nik = $_POST['nik'];
        }
        if ($nik !== 0) {
            $data['individu'] = $this->surat_model->get_penduduk($nik);
        } else {
            $data['individu'] = null;
        }
        $data['p'] = $p;
        $data['o'] = $o;
        if (isset($_POST['per_page'])) {
            $_SESSION['per_page'] = $_POST['per_page'];
        }
        $data['per_page']    = $_SESSION['per_page'];
        $data['paging']      = $this->surat_keluar_model->paging_perorangan($nik, $p, $o);
        $data['main']        = $this->surat_keluar_model->list_data_surat($nik, $o, $data['paging']->offset, $data['paging']->per_page);
        $data['penduduk']    = $this->surat_model->list_penduduk();
        $data['form_action'] = site_url("sid_surat_keluar/perorangan/{$nik}");
        $data['nik']['no']   = $nik;
        $nav['act']          = 2;
        $header              = $this->header_model->get_data();
        echo view('header', $header);

        echo view('surat/nav', $nav);
        echo view('surat/surat_keluar_perorangan', $data);
        echo view('footer');
    }

    public function graph()
    {
        $data['form_action'] = site_url('sid_cetak_surat/print_surat_ket_pengantar');
        $nav['act']          = 2;
        $header              = $this->header_model->get_data();
        $data['stat']        = $this->surat_keluar_model->grafik();
        echo view('header', $header);

        echo view('surat/nav', $nav);
        echo view('surat/surat_keluar_graph', $data);
        echo view('footer');
    }

    public function filter()
    {
        $filter = $this->request->getPost('nik');
        if ($filter !== 0) {
            $_SESSION['filter'] = $filter;
        } else {
            unset($_SESSION['filter']);
        }
        return redirect()->to('keluar/perorangan');
    }

    public function nik()
    {
        $nik = $this->request->getPost('nik');
        if ($nik !== 0) {
            $_SESSION['nik'] = $_POST['nik'];
        } else {
            unset($_SESSION['nik']);
        }
        return redirect()->to('keluar/perorangan');
    }
}
