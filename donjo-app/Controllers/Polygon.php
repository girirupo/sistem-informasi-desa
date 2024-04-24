<?php

namespace App\Controllers;

class Polygon extends BaseController
{
    public function initController(
        \CodeIgniter\HTTP\RequestInterface $request,
        \CodeIgniter\HTTP\ResponseInterface $response,
        \Psr\Log\LoggerInterface $logger
    ) {
        parent::initController($request, $response, $logger);

        $this->load->model('user_model');

        $this->load->model('header_model');
        $this->load->model('plan_polygon_model');
    }

    public function clear()
    {
        unset($_SESSION['cari'], $_SESSION['filter']);

        return redirect()->to('polygon');
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

        $data['paging']  = $this->plan_polygon_model->paging($p, $o);
        $data['main']    = $this->plan_polygon_model->list_data($o, $data['paging']->offset, $data['paging']->per_page);
        $data['keyword'] = $this->plan_polygon_model->autocomplete();
        $header          = $this->header_model->get_data();
        $nav['act']      = 5;

        echo view('header-gis', $header);

        echo view('plan/nav', $nav);
        echo view('polygon/table', $data);
        echo view('footer');
    }

    public function form($p = 1, $o = 0, $id = '')
    {
        $data['p'] = $p;
        $data['o'] = $o;

        if ($id) {
            $data['polygon']     = $this->plan_polygon_model->get_polygon($id);
            $data['form_action'] = site_url("polygon/update/{$id}/{$p}/{$o}");
        } else {
            $data['polygon']     = null;
            $data['form_action'] = site_url('polygon/insert');
        }
        $header = $this->header_model->get_data();

        $nav['act'] = 5;
        echo view('header-gis', $header);

        echo view('plan/nav', $nav);
        echo view('polygon/form', $data);
        echo view('footer');
    }

    public function sub_polygon($polygon = 1)
    {
        $data['subpolygon'] = $this->plan_polygon_model->list_sub_polygon($polygon);
        $data['polygon']    = $polygon;
        $header             = $this->header_model->get_data();
        $nav['act']         = 5;

        echo view('header-gis', $header);

        echo view('plan/nav', $nav);
        echo view('polygon/sub_polygon_table', $data);
        echo view('footer');
    }

    public function ajax_add_sub_polygon($polygon = 0, $id = 0)
    {
        if ($id) {
            $data['polygon']     = $this->plan_polygon_model->get_polygon($id);
            $data['form_action'] = site_url("polygon/update_sub_polygon/{$polygon}/{$id}");
        } else {
            $data['polygon']     = null;
            $data['form_action'] = site_url("polygon/insert_sub_polygon/{$polygon}");
        }
        $header = $this->header_model->get_data();

        $nav['act'] = 5;
        echo view('header-gis', $header);

        echo view('plan/nav', $nav);
        echo view('polygon/ajax_add_sub_polygon_form', $data);
    }

    public function search()
    {
        $cari = $this->request->getPost('cari');
        if ($cari !== '') {
            $_SESSION['cari'] = $cari;
        } else {
            unset($_SESSION['cari']);
        }
        return redirect()->to('polygon');
    }

    public function filter()
    {
        $filter = $this->request->getPost('filter');
        if ($filter !== 0) {
            $_SESSION['filter'] = $filter;
        } else {
            unset($_SESSION['filter']);
        }
        return redirect()->to('polygon');
    }

    public function insert($tip = 1)
    {
        $this->plan_polygon_model->insert($tip);
        return redirect()->to("polygon/index/{$tip}");
    }

    public function update($id = '', $p = 1, $o = 0)
    {
        $this->plan_polygon_model->update($id);
        return redirect()->to("polygon/index/{$p}/{$o}");
    }

    public function delete($p = 1, $o = 0, $id = '')
    {
        $this->plan_polygon_model->delete($id);
        return redirect()->to("polygon/index/{$p}/{$o}");
    }

    public function delete_all($p = 1, $o = 0)
    {
        $this->plan_polygon_model->delete_all();
        return redirect()->to("polygon/index/{$p}/{$o}");
    }

    public function polygon_lock($id = '')
    {
        $this->plan_polygon_model->polygon_lock($id, 1);
        return redirect()->to("polygon/index/{$p}/{$o}");
    }

    public function polygon_unlock($id = '')
    {
        $this->plan_polygon_model->polygon_lock($id, 2);
        return redirect()->to("polygon/index/{$p}/{$o}");
    }

    public function insert_sub_polygon($polygon = '')
    {
        $this->plan_polygon_model->insert_sub_polygon($polygon);
        return redirect()->to("polygon/sub_polygon/{$polygon}");
    }

    public function update_sub_polygon($polygon = '', $id = '')
    {
        $this->plan_polygon_model->update_sub_polygon($id);
        return redirect()->to("polygon/sub_polygon/{$polygon}");
    }

    public function delete_sub_polygon($polygon = '', $id = '')
    {
        $this->plan_polygon_model->delete_sub_polygon($id);
        return redirect()->to("polygon/sub_polygon/{$polygon}");
    }

    public function delete_all_sub_polygon($polygon = '')
    {
        $this->plan_polygon_model->delete_all_sub_polygon();
        return redirect()->to("polygon/sub_polygon/{$polygon}");
    }

    public function polygon_lock_sub_polygon($polygon = '', $id = '')
    {
        $this->plan_polygon_model->polygon_lock($id, 1);
        return redirect()->to("polygon/sub_polygon/{$polygon}");
    }

    public function polygon_unlock_sub_polygon($polygon = '', $id = '')
    {
        $this->plan_polygon_model->polygon_lock($id, 2);
        return redirect()->to("polygon/sub_polygon/{$polygon}");
    }
}
