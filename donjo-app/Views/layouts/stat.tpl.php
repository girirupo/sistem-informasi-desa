<?= view('layouts/header.php');?>
<div id="contentwrapper">
    <div id="contentcolumn">
        <div class="innertube">
            <?php
						if($tipe == 2){
							if($tipex==1){
								view('partials/statistik_sos.php');
							}elseif($tipex==3){
								view('partials/statistik_ras.php');
							}else{
								view('partials/statistik_jam.php');
							}
						}elseif($tipe == 3){
							view('partials/wilayah.php');
						}else{
							view('partials/statistik.php');
						}
						?>
        </div>
    </div>
</div>
<div id="rightcolumn">
    <div class="innertube">
        <?= view('partials/side.right.stat.php');?>
    </div>
</div>

<div id="footer">
    <?php
				view('partials/copywright.tpl.php');
				?>
</div>
</div>
</body>

</html>
