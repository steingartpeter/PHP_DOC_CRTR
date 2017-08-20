<?php
//<M>
//×-
//@-FILENÉV   : PHP_DOC_CRTR - index.php-@
//@-SZERZŐ    : AX07057-@
//@-LÉTREHOZVA:  2016 okt. 30-@
//@-FÜGGŐSÉGEK:
//×-
// @-- DOC_GNRTR.php-@
//-@
//-×
//-@
//@-LEÍRÁS    :
// Ez a PHP kód azt a feladatot látja el, hogy a bemeneti fileban megadott fileból egy HTML dokumentációs
// file-t készít.
//@-MÓDOSÍTÁSOK :
//×-
// @-- GIT integráció Eclipse-be 87O6NEL 2017-08-20 -@
//-×
//-×
//</M>	
    include_once($_SERVER['DOCUMENT_ROOT']."/PHP_DOC_CRTR/php/DOC_GNRTR.php");
    $content = "";
    if(isset($_POST['infile']) && $_POST['infile']<>''){
        echo "\$_POST[]:<br><pre>";
        print_r($_POST);
        echo "</pre><hr>";
        $doccer = new PHP_DOCCER();
        $doccer->setInfile($_POST['infile']);
        if(isset($_POST['outfile']) && $_POST['outfile']<>''){
            $doccer->setOutFile($_POST['outfile']);
        }
        $doccer->createDocs();
        $content = file_get_contents($_SERVER['DOCUMENT_ROOT']."/PHP_DOC_CRTR/outputfiles/out.txt");
    }else{
        //<DEBUG>
        // Itt meggyőződhetünk arról, hogy üres volt a POST tömb.<br>
        // <code>
        // echo "NEM VOLT SUBMIT - \$_POST[]:<br><pre>";
        // print_r($_POST);
        // echo "</pre><hr>";
        // </code>
        //</DEBUG>
    }
    
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
					<li class="active"><a href="#"><span class="glyphicon glyphicon-th-large"></span> Kezdőlap </a></li>
					<li><a href="html/expl.php"><span class="glyphicon glyphicon-info-sign"></span> Leírás</a></li>
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
			<div class="col-lg-12">
				<form action="#" method="post">
					<div class="from-group">
						<h2>Forrás, és célfileok megadása</h2>
					</div>
					<div class="from-group">
						<label for="infile">Forrásfile:</label>
						<input type="file" id="infile" name="infile">
					</div>
					<div class="from-group">
						<label for="outfile">Kimeneti file:</label>
						<input type="file" id="outfile" name="outfile">
					</div>
					<div class="from-group">
						<button type="submit" id="btnSbmt" class="btn btn-info">Dokumetáció elkészítése</button>
					</div>
				</form>
			</div>
		</div>
		<div class="row">
			<div class="col-lg-12">
			<?php
				if($content !== ""){
					echo $content;
				} 
			?>
			</div>
		</div>
	</div>
	<!-- FOOTER DIV -->
	<div class="container" id="pgFooter">
		<div class="row">
		<div class="col-lg-12"><p> Készítette:AX07057 &copy; </p></div>
		</div>
	</div>
</body>
</html>
