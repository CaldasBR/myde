<?php
    //ini_set('display_errors','on');
	//error_reporting(E_ALL);

    include("/var/www/mp_desenv/bg_cookie.php");

    $resposta = rememberMe();
    //status_login($resposta);

    //echo $resposta;
    function status_login($resposta){
        if($resposta==='index.html' || $resposta=='Usuário não autenticado.'){
            return "expulsa";
            //echo "expulsa";
        }else{
            return "mantem";
            //echo "mantem";
        }
    };
?>