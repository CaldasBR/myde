<?php
    //ini_set('display_errors','on');
	//error_reporting(E_ALL);
    
	header('Content-Type: text/html; charset=utf-8');

    $funcao = "assinatura";
    $erro = 0;
    $msg = "Nada foi executado!";
    $opt = "";

    //Verifica se usuário está Logado
    include("/var/www/mp_desenv/bg_protege_php.php");
    $status_log = status_login($resposta); //acredite que $resposta existe, ele está no bg_protege_php
    //echo "status_log: ".$status_log."<br>";
    
    //Se estiver logado executa o código
    if($status_log=="mantem"){
        if(isset($_GET["fn"]) || isset($_POST["fn"])){
            if(isset($_GET["fn"])){
                $fn = addslashes($_GET["fn"]);
            }else{
                $fn = addslashes($_POST["fn"]);
            }
            
            switch ($fn){
                case 'contratar_assinatura':
                    $id_assinatura = addslashes($_POST["id_assinatura"]);
                    $sql = "select id,description,status from tb_planos_assinaturas where id_myde = ".$id_assinatura." and status='active';";
                    
                    //echo "SQL: ".$sql;
                    include("/var/www/mp_desenv/bg_conexao_bd.php");
                    $query = mysqli_query($GLOBALS['con'],$sql);
                    if(mysqli_num_rows($query)>0){
                        $row = mysqli_fetch_row($query);
                        //echo "Inscrever customer. <br><br>";
                        inscrever_customer($row[0], $_SESSION["user_email"]);
                    }else{
                        $erro=1;
                        $msg='Assinatura não localizada.';
                        $opt='Toast';
                    }
                break;
                case 'criar_assinatura':
                    criar_assinatura('Plano top chat', 1, 'months', 0, 'months',69.90);
                break;
            }
        }else{
            $erro = 1;
            $msg = "Erro de solicitação.";
            $opt = "Toast";
        }
    }else{
        $erro = 1;
        $msg = "Usuário não autenticado.";
        $opt = "expulsa";
    }
    
    function criar_assinatura($descricao, $freq, $freq_type, $freq_free, $freq_type_free,$valor){
        global $erro,$msg,$opt;
        $header = array(
            "accept" => "application/json"
            ,"content-type" => "application/json"
        );

        $params = array(
            "description" => $descricao
            ,"auto_recurring" => array(
                "frequency" => $freq
                ,"frequency_type" => $freq_type
                ,"transaction_amount" => $valor
                //,"free_trial" => array(
                //    "frequency" => $freq_free
                //    ,"frequency_type" => $freq_type_free
                //)
            )
            ,"external_reference" => "plano 3"
        );

        //print_r ($params);
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://api.mercadopago.com/v1/plans?access_token=APP_USR-7612629650074174-050422-b04a89099a51578583e8965efd6827f1__LB_LC__-249839521");
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
        $result = curl_exec($ch);
        //echo "<br><br> resultado: <br>";
        //var_dump($result);
        curl_close($ch);

        $ResponseHeader = explode("\n",$result);
        $arr_status_http = explode(" ",$ResponseHeader[0]);
        $status_http = $arr_status_http[1];
        $resposta = $ResponseHeader[17];
        $dados = json_decode($resposta, true);

        if(array_key_exists("id",$dados)){
            //$dados = $envia["resposta"];
            $application_fee = !empty($dados["application_fee"]) ? $dados["application_fee"] : 0;
            $frequency = !empty($dados["auto_recurring"]["frequency"]) ? $dados["auto_recurring"]["frequency"] : 0;
            $transaction_amount = !empty($dados["auto_recurring"]["transaction_amount"]) ? $dados["auto_recurring"]["transaction_amount"] : 0;
            $repetitions = !empty($dados["auto_recurring"]["repetitions"]) ? $dados["auto_recurring"]["repetitions"] : 0;
            $trial_frequency = !empty($dados["auto_recurring"]["free_trial"]["frequency"]) ? $dados["auto_recurring"]["free_trial"]["frequency"] : 0;
            $setup_fee = !empty($dados["setup_fee"]) ? $dados["setup_fee"] : 0;

            $sql = "INSERT INTO tb_planos_assinaturas (id,application_fee,status,description,external_reference,date_created,last_modified,frequency,frequency_type,transaction_amount,currency_id,repetitions,debit_date,trial_frequency,trial_frequency_type,live_mode,setup_fee,metadata) VALUES('".$dados["id"]."',".$application_fee.",'".$dados["status"]."','".$dados["description"]."','".$dados["external_reference"]."','".$dados["date_created"]."','".$dados["last_modified"]."',".$frequency.",'".$dados["auto_recurring"]["frequency_type"]."',".$transaction_amount.",'".$dados["auto_recurring"]["currency_id"]."',".$repetitions.",'".$dados["auto_recurring"]["debit_date"]."',".$trial_frequency.",'".$dados["auto_recurring"]["free_trial"]["frequency_type"]."','".$dados["live_mode"]."',".$setup_fee.",'".$dados["metadata"]."');";

            //echo "SQL: " . $sql . "<br><br>";
            include("/var/www/mp_desenv/bg_conexao_bd.php");
            $query = mysqli_query($GLOBALS['con'],$sql);

            $sql2 = "SELECT id_myde,id,description from tb_planos_assinaturas where id='".$dados["id"]."' and date_created='".$dados["date_created"]."';";
            //echo "SQL2: " . $sql2 . "<br><br>";

            include("/var/www/mp_desenv/bg_conexao_bd.php");        
            $query2 = mysqli_query($GLOBALS['con'],$sql2);
            if(mysqli_num_rows($query2)>0){
                $erro=0;
                $msg='Plano de assinatura salvo.';
                $opt='Toast';
            }else{
                $erro=1;
                $msg='Erro ao salvar plano de assinatura.';
                $opt='Toast';
            }
        }else{
            $erro=1;
            $msg='Ocerreu algum erro durante o processamento.';
            $opt='Toast';
        }
    }
    //criar_assinatura('Assinatura Padrão, 1 mês grátis', 1, 'months', 1, 'months',30);

    function inscrever_customer($planID, $email){
        global $erro,$msg,$opt;
        $header = array(
            "accept" => "application/json"
            ,"content-type" => "application/json"
        );

        $filters = array (
            "email" => $email
        );

        require_once ('sdk-mercadopago/lib/mercadopago.php');
        $mp = new MP ("APP_USR-7612629650074174-050422-b04a89099a51578583e8965efd6827f1__LB_LC__-249839521");
        $customer = $mp->get ("/v1/customers/search", $filters);
        $customer_id = $customer["response"]["results"][0]["id"];

        $params = array(
            "plan_id" => $planID
            ,"payer" => array(
                "id" => $customer_id
            )
        );
        
        //echo "Parametros: <br>";
        //var_dump($params);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://api.mercadopago.com/v1/subscriptions?access_token=APP_USR-7612629650074174-050422-b04a89099a51578583e8965efd6827f1__LB_LC__-249839521");
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
        $result = curl_exec($ch);
        //var_dump($result);
        curl_close($ch);
        $ResponseHeader = explode("\n",$result);
        $arr_status_http = explode(" ",$ResponseHeader[0]);
        $status_http = $arr_status_http[1];
        $resposta = $ResponseHeader[17];
        $envia = json_encode(array('status_http' => $status_http , 'resposta' => json_decode($resposta)));
        $resposta_decoded = json_decode($resposta, true);
        
        //var_dump($status);
        //echo "Resposta: <br>";
        //var_dump(json_decode($envia, true));
        //echo "<br> <br>";
        
        if($status_http=='200' || $status_http=='201'){
            $sql = "INSERT INTO tb_assinaturas (id_myde,id,plan_id,payer_id,payer_type,payer_email,payer_cpf,application_fee,status,description,external_reference,date_created,last_modified,live_mode,start_date,end_date,next_payment_date,setup_fee) VALUES (".$_SESSION["user_id"].",'".$resposta_decoded["id"]."','".$resposta_decoded["plan_id"]."','".$resposta_decoded["payer"]["id"]."','".$resposta_decoded["payer"]["type"]."','".$resposta_decoded["payer"]["email"]."','".$resposta_decoded["payer"]["identification"]["number"]."','".$resposta_decoded["application_fee"]."','".$resposta_decoded["status"]."','".$resposta_decoded["description"]."','".$resposta_decoded["external_reference"]."','".$resposta_decoded["date_created"]."','".$resposta_decoded["last_modified"]."','".$resposta_decoded["live_mode"]."','".$resposta_decoded["start_date"]."','".$resposta_decoded["end_date"]."','".$resposta_decoded["charges_detail"]["next_payment_date"]."','".$resposta_decoded["setup_fee"]."');";
            
            //echo "SQL2: " . $sql . "<br><br>";
            include("/var/www/mp_desenv/bg_conexao_bd.php");
            $query = mysqli_query($GLOBALS['con'],$sql);

            $sql2 = "SELECT id_myde,id,date_created from tb_assinaturas where id='".$resposta_decoded["id"]."' and date_created='".$resposta_decoded["date_created"]."';";
            //echo "SQL3: " . $sql2 . "<br><br>";
            
            include("/var/www/mp_desenv/bg_conexao_bd.php");        
            $query2 = mysqli_query($GLOBALS['con'],$sql2);
            
            $sql3 = "UPDATE tb_usuarios SET access_group='vendedor', id_distribuidor=".$_SESSION["user_id"]." WHERE id=".$_SESSION["user_id"].";";

            include("/var/www/mp_desenv/bg_conexao_bd.php");        
            $query3 = mysqli_query($GLOBALS['con'],$sql3);

            $sql4 = "SELECT id,access_group from tb_usuarios where access_group='vendedor' and id=".$_SESSION["user_id"].";";
                
            include("/var/www/mp_desenv/bg_conexao_bd.php");        
            $query4 = mysqli_query($GLOBALS['con'],$sql4);
            
            if(mysqli_num_rows($query4)>0){
                //Enviar email de boas vindas
                include_once("/var/www/mp_desenv/bg_enviaemail.php");
                $dados = array("1"=>1);
                $resposta = define_envia('boas_vindas_vendedor',$dados);
                
                $erro=0;
                $msg='Assinatura efetuada com sucesso!';
                $opt='redirect_escritorio';
                
            }else{
                $erro=1;
                $msg='Assinatura efetuada mas não consegui registrar no banco de dados.';
                $opt='Toast';
            }  
        }else{
            $erro=1;
            $msg='Houve erro ao vincular assinante a assinatura.';
        }
    }
    //inscrever_customer('036aa6d685c646ce84377685d2218267', 'filipe_caldas@msn.com');


    //########################### PROCURA CUSTOMER ##############################
    /*
    require_once ('sdk-mercadopago/lib/mercadopago.php');
    $mp = new MP ("TEST-7612629650074174-050422-4ed6c8a6ac614e236e7dadd74ce08da7__LB_LC__-249839521");
    $filters = array (
        "email" => "filipe_caldas@msn.com"
    );

    $customer = $mp->get ("/v1/customers/search", $filters);
    print_r ($customer);
    */

    //######################## VER ASSINATURA #############################
    /*
    require_once ('sdk-mercadopago/lib/mercadopago.php');
    $mp = new MP ("TEST-7612629650074174-050422-4ed6c8a6ac614e236e7dadd74ce08da7__LB_LC__-249839521");
    $subscription = $mp->get ("/v1/subscriptions/604a2796aee0400d91d9c1a1b7af2ff0");
    print_r ($subscription["response"]);
    */

    //####################### VER INVOICES ################################
    /*
    //Token de teste não são autorizados
    require_once ('sdk-mercadopago/lib/mercadopago.php');
    $mp = new MP ("TEST-7612629650074174-050422-4ed6c8a6ac614e236e7dadd74ce08da7__LB_LC__-249839521");
    $subscription = $mp->get ("/v1/invoices/129a0696b67c40f299a1590f1e1f5831");
    print_r ($subscription["response"]);
    */


    //######################## VER PAGAMENTOS #############################
    /*
    require_once ('sdk-mercadopago/lib/mercadopago.php');
    $mp = new MP ("TEST-7612629650074174-050422-4ed6c8a6ac614e236e7dadd74ce08da7__LB_LC__-249839521");
    $payments = $mp->get ("/v1/payments/search");
    print_r ($payments["response"]);
    */


    //######################## VER HISTORICO DA CONTA #############################
    /*
    require_once ('sdk-mercadopago/lib/mercadopago.php');
    $mp = new MP ("TEST-7612629650074174-050422-4ed6c8a6ac614e236e7dadd74ce08da7__LB_LC__-249839521");
    $history = $mp->get ("/v1/balance/history");
    print_r ($history["response"]);
    */


    //######################## VER MOVIMENTAÇÕES DE UM USUÁRIO EM UM PERÍODO DE UM MÊS #############################
    function lista_mov_customer($customerID){
        global $erro,$msg,$opt;
        $header = array(
            "accept" => "application/json"
            ,"content-type" => "application/json"
        );

        $params = array(
            "access_token" => "APP_USR-7612629650074174-050422-b04a89099a51578583e8965efd6827f1__LB_LC__-249839521"
            ,"user_id" => $customerID
            ,"range" => "date_created"
            ,"begin_date" => "NOW-1MONTH"
            ,"end_date" => "NOW"
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://api.mercadopago.com/mercadopago_account/movements/search");
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
        $result = curl_exec($ch);
        //var_dump($result);
        curl_close($ch);
        $ResponseHeader = explode("\n",$result);
        $arr_status_http = explode(" ",$ResponseHeader[0]);
        $status_http = $arr_status_http[1];
        $resposta = $ResponseHeader[17];
        $envia = json_encode(array('status_http' => $status_http , 'resposta' => json_decode($resposta)));
        //var_dump($status);
        //echo "Resposta: <br>";
        //var_dump(json_decode($envia, true));
        //echo "<br> <br>";
    }
    //lista_mov_customer("78744562-dZRd27DJlb11AJ");


    //######################## VER MOVIMENTAÇÕES DE RECEBIMENTO #############################
    function lista_mov_receb(){
        global $erro,$msg,$opt;
        $header = array(
            "accept" => "application/json"
            ,"content-type" => "application/json"
        );

        $params = array(
            "access_token" => "APP_USR-7612629650074174-050422-b04a89099a51578583e8965efd6827f1__LB_LC__-249839521"
            ,"type" => "income"
            ,"offset" => 0
            ,"limit" => 100
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://api.mercadopago.com/mercadopago_account/movements/search");
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
        $result = curl_exec($ch);
        //var_dump($result);
        curl_close($ch);
        $ResponseHeader = explode("\n",$result);
        $arr_status_http = explode(" ",$ResponseHeader[0]);
        $status_http = $arr_status_http[1];
        $resposta = $ResponseHeader[17];
        $envia = json_encode(array('status_http' => $status_http , 'resposta' => json_decode($resposta)));
        //var_dump($status);
        //echo "Resposta: <br>";
        //var_dump(json_decode($envia, true));
        //echo "<br> <br>";
    }
    //lista_mov_receb();

    $envia_resposta = array("erro"=>$erro,"msg"=>$msg,"funcao"=>$funcao,"opt"=>$opt);
    print json_encode($envia_resposta, JSON_PRETTY_PRINT);
?>