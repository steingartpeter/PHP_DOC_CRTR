//<M>
//×-
//@-FILENÉV   : PROJECT NAME - localMain.js-@
//@-SZERZŐ    : AX07057-@
//@-LÉTREHOZVA: 2016 okt. 30-@
//@-FÜGGŐSÉGEK:
//×-
// @-- RQRD_FILE01.js-@
// @-- RQRD_FILE02.js-@
// @-- RQRD_FILE03.js-@
// @-- RQRD_FILE04.js-@
//-@
//-×
//-@
//@-LEÍRÁS    :
//Ez az javascript file készült arra a feladatra, hogy ...
//@-MÓDOSÍTÁSOK :
//×-
// @-- ... -@
//-×
//-×
//</M>

var APP_PHPDOC = APP_PHPDOC || {};

$("document").ready(function(){
//<SF>
// A lap indulásakor lefutó inicializációs függvény.
//</SF>
	/*$("ul.navbar-nav>li>a").css("color","#CDCDCD");*/
	$("ul.navbar-nav>li>a").click(APP_PHPDOC.ADD_ACTIVE_CLASS);
	$("#infile").on("change",APP_PHPDOC.FILE_HANDLING);
});

APP_PHPDOC.ADD_ACTIVE_CLASS = function(){
//<SF>
// Ez a függvény kezeli a menülinkek CSS osztályának mozgatását.
//</SF>
	
	$("ul.navbar-nav>li").removeClass("active");
	$(this).parent().addClass("active");
}

APP_PHPDOC.FILE_HANDLING = function(){
	console.info("infile chnaged!");
	var inp = $("#infile");
	console.log("inp");
	console.log(inp);
}











