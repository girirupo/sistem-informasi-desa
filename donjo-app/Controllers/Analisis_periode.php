<?php

namespace App\Controllers;

class Analisis_periode extends BaseController
{
    public function initController(
        \CodeIgniter\HTTP\RequestInterface $request,
        \CodeIgniter\HTTP\ResponseInterface $response,
        \Psr\Log\LoggerInterface $logger
    ) {
        parent::initController($request, $response, $logger);

        $this->load->model('analisis_periode_model');
        $this->load->model('user_model');
        $this->load->model('header_model');
        $grup = $this->user_model->sesi_grup($_SESSION['sesi']);
        if ($grup !== '1') {
            return redirect()->to('siteman');
        }
        $_SESSION['submenu']  = 'Data Periode';
        $_SESSION['asubmenu'] = 'analisis_periode';
    }

    public function clear()
    {
        unset($_SESSION['cari'], $_SESSION['state']);

        return redirect()->to('analisis_periode');
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

        if (isset($_SESSION['state'])) {
            $data['state'] = $_SESSION['state'];
        } else {
            $data['state'] = '';
        }
        if (isset($_POST['per_page'])) {
            $_SESSION['per_page'] = $_POST['per_page'];
        }
        $data['per_page'] = $_SESSION['per_page'];

        $data['paging']          = $this->analisis_periode_model->paging($p, $o);
        $data['main']            = $this->analisis_periode_model->list_data($o, $data['paging']->offset, $data['paging']->per_page);
        $data['keyword']         = $this->analisis_periode_model->autocomplete();
        $data['analisis_master'] = $this->analisis_periode_model->get_analisis_master();
        $data['list_state']      = $this->analisis_periode_model->list_state();
        $header                  = $this->header_model->get_data();

        echo view('header', $header);
        echo view('analisis_master/nav');
        echo view('analisis_periode/table', $data);
        echo view('footer');
    }

    public function form($p = 1, $o = 0, $id = '')
    {
        $data['p'] = $p;
        $data['o'] = $o;

        if ($id) {
            $data['analisis_periode'] = $this->analisis_periode_model->get_analisis_periode($id);
            $data['form_action']      = site_url("analisis_periode/update/{$p}/{$o}/{$id}");
        } else {
            $data['analisis_periode'] = null;
            $data['form_action']      = site_url('analisis_periode/insert');
        }

        $header                  = $this->header_model->get_data();
        $data['analisis_master'] = $this->analisis_periode_model->get_analisis_master();

        echo view('header', $header);
        echo view('analisis_master/nav');
        echo view('analisis_periode/form', $data);
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
        return redirect()->to('analisis_periode');
    }

    public function state()
    {
        $filter = $this->request->getPost('state');
        if ($filter !== 0) {
            $_SESSION['state'] = $filter;
        } else {
            unset($_SESSION['state']);
        }
        return redirect()->to('analisis_periode');
    }

    public function insert()
    {
        $this->analisis_periode_model->insert();
        return redirect()->to('analisis_periode');
    }

    public function update($p = 1, $o = 0, $id = '')
    {
        $this->analisis_periode_model->update($id);
        return redirect()->to("analisis_periode/index/{$p}/{$o}");
    }

    public function delete($p = 1, $o = 0, $id = '')
    {
        $this->analisis_periode_model->delete($id);
        return redirect()->to("analisis_periode/index/{$p}/{$o}");
    }

    public function delete_all($p = 1, $o = 0)
    {
        $this->analisis_periode_model->delete_all();
        return redirect()->to("analisis_periode/index/{$p}/{$o}");
    }

    public function list_state()
    {
        $sql   = 'SELECT * FROM analisis_ref_state';
        $query = $this->db->query($sql);

        return $query->result_array();
    }
}
