<?php
    ini_set('display_errors','on');
    error_reporting(E_ALL);

    //DETALHE NOTIFICAÇÃO
    if(isset($_GET["id"])){
        $id_notif_myde = addslashes($_GET["id"]);

        $sql = "SELECT id_notif_myde,type,data_id from tb_notif_header_mercpago where id_notif_myde=".$id_notif_myde.";";

        include("/var/www/mp/bg_conexao_bd.php");        
        $query = mysqli_query($GLOBALS['con'],$sql);
        if(mysqli_num_rows($query)>0){
            $row = mysqli_fetch_row($query);
            
            require_once ('sdk-mercadopago/lib/mercadopago.php');
            //chave em producao
            $mp = new MP("APP_USR-7612629650074174-050422-b04a89099a51578583e8965efd6827f1__LB_LC__-249839521");
            
            switch ($row[1]) {
                case 'payment':
                    $url = "/collections/notifications/".$row[2];
                    //$url = "/collections/notifications/2787054197";

                    $info = $mp->get($url);
                    $pgto = $info['response']["collection"];
                    $sql_select = "SELECT id from tb_pagamento_loja where id=".$pgto['id'].";";
                    include("/var/www/mp/bg_conexao_bd.php");
                    $query = mysqli_query($GLOBALS['con'],$sql_select);

                    if(mysqli_num_rows($query)>0){
                        $sql = "
                        UPDATE tb_pagamento_loja SET
                            date_created = '".$pgto['date_created']."'
                            ,date_approved = '".$pgto['date_approved']."'
                            ,money_release_date = '".$pgto['money_release_date']."'
                            ,last_modified = '".$pgto['last_modified']."'
                            ,payer_id = '".$pgto['payer']['id']."'
                            ,payer_first_name = '".$pgto['payer']['first_name']."'
                            ,payer_last_name = '".$pgto['payer']['last_name']."'
                            ,payer_phone_area = '".$pgto['payer']['phone']['area_code']."'
                            ,payer_phone_number = '".$pgto['payer']['phone']['number']."'
                            ,payer_cpf = '".$pgto['payer']['identification']['number']."'
                            ,payer_email = '".$pgto['payer']['email']."'
                            ,payer_nickname = '".$pgto['payer']['nickname']."'
                            ,order_id = '".$pgto['order_id']."'
                            ,external_reference = '".$pgto['external_reference']."'
                            ,merchant_order_id = '".$pgto['merchant_order_id']."'
                            ,currency_id = '".$pgto['currency_id']."'
                            ,transaction_amount = '".$pgto['transaction_amount']."'
                            ,net_received_amount = '".$pgto['net_received_amount']."'
                            ,total_paid_amount = '".$pgto['total_paid_amount']."'
                            ,shipping_cost = '".$pgto['shipping_cost']."'
                            ,coupon_amount = '".$pgto['coupon_amount']."'
                            ,coupon_fee = '".$pgto['coupon_fee']."'
                            ,finance_fee = '".$pgto['finance_fee']."'
                            ,discount_fee = '".$pgto['discount_fee']."'
                            ,coupon_id = '".$pgto['coupon_id']."'
                            ,status = '".$pgto['status']."'
                            ,status_detail = '".$pgto['status_detail']."'";

                        if(isset($pgto['installments'])){
                            $sql = $sql . ",installments ='".$pgto['installments']."'";
                        }else{
                            $sql = $sql . ",installments =''";
                        }

                        $sql = $sql . "
                            ,issuer_id ='".$pgto['issuer_id']."'
                            ,installment_amount = '".$pgto['installment_amount']."'
                            ,deferred_period = '".$pgto['deferred_period']."'
                            ,payment_type = '".$pgto['payment_type']."'
                            ,payment_method_id = '".$pgto['payment_method_id']."'
                            ,marketplace = '".$pgto['marketplace']."'
                            ,operation_type = '".$pgto['operation_type']."'";

                        if(isset($pgto['transaction_order_id'])){
                            $sql = $sql . "
                            ,card_transaction_order_id = '".$pgto['transaction_order_id']."'
                            ,card_statement_descriptor = '".$pgto['statement_descriptor']."'
                            ,cardholder_name = '".$pgto['cardholder']['name']."'
                            ,cardholder_cpf = '".$pgto['cardholder']['identification']['type']."'
                            ,card_authorization_code = '".$pgto['authorization_code']."'
                            ,transaction_order_id = '".$pgto['transaction_order_id']."'
                            ,card_last_four_digits = '".$pgto['last_four_digits']."'";
                        }else{
                            $sql = $sql . "
                            ,card_transaction_order_id = ''
                            ,card_statement_descriptor = ''
                            ,cardholder_name = ''
                            ,cardholder_cpf = ''
                            ,card_authorization_code = ''
                            ,transaction_order_id = ''
                            ,card_last_four_digits = ''";
                        }

                        $sql = $sql . ",marketplace_fee = '".$pgto['marketplace_fee']."'";

                        if(isset($pgto['refunds']['id'])){
                            $sql = $sql . "
                                ,extorno_id = '".$pgto['refunds']['id']."'
                                ,extorno_gtw_refund_id = '".$pgto['refunds']['gtw_refund_id']."'
                                ,extorno_movement_id = '".$pgto['refunds']['movement_id']."'
                                ,extorno_collection_id = '".$pgto['refunds']['collection_id']."'
                                ,extorno_amount = '".$pgto['refunds']['amount']."'
                                ,extorno_source_id = '".$pgto['refunds']['id']."'
                                ,extorno_source_name = '".$pgto['refunds']['name']."'
                                ,extorno_source_type = '".$pgto['refunds']['type']."'
                                ,extorno_date_created = '".$pgto['refunds']['date_created']."'";
                        }else{
                            $sql = $sql . "
                                ,extorno_id = ''
                                ,extorno_gtw_refund_id = ''
                                ,extorno_movement_id = ''
                                ,extorno_collection_id = ''
                                ,extorno_amount = ''
                                ,extorno_source_id = ''
                                ,extorno_source_name = ''
                                ,extorno_source_type = ''
                                ,extorno_date_created = ''";
                        }

                        $sql = $sql . "
                            ,amount_refunded = '".$pgto['amount_refunded']."'
                            ,last_modified_by_admin = '".$pgto['last_modified_by_admin']."'
                            ,collector_id = '".$pgto['collector']['id']."'
                            ,collector_first_name = '".$pgto['collector']['first_name']."'
                            ,collector_last_name = '".$pgto['collector']['last_name']."'
                            ,collector_area_code = '".$pgto['collector']['phone']['area_code']."'
                            ,collector_number = '".$pgto['collector']['phone']['number']."'
                            ,collector_extension = '".$pgto['collector']['phone']['extension']."'
                            ,collector_email = '".$pgto['collector']['email']."'
                            ,collector_nickname = '".$pgto['collector']['nickname']."'
                            WHERE ID = '".$pgto['id']."';";
                            //,collector_nickname = 'teste'
                    }else{
                        $sql = 
                            "INSERT INTO tb_pagamento_loja (
                                id
                                ,date_created
                                ,date_approved
                                ,money_release_date
                                ,last_modified
                                ,payer_id
                                ,payer_first_name
                                ,payer_last_name
                                ,payer_phone_area
                                ,payer_phone_number
                                ,payer_cpf
                                ,payer_email
                                ,payer_nickname
                                ,order_id
                                ,external_reference
                                ,merchant_order_id
                                ,currency_id
                                ,transaction_amount
                                ,net_received_amount
                                ,total_paid_amount
                                ,shipping_cost
                                ,coupon_amount
                                ,coupon_fee
                                ,finance_fee
                                ,discount_fee
                                ,coupon_id
                                ,status
                                ,status_detail
                                ,installments
                                ,issuer_id
                                ,installment_amount
                                ,deferred_period
                                ,payment_type
                                ,payment_method_id
                                ,marketplace
                                ,operation_type
                                ,card_transaction_order_id
                                ,card_statement_descriptor
                                ,cardholder_name
                                ,cardholder_cpf
                                ,card_authorization_code
                                ,transaction_order_id
                                ,card_last_four_digits
                                ,marketplace_fee
                                ,extorno_id
                                ,extorno_gtw_refund_id
                                ,extorno_movement_id
                                ,extorno_collection_id
                                ,extorno_amount
                                ,extorno_source_id
                                ,extorno_source_name
                                ,extorno_source_type
                                ,extorno_date_created
                                ,amount_refunded
                                ,last_modified_by_admin
                                ,collector_id
                                ,collector_first_name
                                ,collector_last_name
                                ,collector_area_code
                                ,collector_number
                                ,collector_extension
                                ,collector_email
                                ,collector_nickname
                            )

                            VALUES (
                            '" . $pgto['id'] . "'
                            ,'".$pgto['date_created']."'
                            ,'".$pgto['date_approved']."'
                            ,'".$pgto['money_release_date']."'
                            ,'".$pgto['last_modified']."'
                            ,'".$pgto['payer']['id']."'
                            ,'".$pgto['payer']['first_name']."'
                            ,'".$pgto['payer']['last_name']."'
                            ,'".$pgto['payer']['phone']['area_code']."'
                            ,'".$pgto['payer']['phone']['number']."'
                            ,'".$pgto['payer']['identification']['number']."'
                            ,'".$pgto['payer']['email']."'
                            ,'".$pgto['payer']['nickname']."'
                            ,'".$pgto['order_id']."'
                            ,'".$pgto['external_reference']."'
                            ,'".$pgto['merchant_order_id']."'
                            ,'".$pgto['currency_id']."'
                            ,'".$pgto['transaction_amount']."'
                            ,'".$pgto['net_received_amount']."'
                            ,'".$pgto['total_paid_amount']."'
                            ,'".$pgto['shipping_cost']."'
                            ,'".$pgto['coupon_amount']."'
                            ,'".$pgto['coupon_fee']."'
                            ,'".$pgto['finance_fee']."'
                            ,'".$pgto['discount_fee']."'
                            ,'".$pgto['coupon_id']."'
                            ,'".$pgto['status']."'
                            ,'".$pgto['status_detail']."'";

                        if(isset($pgto['installments'])){
                            $sql = $sql . ",".$pgto['installments'];
                        }else{
                            $sql = $sql . ",''";
                        }

                        $sql = $sql . "
                        ,'".$pgto['issuer_id']."'
                        ,'".$pgto['installment_amount']."'
                        ,'".$pgto['deferred_period']."'
                        ,'".$pgto['payment_type']."'
                        ,'".$pgto['payment_method_id']."'
                        ,'".$pgto['marketplace']."'
                        ,'".$pgto['operation_type']."'";

                        if(isset($pgto['transaction_order_id'])){
                            $sql = $sql . "
                            ,'".$pgto['transaction_order_id']."'
                            ,'".$pgto['statement_descriptor']."'
                            ,'".$pgto['cardholder']['name']."'
                            ,'".$pgto['cardholder']['identification']['type']."'
                            ,'".$pgto['authorization_code']."'
                            ,'".$pgto['transaction_order_id']."'
                            ,'".$pgto['last_four_digits']."'";
                        }else{
                            $sql = $sql . ",null,null,null,null,null,null,null";
                        }

                        $sql = $sql . ",".$pgto['marketplace_fee'];

                        if(isset($pgto['refunds']['id'])){
                            $sql = $sql . "
                                ,'".$pgto['refunds']['id']."'
                                ,'".$pgto['refunds']['gtw_refund_id']."'
                                ,'".$pgto['refunds']['movement_id']."'
                                ,'".$pgto['refunds']['collection_id']."'
                                ,'".$pgto['refunds']['amount']."'
                                ,'".$pgto['refunds']['id']."'
                                ,'".$pgto['refunds']['name']."'
                                ,'".$pgto['refunds']['type']."'
                                ,'".$pgto['refunds']['date_created']."'";
                        }else{
                            $sql = $sql . ",null,null,null,null,null,null,null,null,null";
                        }

                        $sql = $sql . "
                            ,'".$pgto['amount_refunded']."'
                            ,'".$pgto['last_modified_by_admin']."'
                            ,'".$pgto['collector']['id']."'
                            ,'".$pgto['collector']['first_name']."'
                            ,'".$pgto['collector']['last_name']."'
                            ,'".$pgto['collector']['phone']['area_code']."'
                            ,'".$pgto['collector']['phone']['number']."'
                            ,'".$pgto['collector']['phone']['extension']."'
                            ,'".$pgto['collector']['email']."'
                            ,'".$pgto['collector']['nickname']."');";
                    }
                    //echo "<br><br>SQL: " . $sql . "<br<br>";
                    include("/var/www/mp/bg_conexao_bd.php");
                    $query = mysqli_query($GLOBALS['con'],$sql); 
                
                    //#######################################################################
                    //                          Merchant orders
                    //#######################################################################
                    require_once ('sdk-mercadopago/lib/mercadopago.php');
                    //Chave Myde em produção
                    //$mp = new MP("APP_USR-7612629650074174-050422-b04a89099a51578583e8965efd6827f1__LB_LC__-249839521");

                    //Pega a chave do devido distribuidor no banco de dados
                    $sql = "SELECT id_distr from tb_pedido_cabecalho where pedido =". $pgto['order_id'] .";";
                    $query = mysqli_query($GLOBALS['con'],$sql); 
                    $id_distr = mysqli_fetch_row($query);

                    $sql = "SELECT access_token from tb_auth_mercadopago where dt_time = (SELECT max(dt_time) from tb_auth_mercadopago where user_id_myde = ".$id_distr[0].") and user_id_myde = ".$id_distr[0].";";

                    include("/var/www/mp/bg_conexao_bd.php");
                    $query = mysqli_query($GLOBALS['con'],$sql); 
                    $chave = mysqli_fetch_row($query);

                    echo "<br><br> SQL token: " . $sql . "<br><br>";
                    echo "<br><br> chave: " . $chave[0] . "<br><br>";

                    //ENVIA EMAIL INFORMANDO DE NOVO PEDIDO PARA CLIENTE E VENDEDOR
                    //TEM QUE SER FEITO AQUI PORQUE MERCADOPAGO NÃO TEM SESSION
                    $sql = "SELECT id_user, id_distr from tb_pedido_cabecalho where pedido = ".$pgto['order_id'].";";
                    $query = mysqli_query($GLOBALS['con'],$sql); 
                    $tbl_pedido = mysqli_fetch_row($query);

                    $sql_id_user = "SELECT id, email, nome from tb_usuarios where id='".$tbl_pedido[0]."';";
                    include("/var/www/mp/bg_conexao_bd.php");
                    $query_id_user = mysqli_query($GLOBALS['con'],$sql_id_user);
                    $id_user = mysqli_fetch_row($query_id_user);

                    $dados_user = array("id_user"=>$id_user[0],"email"=>$id_user[1],"nome"=>$id_user[2],"id_distr"=>$tbl_pedido[1]);
                    $dados_distrb = array("id_user"=>$id_user[0],"id_distr"=>$tbl_pedido[1]);

                    echo "<br><br>dados_distrb: <br><br>";
                    var_dump($dados_distrb);
                    echo "<br><br>";

                    include_once("/var/www/mp/bg_enviaemail.php");
                    $resposta = define_envia('pedido_efetuado',$dados_user);    
                    $resposta = define_envia('nova_venda',$dados_distrb);

                    //Chave do distribuidor Carlão
                    //$mp = new MP('APP_USR-7612629650074174-060219-5643e1b1eabb1ac556d6de3303cf0b45__LA_LB__-95945735');
                    
                    $mp = new MP($chave[0]);
                    
                    $url = "/merchant_orders/".$pgto['merchant_order_id'];
                    
                    echo "<br><br>Merchant Order: " . $pgto['merchant_order_id'] . "<br><br>";
                    echo "<br><br>URL: " . $url . "<br><br>";
                    $info = $mp->get($url);
                    var_dump($info);
                    $order = $info['response'];
                    
                    $sql_select = "SELECT id from tb_pagamento_orders where id=".$order['id'].";";
                    include("/var/www/mp/bg_conexao_bd.php");
                    $query = mysqli_query($GLOBALS['con'],$sql_select);

                    if(mysqli_num_rows($query)>0){
                        $sql = "
                            UPDATE tb_pagamento_orders SET
                                preference_id = '" . $order['preference_id'] . "'
                                ,date_created = '" . $order['date_created'] . "'
                                ,last_updated = '" . $order['last_updated'] . "'
                                ,application_id = '" . $order['application_id'] . "'
                                ,status = '" . $order['status'] . "'
                                ,site_id = '" . $order['site_id'] . "'
                                ,payer_id = '" . $order['payer']['id'] . "'
                                ,payer_email = '" . $order['payer']['email'] . "'
                                ,collector_id = '" . $order['collector']['id'] . "'
                                ,collector_email = '" . $order['collector']['email'] . "'
                                ,collector_nickname = '" . $order['collector']['nickname'] . "'
                                ,paid_amount = '" . $order['paid_amount'] . "'
                                ,refunded_amount = '" . $order['refunded_amount'] . "'
                                ,shipping_cost = '" . $order['shipping_cost'] . "'
                                ,cancelled = '" . $order['cancelled'] . "'
                                ,marketplace = '" . $order['marketplace'] . "'";

                        $sql = $sql . "
                            ,shipment_id = '" . $order['shipments']['0']['id'] . "'
                            ,shipment_type = '" . $order['shipments']['0']['shipment_type'] . "'
                            ,shipment_mode = '" . $order['shipments']['0']['shipping_mode'] . "'
                            ,shipment_picking_type = '" . $order['shipments']['0']['picking_type'] . "'
                            ,shipment_status = '" . $order['shipments']['0']['shipment_status'] . "'
                            ,shipment_substatus = '" . $order['shipments']['0']['shipment_substatus'] . "'
                            ,shipment_date_created = '" . $order['shipments']['0']['shipment_date_created'] . "'
                            ,shipment_last_modified = '" . $order['shipments']['0']['shipment_last_modified'] . "'
                            ,shipment_date_first_printed = '" . $order['shipments']['0']['shipment_date_first_printed'] . "'
                            ,shipment_service_id  = '" . $order['shipments']['0']['shipment_service_id'] . "'
                            ,shipment_sender_id  = '" . $order['shipments']['0']['shipment_sender_id'] . "'
                            ,shipment_receiver_id  = '" . $order['shipments']['0']['shipment_receiver_id'] . "'
                            ,receiver_address_line = '" . $order['shipments']['0']['receiver_address']['address_line'] . "'
                            ,receiver_zipcode = '" . $order['shipments']['0']['receiver_address']['zip_code'] . "'
                            ,receiver_street_name = '" . $order['shipments']['0']['receiver_address']['street_name'] . "'
                            ,receiver_street_number = '" . $order['shipments']['0']['receiver_address']['street_number'] . "'
                            ,receiver_floor = '" . $order['shipments']['0']['receiver_address']['floor'] . "'
                            ,receiver_apartment  = '" . $order['shipments']['0']['receiver_address']['apartment'] . "'
                            ,receiver_city  = '" . $order['shipments']['0']['receiver_address']['city']['name'] . "'
                            ,receiver_state  = '" . $order['shipments']['0']['receiver_address']['state']['name'] . "'
                            ,receiver_country  = '" . $order['shipments']['0']['receiver_address']['country']['name'] . "'
                            ,receiver_latitude  = '" . $order['shipments']['0']['receiver_address']['latitude'] . "'
                            ,receiver_longitude  = '" . $order['shipments']['0']['receiver_address']['longitude'] . "'
                            ,receiver_complemento  = '" . $order['shipments']['0']['receiver_address']['comment'] . "'
                            ,receiver_contato  = '" . $order['shipments']['0']['receiver_address']['contact'] . "'
                            ,receiver_phone  = '" . $order['shipments']['0']['receiver_address']['phone'] . "'
                            ,ship_opt_currency_id  = '" . $order['shipments']['0']['shipping_option']['currency_id'] . "'
                            ,ship_opt_cost  = '" . $order['shipments']['0']['shipping_option']['cost'] . "'
                            ,ship_opt_id  = '" . $order['shipments']['0']['shipping_option']['id'] . "'
                            ,ship_opt_name  = '" . $order['shipments']['0']['shipping_option']['name'] . "'
                            ,ship_opt_list_cost  = '" . $order['shipments']['0']['shipping_option']['list_cost'] . "'
                            ,ship_opt_method_id  = '" . $order['shipments']['0']['shipping_option']['shipping_method_id'] . "'
                            ,ship_opt_speed  = '" . $order['shipments']['0']['shipping_option']['speed']['shipping'] . "'
                            ,ship_opt_handling  = '" . $order['shipments']['0']['shipping_option']['speed']['handling'] . "'
                            ,estimated_delivery_date  = '" . $order['shipments']['0']['shipping_option']['estimated_delivery']['date'] . "'
                            ,estimated_delivery_time_from  = '" . $order['shipments']['0']['shipping_option']['estimated_delivery']['time_from'] . "'
                            ,estimated_delivery_time_to  = '" . $order['shipments']['0']['shipping_option']['estimated_delivery']['time_to'] . "'"; 

                        $sql = $sql . "
                            ,external_reference = '".$order['external_reference']."'
                            ,additional_info = '".$order['additional_info']."'
                            ,notification_url = '".$order['notification_url']."'
                            ,total_amount = '".$order['total_amount']."' 
                            WHERE ID = '".$order['id']."';";
                            //,additional_info = '".$order['additional_info']."'
                    }else{
                        $sql = 
                            "INSERT INTO tb_pagamento_orders (
                                id
                                ,preference_id
                                ,date_created
                                ,last_updated
                                ,application_id
                                ,status
                                ,site_id
                                ,payer_id
                                ,payer_email
                                ,collector_id
                                ,collector_email
                                ,collector_nickname
                                ,paid_amount
                                ,refunded_amount
                                ,shipping_cost
                                ,cancelled
                                ,marketplace
                                ,shipment_id 
                                ,shipment_type 
                                ,shipment_mode
                                ,shipment_picking_type
                                ,shipment_status
                                ,shipment_substatus
                                ,shipment_date_created
                                ,shipment_last_modified
                                ,shipment_date_first_printed
                                ,shipment_service_id 
                                ,shipment_sender_id 
                                ,shipment_receiver_id 
                                ,receiver_address_line
                                ,receiver_zipcode
                                ,receiver_street_name
                                ,receiver_street_number
                                ,receiver_floor 
                                ,receiver_apartment
                                ,receiver_city
                                ,receiver_state
                                ,receiver_country
                                ,receiver_latitude 
                                ,receiver_longitude 
                                ,receiver_complemento
                                ,receiver_contato
                                ,receiver_phone
                                ,ship_opt_currency_id
                                ,ship_opt_cost
                                ,ship_opt_id 
                                ,ship_opt_name 
                                ,ship_opt_list_cost
                                ,ship_opt_method_id
                                ,ship_opt_speed
                                ,ship_opt_handling
                                ,estimated_delivery_date
                                ,estimated_delivery_time_from
                                ,estimated_delivery_time_to
                                ,external_reference
                                ,additional_info
                                ,notification_url
                                ,total_amount

                            )

                            VALUES (
                            '" . $order['id'] . "'
                            ,'" . $order['preference_id'] . "'
                            ,'" . $order['date_created'] . "'
                            ,'" . $order['last_updated'] . "'
                            ,'" . $order['application_id'] . "'
                            ,'" . $order['status'] . "'
                            ,'" . $order['site_id'] . "'
                            ,'" . $order['payer']['id'] . "'
                            ,'" . $order['payer']['email'] . "'
                            ,'" . $order['collector']['id'] . "'
                            ,'" . $order['collector']['email'] . "'
                            ,'" . $order['collector']['nickname'] . "'
                            ,'" . $order['paid_amount'] . "'
                            ,'" . $order['refunded_amount'] . "'
                            ,'" . $order['shipping_cost'] . "'
                            ,'" . $order['cancelled'] . "'
                            ,'" . $order['marketplace'] . "'";

                        if(isset($order['shipments']['0']['id'])){
                            $sql = $sql . "
                                ,'" . $order['shipments']['0']['id'] . "'
                                ,'" . $order['shipments']['0']['shipment_type'] . "'
                                ,'" . $order['shipments']['0']['shipping_mode'] . "'
                                ,'" . $order['shipments']['0']['picking_type'] . "'
                                ,'" . $order['shipments']['0']['shipment_status'] . "'
                                ,'" . $order['shipments']['0']['shipment_substatus'] . "'
                                ,'" . $order['shipments']['0']['shipment_date_created'] . "'
                                ,'" . $order['shipments']['0']['shipment_last_modified'] . "'
                                ,'" . $order['shipments']['0']['shipment_date_first_printed'] . "'
                                ,'" . $order['shipments']['0']['shipment_service_id'] . "'
                                ,'" . $order['shipments']['0']['shipment_sender_id'] . "'
                                ,'" . $order['shipments']['0']['shipment_receiver_id'] . "'
                                ,'" . $order['shipments']['0']['receiver_address']['address_line'] . "'
                                ,'" . $order['shipments']['0']['receiver_address']['zip_code'] . "'
                                ,'" . $order['shipments']['0']['receiver_address']['street_name'] . "'
                                ,'" . $order['shipments']['0']['receiver_address']['street_number'] . "'
                                ,'" . $order['shipments']['0']['receiver_address']['floor'] . "'
                                ,'" . $order['shipments']['0']['receiver_address']['apartment'] . "'
                                ,'" . $order['shipments']['0']['receiver_address']['city']['name'] . "'
                                ,'" . $order['shipments']['0']['receiver_address']['state']['name'] . "'
                                ,'" . $order['shipments']['0']['receiver_address']['country']['name'] . "'
                                ,'" . $order['shipments']['0']['receiver_address']['latitude'] . "'
                                ,'" . $order['shipments']['0']['receiver_address']['longitude'] . "'
                                ,'" . $order['shipments']['0']['receiver_address']['comment'] . "'
                                ,'" . $order['shipments']['0']['receiver_address']['contact'] . "'
                                ,'" . $order['shipments']['0']['receiver_address']['phone'] . "'
                                ,'" . $order['shipments']['0']['shipping_option']['currency_id'] . "'
                                ,'" . $order['shipments']['0']['shipping_option']['cost'] . "'
                                ,'" . $order['shipments']['0']['shipping_option']['id'] . "'
                                ,'" . $order['shipments']['0']['shipping_option']['name'] . "'
                                ,'" . $order['shipments']['0']['shipping_option']['list_cost'] . "'
                                ,'" . $order['shipments']['0']['shipping_option']['shipping_method_id'] . "'
                                ,'" . $order['shipments']['0']['shipping_option']['speed']['shipping'] . "'
                                ,'" . $order['shipments']['0']['shipping_option']['speed']['handling'] . "'
                                ,'" . $order['shipments']['0']['shipping_option']['estimated_delivery']['date'] . "'
                                ,'" . $order['shipments']['0']['shipping_option']['estimated_delivery']['time_from'] . "'
                                ,'" . $order['shipments']['0']['shipping_option']['estimated_delivery']['time_to'] . "'";
                        }else{
                            $sql = $sql . ",'','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','',''";
                        }

                        $sql = $sql . "
                            ,'" . $order['external_reference'] . "'
                            ,'" . $order['additional_info'] . "'
                            ,'" . $order['notification_url'] . "'
                            ,'" . $order['total_amount'] . "');";
                        
                        
                    }
                    echo "<br><br>SQL Orders: " . $sql . "<br<br>";
                    include("/var/www/mp/bg_conexao_bd.php");
                    $query = mysqli_query($GLOBALS['con'],$sql);

                    break;
                case 'plan':
                    //$url = "/v1/plans/".[ID]."?access_token=".'[ACCESS_TOKEN]';
                    $url = "/v1/plans/".$row[2];
                    break;
                case 'subscription':
                    //$url = "/v1/subscriptions/".[ID]."?access_token=".'[ACCESS_TOKEN]';
                    $url = "/v1/subscriptions/".$row[2];
                    break;
                case 'invoice':
                    //$url = "/v1/invoices/".[ID]."?access_token=".'[ACCESS_TOKEN]';
                    $url = "/v1/invoices/";
                    break;
                default:
                    echo "Essa é uma notificação do tipo: ". $row[1] . "<br><br>Não consegui ver os detalhes da notificação.<br><br> Favor analisar esse caso. <br><br>";
            }
        }
    }else{
        echo "Solicitação incorreta.";
    }

?>
