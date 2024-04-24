<?php

namespace App\Controllers;

class User_setting extends BaseController
{
    public function initController(
        \CodeIgniter\HTTP\RequestInterface $request,
        \CodeIgniter\HTTP\ResponseInterface $response,
        \Psr\Log\LoggerInterface $logger
    ) {
        parent::initController($request, $response, $logger);

        $this->load->model('user_model');
        $grup = $this->user_model->sesi_grup($_SESSION['sesi']);
        if ($grup !== (1 || 2 || 3 || 4 || 5)) {
            return redirect()->to('login');
        }
        $this->load->model('header_model');
    }

    public function index()
    {
        $id     = $_SESSION['user'];
        $header = $this->header_model->get_data();
        // echo view('header', $header);

        $header       = $this->header_model->get_data();
        $data['main'] = $this->user_model->get_user($id);

        echo view('setting', $data);
        // echo view('footer');
    }

    public function update($id = '')
    {
        $this->user_model->update_setting($id);
        return redirect()->to('main');
    }
}
