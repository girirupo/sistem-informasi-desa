<?php

namespace App\Controllers;

use Kenjis\CI3Compatible\Core\CI_Controller as BaseController;

class Kategori extends BaseController
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('user_model');
        $grup = $this->user_model->sesi_grup($_SESSION['sesi']);
        if ($grup !== '1' && $grup !== '2' && $grup !== '3') {
            return redirect()->to('siteman');
        }
        $this->load->model('header_model');
        $this->load->model('web_kategori_model');
        $this->load->model('KategoriModel', 'kategori_model');
    }

    public function clear()
    {
        session()->remove(['cari', 'filter']);

        return redirect()->to('kategori');
    }

    public function index($p = 1, $o = 0)
    {
        $data['p']   = $p;
        $data['o']   = $o;
        $data['tip'] = 2;

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
        if (isset($_POST['per_page'])) {
            $_SESSION['per_page'] = $_POST['per_page'];
        }
        $data['per_page'] = $_SESSION['per_page'];

        $data['paging']  = $this->web_kategori_model->paging($p, $o);
        $data['main']    = $this->web_kategori_model->list_data($o, $data['paging']->offset, $data['paging']->per_page);
        $data['keyword'] = $this->web_kategori_model->autocomplete();
        $header          = $this->header_model->get_data();
        $nav['act']      = 7;

        echo view('header', $header);
        echo view('web/nav', $nav);
        echo view('kategori/table', $data);
        echo view('footer');
    }

    public function form($id = '')
    {
        $data['tip'] = 2;
        if ($id) {
            $data['kategori']    = $this->kategori_model->get($id);
            $data['form_action'] = site_url("kategori/update/{$id}");
        } else {
            $data['kategori']    = null;
            $data['form_action'] = site_url('kategori/insert');
        }
        $header = $this->header_model->get_data();

        $nav['act'] = 7;
        echo view('header', $header);
        echo view('web/nav', $nav);
        echo view('kategori/form', $data);
        echo view('footer');
    }

    public function sub_kategori($kategori = 1)
    {
        $data['tip']         = 2;
        $data['subkategori'] = $this->web_kategori_model->list_sub_kategori($kategori);
        $data['kategori']    = $kategori;
        $header              = $this->header_model->get_data();
        $nav['act']          = 7;

        echo view('header', $header);
        echo view('web/nav', $nav);
        echo view('kategori/sub_kategori_table', $data);
        echo view('footer');
    }

    public function ajax_add_sub_kategori($kategori = '', $id = '')
    {
        $data['kategori'] = $kategori;

        $data['link'] = $this->web_kategori_model->list_link();

        if ($id) {
            $data['subkategori'] = $this->kategori_model->get($id);
            $data['form_action'] = site_url("kategori/update_sub_kategori/{$kategori}/{$id}");
        } else {
            $data['subkategori'] = null;
            $data['form_action'] = site_url("kategori/insert_sub_kategori/{$kategori}");
        }
        echo view('kategori/ajax_add_sub_kategori_form', $data);
    }

    public function search()
    {
        $cari = $this->input->post('cari');
        if ($cari !== '') {
            $_SESSION['cari'] = $cari;
        } else {
            session()->remove('cari');
        }

        return redirect()->to('kategori/index');
    }

    public function filter()
    {
        $filter = $this->input->post('filter');
        if ($filter !== 0) {
            $_SESSION['filter'] = $filter;
        } else {
            session()->remove('filter');
        }

        return redirect()->to('kategori');
    }

    public function insert()
    {
        $this->web_kategori_model->insert($tip);

        return redirect()->to('kategori/index');
    }

    public function update($id = '')
    {
        $this->web_kategori_model->update($id);

        return redirect()->to('kategori/index');
    }

    public function delete($id = '')
    {
        $this->kategori_model->delete($id);

        return redirect()->to('kategori/index');
    }

    public function delete_all($p = 1, $o = 0)
    {
        $this->web_kategori_model->delete_all();
        redirect("kategori/index/{$p}/{$o}");
    }

    public function kategori_lock($id = '')
    {
        $this->web_kategori_model->kategori_lock($id, 1);
        redirect("kategori/index/{$p}/{$o}");
    }

    public function kategori_unlock($id = '')
    {
        $this->web_kategori_model->kategori_lock($id, 2);
        redirect("kategori/index/{$p}/{$o}");
    }

    public function insert_sub_kategori($kategori = '')
    {
        $this->web_kategori_model->insert_sub_kategori($kategori);
        redirect("kategori/sub_kategori/{$kategori}");
    }

    public function update_sub_kategori($kategori = '', $id = '')
    {
        $this->web_kategori_model->update_sub_kategori($id);
        redirect("kategori/sub_kategori/{$kategori}");
    }

    public function delete_sub_kategori($kategori = '', $id = 0)
    {
        $this->kategori_model->delete($id);
        redirect("kategori/sub_kategori/{$kategori}");
    }

    public function delete_all_sub_kategori($kategori = '')
    {
        $this->web_kategori_model->delete_all();
        redirect("kategori/sub_kategori/{$kategori}");
    }

    public function kategori_lock_sub_kategori($kategori = '', $id = '')
    {
        $this->web_kategori_model->kategori_lock($id, 1);
        redirect("kategori/sub_kategori/{$kategori}");
    }

    public function kategori_unlock_sub_kategori($kategori = '', $id = '')
    {
        $this->web_kategori_model->kategori_lock($id, 2);
        redirect("kategori/sub_kategori/{$kategori}");
    }
}
