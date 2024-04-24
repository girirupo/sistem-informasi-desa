<?php

namespace App\Controllers;

class Mandiri extends BaseController
{
    public function initController(
        \CodeIgniter\HTTP\RequestInterface $request,
        \CodeIgniter\HTTP\ResponseInterface $response,
        \Psr\Log\LoggerInterface $logger
    ) {
        parent::initController($request, $response, $logger);

        $this->load->model('user_model');
        $this->load->model('mandiri_model');
        $grup = $this->user_model->sesi_grup($_SESSION['sesi']);
        if ($grup !== '1' && $grup !== '2') {
            return redirect()->to('siteman');
        }
        $this->load->model('header_model');
    }

    public function clear()
    {
        unset($_SESSION['cari'], $_SESSION['filter']);

        return redirect()->to('mandiri');
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
        $data['paging']   = $this->mandiri_model->paging($p, $o);
        $data['main']     = $this->mandiri_model->list_data($o, $data['paging']->offset, $data['paging']->per_page);
        $data['keyword']  = $this->mandiri_model->autocomplete();

        $header     = $this->header_model->get_data();
        $nav['act'] = 1;
        echo view('header', $header);

        echo view('lapor/nav', $nav);
        echo view('mandiri/mandiri', $data);
        echo view('footer');
    }

    public function ajax_pin($p = 1, $o = 0, $id = 0)
    {
        $data['penduduk']    = $this->mandiri_model->list_penduduk();
        $data['form_action'] = site_url("mandiri/insert/{$id}");
        echo view('mandiri/ajax_pin', $data);
    }

    public function search()
    {
        $cari = $this->request->getPost('cari');
        if ($cari !== '') {
            $_SESSION['cari'] = $cari;
        } else {
            unset($_SESSION['cari']);
        }
        return redirect()->to('mandiri');
    }

    public function filter()
    {
        $filter = $this->request->getPost('nik');
        if ($filter !== 0) {
            $_SESSION['filter'] = $filter;
        } else {
            unset($_SESSION['filter']);
        }
        return redirect()->to('mandiri/perorangan');
    }

    public function nik()
    {
        $nik = $this->request->getPost('nik');
        if ($nik !== 0) {
            $_SESSION['nik'] = $_POST['nik'];
        } else {
            unset($_SESSION['nik']);
        }
        return redirect()->to('mandiri/perorangan');
    }

    public function insert()
    {
        $pin             = $this->mandiri_model->insert();
        $_SESSION['pin'] = $pin;
        return redirect()->to('mandiri');
    }

    public function ajax_pin_show($pin = '')
    {
        return redirect()->to('mandiri');
    }
}
