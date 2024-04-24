<?php

namespace App\Controllers;

class Area extends BaseController
{
    public function initController(
        \CodeIgniter\HTTP\RequestInterface $request,
        \CodeIgniter\HTTP\ResponseInterface $response,
        \Psr\Log\LoggerInterface $logger
    ) {
        parent::initController($request, $response, $logger);

        $this->load->model('config_model');
        $this->load->model('header_model');
        $this->load->model('plan_area_model');
        $this->load->model('user_model');
    }

    public function clear()
    {
        unset($_SESSION['cari'], $_SESSION['filter'], $_SESSION['polygon'], $_SESSION['subpolygon']);

        return redirect()->to('area');
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
        if (isset($_SESSION['polygon'])) {
            $data['polygon'] = $_SESSION['polygon'];
        } else {
            $data['polygon'] = '';
        }
        if (isset($_SESSION['subpolygon'])) {
            $data['subpolygon'] = $_SESSION['subpolygon'];
        } else {
            $data['subpolygon'] = '';
        }
        if (isset($_POST['per_page'])) {
            $_SESSION['per_page'] = $_POST['per_page'];
        }
        $data['per_page'] = $_SESSION['per_page'];

        $data['paging']          = $this->plan_area_model->paging($p, $o);
        $data['main']            = $this->plan_area_model->list_data($o, $data['paging']->offset, $data['paging']->per_page);
        $data['keyword']         = $this->plan_area_model->autocomplete();
        $data['list_polygon']    = $this->plan_area_model->list_polygon();
        $data['list_subpolygon'] = $this->plan_area_model->list_subpolygon();
        $header                  = $this->header_model->get_data();
        $nav['act']              = 4;

        echo view('header-gis', $header);

        echo view('plan/nav', $nav);
        echo view('area/table', $data);
        echo view('footer');
    }

    public function form($p = 1, $o = 0, $id = '')
    {
        $data['p'] = $p;
        $data['o'] = $o;

        $data['desa']         = $this->config_model->get_data();
        $data['list_polygon'] = $this->plan_area_model->list_polygon();
        $data['dusun']        = $this->plan_area_model->list_dusun();

        if ($id) {
            $data['area']        = $this->plan_area_model->get_area($id);
            $data['form_action'] = site_url("area/update/{$id}/{$p}/{$o}");
        } else {
            $data['area']        = null;
            $data['form_action'] = site_url('area/insert');
        }
        $header = $this->header_model->get_data();

        $nav['act'] = 4;
        echo view('header-gis', $header);

        echo view('plan/nav', $nav);
        echo view('area/form', $data);
        echo view('footer');
    }

    public function ajax_area_maps($p = 1, $o = 0, $id = '')
    {
        $data['p'] = $p;
        $data['o'] = $o;
        if ($id) {
            $data['area'] = $this->plan_area_model->get_area($id);
        } else {
            $data['area'] = null;
        }

        $data['desa']        = $this->config_model->get_data();
        $data['form_action'] = site_url("area/update_maps/{$p}/{$o}/{$id}");
        echo view('area/maps', $data);
    }

    public function update_maps($p = 1, $o = 0, $id = '')
    {
        $this->plan_area_model->update_position($id);
        return redirect()->to("area/index/{$p}/{$o}");
    }

    public function search()
    {
        $cari = $this->request->getPost('cari');
        if ($cari !== '') {
            $_SESSION['cari'] = $cari;
        } else {
            unset($_SESSION['cari']);
        }
        return redirect()->to('area');
    }

    public function filter()
    {
        $filter = $this->request->getPost('filter');
        if ($filter !== 0) {
            $_SESSION['filter'] = $filter;
        } else {
            unset($_SESSION['filter']);
        }
        return redirect()->to('area');
    }

    public function polygon()
    {
        $polygon = $this->request->getPost('polygon');
        if ($polygon !== 0) {
            $_SESSION['polygon'] = $polygon;
        } else {
            unset($_SESSION['polygon']);
        }
        return redirect()->to('area');
    }

    public function subpolygon()
    {
        unset($_SESSION['polygon']);
        $subpolygon = $this->request->getPost('subpolygon');
        if ($subpolygon !== 0) {
            $_SESSION['subpolygon'] = $subpolygon;
        } else {
            unset($_SESSION['subpolygon']);
        }
        return redirect()->to('area');
    }

    public function insert($tip = 1)
    {
        $this->plan_area_model->insert($tip);
        return redirect()->to("area/index/{$tip}");
    }

    public function update($id = '', $p = 1, $o = 0)
    {
        $this->plan_area_model->update($id);
        return redirect()->to("area/index/{$p}/{$o}");
    }

    public function delete($p = 1, $o = 0, $id = '')
    {
        $this->plan_area_model->delete($id);
        return redirect()->to("area/index/{$p}/{$o}");
    }

    public function delete_all($p = 1, $o = 0)
    {
        $this->plan_area_model->delete_all();
        return redirect()->to("area/index/{$p}/{$o}");
    }

    public function area_lock($id = '')
    {
        $this->plan_area_model->area_lock($id, 1);
        return redirect()->to("area/index/{$p}/{$o}");
    }

    public function area_unlock($id = '')
    {
        $this->plan_area_model->area_lock($id, 2);
        return redirect()->to("area/index/{$p}/{$o}");
    }
}
