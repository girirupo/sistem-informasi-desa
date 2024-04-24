<?php

namespace App\Controllers;

class Man_user extends BaseController
{
    public function initController(
        \CodeIgniter\HTTP\RequestInterface $request,
        \CodeIgniter\HTTP\ResponseInterface $response,
        \Psr\Log\LoggerInterface $logger
    ) {
        parent::initController($request, $response, $logger);

        $this->load->model('user_model');
        $this->load->model('header_model');
        $grup = $this->user_model->sesi_grup($_SESSION['sesi']);
        if ($grup !== '1') {
            return redirect()->to('siteman');
        }
    }

    public function clear()
    {
        unset($_SESSION['cari'], $_SESSION['filter']);

        return redirect()->to('man_user');
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

        $data['paging']  = $this->user_model->paging($p, $o);
        $data['main']    = $this->user_model->list_data($o, $data['paging']->offset, $data['paging']->per_page);
        $data['keyword'] = $this->user_model->autocomplete();
        $header          = $this->header_model->get_data();
        $menu['act']     = 'man_user';

        echo view('header', $header);
        echo view('man_user/nav');
        echo view('man_user/manajemen_user_table', $data);
        echo view('footer');
    }

    public function form($p = 1, $o = 0, $id = '')
    {
        $data['p'] = $p;
        $data['o'] = $o;

        if ($id) {
            $data['user']        = $this->user_model->get_user($id);
            $data['form_action'] = site_url("man_user/update/{$p}/{$o}/{$id}");
        } else {
            $data['user']        = null;
            $data['form_action'] = site_url('man_user/insert');
        }

        $data['grup'] = $this->user_model->list_grup();
        $header       = $this->header_model->get_data();

        echo view('header', $header);
        echo view('man_user/nav');
        echo view('man_user/manajemen_user_form', $data);
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
        return redirect()->to('man_user');
    }

    public function filter()
    {
        $filter = $this->request->getPost('filter');
        if ($filter !== 0) {
            $_SESSION['filter'] = $filter;
        } else {
            unset($_SESSION['filter']);
        }
        return redirect()->to('man_user');
    }

    public function insert()
    {
        $this->user_model->insert();
        return redirect()->to('man_user');
    }

    public function update($p = 1, $o = 0, $id = '')
    {
        $this->user_model->update($id);
        return redirect()->to("man_user/index/{$p}/{$o}");
    }

    public function delete($p = 1, $o = 0, $id = '')
    {
        $this->user_model->delete($id);
        return redirect()->to("man_user/index/{$p}/{$o}");
    }

    public function delete_all($p = 1, $o = 0)
    {
        $this->user_model->delete_all();
        return redirect()->to("man_user/index/{$p}/{$o}");
    }

    public function user_lock($id = '')
    {
        $this->user_model->user_lock($id, 0);
        return redirect()->to("man_user/index/{$p}/{$o}");
    }

    public function user_unlock($id = '')
    {
        $this->user_model->user_lock($id, 1);
        return redirect()->to("man_user/index/{$p}/{$o}");
    }
}
