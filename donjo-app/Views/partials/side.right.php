<?php
$db = db_connect();
?>
<?php if (isset($_SESSION['mandiri']) && $_SESSION['mandiri'] !== 1 && $_SESSION['mandiri_wait'] === 1) { ?>
    <div class="box box-primary box-solid">
        <div class="box-header">
            <h3 class="box-title"><i class="fa fa-user"></i> Layanan Mandiri</h3><br>
            Silakan datang / hubungi perangkat desa untuk mendapatkan kode PIN Anda.
        </div>
        <div class="box-body">
            <h4>Gagal 3 kali. Sila coba kembali dalam <?= waktu_ind((time() - $_SESSION['mandiri_timeout']) * (-1)); ?> detik lagi</h4>
            <div id="note">
                Login Gagal. Username atau Password yang Anda masukkan salah!
            </div>
        </div>
    </div>
<?php } elseif (!isset($_SESSION['mandiri'])) { ?>
    <div class="box box-primary box-solid">
        <div class="box-header">
            <h3 class="box-title"><i class="fa fa-user"></i> Layanan Mandiri</h3><br>
            Silakan datang / hubungi perangkat desa untuk mendapatkan kode PIN Anda.
        </div>
        <div class="box-body">
            <h4>Masukkan NIK dan PIN!</h4>
            <form action="<?= site_url('first/auth') ?>" method="post">
                <input name="nik" type="text" placeholder="NIK" value="" required>
                <input name="pin" type="password" placeholder="PIN" value="" required>
                <button type="submit" id="but">Masuk</button>
                <?php if (isset($_SESSION['mandiri'])) { ?>
                    <?php if ($_SESSION['mandiri_try'] && $_SESSION['mandiri'] === -1) { ?>
                        <div id="note">
                            Kesempatan mencoba <?= $_SESSION['mandiri_try'] - 1; ?> kali lagi.
                        </div>
                    <?php } ?>
                    <?php if ($_SESSION['mandiri'] === -1) { ?>
                        <div id="note">
                            Login Gagal. Username atau Password yang Anda masukkan salah!
                        </div>
                    <?php } ?>
                <?php } ?>
            </form>
        </div>
    </div>
<?php } else { ?>
    <div class="box box-primary box-solid">
        <div class="box-header">
            <h3 class="box-title"><i class="fa fa-user"></i> Layanan Mandiri</h3>
        </div>
        <div class="box-body">
            <ul>
                <table style="padding:2px;">
                    <tr>
                        <td>
                            Nama </td>
                        <td>: <?= $_SESSION['nama']; ?></td>
                    </tr>
                    <tr>
                        <td>
                            NIK </td>
                        <td>: <?= $_SESSION['nik']; ?></td>
                    </tr>
                    <tr style="border-bottom:1px solid #111;">
                        <td>
                            <h4><a href="<?= site_url('first/mandiri/1/1') ?>" class="">Profil Saya </a> </h4>
                        </td>
                        <td></td>
                    </tr>
                    <tr style="border-bottom:1px solid #111;">
                        <td>
                            <h4><a href="<?= site_url('first/mandiri/1/2') ?>" class="">Layanan </a> </h4>
                        </td>
                        <td></td>
                    </tr>
                    <tr style="border-bottom:1px solid #111;">
                        <td>
                            <h4><a href="<?= site_url('first/mandiri/1/3') ?>" class="">Lapor </a> </h4>
                        </td>
                        <td></td>
                    </tr>
                    <tr style="border-bottom:1px solid #111;">
                        <td>
                            <h4><a href="<?= site_url('first/logout') ?>" class="">Keluar</a></h4>
                        </td>
                        <td></td>
                    </tr>
                </table>
        </div>
    </div>
    <?php if ($_SESSION['lg'] === 1) { ?>
        <div class="box box-primary box-solid">
            <div class="box-header">
                <h3 class="box-title"><i class="fa fa-user"></i> Layanan Mandiri</h3><br>
                Untuk keamanan, sila ubah kode PIN Anda.
            </div>
            <div class="box-body">
                <h4>Masukkan PIN Baru</h4>
                <form action="<?= site_url('first/ganti') ?>" method="post">
                    <input name="pin1" type="password" placeholder="PIN" value="">
                    <input name="pin2" type="password" placeholder="Ulangi PIN" value="">
                    <button type="submit" id="but">Ganti</button>
                </form>
                <div id="note">
                    Silakan login kembali setelah PIN baru disimpan.
                </div>
            </div>
        </div>
    <?php } elseif ($_SESSION['lg'] === 1) { ?>
        <div class="box box-primary box-solid">
            <div class="box-header">
                <h3 class="box-title"><i class="fa fa-user"></i> Layanan Mandiri</h3><br>
                Untuk keamanan, silakan ubah kode PIN Anda.
            </div>
            <div class="box-body">
                <div id="note">
                    PIN baru berhasil disimpan!
                </div>
            </div>
        </div>
        <?php unset($_SESSION['lg']); ?>
    <?php } ?>
<?php } ?>

<!-- widget Agenda-->
<?php if ($agenda) { ?>
    <div class="box box-primary box-solid">
        <div class="box-header">
            <h3 class="box-title"><a href="<?= site_url('first/kategori/4') ?>"><i class="fa fa-calendar"></i> Agenda</a></h3>
        </div>
        <div class="box-body">
            <ul class="sidebar-latest">
                <?php foreach ($agenda as $l) { ?>
                    <li><a href="<?= site_url("first/artikel/{$l['id']}") ?>"><?= $l['judul'] ?></a></li>
                <?php } ?>
            </ul>
        </div>
    </div>
<?php } ?>

<!-- widget Galeri-->
<?php if (count($w_gal)) { ?>
    <div class="box box-warning box-solid">
        <div class="box-header">
            <h3 class="box-title"><a href="<?= site_url('first/gallery') ?>"><i class="fa fa-camera"></i> Galeri Foto</a></h3>
        </div>
        <div class="box-body">
            <ul class="sidebar-latest">
                <?php foreach ($w_gal as $data) { ?>

                    <?php if (is_file('assets/files/galeri/sedang_' . $data['gambar'])) { ?>
                        <a class="group3" href="<?= base_url('assets/files/galeri/sedang_' . $data['gambar']) ?>">

                            <img src="<?= base_url('assets/files/galeri/kecil_' . $data['gambar']) ?>" width="130" alt="<?= $data['nama'] ?>">

                        </a>
                    <?php } ?>
                <?php } ?>
            </ul>
        </div>
    </div>
<?php } ?>
<!-- widget Komentar-->
<?php if (count($komen)) { ?>
    <div class="box box-primary box-solid">
        <div class="box-header">
            <h3 class="box-title"><i class="fa fa-comments"></i> Komentar Terkini</h3>
        </div>
        <div class="box-body">
            <ul class="sidebar-latest">
                <?php foreach ($komen as $data) { ?>
                    <li><i class="fa fa-comment"></i> <?= $data['owner'] ?> :
                        <?= $data['komentar'] ?><br>
                        <small>ditulis pada <?= tgl_indo2($data['tgl_upload']) ?></small>
                        <br>
                        <br>
                    </li>
                <?php } ?>
            </ul>
        </div>
    </div>
<?php } ?>
<!-- widget SocMed -->
<div class="box box-default">
    <div class="box-header">
        <h3 class="box-title"><i class="fa fa-globe"></i> Media Sosial</h3>
    </div>
    <div class="box-body">
        <?php foreach ($sosmed as $data) {
            echo '<a href="' . $data['link'] . '" target="_blank"><img src="' . base_url('assets/front/' . $data['gambar']) . '" alt="' . $data['nama'] . '" style="width:50px;height:50px;"></a>';
        } ?>
    </div>
</div>

<div class="box box-success">
    <div class="box-header">
        <h3 class="box-title"><i class="fa fa-bar-chart-o"></i> Statistik Kunjungan</h3>
    </div>
    <div class="box-body">
        <?php
        $ip = $_SERVER['REMOTE_ADDR'] . '{}';
        if (!isset($_SESSION['MemberOnline'])) {
            $cek = $db->query("SELECT Tanggal,ipAddress FROM sys_traffic WHERE Tanggal='" . date('Y-m-d') . "'");
            if ($cek->getNumRows() === 0) {
                $up                       = $db->query("INSERT INTO sys_traffic (Tanggal,ipAddress,Jumlah) VALUES ('" . date('Y-m-d') . "','" . $ip . "','1')");
                $_SESSION['MemberOnline'] = date('Y-m-d H:i:s');
            } else {
                $res                      = $cek->getRow(0);
                $ipaddr                   = $res->ipAddress;
                $up                       = $db->query("UPDATE sys_traffic SET Jumlah=Jumlah + 1,ipAddress='" . $ip . "' WHERE Tanggal='" . date('Y-m-d') . "'");
                $_SESSION['MemberOnline'] = date('Y-m-d H:i:s');
            }
        }
        $rs = $db->query('SELECT Jumlah AS Visitor FROM sys_traffic WHERE Tanggal="' . date('Y-m-d') . '" LIMIT 1');
        if ($rs->getNumRows() > 0) {
            $visitor = $rs->getRow(0);
            $today   = $visitor->Visitor;
        } else {
            $today = 0;
        }
        $strSQL = 'SELECT Jumlah AS Visitor FROM sys_traffic WHERE
	Tanggal=(SELECT DATE_ADD(CURDATE(),INTERVAL -1 DAY) FROM sys_traffic LIMIT 1)
	LIMIT 1';
        $rs = $db->query($strSQL);
        if ($rs->getNumRows() > 0) {
            $visitor   = $rs->getRow(0);
            $yesterday = $visitor->Visitor;
        } else {
            $yesterday = 0;
        }
        $rs      = $db->query('SELECT SUM(Jumlah) as Total FROM sys_traffic');
        $visitor = $rs->getRow(0);
        $total   = $visitor->Total;
        function num_toimage($tot, $jumlah)
        {
            $pattern = '';

            for ($j = 0; $j < $jumlah; $j++) {
                $pattern .= '0';
            }
            $len      = strlen($tot);
            $length   = strlen($pattern) - $len;
            $start    = substr($pattern, 0, $length) . substr($tot, 0, $len - 1);
            $last     = substr($tot, $len - 1, 1);
            $last_rpc = '<img src="_BASE_URL_/assets/images/counter/animasi/' . $last . '.gif" align="absmiddle">';
            $inc      = str_replace($last, $last_rpc, $last);

            for ($i = 0; $i <= 9; $i++) {
                $rpc   = '<img src="_BASE_URL_/assets/images/counter/' . $i . '.gif" align="absmiddle">';
                $start = str_replace($i, $rpc, $start);
            }
            $num = $start . $inc;

            return str_replace('_BASE_URL_', base_url(), $num);
        }
        ?>
        <div id="container" align="center">
            <table cellpadding="0" cellspacing="0" class="counter">
                <tr>
                    <td> Hari ini</td>
                    <td><?= num_toimage($today, 5); ?></td>
                </tr>
                <tr>
                    <td valign="middle" height="20">Kemarin </td>
                    <td valign="middle"><?= num_toimage($yesterday, 5); ?></td>
                </tr>
                <tr>
                    <td valign="middle" height="20">Jumlah pengunjung</td>
                    <td valign="middle"><?= num_toimage($total, 5); ?></td>
                </tr>
            </table>
        </div>
    </div>
</div>

<!-- widget Arsip Artikel -->
<div class="box box-primary box-solid">
    <div class="box-header">
        <h3 class="box-title"><a href="<?= site_url('first/arsip') ?>"><i class="fa fa-archive"></i> Arsip Artikel</a></h3>
    </div>
    <div class="box-body">
        <ul>
            <?php foreach ($arsip as $l) { ?>
                <li><a href="<?= site_url("first/artikel/{$l['id']}") ?>"><?= $l['judul'] ?></a></li>
            <?php } ?>
        </ul>
    </div>
</div>

<!--widget Manual-->
<?php
if ($w_cos) {
    foreach ($w_cos as $data) {
        echo '
		<div class="box box-primary box-solid">
			<div class="box-header">
				<h3 class="box-title">' . $data['judul'] . '</h3>
			</div>
			<div class="box-body">
			' . $data['isi'] . '
			</div>
		</div>
		';
    }
}
?>

<!-- widget Google Map -->
<?php if ($desa['lat'] !== '0') { ?>
    <?= '
	<div class="box box-default box-solid">
		<div class="box-header">
			<h3 class="box-title"><i class="fa fa-map-marker"></i> Lokasi ' . $desa['nama_desa'] . '</h3>
		</div>
		<div class="box-body">
			<div id="map_canvas" style="height:200px;"></div>
			<script src="//maps.google.com/maps/api/js?key=' . $desa['gapi_key'] . '&sensor=false"></script>';
    ?>
    <script>
        var map;
        var marker;
        var location;

        function initialize() {
            var myLatlng = new google.maps.LatLng(<?= $desa['lat'] . ',' . $desa['lng']; ?>);
            var myOptions = {
                zoom: <?= $desa['zoom']; ?>,
                center: myLatlng,
                mapTypeId: google.maps.MapTypeId.ROADMAP,
                overviewMapControl: true
            }
            map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);

            var marker = new google.maps.Marker({
                position: new google.maps.LatLng(<?= $desa['lat'] . ',' . $desa['lng']; ?>),
                map: map,
                draggable: false
            });
        }

        function addEvent(obj, evType, fn) {
            if (obj.addEventListener) {
                obj.addEventListener(evType, fn, false);
                return true;
            } else if (obj.attachEvent) {
                var r = obj.attachEvent("on" + evType, fn);
                return r;
            } else {
                return false;
            }
        }
        addEvent(window, 'load', initialize);
    </script>
    <?= '
			<a href="//www.google.co.id/maps/@' . $desa['lat'] . ',' . $desa['lng'] . 'z?hl=id" target="_blank">tampilkan dalam peta lebih besar</a><br>
		</div>
	</div>
	';
    ?>
<?php }
?>
