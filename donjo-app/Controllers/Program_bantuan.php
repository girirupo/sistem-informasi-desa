<?php

namespace App\Controllers;

class Program_bantuan extends BaseController
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
        $this->load->model('program_bantuan_model');
    }

    public function index()
    {
        $header = $this->header_model->get_data();
        echo view('header', $header);
        $data['tampil']  = 0;
        $data['program'] = $this->program_bantuan_model->get_program(false);
        echo view('program_bantuan/program', $data);
        echo view('footer');
    }

    public function sasaran($sasaran = 0)
    {
        $header = $this->header_model->get_data();
        echo view('header', $header);

        $data['tampil']  = $sasaran;
        $data['program'] = $this->program_bantuan_model->list_program($sasaran);
        echo view('program_bantuan/program', $data);
        echo view('footer');
    }

    public function detail($id)
    {
        $header = $this->header_model->get_data();
        echo view('header', $header);
        if (isset($_POST['nik'])) {
            $data['individu'] = $this->program_bantuan_model->add_peserta($_POST['nik'], $id);
        } else {
            $data['individu'] = null;
        }
        $data['program'] = $this->program_bantuan_model->get_program($id);

        echo view('program_bantuan/detail', $data);
        echo view('footer');
    }

    public function peserta($cat = 0, $id = 0)
    {
        $header = $this->header_model->get_data();
        echo view('header', $header);
        $data['program'] = $this->program_bantuan_model->get_peserta_program($cat, $id);

        echo view('program_bantuan/peserta', $data);
        echo view('footer');
    }

    public function create()
    {
        $this->load->helper('form');
        $this->load->library('form_validation');

        $this->form_validation->set_rules('cid', 'Sasaran', 'required');
        $this->form_validation->set_rules('nama', 'Nama Program', 'required');
        $this->form_validation->set_rules('sdate', 'Tanggal awal', 'required');
        $this->form_validation->set_rules('edate', 'Tanggal akhir', 'required');
        $header = $this->header_model->get_data();
        echo view('header', $header);
        if ($this->form_validation->run() === false) {
            echo view('program_bantuan/create');
        } else {
            $this->program_bantuan_model->set_program();
            return redirect()->to('program_bantuan/');
        }
        echo view('footer');
    }

    public function edit($id)
    {
        $this->load->helper('form');
        $this->load->library('form_validation');

        $this->form_validation->set_rules('cid', 'Sasaran', 'required');
        $this->form_validation->set_rules('nama', 'Nama Program', 'required');
        $this->form_validation->set_rules('sdate', 'Tanggal awal', 'required');
        $this->form_validation->set_rules('edate', 'Tanggal akhir', 'required');
        $header = $this->header_model->get_data();
        echo view('header', $header);
        $data['program'] = $this->program_bantuan_model->get_program($id);
        if ($this->form_validation->run() === false) {
            echo view('program_bantuan/edit', $data);
        } else {
            $this->program_bantuan_model->update_program($id);
            return redirect()->to('program_bantuan/');
        }

        echo view('footer');
    }

    public function update($id)
    {
        $this->program_bantuan_model->update_program($id);
        return redirect()->to('program_bantuan/detail/' . $id);
    }

    public function hapus($id)
    {
        $this->program_bantuan_model->hapus_program($id);
        // echo view('program_bantuan/formsuccess');
        return redirect()->to('program_bantuan/');
    }

    public function unduhsheet($id = 0)
    {
        if ($id > 0) {
            $data['desa']    = $this->header_model->get_data();
            $data['peserta'] = $this->program_bantuan_model->get_program($id);
            echo view('program_bantuan/unduh-sheet', $data);
        }
    }
}
