<?php
//<M>
//×-
//@-FILENÉV   : PHP_DOC_CRTR - DOC_GNRTR.php-@
//@-SZERZŐ    : AX07057-@
//@-LÉTREHOZVA:  2016 okt. 30-@
//@-FÜGGŐSÉGEK:
//×-
// @-- nincs függőség-@
//-@
//-×
//-@
//@-LEÍRÁS    :
// Ez a PHP kód a PHP_DOCCER class implementációját tartalmazza.
//@-MÓDOSÍTÁSOK :
//×-
// @-- 2017-08-20 <br>
// Megpróbálok full git supportot behozni az Eclipse-be.
//-@
//-×
//-×
//</M>	


    class PHP_DOCCER{
    //<SF>
    // 2016. dec. 11.<br>
    // LEÍRÁS<br>
    // Ez az osztály tartalmazza a dokumnetációk előállításához szükséges kódot.
    //MÓDOSTÁSOK:
    //×-
    // @-- ... -@
    //-×
    //</SF>
    
    	//<nn>
    	// Az osztály adattagjai:
    	//×-
    	// @-- $stdPathIn = a beolvasandó file könyvtára -@
    	// @-- $stdPathOut = a kimeneti file (nyers dokumnetációs HTML) könyvtára -@
    	// @-- $inFile = a bemeneti filenév -@
    	// @-- $outFile = a kimeneti filenév -@
    	//-×
    	//</nn>
        private $stdPathIn = "";
        private $stdPathOut = "";
        private $inFile = "";
        private $outFile = "";
        private $funcSzlo = 0;
        
        
        public function __construct($if="", $of=""){
        //<SF>
        // 2016-10-30<br>
        // A konstruktor...
        //<SF>
            
            //<nn>
            // Az alapértelemezett file mappák elérésének beállítása.
            //</nn>
            $this->stdPathIn = $_SERVER['DOCUMENT_ROOT']."/PHP_DOC_CRTR/inputfiles/";
            $this->stdPathOut = $_SERVER['DOCUMENT_ROOT']."/PHP_DOC_CRTR/outputfiles/";
            
            //<nn>
            // Beállítjuk a bemeneti file-t a beérkező vagy be nem érkező
            // paraméter szerint.<br>
            // Az lapértelmezett érték: tstFile.php.
            //</nn>
            if($if !== ""){
                $this->inFile = $if;
            }else{
                //<DEBUG>
                // Írassuk ki a figyelmeztetést, hogy nincs INFILE.<br>
                //<code>
                // echo "NINCS INFILE, STANDRDOT HASZNÁLJUK!!!!!!<br>";
                //</code>
                //</DEBUG>
            	
                $this->inFile = "tstFile.php";
            }
            
            //<nn>
            // Beállítjuk a bemeneti file-t a beérkező vagy be nem érkező
            // paraméter szerint.<br>
            // Az alapértelmezett érték: tstFile.php.
            //</nn>
            if($if !== ""){
                $this->outFile = $of;
            }else{
                $this->outFile = "out.txt";
            }
            
            //<DEBUG>
            // A konstruktor lefutásának jelzése:<br>
            // <code>echo "<p>PHP_DOCCER konstruktor meghívva!</p>";</code>
            //</DEBUG>
        }
        
        public function createDocs(){
        //<SF>
        // 2016-12-10<br>
        // LEÍRÁS<br>
        // Ez a függvény afféle főfüggévny. Alapvetően soronként meghívjuk a prepareString() füvényt, és az eredményt
        // a kimeneti fileba teszük.<br>
        // Ez gyors, és könnyen érthető, de nem lehet vele beágyazottságot megjeleníteni. Vagyis a kimenetben a 
        // HTML elemek egymást követik, és nem tudják egymást tartalmazni.
        // PARAMÉTEREK:
        //×-
        // @-- Nincsenek paraméeterek -@
        //-×
        //MÓDOSTÁSOK:
        //×-
        // @-- ... -@
        //-×
        //</SF>
            echo "<p>createDocs() indul...</p>";
            //<nn>
            // Meg kell nézni, hogy miylen route jött be...
            //</nn>
            if(!strpos($this->inFile,"htdocs")){
            	$fi = fopen($this->stdPathIn.$this->inFile,"r") or 
                die("<p>A bemeneti file(".$this->stdPathIn.$this->inFile.")megnyitása sikertelen!</p>");
            }else{
            	$fnm = strrchr($this->inFile,"\\");
            	$fi = fopen($this->stdPathIn.$fnm ,"r") or
            	die("<p>A bemeneti file(".$this->stdPathIn.$fnm.")megnyitása sikertelen!</p>");
           	}
           	if(!strpos($this->outFile,"htdocs")){
           		$of = fopen($this->stdPathOut.$this->outFile,"w") or
           		die("<p>A kimeneti file(".$this->stdPathOut.$this->outFile.")megnyitása sikertelen!</p>");
           	}else{
           		$fnm = strrchr($this->outFile,"\\");
           		$of = fopen($this->stdPathOut.$fnm,"w") or
           		die("<p>A kimenetifile(".$this->stdPathOut.$fnm.")megnyitása sikertelen!</p>");
           	}
            
            echo "<pre>";
            while(($sor = fgets($fi,1024)) !== false){
                if(substr(trim($sor), 0,2) == "//"){
                    $resultStr = $this->prepareString($sor);
                    //<DEBUG>
                    // A nyers HTML kód kiírásához:<br>
                    //<code>
                    // echo "<p>DOC:" . htmlspecialchars($resultStr) ."</p>";
                    //</code>
                    //</DEBUG>
                    
                    fwrite($of,$resultStr);
                }elseif(substr(trim($sor), 0,15) == "public function" || 
                	substr(trim($sor), 0,16) == "private function" ||
                	substr(trim($sor), 0,8) == "function"){
                	//<nn>
                	// Itt kiszedhetjük a függvény nevét, és paramétereit... de talán ezzel nem is kellene
                	// tökölni, elég lenne, ha egyszerűen 
                	//</nn>
                	$kzd = strpos($sor, "tion ") + 5;
                	$veg = strpos($sor, "){") + 2;
                	$fnNev = substr($sor,$kzd,($veg-$kzd-1));
                	if($this->funcSzlo > 0){
                		$resultStr = '</div><div class="doc-body"><h4>'.$fnNev.'</h4>';
                	}else{
                		$resultStr = '<div class="doc-body"><h4>'.$fnNev.'</h4>';
                	}
                	fwrite($of, $resultStr);
                }else{
                    //echo "<p>NO-DOC:" . $sor ."</p>";
                }
                
            }
            fwrite($of,"</div>");
            echo "</pre>";
            fclose($fi);
            fclose($of);
        }
   
        public function setInfile($inFl){
        //<SF>
        // 2016-12-10<br>
        // LEÍRÁS:<br>
        // Az osztály infile adatattagjának set-er függvénye.<br>
        // PARAMÉTEREK:
        //×-
        // @-- @param \$inFl = a beolvasandó file neve -@
        //-×
        //MÓDOSTÁSOK:
        //×-
        // @-- ... -@
        //-×
        //</SF>
            //<DEBUG>
            // Valami érdekesség van a filenevkkel, írassuk ki előtte, és utána is.<br>
            //<code>
            // ...
            //</code>
            //</DEBUG>
            $this->inFile = $inFl;
        }
        
        public function setOutfile($oFl){
        //<SF>
        // Az osztály outfile adattagjának set-er függvénye.
        //</SF>
            $this->outFile = $oFl;
        }
             
        private function prepareString($rwStr){
        //<SF>
        // 
        //</SF>
            $respStr = $rwStr;
            //<DEBUG>
            // A nyers HTML kód közvetlen vizsgálatához (bemenet):<br>
            //<code>
            // echo "<br>be: " . htmlspecialchars($respStr) ."<br>";
            // echo "<br>STRPOS(: " . htmlspecialchars($respStr) .") = " . (strpos($respStr,'//<M>')) . "<br>";
            //</code>
            //</DEBUG>
            
            $respStr = str_replace('//<M>','<div class="doc-modul">',$respStr);
            $respStr = str_replace('//</M>','</div>',$respStr);
            
            $respStr = str_replace('//×-','<ul class="doc-list">',$respStr);
            $respStr = str_replace('//-×','</ul>',$respStr);
            
            $respStr = str_replace('//@-','<li>',$respStr);
            $respStr = str_replace('// @-','<li>',$respStr);
            
            $respStr = str_replace('// -@','</li>',$respStr);
            $respStr = str_replace('//-@','</li>',$respStr);
            $respStr = str_replace('-@','</li>',$respStr);
            
            $respStr = str_replace('//<SF>','<div class="doc-subFunc">',$respStr);
            $respStr = str_replace('//</SF>','</div>',$respStr);
            
            $respStr = str_replace('//<DEBUG>','<div class="doc-debug">',$respStr);
            $respStr = str_replace(';',';<br/>',$respStr);
            $respStr = str_replace('//</DEBUG>','</div">',$respStr);
            
            $respStr = str_replace('//<nn>','<div class="doc-normNote">',$respStr);
            $respStr = str_replace('//</nn>','</div>',$respStr);
            
            
            $respStr = str_replace('//','',$respStr);
            
            
            
            /*
            if(strpos($respStr,'//<M>') >= 0){
                $respStr = str_replace('//<M>','<div class="doc-modul">',$respStr);
            }elseif(strpos('//</M>',$respStr)){
                $respStr = str_replace('//</M>','</div>',$respStr);
            }elseif(strpos('//<SF>',$respStr)){
                $respStr = str_replace('//<SF>','<div class="doc-subFunc">',$respStr);
            }elseif(strpos('//</SF>',$respStr)){
                $respStr = str_replace('//</SF>','</div>',$respStr);
            }elseif(strpos('//<nn>',$respStr)){
                $respStr = str_replace('//<nn>','<div class="doc-normNote">',$respStr);
            }elseif(strpos('//</nn>',$respStr)){
                $respStr = str_replace('//</nn>','</div>',$respStr);
            }elseif(strpos('//<DEBUG>',$respStr)){
                $respStr = str_replace('//<DEBUG>','<div class="doc-debug">',$respStr);
            }elseif(strpos('//</DEBUG>',$respStr)){
                $respStr = str_replace('//</DEBUG>','</div>',$respStr);
            }elseif(strpos('//×-',$respStr)){
                $respStr = str_replace('//×-','<ul class="doc-list">',$respStr);
            }elseif(strpos('//-×',$respStr)){
                $respStr = str_replace('//-×','</ul>',$respStr);
            }elseif(strpos('//@-',$respStr)){
                $respStr = str_replace('//@-','<li>',$respStr);
            }elseif(strpos('-@',$respStr)){
                $respStr = str_replace('-@','</li>',$respStr);
            }else{
                echo "Semmifelé strpos nem működött!<br>";
            }*/
            //<DEBUG>
            // A nyers HTML kód kiíratásához (kimenet):<br>
            //<code>
            // echo "Ki: " . htmlspecialchars($respStr) . "</hr>";
            //</code>
            //</DEBUG>
            
            return $respStr;
            
        }
    
    
    
    
    
    }
    
        
    
    
    
?>
