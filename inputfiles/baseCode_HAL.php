<?php
//<M>
//×-
//@-MODULÉV   : HAL_WEB_DEV - baseCode_HAL.php-@
//@-SZERZŐ    : AX07057-@
//@-LÉTREHOZVA: 2015-05-26-@
//@-FÜGGŐSÉGEK:
//×-
// @-- EKAER_CODE_WH.php-@
// @-- mySQLHandler.php-@
// @-- vbScriptGenerator.php-@
// @-- MSSQLHandler.php-@
//-×
//-@
//@-LEÍRÁS    :
//Ez az osztály fog terveim szerint minden webpage-generálással kapcsolatos feladatot megoldani.
//Ezen feladatok közé tartozik például az üres lapok generálása, amibe csak az aktuális tartalmakat kell
//majd beilleszteni.
//Maga az implementáció egy class-ban törénik. A class neve: HAL_WEB_Base.
// -@
//@-MÓDOSÍTÁSOK :
//×-
// @-- 2016-07-21
//×-
// @-- public function getWrongShotsUSR($usr) hozzáadása -@
//-×
// -@
// @-- ... -@
//-×
//-×
//</M>
	
	//<DEBUG>
	//<code>
	//define("EKR_FILE_PATH","D:/EKAER/ADAT/AKT_EKAER01.txt");
	//define("EKR_CHROME_UPLOAD_PATH","I:/_Exchange_Insite/EKAER/PHP/TSTXML/");
	//</code>
	//</DEBUG>
	ob_start();
	define("EKR_CHROME_UPLOAD_PATH","I:/_Exchange_Insite/EKAER/XMLek/");
	define("EKR_INV_ITMS_FILE_PATH",'I:\\\Informatika\\\ProgRAM\\\Scriptek\\\Adatfileok\\\AKT_INVC_ITMS.txt');
	//define("FTP_SERVER_IP","10.232.1.49");
	//define("FTP_SERVER_USER_TST","remoteTester");
	//define("FTP_SERVER_PWD_TST","an003722");


	define("FTP_SERVER_PROXY","hal-svip.mc2.renault.fr");
	define("FTP_SERVER_PRXY_USER","ax07057");
	define("FTP_SERVER_PRXY_PWD","qd770499");
	
	
	define("FTP_LOC_TST_FILE",$_SERVER['DOCUMENT_ROOT']. "/HAL_WEB_DEV/adatFileok/FTP/FTP_TESTER01.txt");
	//define("FTP_SERVER_IP2","ftp://ftp.swfwmd.state.fl.us/pub");
	define("FTP_SERVER_IP2","ftp://siro.hu");
	define("FTP_SERVER_USER_TST2","FTPTemp");
	define("FTP_SERVER_PWD_TST2","Abc123456");
	
	//<DEBUG>
	//require_once($_SERVER['DOCUMENT_ROOT'] . 'directory/directory/file');	
	//</DEBUG>
	
	include_once 'EKAER_CODE_WH.php';
	include_once 'mySQLHandler.php';
	include_once 'vbScriptGenerator.php';
	include_once 'MSSQLHandler.php';
	

class HAL_WEB_Base {
//<CLS>
//Az osztály neve: HAL_WEB_Base.
//Alapvetően ez az osztály végez minden PHP alapú site generálást.
//Emellett törekedtem arra is, hogy ez legyen az összekötő kapocs a többi
//egyedi funkciókat megvalósító osztállya, például EKAER_HANDLER, mySQLHandler, stb...
//Ezzel a file-al kapcsolatban érdemes megjegyezni, hogy mivel otthon nincs SQL server, 
//ezért időnként a MSSQL server kezelő include ki vagy be van kapcsolva, amikor nem kellne.
//Itt munkában ez őleg havonta egyszer a jövedéki lekérésekor okoz gondot.
//Amennyiben:
//"Fatal error call to a member function serpaTeszt on string" hibaüzenetet kapunk,
//valószínüleg mind a 72. sor : private $MsDBHndlr = "";, mind a 89. sor:$this->MsDBHndlr = new MSSQLHandler();
//ki van kommentelve, így nincs MSSQLHlr objektum!
//</CLS>

	//<nn>
	//Az osztály privát tagváltozói, melyek maguk is mind egy-egy osztály példányai:
	//×-
	// @-- $ekrHndlr : EKAER funkciókhoz. -@
	// @-- $dbHndlr : a MySQL funkciókhoz.-@
	// @-- $vbScrptHndlr : a VB scriptek kezeléséhez.-@
	// @-- $MsDBHndlr : a MS SQL server kezeléséhez.-@
	//-×
	//</nn>
	private $ekrHndlr = "";
	private $dbHndlr = "";
	private $vbScrptHndlr = "";
	private $MsDBHndlr = "";

	 
	public function __construct(){
	//<SF>
	//A szokásos konstruktor, ami éretéket ad az osztályváltozóknak, példányosítva ezzel
	//egyet egyet az alábbi osztályokból:
	//×-
	// @--EKAER_HANDLER-@
	// @--mySQLHandler-@
	// @--vbScriptGenerator-@
	// @--MSSQLHandler-@
	//-×
	//</SF>
	 	$this->ekrHndlr = new EKAER_HANDLER();
	 	$this->dbHndlr = new mySQLHandler();
	 	$this->vbScrptHndlr = new vbScriptGenerator();
	 	$this->MsDBHndlr = new MSSQLHandler();
	 }

	public function __destruct(){
	//<SF>
	//A destruktor...
	//</SF>
	 	$this->dbHndlr->closeConnection();
	}
	 
	public static function testFunc() {
	//<SF>
	//Ez csak egy tesztfüggvény.
	//@return string
	//</SF>
		$msg = "Ez itt egy teszt szöveg, amit a egy script fileban";
		$msg = $msg . " elkülöniített script generál. Ezt a szöveget létre lehet hozni akár ";
		$msg = $msg . "valamilyen beolvasott adatok alapján is, például egy  .csv file alapján.";
		return $msg;
	}
	
	public function HALAlapLapTeteje($Kepurl=""){
	//<SF>
	//Ez a függvény generálja le a standard lap felső részét, menüvel, címmel,  stb
	//×-
	// @-- param string $mTtl = a lap főcíme -@
	// @-- param array $navArray = a menüelemek tömbje  -@
	// @-- param string $mainThumb = ez a fő thumbnail linkje -@
	// @-- return string -@
	//-×
	//</SF>
		$LapTorzs = '<!DOCTYPE html>
		<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<link rel="stylesheet" type="text/css" href="/HAL_WEB_DEV/Style/alap.css">
		<link rel="stylesheet" type="text/css" href="/HAL_WEB_DEV/Script/jquery-ui-1.11.3.custom/jquery-ui.min.css">
		<link rel="stylesheet" type="text/css" href="/HAL_WEB_DEV/Script/jquery-ui-1.11.3.custom/jquery-ui.structure.min.css">
		<link rel="stylesheet" type="text/css" href="/HAL_WEB_DEV/Script/jquery-ui-1.11.3.custom/jquery-ui.theme.min.css">
		'.
		//<DEBUG>
		//<link rel="stylesheet" type="text/css" href="/HAL_WEB_DEV/Script/javascript/bootstrap/css/bootstrap.css">
		//<link rel="stylesheet" type="text/css" href="/HAL_WEB_DEV/Script/javascript/bootstrap/css/bootstrap-theme.css">
		//</DEBUG>
		'<link rel="stylesheet" type="text/css" href="//cdnjs.cloudflare.com/ajax/libs/font-awesome/4.3.0/css/font-awesome.min.css">	
		';
		
		//<nn>
		//Ha a jqplot libraryval akarunk chartokat rajzoltatni, kell CSS filje!
		//Viszont csak a saját stilusok elé, de a más libraryk után tehetjük be.
		//</nn>
		if(strpos($_SERVER['REQUEST_URI'], "_jqp")){
			///
			$LapTorzs .='<link rel="stylesheet" type="text/css" 
					href="/HAL_WEB_DEV/Script/javascript/chartgen/jqplot/jquery.jqplot.min.css">';
		}
		
		
		$LapTorzs .='<link rel="stylesheet" type="text/css" href="/HAL_WEB_DEV/Style/jquiOverWrite.css">
		<link rel="stylesheet" type="text/css" href="/HAL_WEB_DEV/Style/SpryMenuBarHorizontal.css" rel="stylesheet" type="text/css" />';
		
		$LapTorzs .='<script src="/HAL_WEB_DEV/Script/d3.min.js" type="text/javascript" charset="UTF-8"></script>
		<script src="/HAL_WEB_DEV/Script/jquery-1.11.1.min.js" type="text/javascript" charset="UTF-8"></script>
		<script src="/HAL_WEB_DEV/Script/SpryMenuBar.js" type="text/javascript" charset="UTF-8"></script>
		<script src="/HAL_WEB_DEV/Script/jquery-ui-1.11.3.custom/jquery-ui.min.js" type="text/javascript" charset="UTF-8"></script>
		';
				
		//<DEBUG>
		//<script src="/HAL_WEB_DEV/Script/javascript/bootstrap/bootstrap.min.js" type="text/javascript"></script>
		//<script src="/HAL_WEB_DEV/Script/javascript/angular/angular.js" type="text/javascript"></script>
		//</DEBUG>
		
				
		$LapTorzs .='<script src="/HAL_WEB_DEV/Script/site.js" type="text/javascript" charset="UTF-8"></script>
		<script src="/HAL_WEB_DEV/Script/javascript/chartgen/chartgenerator.js" type="text/javascript" charset="UTF-8"></script>';
		
		//<nn>
		//Hogy ne kelljen mindig minden scriptfilet minden sitre betölteni, az url alpján adunk
		//hozzá speciális script tag-eket.
		//</nn>
		if(strpos($_SERVER['REQUEST_URI'], "munkaruha")){
			$LapTorzs .= '<script src="/HAL_WEB_DEV/Script/javascript/siteSpec/munkaruhaJS.js" 
				type="text/javascript" charset="UTF-8"></script>';
		}
		
		if(strpos($_SERVER['REQUEST_URI'], "_jqp")){
			$LapTorzs .= '<script src="/HAL_WEB_DEV/Script/javascript/chartgen/jqplot/jquery.jqplot.min.js"
				type="text/javascript" charset="UTF-8"></script>
			<script src="/HAL_WEB_DEV/Script/javascript/chartgen/jqplot/plugins/jqplot.barRenderer.min.js"
				type="text/javascript" charset="UTF-8"></script>
			<script src="/HAL_WEB_DEV/Script/javascript/chartgen/jqplot/plugins/jqplot.categoryAxisRenderer.min.js"
				type="text/javascript" charset="UTF-8"></script>
			<script src="/HAL_WEB_DEV/Script/javascript/chartgen/jqplot/plugins/jqplot.canvasTextRenderer.min.js"
				type="text/javascript" charset="UTF-8"></script>
			<script src="/HAL_WEB_DEV/Script/javascript/chartgen/jqplot/plugins/jqplot.canvasAxisLabelRenderer.min.js"
				type="text/javascript" charset="UTF-8"></script>
			<script src="/HAL_WEB_DEV/Script/javascript/chartgen/jqplot/plugins/jqplot.canvasAxisTickRenderer.min.js"
				type="text/javascript" charset="UTF-8"></script>
			<script src="/HAL_WEB_DEV/Script/javascript/chartgen/jqplot/plugins/jqplot.dateAxisRenderer.min.js"
				type="text/javascript" charset="UTF-8"></script>
			<script src="/HAL_WEB_DEV/Script/javascript/chartgen/jqplot/plugins/jqplot.highlighter.min.js"
				type="text/javascript" charset="UTF-8"></script>
			<script src="/HAL_WEB_DEV/Script/javascript/chartgen/jqplot/plugins/jqplot.cursor.min.js"
				type="text/javascript" charset="UTF-8"></script>
			<script src="/HAL_WEB_DEV/Script/javascript/chartgen/jqplot/plugins/jqplot.pointLabels.min.js"
				type="text/javascript" charset="UTF-8"></script>
			<script src="/HAL_WEB_DEV/Script/javascript/jqPlotManager.js"
				type="text/javascript" charset="UTF-8"></script>';
		}
		
		$LapTorzs .= '<title>HAL Kft - Beklikk</title>
		</head>
		
		<body class="twoColHybLtHdr">		
		<div id="container">
		  <div id="header">
		    <h1><a href="/HAL_WEB_DEV/index.php"><img src="/HAL_WEB_DEV/pix/fejlec_main2.jpg" width="965" 
				height="93" border="0" longdesc="/HAL_WEB_DEV/pix/fejlec_main.jpg" /></a></h1>
		</div>
		  <div id="sidebar1">
		    <ul id="MenuBar1" class="MenuBarHorizontal">
		      <li><a class="MenuBarItemSubmenu" href="/HAL_WEB_DEV/subpages/raktar.php">Raktár</a>
		        <ul>
					<li><a href="/HAL_WEB_DEV/subpages/RAKTAR/szervHir.php">Szervezeti hírek</a></li>
					<li><a href="/HAL_WEB_DEV/subpages/RAKTAR/aktEsem.php">Aktuális események</a></li>
					<li><a href="/HAL_WEB_DEV/subpages/RAKTAR/eredm.php">Eredmények</a></li>
					<li><a class="MenuBarItemSubmenu" href="#">Aktuális információk</a>
						<ul>
							<li><a href="/HAL_WEB_DEV/subpages/RAKTAR/NapiAdatok.php">Napi adatok</a></li>
		         			<li><a href="/HAL_WEB_DEV/subpages/RAKTAR/kiszallitas.php">Kiszállítás</a></li>
							<li><a href="/HAL_WEB_DEV/subpages/RAKTAR/EKAER/tchReadInbCFTFile.php">Beérkezés részletei</a></li>
						</ul>
					</li>'.
				  
				  '
				  <li><a class="MenuBarItemSubmenu" href="/HAL_WEB_DEV/index.php">MINDEN AMI EKAER</a>
			            <ul>
							<li><a href="/HAL_WEB_DEV/subpages/RAKTAR/EKAER/NapiEKRInfo.php">EKAER NAPI ADATOK</a></li>
							<li><a href="/HAL_WEB_DEV/subpages/RAKTAR/EKAER/InbLines.php">Beérkezési adatok feltöltése</a></li>
							<li><a href="/HAL_WEB_DEV/subpages/RAKTAR/EKAER/EKAER_BASE.php">EKAER ALAPLAP</a></li>
							<li><a href="/HAL_WEB_DEV/subpages/RAKTAR/EKAER/tchReadInbCFTFile.php">CFT fileok</a></li>
				  			<li><a href="/HAL_WEB_DEV/subpages/RAKTAR/EKAER/EKAERNR_DETAILS.php">Részletek EKAER számra</a></li>
							<li><a href="/HAL_WEB_DEV/subpages/RAKTAR/EKAER/EKAERTEST.php">TESZT_LAP</a></li>
		            	</ul>
				  </li>
				  <li><a class="MenuBarItemSubmenu" href="/HAL_WEB_DEV/subpages/RAKTAR/LOVOLDOZES/lovldozes.php">LÖVÉS</a>
			            <ul>
							<li><a href="/HAL_WEB_DEV/mobLov/shot.php">Mobil eszköz belövés</a></li>
							<li><a href="/HAL_WEB_DEV/mobLov/sendShotData.php">Desktopos belövés</a></li>
							<li><a href="/HAL_WEB_DEV/mobLov/TOsWOShot.php">Be nem lőtt TOk</a></li>
							<li><a href="/HAL_WEB_DEV/mobLov/lovesekmegnezese.php">Lövések listája</a></li>
							<li><a href="/HAL_WEB_DEV/mobLov/checkShotForTO.php">Lövések listája TOkhoz</a></li>
				  			<li><a href="/HAL_WEB_DEV/mobLov/chk_ShtsFroDate.php">Lövések száma dátumra/gyüjtőre</a></li>
		            	</ul>					
					</li>
				  	<li><a class="MenuBarItemSubmenu" href="/HAL_WEB_DEV/subpages/RAKTAR/tstPage.php">TESZTEK</a>
			            <ul>
				  			<li><a href="/HAL_WEB_DEV/subpages/RAKTAR/createHU.php">HU létrehozás</a></li>
				  			<li><a href="/HAL_WEB_DEV/subpages/RAKTAR/volCharts_jqp.php">Kimenő térfogatok - JQPLOT</a></li>
							<li><a href="/HAL_WEB_DEV/subpages/RAKTAR/SHP_LBL/SHPLBL.php">Shippng label generátor - PHP</a></li>
							<li><a href="/HAL_WEB_DEV/subpages/RAKTAR/SHP_LBL/SHPLBL.html">Shippng label - HTML</a></li>
							<li><a href="/HAL_WEB_DEV/subpages/RAKTAR/KPI/KPI_BASE_jqp.php">KPI</a></li>
				  			<li><a href="/HAL_WEB_DEV/subpages/RAKTAR/EKAER/EKAER_LOGS.php">EKAER INB LOG</a></li>
					  		<li><a href="/HAL_WEB_DEV/subpages/RAKTAR/EKAER/PATENT_PORTA.php">Patent belépések</a></li>
		            	</ul>					
					</li>
		        </ul>
		      </li>
		      <li><a class="MenuBarItemSubmenu" href="/HAL_WEB_DEV/subpages/beszerzes.php">Beszerzés</a>
						<ul>
							<li><a href="/HAL_WEB_DEV/subpages/ICS/szervHir.php">Szervezeti hírek</a></li>
							<li><a href="/HAL_WEB_DEV/subpages/ICS/aktEsem.php">Aktuális események</a></li>
							<li><a href="/HAL_WEB_DEV/subpages/ICS/eredm.php">Eredmények</a></li>
						</ul>
					</li>
		      <li><a class="MenuBarItemSubmenu" href="/HAL_WEB_DEV/subpages/vevoSzolgalat.php">DCT</a>
		        <ul>
		          <li><a class="MenuBarItemSubmenu" href="/HAL_WEB_DEV/subpages/DCT/szervHir.php">Szervezeti hírek</a>
		            <ul>
		              <li><a href="#">DCT alLink 1.1</a></li>
		              <li><a href="#">DCT alLink 1.2</a></li>
		            </ul>
		          </li>
		          <li><a href="/HAL_WEB_DEV/subpages/DCT/aktEsem.php">Aktuális események</a></li>
		          <li><a href="/HAL_WEB_DEV/subpages/DCT/eredm.php">Eredmények</a></li>
		        </ul>
		      </li>
		      <li><a class="MenuBarItemSubmenu" href="/HAL_WEB_DEV/subpages/FIN/FINANCE_BASE.php">Könyvelés</a>
		        <ul>
		          <li><a href="/HAL_WEB_DEV/subpages/FIN/szervHir.php">Szervezeti hírek</a></li>
		          <li><a href="/HAL_WEB_DEV/subpages/FIN/aktEsem.php">Aktuális események</a></li>
		          <li><a href="/HAL_WEB_DEV/subpages/FIN/eredm.php">Eredmények</a></li>
				  <li><a href="/HAL_WEB_DEV/subpages/FIN/haviJovedekiLista.php">Jövedéki lista SQL szerverről</a></li>
				  <li><a href="/HAL_WEB_DEV/subpages/FIN/ConvertNissanJovedekiXML.php">Nissan jövedéki XML beolvasás</a></li>
		        </ul>
		      </li>
		      <li><a class="MenuBarItemSubmenu" href="/HAL_WEB_DEV/subpages/management.php">Management</a>
						<ul>
							<li><a href="/HAL_WEB_DEV/subpages/MNGMT/PreziParalax.html">Parallax prezi</a></li>
							<li><a href="/HAL_WEB_DEV/subpages/MNGMT/hetiPrezi01.php">Statikus PHP prezi</a></li>
							<li><a href="/HAL_WEB_DEV/subpages/MNGMT/hetiPrezi01_jqp.php">PHP prezi - JQPLOT</a></li>
							<li><a href="/HAL_WEB_DEV/subpages/MNGMT/hianyzoTimeSheetek.php">Hiányzó timesheetek</a></li>
							<li><a href="/HAL_WEB_DEV/subpages/MNGMT/munkaruha01.php">Munkaruha</a></li>
							<li><a href="/HAL_WEB_DEV/subpages/MNGMT/munkaruha02.php">Korábbi munkaruha felvételek</a></li>
						</ul>
					</li>
					<li><a class="MenuBarItemSubmenu" href="/HAL_WEB_DEV/subpages/helpdesk.php">Helpdesk</a></li>
		    </ul>
		    <p>&nbsp;</p>
		  <!-- end #sidebar1 --></div>
		  <div id="mainContent">';
		$LapTorzs .= $this->kepgenerator($Kepurl); 
		return $LapTorzs;
		
	}//END_OF HAL_ALAP_LAP_TETEJE

	public function halAlapOldalAlja(){
	//<SF>
	//Ez a függvény generálja le a standard weboldalunk alsó részét.
	//@return string
	//</SF>
		$LA='<!-- end #mainContent --></div>
			<!-- This clearing element should immediately follow the #mainContent div in order to force the #container div to contain all child floats -->
			<br class="clearfloat" />
			<div id="footer">
		    <p>HAL Kft @2014</p>
		  <!-- end #footer --></div>
		<!-- end #container --></div>
		<script type="text/javascript">
		<!--
		var MenuBar1 = new Spry.Widget.MenuBar("MenuBar1", {imgDown:"../SpryAssets/SpryMenuBarDownHover.gif", imgRight:"../SpryAssets/SpryMenuBarRightHover.gif"});
		//-->
		</script>
		</body>
		</html>';
		return $LA;
	}//END_OF_HAL_ALAP_LAP_ALJA
	
	private function kepgenerator($kepurl) {
	//<SF>
	//Nyitokep beszurasa
	//</SF>
		//<nn>
		//létrehozzuk a html szöveget
		//</nn>
		$KEP='<img src="'. $kepurl. '">';
		
		//<nn>
		//visszaadjuk a kért stringet
		//</nn>
		return $KEP;
	}

	public function EKRFileBeolvasas(){
	//<SF>
	//Ez a függvény egy előre megadott elérésü file-ból olvassa be az adott napi
	//kumulált EKAER adatokat tájékoztatásképpen!
	//</SF>
		$fpath = EKR_FILE_PATH;
		$retString = "<table class=\"EKRDlyData\">";
		$retString .= "<tr><th>Dátum</th><th>Route</th><th>Dealer</th>";
		$retString .= "<th>Plant</th><th>VTSZ</th><th>Érték(HUF)</th>";
		$retString .= "<th>Súly(kg)</th></tr>";
		$dataArray = "";
		$rdBfr = "";
		$file = fopen($fpath, "r") or die("<p class=\"ERRMsg\">Sikertelen filenyitási kísérlet!(".EKR_FILE_PATH.")</p>");
		while(($rdBfr=fgets($file,4096)) !== false){
			if(substr($rdBfr,0,5) === "|*** "){
				$dataArray = explode("|", $rdBfr);
				$ertekStr = $this->vesszotlenites($dataArray[6]);
				$ertek = $ertekStr + 1;
				$sulyStr = $this->vesszotlenites($dataArray[7]);
				$suly = $sulyStr + 1;
				if ($suly < 2500 && $ertek < 5000000){
					$retString.= "<tr><td>".substr($dataArray[1],6,10)."</td>";
					$retString.= "<td>{$dataArray[2]}</td>";
					$retString.= "<td>{$dataArray[3]}</td>";
					$retString.= "<td>{$dataArray[4]}</td>";
					$retString.= "<td>{$dataArray[5]}</td>";
					$retString.= "<td>". number_format($ertek,0,","," ") ."</td>";
					$retString.= "<td>". number_format($suly,0,","," ") . "</td></tr>";
				}else{
					$retString.= "<tr><td class=\"ovrfld\">".substr($dataArray[1],6,10)."</td>";
					$retString.= "<td class=\"ovrfld\">{$dataArray[2]}</td>";
					$retString.= "<td class=\"ovrfld\">{$dataArray[3]}</td>";
					$retString.= "<td class=\"ovrfld\">{$dataArray[4]}</td>";
					$retString.= "<td class=\"ovrfld\">{$dataArray[5]}</td>";
					$retString.= "<td class=\"ovrfld\">". number_format($ertek,0,","," ") ."</td>";
					$retString.= "<td class=\"ovrfld\">". number_format($suly,0,","," ") . "</td></tr>";					
				}
				
			}
			
		}
		$retString .= "</table>";
		fclose($file);
		return $retString;
	}
	
	private function vesszotlenites($szvg){
	//<SF>
	//Ez a rövid ki függvény a beadott stringből szedi ki a vesszőket, gyakorlatilag szükségtelen a str_replace() miatt.
	//@param unknown $szvg -> a vesszőtlenitendő szöveg
	//@return mixed -> a vesszőmentes eredmény
	//</SF>
		return str_replace(",", "", $szvg);
	}

	public function EKRBeolvasasFilebol(){
	//<SF>
	//Ez még nem működik..
	//</SF>
		return $this->dbHndlr->EKRBeolvasasFilebol();
	}

	public function ekrInbFileBeolv(){
	//<SF>
	//2015-06-18
	//MÓDOSÍTÁSOK:
	//×-
	// @--mostantól benne van a második függvényhívás, ahol a beolvasott adatokat elmentjük egy JSON fileba.
	//Ez a függvény olvassa be egyelőre a NISSAN-os txt fileokat -@
	//@- return unknown -@
	//-×
	//</SF>
		$retString = "";
		//<nn>
		//Az adatbázisba töltjük a file tartalmát
		//</nn>
		$retString .= $this->ekrHndlr->EKRInbFileFeltoltes();
		//<nn>
		//Az adatbázisba lekérdezését file-ba töltjük :).
		//</nn>
		$this->ekrHndlr->EKRInbFileLetoltes();
		return $retString;
	}	
	
	public function EKRListToTable(){
	//<SF>
	//Ez a függvény lekér egy listát az utóbbi egy hétben létrehozott
	//és a helyi adatbázisban tárolt EKAER számokról.
	//Ez a függvény csak meghívja az EKAER_CODE_WH ban elhelyezetett, ami pedig
	//a mySQLHandler-ből fog egyet hívni. Itt egy lekérdezés fut le, amiben a lekérdezés
	//eredménye egy tömbbe kerül, ez kerül vissz ide.
	//Ha a tömb első eleme [0] == OK, akkor a tömbböt kiírjuk táblázat formájában, ha ez NOK, akkor
	//csak egy plusz elem van, ami a hibaüzenetet tartalmazza.
	//</SF>
		$htmlContent = "";
		$retArr="";
		$retArr = $this->ekrHndlr->localEKAERListForOneWeek();
		if($retArr[0] == "OK"){
			$fldNmaes=["EKAER szám","Feladás napja","Fekrakás napja","Lerakés napja","Feladta","Vontató rsz.",
			"Pótkocsi rsz","Utloljára módosítva","Felrakés helye", "Súly(KG)", "Érték(HUF)", "Szállítmányozó"];
			$htmlContent .= "<table class=\"EKRSzamok\">";
			$htmlContent .= "<tr>";
			for($i=0;$i<count($fldNmaes);$i++){
				$htmlContent .= "<th>" . $fldNmaes[$i] . "</th>";
			}
			$htmlContent .= "<th colspan=\"3\">Művelet</th>";
			$htmlContent .= "</tr>";
			for($i=1;$i<count($retArr);$i++){
				$htmlContent .= "<tr>";
				$htmlContent .= "<td>" . $retArr[$i]["ekrnumber"] . "</td>";
				$htmlContent .= "<td>" . $retArr[$i]["feladDat"] . "</td>";
				$htmlContent .= "<td>" . $retArr[$i]["felrakDate"] . "</td>";
				$htmlContent .= "<td>" . $retArr[$i]["lerakDate"] . "</td>";
				$htmlContent .= "<td>" . $retArr[$i]["feladUser"] . "</td>";
				$htmlContent .= "<td>" . $retArr[$i]["frsz1"] . "</td>";
				$htmlContent .= "<td>" . $retArr[$i]["frsz2"] . "</td>";
				$htmlContent .= "<td>" . $retArr[$i]["lstModDate"] . "</td>";
				$htmlContent .= "<td>" . $retArr[$i]["felrakHely"] . "</td>";
				//<DEBUG>
				//$htmlContent .= "<td>" . $retArr[$i]["lerakHely"] . "</td>";
				//</DEBUG>
				$htmlContent .= "<td>" . $retArr[$i]["totsuly"] . "</td>";
				$htmlContent .= "<td>" . $retArr[$i]["totErtek"] . "</td>";
				$htmlContent .= "<td>" . $retArr[$i]["szallCeg"] . "</td>";
				$htmlContent .= "<td><button id=\">" ."btn".$retArr[$i]["ekrnumber"] ."\">Módosítás</button></td>";
				$htmlContent .= "<td>";
				$htmlContent .= "<a class=\"delEKRNrA\" href=\"/HAL_WEB_DEV/subpages/RAKTAR/EKAER/deleteEkareNr.php?ekrNr="
						. $retArr[$i]["ekrnumber"]. "\">Törlés</a></td>";
				$htmlContent .= "<td><button id=\">" ."btn".$retArr[$i]["ekrnumber"] ."\">Lezárás</button></td>";
				$htmlContent .= "</tr>";
			}
			$htmlContent .= "</table>";
		}else{
			$htmlContent = $retArr[1];
		}
		return $htmlContent;
	}

	public function getOneDayEarlierInvoices($datStr){
	//<SF>
	//2015-08-11
	//×-
	// @-- Ez a függvény a MySQLHandlerből lehoz egy listát az tblinbinvoices táblából a paraméterként kapott
	//dátumhoz tartozó számlákról. -@
	// @-- param unknown $datStr -@
	//-×
	//</SF>
		$retString = "";
		$retStruct = $this->dbHndlr->getInvoicesForDate($datStr);
		//<DEBUG>
		//echo "<p>A \$retStruct typusa: <br/>" . gettype($retStruct) . "</p>";
		//</DEBUG>
		if (is_array($retStruct)){
			//<DEBUG>
			//echo "<p>getOneDayEarlierInvoices(\$datStr) array volt!</p>";
			//</DEBUG>
			$retString .= "<table class=\"tblszla\">";
			$retString .= "<thead><tr>";
			$retString .= "<th>Számlaszám</th>";//invcid
			$retString .= "<th>Számlázási dátum</th>";//invdat
			$retString .= "<th>Von.rendszám</th>";//pltnr
			$retString .= "<th>Brt.súly</th>";//brtwght
			$retString .= "<th>Feladó(tipp)</th>";//felado
			$retString .= "<th>Funkció</th>";
			$retString .= "</tr></thead>";
			$retString .= "<tbody>";
			for($i=0;$i<count($retStruct);$i++){

				$retString .= "<tr>";
				$retString .= "<td>" . $retStruct[$i]['invcid'] . "</td>";
				$retString .= "<td>" . $retStruct[$i]['invdat'] . "</td>";
				$retString .= "<td>" . $retStruct[$i]['pltnr'] . "</td>";
				$retString .= "<td>" . $retStruct[$i]['brtwght'] . "</td>";
				$retString .= "<td>" . $retStruct[$i]['felado'] . "</td>";
				$retString .= "<td>" . '<a class="btn" href="/HAL_WEB_DEV/subpages/RAKTAR/EKAER/chngPltNrForInvoice.php?invc='
					. $retStruct[$i]['invcid']. '&dat=' . $retStruct[$i]['invdat'] . '">[PÓTKOCSI !!!]Rendszám módosítása</a>' . "</td>";
				$retString .= "</tr>";
			}
			$retString .= "</tbody></table>";
			return $retString;
		}else{
			//<DEBUG>
			//echo "<p>getOneDayEarlierInvoices(\$datStr) NEM(!!!) array volt!</p>";
			//</DEBUG>
			return $retStuct;
		}
	}//END OF getOneDayEarlierInvoices($datStr)
	
	public function createHUScript($pr,$dlr,$tp){
	//<SF>
	//Ez a függvény egy vbscript file-t hoz léte, ami majd lefuttatva egy HU-t készít a SAPban.
	//</SF>
		$this->vbScrptHndlr->generateHUCreatorScript($pr,$dlr,$tp);
	}
	
	public function getVBScriptGenInst($fn=""){
	//<SF>
	//Sajnos a VBscript generátorunk csak a TEST néven hajalndó menteni a scripteket, 
	//így itt most szerünk belőle egy példányt, amiben a konstruktornál megadjuk, hogy
	//mi legyen a file.
	//×-
	// @-- param string $fn.. -@
	// @-- return vbScriptGenerator -@
	// @-- ... -@
	//-×
	//</SF>
		if($fn = ""){
			$vbg = new vbScriptGenerator();
		}else{
			$vbg = new vbScriptGenerator($fn);
		}
		return $vbg;
	}
	
	///////////////////////////////////////////////////////////////////////////////////////////////////////////////
	/////////////////////////////////////////////EKAER - TRANSZPORT////////////////////////////////////////////////
	///////////////////////////////////////////////////////////////////////////////////////////////////////////////
	
	public function createTableForLastEkaerNrs(){
	//<SF>
	//Ez a függvény generál táblázatot az egy hétnél nem régebbi
	//lokális adatbázisban lévő EKAER számokból.
	//</SF>
		$htmlContent = "";
		$retArr="";
		$retArr = $this->ekrHndlr->localEKAERListForOneWeek();
		if($retArr[0] == "OK"){
			$fldNmaes=["EKAER szám","Feladás napja","Fekrakás napja","Lerakés napja","Feladta","Vontató rsz.",
			"Pótkocsi rsz","Utloljára módosítva","Felrakás helye", "Súly(KG)", "Érték(HUF)", "Szállítmányozó"];
			$htmlContent .= "<table class=\"EKRSzamok\">";
			$htmlContent .= "<tr>";
			for($i=0;$i<count($fldNmaes);$i++){
				$htmlContent .= "<th>" . $fldNmaes[$i] . "</th>";
			}
			$htmlContent .= "<th colspan=\"3\">Művelet</th>";
			$htmlContent .= "</tr>";
			for($i=1;$i<count($retArr);$i++){
				$htmlContent .= "<tr>";
				$htmlContent .= "<td>" . $retArr[$i]["ekrnumber"] . "</td>";
				$htmlContent .= "<td>" . $retArr[$i]["feladDat"] . "</td>";
				$htmlContent .= "<td>" . $retArr[$i]["felrakDate"] . "</td>";
				$htmlContent .= "<td>" . $retArr[$i]["lerakDate"] . "</td>";
				$htmlContent .= "<td>" . $retArr[$i]["feladUser"] . "</td>";
				$htmlContent .= "<td>" . $retArr[$i]["frsz1"] . "</td>";
				$htmlContent .= "<td>" . $retArr[$i]["frsz2"] . "</td>";
				$htmlContent .= "<td>" . $retArr[$i]["lstModDate"] . "</td>";
				$htmlContent .= "<td>" . $retArr[$i]["felrakHely"] . "</td>";
				//<DEBUG>
				//$htmlContent .= "<td>" . $retArr[$i]["lerakHely"] . "</td>";
				//</DEBUG>
				$htmlContent .= "<td>" . $retArr[$i]["totsuly"] . "</td>";
				$htmlContent .= "<td>" . $retArr[$i]["totErtek"] . "</td>";
				$htmlContent .= "<td>" . $retArr[$i]["szallCeg"] . "</td>";
				$htmlContent .= "<td><button id=\"" ."btn".$retArr[$i]["ekrnumber"] ."\" onclick=\"onWork()\">Módosítás</button></td>";
	
				$htmlContent .= "<td>";
				$htmlContent .= "<a class=\"delEKRNrA\" href=\"/HAL_WEB_DEV/subpages/RAKTAR/EKAER/deleteEkareNr.php?ekrNr=". $retArr[$i]["ekrnumber"]. "\">Törlés</a></td>";
				$htmlContent .= "<td><a class=\"delEKRNrA\" href=\"/HAL_WEB_DEV/subpages/RAKTAR/EKAER/finalizeEkareNr.php?ekrNr=". $retArr[$i]["ekrnumber"]. "\">Lezárás</a></td>";
				//<DEBUG>
				//$htmlContent .= "<td><button id=\"" ."btn".$retArr[$i]["ekrnumber"] ."\" onclick=\"finalizeEkrNr()\">Lezárás</button></td>";
				//</DEBUG>
				$htmlContent .= "</tr>";
			}
			$htmlContent .= "</table>";
		}else{
			$htmlContent = $retArr[1];
		}
		return $htmlContent;
	}
	
	public function deleteEKAERNr($nr){
	//<SF>
	//Ez a függvény csak meghívja az EKAER_HANDLER osztál megfelelő függvényét, ami végső soron gondoskodik
	//az adott ekaer szám törléséről.
	//×-
	// @-- param unknown $nr -@
	//-×
	//</SF>
		return $this->ekrHndlr->EKRSzamTorlese($nr);
	}
	
	public function EKRSzamKeresXLSMintara(){
	//<SF>
	//×-
	// @-- 2015-06-15
	// @-- Na akkor most már az 1.8 jegyében a következő módosításokat léptettük életbe
	// @-- betettünk egy gombot a kiinduló formra, hogy "Feltöltés file-ból" -@
	// @-- emellé rakunk egy input eleme is amiben a file elérését tároljuk -@
	// @-- ezt a file elérést be fogjuk ide küldeni, és ebből feltöltjük az item tömböt -@
	// @-- hogy a honlapról kell-e a lsitát használni, vagy fileból tötlünk fel, azt a POST tömb
	//tblSorSzlo eleme alapján döntjük el -@
	//-×
	//Ez a függvény a már létező EXCEL generálást próbálja megvalósítani HTML form alapján.
	//Pillanatnyilag pár tömböt ad vissza, amik tartalmazzák a querykkel megszerzett részadatokat, 
	//amik szükségesek az XML file generálásához.
	//Terv szerint egy standard tömböt fog generálni, ami tartalmazza az xml file legenerálásához
	//szükséges valamennyi adatelemet. Ezek egy része a formról (pl dátum, rendszámok,...) másik része
	//az adatbázisból lekérdezéssel jön majd.
	//Fontos, hogy ezeknek a tömböknek az elemei helyenként szintén tömbök....
	//sőt, olyan tömbök, amikhez az adatbázisból kérdezünk le adatokat, a MySQLHandler() osztály segítségével.
	//</SF>
		$restString = "";
		$dataForXML = array();
		//<nn>
		//Az adatokat egy return stringbe tesszük, így láthatóak a nevek...
		//</nn>
		
		//<DEBUG>
		//foreach($_POST as $k=>$v){
		//	$restString .= $k . " => ";
		//	$restString .= $v . "<br/>";
		//}
		//echo "<p>A POST tömb tartalma a EKRSzamKeresXLSMintara-ban:</p><pre>";
		//print_r($_POST);
		//echo "</pre>";
		//return;
		//</DEBUG>
		
		//<nn>
		//A POST_TÖmb megfelelő adatait betesszük az adattömbbe:
		//</nn>
		$dataForXML['mzgNem'] = substr($_POST['selMozgNem'],0,1);
		$dataForXML['operation'] = $_POST['selOperation'];
		$dataForXML['trdRsn'] = $_POST['trdRsn'];
		if($dataForXML['operation'] == "modify"){
			$dataForXML['tcn'] = $_POST["ekrNr"];
			$dataForXML['dlPlnID'] = $_POST["delPlnID"];
		}
		
		//<nn>
		//Eladó adatainak lekérdezése
		//</nn>
		$restString .= "<hr/><b><u>Eladó: </u></b><br/>";
		$retArr = $this->dbHndlr->getOneDealersData($_POST['selFelado']);
		if($retArr[0]=="OK"){
			$dataForXML['seller'] = $retArr[1];
		}else{
			//<nn>
			//Nem sikerült a lekérdezés.
			//</nn>
			$restString .= $retArr[0];
		}
		
		//<nn>
		//Megrendelő adatainak elkérdezése.
		//</nn>
		$restString .= "<hr/><b><u>Megrendelő: </u></b><br/>";
		if($dataForXML['mzgNem'] == "E"){
			//<nn>
			//Ez azért kell mert kiszállítás esetén ez az elem tartalmazz a ship-to-party mellett a dealer nevét is!
			//Ezt persze nem lehet megtalálni az adatbázisban.
			//</nn>
			if(substr($_POST['selMegrendelo'],0,1) == "1"){
				$retArr = $this->dbHndlr->getOneDealersData(substr($_POST['selMegrendelo'],0,9));
			}else{
				//<nn>
				//No ez meg azért, mert göngyöleg visszassállítás esetén nem ship to party, hanem
				//egy RENFLINS megy be, persze a megnevezéssel együtt...
				//</nn>
				$endPos = strpos($_POST['selMegrendelo'], " "); 
				$retArr = $this->dbHndlr->getOneDealersData(substr($_POST['selMegrendelo'],0,$endPos));
			}
		}else{
			$retArr = $this->dbHndlr->getOneDealersData($_POST['selMegrendelo']);
		}
		if($retArr[0]=="OK"){
			$dataForXML['dstn'] = $retArr[1];
		}else{
			//<nn>
			//A lekérdezés nem sikerült...
			//</nn>
			$restString .= $retArr[0];
		}
		//<nn>
		//Felrakodó hely adatainak lekérdezése:
		//</nn>
		$restString .= "<hr/><b><u>Felrakodás helye: </u></b><br/>";
		$retArr = $this->dbHndlr->getOneDealersData($_POST['felRakLoc']);
		if($retArr[0]=="OK"){
			$dataForXML['loadLoc'] = $retArr[1];
		}else{
			$restString .= $retArr[0];
		}
		//<nn>
		//Le(!)rakodó hely adatainak lekérdezése:
		//</nn>
		$restString .= "<hr/><b><u>Lerakodás helye: </u></b><br/>";
		$retArr = $this->dbHndlr->getOneDealersData($_POST['leRakLoc']);
		if($retArr[0]=="OK"){
			$dataForXML['unlLoadLoc'] = $retArr[1];
		}else{
			$restString .= $retArr[0];
		}
		$dataForXML['trdRsn'] = substr($_POST['trdRsn'],0,1);
		$dataForXML['frsz1'] = $_POST['frsz1'];
		$dataForXML['orsz1'] = $_POST['orsz1'];
		$dataForXML['frsz2'] = $_POST['frsz2'];
		$dataForXML['orsz2'] = $_POST['orsz2'];
		$d=DateTime::createFromFormat("Y-m-d-H",$_POST['felrakDat']."-18");
		$dataForXML['felrakDat'] =$d;
		$d=DateTime::createFromFormat("Y-m-d-H",$_POST['lerakDat']."-16");
		$dataForXML['lerakDat'] = $d;
		$szlo = $_POST['tblSorSzlo'];
		//<DEBUG>
		//echo "<p>A szlo értéke a baseCodeHal-ban: {$szlo}</p>";
		//</DEBUG>
		if((!isset($_POST['upldFile']) || $_POST['upldFile'] == "") && $szlo == 0){
			$dataForXML['items']['Vtsz0'] = $_POST['itemsVtsz0'];
			$dataForXML['items']['Suly0'] = $_POST['itemsSuly0'];
			if(isset($_POST['itemsErtek0'])){
				$dataForXML['items']['Ertek0'] = $_POST['itemsErtek0'];
			}else{
				$dataForXML['items']['Ertek0'] = "";
			}
			
			if(isset($_POST['itemsMegnev0'])){
				$dataForXML['items']['leiras0'] = $_POST['itemsMegnev0'];
				//<DEBUG>
				//echo "<hr/>AZ itemsMegnev1 megvolt:  " &  $_POST['itemsMegnev0'] & "<hr/>";
				//</DEBUG>
			}
		}elseif((!isset($_POST['upldFile']) || $_POST['upldFile'] == "") && $szlo > 0){
			for($i=0;$i<=$szlo; $i++){
				$dataForXML['items']['Vtsz'.$i] = $_POST['itemsVtsz'.$i];
				$dataForXML['items']['Suly'.$i] = $_POST['itemsSuly'.$i];
				//<DEBUG>
				// Cserélve az érték XML node kihagyása miatt:<br>
				//$dataForXML['items']['Ertek'.$i] = $_POST['itemsErtek'.$i];
				//</DEBUG>
				if(isset($_POST['itemsErtek0'])){
					$dataForXML['items']['Ertek0'] = $_POST['itemsErtek0'];
				}else{
					$dataForXML['items']['Ertek0'] = "";
				}
				//<nn>
				//Egy plusz mező hozzáadása, hogy vigyük a leírás mezőt tovább.
				//A kompatibilitás miatt érdemes volt megviszgálni hogy van-e Megnev mező.
				//</nn>
				if(isset($_POST['itemsMegnev' . $i])){
					$dataForXML['items']['leiras'.$i] = $_POST['itemsMegnev'.$i];
				}
			}
		}elseif(isset($_POST['upldFile']) && $_POST['upldFile'] !== ""){
			//<DEBUG>
			//echo "Akkor megjött a fileNev: " . $_POST['upldFile'] . "<br/>";
			//</DEBUG>
			if(strpos($_POST['upldFile'], ":")){
				$ulFlNm = $_POST['upldFile'];
				//<DEBUG>
				//echo "A feldolgozott fileNev: " . $ulFlNm . "<br/>";
				//</DEBUG>
			}else{
				$ulFlNm = EKR_CHROME_UPLOAD_PATH . $_POST['upldFile'];
				//<DEBUG>
				//echo "A feldolgozott fileNev: " . $ulFlNm . "<br/>";
				//</DEBUG>
			}
			$jsnData = file_get_contents($ulFlNm)or die("<p class=\"ERRMsg\">Sajnos a filenyitás {$ulFlNm} sikertelen!</p>");
			//<DEBUG>
			//echo "<hr/>A JSON fileból olvasva: <br/><pre>";
			//print_r($jsnData);
			//echo "</pre></hr>";			
			//</DEBUG>
			$jsnArray = json_decode($jsnData,true);
			$dataForXML['items'] = $jsnArray;
			//<DEBUG>
			//echo "<hr/>A JSON fileból olvasva: <br/><pre>";
			//print_r($dataForXML['items']);
			//echo "</pre></hr>";
			//<DEBUG>
		}
		//<nn>
		//Na ezt nem kell meghívni, mer nincs is megírva!!!!
		//$this->ekr->generateXMLForDataArray($dataForXML);
		//EZ JÓ DEBUGGOLÁSHOZ A BEMENŐ TÖMB ADATAIT SZÉPEN KIÍRJA A LAP TETJÉRE...
		//</nn>
		//<DEBUG>
		//echo "<hr><p>\$dataForXML:<br><pre>";
		//print_r($dataForXML);
		//echo "</pre></p>";
		//return $restString;
		//EZ MOST NEM LESZ MEG? DE NEM IS ÉRDEKES csak megnézem, h leakad-e?
		//</DEBUG>
		$restString = $this->ekrHndlr->generateXML($dataForXML);
		return $restString;
	}
	
	public function ICS_ReadLokFelszFile(){
	//<SF>
	//Ez a függvény beolvassa a lokfelszab filet, és tömbbé alakítja a tartalmát, majd a tömbből
	//egy html table elemeit tartalmazó sztringet generál és visszaadja a hívónak.
	//×-
	// @-- return string = a táblázat HTML kódja. -@
	//-×
	//</SF>
		$fPath = "I:\Informatika\ProgRAM\Scriptek\SAP_DWNLDs\LokFelsz\QUERY_RES.txt";
		//<nn>
		//Egy sornyi adat
		//</nn>
		$a_rec = array();
		//<nn>
		//A header sor elemei
		//</nn>
		$rec_hdr = array();
		//<nn>
		//Az összes adatot tartalmazó tömb...
		//</nn>
		$records = array();
		$tblString = "";
		$fh = fopen($fPath, "r")or die("<p>A file megnyitás sikertelen!</p>");
		for($i=0; $i<7;$i++){
			$sor = fgets($fh,4096);
			$sor = substr($sor,1,strlen($sor)-4);
		}
		$rec_hdr = explode("|",$sor);
		//<nn>
		//Egy sort átugrunk...
		//</nn>
		$sor = fgets($fh,4096);
		while(($sor=fgets($fh)) !== FALSE){
			if(substr($sor,0,4)=="|050"){
				$sor = substr($sor,1,strlen($sor)-4);
				$a_rec = explode("|", $sor);
				$a_rec[18] = utf8_encode($a_rec[18]);
				$a_rec[19] = utf8_encode($a_rec[19]);
				array_push($records, $a_rec);
			}
		}
		//<nn>
		//Nézzük lesz-e ebből XML! :)
		//Hát a siker elmaradt, CSAK asszociatív tömböt lehet így kiírni, különben a 
		//cellaértékek lesznek az XML tag nevek :(
		//$xmlCntn = new SimpleXMLElement('<root/>');
		//array_walk_recursive($records,array($xmlCntn,'addChild'));
		//$xmlCntn->saveXML("I:/Informatika/ProgRAM/Scriptek/egyeb/LOKFELSZ.xml");*/
		//FEJLESZTGETÉS VÉGE
		//</nn>
		$tblString .= "<table>";
		$tblString .=  "<tr>";
		for($i=0; $i<count($rec_hdr);$i++){
			$tblString .=  "<th>";
			$tblString .=  trim($rec_hdr[$i]);
			$tblString .=  "</th>";
		}
		$tblString .=  "</tr>";
		for($i=0; $i<count($records);$i++){
			$tblString .=  "<tr>";
			for($j=0; $j<count($records[$i]);$j++){
				if(trim($records[$i][19]) == "IGEN*" || trim($records[$i][19]) == "IGEN"){
					$tblString .=  "<td class=\"felszabIGEN\">";
					$tblString .=  trim($records[$i][$j]);
					$tblString .=  "</td>";
				}elseif(trim($records[$i][19]) == "NEM" || trim($records[$i][19]) == "NEM*"){
					$tblString .=  "<td class=\"felszabNEM\">";
					$tblString .=  trim($records[$i][$j]);
					$tblString .=  "</td>";
				}else{
					$tblString .=  "<td class=\"felszabNORM\">";
					$tblString .=  trim($records[$i][$j]);
					$tblString .=  "</td>";
				}
			}
			$tblString .=  "</tr>";
		}
		$tblString .=  "</table>";
		fclose($fh);
		return $tblString;
	}

	public function insertInbInvoice($data){
	//<SF>
	//×-
	// @-- 2015-07-01 -@
	// @-- param unknown $data = a számla adatait tartalmazó tömb. -@
	//-×
	//</SF>
		$retString = "";
		$retString = $this->dbHndlr->insertInbInoice($data);
		return $retString;
	}
	
	public function getEkrInbInvcWghtData(){
	//<SF>
	//×-
	// @-- 2015-07-06 -@
	// @--Itt kérdezzük le az adatbázisba előző napra bekerült számlák súly szerinti összesítőjét, rendszámok szerint. -@
	// @--return string -@
	//-×
	//</SF>
	
		$retString= "";
		$sqlDate = "";
		if(date("N") == 1){
			//<DEBUG>
			//echo "<p>Akkor a Date(N): " . date("N") . "</p>";
			//</DEBUG>
			$sqlDate = date('Y-m-d',strtotime("-3 days"));
		}else{
			$sqlDate = date('Y-m-d',strtotime("-1 days"));
		}
		$arr = $this->dbHndlr->getInbInvcWghtData($sqlDate);
		if(is_array($arr[0])){
			//<DEBUG>
			//echo "<pre>";
			//print_r($arr);
			//echo "</pre><hr/>";
			//</DEBUG>
			$retString .= "<table class=\"tblSzla\"><thead><tr>";
			$retString .= "<th>Feladás</th><th>Feladó</th><th>Rendszám</th><th>Összsúly</th>";
			$retString .= "</tr></thead><tbody>";
			for($i=0;$i<count($arr);$i++){
				$retString .= "<tr>";
				$retString .= "<td>" . $arr[$i]["invdat"] . "</td>";
				$retString .= "<td>" . $arr[$i]["felado"] . "</td>";
				$retString .= "<td><span class=\"kiemeles\">" . $arr[$i]["pltnr"] . "</span></td>";
				$retString .= "<td>" . $arr[$i]["Súly(KG)"] . "</td>";
				$retString .= "</tr>";
			}
			$retString .= "</tbody></table>";
		}else{
			$retString .= "<p class= \"ERRMsg\">Sajnos ez nem jött össze:<br/>";
			$retString .= $arr[0] . "</p>";			
		}
		return $retString;
	}
	
	public function VBScriptGenTest(){
	//<SF>
	//×-
	// @-- 2015-07-06 -@
	// @-- MÓDOSÍTÁSOK:
	//×-
	// @-- Csonkfüggvény a vbScriptGenerator osztály tester() függvényéhez. -@
	//-×
	//-@
	//-×
	//</SF>
		$vbScriptTxt = "'Ez itt a tesztszöveg, ennek kellene a szubrutinhivások sorában feltűnni.\n";
		$vbScriptTxt .= "'CALL LOKALIS_SCRIPT_SUB()\n";
		$vbScriptTxt .= "'ErrMsg = \"Teszt hibaüzenet...\"\n";
		$vbScriptTxt .= "'CALL hibaKezeles()\n\n";
		$vbScriptTxt .= "'CALL hibaKezeles()\n\n";
		
		$this->vbScrptHndlr->addTextToScript($vbScriptTxt);
		$this->vbScrptHndlr->addClosingElements();
	}

	public function getLdLoc($ekrNr){
	//<SF>
	//×-
	// @-- 2015-07-07 -@
	// @-- Ez a függvény csak visszaadja a felrakodás helyét.
	//Ez ahhoz kell, hogy egy adott EKR számhoz meg tudjuk mondani, hogy melyik adatbázisba kell bejelentkezni az XML nek.  -@
	// @-- param unknown $ekrNr = az EKAER szám. -@
	// @-- return unknown -@
	//-×
	//</SF>
		$retString = "";
		$retString = $this->dbHndlr->getLoadLocForEKRNr($ekrNr);
		return $retString;
	}
	
	public function finalizeOneEKRNr($ekrNr, $db){
	//<SF>
	//×-
	// @-- 2015-07-07 -@
	// @-- Ez a függvény beíndítja a lezárási folyamatot a beküldött EKAER számra. -@
	// @-- param unknown $ekrNr -@
	// @-- param unknown $db -@
	// @-- return unknown -@
	//-×
	//</SF>
		$retString = "";
		$retString = $this->ekrHndlr->EKRSzamLezarasa($ekrNr, $db);
		return $retString;
	} 

	public function upldInbInvcItemsFromFlatFile(){
	//<SF>
	//×-
	// @-- 2015-07-14 -@
	// @-- Ez a függvény egy fix fileból tölti fel az abban taláható beérkezési adatokat.
	//Ez egy nagyobb folyamt része, amiben: -@
	// @--
		//×-
		// @-- letöltjük a CFT fileokat, amiket éjjel dolgoz fel a SAP -@
		// @-- a CFT fileokból összegyűjtjük egy adatbázistáblába a számlák fejlécadatait -@
		// @-- a fejlécből szerzett számlaszámokhoz letöltjük a SAPból a VTSZre összegzett tételadatokat. -@
		// @-- ezt egy PHP rutin egy a mySql adatbázis által olvasható fix flat file-á alakítja -@
		// @-- Ez a függvény hívja meg a feltöltéshez szükséges mySql queryt kezelő függvényt a mySQLHandelr osztáyban -@
		//-×
	//-@
	//-×
	//MÓDOSÍTÁSOK:
	//×-
	// @-- 2016-10-10:<br>
	// Betettem az utolsó előtti sorba a logolást, meglátjuk  működik-e a helyi create_EKAER_LOG függvényre, 
	// és a ma létrehozott új tblekaerlog adatbázis táblára támaszkodva.
	//-@
	//-×
	//</SF>
		$ILL_KOT_VTSZ = array("0201","0202","0203","0204","0205","0206","0207","0208","0209","0210","0302","0303",
				"0304","0401","0402","0403","0404","0405","0406","0407","0408","0409","0601","0602",
				"0701","0702","0703","0704","0705","0706","0707","0708","0709","0710","0711","0712",
				"0713","0714","0801","0802","0803","0804","0805","0806","0807","0808","0809","0810",
				"0811","0901","0904","1006","1007","1008","1101","1102","1507","1508","1509","1510",
				"1511","1512","1513","1514","1515","1516","1517","1518","1601","1602","1701","1702",
				"2309","2505","2517","2706","2707","2712","2715","2902","3102","3103","3104","3105",
				"3403","3808","3811","3814","3824","4401","4403","6101","6102","6103","6104","6105",
				"6106","6107","6108","6109","6110","6111","6112","6113","6114","6115","6116","6117",
				"6201","6202","6203","6204","6205","6206","6207","6208","6209","6210","6211","6212",
				"6213","6214","6215","6216","6217","6309","6401","6402","6403","6404","6405","6406");
		//<nn>
		//Beállítjuk a fileeléréseket: input (ez a SAPból jön, output ez a mysql-be megy!
		//</nn>
		$sorSzlo = 0;
		$feldoglozottSzamlak = array();
		$aktSzla = array();
		$log = "*************************************************************************************************************" . 
				"*******************".PHP_EOL;
		$log .= "* Ez a log a számlaTÉTELEK feltöltéséről készült [dbekaer->tblinvcitms]" .
			Date("Y\.m\.d\. H\:m\:s") . "****************" . PHP_EOL;
		$log .= "*************************************************************************************************************" . 
			"*******************".PHP_EOL;
		$fPath = "I:/Informatika/ProgRAM/Scriptek/SAP_DWNLDs/EKAER/SZAMLAK/";
		$fPathOUT = "I:/Informatika/ProgRAM/Scriptek/Adatfileok/";
		$fPathLog = "I:/Informatika/ProgRAM/Scriptek/SAP_DWNLDs/EKAER/SZAMLAK/LOG/";
		$fn = "AKT_INVC_ITMS.TXT";
		$fnOut = "AKT_INVC_ITMS.TXT";
		$fnLog = "INVC_ITMS_" . Date("Y\_m\_d\_His") . ".TXT";
		
		//<nn>
		//Megnyitjuk a fileokat, ha nem megy elhalunk DIE-al.
		//</nn>
		$fh = fopen($fPath . $fn, "r")or die("<p>A filenyitás ({$fPath}{$fn})sikertelen!</p>");
		$fhOut = fopen($fPathOUT.$fnOut,"w")or die("<p>Az írásra szánt file({$fPathOUT}{$fnOut}) megnyitása sikertelen!</p>");
		$fhLog = fopen($fPathLog.$fnLog,"w")or die("<p>Az írásra szánt LOG file({$fPathLog}{$fnLog}) megnyitása sikertelen!</p>");
		
		//<nn>
		//Az aktuálisan beolvasott sornak szánt tömb, a tömböt könnyebb fileba írni.
		//</nn>
		$oneRec = array();
		
		//<nn>
		//Végigolvassuk a file-t.
		//</nn>
		while($sor = fgets($fh,4096)){
			//<nn>
			//Ha megfelelő összefoglalási szintű a sor, akkor tömböt csinálunk belőle, 
			//és egy kiírandó sort is, amit a végén a fileba is írunk.
			//</nn>
			if(substr($sor,0,5) === "|*   "){
				$sqlFltsor = "|";
				$sor = subStr($sor,3,1000);
				$oneRec = explode("|",$sor);
				$sqlFltsor .= trim($oneRec[1]) . "|";
				$VTSZ = substr(trim(($oneRec[3])),0,4);
				if(in_array($VTSZ, $ILL_KOT_VTSZ)){
					$sqlFltsor .= "8707" . "|";
				}else{
					$sqlFltsor .= $VTSZ . "|";
				}
				$sqlFltsor .= ceil(str_replace(",", "", trim($oneRec[4]))) . "|";
				$sqlFltsor .= ceil(str_replace(",", "", trim($oneRec[5]))) . "|\n";
				fwrite($fhOut,$sqlFltsor);
				$sorSzlo ++;
			}elseif(substr($sor,0,5) === "|**  "){
				$aktSzla['szlaSzam'] = trim(substr($sor,21,10));
				$aktSzla['brWght'] = ceil(str_replace(",", "", trim(substr($sor,70,12))));
				array_push($feldoglozottSzamlak, $aktSzla);
				$aktSzla = array();
			}
		}
		
		//<nn>
		//Bezárjuk a file-okat.
		//</nn>
		fclose($fh);
		fclose($fhOut);
		$retString = "<div class=\"alapRaktarDiv\"><p>{$sorSzlo} tétel átírva flatfile-ba.</p>";
		$log .= "{$sorSzlo} tétel átírva flatfile-ba." . PHP_EOL;
		
		//<DEBUG>
		//fwrite($fhLog, $log);
		//</DEBUG>
		
		$retString .= "<h4>A számlák:</h4>";
		$log .= "A következő számlák tételei kerültek feltöltésre: " . PHP_EOL;
		for($i=0;$i<count($feldoglozottSzamlak);$i++){
			$retString .= "<p>";
			$retString .= "Számlaszám: " . $feldoglozottSzamlak[$i]['szlaSzam'] . " - ";
			$log .= "Számlaszám: " .  $feldoglozottSzamlak[$i]['szlaSzam'] . " - ";
			$retString .= ", bruttó súly : " . $feldoglozottSzamlak[$i]['brWght'] . " - ";
			$log .= ", bruttó súly : " . $feldoglozottSzamlak[$i]['brWght'] . PHP_EOL;
			$retString .= "</p>";
		}
		//<nn>
		//Feltöltjük a flat file-t az adatbázisba.
		//</nn>
		fwrite($fhLog, $log);
		fclose($fhLog);
		$retString .= $this->dbHndlr->upldInbInvItms(EKR_INV_ITMS_FILE_PATH);
		$this->create_EKAER_LOG("Számla tételek", $retString);
		return $retString;
	}

	public function getNetWghtFromTblinvcitms($invNr){
	//<SF>
	//×-
	// @-- 2015-07-16 -@
	// @-- Ez a függvény a mysqlhandler-re támaszkodva megszerzi a beküldött számlaszámhoz tartozó
	//nettó súlyösszeget. -@
	// @-- param unknown $invNr = számlaszám -@
	//-×
	//</SF>
		$retString = "";
		$retVal = 0;
		$retString = $this->dbHndlr->getSumNetWghtForInvcNr($invNr);
		if(is_numeric($retString)){
			$retVal = (int)$retString;
			return $retVal;
		}else{
			$retString = "<p>String jött vissza, vagy legalábbis az is_numerc() false-t adottt...({$retString})</p>";
			return $retString;
		}
		
	}

	public function insertPkgWghtForINvc($invc, $wght){
	//<SF>
	//×-
	// @-- 2015-07-16 -@
	// @-- Ez a függvény a MySQLHandler.php egy függvényét hívja, hogy a paraméterként kapott súlyt, a 
	//szintén paraméterként kapott számlaszámmal, egy gönygyölegételként beszúrja a tblinvcitms táblába. -@
	// @-- param unknown $invc = számlaszám -@
	// @-- param unknown $wght = göngyölegsúly -@
	// @-- return unknown = az eredmény... -@
	//-×
	//</SF>
		$retStr = "";
		$retStr .= $this->dbHndlr->insertPKGItemIntoInvoice($invc,$wght);
		return $retStr;
	} 

	public function getEKRNRDeatils($ekrNr){
		echo "EKR szám: " . $ekrNr;
		$resArr = array();
		$resArr = $this->dbHndlr->getDetailsForEKRNr($ekrNr);
		return $resArr;
	}

	public function updtOneEKRNr($data){
	//<SF>
	//×-
	// @-- TESZTFÜGGVÉNY queryhez... -@
	// @-- param unknown $data -@
	//-×
	//</SF>
		return $this->dbHndlr->updtOneEKRNr($data);
	}

	public function Call_EKRHandlerTester(){
	//<SF>
	//Ez a függvény az EKAER_CODE_WH.php függvény teszter függvényét hívja meg.
	//Ezzel neyrünk tesztelési lehetőséget az amúgy privát függvényekbe...
	//</SF>
		$retString = $this->ekrHndlr->testPrivateFunctions();
		return $retString;
	}

	public function CALL_MySQLHNDLRTester($ekrNr){
	//<SF>
	//Ez a függvény egy mysqlHandler class függvényt kezel :)
	//</SF>
		
		return $this->dbHndlr->getInvcItmListForDelteXMLTags($ekrNr); 
	}

	public function callEkAERQueryCreation($tcn="",$usr=""){
	//<SF>
	//2016-09-22<br>
	// Ez a függvény hívja a EKAER_CODE_WH.php generateQueryXMLForTCNR() függvényt, hogy
	// az adott ekaer számra, generáljunk egy lekérdező QUERY-t a NAVhoz.
	// PARAMÉTEREK:
	//×-
	// @-- ... -@
	//-×
	// MÓDOSÍTÁSOK :
	//×-
	// @-- ... -@
	//-×
	//</SF>
		if($tcn == ""){
			$tcn = "E1609191276D675";
		}
		if($usr == ""){
			$usr ="Ren";
		}
		
		$this->ekrHndlr->generateQueryXMLForTCNR($tcn, $usr);
	}
	
	public function getInvcDataToPltnrMod($invc,$dat){
	//<SF>
	// 2016-09-23<br>
	// Ez a függvény egy számla adatit kéri le a helyi adatbázisból.
	// PARAMÉTEREK:
	//×-
	// @-- @param $invc = a számla azonosítója -@
	// @-- @param $dat = a számla dátuma, - jbbha van, mert évente ismétlődhet a sorszám.  -@
	//-×
	// MÓDOSÍTÁSOK :
	//×-
	// @-- ... -@
	//-×
	//</SF>
		$resArr = array();
		$resArr = $this->dbHndlr->getINVCDataForPltnrModif($invc, $dat);
		return $resArr;		
	}
	
	public function updatePltnRForInvoice(){
	//<SF>
	// 2016-09-23<br>
	// Ez a függvény a chngPltNrForInvoice.php lapról kerül meghívásra, és frissíti a 
	// POST szuperglobális tömb elemei alapján egy számla adatait.
	// PARAMÉTEREK:
	//×-
	// @-- ... -@
	//-×
	// MÓDOSÍTÁSOK :
	//×-
	// @-- ... -@
	//-×
	//</SF>
		$resArr = array();
		$invc = $_POST['newInv'];
		$dat = $_POST['newDat'];
		$plNr = $_POST['newPltnr'];
		$fldo = $_POST['newFldo'];
		$oPlt = $_POST['oldPlt'];
		$oFl = $_POST['oldFld'];
		$resArr = $this->dbHndlr->UpdateOneInvoicePltNrSender($invc,$dat,$plNr,$fldo,$oPlt,$oFl);
		return $resArr;
	}

	public function teszter() {
		//<SF>
		//×-
		// @-- Csak egy tesztfüggvény, amivel csekkoltam a képgenerátor függvény mködését. -@
		// @-- return string -@
		//-×
		//</SF>
		$retString = "";
		$oneRec = array();
		$data = array();
		$fPath = "I:/Informatika/ProgRAM/Scriptek/SAP_DWNLDs/EKAER/SZAMLAK/AKT_CFT_NIS.TXT";
		$fh = fopen($fPath, "r")or die("<p class=\"ERRMsg\">A file ({$fPath}) megnyitása nem sikerült!</p>");
		while($sor = fgets($fh, 4096)){
			if(substr($sor,0,5) === "20HAL"){
				$oneRec['SzlaSzm'] = substr($sor,7,5);
				$oneRec['AckId'] = substr($sor,12,6);
				$oneRec['AckItm'] = substr($sor,18,6);
				$oneRec['PoNr'] = substr($sor,24,10);
				$oneRec['dat1'] = "20" . substr($sor,34,6);
				$oneRec['PnShp'] = trim(substr($sor,52,12));
				$oneRec['Qtty'] = (int)trim(substr($sor,64,6));
				$oneRec['HUId'] = substr($sor,88,6);
				$oneRec['HUTp'] = substr($sor,84,4);
				$oneRec['NtWg'] = (substr($sor,103,7)*$oneRec['Qtty'])/1000;
				$oneRec['VTSZ'] = substr($sor,138,8);
				array_push($data, $oneRec);
				$oneRec = array();
			}
		}
		$invDate = substr($data[0]['dat1'],0,4) . "-" . substr($data[0]['dat1'],4,2) . "-" . substr($data[0]['dat1'],-2);
		$szId = $data[0]['SzlaSzm'];
		$kollik= array();
		$netSuly = 0;
		for($i=0;$i<count($data);$i++){
			$netSuly += $data[$i]['NtWg'];
			$huStr = $data[$i]['HUId'] . "-" . $data[$i]['HUTp'];
			if(!in_array($huStr, $kollik)){
				array_push($kollik,$huStr);
			}
		}
		$kolliSuly = 0;
		echo "<pre>";
		print_r($kollik);
		echo "</pre>";
		for($i=0;$i<count($kollik);$i++){
			if(substr($kollik[$i], -2) == "A1"){
				$kolliSuly += 43;
			}elseif(substr($kollik[$i], -2) == "A2"){
				$kolliSuly += 48;
			}elseif(substr($kollik[$i], -2) == "A7"){
				$kolliSuly += 131;
			}elseif(substr($kollik[$i], -2) == "A8"){
				$kolliSuly += 64;
			}elseif(substr($kollik[$i], -2) == "A9"){
				$kolliSuly += 170;
			}elseif(substr($kollik[$i], -2) == "B1"){
				$kolliSuly += 42;
			}elseif(substr($kollik[$i], -2) == "B2"){
				$kolliSuly += 55;
			}elseif(substr($kollik[$i], -2) == "B3"){
				$kolliSuly += 65;
			}elseif(substr($kollik[$i], -2) == "C7"){
				$kolliSuly += 29;
			}elseif(substr($kollik[$i], -2) == "E0"){
				$kolliSuly += 1;
			}elseif(substr($kollik[$i], -2) == "F0"){
				$kolliSuly += 28;
			}elseif(substr($kollik[$i], -2) == "G7"){
				$kolliSuly += 58;
			}elseif(substr($kollik[$i], -2) == "H1"){
				$kolliSuly += 30;
			}elseif(substr($kollik[$i], -2) == "M1"){
				$kolliSuly += 24;
			}else{
				$kolliSuly += 1;
			}
		}
		$felado = "Nissan";
		$pltNr = "NISPLT";
		echo "<p>A nettó súly: " . $netSuly . " kg.<br/>A kollisúly: {$kolliSuly} kg<br/>";
		echo "Ez összesen: " . ceil(($netSuly + $kolliSuly)) . "kg<br/>Számladátum: {$invDate}</p>";
		$dataToInsert = array();
		$dataToInsert[0] = $szId;
		$dataToInsert[1] = $invDate;
		$dataToInsert[2] = $pltNr;
		$dataToInsert[3] = ceil(($netSuly + $kolliSuly));
		$dataToInsert[4] = $felado;
		echo "<pre>";
		print_r($dataToInsert);
		echo "</pre>";
		echo $this->dbHndlr->insertAccumNissanInvoice($dataToInsert);
	
		return $retString;
	}
	
	public function getPatentData($dat = ""){
	//<SF>
	// 2016-10-10<br>
	// Ez a függvény a patentes beléptető adatbázisból kér le adatokat. Igen, ez is az EKAER keresglés miatt
	// vált szükségessé.
	// PARAMÉTEREK:
	//×-
	// @-- @param - $dat= a belépések dátuma. Ha nincs, akkor automatikusan a tegnapi napra keresünk. -@
	//-×
	// MÓDOSÍTÁSOK :
	//×-
	// @-- ... -@
	//-×
	//</SF>
		$dbhlr = new mySQLHandler("patent","patentporta");
		$c = $dbhlr->getConnection();
		
		$qry = "SELECT jmzg.jarmu_mozgas_id,jmzg.rogzites_idopontja AS 'Rögzítve',jmzg.kilepes_ideje AS ";
		$qry .= "'Kilépés',jmzg.beszallitopartner_be AS 'Beszállító',j.rendszam AS 'Vontató',j2.rendszam AS 'Pótkocsi' ";
		$qry .= "FROM jarmu_mozgas AS jmzg ";
		$qry .= "JOIN jarmu AS j ON j.jarmu_id = jmzg.jarmu_fk ";
		$qry .= "JOIN jarmu AS j2 ON j2.jarmu_id = jmzg.belepes_potkocsival_fk ";
		if($dat == ""){
			$mltplr = 1;
			$dat = Date("Y-m-d", time() - $mltplr*60*60*24);
		}
		
		$qry .= "WHERE DATE( rogzites_idopontja ) = '" . $dat . "' ";
		$qry .= "ORDER BY jarmu_mozgas_id DESC LIMIT 100; ";
		//<DEBUG>
		// A QUERY szövegének ellenőrzése:<br>
		//<pre>echo "<p>Query: <br>" . $qry ."</p>";</pre>
		//</DEBUG>
		
		$res = mysqli_query($c, $qry);
		//<nn>
		// Ellenőrizzük, hogy a query eredménye mi lett.
		// Külön kell kezelni az lábbi eseteket:
		//×-
		// @-- A query-re hiba a válasz. FLAG + MSG -@
		// @-- A query nem okozott hibát, de nincs találata. FLAG + MSG -@
		// @-- A query nem okozott hibát és adat is van. FLAG + DATA -@
		//-×
		//</nn>
		if(!$res){
			$retArr['FLAG'] = "NOK";
			$msg = "<p>QUERY nem működött!";
			$msg .= "MySQL hiba: " . mysqli_error($c);
			$retArr['MSG'] = $msg;
		}else{
			if(mysqli_num_rows($res) < 1){
				$retArr['FLAG'] = "OK";
				$msg = "<p>QUERY működött, de az adott dátumra: " . $dat . " nincs belépés rögzítve!";
				$retArr['MSG'] = $msg;
			}else{
				$retArr = array();
				$retArr['FLAG'] = "OK";
				$retArr['DATA'] = array();
				while($sor=mysqli_fetch_assoc($res)){
					array_push($retArr['DATA'], $sor);
				}
			}
		}
		return $retArr;
	}
	
	public function create_EKAER_LOG($shtrt, $long){
	//<SF>
	// 2016-10-10<br>
	// Ez a függvény egy logot illeszt be a dbekaer adatbázis tblekaerlog táblájába. 
	// PARAMÉTEREK:
	//×-
	// @-- @param:$shtrt = az adatbázis log rövid leírása -@
	// @-- @param:$long = az adatbázis log részletes leírása -@
	//-×
	// MÓDOSÍTÁSOK :
	//×-
	// @-- ... -@
	//-×
	//</SF>
		//<nn>
		// A menet a szokásos, az osztály lokális adatbázishozzásférést hordozó adattagjából lekérünk egy példányt, 
		//, majd ebből leveszünk egy connection objektumot. Ezután beállítjuk az adatbázist.<br>
		// Majd összeállítjuk, és lefuttatjuk a queryt. Az eredmény egy szoksásos tömbbe kerül amit visszaadunk.
		//</nn>
		$c = $this->dbHndlr->getConnection();
		$this->dbHndlr->changeDatabase("dbekaer");
		$qry = "INSERT INTO tblekaerlog VALUES(NULL, '";
		$qry .= $shtrt . "', '";
		$qry .= $long . "', ";
		$qry .= "NOW());";
		
		$retArr = array();
		
		$res = mysqli_query($c, $qry);
		if(!$res){
			$retArr['FLAG'] = "NOK";
			$msg = "<p class=\"ERRMsg\">Sajnos a log-beszúró query<br>(". $qry . ")<br>hibát okozott!<br>";
			$msg .= "A mysqli hiba leírása: <br>";
			$msg .= mysqli_error($c);
			$msg .= "SIGNATURE: <br> baseCode_HAL.php - create_EKAER_LOG(\$shtrt, \$long)<br></p>.";
			$retArr['MSG'] = $msg;
		}else{
			$retArr['FLAG'] = "OK";
			$retArr['MSG'] = "<p class=\"OKMsg\">Logolás = OK</p>";
		}
		return $retArr;
	}
	
	///////////////////////////////////////////////////////////////////////////////////////////////////////////////
	///////////////////////////////////////MICROSOFT SQL SERVER MŰVELETEK//////////////////////////////////////////
	///////////////////////////////////////////////////////////////////////////////////////////////////////////////
	
	public function getHaviJovedeki(){
	//<SF>
	//2016-04-04
	//Havi jövedéki kiírása fileba, amit excelle be lehet olvasni.
	//@return string[]
	//</SF>
		//<DEBUG>
		//echo "<p>A post tartalma a baseCodeHal->getHaviJovedeki-ben:<br><pre>";
		//print_r($_POST);
		//echo "</pre></p>";
		//</DEBUG>
		$dbN = $_POST['radPlant'];
		$begDt = $_POST['begDt'];
		$endDt = $_POST['endDt'];
		$resArr = array();
		$resArr = $this->MsDBHndlr->serpaTeszt($begDt,$endDt,$dbN);
		return $resArr;
	}
	
	public function getHianyzoTimeSheetek(){
	//<SF>
	//Nem sok minden csinál a függvény, csak meghívja a MS SQL server kezelő osztály 
	//hianyzoTimeSheetekTst(...) függvényét, hogy a hiányzó timesheeteket megkapjuk.
	//</SF>
		//<DEBUG>
		//echo "<p>A post tartalma a baseCodeHal->getHaviJovedeki-ben:<br><pre>";
		//print_r($_POST);
		//echo "</pre></p>";
		//</DEBUG>
		$mszk = $_POST['radMuszak'];
		$begDt = $_POST['begDt'];
		$endDt = $_POST['endDt'];
		$resArr = array();
		$resArr = $this->MsDBHndlr->hianyzoTimeSheetekTst($mszk,$begDt,$endDt);
		return $resArr;
	}
	
	
	///////////////////////////////////////////////////////////////////////////////////////////////////////////////
	///////////////////////////////////////    BELÖVŐS PROJEKT   //////////////////////////////////////////

	
	public function getAllShots(){
	//<SF>
	//×-
	// @-- COPY TO PRODUCTION -@
	// @-- 2016-04-04 -@
	// @-- Az összes lövés kiolvasása. Garnatáltan csak  mai lesz benne, 
	//mert JOIN-t tartalmaz a mai TOk, és HUk táblájára. -@
	//-×
	//</SF>
		$dbCon = new mySQLHandler("beklikk", "munkaadatbazis");
		$resp = array();
		$resp = $dbCon->getListOfShots();
		if($resp[0] == "OK"){
			return $resp[1];
		}else{
			$retString = "<p class=\"ERRmsg\">Hiba történt!<br/>Részletek:<br/>";
			$retString .= $resp[1] . $resp[2] . $resp[3] . $resp[4] . $resp[5];
		}
	}

	public function getDBConnShots(){
	//<SF>
	//×-
	// @-- COPY TO PRODUCTION -@
	// @-- 2016-01-27 -@
	// @-- Nemigen használható.... -@
	// @-- return mySQLHandler -@
	//-×
	//</SF>
		$dbCon = new mySQLHandler("beklikk", "munkaadatbazis");
		return $dbCon;
	}

	public function getWrongShots($route){
	//<SF>
	//×-
	// @-- COPY TO PRODUCTION -@
	// @-- 2016-01-27 -@
	// @-- A rossz helyre lőtt TOk listája egy egész route-ra
	//elméletileg ennek mindig üresnek kellene lennie, mert a scanner nem engedi belőni ezeket. -@
	// @-- param unknown $route = ROUTE -@
	// @-- return multitype:string -@
	//-×
	//</SF>
	
		//<nn>
		//Az alap módszer:
		//×-
		// @-- - deklarálunk egy tömböt -@
		// @-- - csatlakozunk az adatbázishoz a megfelelő osztály paraméterezett konstruktorával -@
		// @-- - összeállítjuk a queryt -@
		// @-- - feluttatjuk -@
		// @-- - az eredmény OK voltát eltároljuk a választömb "flag" nevű elemében -@
		// @-- - Ha a flag = OK -@
		// @-- - - akkor a tömb "data" nevű elemébe a visszaadott adatok tömbjét tesszük -@
		// @-- - Ha a flag = NOK -@
		// @-- - - akkor nem tömböt, csak egy stringet teszünk a data-ba, ami a hiba leírása. -@
		//-×
		//A hívó megvizsgálva a data-t el tudja dönteni, hogy kíírja a hibastringet, vagy feldolgozza
		//az adat array-t.
		//</nn>
		$retArr = array();
		$db = new mySQLHandler("beklikk", "munkaadatbazis");
		$dbCon = $db->getConnection();
		
		if($route !== "ALL"){
			$qry = "SELECT toit.totoitm,toit.grids,toit.gridu,hu.exthuid,dlrs.stockGrid,dlrs.urgGrid FROM tbllovesek as lov ";
			$qry .= "JOIN tblakttsferrdrs as toit ON lov.totoitm = toit.totoitm ";
			$qry .= "JOIN tblhutopick as hu ON lov.exthuid = hu.exthuid ";
			$qry .= "JOIN tbldealers as dlrs ON hu.dlr = dlrs.DlrID ";
			$qry .= "WHERE toit.route ='" .  $route . "' and toit.shpttprty != hu.dlr;";
		}else{
			$qry = "CALL sp_wronghutoitm();";
		}
		
		$res = mysqli_query($dbCon, $qry);
		if(!$res){
			$retArr['flag'] = "NOK";
			$msg = "<p class=\"ERRMsg\">Sajnos a lekérdezés: " . $qry . " sikertelen volt!<br/>";
			$msg .= "MySQL hiba: <br/>" . mysqli_error($dbCon);
			$msg .= "<br/>Signature:<br/><b>baseCode_HAL.php->getWrongShots()<b><br/>";
			$sms .= "</p>";
			$retArr['data'] = $msg;
		}elseif(mysqli_num_rows($res) == 0){
			$retArr['flag'] = "OK";
			$msg = "<p class=\"OKMsg\">Nem volt rossz helyre lőtt TO!</p>";
			$retArr['data'] = $msg;
		}else{
			$retArr['flag'] = "OK";
			$retArr['data'] = array();
			while($sor=mysqli_fetch_assoc($res)){
				array_push($retArr['data'], $sor);
			}
		}
		return $retArr;
	}

	public function getWrongShotsUSR($usr){
		//<SF>
		//×-
		// @-- COPY TO PRODUCTION -@
		// @-- 2016-07-21 -@
		// @-- A rossz helyre lőtt TOk listája egy felhasználóra.
		// elméletileg ennek mindig üresnek kellene lennie, mert a scanner nem engedi belőni ezeket. -@
		// @-- param unknown $usr = a NAGYBETŰSÍTETT felhasználónév. -@
		// @-- return multitype:string -@
		//-×
		//</SF>
	
		//<nn>
		//Az alap módszer:
		//×-
		// @-- - deklarálunk egy tömböt -@
		// @-- - csatlakozunk az adatbázishoz a megfelelő osztály paraméterezett konstruktorával -@
		// @-- - összeállítjuk a queryt -@
		// @-- - feluttatjuk -@
		// @-- - az eredmény OK voltát eltároljuk a választömb "flag" nevű elemében -@
		// @-- - Ha a FLAG = OK -@
		// @-- - - akkor a tömb "data" nevű elemébe a visszaadott adatok tömbjét tesszük -@
		// @-- - Ha a FLAG = NOK -@
		// @-- - - akkor nem tömböt, csak egy stringet teszünk a data-ba, ami a hiba leírása. -@
		//-×
		//A hívó megvizsgálva a data-t el tudja dönteni, hogy kíírja a hibastringet, vagy feldolgozza
		//az adat array-t.
		//</nn>
		
		//<nn>
		// Definiáljuk a változókat, főképpen az eredmény tömböt: $retArr = array();, valamint az adatbázis kezelő
		// példányt: $db = new mySQLHandler("beklikk", "munkaadatbazis");. Ebből jöhet egy kapcsoltapéldány, amit
		// a queryhez használunk majd: $dbCon = $db->getConnection();.
		//</nn>
		$retArr = array();
		$db = new mySQLHandler("beklikk", "munkaadatbazis");
		$dbCon = $db->getConnection();
	
		//<nn>
		// Ha volt user kiválasztva összeállítjuk rá a query-t. Ha nem volt, akkor
		// letolunk egy stored procedure-t, ami visszaadja az összes rossz lövést.
		//</nn>
		if($usr !== "-----"){
			//<nn>
			// A részletes query.
			//</nn>
			$qry = "SELECT toit.totoitm,toit.grids,toit.gridu,hu.exthuid,dlrs.stockGrid,dlrs.urgGrid FROM tbllovesek as lov ";
			$qry .= "JOIN tblakttsferrdrs as toit ON lov.totoitm = toit.totoitm ";
			$qry .= "JOIN tblhutopick as hu ON lov.exthuid = hu.exthuid ";
			$qry .= "JOIN tbldealers as dlrs ON hu.dlr = dlrs.DlrID ";
			$qry .= "WHERE toit.cnfusr ='" .  $usr . "' and toit.shpttprty != hu.dlr;";
		}else{
			//<nn>
			// A tárolt eljárás-hivó query $qry = "CALL sp_wronghutoitm();";.
			//</nn>
			$qry = "CALL sp_wronghutoitm();";
		}
		
		//<nn>
		// A query le is futtatjuk -> mysqli_query($dbCon, $qry).
		//</nn>
		$res = mysqli_query($dbCon, $qry);
		
		//<nn>
		// Jöhet az eredméyntömböt összeállító IF szerkezet, majd az eredmény visszaadása.
		//</nn>
		if(!$res){
			$retArr['FLAG'] = "NOK";
			$msg = "<p class=\"ERRMsg\">Sajnos a lekérdezés: " . $qry . " sikertelen volt!<br/>";
			$msg .= "MySQL hiba: <br/>" . mysqli_error($dbCon);
			$msg .= "<br/>Signature:<br/><b>baseCode_HAL.php->getWrongShots()<b><br/>";
			$msg .= "A query:<br/><code>";
			$msg .= $qry . "</code>";
			$sms .= "</p>";
			$retArr['data'] = $msg;
		}elseif(mysqli_num_rows($res) == 0){
			$retArr['FLAG'] = "OK";
			$msg = "<p class=\"OKMsg\">Nem volt rossz helyre lőtt TO az adatbázisban a mai napra, " . $usr . 
				" felhasználónak!</p>";
			$retArr['DATA'] = $msg;
		}else{
			$retArr['FLAG'] = "OK";
			$retArr['DATA'] = array();
			while($sor=mysqli_fetch_assoc($res)){
				array_push($retArr['DATA'], $sor);
			}
		}
		//<DEBUG>
		//echo "<p>A return array: <pre>";
		//print_r($retArr);
		//echo "</pre></p>";
		//</DEBUG>
		return $retArr;
	}
	
	public function getWrongShotsTONR($tonr){
		//<SF>
		//×-
		// @-- COPY TO PRODUCTION -@
		// @-- 2016-07-21 -@
		// @-- A rossz helyre lőtt TOk listája egy felhasználóra.
		// elméletileg ennek mindig üresnek kellene lennie, mert a scanner nem engedi belőni ezeket. -@
		// @-- param unknown $tonr = a TP száma -@
		// @-- return array = erdménytömb -@
		//-×
		//</SF>
	
		//<nn>
		//Az alap módszer:
		//×-
		// @-- - deklarálunk egy tömböt -@
		// @-- - csatlakozunk az adatbázishoz a megfelelő osztály paraméterezett konstruktorával -@
		// @-- - összeállítjuk a queryt -@
		// @-- - feluttatjuk -@
		// @-- - az eredmény OK voltát eltároljuk a választömb "flag" nevű elemében -@
		// @-- - Ha a FLAG = OK -@
		// @-- - - akkor a tömb "data" nevű elemébe a visszaadott adatok tömbjét tesszük -@
		// @-- - Ha a FLAG = NOK -@
		// @-- - - akkor nem tömböt, csak egy stringet teszünk a data-ba, ami a hiba leírása. -@
		//-×
		//A hívó megvizsgálva a data-t el tudja dönteni, hogy kíírja a hibastringet, vagy feldolgozza
		//az adat array-t.
		//</nn>
	
		//<nn>
		// Definiáljuk a változókat, főképpen az eredmény tömböt: $retArr = array();, valamint az adatbázis kezelő
		// példányt: $db = new mySQLHandler("beklikk", "munkaadatbazis");. Ebből jöhet egy kapcsoltapéldány, amit
		// a queryhez használunk majd: $dbCon = $db->getConnection();.
		//</nn>
		$retArr = array();
		$db = new mySQLHandler("beklikk", "munkaadatbazis");
		$dbCon = $db->getConnection();
	
		//<nn>
		// Ha volt user kiválasztva összeállítjuk rá a query-t. Ha nem volt, akkor
		// letolunk egy stored procedure-t, ami visszaadja az összes rossz lövést.
		//</nn>
		if($tonr !== ""){
			//<nn>
			// A részletes query.
			//</nn>
			$qry = "SELECT toit.totoitm,toit.grids,toit.gridu,hu.exthuid,dlrs.stockGrid,dlrs.urgGrid FROM tbllovesek as lov ";
			$qry .= "JOIN tblakttsferrdrs as toit ON lov.totoitm = toit.totoitm ";
			$qry .= "JOIN tblhutopick as hu ON lov.exthuid = hu.exthuid ";
			$qry .= "JOIN tbldealers as dlrs ON hu.dlr = dlrs.DlrID ";
			$qry .= "WHERE toit.totoitm LIKE '" .  $tonr . "%' and toit.shpttprty != hu.dlr ORDER BY toit.totoitm;;";
		}else{
			//<nn>
			// A tárolt eljárás-hivó query $qry = "CALL sp_wronghutoitm();";.
			//</nn>
			$qry = "CALL sp_wronghutoitm();";
		}
	
		//<nn>
		// A query le is futtatjuk -> mysqli_query($dbCon, $qry).
		//</nn>
		$res = mysqli_query($dbCon, $qry);
	
		//<nn>
		// Jöhet az eredméyntömböt összeállító IF szerkezet, majd az eredmény visszaadása.
		//</nn>
		if(!$res){
			$retArr['FLAG'] = "NOK";
			$msg = "<p class=\"ERRMsg\">Sajnos a lekérdezés: " . $qry . " sikertelen volt!<br/>";
			$msg .= "MySQL hiba: <br/>" . mysqli_error($dbCon);
			$msg .= "<br/>Signature:<br/><b>baseCode_HAL.php->getWrongShots()<b><br/>";
			$msg .= "A query:<br/><code>";
			$msg .= $qry . "</code>";
			$sms .= "</p>";
			$retArr['data'] = $msg;
		}elseif(mysqli_num_rows($res) == 0){
			$retArr['FLAG'] = "OK";
			$msg = "<p class=\"OKMsg\">Nem volt rossz helyre lőtt sor az adatbázisban a következő TO-ra: " . $tonr ."!</p>";
			$retArr['DATA'] = $msg;
		}else{
			$retArr['FLAG'] = "OK";
			$retArr['DATA'] = array();
			while($sor=mysqli_fetch_assoc($res)){
				array_push($retArr['DATA'], $sor);
			}
		}
		//<DEBUG>
		//echo "<p>A return array: <pre>";
		//print_r($retArr);
		//echo "</pre></p>";
		//</DEBUG>
		return $retArr;
	}
	
	public function getNonShotTOs($to = "", $route=""){
	//<SF>
	//×-
	// @-- COPY TO PRODUCTION -@
	// @-- 2016-01-27 -@
	// @-- A be nem lőtt TOk lekérdezése, route-ra, VAGY TO-ra. -@
	// @-- param string $to -@
	// @-- param string $route -@
	//-×
	//</SF>
		$retArr = array();
		$db = new mySQLHandler("beklikk", "munkaadatbazis");
		$dbCon = $db->getConnection();
		
		if($to == "" && $route == ""){
			$qry = "SELECT tok.totoitm, tok.prtnmbr, tok.trgtQtty, tok.cnfusr, lvsk.exthuid ";
			$qry .= "FROM tblakttsferrdrs as tok ";
			$qry .= "NATURAL LEFT JOIN tbllovesek as lvsk ";
			$qry .= "WHERE tok.stgType = 'A00' AND lvsk.exthuid is NULL;"; //AND tok.totoitm LIKE tonr; ";
		}elseif($to != ""){
			$to = $to . "%";
			$qry = "SELECT tok.totoitm, tok.prtnmbr, tok.trgtQtty, tok.cnfusr, lvsk.exthuid ";
			$qry .= "FROM tblakttsferrdrs as tok ";
			$qry .= "NATURAL LEFT JOIN tbllovesek as lvsk ";
			$qry .= "WHERE lvsk.exthuid is NULL AND tok.totoitm LIKE '" . $to ."';";			
		}elseif($route != ""){
			$qry = "SELECT tok.totoitm, tok.prtnmbr, tok.trgtQtty, tok.cnfusr, lvsk.exthuid ";
			$qry .= "FROM tblakttsferrdrs as tok ";
			$qry .= "NATURAL LEFT JOIN tbllovesek as lvsk ";
			$qry .= "WHERE tok.stgType = 'A00' AND lvsk.exthuid is NULL AND tok.route = '" . $route ."';";			
		}
		
		//<DEBUG>
		//echo 'QUERY: ' . $qry;
		//</DEBUG>
		
		$res = mysqli_query($dbCon, $qry);
		if(!$res){
			$retArr['flag'] = "NOK";
			$msg = "<p class=\"ERRMsg\">Sajnos a lekérdezés: " . $qry . " sikertelen volt!<br/>";
			$msg .= "MySQL hiba: <br/>" . mysqli_error($dbCon);
			$msg .= "<br/>Signature:<br/><b>baseCode_HAL.php->getNonShotTOs(\$to = \"\", \$route=\"\")<b><br/>";
			$sms .= "</p>";
			$retArr['data'] = $msg;			
		}else{
			if(mysqli_num_rows($res) == 0){
				$retArr['flag'] = "OK";
				$msg = "<p class=\"OKMsg\">Nincs olyan TO ami ne lenne belőve!</p>";
				$retArr['data'] = $msg;
			}else{
				$retArr['flag'] = "OK";
				$retArr['data'] = array();
				while($sor=mysqli_fetch_assoc($res)){
					array_push($retArr['data'], $sor);
				}
			}
		}
		return $retArr;
	}

	public function getShotsForTO($TONr){
	//<SF>
	//×-
	// @--COPY TO PRODUCTION -@
	// @-- 2016-02-02 -@
	// @-- Ez a függvény a bajővö TO ról kitalálja, h TO vagy item, majd 
	//ennek megfelelően lekérdezi az adtot, és visszaadja az eredményt egy tömbben. -@
	// @-- param unknown $TONr -@
	//-×
	//</SF>
		$retArr = array();
		$db = new mySQLHandler("beklikk", "munkaadatbazis");
		$dbCon = $db->getConnection();

		//<DEBUG>
		//echo "<p>A beküldött to: " . $TONr . "</p>";
		//</DEBUG>

		if(strlen($TONr) == 14){
			$qry = "SELECT * FROM tbllovesek WHERE totoitm='" . $TONr . "' ORDER BY totoitm;";
		}else{
			$qry = "SELECT * FROM tbllovesek WHERE totoitm LIKE '" . $TONr . "%' ORDER BY totoitm;";
		}

		//<DEBUG>
		//echo "<p>A query: " . $qry . "</p>";
		//</DEBUG>

		$res = mysqli_query($dbCon, $qry);
		if(!$res){
			$retArr['flag'] = "NOK";
			$msg = "A lekérdezés: <br>(" . $qry . ")<br/> sajnos hibás volt, a MYSQL szerver válasza: <br>";
			$msg .= mysqli_error($dbCon);
			$msg .= "<br/>Signature:<br/><b>baseCode_HAL.php->getShotsForTO(\$TONr)<b><br/>";
			$retArr['data'] = $msg;
		}else{
			$retArr['flag'] = "OK";
			if(mysqli_num_rows($res) == 0){
				$retArr['data'] = "A beírt TOhoz nem tartozik scannelés :(";
			}else{
				$retArr['data'] = array();
				while($sor = mysqli_fetch_assoc($res)){
					array_push($retArr['data'], $sor);
				}
			}
		
		}
		//<DEBUG>
		//echo "<p>A visszaküldött array: <pre>";
		//print_r($retArr);
		//echo "</pre></p>";
		//</DEBUG>
		return $retArr;
	}

	public function getShotsForHU($HUNr){
	//<SF>
	//×-
	// @-- COPY TO PRODUCTION -@
	// @-- 2016-04-05 -@
	// @-- Ez a függvény a beküldött HU-ra visszaadja a lövéseket egy tömbben. -@
	// @-- param unknown $HUNr -@
	//-×
	//</SF>
		$retArr = array();
		$db = new mySQLHandler("beklikk", "munkaadatbazis");
		$dbCon = $db->getConnection();

		//<DEBUG>
		//echo "<p>getShotsForHU->\$HUNr: " . $HUNr . "</p>";
		//</DEBUG>
		$retArr = $db->getShotsForHUNr($HUNr);
		return $retArr;
	}
	
	public function copyAndDeleteLovesHistory(){
	//<SF>
	//×-
	// @-- COPY TO PRODUCTION -@
	// @-- 2016-02-08 -@
	// @-- Ez a függvény amysql adatbázisba dob be 4 query-t, amiknek az a feladata, hogy: -@
	// @--
		//×-
		// @-- a TO itemeket az ellenőrzőtáblából a tároló táblába másolja -@
		// @-- a TO tároló táblából kitörli az 1 hétnél régebbi adatokat -@
		// @-- A HU-kat az ellenőrző táblából a tárolótáblába másolja -@
		// @-- HU tároló táblából kitörli az 1 hétnél régebbi adatokat  -@
		//-×
	// -@
	//-×
	//</SF>
	
		//<nn>
		//Adatbázis csatlakozás + objektum megszerzése.
		//</nn>
		$retArr = array();
		$db = new mySQLHandler("beklikk", "munkaadatbazis");
		$dbCon = $db->getConnection();
		
		//<nn>
		//A TO itemek áttöltése.
		//</nn>
		$qry = "INSERT INTO tbltoitmoneweek SELECT * FROM tblakttsferrdrs;";
		$res = mysqli_query($dbCon, $qry);
		if(!$res){
			$retArr['flag'] = "NOK";
			$msg = "A query: (" . $qry . ") hibát okozott a MySQL szeveren: <br>" . mysqli_error($dbCon);
			$msg .= "<br/>Signature:<br/><b>baseCode_HAL.php->copyAndDeleteLovesHistory()<b><br/>";
			$retArr['data'] = $msg;
		}else{
			$retArr['flag'] = "OK";
			$msg = "A TO itemek átmásolva a tbltoitmoneweek táblába!<br/>";
			$retArr['data'] = $msg;
		}
		
		//<nn>
		//A régi TO itemek törlése.
		//</nn>
		$dbCon = $db->getConnection();
		$qry = "DELETE FROM tbltoitmoneweek WHERE crtdate < (CURDATE() - INTERVAL 7 DAY);";
		$res = mysqli_query($dbCon, $qry);
		if(!$res){
			$retArr['flag'] = "NOK";
			$msg = $retArr['data'];
			$msg .= "A query: (" . $qry . ") hibát okozott a MySQL szeveren: <br>" . mysqli_error($dbCon);
			$msg .= "<br/>Signature:<br/><b>baseCode_HAL.php->copyAndDeleteLovesHistory()<b><br/>";
			$retArr['data'] = $msg;
			
		}else{
			$msg = $retArr['data'];
			$msg .= "Az egy hétnél régebbi TO itemek törölve vannak a tbltoitmoneweek táblából.<br/>";
			$retArr['data'] = $msg;
		}
		
		//<nn>
		//A HU adatok áttöltése.
		//</nn>
		$dbCon = $db->getConnection();
		$qry = "INSERT INTO tblhusoneweek SELECT * FROM tblhutopick;";
		$res = mysqli_query($dbCon, $qry);
		if(!$res){
			$retArr['flag'] = "NOK";
			$msg .= "A query: (" . $qry . ") hibát okozott a MySQL szeveren: <br>" . mysqli_error($dbCon);
			$msg .= "<br/>Signature:<br/><b>baseCode_HAL.php->copyAndDeleteLovesHistory()<b><br/>";
			$retArr['data'] = $msg;
		}else{
			$msg = $retArr['data'];
			$msg .= "Az HU adatok átmásolva a tblhusoneweek táblába!<br/>";
			$retArr['data'] = $msg;
		}
		
		//<nn>
		//A régi Hu adatok törlése.
		//</nn>
		$dbCon = $db->getConnection();
		$qry = "DELETE FROM tblhusoneweek WHERE crtdate < (CURDATE() - INTERVAL 7 DAY);";
		$res = mysqli_query($dbCon, $qry);
		if(!$res){
			$retArr['flag'] = "NOK";
			$msg .= "A query: (" . $qry . ") hibát okozott a MySQL szeveren: <br>" . mysqli_error($dbCon);
			$msg .= "<br/>Signature:<br/><b>baseCode_HAL.php->copyAndDeleteLovesHistory()<b><br/>";
			$retArr['data'] = $msg;
		}else{
			$msg = $retArr['data'];
			$msg .= "Az egy hétnél régebbi HU adatok törölve vannak a tblhusoneweek táblából.<br/>";
			$retArr['data'] = $msg;			
		}
		
		//<nn>
		//Ha végeztünk, akkor visszaadjuk az adatokat tartalmazó eredménytömböt.
		//</nn>
		return $retArr;
	}

	public function getRepeatedShotsList(){
	//<SF>
	//×-
	// @-- COPY TO PRODUCTION -@
	// @-- 2016-04-04 -@
	//-×
	//Itt az ismétlődő lövések listája. Ez azért érdekes, mert az adatbázisba nem áttöltött 
	//dobozknál nem lehet ellenőrizni, hogy a belövendő TO dealere megegyezik-e a HU dealerével,
	//így ezeket a lövéseket alapból rögzítjük ellenőrzés nélkül.
	//Ha viszont vki vmit rossz helyre lő (regisztrálatlan dobozba), majd átveszi, és beteszi egy másik,
	//megfelelő kereskedőhőz tartozó dobozba, akkor arra dupla lövés lesz. Egy rossz, és egy jó.
	//Ekkor a rossz lövések lekérdezésbéen csak a rossz fog megjelenni, így marshal feleslegesen 
	//keresgléhet egy olyan dobozba amiben már nincs benne a rossz cucc.
	//Hogy ezt kiküszöböljük, első körben lekérem az ismétlődő lövések listáját, hogy lássam
	//mennyi ilyen eset van!
	//</SF>
		$resArray = array();;
		$db = new mySQLHandler("beklikk", "munkaadatbazis");
		$dbCon = $db->getConnection();
		$resArray = $db->getListOfMoreThanOneShots();
		return $resArray;
	}
	
	public function getShotListsForDate($dt){
	//<SF>
	//×-
	// @-- COPY TO PRODUCTION -@
	// @-- 2016-06-07 -@
	//-×
	//Ez a függvény egy dátumra kér le egy listát, az aznapi lövésekből. Kizárólag a tbllovesek táblát használja, 
	//így egész gyors.
	//A menete a szokásos, adatbázis csatlakozás, majd a mysqlhandlerünk egy metódusának a meghívása, végül
	//az eredmény viszaadása.
	//</SF>
		$retArr = array();
		$db = new mySQLHandler("beklikk", "munkaadatbazis");
		$dbCon = $db->getConnection();
		
		//<DEBUG>
		//echo"<p>A query eredménye (HAL_WEB_BASE->getShotListsForDate): </p><pre>";
		//print_r($retArr);
		//echo"</pre>";
		//</DEBUG>
		$retArr = $db->getShotListsForDate($dt);
		return $retArr;	
	}
	
	public function getShots_ByUsersForDate($dt,$det = false){
	//<SF>
	//×-
	// @-- COPY TO PRODUCTION -@
	// @-- 2016-06-07 -@
	//-×
	//Ez a függvény egy dátumra kér le egy listát, az aznapi lövésekből, és belövő személyekről.
	//Itt több táblára kell JOIN lekérdezést futtatni.
	//A menete a szokásos, adatbázis csatlakozás, majd a mysql-handlerünk egy metódusának a meghívása, végül
	//az eredmény visszaadása.
	//Paraméterként csak egy van $dt a lekérdezés dátuma.
	//</SF>	
		$retArr = array();
		$db = new mySQLHandler("beklikk", "munkaadatbazis");
		$dbCon = $db->getConnection();
		
		$retArr = $db->getShots_ByUsersForDate($dt,$det);
		
		
		return $retArr;	
	}
	
	///////////////////////////////////////////////////////////////////////////////////////////////////////////////
	///////////////////////////////////////        KPI-OS FÜGGVÉNYEK     //////////////////////////////////////////
	public function getKPIData() {
	//<SF>
	// 2016-08-24<br>
	// Ennek a függvénynek az a dolga, hogy a test(jelenleg) adatbázis tblKPIDAILY táblájából kérezzen le adatokat.<br>
	// PARAMÉTEREK:
	//×-
	// @-- Pillanatnyilag nincsenek -@
	//-×
	// MÓDOSÍTÁSOK:
	//×-
	// @-- ... -@
	//-×
	//</SF>
		$db = new mySQLHandler("kpi", "test");
		$resArray = array();
		$c = $db->getConnection();
		$db->changeDatabase("test");
		$qr = "SELECT * FROM tblkpidaily ORDER BY dat DESC LIMIT 10;";
		$res = mysqli_query($c, $qr);
		if(!$res){
			$resArray['FLAG'] = "NOK";
			$msg = "A query - (" . $qr .") hibát okozott!<br>";
			$msg .= "A mysli server hibaleírása: <br>";
			$msg .= mysqli_error($c);
			$msg .= "<br>Signature:<br>baseCode_HAL.php - > public function getKPIData()!<br>";
			$resArray["MSG"] = $msg;
		}else{
			$resArray['FLAG'] = "OK";
			$resArray['DATA'] = array();
			while($sor=mysqli_fetch_assoc($res)){
				array_push($resArray['DATA'], $sor);
			}
		}
		return $resArray;
	}
	
	public function insertKPIToMYSql(){
	//<SF>
	// Ez a függvény reggelente, amikor az első BWs letöltés lefut, feltölti a mysql atdabázisba (test - tblkpidaily),
	// a$_GET superglobális tömb elemei alapján, hogy milyen dátum, mennyi a TCC, és a NISSAN fillrate.
	// PARAMÉTEREK:
	//×-
	// @-- Nincsenek, az a függvény aszuperglobális GET tömböt használja -@
	//-×
	// MÓDOSÍTÁSOK :
	//×-
	// @-- ... -@
	//-×
	//</SF>
		//<DEBUG>
		// A $_GET tömb ellenőrzéséhez:<br>
		// echo "<p>insertKPIToMYSql - GET:<pre>";
		// print_r($_GET);
		// echo "</p>";
		//</DEBUG>
		$retArray = array();
		//<nn>
		// Szerzünk egy connection objektumot.
		//</nn>
		$c = $this->dbHndlr->getConnection();
		
		//<nn>
		// Átváltunk atest adatbázisra, mert abban van jelenleg a KPI táblánk (tblkpidaily).
		//</nn>
		$this->dbHndlr->changeDatabase("test");
		$qry = "INSERT INTO tblkpidaily(id,tccservrt,nissflrt,dat) VALUES( null,". $_GET['re85flrt'] . "," . $_GET['niflrt'] 
			. ",'" . $_GET['dat'] . "');";
		$res = mysqli_query($c, $qry);
		if(!$res){
			$retArray['FLAG'] = "NOK";
			$retArray['MSG'] = "<h3>MYSQL HIBA!!!</h3>A query :(" . $qry . ") hibát okozott a mysql szerveren.<br>";
			$retArray['MSG'] .= "A részletes leírás: <br>" . mysqli_error($c) . "<br>";
			$retArray['MSG'] .= "Signature: <br>baseCodeHal.php - public function insertKPIToMYSql() <br>";
		}else{
			$retArray['FLAG'] = "OK";
			$retArray['MSG'] = "<span>MINDEN OK<span><br>A query :(" . mysqli_affected_rows($c) . ") db rekrod sikeresen beszúrva!<br>";
		}
		
		return $retArray;
	}
	
	///////////////////////////////////////////////////////////////////////////////////////////////////////////////
	///////////////////////////////////////     FILEKEZELÉSI MŰVELETEK   //////////////////////////////////////////

	public function createFlatFileForMySQLUpload($flNm){
	//<SF>
	//×-
	// @-- COPY TO PRODUCTION -@
	// @-- 2016-01-06 -@
	// @-- Általnos függvény, ami a filenév alapján kiválasztott algoritmussal
	// MySQL képes, táblaspecifikus FLAt FILE-t készít, az adatok gyors feltöltéséhez. -@
	// @-- param unknown $flNm az átlakítandó file(forrásfile) neve. -@
	// @-- return string -@
	//-×
	//</SF>
		$retArr = array();
		$dir = "I:\Informatika\ProgRAM\Scriptek\SAP_DWNLDs\NapiAdatok\AKT_PICK\\";
		if($flNm === "AKT_PICK.TXT"){
		//<nn>
		//T.O.-K ÁTÍRÁSA FLAT FILEBA
		//</nn>
			$ma = Date("Y-m-d");
			$lstTOFlNm = "I:\Informatika\ProgRAM\Scriptek\FLAGS\FST_TO_TODAY.TXT";
			$sltModif = Date("Y-m-d",filemtime($lstTOFlNm));
			$fstTO = "";
			$kiirva = false;
			$fhndlr = fopen($dir . $flNm, "r") or die("Filenyitás (_ {$flNm} _) sikertelen!");
			$outFHlr = fopen($dir . "FLAT_TO.TXT", "w") or DIE("A kimeneti file-t nem sikerült megnyitni!");
			while($sor = fgets($fhndlr,4096)){
				if(substr($sor,0,4) === "|201"){
					$aktRec = explode("|",$sor);
				
					//<DEBUG>
					//echo "<pre>";
					//print_r($aktRec);
					//echo "</pre>";
					//</DEBUG>
					if($fstTO == ""){
						echo "AZ aktRec[4] megy a \$fstTO-ba: " . $aktRec[4] . "<br>";
						$fstTO = $aktRec[4];
					}
					$aktRec[4] = $aktRec[4] . $aktRec[5];
					$aktRec[6] = str_replace(" ", "", (trim($aktRec[6])));
					
					$outSor = $aktRec[4];
					$outSor .= "|" . $aktRec[1] . "|" . $aktRec[2] . "|" . $aktRec[3];
					$outSor .= "|" . $aktRec[6] . "|" . $aktRec[7] . "|" . $aktRec[8] . "|" . $aktRec[9];
					$outSor .= "|" . $aktRec[10] . "|" . $aktRec[11] . "|" . $aktRec[12];
					$outSor .= "|" . trim($aktRec[13]) . "|" . trim($aktRec[14]);
					$outSor .= "|" . $aktRec[15] . "|" . $aktRec[16] . "|" . $aktRec[17];
					$outSor .= "|" . trim($aktRec[18]). "|" . trim($aktRec[19]) . "|\n";
					
					fwrite($outFHlr, $outSor);
					$outSor = "";
				}
			}
			//$ma <> $sltModif
			if($ma != $sltModif && $kiirva != true && $fstTO != ""){
				$fhlr01 = fopen($lstTOFlNm, "w");
				fwrite($fhlr01,$fstTO);
				echo "<p>Fsto:{$fstTO} kiirva!</p>";
				$kiirva = true;
				fflush($fhlr01);
				fclose($fhlr01);
			}else{
				echo "<p>";
				echo "Vmi nem volt OK a fileírással.. <br>";
				echo "A \$kiirva értéke: " . $kiirva . "<br>";
				echo "A \$fstTO értéke: " . $fstTO  . "<br>";
				echo "A \$ma értéke: " . $ma . ", \$sltModif = " . $sltModif;
				echo "</p>";
			}
			fflush($outFHlr);
			fclose($outFHlr);
			fclose($fhndlr);
			//<nn>
			//Mivel kész a feldolgozható flat fileunk, ->
			//Itt egy új kapcsolatot nyitunk, hogy a munkaadatabazis legyen az aktív.
			//Majd a MySQLHandler class megfelelő függvényével fdeltöltjük a TOk adatait.
			//</nn>
			$dbCon = new mySQLHandler("beklikk", "munkaadatbazis");
			$resArr =  $dbCon->uploadFlatFile();
			if($resArr[0] == "OK"){
				$retArr = $resArr;
			}else{
				$retArr[0] = "NOK";
				$retArr[1] = $resArr[1];
				$retArr[2] = $resArr[2];
				$retArr[3] = $resArr[3];	
			}
			
		}elseif($flNm === "HU_LIST.TXT"){
			//<nn>
			//H.U.-K ÁTÍRÁSA FLAT FILEBA
			//</nn>
			$fhndlr = fopen($dir . $flNm, "r") or die("Filenyitás (_ {$flNm} _) sikertelen!");
			$outFlNm = "FLAT_HU.TXT";
			$outFHlr = fopen($dir . $outFlNm, "w") or DIE("A kimeneti file-t nem sikerült megnyitni!");
			while($sor = fgets($fhndlr,4096)){
				if(substr($sor,0,4) === "|  4"){
					$aktRec = explode("|",$sor);
					
					//<DEBUG>
					//echo "<pre>";
					//print_r($aktRec);
					//echo "</pre>";
					//</DEBUG>
					$outSor = trim($aktRec[1]);
					$outSor .= "|" . trim($aktRec[2]) . "|" . str_replace(",", "",trim($aktRec[3]));
					$outSor .= "|" . trim($aktRec[4]) . "|" . str_replace(",", "",trim($aktRec[5]));
					$outSor .= "|" . trim($aktRec[6]) . "|" . trim($aktRec[7]) . "|" . trim($aktRec[8]);
					$outSor .= "|" . trim($aktRec[9]) . "|" . trim($aktRec[10]);
					$outSor .= "|" . substr(trim($aktRec[12]), 1,9) . "|\n";
						
					fwrite($outFHlr, $outSor);
					$outSor = "";
				}
			}
			fflush($outFHlr);
			fclose($outFHlr);
			fclose($fhndlr);
			//<nn>
			//Mivel kész a feldolgozható flat fileunk, ->
			//Itt egy új kapcsolatot nyitunk, hogy a munkaadatabazis legyen az aktív.
			//Majd a MySQLHandler class megfelelő függvényével fdeltöltjük a TOk adatait.
			//</nn>	

			$dbCon = new mySQLHandler("beklikk", "munkaadatbazis");
			$flatFlNm = $dir .$outFlNm;
			$tblNm = "tblhutopick";
			$resArr =  $dbCon->uploadFlatFile($flatFlNm, $tblNm);
			if($resArr[0] == "OK"){
				$retArr = $resArr;
			}else{
				$retArr[0] = "NOK";
				$retArr[1] = $resArr[1];
				$retArr[2] = $resArr[2];
				$retArr[3] = $resArr[3];	
			}
		
		}
		else{
			$retArr[0] = "NOK";
			$retArr[1] = "Ismeretlen filenév: (" . $flNm . ")! Ezt nem tudja még feldolgozni a függvény!";
			$retArr[2] = "<br/>Signature:<br/><b>baseCode_HAL.php->createFlatFileForMySQLUpload(\$flNm)<b><br/>";
		}

		return $retArr;
	}

	public function getCode128String($rawString){
	//<SF>
	//×-
	// @-- COPY TO PRODUCTION -@
	// @-- 2016-01-27 -@
	// @-- A függvény egy bemenő stringre egy váalszstringet generál, ami rendelkezik a
	//CODE128B vonalkód ellenőrzőkarakterével!  -@
	// @-- param string $rawString -@
	//-×
	//</SF>
		
		//<nn>
		//A kezdő és zárókarakterek speciálisak. Ezek nélkül is lehet vonalkódot geenrálni, 
		//de azt nem ismeri fel a mi vonalkódolvasónk.
		//</nn>
		$begChr = "Ì";
		$endChr = "Î";
		$chkDig = 104;

		//<nn>
		//Egy for ciklussal végiglépkedve a karakreteken számolgatjuk az ellenőrzőösszeget
		//mert ebből számolódik az utolsó előtti karakter.
		//</nn>
		for($i=0;$i<strlen($rawString);$i++){
			$kar = substr($rawString,$i, 1);
			$karVal = ord($kar)-32;
			$chkDig += ($i+1) * $karVal;
		}
		
		//<nn>
		//Ha megvan az ellenőrzőösszeg kiszámítjuk vele  akaraktert, és beletesszük az
		//eredménystringbe.
		//</nn>
		$chkDig = $chkDig % 103;
		$kar = chr($chkDig+32);
		return $begChr . $rawString . $kar . $endChr;
	}

	public function readWFIPartNumbers(){
	//<SF>
	// 2016-09-16<br>
	// Ez a függvény a DIC-WFI projekt keretében készült.<br>
	// A dolog lényege, hogy automatizálni kellett valahogy a DIC fileból a WFI fileba kerülő tételek
	// átírását. Ezt egy vbscript kezeli (I:\Informatika\ProgRAM\Scriptek\vb_scripts\NAPI_FUTAS\DIC_CFT.vbs.
	// Ez a script átteszi DIC aktuális CFT fileban is szereplő cikkszámait a WFI táblába, és ezeket az 
	// átmásolt cikkszámokat egy TXT fileba is elmenti.<br>
	// Emellett még egy infó is kell a hatékonysághoz, méghozzá az aznapi első TO, ezt egy reggeli script tölti le
	// és teszi el egy TXT fileba. Ha a cikkszámok, és a TO szám megvan, akkor egy újabb VB script (I:\Informatika\
	// ProgRAM\Scriptek\vb_scripts\NAPI_FUTAS\FIND_TO_TO_PNRS.vbs időzített futással letölti a SAPból a cikkszámokhoz
	// tartozó TOkat.<br>
	// Ez a script hívja meg böngészőben ezt a /HAL_WEB_DEV/Script/FUNCTIONAL_SITES/sned_WFI_updateMail.php oldalt, 
	// ami egy lvelet küld az aktuális állapotról.<br>
	// Mindebből ez a függvény csak azt a parányi elemet valósítja meg, hogy beolvasssa a cikkszámokat egy tömbbe, 
	// amit visszaad a hívónak.<br>
	// Forrásfileok:
	//×-
	// @-- A to lista:  -@
	// @-- I:\HAL_Kozos\Project\Fejlesztesek\WHS_DIC_TO_WIFI\PNR_FOR_WFI.TXT = a cikkszámlista -@
	// @-- I:\Informatika\ProgRAM\Scriptek\SAP_DWNLDs\DIC_WFI\AKT_WFI_TOS.TXT = a cikkszámlista -@
	//-×
	// PARAMÉTEREK:
	//×-
	// @-- ... -@
	//-×
	// MÓDOSÍTÁSOK :
	//×-
	// @-- ... -@
	//-×
	//</SF>
		$retArr = array();
		$fCszName = "I:\HAL_Kozos\Project\Fejlesztesek\WHS_DIC_TO_WIFI\PNR_FOR_WFI.TXT";
		$fhlr = fopen($fCszName,"r") or die("A filet: " . $fCszName . " nem sikerült megnyitni! A futás leáll!!!");
		while (!feof($fhlr)) {
			$aktCsz = fgets($fhlr,4096);
			if(trim($aktCsz) !== ""){
				array_push($retArr, $aktCsz);
			}
		}
		
		return $retArr;
	}
	
	public function readWFITOFile(){
	//<SF>
	// Ez a függvény szintén a DIC_WFI projekt keretében készült, és a lényege az hogy egy VBScript által a 
	// SAPból letöltött TO adatokat beolvassa a /HAL_WEB_DEV/Script/FUNCTIONAL_SITES/send_WFI_updateMail.php
	// lap számára.
	// PARAMÉTEREK:
	//×-
	// @-- ... -@
	//-×
	// MÓDOSÍTÁSOK :
	//×-
	// @-- ... -@
	//-×
	//</SF>
		$retArr = array();
		$fCszName = "I:\Informatika\ProgRAM\Scriptek\SAP_DWNLDs\DIC_WFI\AKT_WFI_TOS.TXT";
		$fhlr = fopen($fCszName,"r") or die("A filet: " . $fCszName . " nem sikerült megnyitni! A futás leáll!!!");
		while (!feof($fhlr)) {
			$aktLn = fgets($fhlr,4096);
			if(trim($aktLn) !== ""){
				if(strpos($aktLn,"table entries") !== false){
					array_push($retArr, "NO_TO");
					break;
				}else{
					if(strpos($aktLn,"|RE85") !== false || strpos($aktLn,"|HU01") !== false || strpos($aktLn,"|HU01") !== false){
						$aktRec = array();
						$aktRec = explode("|",$aktLn);
						$aktRec[1] = str_replace(" ", "",$aktRec[1]);
						$aktRec[3] = trim($aktRec[3]);
						$aktRec[5] = trim($aktRec[5]);
						$aktRec[13] = trim($aktRec[13]);
						array_push($retArr, $aktRec);
					}
				}
			}
		}
		return $retArr;		
	}
	
	public function LOCAL_FTP_TEST01($fn=""){
	//<SF>
	// 2016-09-28<br>
	// Ez a függvény a webszerveren beüzelemt FileZilla FTP sezrver elérését teszteli.
	// PARAMÉTEREK:
	//×-
	// @-- nincsenek paraméterek, DEFINE direktivában megadott paramétereket használunk -@
	//-×
	// MÓDOSÍTÁSOK :
	//×-
	// @-- ... -@
	//-×
	//</SF>
		/*if($fn == ""){
			$locF = FTP_LOC_TST_FILE;
		}else{
			$locF = $fn;
		}
		
		$ftp_user_name=FTP_SERVER_USER_TST;
		//$ftp_user_name=FTP_SERVER_USER_TST2;
		$ftp_user_pass=FTP_SERVER_PWD_TST;
		//$ftp_user_pass=FTP_SERVER_PWD_TST2;
		$conn_id = ftp_connect(FTP_SERVER_IP);
		//$conn_id = ftp_connect(FTP_SERVER_IP2);
		if (!$conn_id) {
			echo "<p class=\"ERRMsg\">FTP kapcsolat létrehozása sikertelen!<\p>";
			exit;
		} else {		
			$login_result = ftp_login($conn_id,$ftp_user_name,$ftp_user_pass );
			if(!$login_result){
				echo "<p class=\"ERRMsg\">FTP bejelentkezés sikertelen!<br>( a kapcsolat objektum OK volt, a server megy!)<\p>";
			}else{
				$fNm = "stpTest_" . date("Y_m_d_h_i_s"). ".TXT";
				$upload = ftp_put( $conn_id, $fNm, $locF, FTP_ASCII );
				if(!$upload){
					echo "<p class=\"ERRMsg\">Server = OK, login = OK, de a file feltöltése meghíúsult!</p>" ;
				} else {
					echo "<p class=\"OKMsg\">A <br><b>" . $locF . "</b><br> file feltöltése <br><b>" 
						. $fNm . "</b><br> néven sikeres!<br>Az FTP szerveren megetkintehő!</p>"  ;
				}
			}
		}*/
		
		$ch = curl_init();
		
		
		$localfile = FTP_LOC_TST_FILE;
		$fp = fopen($localfile, 'r');
		curl_setopt($ch, CURLOPT_PROXY, FTP_SERVER_PROXY);
		curl_setopt($ch, CURLOPT_PROXYUSERPWD, CURLOPT_PROXYUSERPWD_VAL );
		curl_setopt($ch, CURLOPT_URL, 'ftp://siro.hu/Temp'.$localfile);
		curl_setopt($ch, CURLOPT_USERPWD, FTP_SERVER_USER_TST2.":".FTP_SERVER_PWD_TST2);
		curl_setopt($ch, CURLOPT_UPLOAD, 1);
		curl_setopt($ch, CURLOPT_INFILE, $fp);
		curl_setopt($ch, CURLOPT_INFILESIZE, filesize($localfile));
		curl_exec ($ch);
		$error_no = curl_errno($ch);
		curl_close($ch);
		if ($error_no == 0) {
			$error = 'File uploaded succesfully.';
		} else {
			$error = 'File upload error.<br>';
			$error .= 'ErrNo: ' . curl_errno($ch) . "<br>";
			$error .= "Error: " . curl_error($ch);
		}
		echo $error;
		
	}
	
	public function generateFTP_SLV_STOCK_FILE(){
	//<SF>
	// 2016-09-28<br>
	// Ez a függvény a SAPból letölött fileból készít egy a szlovén kérésnek megfelelő formátumú filet. 
	// PARAMÉTEREK:
	//×-
	// @-- ... -@
	//-×
	// MÓDOSÍTÁSOK :
	//×-
	// @-- ... -@
	//-×
	//</SF>	
		echo "RUN generateFTP_SLV_STOCK_FILE()";
		$fPath = "I:\\Informatika\\ProgRAM\\Scriptek\\SAP_DWNLDs\\SLV_STOCK\\";
		//$fNm = "AKT_STCK.TXT";
		$fNm = "AKT_STCK_MBEW.TXT";
		$fPathOut = $_SERVER['DOCUMENT_ROOT'] . "/HAL_WEB_DEV/adatFileok/FTP/";
		$fNmOut = "SLV_STCK_FTP.TXT";
		$szlo = 0;
		$outLine = "";
		$filler = "               ";
		$fHlr = fopen($fPath.$fNm, "r") or die("<p class=\"ERRMsg\">Szlovén stock file<br>(".  $fPath . $fNm .
		 	")<br>megnyitása olvasásra sikertelen!</p>");
		$fhLrOut = fopen($fPath.$fNmOut, "w") or die("<p class=\"ERRMsg\">Szlovén FTP file<br>(".  $fPath . $fNm .
		 	")<br>megnyitása írásra sikertelen!</p>");
		
		for($ix1 =1;$ix1<6;$ix1++){$sor=fgets($fHlr,4096);}
		
		while(($sor=fgets($fHlr,4096)) !== false){
			$szlo++;
			if(substr($sor,0,5) != "-----")
			$dataArr = explode("|",$sor);
			$dataArr[3] = DATE("YmdHis");
			$dataArr[1] = trim($dataArr[1]);
			$dataArr[1] = str_replace(" ", "", $dataArr[1]);
			while(strlen($dataArr[1]) < 12){
				$dataArr[1] = $dataArr[1] .  " ";
			}
			while(strlen($dataArr[1]) < 7){
				$dataArr[2] = $dataArr[2] .  " ";
			}
			$outLine = $dataArr[1] . $dataArr[2] . $dataArr[3] . $filler . PHP_EOL;
			fwrite($fhLrOut, $outLine);
		}
		echo "generateFTP_SLV_STOCK_FILE() - DONE";
		return "<p class=\"OKMsg\ >" . $szlo . " sor átírva a kiindulási fileból a célfileba.</p>";	 
	}
	
	
} // END OF HAL_WEB_Base CLASS


?>























