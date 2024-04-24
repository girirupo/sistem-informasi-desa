<?php

namespace App\Models;

use CodeIgniter\Model as CI_Model;

class Laporan_perubahan_model extends CI_Model
{
    public function list_data()
    {
        $sql = "SELECT x.dusun,
(SELECT COUNT(id_pend) FROM log_perubahan_penduduk c LEFT JOIN tweb_penduduk b ON c.id_pend=b.id WHERE b.sex ='1') AS lalu_L,
(SELECT COUNT(id_pend) FROM log_perubahan_penduduk c LEFT JOIN tweb_penduduk b ON c.id_pend=b.id WHERE b.sex ='1') AS lalu_P,
(SELECT COUNT(id_pend) FROM log_penduduk a LEFT JOIN tweb_penduduk b ON a.id_pend=b.id LEFT JOIN tweb_wil_clusterdesa c ON b.id_cluster=c.id WHERE b.sex='1' AND month(a.tanggal)=month(curdate()) AND year(a.tanggal)=year(curdate()) AND c.dusun=x.dusun AND a.id_detail='7') AS pecah_L ,
(SELECT COUNT(id_pend) FROM log_penduduk a LEFT JOIN tweb_penduduk b ON a.id_pend=b.id LEFT JOIN tweb_wil_clusterdesa c ON b.id_cluster=c.id WHERE b.sex='2' AND month(a.tanggal)=month(curdate()) AND year(a.tanggal)=year(curdate()) AND c.dusun=x.dusun AND a.id_detail='7') AS pecah_P ,
(SELECT COUNT(id_pend) FROM log_penduduk a LEFT JOIN tweb_penduduk b ON a.id_pend=b.id LEFT JOIN tweb_wil_clusterdesa c ON b.id_cluster=c.id WHERE b.sex='1' AND month(a.tanggal)=month(curdate()) AND year(a.tanggal)=year(curdate()) AND c.dusun=x.dusun AND a.id_detail='5') AS datang_L ,
(SELECT COUNT(id_pend) FROM log_penduduk a LEFT JOIN tweb_penduduk b ON a.id_pend=b.id LEFT JOIN tweb_wil_clusterdesa c ON b.id_cluster=c.id WHERE b.sex='2' AND month(a.tanggal)=month(curdate()) AND year(a.tanggal)=year(curdate()) AND c.dusun=x.dusun AND a.id_detail='5') AS datang_P ,
(SELECT COUNT(id_pend) FROM log_penduduk a LEFT JOIN tweb_penduduk b ON a.id_pend=b.id LEFT JOIN tweb_wil_clusterdesa c ON b.id_cluster=c.id WHERE b.sex='1' AND month(a.tanggal)=month(curdate()) AND year(a.tanggal)=year(curdate()) AND c.dusun=x.dusun AND a.id_detail='3') AS pergi_L ,
(SELECT COUNT(id_pend) FROM log_penduduk a LEFT JOIN tweb_penduduk b ON a.id_pend=b.id LEFT JOIN tweb_wil_clusterdesa c ON b.id_cluster=c.id WHERE b.sex='2' AND month(a.tanggal)=month(curdate()) AND year(a.tanggal)=year(curdate()) AND c.dusun=x.dusun AND a.id_detail='3') AS pergi_P ,
(SELECT COUNT(id_pend) FROM log_penduduk a LEFT JOIN tweb_penduduk b ON a.id_pend=b.id LEFT JOIN tweb_wil_clusterdesa c ON b.id_cluster=c.id WHERE b.sex='1' AND month(a.tanggal)=month(curdate()) AND year(a.tanggal)=year(curdate()) AND c.dusun=x.dusun AND a.id_detail='2') AS mati_L ,
(SELECT COUNT(id_pend) FROM log_penduduk a LEFT JOIN tweb_penduduk b ON a.id_pend=b.id LEFT JOIN tweb_wil_clusterdesa c ON b.id_cluster=c.id WHERE b.sex='2' AND month(a.tanggal)=month(curdate()) AND year(a.tanggal)=year(curdate()) AND c.dusun=x.dusun AND a.id_detail='2') AS mati_P
FROM tweb_wil_clusterdesa x WHERE rw='0' AND rt='0' ";
        $query = $this->db->query($sql);

        return $query->getResultArray();
    }

    public function total_data()
    {
        $sql = "SELECT SUM(lalu_L) as tlaluL,SUM(lalu_P) as tlaluP,SUM(pecah_L) as tpecahL,SUM(pecah_P) as tpecahP,SUM(datang_L) as tdatangL,SUM(datang_p) as tdatangP,SUM(pergi_L) as tpergiL,SUM(pergi_P) as tpergiP,SUM(mati_L) as tmatiL,SUM(mati_P) as tmatiP FROM(SELECT x.dusun,
(SELECT COUNT(id_pend) FROM log_perubahan_penduduk c LEFT JOIN tweb_penduduk d ON c.id_pend=d.id WHERE d.sex ='1' ) AS lalu_L,
(SELECT COUNT(id_pend) FROM log_perubahan_penduduk c LEFT JOIN tweb_penduduk d ON c.id_pend=d.id WHERE d.sex ='1' ) AS lalu_P,
(SELECT COUNT(id_pend) FROM log_penduduk a LEFT JOIN tweb_penduduk b ON a.id_pend=b.id LEFT JOIN tweb_wil_clusterdesa c ON b.id_cluster=c.id WHERE b.sex='1' AND month(a.tanggal)=month(curdate()) AND year(a.tanggal)=year(curdate()) AND c.dusun=x.dusun AND a.id_detail='7') AS pecah_L ,
(SELECT COUNT(id_pend) FROM log_penduduk a LEFT JOIN tweb_penduduk b ON a.id_pend=b.id LEFT JOIN tweb_wil_clusterdesa c ON b.id_cluster=c.id WHERE b.sex='2' AND month(a.tanggal)=month(curdate()) AND year(a.tanggal)=year(curdate()) AND c.dusun=x.dusun AND a.id_detail='7') AS pecah_P ,
(SELECT COUNT(id_pend) FROM log_penduduk a LEFT JOIN tweb_penduduk b ON a.id_pend=b.id LEFT JOIN tweb_wil_clusterdesa c ON b.id_cluster=c.id WHERE b.sex='1' AND month(a.tanggal)=month(curdate()) AND year(a.tanggal)=year(curdate()) AND c.dusun=x.dusun AND a.id_detail='5') AS datang_L ,
(SELECT COUNT(id_pend) FROM log_penduduk a LEFT JOIN tweb_penduduk b ON a.id_pend=b.id LEFT JOIN tweb_wil_clusterdesa c ON b.id_cluster=c.id WHERE b.sex='2' AND month(a.tanggal)=month(curdate()) AND year(a.tanggal)=year(curdate()) AND c.dusun=x.dusun AND a.id_detail='5') AS datang_P ,
(SELECT COUNT(id_pend) FROM log_penduduk a LEFT JOIN tweb_penduduk b ON a.id_pend=b.id LEFT JOIN tweb_wil_clusterdesa c ON b.id_cluster=c.id WHERE b.sex='1' AND month(a.tanggal)=month(curdate()) AND year(a.tanggal)=year(curdate()) AND c.dusun=x.dusun AND a.id_detail='3') AS pergi_L ,
(SELECT COUNT(id_pend) FROM log_penduduk a LEFT JOIN tweb_penduduk b ON a.id_pend=b.id LEFT JOIN tweb_wil_clusterdesa c ON b.id_cluster=c.id WHERE b.sex='2' AND month(a.tanggal)=month(curdate()) AND year(a.tanggal)=year(curdate()) AND c.dusun=x.dusun AND a.id_detail='3') AS pergi_P ,
(SELECT COUNT(id_pend) FROM log_penduduk a LEFT JOIN tweb_penduduk b ON a.id_pend=b.id LEFT JOIN tweb_wil_clusterdesa c ON b.id_cluster=c.id WHERE b.sex='1' AND month(a.tanggal)=month(curdate()) AND year(a.tanggal)=year(curdate()) AND c.dusun=x.dusun AND a.id_detail='2') AS mati_L ,
(SELECT COUNT(id_pend) FROM log_penduduk a LEFT JOIN tweb_penduduk b ON a.id_pend=b.id LEFT JOIN tweb_wil_clusterdesa c ON b.id_cluster=c.id WHERE b.sex='2' AND month(a.tanggal)=month(curdate()) AND year(a.tanggal)=year(curdate()) AND c.dusun=x.dusun AND a.id_detail='2') AS mati_P
FROM tweb_wil_clusterdesa x WHERE rw='0' AND rt='0') as z ";
        $query = $this->db->query($sql);

        return $query->getResultArray();
    }
}
