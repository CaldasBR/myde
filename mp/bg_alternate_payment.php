<?php
//############ Ver métodos de pagamento disponíveis ############
/*
    require_once ('sdk-mercadopago/lib/mercadopago.php');
    $mp = new MP ("TEST-7612629650074174-050422-4ed6c8a6ac614e236e7dadd74ce08da7__LB_LC__-249839521");
    $payment_methods = $mp->get ("/v1/payment_methods");
    print_r ($payment_methods);
*/

    require_once ('sdk-mercadopago/lib/mercadopago.php');
    //chave em producao
    //$mp = new MP("APP_USR-7612629650074174-050422-b04a89099a51578583e8965efd6827f1__LB_LC__-249839521");

    //chave de teste
    $mp = new MP("TEST-7612629650074174-050422-4ed6c8a6ac614e236e7dadd74ce08da7__LB_LC__-249839521");

    $payment_data = array(
           "transaction_amount" => 10,
           "description" => "Produto A (exemplo de pgto)",
           "payment_method_id" => "bolbradesco",
           "payer" => array (
                           "email" => "filipe_caldas@msn.com"
           )
    );

    $payment = $mp->post("/v1/payments", $payment_data);
    //print_r ($payment["response"]);
    $resposta = $payment["response"];
    //print_r($resposta);

    
    echo "ID do pgto=" . $resposta['id'] . "<br>";
    echo "date_created do pgto=" . $resposta['date_created'] . "<br>";
    echo "date_approved do pgto=" . $resposta['date_approved'] . "<br>";
    echo "date_last_updated do pgto=" . $resposta['date_last_updated'] . "<br>";
    echo "money_release_date do pgto=" . $resposta['money_release_date'] . "<br>";
    echo "operation_type do pgto=" . $resposta['operation_type'] . "<br>";
    echo "issuer_id do pgto=" . $resposta['issuer_id'] . "<br>";
    echo "payment_method_id do pgto=" . $resposta['payment_method_id'] . "<br>";
    echo "payment_type_id do pgto=" . $resposta['payment_type_id'] . "<br>";
    echo "status do pgto=" . $resposta['status'] . "<br>";
    echo "status_detail do pgto=" . $resposta['status_detail'] . "<br>";
    echo "currency_id do pgto=" . $resposta['currency_id'] . "<br>";
    echo "description do pgto=" . $resposta['description'] . "<br>";
    echo "live_mode do pgto=" . $resposta['live_mode'] . "<br>";
    echo "sponsor_id do pgto=" . $resposta['sponsor_id'] . "<br>";
    echo "authorization_code do pgto=" . $resposta['authorization_code'] . "<br>";
    echo "collector_id do pgto=" . $resposta['collector_id'] . "<br>";
    echo "payer_type do pgto=" . $resposta['payer']['type'] . "<br>";
    echo "payer_id do pgto=" . $resposta['payer']['id'] . "<br>";
    echo "payer_email do pgto=" . $resposta['payer']['email'] . "<br>";
    echo "payer_cpf do pgto=" . $resposta['payer']['identification']['number'] . "<br>";
    echo "payer_phone_ddd do pgto=" . $resposta['payer']['phone']['area_code'] . "<br>";
    echo "payer_phone_cel do pgto=" . $resposta['payer']['phone']['number'] . "<br>";
    echo "payer_firstname do pgto=" . $resposta['payer']['first_name'] . "<br>";
    echo "payer_lastname do pgto=" . $resposta['payer']['last_name'] . "<br>";
    echo "external_reference do pgto=" . $resposta['external_reference'] . "<br>";
    echo "transaction_amount do pgto=" . $resposta['transaction_amount'] . "<br>";
    echo "transaction_amount_refunded do pgto=" . $resposta['transaction_amount_refunded'] . "<br>";
    echo "coupon_amount do pgto=" . $resposta['coupon_amount'] . "<br>";
    echo "net_received_amount do pgto=" . $resposta['transaction_details']['net_received_amount'] . "<br>";
    echo "total_paid_amount do pgto=" . $resposta['transaction_details']['total_paid_amount'] . "<br>";
    echo "overpaid_amount do pgto=" . $resposta['transaction_details']['overpaid_amount'] . "<br>";
    echo "external_resource_url do pgto=" . $resposta['transaction_details']['external_resource_url'] . "<br>";
    echo "installment_amount do pgto=" . $resposta['transaction_details']['installment_amount'] . "<br>";
    echo "financial_institution do pgto=" . $resposta['transaction_details']['financial_institution'] . "<br>";
    echo "payment_method_reference_id do pgto=" . $resposta['transaction_details']['payment_method_reference_id'] . "<br>";
    echo "call_for_authorize_id do pgto=" . $resposta['call_for_authorize_id'] . "<br>";
    echo "installments do pgto=" . $resposta['installments'] . "<br>";

    
    $id = $resposta['id'];
    $date_created = $resposta['date_created'];
    $date_approved = $resposta['date_approved'];
    $date_last_updated = $resposta['date_last_updated'];
    $money_release_date = $resposta['money_release_date'];
    $operation_type = $resposta['operation_type'];
    $issuer_id = $resposta['issuer_id'];
    $payment_method_id = $resposta['payment_method_id'];
    $payment_type_id = $resposta['payment_type_id'];
    $status = $resposta['status'];
    $status_detail = $resposta['status_detail'];
    $currency_id = $resposta['currency_id'];
    $live_mode = $resposta['live_mode'];
    $description = $resposta['description'];
    $sponsor_id = $resposta['sponsor_id'];
    $authorization_code = $resposta['authorization_code'];
    $collector_id = $resposta['collector_id'];
    $payer_type = $resposta['payer']['type'];
    $payer_id = $resposta['payer']['id'];
    $payer_email = $resposta['payer']['email'];
    $payer_cpf = $resposta['payer']['identification']['number'];
    $payer_phoneareacode = $resposta['payer']['phone']['area_code'];
    $payer_phonenumber = $resposta['payer']['phone']['number'];
    $payer_firstname = $resposta['payer']['first_name'];
    $payer_lastname = $resposta['payer']['last_name'];
    $external_reference = $resposta['external_reference'];
    $transaction_amount = $resposta['transaction_amount'];
    $transaction_amount_refunded = $resposta['transaction_amount_refunded'];
    $coupon_amount = $resposta['coupon_amount'];
    $net_received_amount = $resposta['transaction_details']['net_received_amount'];
    $total_paid_amount = $resposta['transaction_details']['total_paid_amount'];
    $overpaid_amount = $resposta['transaction_details']['overpaid_amount'];
    $external_resource_url = $resposta['transaction_details']['external_resource_url'];
    $installment_amount = $resposta['transaction_details']['installment_amount'];
    $financial_institution = $resposta['transaction_details']['financial_institution'];
    $payment_method_reference_id = $resposta['transaction_details']['payment_method_reference_id'];
    $call_for_authorize_id = $resposta['call_for_authorize_id'];
    $installments = $resposta['installments'];

    // ########## REDIRECIONAR PARA $external_resource_url ##########
    echo '<a href="'.$external_resource_url.'">Imprimir boleto</a>';

    echo "<script>window.open('$external_resource_url');</script>";
?>