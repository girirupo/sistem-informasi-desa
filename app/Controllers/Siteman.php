<?php

namespace App\Controllers;

class Siteman extends BaseController
{
    public function index()
    {
        $this->user_model->logout();
        $header = $this->header_model->get_config();

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
        redirect('main');
    }

    public function login()
    {
        $this->user_model->logout();
        redirect('siteman');
    }
}
