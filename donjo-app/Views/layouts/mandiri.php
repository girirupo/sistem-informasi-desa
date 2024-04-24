<?= view('layouts/header');?>
<div id="contentwrapper">
    <div id="contentcolumn">
        <div class="innertube">
            <?php
							if($m==1)
								echo view('partials/mandiri');
							elseif($m==2)
								echo view('partials/layanan');
							else
								echo view('partials/lapor');
						?>
        </div>
    </div>
</div>
<div id="rightcolumn">
    <div class="innertube">
        <?= view('partials/side.right.php');?>
    </div>
</div>
<div id="footer">
    <?= view('partials/copywright.tpl.php');?>
</div>
</div>
</body>

</html>
