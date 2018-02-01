<?php
    //ini_set('display_errors','on');
    //error_reporting(E_ALL);

    header('Content-Type: text/html; charset=utf-8');
    session_start();
    include("/var/www/mp_desenv/bg_conexao_bd.php");

    $msg = "Nada foi executado!";
    $funcao = "loja";
    $opt = "loja__carregar_dados_dist";
    $erro = 0;
    $code="";

    //Verifica se distribuidor está definido
    //Verifica se usuário está Logado'
    include("/var/www/mp_desenv/bg_protege_php.php");
    $status_log = status_login($resposta); //acredite que $resposta existe, ele está no bg_protege_php
    //echo "status_log: ".$status_log."<br>";
    
    //Se estiver logado executa o código
    if($status_log=="mantem"){
        $sql_consulta1="select nome_completo, email, cel, imagem from tb_usuario where id='".$_SESSION["user_id_distribuidor"]."'";";
        $consulta1 = mysqli_query($GLOBALS['con'],$sql_consulta1);        
        if(mysqli_num_rows($consulta1)>0){
            //Se existir um anúncio com esse ID faz as demais consultas...
            //$envia_dados = array();
            for($i=0;$i<mysqli_num_rows($consulta1);$i++){
                $dados1 = mysqli_fetch_row($consulta1);
                $nome=$dados1[0];
                $email=$dados1[1];
                $cel=$dados1[2];
                $imagem=$dados1[3];
        
        
     }else{
        $erro = 1;
        $msg = "Usuário não autenticado";
        $opt = "popup";
    }

    mysqli_close($GLOBALS['con']);
    $envia_resposta = array("erro"=>$erro,"nome"=>$nome,"email"=>$email,"cel"=>$cel,"imagem"=>$imagem, "funcao"=>$funcao, "msg"=> "$opt=>$opt);
    print json_encode($envia_resposta, JSON_PRETTY_PRINT);
?>
        