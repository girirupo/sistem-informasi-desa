<?= view('layouts/header.php');?>
<div id="contentwrapper">
    <div id="contentcolumn">
        <div class="innertube">
            <?= view('partials/artikel.php') ?>
        </div>
    </div>
</div>
<div id="rightcolumn">
    <div class="innertube">
        <?= view('partials/side.right.php');?>
    </div>
</div>

<div id="footer">
    <?= view('partials/copywright.tpl.php'); ?>
</div>
</div>
</body>

</html>
