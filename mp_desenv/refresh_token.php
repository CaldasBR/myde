<?php
function atualizar_token($token_myde, $refresh_token){
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

    $response = $mp->post($request);
    print_r($response['response']);
    }
    atualizar_token("TEST-7612629650074174-050422-4ed6c8a6ac614e236e7dadd74ce08da7__LB_LC__-249839521", "TG-590bbab1e4b0539f2ae0b7d8-249839521");
?>