<?php

namespace App\Controllers;

class Widget extends BaseController
{
    public function initController(
        \CodeIgniter\HTTP\RequestInterface $request,
        \CodeIgniter\HTTP\ResponseInterface $response,
        \Psr\Log\LoggerInterface $logger
    ) {
        parent::initController($request, $response, $logger);

        $this->load->model('user_model');
        $grup = $this->user_model->sesi_grup($_SESSION['sesi']);
        if ($grup !== '1' && $grup !== '2' && $grup !== '3') {
            return redirect()->to('siteman');
        }
        $this->load->model('header_model');
        $this->load->model('web_widget_model');
    }

    public function index()
    {
        $data['main']        = $this->web_widget_model->get_widget();
        $id                  = $data['main']['id'];
        $data['form_action'] = site_url("web/widget/update/1/{$id}");
        $header              = $this->header_model->get_data();
        $nav['act']          = 5;

        echo view('header', $header);
        echo view('web/nav', $nav);
        echo view('web/widget/facebook', $data);
        echo view('footer');
    }

    public function twitter()
    {
        $data['main']        = $this->web_widget_model->get_widget();
        $id                  = $data['main']['id'];
        $data['form_action'] = site_url("web/widget/update/2/{$id}");
        $header              = $this->header_model->get_data();
        $nav['act']          = 5;

        echo view('header', $header);
        echo view('web/nav', $nav);
        echo view('web/widget/twitter', $data);
        echo view('footer');
    }

    public function update($tipe = '', $id = '')
    {
        $this->web_widget_model->update($id);
        if ($tipe === '1') {
            return redirect()->to('web/widget');
        } else {
            return redirect()->to('web/widget/twitter');
        }
    }
}
