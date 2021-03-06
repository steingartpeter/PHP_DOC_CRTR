<?php
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf8">
	
	<!-- BOOTSTRAP CSS REMOTE LINK-->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" 
		integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
	<link rel="stylesheet" href="/PHP_DOC_CRTR/css/basic.css">
		
	<!-- BOOTSTRAP JavaScript REMOTE LINK -->
	<script src="/PHP_DOC_CRTR/js/jquery311min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" 
		integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
	<script src="/PHP_DOC_CRTR/js/localMain.js"></script>
</head>

<body>
	<!-- HEADER DIV -->
	<div class="container" id="pgHeader">
		<nav class="navbar navbar-inverse">
			<div class="container-fluid">
				<div class="navbar-header">
					<a class="navbar-brand" href="#">PHP Dokumnetálás</a>
				</div>
				<ul class="nav navbar-nav">
					<li><a href="/PHP_DOC_CRTR/index.php"><span class="glyphicon glyphicon-th-large"></span> Kezdőlap </a></li>
					<li class="active"><a href="#"><span class="glyphicon glyphicon-info-sign"></span> Leírás</a></li>
				</ul>
				<ul class="nav navbar-nav navbar-right">
					<li><a href="#"><span class="glyphicon glyphicon-user"></span> Regisztrálás</a></li>
					<li><a href="#"><span class="glyphicon glyphicon-log-in"></span> Belépés </a></li>
			    </ul>
			</div>
		</nav>
	</div>
	<!-- MAIN CONTENT DIV -->
	<div class="container" id="pgContent">
		<div class="row">
			<div class="col-lg-6">
				<h3>A lap funkciója</h3>
				<p>
					Ez a lap, egy <b>forrásfileból</b> generál HTML dokumentációt.<br>
					Sok esetben ez a dokumentáció még nem kész, képekkel, ábrákkal ki lehet
					egészíteni, hogy többet mutasson.
				</p>
			</div>
			<div class="col-lg-6">
				<img class="baseImg" src="/PHP_DOC_CRTR/img/tstPic01.jpg"/>
			</div>
		</div>
	</div>
	<!-- FOOTER DIV -->
	<div class="container" id="pgFooter">
	</div>
</body>
</html>