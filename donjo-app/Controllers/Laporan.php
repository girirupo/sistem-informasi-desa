<?php

namespace App\Controllers;

class Laporan extends BaseController
{
    public function initController(
        \CodeIgniter\HTTP\RequestInterface $request,
        \CodeIgniter\HTTP\ResponseInterface $response,
        \Psr\Log\LoggerInterface $logger
    ) {
        parent::initController($request, $response, $logger);

        $this->load->model('user_model');
        $this->load->model('laporan_bulanan_model');
        $grup = $this->user_model->sesi_grup($_SESSION['sesi']);
        if ($grup !== '1' && $grup !== '2' && $grup !== '3') {
            return redirect()->to('siteman');
        }
        $this->load->model('config_model');
        $this->load->model('header_model');

        $_SESSION['success'] = 0;
        $_SESSION['cari']    = '';

        $this->load->model('header_model');
    }

    public function clear()
    {
        unset($_SESSION['cari'], $_SESSION['filter'], $_SESSION['sex'], $_SESSION['dusun'], $_SESSION['rw'], $_SESSION['rt'], $_SESSION['agama'], $_SESSION['umur_min'], $_SESSION['umur_max'], $_SESSION['pekerjaan_id'], $_SESSION['status'], $_SESSION['pendidikan_id'], $_SESSION['status_penduduk']);

        $_SESSION['bulanku']  = date('n');
        $_SESSION['tahunku']  = date('Y');
        $_SESSION['per_page'] = 200;
        return redirect()->to('laporan');
    }

    public function index($lap = 0, $p = 1, $o = 0)
    {
        $data['p'] = $p;
        $data['o'] = $o;
        if (isset($_POST['per_page'])) {
            $_SESSION['per_page'] = $_POST['per_page'];
        }
        $data['per_page'] = $_SESSION['per_page'];

        if (isset($_SESSION['bulanku'])) {
            $data['bulanku'] = $_SESSION['bulanku'];
        } else {
            $data['bulanku'] = date('n');
        }

        if (isset($_SESSION['tahunku'])) {
            $data['tahunku'] = $_SESSION['tahunku'];
        } else {
            $data['tahunku'] = date('Y');
        }

        $data['bulan']          = $data['bulanku'];
        $data['tahun']          = $data['tahunku'];
        $data['config']         = $this->config_model->get_data(true);
        $data['penduduk_awal']  = $this->laporan_bulanan_model->penduduk_awal();
        $data['penduduk_akhir'] = $this->laporan_bulanan_model->penduduk_akhir();
        $data['kelahiran']      = $this->laporan_bulanan_model->kelahiran();
        $data['kematian']       = $this->laporan_bulanan_model->kematian();
        $data['pendatang']      = $this->laporan_bulanan_model->pendatang();
        $data['pindah']         = $this->laporan_bulanan_model->pindah();
        $data['hilang']         = $this->laporan_bulanan_model->hilang();
        $data['lap']            = $lap;
        $nav['act']             = 3;
        $header                 = $this->header_model->get_data();
        echo view('header', $header);
        echo view('statistik/nav', $nav);
        echo view('laporan/bulanan', $data);
        echo view('footer');
    }

    public function cetak($lap = 0)
    {
        $data['config']         = $this->config_model->get_data(true);
        $data['bulan']          = $_SESSION['bulanku'];
        $data['tahun']          = $_SESSION['tahunku'];
        $data['bln']            = getBulan($data['bulan']);
        $data['penduduk_awal']  = $this->laporan_bulanan_model->penduduk_awal();
        $data['penduduk_akhir'] = $this->laporan_bulanan_model->penduduk_akhir();
        $data['kelahiran']      = $this->laporan_bulanan_model->kelahiran();
        $data['kematian']       = $this->laporan_bulanan_model->kematian();
        $data['pendatang']      = $this->laporan_bulanan_model->pendatang();
        $data['pindah']         = $this->laporan_bulanan_model->pindah();
        $data['hilang']         = $this->laporan_bulanan_model->hilang();
        $data['lap']            = $lap;
        echo view('laporan/bulanan_print', $data);
    }

    public function excel($lap = 0)
    {
        $data['config']         = $this->config_model->get_data(true);
        $data['bulan']          = $_SESSION['bulanku'];
        $data['tahun']          = $_SESSION['tahunku'];
        $data['bln']            = getBulan($data['bulan']);
        $data['penduduk_awal']  = $this->laporan_bulanan_model->penduduk_awal();
        $data['penduduk_akhir'] = $this->laporan_bulanan_model->penduduk_akhir();
        $data['kelahiran']      = $this->laporan_bulanan_model->kelahiran();
        $data['kematian']       = $this->laporan_bulanan_model->kematian();
        $data['pendatang']      = $this->laporan_bulanan_model->pendatang();
        $data['pindah']         = $this->laporan_bulanan_model->pindah();
        $data['hilang']         = $this->laporan_bulanan_model->hilang();
        $data['lap']            = $lap;
        echo view('statistik/laporan/bulanan_excel', $data);
    }

    public function bulan()
    {
        $bulanku = $this->request->getPost('bulan');
        if ($bulanku !== '') {
            $_SESSION['bulanku'] = $bulanku;
        } else {
            unset($_SESSION['bulanku']);
        }

        $tahunku = $this->request->getPost('tahun');
        if ($tahunku !== '') {
            $_SESSION['tahunku'] = $tahunku;
        } else {
            unset($_SESSION['tahunku']);
        }
        return redirect()->to('laporan');
    }
}
