<?php

use App\Libraries\Install as InstallLib;

class Install extends InstallController
{
    public function initController(
        \CodeIgniter\HTTP\RequestInterface $request,
        \CodeIgniter\HTTP\ResponseInterface $response,
        \Psr\Log\LoggerInterface $logger
    ) {
        parent::initController($request, $response, $logger);

        $this->load->model('header_model');
        $this->load->model('user_model');
        $this->load->model('config_model');
    }

    /**
     * View halaman installasi.
     *
     * @return string
     */
    public function index()
    {
        return echo view('install/index');
    }

    /**
     * Proses installasi database
     *
     * @todo Perbaiki proses installasi
     *
     * @return string|void
     */
    public function run()
    {
        $install = new InstallLib();
        $out     = $install->run();

        if (null === $out) {
            return redirect()->to('/');
        }

        return echo view('install/done', $out);
    }
}
