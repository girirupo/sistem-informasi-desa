<?php

namespace App\Controllers;

class Analisis_statistik_jawaban extends BaseController
{
    public function initController(
        \CodeIgniter\HTTP\RequestInterface $request,
        \CodeIgniter\HTTP\ResponseInterface $response,
        \Psr\Log\LoggerInterface $logger
    ) {
        parent::initController($request, $response, $logger);

        $this->load->model('analisis_statistik_jawaban_model');
        $this->load->model('user_model');
        $this->load->model('header_model');
        $grup = $this->user_model->sesi_grup($_SESSION['sesi']);
        if ($grup !== '1') {
            return redirect()->to('siteman');
        }
        $_SESSION['submenu']  = 'Statistik Jawaban';
        $_SESSION['asubmenu'] = 'analisis_statistik_jawaban';
    }

    public function clear()
    {
        unset($_SESSION['cari'], $_SESSION['filter'], $_SESSION['tipe'], $_SESSION['kategori'], $_SESSION['dusun'], $_SESSION['rw'], $_SESSION['rt']);

        return redirect()->to('analisis_statistik_jawaban');
    }

    public function leave()
    {
        $id = $_SESSION['analisis_master'];
        unset($_SESSION['analisis_master']);
        return redirect()->to("analisis_master/menu/{$id}");
    }

    public function index($p = 1, $o = 0)
    {
        unset($_SESSION['cari2']);
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
        if (isset($_SESSION['tipe'])) {
            $data['tipe'] = $_SESSION['tipe'];
        } else {
            $data['tipe'] = '';
        }
        if (isset($_SESSION['kategori'])) {
            $data['kategori'] = $_SESSION['kategori'];
        } else {
            $data['kategori'] = '';
        }
        if (isset($_POST['per_page'])) {
            $_SESSION['per_page'] = $_POST['per_page'];
        }
        $data['per_page'] = $_SESSION['per_page'];

        if (isset($_SESSION['dusun'])) {
            $data['dusun']   = $_SESSION['dusun'];
            $data['list_rw'] = $this->analisis_statistik_jawaban_model->list_rw($data['dusun']);

            if (isset($_SESSION['rw'])) {
                $data['rw']      = $_SESSION['rw'];
                $data['list_rt'] = $this->analisis_statistik_jawaban_model->list_rt($data['dusun'], $data['rw']);

                if (isset($_SESSION['rt'])) {
                    $data['rt'] = $_SESSION['rt'];
                } else {
                    $data['rt'] = '';
                }
            } else {
                $data['rw'] = '';
            }
        } else {
            $data['dusun'] = '';
            $data['rw']    = '';
            $data['rt']    = '';
        }

        $data['paging']          = $this->analisis_statistik_jawaban_model->paging($p, $o);
        $data['main']            = $this->analisis_statistik_jawaban_model->list_data($o, $data['paging']->offset, $data['paging']->per_page);
        $data['keyword']         = $this->analisis_statistik_jawaban_model->autocomplete();
        $data['analisis_master'] = $this->analisis_statistik_jawaban_model->get_analisis_master();
        $data['list_tipe']       = $this->analisis_statistik_jawaban_model->list_tipe();
        $data['list_kategori']   = $this->analisis_statistik_jawaban_model->list_kategori();
        $data['list_dusun']      = $this->analisis_statistik_jawaban_model->list_dusun();
        $header                  = $this->header_model->get_data();

        echo view('header', $header);
        echo view('analisis_master/nav');
        echo view('analisis_statistik_jawaban/table', $data);
        echo view('footer');
    }

    public function form($p = 1, $o = 0, $id = '')
    {
        $data['p'] = $p;
        $data['o'] = $o;

        if ($id) {
            $data['analisis_statistik_jawaban'] = $this->analisis_statistik_jawaban_model->get_analisis_indikator($id);
            $data['form_action']                = site_url("analisis_statistik_jawaban/update/{$p}/{$o}/{$id}");
        } else {
            $data['analisis_statistik_jawaban'] = null;
            $data['form_action']                = site_url('analisis_statistik_jawaban/insert');
        }

        $data['list_kategori']   = $this->analisis_statistik_jawaban_model->list_kategori();
        $header                  = $this->header_model->get_data();
        $data['analisis_master'] = $this->analisis_statistik_jawaban_model->get_analisis_master();

        echo view('header', $header);
        echo view('analisis_master/nav');
        echo view('analisis_statistik_jawaban/form', $data);
        echo view('footer');
    }

    public function parameter($id = '')
    {
        $ai = $this->analisis_statistik_jawaban_model->get_analisis_indikator($id);
        if ($ai['id_tipe'] === 3 || $ai['id_tipe'] === 4) {
            return redirect()->to('analisis_statistik_jawaban');
        }

        $data['analisis_statistik_jawaban'] = $this->analisis_statistik_jawaban_model->get_analisis_indikator($id);
        $data['analisis_master']            = $this->analisis_statistik_jawaban_model->get_analisis_master();
        $data['main']                       = $this->analisis_statistik_jawaban_model->list_indikator($id);

        $header = $this->header_model->get_data();

        echo view('header', $header);
        echo view('analisis_master/nav');
        echo view('analisis_statistik_jawaban/parameter/table', $data);
        echo view('footer');
    }

    public function grafik_parameter($id = '')
    {
        if (isset($_SESSION['dusun'])) {
            $data['dusun']   = $_SESSION['dusun'];
            $data['list_rw'] = $this->analisis_statistik_jawaban_model->list_rw($data['dusun']);

            if (isset($_SESSION['rw'])) {
                $data['rw']      = $_SESSION['rw'];
                $data['list_rt'] = $this->analisis_statistik_jawaban_model->list_rt($data['dusun'], $data['rw']);

                if (isset($_SESSION['rt'])) {
                    $data['rt'] = $_SESSION['rt'];
                } else {
                    $data['rt'] = '';
                }
            } else {
                $data['rw'] = '';
            }
        } else {
            $data['dusun'] = '';
            $data['rw']    = '';
            $data['rt']    = '';
        }
        $data['list_dusun'] = $this->analisis_statistik_jawaban_model->list_dusun();

        $ai = $this->analisis_statistik_jawaban_model->get_analisis_indikator($id);

        // return redirect()->to('analisis_statistik_jawaban');

        $data['analisis_statistik_jawaban'] = $this->analisis_statistik_jawaban_model->get_analisis_indikator($id);
        $data['analisis_master']            = $this->analisis_statistik_jawaban_model->get_analisis_master();
        $data['main']                       = $this->analisis_statistik_jawaban_model->list_indikator($id);

        $header = $this->header_model->get_data();

        echo view('header', $header);
        echo view('analisis_master/nav');
        echo view('analisis_statistik_jawaban/parameter/grafik_table', $data);
        echo view('footer');
    }

    public function subjek_parameter($id = '', $par = '')
    {
        if (isset($_SESSION['dusun'])) {
            $data['dusun']   = $_SESSION['dusun'];
            $data['list_rw'] = $this->analisis_statistik_jawaban_model->list_rw($data['dusun']);

            if (isset($_SESSION['rw'])) {
                $data['rw']      = $_SESSION['rw'];
                $data['list_rt'] = $this->analisis_statistik_jawaban_model->list_rt($data['dusun'], $data['rw']);

                if (isset($_SESSION['rt'])) {
                    $data['rt'] = $_SESSION['rt'];
                } else {
                    $data['rt'] = '';
                }
            } else {
                $data['rw'] = '';
            }
        } else {
            $data['dusun'] = '';
            $data['rw']    = '';
            $data['rt']    = '';
        }
        $data['list_dusun'] = $this->analisis_statistik_jawaban_model->list_dusun();

        $ai = $this->analisis_statistik_jawaban_model->get_analisis_indikator($id);
        // if($ai['id_tipe']==3 OR $ai['id_tipe']==4)
        //	return redirect()->to('analisis_statistik_jawaban');

        $data['analisis_statistik_pertanyaan'] = $this->analisis_statistik_jawaban_model->get_analisis_indikator($id);
        $data['analisis_statistik_jawaban']    = $this->analisis_statistik_jawaban_model->get_analisis_parameter($par);
        $data['analisis_master']               = $this->analisis_statistik_jawaban_model->get_analisis_master();
        $data['main']                          = $this->analisis_statistik_jawaban_model->list_subjek($par);

        $header = $this->header_model->get_data();

        echo view('header', $header);
        echo view('analisis_master/nav');
        echo view('analisis_statistik_jawaban/parameter/subjek_table', $data);
        echo view('footer');
    }

    public function cetak($o = 0)
    {
        $data['main'] = $this->analisis_statistik_jawaban_model->list_data($o, 0, 10000);
        echo view('analisis_statistik_jawaban/table_print', $data);
    }

    public function excel($o = 0)
    {
        $data['main'] = $this->analisis_statistik_jawaban_model->list_data($o, 0, 10000);
        echo view('analisis_statistik_jawaban/table_excel', $data);
    }

    public function cetak2($id = '', $par = '')
    {
        $data['analisis_statistik_pertanyaan'] = $this->analisis_statistik_jawaban_model->get_analisis_indikator($id);
        $data['analisis_statistik_jawaban']    = $this->analisis_statistik_jawaban_model->get_analisis_parameter($par);
        $data['main']                          = $this->analisis_statistik_jawaban_model->list_subjek($par);
        echo view('analisis_statistik_jawaban/parameter/table_print', $data);
    }

    public function excel2($id = '', $par = '')
    {
        $data['analisis_statistik_pertanyaan'] = $this->analisis_statistik_jawaban_model->get_analisis_indikator($id);
        $data['analisis_statistik_jawaban']    = $this->analisis_statistik_jawaban_model->get_analisis_parameter($par);
        $data['main']                          = $this->analisis_statistik_jawaban_model->list_subjek($par);
        echo view('analisis_statistik_jawaban/parameter/subjek_excel', $data);
    }

    public function search()
    {
        $cari = $this->request->getPost('cari');
        if ($cari !== '') {
            $_SESSION['cari'] = $cari;
        } else {
            unset($_SESSION['cari']);
        }
        return redirect()->to('analisis_statistik_jawaban');
    }

    public function filter()
    {
        $filter = $this->request->getPost('filter');
        if ($filter !== 0) {
            $_SESSION['filter'] = $filter;
        } else {
            unset($_SESSION['filter']);
        }
        return redirect()->to('analisis_statistik_jawaban');
    }

    public function tipe()
    {
        $filter = $this->request->getPost('tipe');
        if ($filter !== 0) {
            $_SESSION['tipe'] = $filter;
        } else {
            unset($_SESSION['tipe']);
        }
        return redirect()->to('analisis_statistik_jawaban');
    }

    public function kategori()
    {
        $filter = $this->request->getPost('kategori');
        if ($filter !== 0) {
            $_SESSION['kategori'] = $filter;
        } else {
            unset($_SESSION['kategori']);
        }
        return redirect()->to('analisis_statistik_jawaban');
    }

    public function dusun()
    {
        unset($_SESSION['rw'], $_SESSION['rt']);

        $dusun = $this->request->getPost('dusun');
        if ($dusun !== '') {
            $_SESSION['dusun'] = $dusun;
        } else {
            unset($_SESSION['dusun']);
        }
        return redirect()->to('analisis_statistik_jawaban');
    }

    public function rw()
    {
        unset($_SESSION['rt']);
        $rw = $this->request->getPost('rw');
        if ($rw !== '') {
            $_SESSION['rw'] = $rw;
        } else {
            unset($_SESSION['rw']);
        }
        return redirect()->to('analisis_statistik_jawaban');
    }

    public function rt()
    {
        $rt = $this->request->getPost('rt');
        if ($rt !== '') {
            $_SESSION['rt'] = $rt;
        } else {
            unset($_SESSION['rt']);
        }
        return redirect()->to('analisis_statistik_jawaban');
    }

    public function dusun2($id = '', $par = '')
    {
        unset($_SESSION['rw'], $_SESSION['rt']);

        $dusun = $this->request->getPost('dusun');
        if ($dusun !== '') {
            $_SESSION['dusun'] = $dusun;
        } else {
            unset($_SESSION['dusun']);
        }
        return redirect()->to("analisis_statistik_jawaban/subjek_parameter/{$id}/{$par}");
    }

    public function rw2($id = '', $par = '')
    {
        unset($_SESSION['rt']);
        $rw = $this->request->getPost('rw');
        if ($rw !== '') {
            $_SESSION['rw'] = $rw;
        } else {
            unset($_SESSION['rw']);
        }
        return redirect()->to("analisis_statistik_jawaban/subjek_parameter/{$id}/{$par}");
    }

    public function rt2($id = '', $par = '')
    {
        $rt = $this->request->getPost('rt');
        if ($rt !== '') {
            $_SESSION['rt'] = $rt;
        } else {
            unset($_SESSION['rt']);
        }
        return redirect()->to("analisis_statistik_jawaban/subjek_parameter/{$id}/{$par}");
    }

    public function dusun3($id = '')
    {
        unset($_SESSION['rw'], $_SESSION['rt']);

        $dusun = $this->request->getPost('dusun');
        if ($dusun !== '') {
            $_SESSION['dusun'] = $dusun;
        } else {
            unset($_SESSION['dusun']);
        }
        return redirect()->to("analisis_statistik_jawaban/grafik_parameter/{$id}");
    }

    public function rw3($id = '')
    {
        unset($_SESSION['rt']);
        $rw = $this->request->getPost('rw');
        if ($rw !== '') {
            $_SESSION['rw'] = $rw;
        } else {
            unset($_SESSION['rw']);
        }
        return redirect()->to("analisis_statistik_jawaban/grafik_parameter/{$id}");
    }

    public function rt3($id = '')
    {
        $rt = $this->request->getPost('rt');
        if ($rt !== '') {
            $_SESSION['rt'] = $rt;
        } else {
            unset($_SESSION['rt']);
        }
        return redirect()->to("analisis_statistik_jawaban/grafik_parameter/{$id}");
    }

    public function insert()
    {
        $this->analisis_statistik_jawaban_model->insert();
        return redirect()->to('analisis_statistik_jawaban');
    }

    public function update($p = 1, $o = 0, $id = '')
    {
        $this->analisis_statistik_jawaban_model->update($id);
        return redirect()->to("analisis_statistik_jawaban/index/{$p}/{$o}");
    }

    public function delete($p = 1, $o = 0, $id = '')
    {
        $this->analisis_statistik_jawaban_model->delete($id);
        return redirect()->to("analisis_statistik_jawaban/index/{$p}/{$o}");
    }

    public function delete_all($p = 1, $o = 0)
    {
        $this->analisis_statistik_jawaban_model->delete_all();
        return redirect()->to("analisis_statistik_jawaban/index/{$p}/{$o}");
    }

    public function p_insert($in = '')
    {
        $this->analisis_statistik_jawaban_model->p_insert($in);
        return redirect()->to("analisis_statistik_jawaban/parameter/{$in}");
    }

    public function p_update($in = '', $id = '')
    {
        $this->analisis_statistik_jawaban_model->p_update($id);
        return redirect()->to("analisis_statistik_jawaban/parameter/{$in}");
    }

    public function p_delete($in = '', $id = '')
    {
        $this->analisis_statistik_jawaban_model->p_delete($id);
        return redirect()->to("analisis_statistik_jawaban/parameter/{$in}");
    }

    public function p_delete_all()
    {
        $this->analisis_statistik_jawaban_model->p_delete_all();
        return redirect()->to("analisis_statistik_jawaban/parameter/{$in}");
    }
}
