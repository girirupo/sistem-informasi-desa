<?php

namespace App\Controllers;

use Kenjis\CI3Compatible\Core\CI_Controller as BaseController;

class Point extends BaseController
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('user_model');
        $this->load->model('header_model');
        $this->load->model('plan_point_model');
    }

    public function clear()
    {
        session()->remove(['cari', 'filter']);

        return redirect()->to('point');
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

        $data['paging']  = $this->plan_point_model->paging($p, $o);
        $data['main']    = $this->plan_point_model->list_data($o, $data['paging']->offset, $data['paging']->per_page);
        $data['keyword'] = $this->plan_point_model->autocomplete();
        $header          = $this->header_model->get_data();
        $nav['act']      = 0;

        echo view('header', $header);
        echo view('plan/nav', $nav);
        echo view('point/table', $data);
        echo view('footer');
    }

    public function form($p = 1, $o = 0, $id = '')
    {
        $data['p'] = $p;
        $data['o'] = $o;

        if ($id) {
            $data['point']       = $this->plan_point_model->get_point($id);
            $data['form_action'] = site_url("point/update/{$id}/{$p}/{$o}");
        } else {
            $data['point']       = null;
            $data['form_action'] = site_url('point/insert');
        }
        $data['simbol'] = $this->plan_point_model->list_simbol();
        $header         = $this->header_model->get_data();

        $nav['act'] = 0;
        echo view('header', $header);

        echo view('plan/nav', $nav);
        echo view('point/form', $data);
        echo view('footer');
    }

    public function sub_point($point = 1)
    {
        $data['subpoint'] = $this->plan_point_model->list_sub_point($point);
        $data['point']    = $point;
        $header           = $this->header_model->get_data();
        $nav['act']       = 0;

        echo view('header', $header);

        echo view('plan/nav', $nav);
        echo view('point/sub_point_table', $data);
        echo view('footer');
    }

    public function ajax_add_sub_point($point = 0, $id = 0)
    {
        if ($id) {
            $data['point']       = $this->plan_point_model->get_point($id);
            $data['form_action'] = site_url("point/update_sub_point/{$point}/{$id}");
        } else {
            $data['point']       = null;
            $data['form_action'] = site_url("point/insert_sub_point/{$point}");
        }
        $data['simbol'] = $this->plan_point_model->list_simbol();
        echo view('point/ajax_add_sub_point_form', $data);
    }

    public function search()
    {
        $cari = $this->input->post('cari');
        if ($cari !== '') {
            $_SESSION['cari'] = $cari;
        } else {
            session()->remove('cari');
        }

        return redirect()->to('point');
    }

    public function filter()
    {
        $filter = $this->input->post('filter');
        if ($filter !== 0) {
            $_SESSION['filter'] = $filter;
        } else {
            session()->remove('filter');
        }

        return redirect()->to('point');
    }

    public function insert($tip = 1)
    {
        $this->plan_point_model->insert($tip);
        redirect("point/index/{$tip}");
    }

    public function update($id = '', $p = 1, $o = 0)
    {
        $this->plan_point_model->update($id);
        redirect("point/index/{$p}/{$o}");
    }

    public function delete($p = 1, $o = 0, $id = '')
    {
        $this->plan_point_model->delete($id);
        redirect("point/index/{$p}/{$o}");
    }

    public function delete_all($p = 1, $o = 0)
    {
        $this->plan_point_model->delete_all();
        redirect("point/index/{$p}/{$o}");
    }

    public function point_lock($id = '')
    {
        $this->plan_point_model->point_lock($id, 1);
        redirect("point/index/{$p}/{$o}");
    }

    public function point_unlock($id = '')
    {
        $this->plan_point_model->point_lock($id, 2);
        redirect("point/index/{$p}/{$o}");
    }

    public function insert_sub_point($point = '')
    {
        $this->plan_point_model->insert_sub_point($point);
        redirect("point/sub_point/{$point}");
    }

    public function update_sub_point($point = '', $id = '')
    {
        $this->plan_point_model->update_sub_point($id);
        redirect("point/sub_point/{$point}");
    }

    public function delete_sub_point($point = '', $id = '')
    {
        $this->plan_point_model->delete_sub_point($id);
        redirect("point/sub_point/{$point}");
    }

    public function delete_all_sub_point($point = '')
    {
        $this->plan_point_model->delete_all_sub_point();
        redirect("point/sub_point/{$point}");
    }

    public function point_lock_sub_point($point = '', $id = '')
    {
        $this->plan_point_model->point_lock($id, 1);
        redirect("point/sub_point/{$point}");
    }

    public function point_unlock_sub_point($point = '', $id = '')
    {
        $this->plan_point_model->point_lock($id, 2);
        redirect("point/sub_point/{$point}");
    }
}
