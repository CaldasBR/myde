<?php
    ini_set('display_errors','on');
	error_reporting(E_ALL);

	header('Content-Type: text/html; charset=utf-8');

    $funcao = "pagamento";
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
                case 'resgata_cliente':
                    //echo "Processo para buscar quem é o cliente logado no sistema. <br>";
                    $sql = "select id,NOME,SOBRENOME,NOME_COMPLETO,CEL,access_group,id_distribuidor from tb_usuarios where email = '". $_SESSION['user_email']."'";
                    include("/var/www/mp_desenv/bg_conexao_bd.php");
                    $query = mysqli_query($GLOBALS['con'],$sql);
        
                    if(mysqli_num_rows($query)>0){
                        $row = mysqli_fetch_row($query);
                    }
                    
                    $erro = 0;
                    $msg = array(
                            "id" => $_SESSION['user_id']
                            ,"nome" => $_SESSION['user_nome']
                            ,"sobrenome" => $_SESSION['user_sobrenome']
                            ,"nome_compl" => $_SESSION['user_nome_compl']
                            ,"cel" => $_SESSION['user_cel']
                            ,"email" => $_SESSION['user_email']
                    );
                    $opt = "resgata_cliente";
                    break;
                case 'salva_cartao': 
                    $card_id = $_POST["card_id"];
                    $card_number_length = $_POST["card_number_length"];
                    $cardholder_name = $_POST["cardholder_name"];
                    $cardholder_ident_type = $_POST["cardholder_ident_type"];
                    $cardholder_ident_number = $_POST["cardholder_ident_number"];
                    $date_created = $_POST["date_created"];
                    $date_due = $_POST["date_due"];
                    $date_last_updated = $_POST["date_last_updated"];
                    $date_used = $_POST["date_used"];
                    $expiration_month = $_POST["expiration_month"];
                    $expiration_year = $_POST["expiration_year"];
                    $first_six_digits = $_POST["first_six_digits"];
                    $id = $_POST["id"];
                    $last_four_digits = $_POST["last_four_digits"];
                    $live_mode = $_POST["live_mode"];
                    $luhn_validation = $_POST["luhn_validation"];
                    $public_key = $_POST["public_key"];
                    $security_code_length = $_POST["security_code_length"];
                    $status = $_POST["status"];
                    $method = $_POST["method"];
                    
                    $sql = "INSERT INTO tb_card_tokens (id_user,email,card_id,card_number_length,cardholder_name,cardholder_ident_type,cardholder_ident_number,date_created,date_due,date_last_updated,date_used,expiration_month,expiration_year,first_six_digits,id,last_four_digits,live_mode,luhn_validation,public_key,security_code_length,status,method) VALUES (".$_SESSION['user_id'].",'".$_SESSION['user_email']."','".$card_id."',".$card_number_length.",'".$cardholder_name."','".$cardholder_ident_type."','".$cardholder_ident_number."','".$date_created."','".$date_due."','".$date_last_updated."','".$date_used."',".$expiration_month.",".$expiration_year.",'".$first_six_digits."','".$id."','".$last_four_digits."',".$live_mode.",".$luhn_validation.",'".$public_key."',".$security_code_length.",'".$status."','".$method."');";
                    
                    //echo "SQL: " . $sql . "<br>";
                    include("/var/www/mp_desenv/bg_conexao_bd.php");
                    $query = mysqli_query($GLOBALS['con'],$sql);
                    
                    $sql2 = "Select card_id,email from tb_card_tokens where card_id='".$card_id."';";
                    $query2 = mysqli_query($GLOBALS['con'],$sql2);
                    if(mysqli_num_rows($query2)>0){
                        $erro = 0;
                        $msg = "Dados devidamente salvos!";
                        $opt = "criar_customer";
                    }else{
                        $erro = 1;
                        $msg = "Recebi os dados e não consegui salvar no BD.";
                        $opt = "Toast";
                    }
                    break;
                case "pagar":
                    $sql_tk = "select user_id_myde,access_token, user_id_merc from tb_auth_mercadopago where user_id_myde = ".$_SESSION['user_id_distribuidor']." and dt_time = (select max(dt_time) as max_dt_time from tb_auth_mercadopago where user_id_myde = ".$_SESSION['user_id_distribuidor'].");";
                    
                    include("/var/www/mp_desenv/bg_conexao_bd.php");
                    $query_tk = mysqli_query($GLOBALS['con'],$sql_tk);
                    
                    if(mysqli_num_rows($query_tk)>0){
                        $row_tk = mysqli_fetch_row($query_tk);
                        $token_distr = $row_tk[1];
                        $id_distr_merc = $row_tk[2];
                        
                        $sql = "Select id,email,method from tb_card_tokens where email='".$_SESSION['user_email']."' order by date_last_updated desc limit 1;";

                        include("/var/www/mp_desenv/bg_conexao_bd.php");
                        $query = mysqli_query($GLOBALS['con'],$sql);
                        
                        require_once ('sdk-mercadopago/lib/mercadopago.php');
                        
                        $customer = array (
                            "email" => $_SESSION['user_email']
                        );
                        $mp = new MP('APP_USR-7612629650074174-050422-b04a89099a51578583e8965efd6827f1__LB_LC__-249839521');
                        
                        //$mp = new MP('APP_USR-7612629650074174-050422-b04a89099a51578583e8965efd6827f1__LB_LC__-249839521');
                        
                        $saved_customer = $mp->get ("/v1/customers/search", $customer);
                        $customer_id = $saved_customer["response"]["results"][0]["id"];
                        
                        if(mysqli_num_rows($query)>0){
                            $row = mysqli_fetch_row($query);
                            $payment_data = array(
                                "transaction_amount" => 20,
                                "token" => $row[0],
                                "description" => "Teste produto 2 desenv.queromarita.com.br",
                                "installments" => 1,
                                "payment_method_id" => $row[2],
                                "payer" => array (
                                    "email" => $_SESSION['user_email'],
                                    "id" => $customer_id
                                ),
                                "statement_descriptor" => "Queromarita - MYDE",
                                "notification_url" => "http://desenv.queromarita.com.br/bg_answer_mercpago.php",
                                "additional_info"=> array(
                                    "items" => array(
                                            array(
                                                "id" => "item-ID-1234",
                                                "title" => "Title of what you are paying for",
                                                "picture_url" => "https://www.mercadopago.com/org-img/MP3/home/logomp3.gif",
                                                "description" => "O primeiro café funcional do Brasil",
                                                "category_id" => "fashion", // Available categories at https://api.mercadopago.com/item_categories
                                                "quantity" => 1,
                                                "unit_price" => 60
                                            )
                                    ),
                                    "shipments" => array(
                                        //"mode" => "me2",
                                        //"dimensions" => "30x30x30,500",
                                        //"local_pickup": true
                                        //    "free_methods" => array(
                                        //        "id"=> 100009
                                        //),
                                        "receiver_address" => array(
                                            "zip_code" => "07193020",
                                            "street_name" => "Rua Fonte Boa",
                                            "street_number" => 398
                                            //"floor" => 4,
                                            //"apartment" => "C"
                                        )
                                    )
                                )
                            );
                            efetua_pagamento($payment_data,$token_distr);
                        }
                    }
                    break;
            }
        }
    }else{
        $erro = 1;
        $msg = "Usuário não autenticado.";
        $opt = "expulsa";
    }

    function efetua_pagamento($payment_data2,$token_distr2){
        global $erro,$msg,$opt;
        
        require_once ('sdk-mercadopago/lib/mercadopago.php');
        if($token_distr2===null){
            $mp = new MP('APP_USR-7612629650074174-050422-b04a89099a51578583e8965efd6827f1__LB_LC__-249839521');
            
            //$mp = new MP('APP_USR-7612629650074174-050422-b04a89099a51578583e8965efd6827f1__LB_LC__-249839521');
        }else{
            $mp = new MP($token_distr2);
        }

        $payment = $mp->post("/v1/payments", $payment_data2);
        echo "Payment: ";
        print_r($payment);
        
        switch($payment["status"]){
            case 200:
                confirmar_pagamento($payment,$payment_data2["token"]);
                break;
            case 201:
                confirmar_pagamento($payment,$payment_data2["token"] );
                break;
            default:
                $erro = 1;
                $msg = "Erro no pagamento. Verifique os códigos de erro.";
                $opt = "Toast";
                
        }
    }

    function confirmar_pagamento($payment,$token_card){
        global $erro,$msg,$opt; 
        
        $payment_id = $payment["response"]["id"];
        $date_created = $payment["response"]["date_created"];
        $date_approved = $payment["response"]["date_approved"];
        $date_last_updated = $payment["response"]["date_last_updated"];
        $money_release_date = $payment["response"]["money_release_date"];
        $operation_type = $payment["response"]["operation_type"];
        $issuer_id = $payment["response"]["issuer_id"];
        $payment_method_id = $payment["response"]["payment_method_id"];
        $payment_type_id = $payment["response"]["payment_type_id"];
        $status = $payment["response"]["status"];
        $status_detail = $payment["response"]["status_detail"];
        $currency_id = $payment["response"]["currency_id"];
        $description = $payment["response"]["description"];
        $live_mode = $payment["response"]["live_mode"];
        $sponsor_id = $payment["response"]["sponsor_id"];
        $authorization_code = $payment["response"]["authorization_code"];
        $collector_id = $payment["response"]["collector_id"];
        $payer_id = $payment["response"]["payer"]["id"];
        $payer_email = $payment["response"]["payer"]["email"];
        $transaction_amount = $payment["response"]["transaction_amount"];
        $transaction_amount_refunded = $payment["response"]["transaction_amount_refunded"];
        $coupon_amount = $payment["response"]["coupon_amount"];
        $net_received_amount = $payment["response"]["transaction_details"]["net_received_amount"];
        $total_paid_amount = $payment["response"]["transaction_details"]["total_paid_amount"];
        $overpaid_amount = $payment["response"]["transaction_details"]["overpaid_amount"];
        $installment_amount = $payment["response"]["transaction_details"]["installment_amount"];
        $card_id = $payment["response"]["card"]["id"];
        $card_first_six_digits = $payment["response"]["card"]["first_six_digits"];
        $card_last_four_digits = $payment["response"]["card"]["last_four_digits"];
        $card_expiration_month = $payment["response"]["card"]["expiration_month"];
        $card_expiration_year = $payment["response"]["card"]["expiration_year"];
        $card_date_created = $payment["response"]["card"]["date_created"];
        $card_date_last_updated = $payment["response"]["card"]["date_last_updated"];
        $cardholder_name = $payment["response"]["card"]["cardholder"]["name"];
        $card_cpf = $payment["response"]["card"]["cardholder"]["identification"]["number"];
        
        $sql = "INSERT INTO tb_pgto_mercpago (id_user,email,payment_id,date_created,date_approved,date_last_updated,money_release_date,operation_type,issuer_id,payment_method_id,payment_type_id,status,status_detail,currency_id,description,live_mode,sponsor_id,authorization_code,collector_id,payer_id,payer_email,transaction_amount,transaction_amount_refunded,coupon_amount,net_received_amount,total_paid_amount,overpaid_amount,installment_amount,card_id,card_first_six_digits,card_last_four_digits,card_expiration_month,card_expiration_year,card_date_created,card_date_last_updated,cardholder_name,card_cpf) VALUES(".$_SESSION['user_id'].",'".$_SESSION['user_email']."',".$payment_id.",'".$date_created."','".$date_approved."','".$date_last_updated."','".$money_release_date."','".$operation_type."',".$issuer_id.",'".$payment_method_id."','".$payment_type_id."','".$status."','".$status_detail."','".$currency_id."','".$description."','".$live_mode."','".$sponsor_id."','".$authorization_code."',".$collector_id.",'".$payer_id."','".$payer_email."',".$transaction_amount.",".$transaction_amount_refunded.",".$coupon_amount.",".$net_received_amount.",".$total_paid_amount.",".$overpaid_amount.",".$installment_amount.",'".$card_id."','".$card_first_six_digits."','".$card_last_four_digits."',".$card_expiration_month.",".$card_expiration_year.",'".$card_date_created."','".$card_date_last_updated."','".$cardholder_name."','".$card_cpf."');";
        
        include("/var/www/mp_desenv/bg_conexao_bd.php");
        $query = mysqli_query($GLOBALS['con'],$sql);
        
        $sql2 = "SELECT id_user, email, payment_id from tb_pgto_mercpago where id_user=".$_SESSION['user_id']." and email='".$_SESSION['user_email']."' and payment_id=".$payment_id.";";
        
        $query2 = mysqli_query($GLOBALS['con'],$sql2);
        if(mysqli_num_rows($query2)>0){
            $erro = 0;
            $msg = "Pagamento Registrado!";
            $opt = "pagamento_concluido";
        }else{
            $erro = 1;
            $msg = "Recebi os dados e não consegui salvar no BD.";
            $opt = "Toast";
        }

        //#### adiciona cartao ao customer ####
        $customer = array (
            "email" => $_SESSION['user_email']
        );

        $saved_customer = $GLOBALS['mp']->get ("/v1/customers/search", $customer);
        $customer_id = $saved_customer["response"]["results"][0]["id"];
        $card = $GLOBALS['mp']->post ("/v1/customers/".$customer_id."/cards", array("token" =>$token_card));
    }

    $envia_resposta = array("erro"=>$erro,"msg"=>$msg,"funcao"=>$funcao,"opt"=>$opt);
    print json_encode($envia_resposta, JSON_PRETTY_PRINT);
?>