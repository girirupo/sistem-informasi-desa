<style>
    table.head {
        font-size: 14px;
        font-weight: bold;
    }

    tr.total td {
        font-weight: bold;
        background-color: #7fffaf;
    }

    tr.bg td {
        font-weight: bold;
        background-color: #efff22;
    }
</style>
<div id="pageC">
    <?= view('analisis_master/left', $data); ?>
    <div class="content-header">
    </div>
    <div id="contentpane">
        <div class="ui-layout-north panel">
        </div>
        <div class="ui-layout-center" id="maincontent">
            <table class="head">
                <tr>
                    <td width="150">Hasil Pendataan</td>
                    <td> : </td>
                    <td><a href="<?= site_url('analisis_master/menu/' . $_SESSION['analisis_master']) ?>"><?= $analisis_master['nama'] ?></a></td>
                </tr>
                <tr>
                    <td>Nomor Identitas</td>
                    <td> : </td>
                    <td><?= $subjek['nid'] ?></td>
                </tr>
                <tr>
                    <td>Nama Subjek</td>
                    <td> : </td>
                    <td><?= $subjek['nama'] ?></td>
                </tr>
            </table>
            <?php if ($list_anggota) { ?>
                <h4>DAFTAR ANGGOTA</h4>
                <table class="list data">
                    <tr>
                        <th width="10">NO</th>
                        <th width="100">NIK</th>
                        <th width="200">NAMA</th>
                        <th width="100">TANGGAL LAHIR</th>
                        <th width="80">JENIS KELAMIN</th>
                        <th>&nbsp;</th>
                    </tr>
                    <?php $i = 1;

                    foreach ($list_anggota as $ang) { ?>
                        <tr>
                            <td><?= $i ?></td>
                            <td><?= $ang['nik'] ?></td>
                            <td><?= $ang['nama'] ?></td>
                            <td><?= tgl_indo($ang['tanggallahir']) ?></td>
                            <td><?php if ($ang['sex'] === 1) {
                                    echo 'LAKI-LAKI';
                                } ?><?php if ($ang['sex'] === 2) {
                            echo 'PEREMPUAN';
                        } ?></td>
                            <td>&nbsp;</td>
                        </tr>
                    <?php $i++;
                    } ?>
                </table>
            <?php } ?>
            <input type="hidden" name="rt" value="">
            <table class="list">
                <tr>
                    <th width='16'>No</th>
                    <th width='360'>Pertanyaan / Indikator</th>
                    <th width='20'>Bobot</td>
                    <th width='200'>Jawaban</th>
                    <th width='20'>Nilai</th>
                    <th width='20'>Poin</th>
                    <th></th>
                </tr>
                <?php foreach ($list_jawab as $data) {
                    if ($data['cek'] >= 1) {
                        $bg = "class='bg'";
                    } else {
                        $bg = '';
                    } ?>
                    <tr <?= $bg ?>>
                        <td><?= $data['no'] ?></td>
                        <td><?= $data['pertanyaan'] ?></td>
                        <td><?= $data['bobot'] ?></td>
                        <td><?= $data['jawaban'] ?></td>
                        <td><?= $data['nilai'] ?></td>
                        <td><?= $data['poin'] ?></td>
                        <td></td>
                    </tr>
                <?php
                } ?>
                <tr class="total">
                    <td colspan='5'>TOTAL</td>
                    <td><?= $total ?></td>
                    <td></td>
                </tr>
            </table>
            <?php if ($list_bukti) { ?>
                <table>
                    <tr>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <table>
                            <tr>
                                <td>
                                    <h3>Berkas Bukti / Pengesahan Form Pendataan</h3>
                                </td>
                            </tr>
                            <tr>
                                <?php foreach ($list_bukti as $bukti) { ?>
                                    <td>
                                        <a href="<?= base_url('assets/files/pengesahan/' . $bukti['pengesahan']) ?>" target="_blank">
                                            <img src="<?= base_url('assets/files/pengesahan/' . $bukti['pengesahan']) ?>" width='320'>
                                        </a>
                                    </td>
                                <?php } ?>
                            </tr>
                        </table>
                    <?php } ?>
        </div>
        <div class="ui-layout-south panel bottom">
            <div class="left">
                <a href="<?= site_url('analisis_laporan') ?>" class="uibutton icon prev">Kembali</a>
            </div>
            <div class="right">
                <div class="uibutton-group">
                </div>
            </div>
        </div>
    </div>
