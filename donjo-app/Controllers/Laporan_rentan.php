<?php

namespace App\Controllers;

use Kenjis\CI3Compatible\Core\CI_Controller as BaseController;

class Laporan_rentan extends BaseController
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('user_model');
        $this->load->model('laporan_bulanan_model');
        $grup = $this->user_model->sesi_grup($_SESSION['sesi']);
        if ($grup !== '1' && $grup !== '2' && $grup !== '3') {
            return redirect()->to('siteman');
        }
        $this->load->model('config_model');
        $this->load->model('header_model');

        $_SESSION['success']  = 0;
        $_SESSION['per_page'] = 20;
        $_SESSION['cari']     = '';

        $this->load->model('header_model');
    }

    public function clear()
    {
        session()->remove(['cari', 'filter', 'dusun', 'rw', 'rt']);

        return redirect()->to('laporan_rentan');
    }

    public function index()
    {
        if (isset($_SESSION['dusun'])) {
            $data['dusun'] = $_SESSION['dusun'];
        } else {
            $data['dusun'] = '';
        }

        $data['list_dusun'] = $this->laporan_bulanan_model->list_dusun();
        $data['config']     = $this->config_model->get_data(true);

        $data['main'] = $this->laporan_bulanan_model->list_data();

        $nav['act'] = 2;
        $header     = $this->header_model->get_data();
        echo view('header', $header);
        echo view('statistik/nav', $nav);
        echo view('laporan/kelompok', $data);
        echo view('footer');
    }

    public function cetak()
    {
        $data['config'] = $this->config_model->get_data(true);
        $data['main']   = $this->laporan_bulanan_model->list_data();
        echo view('laporan/kelompok_print', $data);
    }

    public function excel()
    {
        $data['config'] = $this->config_model->get_data(true);
        $data['main']   = $this->laporan_bulanan_model->list_data();
        echo view('laporan/kelompok_excel', $data);
    }

    public function dusun()
    {
        $dusun = $this->input->post('dusun');
        if ($dusun !== '') {
            $_SESSION['dusun'] = $dusun;
        } else {
            session()->remove('dusun');
        }

        return redirect()->to('laporan_rentan');
    }
}
