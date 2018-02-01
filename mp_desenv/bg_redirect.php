<?php
    ini_set('display_errors','on');
    error_reporting(E_ALL);

    require_once ('sdk-mercadopago/lib/mercadopago.php');
    
    if(isset($_GET['code'])){
        //$mp = new MP("TEST-7612629650074174-050422-4ed6c8a6ac614e236e7dadd74ce08da7__LB_LC__-249839521");
        $mp = new MP("APP_USR-7612629650074174-050422-b04a89099a51578583e8965efd6827f1__LB_LC__-249839521");
        
        session_start();
        $user_id = $_SESSION['user_id'];
        $code = addslashes($_GET['code']);
        
        $sql = "INSERT INTO tb_code_mercadopago (id_user,code) VALUES (".$user_id.",'".$code."');";

        //echo "SQL: " . $sql . "<br>";
        include("/var/www/mp_desenv/bg_conexao_bd.php");
        $query = mysqli_query($GLOBALS['con'],$sql);

        $sql2 = "Select id_user,code from tb_code_mercadopago where id_user=".$user_id." and code = '".$code."';";
        //echo "SQL2: " . $sql2 . "<br>";
        
        include("/var/www/mp_desenv/bg_conexao_bd.php");
        $query2 = mysqli_query($GLOBALS['con'],$sql2);
        if(mysqli_num_rows($query2)>0){
            $erro = 0;
            $msg = "Dados devidamente salvos!";
            $opt = "obter_token_distribuidor";
            
            //echo "Code: " . $code . "<br>";
            obtem_credenciais($code,$user_id);
        }else{
            $erro = 1;
            $msg = "Recebi os dados e não consegui salvar no BD.";
            $opt = "Toast";
            $envia_resposta = array("erro"=>$erro,"msg"=>$msg,"funcao"=>"redir_aut_mercadopago","opt"=>$opt);
            print json_encode($envia_resposta, JSON_PRETTY_PRINT);
        }
        
        //$envia_resposta = array("erro"=>$erro,"msg"=>$msg,"funcao"=>"redir_aut_mercadopago","opt"=>$opt);
        //print json_encode($envia_resposta, JSON_PRETTY_PRINT);
    }
    
    function obtem_credenciais($code,$user_id){
        //echo "Code cred: " . $code . "<br>";
        //echo "userid cred: " . $user_id . "<br>";
        
        //$mp = new MP("TEST-7612629650074174-050422-4ed6c8a6ac614e236e7dadd74ce08da7__LB_LC__-249839521");
        $mp = new MP("APP_USR-7612629650074174-050422-b04a89099a51578583e8965efd6827f1__LB_LC__-249839521");

        $request = array(
            "uri" => "/oauth/token",
            "data" => array(
                 "client_secret" => $mp->get_access_token(),
                 "grant_type" => "authorization_code",
                 "code" => $code,
                 "redirect_uri" => "http://desenv.queromarita.com.br/bg_redirect.php"
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
        
        $sql = "INSERT INTO tb_auth_mercadopago (user_id_myde,access_token,public_key,refresh_token,live_mode,user_id_merc,token_type,expires_in,scope) VALUES (".$user_id.",'".$access_token."','".$public_key."','".$refresh_token."','".$live_mode."','".$user_id_merc."','".$token_type."','".$expires_in."','".$scope."');";

        //echo "SQL: " . $sql . "<br>";
        include("/var/www/mp_desenv/bg_conexao_bd.php");
        $query = mysqli_query($GLOBALS['con'],$sql);

        $sql2 = "Select user_id_myde,access_token from tb_auth_mercadopago where user_id_myde=".$user_id." and access_token = '".$access_token."';";
        //echo "SQL2: " . $sql2 . "<br>";
        
        include("/var/www/mp_desenv/bg_conexao_bd.php");
        $query2 = mysqli_query($GLOBALS['con'],$sql2);
        if(mysqli_num_rows($query2)>0){
            $erro = 0;
            $msg = "Tokens devidamente salvos!";
            $opt = "Distribuidor Vinculado!";
            $envia_resposta = array("erro"=>$erro,"msg"=>$msg,"funcao"=>"redir_aut_mercadopago","opt"=>$opt);
            print json_encode($envia_resposta, JSON_PRETTY_PRINT);
            header("Location: http://desenv.queromarita.com.br/pagamento.html");
        }else{
            $erro = 1;
            $msg = "Recebi os dados do token e não consegui salvar no BD.";
            $opt = "Toast";
            $envia_resposta = array("erro"=>$erro,"msg"=>$msg,"funcao"=>"redir_aut_mercadopago","opt"=>$opt);
            print json_encode($envia_resposta, JSON_PRETTY_PRINT);
        }
        
        
    }
?>