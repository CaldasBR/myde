<?php
function atualizar_token($token_myde, $refresh_token){
    ini_set('display_errors','on');
    error_reporting(E_ALL);
    session_start();
    require_once ('sdk-mercadopago/lib/mercadopago.php');
    $mp = new MP($token_myde);

    $request = array(
            "uri" => "/oauth/token",
            "data" => array(
                 "client_secret" => $mp->get_access_token(),
                 "grant_type" => "refresh_token",
                 "refresh_token" => $refresh_token
            ),
            "headers" => array(
                "content-type" => "application/x-www-form-urlencoded"
            ),
            "authenticate" => false
        );

    $token_resp = $mp->post($request);
        //print_r ($token_resp["response"]);
        $access_token = $token_resp["response"]['access_token'];
        $public_key = $token_resp["response"]['public_key'];
        $refresh_token = $token_resp["response"]['refresh_token'];
        $live_mode = $token_resp["response"]['live_mode'];
        $user_id_merc = $token_resp["response"]['user_id'];
        $token_type = $token_resp["response"]['token_type'];
        $expires_in = $token_resp["response"]['expires_in'];
        $scope = $token_resp["response"]['scope'];
        $user_id=$_SESSION['user_id_distribuidor'];
        
        $sql = "INSERT INTO tb_auth_mercadopago (user_id_myde,access_token,public_key,refresh_token,live_mode,user_id_merc,token_type,expires_in,scope) VALUES (".$user_id.",'".$access_token."','".$public_key."','".$refresh_token."','".$live_mode."','".$user_id_merc."','".$token_type."','".$expires_in."','".$scope."');";

        //echo "SQL: " . $sql . "<br>";
        include("/var/www/mp/bg_conexao_bd.php");
        $query = mysqli_query($GLOBALS['con'],$sql);

        $sql2 = "Select user_id_myde,access_token from tb_auth_mercadopago where user_id_myde=".$user_id." and access_token = '".$access_token."';";
        //echo "SQL2: " . $sql2 . "<br>";
        
        include("/var/www/mp/bg_conexao_bd.php");
        $query2 = mysqli_query($GLOBALS['con'],$sql2);
        if(mysqli_num_rows($query2)>0){
            $erro = 0;
            $msg = "Tokens devidamente salvos!";
            $opt = "Distribuidor Vinculado!";
            $envia_resposta = array("erro"=>$erro,"msg"=>$msg,"funcao"=>"redir_aut_mercadopago","opt"=>$opt);
            print json_encode($envia_resposta, JSON_PRETTY_PRINT);
        }else{
            $erro = 1;
            $msg = "Recebi os dados do token e não consegui salvar no BD.".$sql;
            $opt = "Toast";
            $envia_resposta = array("erro"=>$erro,"msg"=>$msg,"funcao"=>"redir_aut_mercadopago","opt"=>$opt);
            print json_encode($envia_resposta, JSON_PRETTY_PRINT);
        }
    }
    atualizar_token("TEST-7612629650074174-050422-4ed6c8a6ac614e236e7dadd74ce08da7__LB_LC__-249839521", "TG-590bbab1e4b0539f2ae0b7d8-249839521");
?>