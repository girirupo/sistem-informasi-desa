<?php

namespace App\Controllers;


class Hom_desa extends BaseController
{
    public function initController(
        \CodeIgniter\HTTP\RequestInterface $request,
        \CodeIgniter\HTTP\ResponseInterface $response,
        \Psr\Log\LoggerInterface $logger
    ) {
        parent::initController($request, $response, $logger);

        $this->load->model('user_model');
        $grup = $this->user_model->sesi_grup($_SESSION['sesi']);
        if ($grup !== '1' && $grup !== '2') {
            return redirect()->to('siteman');
        }
        $this->load->model('header_model');
        $this->load->model('config_model');
    }

    public function index()
    {
        $_SESSION['delik'] = 0;
        $nav['act']        = 0;
        $header            = $this->header_model->get_data();
        $data['main']      = $this->config_model->get_data();
        echo view('header', $header);
        echo view('home/nav', $nav);
        echo view('home/konfigurasi_form', $data);
        echo view('footer');
    }

    public function about()
    {
        $nav['act'] = 2;
        $header     = $this->header_model->get_data();
        echo view('header', $header);
        echo view('home/nav', $nav);
        echo view('home/desa');
        echo view('footer');
    }

    public function insert()
    {
        $this->config_model->insert();
        return redirect()->to('hom_desa');
    }

    public function update($id = '')
    {
        $this->config_model->update($id);
        return redirect()->to('hom_desa');
    }

    public function ajax_kantor_maps()
    {
        $data['desa']        = $this->config_model->get_data();
        $data['form_action'] = site_url('hom_desa/update_kantor_maps/');
        echo view('home/ajax_kantor_desa_maps', $data);
    }

    public function ajax_wilayah_maps()
    {
        $data['desa']        = $this->config_model->get_data();
        $data['form_action'] = site_url('hom_desa/update_wilayah_maps/');
        echo view('home/ajax_wilayah_desa_maps', $data);
    }

    public function update_kantor_maps()
    {
        $this->config_model->update_kantor();
        return redirect()->to('hom_desa');
    }

    public function update_wilayah_maps()
    {
        $this->config_model->update_wilayah();
        return redirect()->to('hom_desa');
    }

    public function kosong_pend()
    {
        $this->config_model->kosong_pend();
        return redirect()->to('hom_desa');
    }

    public function undelik()
    {
        if (isset($_SESSION['delik'])) {
            unset($_SESSION['delik']);
        }
        return redirect()->to('analisis_master/clear');
    }
}
