<?php

namespace App\Controllers;

class Plan extends BaseController
{
    public function initController(
        \CodeIgniter\HTTP\RequestInterface $request,
        \CodeIgniter\HTTP\ResponseInterface $response,
        \Psr\Log\LoggerInterface $logger
    ) {
        parent::initController($request, $response, $logger);

        $this->load->model('config_model');
        $this->load->model('header_model');
        $this->load->model('plan_lokasi_model');
        $this->load->model('user_model');
        $grup = $this->user_model->sesi_grup($_SESSION['sesi']);
        if ($grup !== '1') {
            return redirect()->to('siteman');
        }
    }

    public function clear()
    {
        unset($_SESSION['cari'], $_SESSION['filter'], $_SESSION['point'], $_SESSION['subpoint']);

        return redirect()->to('plan');
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
        if (isset($_SESSION['point'])) {
            $data['point'] = $_SESSION['point'];
        } else {
            $data['point'] = '';
        }
        if (isset($_SESSION['subpoint'])) {
            $data['subpoint'] = $_SESSION['subpoint'];
        } else {
            $data['subpoint'] = '';
        }
        if (isset($_POST['per_page'])) {
            $_SESSION['per_page'] = $_POST['per_page'];
        }
        $data['per_page'] = $_SESSION['per_page'];

        $data['paging']        = $this->plan_lokasi_model->paging($p, $o);
        $data['main']          = $this->plan_lokasi_model->list_data($o, $data['paging']->offset, $data['paging']->per_page);
        $data['keyword']       = $this->plan_lokasi_model->autocomplete();
        $data['list_point']    = $this->plan_lokasi_model->list_point();
        $data['list_subpoint'] = $this->plan_lokasi_model->list_subpoint();

        $header     = $this->header_model->get_data();
        $nav['act'] = 3;

        echo view('header-gis', $header);
        echo view('plan/nav', $nav);
        echo view('lokasi/table', $data);
        echo view('footer');
    }

    public function form($p = 1, $o = 0, $id = '')
    {
        $data['p'] = $p;
        $data['o'] = $o;

        $data['desa']       = $this->config_model->get_data();
        $data['list_point'] = $this->plan_lokasi_model->list_point();
        $data['dusun']      = $this->plan_lokasi_model->list_dusun();

        if ($id) {
            $data['lokasi']      = $this->plan_lokasi_model->get_lokasi($id);
            $data['form_action'] = site_url("plan/update/{$id}/{$p}/{$o}");
        } else {
            $data['lokasi']      = null;
            $data['form_action'] = site_url('plan/insert');
        }
        $header = $this->header_model->get_data();

        $nav['act'] = 3;
        echo view('header-gis', $header);

        echo view('plan/nav', $nav);
        echo view('lokasi/form', $data);
        echo view('footer');
    }

    public function ajax_lokasi_maps($p = 1, $o = 0, $id = '')
    {
        $data['p'] = $p;
        $data['o'] = $o;
        if ($id) {
            $data['lokasi'] = $this->plan_lokasi_model->get_lokasi($id);
        } else {
            $data['lokasi'] = null;
        }

        $data['desa']        = $this->config_model->get_data();
        $data['form_action'] = site_url("plan/update_maps/{$p}/{$o}/{$id}");
        echo view('lokasi/maps', $data);
    }

    public function update_maps($p = 1, $o = 0, $id = '')
    {
        $this->plan_lokasi_model->update_position($id);
        return redirect()->to("plan/index/{$p}/{$o}");
    }

    public function search()
    {
        $cari = $this->request->getPost('cari');
        if ($cari !== '') {
            $_SESSION['cari'] = $cari;
        } else {
            unset($_SESSION['cari']);
        }
        return redirect()->to('plan');
    }

    public function filter()
    {
        $filter = $this->request->getPost('filter');
        if ($filter !== 0) {
            $_SESSION['filter'] = $filter;
        } else {
            unset($_SESSION['filter']);
        }
        return redirect()->to('plan');
    }

    public function point()
    {
        $point = $this->request->getPost('point');
        if ($point !== 0) {
            $_SESSION['point'] = $point;
        } else {
            unset($_SESSION['point']);
        }
        return redirect()->to('plan');
    }

    public function subpoint()
    {
        unset($_SESSION['point']);
        $subpoint = $this->request->getPost('subpoint');
        if ($subpoint !== 0) {
            $_SESSION['subpoint'] = $subpoint;
        } else {
            unset($_SESSION['subpoint']);
        }
        return redirect()->to('plan');
    }

    public function insert($tip = 1)
    {
        $this->plan_lokasi_model->insert($tip);
        return redirect()->to("plan/index/{$tip}");
    }

    public function update($id = '', $p = 1, $o = 0)
    {
        $this->plan_lokasi_model->update($id);
        return redirect()->to("plan/index/{$p}/{$o}");
    }

    public function delete($p = 1, $o = 0, $id = '')
    {
        $this->plan_lokasi_model->delete($id);
        return redirect()->to("plan/index/{$p}/{$o}");
    }

    public function delete_all($p = 1, $o = 0)
    {
        $this->plan_lokasi_model->delete_all();
        return redirect()->to("plan/index/{$p}/{$o}");
    }

    public function lokasi_lock($id = '')
    {
        $this->plan_lokasi_model->lokasi_lock($id, 1);
        return redirect()->to("plan/index/{$p}/{$o}");
    }

    public function lokasi_unlock($id = '')
    {
        $this->plan_lokasi_model->lokasi_lock($id, 2);
        return redirect()->to("plan/index/{$p}/{$o}");
    }
}
