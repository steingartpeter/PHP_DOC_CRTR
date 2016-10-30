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
// @-- ... -@
//-×
//-×
//</M>	


    class PHP_DOCCER{
    //<SF>
    // Ez az osztály tartalmazza a dokumnetációk előállításához szükséges kódot.
    //</SF>
        private $stdPathIn = "";
        private $stdPathOut = "";
        private $inFile = "";
        private $outFile = "";
        
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
                $this->inFile = "tstFile.php";
            }
            
            //<nn>
            // Beállítjuk a bemeneti file-t a beérkező vagy be nem érkező
            // paraméter szerint.<br>
            // Az lapértelmezett érték: tstFile.php.
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
        //
        //</SF>
            echo "<createDocs() indul...>";
            $fi = fopen($this->stdPathIn.$this->inFile,"r") or 
                die("<p>A bemeneti file(".$this->stdPathIn.$this->inFile.")megnyitása sikertelen!</p>");
            
            $of = fopen($this->stdPathOut.$this->outFile,"w") or 
                die("<p>A kimenetifile(".$this->stdPathOut.$this->outFile.")megnyitása sikertelen!</p>");
            
            while(($sor = fgets($fi,1024)) !== false){
                if(substr($sor, 0,2) == "//"){
                    echo "<p>DOC:" .$sor ."</p>";
                }else{
                    echo "<p>NO-DOC:" .$sor ."</p>";
                }
                fwrite($of,$sor);
            }
            fclose($fi);
            fclose($of);
        }
    
        private function prepareString($rwStr){
        //<SF>
        //
        //</SF>
            $respStr = $rwStr;
            $respStr = str_replace($respStr,'//<M>','<div class="doc-modul">');
            $respStr = str_replace($respStr,'//</M>','</div>');
            
            return $respStr;
            
        }
    
    
    
    
    
    }
    
        
    
    
    
?>
