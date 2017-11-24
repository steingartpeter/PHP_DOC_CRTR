<?php
#moddoc:
//<M>
//×-
//@-FILENÉV   : HAL_WEB_DEV - readSQ01Export_HU.php-@
//@-SZERZŐ    : AX07057-@
//@-LÉTREHOZVA:  2017. aug. 17.-@
//@-FÜGGŐSÉGEK:
//×-
// @-- baseCode_HAL.php-@
// @-- EKAER2_reqNrfromNAV.php-@
//-×
//-@
//@-LEÍRÁS    :
// Ez a site a kifelé menő kamionok EKAER szám kérését kezeli.<br>
// A működéséhez egy SAP query adataira van szüksége, amit a felhasználónak le kell töltenie az SQ01
// -es tranzakcióból.<br/><hr/>A site a letöltött file-t automatikusan beolvassa (emiatt fix helyre, és
// fix néven kell előállítani a file-t).
//-@
//@-MÓDOSÍTÁSOK :
//×-
// @-- 2017-11-23 
//×-
// @-- refractoring -@
// @-- részfunciók függvényekbe foglalása -@
// @-- maygar kiszállítás specifikus kód elkezdése -@
//-×
//-@
//-×
//-@
//-×
//</M>
	ob_start();
	include_once $_SERVER['DOCUMENT_ROOT']."/HAL_WEB_DEV/Script/baseCode_HAL.php";
	$pgGnrtr = new HAL_WEB_Base();
	$kepUrl = "/HAL_WEB_DEV/pix/ekaer_test01.png";
	echo $pgGnrtr->HALAlapLapTeteje();
	echo createSiteHTMLUpper();
	
	
	
	//<nn>
	// Megnyitjuk a SAP exportot, a részletek beolvasásához. FOPEN=>AKT_EKR_OUT.TXT
	//</nn>
	$fhlr = fopen("I:/_Exchange_Insite/SAP_DWNLDS/EKAER02_OUT/AKT_EKR_OUT.TXT","r") or die("<p class=\"ERRMsg\">Filet nem sikerült megnyitni!</p>");	
	
	$ttlek = array();
	$gngylg = array();
	$plntArr = array();
	$valArr = array();
	$szallSzam = "";
	$route = "";
	$frsz = "";
	$kell_e_szam = FALSE;
	
	//<nn>
	// Az első 7 sort átugorjuk, mert ott csak a SAP által generált fejléc van.
	//</nn>
	for($ix1 = 0;$ix1<7;$ix1++){
		$ln = fgets($fhlr);
	}
	//<DEBUG>
	// Debug leírás:
	//<code>
	// echo "<p>A jelenlegi sor:<br>";
	// echo $ln ."</p>";
	//</code>
	//</DEBUG>
	
	
	//<nn>
	// Inicializáljuk a munkatömböket.
	//</nn>
	$plntArr[0]="HU01";
	$plntArr[1]="NI01";
	$plntArr[2]="RE85";
	$plntArr[3]="OTHER";
	
	$valArr['hu01'] = 0;
	$valArr['ni01'] = 0;
	$valArr['re85'] = 0;
	$valArr['oth'] = "#";
	
	$frszek = array();
	$frszOrsz = "";
	
	//<nn>
	// Az itemeket tároló XML fileoknak létrehozunk 3 simpleXML elemet.
	//</nn>
	$itmsHU01 = new SimpleXMLElement("<items></items>");
	$itmsNI01 = new SimpleXMLElement("<items></items>");
	$itmsRE85 = new SimpleXMLElement("<items></items>");
	
	
	
	
	while($ln = fgets($fhlr)){
	//<nn>
	// File olvasása filevégig...
	//</nn>
	
		//<nn>
		// Kétféle sort kezelünk:
		//×-
		// @-- A kollikat, és cikkszámokat tartalmazó sort -@
		// @-- A csak cikkszámokat tartalmazó sort. -@
		//-×
		// A többi sort csak átugorjuk.
		//</nn>
		if(substr($ln,0,8) == "|       "){
		//<nn>
		// Kollis header sorok kezelése.
		//</nn>
			$aktRec = explode("|",$ln);
			
			//<nn>
			// Nem csak a szállítmányszám az amire szükség van...
			//</nn>
			if($szallSzam == ""){
				$szallSzam = $aktRec[1];
			}
			//<nn>
			// Kell a ROUTE is.<br>
			// A route-ot csak egyszer töltjük fel.
			//</nn>
			if($route == ""){
				$route = trim($aktRec[14]);
			}
			
			if($route !== "HU-15"){
			//<nn>
			// Az országon kivüi fuvarok kezelése:
			//</nn>
			
				//<nn>
				//Ja!, meg mostantól a rendszám is, ami egy új táblában van  -> VTTK-SIGNI
				//</nn>
				if($frsz == ""){
					getFRENDSZ($aktRec[15], $frsz,$frszek,$frszOrsz);
				}
				//<nn>
				// Létrehozzuk az aktItem XML elemet a megfelelő item tömbbe.
				//</nn>
				createAktItem($aktRec,$valArr,$itmsHU01,$itmsNI01,$itmsRE85);
				//<nn>
				// A get_PKG_WIGHT függvény segítségével a csomagoló anyag nevének függvényében(aktrec[4]) megadjuk annak súlyát grammban.
				//</nn>
				$aktRec[11] = get_PKG_WIGHT($aktRec[4]);
				//<nn>
				// A LOOSE itemeket, és a VIRTUAL-okat eldobjuk!<br>
				// A maradékot pedig a csak egyszer tesszük be a tömbbe, annak köszönhetően,
				// hogy az in_array, és array_column függvényekkel megnézzük, hogy az internal HU ID
				// értéke (array[4]) benne van-e már a gyűjteményben.<br>
				//</nn>
				if(trim($aktRec[4]) != "PKG_LOOSE" && trim($aktRec[4]) != "VIRTUAL" && trim($aktRec[4]) != ""){
					if(! in_array($aktRec[3], array_column($gngylg,3))){
						array_push($gngylg,$aktRec);
					}
				}
					
			}else{
			//<nn>
			// Az országon belüli fuvarok kezelése.<br>
			// Pillanatnyilag dolgozunk rajta...
			//</nn>
				echo "<p class=\"ERRMsg\">MAGYAR SZÁLLÍTMÁNY? GÁZ VAN!<br>
					Sajnos ennek a kezelésére, még nincs PHP kód, jelenleg dolgozunk rajat!</p>";
				return;
			}
		}elseif(substr($ln,0,8) == "|*      "){
		//<nn>
		// Kolli nélküli, sima tételsorok kezelése.
		//</nn>
			if($route !== "HU-15"){
				//<nn>
				// Az országon kivüi fuvarok kezelése:
				//</nn>
				$aktRec = explode("|",$ln);
				array_push($ttlek,$aktRec);
			}else{
			//<nn>
			// Az országon belüli fuvarok kezelése.<br>
			// Pillanatnyilag dolgozunk rajta...
			//</nn>
				echo "<p class=\"ERRMsg\">MAGYAR SZÁLLÍTMÁNY? GÁZ VAN!<br>
					Sajnos ennek a kezelésére, még nincs PHP kód, jelenleg dolgozunk rajat!</p>";
				return;
			}
		}
	}
	
	
	//<nn>
	// A súlyértékek kiszámítása, összegzése a tömbök alapján, plantonként.<br>
	// Külön árukra, és gonygölegekre.
	//</nn>
	$wghtArr = fillWghtArray($gngylg,$ttlek);

	//<DEBUG>
	// Debug leírás:<br>
	// Itt kiirathatjuk a tömböket, hogy lássuk, minden benne van-e:
	//<code>
	// writeRawArrayData($wghtArr,$valArr);
	//</code>
	//</DEBUG>
	
	//<nn>
	// A göngyölegtételt hozzáadjuk a tételek sorához, hogy azok is megjelenjenek az EKAER kérő XML-ben.
	//</nn>
	addPKGWghtItem($itmsRE85,$itmsNI01,$itmsHU01,$wghtArr);
	
	//<nn>
	// Kiíratjuk egy HTML táblában az eredményt.
	//</nn>
	writeResultToSite($szallSzam,$route,$wghtArr,$valArr,$frsz,$frszek,$frszOrsz);
	
	//<nn>
	// Lezárjuk az oldal kódját.
	//</nn>	
	echo '</div></div>';
	
	
	//<nn>
	// Az itemek xml filejai fel vannak töltve, kiírjuk őket:
	//</nn>
	writeOutItemsXML($itmsHU01,$itmsNI01,$itmsRE85);
	
	
?>

<?php 
	//<nn>
	// Az oldal standard alsó részének legenerálása.
	//</nn>
	echo $pgGnrtr->halAlapOldalAlja();
?>




<?php 
//<nn>
// PHP kód a részfeleadtok megvalósításához.<br><hr>
// A következő PHP szakaszban a szükséges (rész) funkciókat egy-egy függvénybe foglalom, hogy átláthatóbb legyen a kód.
//</nn>

function createSiteHTMLUpper(){
//<SF>
// 2017. nov. 24.<br>
// Az oldal HTML kódjának felső részét legeneráló alfüggvény.<br>
// Erre azért volt szükség, hogy valamilyen módon átláthatóbbá tegyem a felső rész funkcionális kódját.<br>
// PARAMÉTEREK:
//×-
// @-- NINCSENEK paraméterek -@
//-×
//MÓDOSTÁSOK:
//×-
// @-- ... -@
//-×
//</SF>

	//<nn>
	// Legeneráljuk, majd visszaadjuk az oldal kódját.
	//</nn>
	$htmlC = "";
	$htmlC .= "<h2> EKAER kimenő file beolvasása:</h2>";
	$htmlC .= '<div class="alapRaktarDiv">';
	$htmlC .= '<h2 class="kovBecsuk"><i class="fa fa-minus pl-min-toggler" aria-hidden="true"></i>  Útmutató:</h2>';
	$htmlC .= "<div><ol>";
	$htmlC .= "<li>Le kell tölteni SAPból a szállítmány adatait:</li>";
	$htmlC .= "<ul><li>Menjünk az SQ01-es tranzakcióba, és válassszuk a <span class=\"kiemeles\">QUE_AX07057_99</span> nevű queryt.</li>";
	$htmlC .= "<li>Adjuk meg a (0-k után !!!) a szállítmányszámot:<br>
		<img src=\"/HAL_WEB_DEV/UserGuides/USRGD_PIX/EKR_OUT/EKR_OUT_0011.JPG\"><br/>
		<hr>
		<img src=\"/HAL_WEB_DEV/UserGuides/USRGD_PIX/EKR_OUT/EKR_OUT_0012.JPG\"><br/>
		</li>";
	$htmlC .= "</ul><li>";
	$htmlC .= "A megjelenő lsitát TXT fileként mentsük el:<br>
		<img src=\"/HAL_WEB_DEV/UserGuides/USRGD_PIX/EKR_OUT/EKR_OUT_0013.JPG\"><br/></li>";
	$htmlC .= "<li>A file helye: I:\_Exchange_Insite\SAP_DWNLDS\EKAER02_OUT\</li>";
	$htmlC .= "<li>A file neve: AKT_EKR_OUT.txt<br>
		<img src=\"/HAL_WEB_DEV/UserGuides/USRGD_PIX/EKR_OUT/EKR_OUT_0014.JPG\">
		<br><span class=\"code\">(Használjuk a REPLACE gombot!!!)</span>";
	$htmlC .= "</li>";
	$htmlC .= "</ul>";
	$htmlC .= "</ol>";
	$htmlC .= "<p>A file elérése: I:\_Exchange_Insite\SAP_DWNLDS\EKAER02_OUT\AKT_EKR_OUT.TXT</p></div>";
	
	//<nn>
	// A rendezett HTML sztringet visszaadjuk, megjelenítésre.
	//</nn>
	return $htmlC;
}

function get_PKG_WIGHT($pkgNm){
//<SF>
// 2017. nov. 23.<br>
// LEÍRÁS<br>
// A csomagolóanyagok súlyát kikereső függvényt külön implementáltam.<br>
// A végelges helyének jobb lenne mondjuk a BASE_HAL.php file.<br>
// PARAMÉTEREK:
//×-
// @-- @param $pkgNm = a csonmagoló anyag megnevezése aminek a súlyát keressük -@
//-×
//MÓDOSTÁSOK:
//×-
// @-- ... -@
//-×
//</SF>
	
	//<nn>
	// Egy hosszú SWITCH szerkezettel kiválasztjuk, hogy melyik csomagolóaynag súly a megfelelő.<br>
	// Fontos, hogy új csomagolóanyagok megjelenésével, vagy a meglévők súlyaának változásval, az itteni
	// kód karbantartása szükséges lesz!
	//</nn>
	$pkgWght = 1;
	switch (trim($pkgNm)) {
		case "C1003038":
			$pkgWght = 1540;
			break;
		case "C1003 038NE W":
			$pkgWght = 1869.010;
			break;
		case "C1303240":
			$pkgWght = 2700;
			break;
		case "C1303250":
			$pkgWght = 3000;
			break;
		case "C1303 250NE W":
			$pkgWght = 2564;
			break;
		case "C1303280":
			$pkgWght = 3750;
			break;
		case "C202038":
			$pkgWght = 406;
			break;
		case "C21060100":
			$pkgWght = 9000;
			break;
		case "C2106 0100N EW":
			$pkgWght = 8878;
			break;
		case "C603038":
			$pkgWght = 1260;
			break;
		case "CBOU_2840":
			$pkgWght = 26000;
			break;
		case "CGBO_3240":
			$pkgWght = 30000;
			break;
		case "CPOR_2400":
			$pkgWght = 25000;
			break;
		case "CPOR_2630":
			$pkgWght = 26000;
			break;
		case "CPOR_3630":
			$pkgWght = 38000;
			break;
		case "C_148":
			$pkgWght = 12000;
			break;
		case "C_175":
			$pkgWght = 21000;
			break;
		case "C_268":
			$pkgWght = 19500;
			break;
		case "D107":
			$pkgWght = 456;
			break;
		case "D107BB":
			$pkgWght = 220;
			break;
		case "D107NEW":
			$pkgWght = 323;
			break;
		case "D172":
			$pkgWght = 141;
			break;
		case "D173":
			$pkgWght = 1760;
			break;
		case "D174":
			$pkgWght = 202;
			break;
		case "D174BB":
			$pkgWght = 193;
			break;
		case "D174NEW":
			$pkgWght = 298.44;
			break;
		case "D175":
			$pkgWght = 770;
			break;
		case "D175BB":
			$pkgWght = 770;
			break;
		case "D175NEW":
			$pkgWght = 720;
			break;
		case "ENVELOPE1":
			$pkgWght = 32;
			break;
		case "ETM_1700":
			$pkgWght = 95000;
			break;
		case "FELNI DOBOZ":
			$pkgWght = 1091;
			break;
		case "HENGER":
			$pkgWght = 972;
			break;
		case "HOSSZUKARTONDOBOZ":
			$pkgWght = 640;
			break;
		case "KISRAKLAP_VRT":
			$pkgWght = 15000;
			break;
		case "LIM_Q TY_VI RT":
			$pkgWght = 0;
			break;
		case "MPR1300":
			$pkgWght = 156000;
			break;
		case "NAGYRAKLAP_VRT":
			$pkgWght = 20000;
			break;
		case "NEWPLASTICBOXNR":
			$pkgWght = 3500;
			break;
		case "PKG-FICTIF":
			$pkgWght = 0;
			break;
		case "PKG_F ICTIF":
			$pkgWght = 0;
			break;
		case "PKG_LOOSE":
			$pkgWght = 0;
			break;
		case "PLAST ICBAG":
			$pkgWght = 1;
			break;
		case "PLAST ICBOX":
			$pkgWght = 0;
			break;
		case "R1AMS":
			$pkgWght = 58000;
			break;
		case "R1WIEN":
			$pkgWght = 87500;
			break;
		case "R2AMS":
			$pkgWght = 97000;
			break;
		case "R2WIEN":
			$pkgWght = 136000;
			break;
		case "R3AMS":
			$pkgWght = 82000;
			break;
		case "R4AMS":
			$pkgWght = 122000;
			break;
		case "R5AMS":
			$pkgWght = 83000;
			break;
		case "RCL":
			$pkgWght = 83000;
			break;
		case "RCS":
			$pkgWght = 70500;
			break;
		case "SLI_760":
			$pkgWght = 95000;
			break;
		case "SLOVENIA_DACIA_VRT":
			$pkgWght = 1000;
			break;
		case "SMALLCAGE":
			$pkgWght = 63500;
			break;
		case "SMALLPLASTICBOXNR":
			$pkgWght = 3000;
			break;
		case "VIRTUAL":
			$pkgWght = 1;
			break;
		default:
			$pkgWght = trim($pkgNm);
			break;
		}
		
	//<nn>
	// Ha megvan a súly, csak vissaz kell adni a hívónak.
	//</nn>
	return $pkgWght;
}

function getFRENDSZ($f,&$frsz,&$frszek,&$frszOrsz){
//<SF>
// 2017. nov. 23.<br>
// A rendszám megállapítása, ha lehetséges...<br>
// PARAMÉTEREK:
//×-
// @-- @param $f = a nyers rendszámsztring, amit trimmelünk. -@
// @-- @param &$frsz = egy referencia a hivókörnyezet rendszámváltozójára, hogy itt átírhassuk azt. -@
// @-- @param &$frszek =  egy referencia a hivókörnyezet rendszám tömbjére, hogy itt belepakolhassunk. -@
// @-- @param &$frszOrsz = egy referencia a rendszám-ország változójár, hogy helyben írhassuk renszám . -@
// @-- @param $f = a nyers rendszámsztring, amit trimmelünk. -@
//-×
//MÓDOSTÁSOK:
//×-
// @-- ... -@
//-×
//</SF>	
	
	
	$frsz = trim($f);
	
	//<nn>
	// Akor próbáljuk kitalálni a két rendszámot...
	//</nn>

	if(strpos($frsz,"/")){
		//<nn>
		// Ha megtaláltuk a pótkocsi, és a vontatókocsi rendszámát
		// elválasztó karaktert, akkor explodozzuk a cuccot
		//</nn>
		$frszek = explode("/",strtoupper($frsz));
		//<nn>
		// Kigyomláljuk a lehetséges felesleges karaktereket
		//</nn>
		$frszek[0] = str_replace(" ", "",$frszek[0]);
		$frszek[1] = str_replace(" ", "",$frszek[1]);
		$frszek[0] = str_replace("-", "",$frszek[0]);
		$frszek[1] = str_replace("-", "",$frszek[1]);
	}elseif(strpos($frsz,"\\")){
		$frszek = explode("\\",strtoupper($frsz));
		$frszek[0] = str_replace(" ", "",$frszek[0]);
		$frszek[1] = str_replace(" ", "",$frszek[1]);
		$frszek[0] = str_replace("-", "",$frszek[0]);
		$frszek[1] = str_replace("-", "",$frszek[1]);
	}elseif(strpos($frsz,"-",5)){
		$frszek = explode("-",strtoupper($frsz));
		$frszek[0] = str_replace(" ", "",$frszek[0]);
		$frszek[1] = str_replace(" ", "",$frszek[1]);
	}elseif(strpos($frsz,"  ",5)){
		//<nn>
		// Itt kezdődnek a gondok...
		// Az egy, és két SPACE-es estet kezeljük, de ha hármat tesz majd a plgár bele,
		// akkor gáz lesz, mert a tömb nem 2 hanem 3 elemű lesz
		//</nn>
		$frszek = explode("  ",strtoupper($frsz));
		$frszek[0] = str_replace("-", "",$frszek[0]);
		$frszek[1] = str_replace("-", "",$frszek[1]);
	}
	elseif(strpos($frsz," ",5)){
		$frszek = explode(" ",strtoupper($frsz));
		$frszek[0] = str_replace("-", "",$frszek[0]);
		$frszek[1] = str_replace("-", "",$frszek[1]);
	}else{
		//<nn>
		// A furcs eset amikor csak egy rendszám van, többnyire ez az AT-MUL/DIR
		// reggeli kocsi.
		//</nn>
		$frszek[0] = $frsz;
		$frszek[1] = "";
	}

	//<nn>
	// Akkor próbáljuk kitalálni az országot is.<br>
	// Pár regulásris kifejezéssel opreálva teszünk erre kiísérletet.
	//×-
	// @-- 1 szám+2 betű+4 szám = CZ -@
	// @-- 3 betű+3 szám = H -@
	// @-- 2 betű+3szám+2betű = SK -@
	//-×
	//</nn>
	$frszOrsz = "??";
	if(preg_match("/([0-9]{1}[A-Z]{1}[0-9]{5})/", $frszek[0]) || preg_match("/([0-9]{1}[A-Z]{2}[0-9]{4})/", $frszek[0])){
		$frszOrsz = "CZ";
	}elseif(preg_match("/([A-Z]{3}[0-9]{3})/", $frszek[0])){
		$frszOrsz = "H";
	}elseif(preg_match("/([A-Z]{2}[0-9]{3}[A-Z]{2})/", $frszek[0])){
		$frszOrsz = "SK";
	}elseif(preg_match("/([A-Z]{2}[0-9]{3}[A-Z]{2})/", $frszek[0])){
		$frszOrsz = "SK";
	}
	
}

function createAktItem($aktRec,&$valArr,$itmsHU01,$itmsNI01,$itmsRE85){
//<SF>
// 2017. nov. 24.<br>
// Az aktuális tétel XML elem összeállítása.<br>
// PARAMÉTEREK:
//×-
// @-- @param $plnt = plant: HU01/NI01/RE85 -@
// @-- @param $pnr = cikkszám (aktRec[6]) -@
// @-- @param &$valArr = referencia az értéktömbre -@
//-×
//MÓDOSTÁSOK:
//×-
// @-- ... -@
//-×
//</SF>
	
	//<DEBUG>
	// Az aktuális sorból készült tömb megnézése:
	//<code>
	// echo "<p><pre>";
	// print_r($aktRec);
	// echo "</pre></p>";
	//</code>
	//</DEBUG>

	//<nn>
	// A planttól függően (ami az aktRec 3. eleme) a megfelelő érték tömb elemet megnöveljük.
	//</nn>
	if(strtoupper(trim($aktRec[2]))=="HU01"){
		$valArr['hu01'] += str_replace(",","",$aktRec[13]);
		//<nn>
		// Létrehozzuk egy új XML elementet az aktuális itemnek, és az elemeit feltöltjük a tömbből.
		//</nn>
		$aktItm = $itmsHU01->addChild("tradeCardItem");
		$trdRsn = $aktItm->addChild("tradeReason", "S");
		$pNmStr = utf8_encode($aktRec[6]);
		$pNmStr = str_replace("&","",$pNmStr);
		//<nn>
		//Ha magyar nevket töltünk le, akkor az utf8_encode elrontja a stringet
		// $pNm = $aktItm->addChild("productName", utf8_encode($pNmStr));
		//</nn>
		$pNm = $aktItm->addChild("productName", trim($pNmStr));
		$pVtsz = $aktItm->addChild("productVtsz", substr(trim($aktRec[8]),0,4));
		$pWght = $aktItm->addChild("weight", str_replace(",","",trim($aktRec[11]))/1000);
		$pVal = $aktItm->addChild("value",intval(str_replace(",","",trim($aktRec[13]))));
		$pPnr = $aktItm->addChild("importerItemNumber",trim($aktRec[5]));
	}elseif(strtoupper(trim($aktRec[2]))=="NI01"){
		$valArr['ni01'] += str_replace(",","",$aktRec[13]);
		//<nn>
		// Létrehozzuk egy új XML elementet az aktuális itemnek, és az elemeit feltöltjük a tömbből.
		//</nn>
		$aktItm = $itmsNI01->addChild("tradeCardItem");
		$trdRsn = $aktItm->addChild("tradeReason", "S");
		$pNmStr = utf8_encode($aktRec[6]);
		$pNmStr = str_replace("&","",$pNmStr);
		//<nn>
		//Ha magyar nevket töltünk le, akkor az utf8_encode elrontja a stringet
		// $pNm = $aktItm->addChild("productName", utf8_encode($pNmStr));
		//</nn>
		$pNm = $aktItm->addChild("productName", trim($pNmStr));
		$pVtsz = $aktItm->addChild("productVtsz", substr(trim($aktRec[8]),0,4));
		$pWght = $aktItm->addChild("weight", str_replace(",","",trim($aktRec[11]))/1000);
		$pVal = $aktItm->addChild("value",intval(str_replace(",","",trim($aktRec[13]))));
		$pPnr = $aktItm->addChild("importerItemNumber",trim($aktRec[5]));
	}elseif(strtoupper(trim($aktRec[2]))=="RE85"){
		$valArr['re85'] += str_replace(",","",$aktRec[13]);
		//<nn>
		// Létrehozzuk egy új XML elementet az aktuális itemnek, és az elemeit feltöltjük a tömbből.
		//</nn>
		$aktItm = $itmsRE85->addChild("tradeCardItem");
		$trdRsn = $aktItm->addChild("tradeReason", "S");
		$pNmStr = trim($aktRec[6]);
		$pNmStr = str_replace("&","",$pNmStr);
		//<nn>
		// Nem tudom ez miért van de itt meg KELL az utf8_encode...
		//</nn>
		$pNm = $aktItm->addChild("productName", trim(utf8_encode($pNmStr)));
		$pVtsz = $aktItm->addChild("productVtsz", trim($aktRec[8]));
		//<DEBUG>
		// Ha akrjuk a problémás VTSZ-eket itt kicserélhatjük egy általánosra!:
		//<code>
		// $vtsz = substr(trim($aktRec[8]),0,4);
		// if($vtsz == "3403" || $vtsz == "3811" || $vtsz == "3824"){
		// 	$pVtsz = $aktItm->addChild("productVtsz", "8707");
		// }else{
		// 	$pVtsz = $aktItm->addChild("productVtsz", substr(trim($aktRec[8]),0,4));
		// }
		//</code>
		//</DEBUG>
	
		$pWght = $aktItm->addChild("weight", str_replace(",","",trim($aktRec[11]))/1000);
		$pVal = $aktItm->addChild("value",intval(str_replace(",","",trim($aktRec[13]))));
		$pPnr = $aktItm->addChild("importerItemNumber",trim($aktRec[5]));
	}else{
		$valArr['oth'] += str_replace(",","",$aktRec[13]);
	}
}

function fillWghtArray($gngylg,$ttlek){
//<SF>
// 2017. nov. 24.<br>
// A göngyöleg, és áru súly-tömbök feltöltése.<br>
// PARAMÉTEREK:
//×-
// @-- @param $gngylg = a göngyölegsúlyokat tartalmazó munkatömb -@
// @-- @param $ttlek = a tételek súlyait tartalmazó munkatömb -@
//-×
//MÓDOSTÁSOK:
//×-
// @-- ... -@
//-×
//</SF>

	//<nn>
	// Itt egy rendezőfüggvényt definiálunk két tömbelemre, ami majd az usort függvénynek segít a
	// tömböt rendezni.
	//</nn>
	function cmpr($a,$b){
		return $a[3] - $b[3];
	};
	
	usort($gngylg, "cmpr");
	
	$wghtArr = array();
	
	//<nn>
	// A wghtArr tömbüket kibővítjük, áru, és gönygölegsúlyokra, plantonként.<br>
	// A biztonság kedvéért meghagyunk egy plusz tipus az ismeretlen/hiányzó platoknak.
	//</nn>
	$wghtArr['aru']['hu01'] = 0;
	$wghtArr['aru']['re85'] = 0;
	$wghtArr['aru']['ni01'] = 0;
	$wghtArr['aru']['oth'] = "#";
	
	$wghtArr['gongy']['hu01'] = 100;
	$wghtArr['gongy']['re85'] = 100;
	$wghtArr['gongy']['ni01'] = 100;
	$wghtArr['gongy']['oth'] = "#";
	
	//<nn>
	// A tömb egyes elemeit (göngy./áru, HU01/NI01/RE85 feltöltjük a gngylg, és ttlek tömbökből.<br>
	// For ciklussal járjuk be a forrástömböket és számmá alakítva beszúrjuk az adatokat a céltömbökbe.
	//</nn>
	for($i =0; $i<sizeof($gngylg);$i++){
		if(strtoupper(trim($gngylg[$i][2])) == "HU01"){
			$wghtArr['gongy']['hu01'] += trim(str_replace(",","",$gngylg[$i][11]));
		}else if(strtoupper(trim($gngylg[$i][2])) == "RE85"){
			$wghtArr['gongy']['re85'] += trim(str_replace(",","",$gngylg[$i][11]));
		}else if(strtoupper(trim($gngylg[$i][2])) == "NI01"){
			$wghtArr['gongy']['ni01'] += trim(str_replace(",","",$gngylg[$i][11]));
		}else{
			$wghtArr['gongy']['oth'] = $gngylg[$i][4] . "<->" . $gngylg[$i][11];
		}
	}
	
	
	for($i =0; $i<sizeof($ttlek);$i++){
		if(strtoupper(trim($ttlek[$i][2])) == "HU01"){
			$wghtArr['aru']['hu01'] += str_replace(",","",$ttlek[$i][11]);
		}else if(strtoupper(trim($ttlek[$i][2])) == "RE85"){
			$wghtArr['aru']['re85'] += str_replace(",","",$ttlek[$i][11]);
		}else if(strtoupper(trim($ttlek[$i][2])) == "NI01"){
			$wghtArr['aru']['ni01'] += str_replace(",","",$ttlek[$i][11]);
		}else{
			$wghtArr['aru']['oth'] = $ttlek[$i][4] . "<->" . $ttlek[$i][11];
		}
	}
	
	//<nn>
	// A feltöltött wghtArr tömböt visszadjuk a hívó környezetnek.
	//</nn>
	return $wghtArr;
}

function addPKGWghtItem($itmsRE85,$itmsNI01,$itmsHU01,$wghtArr){
//<SF>
// 2017. nov. 24.<br>
// Ebben a függvényben minden plant tételeihez adunk egy göngyöleg tételt, arra az esetre, hogy ekerüljük azt a 
// problémát, hogy nincs feltöltve az előre beállított göngyöleg item, mert mondjuk egy extra kocsin csak loose item van.<br>
// PARAMÉTEREK:
//×-
// @-- @param $itmsRE85 = a RE85 planthoz tartozó tételek tömbje -@
// @-- @param $itmsNI01 = a NI01 planthoz tartozó tételek tömbje -@
// @-- @param $itmsHU01 = a HU01 planthoz tartozó tételek tömbje -@
// @-- @param $wghtArr = a súlyok tömbje -@
//-×
//MÓDOSTÁSOK:
//×-
// @-- ... -@
//-×
//</SF>	

	//<nn>
	// A RE85 göngyölegtétel hozzáadása...
	//</nn>
	$aktItm = $itmsRE85->addChild("tradeCardItem");
	$trdRsn = $aktItm->addChild("tradeReason", "S");
	$pNm = $aktItm->addChild("productName", "Gönygöleg");
	$pVtsz = $aktItm->addChild("productVtsz", "6809");
	$pWght = $aktItm->addChild("weight", number_format(($wghtArr['gongy']['re85']/1000),2,"."," "));
	$pVal = $aktItm->addChild("value", 1);
	$pPnr = $aktItm->addChild("importerItemNumber", "00 00 000 000");
	
	//<nn>
	// A NI01 göngyölegtétel hozzáadása...
	//</nn>
	$aktItm = $itmsNI01->addChild("tradeCardItem");
	$trdRsn = $aktItm->addChild("tradeReason", "S");
	$pNm = $aktItm->addChild("productName", "Gönygöleg");
	$pVtsz = $aktItm->addChild("productVtsz", "6809");
	$pWght = $aktItm->addChild("weight", number_format(($wghtArr['gongy']['ni01']/1000),2,"."," "));
	$pVal = $aktItm->addChild("value", 1);
	$pPnr = $aktItm->addChild("importerItemNumber", "00 00 000 000");
	
	//<nn>
	// A HU01 göngyölegtétel hozzáadása...
	//</nn>
	$aktItm = $itmsHU01->addChild("tradeCardItem");
	$trdRsn = $aktItm->addChild("tradeReason", "S");
	$pNm = $aktItm->addChild("productName", "Gönygöleg");
	$pVtsz = $aktItm->addChild("productVtsz", "6809");
	$pWght = $aktItm->addChild("weight", number_format(($wghtArr['gongy']['hu01']/1000),2,"."," "));
	$pVal = $aktItm->addChild("value", 1);
	$pPnr = $aktItm->addChild("importerItemNumber", "00 00 000 000");
	
}

function writeResultToSite($szallSzam,$route,$wghtArr,$valArr,$frsz,$frszek,$frszOrsz){
//<SF>
// 2017. nov. 24.<br>
// Ez a függvény a beolvasott file feldolgozása után egy HTML tálát állít össze, 
// majd annak a kódját a lapba szúrja.<br>
// Ez a kód vizsgálja meg, hogy szükséges-e EKAER számot kérni, és FROM elemeit eszerint alakítja ki.<br>
//×-
// @-- Amennyiben nem kell EKAER szám, egy egy feliratot jelenit meg az utolsó táblacellában, ami erről tárjékoztatja a felhasználót -@
// @-- Amennyiben kell EKAER szám, egy gomb jelenik meg, ami a FROM submitja lesz, és egy másik sitera viszi a felhasználót -@
//-×
// PARAMÉTEREK:
//×-
// @-- @param $szallSzam = SAP shipment number -@
// @-- @param $route = SAP route code -@
// @-- @param $wghtArr = A súlyokat tartalmazó tömb -@
// @-- @param $valArr = Az értékeket (Ft) tartalmazó tömb -@
// @-- @param $frsz = A rendszám, ha egy van -@
// @-- @param $frszek = A rendszámok tömbje, ha több van -@
// @-- @param $frszOrsz = A rendszám felségjelzése -@
//-×
//MÓDOSTÁSOK:
//×-
// @-- ... -@
//-×
//</SF>

	//<nn>
	// EKAER szám kérésének szükségességét jelző bool flag.
	//</nn>
	$kell_e_szam_r = FALSE;
	$kell_e_szam_n = FALSE;
	$kell_e_szam_h = FALSE;

	
	//<nn>
	// Elkezdjük összeállítani a html table-t, ami egy fomr lesz valójában.
	//</nn>
	$htmlTbl = "<p></p>";
	$htmlTbl .= '<form action="EKAER2_reqNrfromNAV.php" method="POST">';
	$htmlTbl .= "<table class=\"mid-align-td\">";

	$htmlTbl .= '<tr><th>Shpmnt.</th><th colspan="3">' . substr($szallSzam,-5,5) .'</th></tr>';
	$htmlTbl .= '<tr><th>Route</th><th colspan="3">' . $route .'</th></tr>';
	$htmlTbl .= '<tr><th>PLANT</th><th>RE85</th><th>NI01</th><th>HU01</th></tr>';
	$htmlTbl .= '<tr><th>ÖSSZSÚLY(KG)</th>';
	//<nn>
	// Megvizsgáljuk (PLANT-onként), hogy a teljes súly meghaladja-e az EKAER köteles mértéket (2500 KG).<br>
	//×-
	// @-- Ha igen, akkor a súly mezőnek &quotproblem&quot; css class-t adunk, és a jelző flag-et beállítjuk TRUE-ra -@
	// @-- ha nem, csak egy sima td-t adunk a HTML kódhoz. -@
	// @-- item text 03 -@
	// @-- ... -@
	//-×
	//</nn>
	$totWght = ($wghtArr['aru']['re85']/1000)+($wghtArr['gongy']['re85']/1000);
	if ($totWght >= 2500.001){
		$htmlTbl .= "<td class=\"problem\">";
		$kell_e_szam_r = TRUE;
	}else{
		$htmlTbl .= "<td>";
	}
	$htmlTbl .= number_format($totWght,2,","," ") ." KG</td>";
	$totWght = ($wghtArr['aru']['ni01']/1000)+($wghtArr['gongy']['ni01']/1000);
	if ($totWght >= 2500.001){
		$htmlTbl .= "<td class=\"problem\">";
		$kell_e_szam_n = TRUE;
	}else{
		$htmlTbl .= "<td>";
	}
	$htmlTbl .= number_format($totWght,2,","," ") ." KG</td>";
	$totWght = ($wghtArr['aru']['hu01']/1000)+($wghtArr['gongy']['hu01']/1000);
	if ($totWght >= 2500.001){
		$htmlTbl .= "<td class=\"problem\">";
		$kell_e_szam_h = TRUE;
	}else{
		$htmlTbl .= "<td>";
	}
	$htmlTbl .= number_format($totWght,2,","," ") ." KG</td></tr>";

	//<nn>
	// Megvizsgáljuk, hogy az érték nem túl nagy-e - > 5 000 000 Ft<br>
	//×-
	// @-- ha igen, akkor a $kell_e_szam flaget TRUE-ra állítjuk + problem CSS class. -@
	// @-- ha nem, akkor csak kírunk normál mezőbe. -@
	//-×
	//</nn>
	$htmlTbl .= '<tr><th>ÖSSZÉRTÉK</th>';
	if($valArr['re85'] >= 5000000){
		$htmlTbl .= "<td class=\"problem\">";
		$kell_e_szam_r = true;
	}else{
		$htmlTbl .= "<td>";
	}
	$htmlTbl .= number_format(($valArr['re85']),2,","," ") . " Ft</td>";

	if($valArr['ni01'] >= 5000000){
		$htmlTbl .= "<td class=\"problem\">";
		$kell_e_szam_n = true;
	}else{
		$htmlTbl .= "<td>";
	}
	$htmlTbl .= number_format(($valArr['ni01']),2,","," ") . " Ft</td>";

	if($valArr['hu01'] >= 5000000){
		$htmlTbl .= "<td class=\"problem\">";
		$kell_e_szam_h = true;
	}else{
		$htmlTbl .= "<td>";
	}

	//<nn>
	// A HTML kódhoz adjuk a rendszám adatait.
	//</nn>
	$htmlTbl .= number_format(($valArr['hu01']),2,","," ") . " Ft</td></tr>";
	$htmlTbl .= '<tr><th>SAP.Rendsz.</th>' . "<td colspan=\"3\">". $frsz . "</td></tr>";
	$htmlTbl .= '<tr><th> - </th>' . '<td>Vontató:</td><td>Pótkocsi:</td><td>Ország:</td></tr>';
	$htmlTbl .= '<tr><th>Rendsz.mód.:</th>';
	$htmlTbl .= "<td><input type=\"text\" size=\"8\" value=\"" . $frszek[0] . "\" id=\"frsz-vont\" name=\"frsz-vont\" /></td>";
	$htmlTbl .= "<td><input type=\"text\" size=\"8\" value=\"" . $frszek[1] . "\" id=\"frsz-pot\" name=\"frsz-pot\"/></td>";
	$htmlTbl .= "<td><input type=\"text\" size=\"8\" value=\"" . $frszOrsz . "\" id=\"frsz-orsz\" name=\"frsz-orsz\" /></td></tr>";

	$htmlTbl .= '<tr><th>Eredmény</th>';
	
	//<nn>
	// Egy IF szerkezetben megvizsgáljuk, hogy van e olyan plant ahol EKAER köteles a súly, vagy az érték.<br>
	//</nn>
	if($kell_e_szam_r){
		//<nn>
		// Ha kell EKAER szám, akkor az url-be bele kell tenni a:
		//×-
		// @-- SZÁLLTMÁNYSZÁMOT - a tétlek XMLhez -@
		// @-- A RUOTE-ot: a header XMLhez -@
		// @-- A PLANT-OT mindkettőhöz -@
		// @-- És sajnos úgy tűnik a rendszámot is ... -@
		//-×
		// Ezután az utolsó cellába egy input submit elem kerül, és a form ACTION eleme gondoskodik arról, 
		// hogy a submit megfelelő sitera mutasson, ahol kezeljük magát a távoli kérést az NAV felé.
		//</nn>
		
		//<DEBUG>
		//Az eredeti URL-es megoldás helyet egy másik site-os POST + SUBMIT lett a vége.
		//<code>
		//$url = "/HAL_WEB_DEV/subpages/RAKTAR/EKAER/EKAER2_reqNrfromNAV.php";
		//$url .= "?szsz=" . trim($szallSzam) . "&plnt=RE85&route=" . str_replace("-","",$route);
		//echo "<td><a class=\"btn\" href=\"" . $url . "\">EKAER-szám kérés</a></td></tr>";
		//</code>
		//</DEBUG>
		$htmlTbl .= '<input type="hidden" name="szsz" value="' . trim($szallSzam) .'" />';
		$htmlTbl .= '<input type="hidden" name="plnt" value="RE85" />';
		$htmlTbl .= '<input type="hidden" name="route" value="' . str_replace("-","",$route) .'" />';
		$htmlTbl .= "<td><input type=\"submit\" class=\"btn\" value=\"EKAER-szám kérés\" /></td>";
		$kell_e_szam = FALSE;
	}else{
		$htmlTbl .= "<td>NEM KELL EKAER SZÁM!</td>";
	}
	if($kell_e_szam_n){
		$htmlTbl .= '<input type="hidden" name="szsz" value="' . trim($szallSzam) .'" />';
		$htmlTbl .= '<input type="hidden" name="plnt" value="NI01" />';
		$htmlTbl .= '<input type="hidden" name="route" value="' . str_replace("-","",$route) .'" />';
		$htmlTbl .= "<td><input type=\"submit\" class=\"btn\" value=\"EKAER-szám kérés\" /></td>";
		$kell_e_szam = FALSE;
	}else{
		$htmlTbl .= "<td>NEM KELL EKAER SZÁM!</td>";
	}
	if($kell_e_szam_h){
		$htmlTbl .= '<input type="hidden" name="szsz" value="' . trim($szallSzam) .'" />';
		$htmlTbl .= '<input type="hidden" name="plnt" value="HU01" />';
		$htmlTbl .= '<input type="hidden" name="route" value="' . str_replace("-","",$route) .'" />';
		$htmlTbl .= "<td><input type=\"submit\" class=\"btn\" value=\"EKAER-szám kérés\" /></td></tr>";
		$kell_e_szam = FALSE;
	}else{
		$htmlTbl .= "<td>NEM KELL EKAER SZÁM!</td></tr>";
	}

	//<nn>
	// Lezárjuk a HTML kódot tároló változót a tábla, majd a form elem lezárásaával.
	//</nn>
	$htmlTbl .= "</table></form>";

	//<nn>
	// A hívás helyére beillesztjük a generált HTML kódot.
	//</nn>
	echo $htmlTbl;
}

function writeOutItemsXML($itmsHU01,$itmsNI01,$itmsRE85){
//<SF>
// 2017. nov. 24.<br>
// Ez a függvény csak egyszerűen kiírja az XML fomrátumban tárolt tételadatokat egy-egy fileba.<br>
// Később az EKAER szám kérés során (ha lesz ilyen), akkor csak a megfelelő fileból kell kiolvasni a tételeket.
// PARAMÉTEREK:
//×-
// @-- @param $itmsHU01 = a HU01-es plant tételei -@
// @-- @param $itmsNI01 = a NI01-es plant tételei -@
// @-- @param $itmsRE85 = a RE85-es plant tételei -@
//-×
//MÓDOSTÁSOK:
//×-
// @-- ... -@
//-×
//</SF>

	//<nn>
	// A fileok tényleges kiírása a ->saveXML függvénnyel.
	//</nn>
	$itmsHU01->saveXML("I:/_Exchange_Insite/EKAER/EKAER_OUT_ITEMS/".trim($szallSzam)."HU01".".xml");
	$itmsNI01->saveXML("I:/_Exchange_Insite/EKAER/EKAER_OUT_ITEMS/".trim($szallSzam)."NI01".".xml");
	$itmsRE85->saveXML("I:/_Exchange_Insite/EKAER/EKAER_OUT_ITEMS/".trim($szallSzam)."RE85".".xml");	
}

function writeRawArrayData($wghtArr,$valArr){
//<SF>
// 2017. nov. 24.<br>
// Egy segédfüggvény arra az esetre, ha az összesített adatokat meg szeretnénk nézni.<br>
// PARAMÉTEREK:
//×-
// @-- @param $wghtArr = a súlytömb -@
// @-- @param $valArr = az értéktömb -@
//-×
//MÓDOSTÁSOK:
//×-
// @-- ... -@
//-×
//</SF>

	//<nn>
	// Simán elemenként kiírjuk egy-egy paragraph-ba az eredményeket ECHO-val.
	//</nn>
	echo "<p><h4>Göngyölegek súlyai plantonként</h4>";
	echo "A HU01 plant göngyölegének súlya: " . number_format(($wghtArr['gongy']['hu01']/1000),2,","," ") . " KG<br/>";
	echo "A NI01 plant göngyölegének súlya: " . number_format(($wghtArr['gongy']['ni01']/1000),2,","," ") . " KG<br/>";
	echo "A RE85 plant göngyölegének súlya: " . number_format(($wghtArr['gongy']['re85']/1000),2,","," ") . " KG<br/>";
	echo "Az EGYÉB plant göngyölegének súlya: " . $wghtArr['gongy']['oth'] . "<br/><hr/>";
	echo "</p>";
	
	echo "<p><h4>Tételek súlyai plantonként:</h4>";
	echo "A HU01 plant alkatrészeinek súlya: " . number_format(($wghtArr['aru']['hu01']/1000),2,","," ") . " KG<br/>";
	echo "A NI01 plant alkatrészeinek súlya: " . number_format(($wghtArr['aru']['ni01']/1000),2,","," ") . " KG<br/>";
	echo "A RE85 plant alkatrészeinek súlya: " . number_format(($wghtArr['aru']['re85']/1000),2,","," ") . " KG<br/>";
	echo "Az EGYÉB plant alkatrészeinek súlya: " . $wghtArr['aru']['oth'] . "<br/><hr/>";
	echo "</p>";
	
	echo "<p><h4>Tételek értéke plantonként (HUF):</h4>";
	echo "A HU01 plant alkatrészeinek értéke: " . number_format(($valArr['hu01']),2,","," ") . " Ft<br/>";
	echo "A NI01 plant alkatrészeinek értéke: " . number_format(($valArr['ni01']),2,","," ") . " Ft<br/>";
	echo "A RE85 plant alkatrészeinek értéke: " . number_format(($valArr['re85']),2,","," ") . " Ft<br/>";
	echo "Az EGYÉB plant alkatrészeinek értéke: " . $valArr['oth'] . "<br/><hr/>";
	echo "</pre></p>";
	
}

function debugger($stgNr){
//<SF>
// 2017. nov. 24.<br>
// Egy debugger, ahol különböző paraméterek segítségével, stringbe írhatjuk a futási időtartamokat, 
// majd megejeleníthetjük azokat.<br>
// PARAMÉTEREK:
//×-
// @-- @param ... = ... -@
//-×
//MÓDOSTÁSOK:
//×-
// @-- ... -@
//-×
//</SF>
	
	$debugHTML = "";
	
	//<nn>
	// Egy IF szerkezettel a bejövő státuszjelző paraméter függvényében elvégezzük a megfelelő feladatot, üres paraméter esetén kiírjuk
	// az eddig generált HTML kódot, amit a $debugHTML változóban tárolunk. 
	//</nn>
	if($stgNr == 0){
		$t1 = microtime(true);
		$debugHTML = "";
	}elseif($stgNr == 1){
		$t2 = microtime(true);
		$dur01 =  number_format($t2-$t1,3);
		$debugHTML .= "<div class=\"alapRaktarDiv\"><p class=\"debugInfo\"> Az incializáció ciklus ideje: " . $dur01 . " s.</p><hr/>";
		$t1=microtime(true);
	}elseif($stgNr == 2){
		$t2 = microtime(true);
		$dur02 = number_format($t2-$t1,3);
		$debugHTML .= "<p class=\"debugInfo\"> Az tömbök feltöltésének ideje: " .$dur02 . " s.</p><hr/>";
		$t1 = microtime(true);
	}elseif($stgNr == 3){
		$t2 = microtime(true);
		$dur03 =  number_format($t2-$t1,3);
		$debugHTML .= "<p class=\"debugInfo\"> Az súlyok (áru+göngyöleg) összegzésnek ideje: " . $dur03 . " s.</p><hr/>";
		$t1 = microtime(true);
	}elseif($stgNr == 4){
		$t2 = microtime(true);
		$dur04 = number_format($t2-$t1,3);
		$debugHTML .= "<p class=\"debugInfo\"> Az tételek (áru+göngyöleg+érték) kiíratásnak ideje: " . $dur04 . " s.</p><hr/>";
		$t1 = microtime(true);
	}elseif($stgNr == 5){
		$t2 = microtime(true);
		$dur05 = number_format($t2-$t1,3);
		$debugHTML .= "<p class=\"debugInfo\"> Az táblázat létrehozásának, és kiíratásnak ideje: " . $dur05 . " s.</p><hr/>";
		
		$debugHTML .= "<p class=\"debugInfo\">";
		$debugHTML .= "-------------------------------------------------------<br/>";
		$debugHTML .= "\$dur01 = " . $dur01 . "<br/>";
		$debugHTML .= "\$dur02 = " . $dur02 . "<br/>";
		$debugHTML .= "\$dur03 = " . $dur03 . "<br/>";
		$debugHTML .= "\$dur04 = " . $dur04 . "<br/>";
		$debugHTML .= "\$dur01 = " . $dur05 . "<br/>";
		$debugHTML .= "Összesen: " . ($dur01+$dur02+$dur03+$dur04+$dur05) . " s.<br/>";
		$debugHTML .= "-------------------------------------------------------<br/></p>";
	}elseif($stgNr == 6){
		
	}elseif($stgNr == 7){
		
	}else{
		//<nn>
		// Futási adatok kiírása:
		//</nn>
		echo $debugHTML;
	}
	
}


?>
	