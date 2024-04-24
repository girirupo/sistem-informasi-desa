<?php

namespace App\Controllers;

class Line extends BaseController
{
    public function initController(
        \CodeIgniter\HTTP\RequestInterface $request,
        \CodeIgniter\HTTP\ResponseInterface $response,
        \Psr\Log\LoggerInterface $logger
    ) {
        parent::initController($request, $response, $logger);

        $this->load->model('user_model');
        $this->load->model('header_model');
        $this->load->model('plan_line_model');
    }

    public function clear()
    {
        unset($_SESSION['cari'], $_SESSION['filter']);

        return redirect()->to('line');
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

        $data['paging']  = $this->plan_line_model->paging($p, $o);
        $data['main']    = $this->plan_line_model->list_data($o, $data['paging']->offset, $data['paging']->per_page);
        $data['keyword'] = $this->plan_line_model->autocomplete();
        $header          = $this->header_model->get_data();
        $nav['act']      = 2;

        echo view('header-gis', $header);

        echo view('plan/nav', $nav);
        echo view('line/table', $data);
        echo view('footer');
    }

    public function form($p = 1, $o = 0, $id = '')
    {
        $data['p'] = $p;
        $data['o'] = $o;

        if ($id) {
            $data['line']        = $this->plan_line_model->get_line($id);
            $data['form_action'] = site_url("line/update/{$id}/{$p}/{$o}");
        } else {
            $data['line']        = null;
            $data['form_action'] = site_url('line/insert');
        }
        $header = $this->header_model->get_data();

        $nav['act'] = 2;
        echo view('header-gis', $header);

        echo view('plan/nav', $nav);
        echo view('line/form', $data);
        echo view('footer');
    }

    public function sub_line($line = 1)
    {
        $data['subline'] = $this->plan_line_model->list_sub_line($line);
        $data['line']    = $line;
        $header          = $this->header_model->get_data();
        $nav['act']      = 2;

        echo view('header-gis', $header);

        echo view('plan/nav', $nav);
        echo view('line/sub_line_table', $data);
        echo view('footer');
    }

    public function ajax_add_sub_line($line = 0, $id = 0)
    {
        if ($id) {
            $data['line']        = $this->plan_line_model->get_line($id);
            $data['form_action'] = site_url("line/update_sub_line/{$line}/{$id}");
        } else {
            $data['line']        = null;
            $data['form_action'] = site_url("line/insert_sub_line/{$line}");
        }
        echo view('line/ajax_add_sub_line_form', $data);
    }

    public function search()
    {
        $cari = $this->request->getPost('cari');
        if ($cari !== '') {
            $_SESSION['cari'] = $cari;
        } else {
            unset($_SESSION['cari']);
        }
        return redirect()->to('line');
    }

    public function filter()
    {
        $filter = $this->request->getPost('filter');
        if ($filter !== 0) {
            $_SESSION['filter'] = $filter;
        } else {
            unset($_SESSION['filter']);
        }
        return redirect()->to('line');
    }

    public function insert($tip = 1)
    {
        $this->plan_line_model->insert($tip);
        return redirect()->to("line/index/{$tip}");
    }

    public function update($id = '', $p = 1, $o = 0)
    {
        $this->plan_line_model->update($id);
        return redirect()->to("line/index/{$p}/{$o}");
    }

    public function delete($p = 1, $o = 0, $id = '')
    {
        $this->plan_line_model->delete($id);
        return redirect()->to("line/index/{$p}/{$o}");
    }

    public function delete_all($p = 1, $o = 0)
    {
        $this->plan_line_model->delete_all();
        return redirect()->to("line/index/{$p}/{$o}");
    }

    public function line_lock($id = '')
    {
        $this->plan_line_model->line_lock($id, 1);
        return redirect()->to("line/index/{$p}/{$o}");
    }

    public function line_unlock($id = '')
    {
        $this->plan_line_model->line_lock($id, 2);
        return redirect()->to("line/index/{$p}/{$o}");
    }

    public function insert_sub_line($line = '')
    {
        $this->plan_line_model->insert_sub_line($line);
        return redirect()->to("line/sub_line/{$line}");
    }

    public function update_sub_line($line = '', $id = '')
    {
        $this->plan_line_model->update_sub_line($id);
        return redirect()->to("line/sub_line/{$line}");
    }

    public function delete_sub_line($line = '', $id = '')
    {
        $this->plan_line_model->delete_sub_line($id);
        return redirect()->to("line/sub_line/{$line}");
    }

    public function delete_all_sub_line($line = '')
    {
        $this->plan_line_model->delete_all_sub_line();
        return redirect()->to("line/sub_line/{$line}");
    }

    public function line_lock_sub_line($line = '', $id = '')
    {
        $this->plan_line_model->line_lock($id, 1);
        return redirect()->to("line/sub_line/{$line}");
    }

    public function line_unlock_sub_line($line = '', $id = '')
    {
        $this->plan_line_model->line_lock($id, 2);
        return redirect()->to("line/sub_line/{$line}");
    }
}
