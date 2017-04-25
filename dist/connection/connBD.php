<?php session_start();
    //servidor
    $DBHOST="localhost";
    $DATABASE="enorhg12_RegDiario" ;
    $DBUSER ="enorhg12_rg" ;
    $DBPASS ="RegDiario." ;
    $mysqli = new mysqli($DBHOST ,$DBUSER, $DBPASS, $DATABASE);
    if ($mysqli -> connect_errno) {	die( "Fallo la conexión a MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error); exit;}
    
    //sesion
    $acceso= $_SESSION['access'];//tipo usuario
    $fechaGuardada = $_SESSION["ultimoAcceso"];    
    $ahora=date('Y-m-d H:i:s');
    $tiempo_transcurrido = (strtotime($ahora)-strtotime($fechaGuardada));
    
    $dia = date('d');
    $año_mes_=$_SESSION['dato'];
    $datos = explode("-",$año_mes_);
    $año_=$datos[0];
    $mes_=$datos[1];
    $hoy_=$año_.'-'.$mes_.'-'.$dia;
    //$ultimoDia= date("d",(mktime(0,0,0,$mes_+1,1,$año_)-1));
    $ultimoDia= date("d",(mktime(0,0,0,$mes+1,1,$año)-1));
    if($año_.'-'.$mes_==date('Y-m')){
        $ayer = date( "d", strtotime( "-1 day", strtotime($hoy_)));
        $ultimoDia= date("d",(mktime(0,0,0,$mes_+1,1,$año_)-1));
    }else{
        $ayer =  $ultimoDia;
    }

    if($hoy_==date('Y-m').'-01'){
        //obtener mes anterior para ingresar ultimo dato
        $mes=date("m",mktime(0,0,0,date("$mes_")-1,date("d"),date("$año_")));
        $año=date("Y",mktime(0,0,0,date("$mes_")-1,date("d"),date("$año_")));
        $hoy=$año.'-'.$mes.'-'.$dia;
        $ultimoDia= date("d",(mktime(0,0,0,$mes+1,1,$año)-1));
    }else{
        $mes=$mes_;
        $año=$año_;
        $hoy=$año.'-'.$mes.'-'.$dia;
        //$ultimoDia= date("d",(mktime(0,0,0,$mes+1,1,$año)-1));//ultima modificacion
    }

    /*for($dias=1;$dias<=$ultimoDia;$dias++){
        $dias[]=$dias;
    }*/
    
    switch ($mes) {  // Obtenemos el nombre en castellano del mes
        case 1 : $month_name = "Ene";
            break;
        case 2 : $month_name = "Feb";
            break;
        case 3 : $month_name = "Mar";
            break;
        case 4 : $month_name = "Abr";
            break;
        case 5 : $month_name = "May";
            break;
        case 6 : $month_name = "Jun";
            break;
        case 7 : $month_name = "Jul";
            break;
        case 8 : $month_name = "Agos";
            break;
        case 9 : $month_name = "Sept";
            break;
        case 10 : $month_name = "Oct";
            break;
        case 11 : $month_name = "Nov";
            break;
        case 12 : $month_name = "Dic";		
    }
    /*
?>

<script>
    function disableselect(e){
    return false
    }

    function reEnable(){
    return true
    }

    //if IE4+
    document.onselectstart=new Function ("return false")
    document.oncontextmenu=new Function ("return false")
    //if NS6
    if (window.sidebar){
    document.onmousedown=disableselect
    document.onclick=reEnable
    }
    //-->
</script>*/?>