<?php

namespace App\Controllers;

class Pengurus extends BaseController
{
    public function initController(
        \CodeIgniter\HTTP\RequestInterface $request,
        \CodeIgniter\HTTP\ResponseInterface $response,
        \Psr\Log\LoggerInterface $logger
    ) {
        parent::initController($request, $response, $logger);

        $this->load->model('user_model');
        $this->load->model('pamong_model');
        $grup = $this->user_model->sesi_grup($_SESSION['sesi']);
        if ($grup !== '1' && $grup !== '2') {
            return redirect()->to('siteman');
        }
        $this->load->model('header_model');
    }

    public function clear()
    {
        unset($_SESSION['cari'], $_SESSION['filter']);

        return redirect()->to('pengurus');
    }

    public function index()
    {
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
        $data['main']    = $this->pamong_model->list_data();
        $data['keyword'] = $this->pamong_model->autocomplete();
        $nav['act']      = 1;
        $header          = $this->header_model->get_data();

        echo view('header', $header);

        echo view('home/nav', $nav);
        echo view('home/pengurus', $data);
        echo view('footer');
    }

    public function form($id = '')
    {
        if ($id) {
            $data['pamong']      = $this->pamong_model->get_data($id);
            $data['form_action'] = site_url("pengurus/update/{$id}");
        } else {
            $data['pamong']      = null;
            $data['form_action'] = site_url('pengurus/insert');
        }

        $header = $this->header_model->get_data();

        echo view('header', $header);

        $nav['act'] = 1;
        echo view('home/nav', $nav);
        echo view('home/pengurus_form', $data);
        echo view('footer');
    }

    public function filter()
    {
        $filter = $this->request->getPost('filter');
        if ($filter !== '') {
            $_SESSION['filter'] = $filter;
        } else {
            unset($_SESSION['filter']);
        }
        return redirect()->to('pengurus');
    }

    public function search()
    {
        $cari = $this->request->getPost('cari');
        if ($cari !== '') {
            $_SESSION['cari'] = $cari;
        } else {
            unset($_SESSION['cari']);
        }
        return redirect()->to('pengurus');
    }

    public function insert()
    {
        $this->pamong_model->insert();
        return redirect()->to('pengurus');
    }

    public function update($id = '')
    {
        $this->pamong_model->update($id);
        return redirect()->to('pengurus');
    }

    public function delete($id = '')
    {
        $this->pamong_model->delete($id);
        return redirect()->to('pengurus');
    }

    public function delete_all()
    {
        $this->pamong_model->delete_all();
        return redirect()->to('pengurus');
    }
}
