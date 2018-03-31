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
//-@
//-×
//</M>

var APP_PHPDOC = APP_PHPDOC || {};
APP_PHPDOC.ADD_ACTIVE_CLASS = a001;
APP_PHPDOC.FILE_HANDLING = a002;
APP_PHPDOC.closeDetails = a003;

$("document").ready(a00);

function a00(){
//<SF>
// 2017. nov. 7.<br>
// A lap indulásakor lefutó inicializációs függvény.<br>
// PARAMÉTEREK:
//×-
// @-- @param ... = NINCS PARAMÉTER -@
//-×
//MÓDOSTÁSOK:
//×-
// @-- ... -@
//-×
//</SF>

	//<DEBUG>
	// Egy elrontott színezés, és egy jelző alert a működéshez.<br>
	//<code>
	// $("ul.navbar-nav>li>a").css("color","#CDCDCD");
	// alert("JQuery ready -> OK");
	//</code>
	//</DEBUG>
	
	$("ul.navbar-nav>li>a").click(APP_PHPDOC.ADD_ACTIVE_CLASS);
	$("#infile").on("change",APP_PHPDOC.FILE_HANDLING);
	$(".doc-body").click(a003);
}

function a001(){
//<SF>
// 2017. nov. 7.<br>
// Ez a függvény kezeli a menülinkek CSS osztályának mozgatását.<br>
// PARAMÉTEREK:
//×-
// @-- @param ... = ... -@
//-×
//MÓDOSTÁSOK:
//×-
// @-- ... -@
//-×
//</SF>
	
	$("ul.navbar-nav>li").removeClass("active");
	$(this).parent().addClass("active");
}
 
function a002(){
//<SF>
// 2017. nov. 7.<br>
// Egy fügvény az infile paraméter konzolra iratására.<br>
// PARAMÉTEREK:
//×-
// @-- @param ... = ... -@
//-×
//MÓDOSTÁSOK:
//×-
// @-- ... -@
//-×
//</SF>
	console.info("infile chnaged!");
	var inp = $("#infile");
	console.log("inp");
	console.log(inp);
}

function a003(){
	console.log(this);
	$(this).nextUntil("hr").slideToggle();
}










