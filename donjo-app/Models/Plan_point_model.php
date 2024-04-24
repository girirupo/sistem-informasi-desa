<?php

namespace App\Models;

use CodeIgniter\Model as CI_Model;

use App\Libraries\Paging;

class Plan_point_model extends CI_Model
{
    public function autocomplete()
    {
        $sql   = 'SELECT nama FROM point';
        $query = $this->db->query($sql);
        $data  = $query->getResultArray();

        $i    = 0;
        $outp = '';

        while ($i < count($data)) {
            $outp .= ',"' . $data[$i]['nama'] . '"';
            $i++;
        }
        $outp = strtolower(substr($outp, 1));

        return '[' . $outp . ']';
    }

    public function search_sql()
    {
        if (isset($_SESSION['cari'])) {
            $cari       = $_SESSION['cari'];
            $kw         = $this->db->escape_like_str($cari);
            $kw         = '%' . $kw . '%';
            $search_sql = " AND (nama LIKE '{$kw}')";

            return $search_sql;
        }
    }

    public function filter_sql()
    {
        if (isset($_SESSION['filter'])) {
            $kf         = $_SESSION['filter'];
            $filter_sql = " AND enabled = {$kf}";

            return $filter_sql;
        }
    }

    public function paging($p = 1, $o = 0)
    {
        $paging = new Paging();

        $sql = 'SELECT COUNT(id) AS id FROM point WHERE tipe = 0 ';
        $sql .= $this->search_sql();
        $query    = $this->db->query($sql);
        $row      = $query->getRowArray();
        $jml_data = $row['id'];

        $cfg['page']     = $p;
        $cfg['per_page'] = $_SESSION['per_page'];
        $cfg['num_rows'] = $jml_data;

        $paging->init($cfg);

        return $paging;
    }

    public function list_data($o = 0, $offset = 0, $limit = 500)
    {
        switch ($o) {
            case 1: $order_sql = ' ORDER BY nama';
                break;

            case 2: $order_sql = ' ORDER BY nama DESC';
                break;

            case 3: $order_sql = ' ORDER BY enabled';
                break;

            case 4: $order_sql = ' ORDER BY enabled DESC';
                break;

            default:$order_sql = ' ORDER BY id';
        }
        $paging_sql = ' LIMIT ' . $offset . ',' . $limit;

        $sql = 'SELECT * FROM point WHERE tipe = 0 ';

        $sql .= $this->search_sql();
        $sql .= $this->filter_sql();
        $sql .= $order_sql;
        $sql .= $paging_sql;

        $query = $this->db->query($sql);
        $data  = $query->getResultArray();

        $i = 0;
        $j = $offset;

        while ($i < count($data)) {
            $data[$i]['no'] = $j + 1;

            if ($data[$i]['enabled'] === 1) {
                $data[$i]['aktif'] = 'Yes';
            } else {
                $data[$i]['aktif'] = 'No';
            }

            $i++;
            $j++;
        }

        return $data;
    }

    public function insert()
    {
        $data = $_POST;

        $outp = $this->db->insert('point', $data);
        if ($outp) {
            $_SESSION['success'] = 1;
        } else {
            $_SESSION['success'] = -1;
        }
    }

    public function update($id = 0)
    {
        $data = $_POST;

        $this->db->where('id', $id);
        $outp = $this->db->update('point', $data);
        if ($outp) {
            $_SESSION['success'] = 1;
        } else {
            $_SESSION['success'] = -1;
        }
    }

    public function delete($id = '')
    {
        $sql  = 'DELETE FROM point WHERE id=?';
        $outp = $this->db->query($sql, [$id]);

        if ($outp) {
            $_SESSION['success'] = 1;
        } else {
            $_SESSION['success'] = -1;
        }
    }

    public function delete_all()
    {
        $id_cb = $_POST['id_cb'];

        if (count($id_cb)) {
            foreach ($id_cb as $id) {
                $sql  = 'DELETE FROM point WHERE id=?';
                $outp = $this->db->query($sql, [$id]);
            }
        } else {
            $outp = false;
        }

        if ($outp) {
            $_SESSION['success'] = 1;
        } else {
            $_SESSION['success'] = -1;
        }
    }

    public function list_sub_point($point = 1)
    {
        $sql = 'SELECT * FROM point WHERE parrent = ? AND tipe = 2 ';

        $query = $this->db->query($sql, $point);
        $data  = $query->getResultArray();

        $i = 0;

        while ($i < count($data)) {
            $data[$i]['no'] = $i + 1;

            if ($data[$i]['enabled'] === 1) {
                $data[$i]['aktif'] = 'Yes';
            } else {
                $data[$i]['aktif'] = 'No';
            }

            $i++;
        }

        return $data;
    }

    public function insert_sub_point($parrent = 0)
    {
        $data            = $_POST;
        $data['parrent'] = $parrent;
        $data['tipe']    = 2;
        $outp            = $this->db->insert('point', $data);
        if ($outp) {
            $_SESSION['success'] = 1;
        } else {
            $_SESSION['success'] = -1;
        }
    }

    public function update_sub_point($id = 0)
    {
        $data = $_POST;

        $this->db->where('id', $id);
        $outp = $this->db->update('point', $data);
        if ($outp) {
            $_SESSION['success'] = 1;
        } else {
            $_SESSION['success'] = -1;
        }
    }

    public function delete_sub_point($id = '')
    {
        $sql  = 'DELETE FROM point WHERE id=?';
        $outp = $this->db->query($sql, [$id]);

        if ($outp) {
            $_SESSION['success'] = 1;
        } else {
            $_SESSION['success'] = -1;
        }
    }

    public function delete_all_sub_point()
    {
        $id_cb = $_POST['id_cb'];

        if (count($id_cb)) {
            foreach ($id_cb as $id) {
                $sql  = 'DELETE FROM point WHERE id=?';
                $outp = $this->db->query($sql, [$id]);
            }
        } else {
            $outp = false;
        }

        if ($outp) {
            $_SESSION['success'] = 1;
        } else {
            $_SESSION['success'] = -1;
        }
    }

    public function point_lock($id = '', $val = 0)
    {
        $sql  = 'UPDATE point SET enabled=? WHERE id=?';
        $outp = $this->db->query($sql, [$val, $id]);

        if ($outp) {
            $_SESSION['success'] = 1;
        } else {
            $_SESSION['success'] = -1;
        }
    }

    public function get_point($id = 0)
    {
        $sql   = 'SELECT * FROM point WHERE id=?';
        $query = $this->db->query($sql, $id);

        return $query->getRowArray();
    }

    public function point_show()
    {
        $sql   = 'SELECT * FROM point WHERE enabled=?';
        $query = $this->db->query($sql, 1);

        return $query->getResultArray();
    }

    public function list_simbol()
    {
        $sql   = 'SELECT * FROM gis_simbol WHERE 1';
        $query = $this->db->query($sql);

        return $query->getResultArray();
    }

    public function list_point_atas()
    {
        $sql = 'SELECT m.* FROM point m WHERE m.parrent = 1 AND m.enabled = 1 AND m.tipe = 1';

        $query = $this->db->query($sql);
        $data  = $query->getResultArray();
        $url   = site_url('first');
        $i     = 0;

        while ($i < count($data)) {
            $data[$i]['point'] = "<li><a href='{$url}/" . $data[$i]['simbol'] . "'>" . $data[$i]['nama'] . '</a>';

            $sql2  = 'SELECT s.* FROM point s WHERE s.parrent = ? AND s.enabled = 1 AND s.tipe = 3';
            $query = $this->db->query($sql2, $data[$i]['id']);
            $data2 = $query->getResultArray();

            if ($data2) {
                $data[$i]['point'] .= '<ul>';
                $j = 0;

                while ($j < count($data2)) {
                    $data[$i]['point'] = $data[$i]['point'] . "<li><a href='{$url}/" . $data2[$j]['simbol'] . "'>" . $data2[$j]['nama'] . '</a></li>';
                    $j++;
                }
                $data[$i]['point'] .= '</ul>';
            }
            $data[$i]['point'] .= '</li>';
            $i++;
        }

        return $data;
    }

    public function list_point_kiri()
    {
        $sql = 'SELECT m.* FROM point m WHERE m.parrent = 1 AND m.enabled = 1 AND m.tipe = 2';

        $query = $this->db->query($sql);
        $data  = $query->getResultArray();
        $url   = site_url('first');
        $i     = 0;

        while ($i < count($data)) {
            $data[$i]['point'] = "<li><a href='{$url}/" . $data[$i]['simbol'] . "'>" . $data[$i]['nama'] . '</a>';

            $sql2  = 'SELECT s.* FROM point s WHERE s.parrent = ? AND s.enabled = 1 AND s.tipe = 3';
            $query = $this->db->query($sql2, $data[$i]['id']);
            $data2 = $query->getResultArray();

            if ($data2) {
                $data[$i]['point'] .= '<ul>';
                $j = 0;

                while ($j < count($data2)) {
                    $data[$i]['point'] = $data[$i]['point'] . "<li><a href='{$url}/" . $data2[$j]['simbol'] . "'>" . $data2[$j]['nama'] . '</a></li>';
                    $j++;
                }
                $data[$i]['point'] .= '</ul>';
            }
            $data[$i]['point'] .= '</li>';
            $i++;
        }

        return $data;
    }
}
