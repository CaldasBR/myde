<?php
    include("/var/www/mp_desenv/bg_cookie.php");
    $erro = 0;
    $msg = '';
    $funcao = 'protege_pagina';

    $resposta = rememberMe();
    //echo $resposta;
    if($resposta==='index.html' || $resposta=='Usuário não autenticado.'){
        $erro = 0;
        $msg = 'index.html';
        $opt = "expulsa";
    }else{
        $erro = 0;
        $opt = "mantem";
    }
  
    $envia_resposta = array("erro"=>$erro,"msg"=>$msg,"funcao"=>$funcao,"opt"=>$opt);
    print json_encode($envia_resposta);
?>