<?php

namespace App\Controllers;

class Kelompok_master extends BaseController
{
    public function initController(
        \CodeIgniter\HTTP\RequestInterface $request,
        \CodeIgniter\HTTP\ResponseInterface $response,
        \Psr\Log\LoggerInterface $logger
    ) {
        parent::initController($request, $response, $logger);

        $this->load->model('kelompok_master_model');
        $this->load->model('user_model');
        $this->load->model('header_model');
        $grup = $this->user_model->sesi_grup($_SESSION['sesi']);
        if ($grup !== '1') {
            return redirect()->to('siteman');
        }
    }

    public function clear()
    {
        unset($_SESSION['cari'], $_SESSION['filter'], $_SESSION['state']);

        return redirect()->to('kelompok_master');
    }

    public function index($p = 1, $o = 0)
    {
        unset($_SESSION['kelompok_master']);
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
        if (isset($_SESSION['state'])) {
            $data['state'] = $_SESSION['state'];
        } else {
            $data['state'] = '';
        }
        if (isset($_POST['per_page'])) {
            $_SESSION['per_page'] = $_POST['per_page'];
        }
        $data['per_page'] = $_SESSION['per_page'];

        $data['paging']  = $this->kelompok_master_model->paging($p, $o);
        $data['main']    = $this->kelompok_master_model->list_data($o, $data['paging']->offset, $data['paging']->per_page);
        $data['keyword'] = $this->kelompok_master_model->autocomplete();

        $header = $this->header_model->get_data();

        echo view('header', $header);
        $nav['act'] = 4;

        echo view('sid/nav', $nav);
        echo view('kelompok_master/table', $data);
        echo view('footer');
    }

    public function form($p = 1, $o = 0, $id = '')
    {
        $data['p'] = $p;
        $data['o'] = $o;

        if ($id) {
            $data['kelompok_master'] = $this->kelompok_master_model->get_kelompok_master($id);
            $data['form_action']     = site_url("kelompok_master/update/{$p}/{$o}/{$id}");
        } else {
            $data['kelompok_master'] = null;
            $data['form_action']     = site_url('kelompok_master/insert');
        }

        $header = $this->header_model->get_data();

        echo view('header', $header);
        $nav['act'] = 4;

        echo view('sid/nav', $nav);
        echo view('kelompok_master/form', $data);
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
        return redirect()->to('kelompok_master');
    }

    public function filter()
    {
        $filter = $this->request->getPost('filter');
        if ($filter !== 0) {
            $_SESSION['filter'] = $filter;
        } else {
            unset($_SESSION['filter']);
        }
        return redirect()->to('kelompok_master');
    }

    public function state()
    {
        $filter = $this->request->getPost('state');
        if ($filter !== 0) {
            $_SESSION['state'] = $filter;
        } else {
            unset($_SESSION['state']);
        }
        return redirect()->to('kelompok_master');
    }

    public function insert()
    {
        $this->kelompok_master_model->insert();
        return redirect()->to('kelompok_master');
    }

    public function update($p = 1, $o = 0, $id = '')
    {
        $this->kelompok_master_model->update($id);
        return redirect()->to("kelompok_master/index/{$p}/{$o}");
    }

    public function delete($p = 1, $o = 0, $id = '')
    {
        $this->kelompok_master_model->delete($id);
        return redirect()->to("kelompok_master/index/{$p}/{$o}");
    }

    public function delete_all($p = 1, $o = 0)
    {
        $this->kelompok_master_model->delete_all();
        return redirect()->to("kelompok_master/index/{$p}/{$o}");
    }
}
