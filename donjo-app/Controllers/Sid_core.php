<?php

namespace App\Controllers;

class Sid_core extends BaseController
{
    public function initController(
        \CodeIgniter\HTTP\RequestInterface $request,
        \CodeIgniter\HTTP\ResponseInterface $response,
        \Psr\Log\LoggerInterface $logger
    ) {
        parent::initController($request, $response, $logger);

        $_SESSION['filter'] = 77;

        unset($_SESSION['log']);
        $_SESSION['status_dasar'] = 1;
        unset($_SESSION['cari'], $_SESSION['duplikat'], $_SESSION['sex'], $_SESSION['warganegara'], $_SESSION['cacat'], $_SESSION['menahun'], $_SESSION['cacatx'], $_SESSION['menahunx'], $_SESSION['golongan_darah'], $_SESSION['dusun'], $_SESSION['rw'], $_SESSION['rt'], $_SESSION['hubungan'], $_SESSION['agama'], $_SESSION['umur_min'], $_SESSION['umur_max'], $_SESSION['pekerjaan_id'], $_SESSION['status'], $_SESSION['pendidikan_id'], $_SESSION['pendidikan_sedang_id'], $_SESSION['pendidikan_kk_id'], $_SESSION['umurx'], $_SESSION['status_penduduk'], $_SESSION['judul_statistik'], $_SESSION['hamil']);

        $this->load->model('user_model');
        $this->load->model('wilayah_model');
        $this->load->model('config_model');
        $grup = $this->user_model->sesi_grup($_SESSION['sesi']);
        if ($grup !== '1' && $grup !== '2') {
            return redirect()->to('siteman');
        }
        $this->load->model('header_model');
    }

    public function clear()
    {
        unset($_SESSION['cari'], $_SESSION['filter']);

        return redirect()->to('sid_core');
    }

    public function index($p = 1, $o = 0)
    {
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
        if (isset($_POST['per_page'])) {
            $_SESSION['per_page'] = $_POST['per_page'];
        }
        $data['per_page'] = $_SESSION['per_page'];
        $data['grup']     = $this->user_model->sesi_grup($_SESSION['sesi']);
        $data['paging']   = $this->wilayah_model->paging($p, $o);
        $data['main']     = $this->wilayah_model->list_data($o, $data['paging']->offset, $data['paging']->per_page);
        $data['keyword']  = $this->wilayah_model->autocomplete();
        $data['total']    = $this->wilayah_model->total();
        $nav['act']       = 0;
        $header           = $this->header_model->get_data();
        echo view('header', $header);
        echo view('sid/nav', $nav);
        echo view('sid/wilayah/wilayah', $data);
        echo view('footer');
    }

    public function cetak()
    {
        $data['desa']  = $this->header_model->get_data();
        $data['main']  = $this->wilayah_model->list_data(0, 0, 1000);
        $data['total'] = $this->wilayah_model->total();
        echo view('sid/wilayah/wilayah_print', $data);
    }

    public function excel()
    {
        $data['desa']  = $this->header_model->get_data();
        $data['main']  = $this->wilayah_model->list_data(0, 0, 1000);
        $data['total'] = $this->wilayah_model->total();
        echo view('sid/wilayah/wilayah_excel', $data);
    }

    public function form($id = '')
    {
        $data['penduduk'] = $this->wilayah_model->list_penduduk();

        if ($id) {
            $temp             = $this->wilayah_model->cluster_by_id($id);
            $data['dusun']    = $temp['dusun'];
            $data['individu'] = $this->wilayah_model->get_penduduk($temp['id_kepala']);
            if (empty($data['individu'])) {
                $data['individu'] = null;
            } else {
                $ex               = $data['individu'];
                $data['penduduk'] = $this->wilayah_model->list_penduduk_ex($ex['id']);
            }
            $data['form_action'] = site_url("sid_core/update/{$id}");
        } else {
            $data['dusun']       = null;
            $data['form_action'] = site_url('sid_core/insert');
        }

        $nav['act'] = 0;
        $header     = $this->header_model->get_data();
        echo view('header', $header);
        echo view('sid/nav', $nav);
        echo view('sid/wilayah/wilayah_form', $data);
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
        return redirect()->to('sid_core');
    }

    public function insert($dusun = '')
    {
        $this->wilayah_model->insert();
        return redirect()->to('sid_core');
    }

    public function update($id = '')
    {
        $this->wilayah_model->update($id);
        return redirect()->to('sid_core');
    }

    public function delete($id = '')
    {
        $this->wilayah_model->delete($id);
        return redirect()->to('sid_core');
    }

    public function delete_all()
    {
        $this->wilayah_model->delete_all();
        return redirect()->to('sid_core');
    }

    public function sub_rw($id_dusun = '')
    {
        $dusun            = $this->wilayah_model->cluster_by_id($id_dusun);
        $nama_dusun       = $dusun['dusun'];
        $data['dusun']    = $dusun['dusun'];
        $data['id_dusun'] = $id_dusun;
        $data['main']     = $this->wilayah_model->list_data_rw($id_dusun);
        $data['total']    = $this->wilayah_model->total_rw($nama_dusun);
        $nav['act']       = 0;
        $header           = $this->header_model->get_data();
        echo view('header', $header);
        echo view('sid/nav', $nav);
        echo view('sid/wilayah/wilayah_rw', $data);
        echo view('footer');
    }

    public function cetak_rw($id_dusun = '')
    {
        $dusun            = $this->wilayah_model->cluster_by_id($id_dusun);
        $nama_dusun       = $dusun['dusun'];
        $data['dusun']    = $dusun['dusun'];
        $data['id_dusun'] = $id_dusun;
        $data['main']     = $this->wilayah_model->list_data_rw($id_dusun);
        $data['total']    = $this->wilayah_model->total_rw($nama_dusun);
        echo view('sid/wilayah/wilayah_rw_print', $data);
    }

    public function excel_rw($id_dusun = '')
    {
        $dusun            = $this->wilayah_model->cluster_by_id($id_dusun);
        $nama_dusun       = $dusun['dusun'];
        $data['dusun']    = $dusun['dusun'];
        $data['id_dusun'] = $id_dusun;
        $data['main']     = $this->wilayah_model->list_data_rw($id_dusun);
        $data['total']    = $this->wilayah_model->total_rw($nama_dusun);
        echo view('sid/wilayah/wilayah_rw_excel', $data);
    }

    public function form_rw($id_dusun = '', $rw = '')
    {
        $temp             = $this->wilayah_model->cluster_by_id($id_dusun);
        $dusun            = $temp['dusun'];
        $data['dusun']    = $temp['dusun'];
        $data['id_dusun'] = $id_dusun;

        $data['penduduk'] = $this->wilayah_model->list_penduduk();

        if ($rw) {
            $data['rw']       = $rw;
            $temp             = $this->wilayah_model->get_rw($dusun, $rw);
            $data['individu'] = $this->wilayah_model->get_penduduk($temp['id_kepala']);
            if (empty($data['individu'])) {
                $data['individu'] = null;
            } else {
                $ex               = $data['individu'];
                $data['penduduk'] = $this->wilayah_model->list_penduduk_ex($ex['id']);
            }
            $data['form_action'] = site_url("sid_core/update_rw/{$id_dusun}/{$rw}");
        } else {
            $data['rw']          = null;
            $data['form_action'] = site_url("sid_core/insert_rw/{$id_dusun}");
        }

        $nav['act'] = 0;
        $header     = $this->header_model->get_data();
        echo view('header', $header);
        echo view('sid/nav', $nav);
        echo view('sid/wilayah/wilayah_form_rw', $data);
        echo view('footer');
    }

    public function insert_rw($dusun = '')
    {
        $this->wilayah_model->insert_rw($dusun);
        return redirect()->to("sid_core/sub_rw/{$dusun}");
    }

    public function update_rw($dusun = '', $rw = '')
    {
        $this->wilayah_model->update_rw($dusun, $rw);
        return redirect()->to("sid_core/sub_rw/{$dusun}");
    }

    public function delete_rw($id_dusun = '', $id = '')
    {
        $this->wilayah_model->delete_rw($id);
        return redirect()->to("sid_core/sub_rw/{$id_dusun}");
    }

    public function delete_all_rw($dusun = '')
    {
        $this->wilayah_model->delete_all_rw();
        return redirect()->to("sid_core/sub_rw/{$dusun}");
    }

    public function sub_rt($id_dusun = '', $rw = '')
    {
        $temp             = $this->wilayah_model->cluster_by_id($id_dusun);
        $dusun            = $temp['dusun'];
        $data['dusun']    = $temp['dusun'];
        $data['id_dusun'] = $id_dusun;

        $data['rw']    = $rw;
        $data['main']  = $this->wilayah_model->list_data_rt($dusun, $rw);
        $data['total'] = $this->wilayah_model->total_rt($dusun, $rw);
        $nav['act']    = 0;
        $header        = $this->header_model->get_data();
        echo view('header', $header);
        echo view('sid/nav', $nav);
        echo view('sid/wilayah/wilayah_rt', $data);
        echo view('footer');
    }

    public function cetak_rt($id_dusun = '', $rw = '')
    {
        $temp             = $this->wilayah_model->cluster_by_id($id_dusun);
        $dusun            = $temp['dusun'];
        $data['dusun']    = $temp['dusun'];
        $data['id_dusun'] = $id_dusun;

        $data['rw']    = $rw;
        $data['main']  = $this->wilayah_model->list_data_rt($dusun, $rw);
        $data['total'] = $this->wilayah_model->total_rt($dusun, $rw);
        echo view('sid/wilayah/wilayah_rt_print', $data);
    }

    public function excel_rt($id_dusun = '', $rw = '')
    {
        $temp             = $this->wilayah_model->cluster_by_id($id_dusun);
        $dusun            = $temp['dusun'];
        $data['dusun']    = $temp['dusun'];
        $data['id_dusun'] = $id_dusun;

        $data['rw']    = $rw;
        $data['main']  = $this->wilayah_model->list_data_rt($dusun, $rw);
        $data['total'] = $this->wilayah_model->total_rt($dusun, $rw);
        echo view('sid/wilayah/wilayah_rt_excel', $data);
    }

    public function list_dusun_rt($dusun = '', $rw = '')
    {
        $data['dusun'] = $dusun;
        $data['rw']    = $rw;
        $data['main']  = $this->wilayah_model->list_data_rt($dusun, $rw);
        $nav['act']    = 0;
        $header        = $this->header_model->get_data();
        echo view('header', $header);
        echo view('sid/nav', $nav);
        echo view('sid/wilayah/list_dusun_rt', $data);
        echo view('footer');
    }

    public function form_rt($id_dusun = '', $rw = '', $rt = '')
    {
        $temp             = $this->wilayah_model->cluster_by_id($id_dusun);
        $dusun            = $temp['dusun'];
        $data['dusun']    = $temp['dusun'];
        $data['id_dusun'] = $id_dusun;

        $data['rw']       = $rw;
        $data['penduduk'] = $this->wilayah_model->list_penduduk();

        if ($rt) {
            $temp2            = $this->wilayah_model->cluster_by_id($rt);
            $id_cluster       = $temp2['id'];
            $data['rt']       = $temp2['rt'];
            $data['individu'] = $this->wilayah_model->get_penduduk($temp2['id_kepala']);
            if (empty($data['individu'])) {
                $data['individu'] = null;
            } else {
                $ex               = $data['individu'];
                $data['penduduk'] = $this->wilayah_model->list_penduduk_ex($ex['id']);
            }
            $data['form_action'] = site_url("sid_core/update_rt/{$id_dusun}/{$rw}/{$id_cluster}");
        } else {
            $data['rt']          = null;
            $data['form_action'] = site_url("sid_core/insert_rt/{$id_dusun}/{$rw}");
        }

        $nav['act'] = 0;
        $header     = $this->header_model->get_data();
        echo view('header', $header);
        echo view('sid/nav', $nav);
        echo view('sid/wilayah/wilayah_form_rt', $data);
        echo view('footer');
    }

    public function insert_rt($dusun = '', $rw = '')
    {
        $this->wilayah_model->insert_rt($dusun, $rw);
        return redirect()->to("sid_core/sub_rt/{$dusun}/{$rw}");
    }

    public function update_rt($dusun = '', $rw = '', $id_cluster = 0)
    {
        $this->wilayah_model->update_rt($id_cluster);
        return redirect()->to("sid_core/sub_rt/{$dusun}/{$rw}");
    }

    public function delete_rt($id_cluster = '')
    {
        $temp     = $this->wilayah_model->cluster_by_id($id_cluster);
        $id_dusun = $temp['id'];
        $dusun    = $temp['dusun'];
        $rw       = $temp['rw'];
        $this->wilayah_model->delete_rt($id_cluster);
        echo '<script>self.history.back();self.history.back();</script>';
    }

    public function delete_all_rt()
    {
        $temp     = $this->wilayah_model->cluster_by_id($id_cluster);
        $id_dusun = $temp['id'];
        $dusun    = $temp['dusun'];
        $rw       = $temp['rw'];
        $this->wilayah_model->delete_all_rt();
        return redirect()->to('sid_core');
    }

    public function cetakx()
    {
        $data['input']            = $_POST;
        $data['tanggal_sekarang'] = tgl_indo(date('Y m d'));
        $data['total']            = $this->wilayah_model->total();
        $this->surat_keluar_model->log_surat($f, $id, $g, $u);
        echo view('surat/print_surat_ket_pengantar', $data);
    }

    public function ajax_wil_maps($id = 0)
    {
        $data['dusun']       = $this->wilayah_model->get_dusun_maps($id);
        $data['desa']        = $this->config_model->get_data();
        $data['form_action'] = site_url("sid_core/update_dusun_map/{$id}");

        echo view('sid/wilayah/ajax_wil_dusun', $data);
    }

    public function update_dusun_map($id = 0)
    {
        $this->wilayah_model->update_dusun_map($id);
        return redirect()->to('sid_core');
    }

    public function ajax_rw_maps($dus = 0, $id = 0)
    {
        $data['dusun']       = $this->wilayah_model->get_rw($dus, $id);
        $data['desa']        = $this->config_model->get_data();
        $data['form_action'] = site_url("sid_core/update_rw_map/{$dus}/{$id}");

        echo view('sid/wilayah/ajax_wil_dusun', $data);
    }

    public function update_rw_map($dus = 0, $id = 0)
    {
        $this->wilayah_model->update_rw_map($dus, $id);
        return redirect()->to("sid_core/sub_rw/{$dus}");
    }

    public function ajax_rt_maps($dus = 0, $rw = 0, $id = 0)
    {
        $data['dusun']       = $this->wilayah_model->get_rt($dus, $rw, $id);
        $data['desa']        = $this->config_model->get_data();
        $data['form_action'] = site_url("sid_core/update_rt_map/{$dus}/{$rw}/{$id}");

        echo view('sid/wilayah/ajax_wil_dusun', $data);
    }

    public function update_rt_map($dus = 0, $rw = 0, $id = 0)
    {
        $this->wilayah_model->update_rt_map($dus, $rw, $id);
        return redirect()->to("sid_core/sub_rt/{$dus}/{$rw}");
    }

    public function warga($id = '')
    {
        $temp     = $this->wilayah_model->cluster_by_id($id);
        $id_dusun = $temp['id'];
        $dusun    = $temp['dusun'];

        $_SESSION['per_page'] = 100;
        $_SESSION['dusun']    = $dusun;
        return redirect()->to('penduduk/index/1/0');
    }

    public function warga_kk($id = '')
    {
        $temp                 = $this->wilayah_model->cluster_by_id($id);
        $id_dusun             = $temp['id'];
        $dusun                = $temp['dusun'];
        $_SESSION['per_page'] = 50;
        $_SESSION['dusun']    = $dusun;
        return redirect()->to('keluarga/index/1/0');
    }

    public function warga_l($id = '')
    {
        $temp     = $this->wilayah_model->cluster_by_id($id);
        $id_dusun = $temp['id'];
        $dusun    = $temp['dusun'];

        $_SESSION['per_page'] = 100;
        $_SESSION['dusun']    = $dusun;
        $_SESSION['sex']      = 1;
        return redirect()->to('penduduk/index/1/0');
    }

    public function warga_p($id = '')
    {
        $temp     = $this->wilayah_model->cluster_by_id($id);
        $id_dusun = $temp['id'];
        $dusun    = $temp['dusun'];

        $_SESSION['per_page'] = 100;
        $_SESSION['dusun']    = $dusun;
        $_SESSION['sex']      = 2;
        return redirect()->to('penduduk/index/1/0');
    }

    public function migrate()
    {
        $this->wilayah_model->migrate();

        $this->dbforge->drop_table('tweb_dusun_x');
        $this->dbforge->drop_table('tweb_rw_x');
        $this->dbforge->drop_table('tweb_rt_x');
        $this->dbforge->drop_table('tweb_keluarga_x');
        $this->dbforge->drop_table('tweb_keluarga_x_pindah');
        $this->dbforge->drop_table('tweb_penduduk_x');
        $this->dbforge->drop_table('tweb_penduduk_x_pindah');

        return redirect()->to('penduduk/clear');
    }

    public function pre_migrate()
    {
        $nav['act'] = 3;
        $header     = $this->header_model->get_data();
        echo view('header', $header);
        echo view('sid/nav', $nav);
        echo view('sid/wilayah/mig');
        echo view('footer');
    }
}
