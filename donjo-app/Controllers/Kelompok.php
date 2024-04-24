<?php

namespace App\Controllers;

class Kelompok extends BaseController
{
    public function initController(
        \CodeIgniter\HTTP\RequestInterface $request,
        \CodeIgniter\HTTP\ResponseInterface $response,
        \Psr\Log\LoggerInterface $logger
    ) {
        parent::initController($request, $response, $logger);

        $this->load->model('kelompok_model');
        $this->load->model('user_model');
        $this->load->model('header_model');
        $grup = $this->user_model->sesi_grup($_SESSION['sesi']);
        if ($grup !== '1') {
            return redirect()->to('siteman');
        }
    }

    public function clear()
    {
        unset($_SESSION['cari'], $_SESSION['filter'], $_SESSION['state']);

        return redirect()->to('kelompok');
    }

    public function index($p = 1, $o = 0)
    {
        unset($_SESSION['kelompok']);
        $data['p'] = $p;
        $data['o'] = $o;

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
        if (isset($_SESSION['state'])) {
            $data['state'] = $_SESSION['state'];
        } else {
            $data['state'] = '';
        }
        if (isset($_POST['per_page'])) {
            $_SESSION['per_page'] = $_POST['per_page'];
        }
        $data['per_page'] = $_SESSION['per_page'];

        $data['paging']      = $this->kelompok_model->paging($p, $o);
        $data['main']        = $this->kelompok_model->list_data($o, $data['paging']->offset, $data['paging']->per_page);
        $data['keyword']     = $this->kelompok_model->autocomplete();
        $data['list_master'] = $this->kelompok_model->list_master();
        $header              = $this->header_model->get_data();

        echo view('header', $header);
        $nav['act'] = 4;

        echo view('sid/nav', $nav);
        echo view('kelompok/table', $data);
        echo view('footer');
    }

    public function anggota($id = 0)
    {
        $data['kel']      = $id;
        $data['kelompok'] = $this->kelompok_model->get_kelompok($id);
        $data['main']     = $this->kelompok_model->list_anggota($id);
        $header           = $this->header_model->get_data();

        echo view('header', $header);
        $nav['act'] = 4;

        echo view('sid/nav', $nav);
        echo view('kelompok/anggota/table', $data);
        echo view('footer');
    }

    public function form($p = 1, $o = 0, $id = '')
    {
        $data['p'] = $p;
        $data['o'] = $o;

        if ($id) {
            $data['kelompok']    = $this->kelompok_model->get_kelompok($id);
            $data['form_action'] = site_url("kelompok/update/{$p}/{$o}/{$id}");
        } else {
            $data['kelompok']    = null;
            $data['form_action'] = site_url('kelompok/insert');
        }

        $data['list_master']   = $this->kelompok_model->list_master();
        $data['list_penduduk'] = $this->kelompok_model->list_penduduk();
        $header                = $this->header_model->get_data();

        echo view('header', $header);
        $nav['act'] = 4;

        echo view('sid/nav', $nav);
        echo view('kelompok/form', $data);
        echo view('footer');
    }

    public function form_anggota($id = 0, $id_a = 0)
    {
        if ($id_a === 0) {
            $data['kelompok']    = null;
            $data['pend']        = null;
            $data['form_action'] = site_url("kelompok/insert_a/{$id}");
        } else {
            $data['kelompok']    = $id;
            $data['pend']        = $this->kelompok_model->get_anggota($id, $id_a);
            $data['form_action'] = site_url("kelompok/update_a/{$id}/{$id_a}");
            // echo $id.$id_a;
        }
        $data['list_penduduk'] = $this->kelompok_model->list_penduduk();
        $header                = $this->header_model->get_data();

        echo view('header', $header);
        $nav['act'] = 4;

        echo view('sid/nav', $nav);
        echo view('kelompok/anggota/form', $data);
        echo view('footer');
    }

    public function panduan()
    {
        $header = $this->header_model->get_data();

        echo view('header', $header);
        echo view('kelompok/nav2');
        echo view('kelompok/panduan');
        echo view('footer');
    }

    public function cetak()
    {
        $data['header'] = $this->header_model->get_data();
        $data['main']   = $this->kelompok_model->list_data();

        echo view('kelompok/cetak', $data);
    }

    public function excel()
    {
        $data['header'] = $this->header_model->get_data();
        $data['main']   = $this->kelompok_model->list_data();

        echo view('kelompok/excel', $data);
    }

    public function cetak_a($id = 0)
    {
        $data['header']   = $this->header_model->get_data();
        $data['main']     = $this->kelompok_model->list_anggota($id);
        $data['kelompok'] = $this->kelompok_model->get_kelompok($id);

        echo view('kelompok/anggota/cetak', $data);
    }

    public function excel_a($id = 0)
    {
        $data['header']   = $this->header_model->get_data();
        $data['main']     = $this->kelompok_model->list_anggota($id);
        $data['kelompok'] = $this->kelompok_model->get_kelompok($id);

        echo view('kelompok/anggota/excel', $data);
    }

    public function menu($id = '')
    {
        $_SESSION['kelompok'] = $id;
        $data['kelompok']     = $this->kelompok_model->get_kelompok($id);
        $da                   = $data['kelompok'];
        $master               = $da['master_tipe'];

        switch ($master) {
            case 1: $data['menu_respon'] = 'kelompok_respon_penduduk';
                break;

            case 2: $data['menu_respon'] = 'kelompok_respon_keluarga';
                break;

            case 3: $data['menu_respon'] = 'kelompok_respon_rtm';
                break;

            case 4: $data['menu_respon'] = 'kelompok_respon_kelompok';
                break;

            default:return redirect()->to('kelompok');
        }

        $header = $this->header_model->get_data();

        echo view('header', $header);
        echo view('kelompok/nav');
        echo view('kelompok/menu', $data);
        echo view('footer');
    }

    public function search()
    {
        $cari = $this->request->getPost('cari');
        if ($cari !== '') {
            $_SESSION['cari'] = $cari;
        } else {
            unset($_SESSION['cari']);
        }
        return redirect()->to('kelompok');
    }

    public function filter()
    {
        $filter = $this->request->getPost('filter');
        if ($filter !== 0) {
            $_SESSION['filter'] = $filter;
        } else {
            unset($_SESSION['filter']);
        }
        return redirect()->to('kelompok');
    }

    public function state()
    {
        $filter = $this->request->getPost('state');
        if ($filter !== 0) {
            $_SESSION['state'] = $filter;
        } else {
            unset($_SESSION['state']);
        }
        return redirect()->to('kelompok');
    }

    public function insert()
    {
        $this->kelompok_model->insert();
        return redirect()->to('kelompok');
    }

    public function update($p = 1, $o = 0, $id = '')
    {
        $this->kelompok_model->update($id);
        return redirect()->to("kelompok/index/{$p}/{$o}");
    }

    public function update_a($id = '', $id_a = 0)
    {
        $this->kelompok_model->update_a($id, $id_a);
        return redirect()->to("kelompok/anggota/{$id}");
    }

    public function delete($p = 1, $o = 0, $id = '')
    {
        $this->kelompok_model->delete($id);
        return redirect()->to("kelompok/index/{$p}/{$o}");
    }

    public function delete_all($p = 1, $o = 0)
    {
        $this->kelompok_model->delete_all();
        return redirect()->to("kelompok/index/{$p}/{$o}");
    }

    public function insert_a($id = 0)
    {
        $this->kelompok_model->insert_a($id);
        return redirect()->to("kelompok/anggota/{$id}");
    }

    public function delete_a($id = '', $a = 0)
    {
        $this->kelompok_model->delete_a($a);
        return redirect()->to("kelompok/anggota/{$id}");
    }

    public function to_master($id = 0)
    {
        $filter = $id;
        if ($filter !== 0) {
            $_SESSION['filter'] = $filter;
        } else {
            unset($_SESSION['filter']);
        }
        return redirect()->to('kelompok');
    }
}
