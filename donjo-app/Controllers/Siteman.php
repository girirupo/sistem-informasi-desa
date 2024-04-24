<?php

namespace App\Controllers;

class Siteman extends BaseController
{
    protected $user_model;
    protected $config_model;

    public function initController(
        \CodeIgniter\HTTP\RequestInterface $request,
        \CodeIgniter\HTTP\ResponseInterface $response,
        \Psr\Log\LoggerInterface $logger
    ) {
        parent::initController($request, $response, $logger);

        $this->user_model = model('user_model');
        $this->config_model = model('config_model');
    }

    public function index()
    {
        $this->user_model->logout();
        $header = [
            'desa' => $this->config_model->get_data(),
        ];

        if (! isset($_SESSION['siteman'])) {
            $_SESSION['siteman'] = 0;
        }
        $_SESSION['success']    = 0;
        $_SESSION['per_page']   = 10;
        $_SESSION['cari']       = '';
        $_SESSION['pengumuman'] = 0;
        $_SESSION['sesi']       = 'kosong';
        $_SESSION['timeout']    = 0;

        echo view('siteman', $header);
        $_SESSION['siteman'] = 0;
    }

    public function auth()
    {
        $this->user_model->siteman();

        return redirect()->to('main');
    }

    public function login()
    {
        $this->user_model->logout();
        return redirect()->to('siteman');
    }
}
