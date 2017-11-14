Rem Attribute VBA_ModuleType=VBAModule
Option VBASupport 1
Option Explicit
'<M>
'Ez a modul egy egszerű MARA adathalmazt tölt le a nem regisztrált cikkszámokról.
'Ezek azok, amikből hiányzik a DIVISION.
'Egy VBScriptet hív meg a modul, ami letöltia friss adatokat, majd ezt beolvassa az
'aktuális munkafüzetbe.
'Ezután megkéri a felhasználót hogy keresse meg az előző file-t, és az ottani adatokat
'FKERES függvénnyel ide másolja, majd a korábbi file-t bezárja.
'Létrehozva: 2016-04-18
'Szerző: AX07057
'Függgőségek (ezek class-ok, amik az itteni kód működéséhez szükséges kódot tartalmaznak):
' - clsVBScriptGenerator
' - clsFormatter
' - UTIL_FilePicker01
' - clsTextHndlr
' - clsHibaKezelo
'</M>

Private Const SCRIPT_PATH As String = "I:\_Exchange_Insite\VBSCRIPTS\NI_FAIL_REG\"
Private Const SCRIPT_FILE As String = "DWNLD_MARA_FR.vbs"
Private Const DATA_PATH As String = "I:\_Exchange_Insite\SAP_DWNLDS\NI_FAIL_REG\"
Private Const DATA_FILE As String = "FAIL_REG_AKT.TXT"
Private Const FRMR_PRTREG_PATH_DEV As String = "I:\HAL_Kozos\Project\Fejlesztesek\ICS_PARTREG\partreg\"
Private Const FRMR_PRTREG_PATH_PRD As String = "I:\Procurement\Nissan\Documents\Part registration problems\"


Sub MAIN_NIS_PARTREG()
'<SF>
'A főfüggvény, ami beállítja a szükséges váéltozókat, és az al-szubrutinokat hivogatja.
'</SF>
Dim trgtWbk As Workbook, scrptHlr As New clsVBScriptGenerator, trgtWsht As Worksheet, wsht_frmrData As Worksheet
    
    '<nn>
    'A munkafüzet előkészítése a munkára.
    '</nn>
    Call INIT(trgtWbk, trgtWsht, wsht_frmrData)
    
    '<nn>
    'Letöltjük az adatokat a SAPból majd várunk 3 msáodpercet, mielőtt feldogozzuk az
    'eredmény-file-t.
    '</nn>
    Call DWNLD_SAP_DATA
    Application.Wait (Now + TimeValue("00:00:03"))
    
    '<nn>
    'Beolvassuk a letöltött file-t a célmunkalapra.
    '</nn>
    Call READ_SAP_DATA(trgtWsht)
    
    '<nn>
    'Megnyijuk a korábbi file-t, és az adatait bemásoljuk a mostani munkafüzetbe.
    'A megfelelő cellákba bedobjuk a FKERES függvényt az korábbi adatokra.
    '</nn>
    Call COPY_FORMER_DATA(wsht_frmrData)
    
    MsgBox "Makró lefutott..."
End Sub

Private Sub INIT(ByRef wbk As Workbook, ByRef wsht As Worksheet, ByRef old_wsht As Worksheet)
'<SF>
'A változók beállítása, inicializálása.
'</SF>
Dim D As Date, wshtNm As String
    
    '<nn>
    'A munkafüzetek, munkalapo refrenciáinak feltöltése.
    '</nn>
    Set wbk = ActiveWorkbook
    D = Date
    wshtNm = "partReg_" & format(D, "yyyy_mm_dd")
    Set wsht = wbk.Worksheets(1)
    wsht.name = wshtNm
    
    '<nn>
    'A második munkafüzetet előkészítjük a korábbi adatok bemásolásásra.
    '</nn>
    wshtNm = "KORABBI_ADATOK"
    Set old_wsht = wbk.Worksheets(2)
    old_wsht.name = wshtNm
    
    '<nn>
    'A célmunkafüzetet megformázzuk.
    '</nn>
    Call CREATE_HEADER(wsht)

End Sub

Private Sub CREATE_HEADER(ByRef wsht As Worksheet)
'<SF>
'Feltöltjük, és megformázzuk a fejlécet.
'</SF>
Dim hdrTxts() As String, r As Long, c As Long, ix As Integer, frmtr As New clsFormatter, rng As Range
    
    ReDim hdrTxts(19)
    
    '<nn>
    'A fejléc elnevezések tömbjének felrtöltése.
    '</nn>
    hdrTxts(0) = "Material"
    hdrTxts(1) = "Material Description"
    hdrTxts(2) = "Matl Group"
    hdrTxts(3) = "MS"
    hdrTxts(4) = "St"
    hdrTxts(5) = "BUn"
    hdrTxts(6) = "PV key"
    hdrTxts(7) = "VUn"
    hdrTxts(8) = "VUn"
    hdrTxts(9) = "SC"
    hdrTxts(10) = "Dv"
    hdrTxts(11) = "Created"
    hdrTxts(12) = "Created By"
    hdrTxts(13) = "Last Chg"
    hdrTxts(14) = "Changed By"
    hdrTxts(15) = "Clt"
    hdrTxts(16) = "reason"
    hdrTxts(17) = "Problem"
    hdrTxts(18) = "Action"
    hdrTxts(19) = "Comment"
    
    '<nn>
    'Beírjuk a fejléc neveket.
    '</nn>
    r = 1: c = 1
    For c = 1 To UBound(hdrTxts) + 1
        wsht.Cells(r, c).Value = hdrTxts(c - 1)
    Next
    
    '<nn>
    'A clsFormatter osztály segítségével beállíjuk a STD_DHDR01
    'formátumot a fejlétartománynak.
    '</nn>
    Set rng = Range(wsht.Cells(1, 1), wsht.Cells(1, c - 1))
    Call frmtr.applyFormat("STD_HDR01", rng)
End Sub

Private Sub READ_SAP_DATA(ByRef wsht As Worksheet)
'<SF>
'Megnyitjuk a frissen letöltött file-t, és beolvassuk az adatokat.
'</SF>
Dim sor As String, txhlr As New clsTextHndlr, ix As Integer, data() As String, rw As Long

    '<nn>
    'Megnyitjuk a letöltött file-t olvasásra.
    '</nn>
    Open DATA_PATH & DATA_FILE For Input As #1
    
    '<nn>
    'Átugorjuk a query fejlécsorait.
    '</nn>
    For ix = 1 To 5
        Line Input #1, sor
    Next
       
    
    rw = 2
    Do While Not EOF(1)
        Line Input #1, sor
        '<nn>
        'Ellnőrizzük, hogy normális adatsorunk van-e, és nem a zárósor.
        '</nn>
        If Left(sor, 5) <> "-----" Then
            Call txhlr.explode(sor, "|", data)
            '<nn>
            'A cikkszámból kidobjuk a szóközöket.
            '</nn>
            data(0) = Replace(data(0), " ", "")
            '<nn>
            'A data tömböt beírjuk a munkalap következő sorába.
            '</nn>
            For ix = 0 To UBound(data)
                If ix = 0 Or ix = 2 Then
                    wsht.Cells(rw, ix + 1).NumberFormat = "@"
                End If
                wsht.Cells(rw, ix + 1) = data(ix)
            Next
            rw = rw + 1
        End If
    Loop
    
    '<nn>
    'A forrásfile-t bezárjuk.
    '</nn>
    Close #1
    
    '<nn>
    'Az adatokat láthatóvá tesszük.
    '</nn>
    Columns("A:T").AutoFit
    
      
End Sub

Private Sub DWNLD_SAP_DATA()
'<SF>
'Ez a szubrutin tölti le egy előre megírt VBScript segítségével az aktuális adatokat
'z SAP-ból.
'</SF>
Dim scrptHlr As New clsVBScriptGenerator, errHlr As New clsHibaKezelo, msg As String
    
    '<nn>
    'Nehogy valami korábbi hiba miatt állítsuk le a futást!
    '</nn>
    err.Clear
    

    '<nn>
    'Lefuttatjuk az adatletöltő scriptet.
    '</nn>
    Call scrptHlr.runVbScriptFromFile(SCRIPT_PATH, SCRIPT_FILE)
    
    '<nn>
    'Ha hiba volt értesítjük a felhasználót és kilépünk.
    '</nn>
    If err.Number <> 0 Then
        msg = "HIBA!" & vbCrLf
        msg = msg & "Úgy tűnik a SAP adatokat letöltő VB script hibát okozott." & vbCrLf
        msg = msg & "Mivel adatok nélkül nem sokat tehetünk a script most kilép!"
        errHlr.CritBox (msg)
        Exit Sub
    End If
    
End Sub

Private Sub COPY_FORMER_DATA(ByRef trgtWsht As Worksheet)
Dim wbk_frmData As Workbook, wsht_frmrData As Worksheet, wbkName As String, fOpenHdr As String, fOpenPath As String
Dim lstRw As Long

    '<nn>
    'Beállítjuk a filenyitási paramétereket.
    'A feliratot az ablakon, és a kiindulási mappát - ez utóbbit majd a
    'kész makróban a megfelelő konstansra kell átállítani DEV -> PRD.
    '</nn>
    fOpenHdr = "Előző part.reg adatokat tartalmazó file megnyitása"
    fOpenPath = FRMR_PRTREG_PATH_DEV
    'fOpenPath = FRMR_PRTREG_PATH_PRD
    
    '<nn>
    'A felhasználó kiválasztja  akorábbi adatokat tartalmazó munkafüzetet.
    '</nn>
    wbkName = UTIL_FilePicker_01.UseFileDialogFor1File(fOpenHdr, fOpenPath)
    
    
    '<nn>
    'Beállítjuk a referenciákat.
    '</nn>
    Set wbk_frmData = Workbooks.Open(wbkName)
    Set wsht_frmrData = wbk_frmData.Worksheets(1)
    
    '<nn>
    'Megkeressüka  forrásadatok utolsó sorát.
    '</nn>
    wsht_frmrData.Cells(1, 1).Select
    Selection.End(xlDown).Select
    lstRw = Selection.row
    
    '<nn>
    'Az első és utolsó sor között az A-T oszlopok tartományát átmásoljuk.
    '</nn>
    Range("A1:T" & lstRw).Select
    Selection.Copy
    
    '<nn>
    'A másolt adatokat a célmunkafüzetbe beillesztjük - formátum és érték.
    '</nn>
    trgtWsht.Activate
    trgtWsht.Select
    
    trgtWsht.Cells(1, 1).Select
    Selection.PasteSpecial Paste:=xlPasteAllUsingSourceTheme, Operation:=xlNone _
        , SkipBlanks:=False, Transpose:=False
    Selection.PasteSpecial Paste:=xlPasteValues, Operation:=xlNone, SkipBlanks _
        :=False, Transpose:=False
    
    '<nn>
    'A forrásmunkafüzetet változtatás nélkül bezárjuk.
    '</nn>
    Application.DisplayAlerts = False
    wbk_frmData.Close False
    
    '<nn>
    'Ha az adatokat átmásoltuk, jöhet a FKERES képletek megadása.
    '</nn>
    Call CREATE_FUNCTIONS(trgtWsht)
    
    
End Sub

Private Sub CREATE_FUNCTIONS(ByRef wsht As Worksheet)
'<SF>
'Miután az adatokat bemásoltuk feltöltjük képletekkel a megfelelő cellákat.
'</SF>
Dim trgtWsht As Worksheet, lastTrgtRw As Long, lastSrcRw As Long
    
    '<nn>
    'Megkeressük az utolsó sort, ahová kell a képlet.
    '</nn>
    Set trgtWsht = Worksheets(1)
    trgtWsht.Activate
    trgtWsht.Cells(1, 1).Select
    Selection.End(xlDown).Select
    lastTrgtRw = Selection.row
    trgtWsht.Cells(1, 1).Select
    
    '<nn>
    'Megkeressük az utolsó sort az adatfileban.
    '</nn>
    wsht.Activate
    wsht.Cells(1, 1).Select
    Selection.End(xlDown).Select
    lastSrcRw = Selection.row
    wsht.Cells(1, 1).Select
    
    '<nn>
    'A célmunkalapra lépünk.
    '</nn>
    trgtWsht.Activate
    trgtWsht.Select
    
    '<nn>
    'Megadjuk a képletet az öt cellából az elsőbe.
    'Majd aátmásoljuk az első képletet a második négybe.
    'Végül azokban átírjuk az oszlopszámot a kpletben.
    '</nn>
    Range("Q2").Select
    ActiveCell.FormulaR1C1 = _
        "=IFERROR(VLOOKUP(RC1,KORABBI_ADATOK!R1C1:R" & lastSrcRw & "C20,17,0),""ÚJ CSZ."")"
    Range("Q2").Select
    Selection.AutoFill Destination:=Range("Q2:T2"), Type:=xlFillDefault
    Range("Q2:T2").Select
    
    Range("R2").Select
    ActiveCell.FormulaR1C1 = _
        "=IFERROR(VLOOKUP(RC1,KORABBI_ADATOK!R1C1:R500C20,18,0),""ÚJ CSZ."")"
    Range("S2").Select
    ActiveCell.FormulaR1C1 = _
        "=IFERROR(VLOOKUP(RC1,KORABBI_ADATOK!R1C1:R500C20,19,0),""ÚJ CSZ."")"
    Range("T2").Select
    ActiveCell.FormulaR1C1 = _
        "=IFERROR(VLOOKUP(RC1,KORABBI_ADATOK!R1C1:R500C20,20,0),""ÚJ CSZ."")"
    Range("Q2:T2").Select
    Selection.AutoFill Destination:=Range("Q2:T" & lastTrgtRw)
    
    Range("Q2:T413").Select
    
    Range("U2").Select

    '<nn>
    'Az adatokat láthatóvá tesszük.
    '</nn>
    Columns("A:T").AutoFit

End Sub

Private Sub tester()
'<nn>
'A szokásos fejlesztéshez használt TESTER szubrutin.
'</nn>
Dim trgtWbk As Workbook, trgtWsht As Worksheet, o_wsht As Worksheet
    'Call INIT(trgtWbk, trgtWsht, o_wsht)
    Set o_wsht = Worksheets("KORABBI_ADATOK")
    
    'Call COPY_FORMER_DATA(o_wsht)
    Call CREATE_FUNCTIONS(o_wsht)
End Sub






























