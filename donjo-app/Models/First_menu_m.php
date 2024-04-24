<?php

namespace App\Models;

use CodeIgniter\Model as CI_Model;

class First_menu_m extends CI_Model
{
    public function list_menu_atas()
    {
        $sql = 'SELECT m.* FROM menu m WHERE m.parrent = 1 AND m.enabled = 1 AND m.tipe = 1 order by id asc';

        $query = $this->db->query($sql);
        $data  = $query->getResult('array');

        $i = 0;

        while ($i < count($data)) {
            $data[$i]['menu'] = '<li><a href="' . site_url('first/' . $data[$i]['link']) . '">' . $data[$i]['nama'] . '</a>';

            $sql2  = 'SELECT s.* FROM menu s WHERE s.parrent = ? AND s.enabled = 1 AND s.tipe = 3';
            $query = $this->db->query($sql2, $data[$i]['id']);
            $data2 = $query->getResult('array');

            if ($data2) {
                $data[$i]['menu'] .= '<ul>';
                $j = 0;

                while ($j < count($data2)) {
                    $data[$i]['menu'] = $data[$i]['menu'] . '<li><a href="' . site_url('first/' . $data2[$j]['link']) . '">' . $data2[$j]['nama'] . '</a></li>';

                    $j++;
                }
                $data[$i]['menu'] .= '</ul>';
            }
            $data[$i]['menu'] .= '</li>';
            $i++;
        }

        return $data;
    }

    public function list_menu_kiri()
    {
        $sql = "SELECT m.*,m.kategori AS nama FROM kategori m WHERE m.parrent =0 AND m.enabled = 1 AND m.kategori <> 'teks_berjalan' ORDER BY id";

        $query = $this->db->query($sql);
        $data  = $query->getResult('array');
        $i     = 0;

        while ($i < count($data)) {
            $data[$i]['menu'] = '<li><a href="' . site_url('first/kategori/' . $data[$i]['id']) . '">' . $data[$i]['nama'] . '</a>';

            $sql2  = 'SELECT s.*,s.kategori AS nama FROM kategori s WHERE s.parrent = ? AND s.enabled = 1';
            $query = $this->db->query($sql2, $data[$i]['id']);
            $data2 = $query->getResult('array');

            if ($data2) {
                $data[$i]['menu'] .= '<ul>';
                $j = 0;

                while ($j < count($data2)) {
                    $data[$i]['menu'] = $data[$i]['menu'] . '<li><a href="' . site_url('first/kategori/' . $data2[$j]['id']) . '">' . $data2[$j]['nama'] . '</a></li>';
                    $j++;
                }
                $data[$i]['menu'] .= '</ul>';
            }
            $data[$i]['menu'] .= '</li>';
            $i++;
        }

        return $data;
    }
}
