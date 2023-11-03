<?= $this->extend('template') ?>

<?= $this->section('content') ?>
    <link rel="stylesheet" href="<?= base_url('assets/css/screen.css') ?>">

    <style>
        body {
            background: url(<?= base_url('assets/files/bg.jpg') ?>) no-repeat center center fixed;
            -webkit-background-size: cover;
            -moz-background-size: cover;
            -o-background-size: cover;
            background-size: cover;
            margin: 0px;
            padding: 0px;
        }

        #full {
            max-width: 400px;
            background: #ddddff repeat-x;
            margin: 150px auto 0px auto;
            padding: 10px;
            box-shadow: 0px 0px 15px #777;
            -moz-box-shadow: 0px 0px 15px #777;
            -webkit-box-shadow: 0px 0px 15px #777;
            font-size: 1.5em;
        }

    </style>

<?= $this->endSection() ?>

<?= $this->section('content') ?>
    <div id="full">
        <p>
            Selamat, aplikasi Sistem Infomasi Desa telah berhasil diinstall.
        </p>
        <a href="<?= site_url('/') ?>" class="uibutton special">Lihat situs</a>

    </div>
<?= $this->endSection() ?>
