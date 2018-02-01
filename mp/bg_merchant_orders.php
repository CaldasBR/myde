<?php
    $id = $_GET["id"];
    require_once ('sdk-mercadopago/lib/mercadopago.php');
    
    //chave em producao
    $mp = new MP("APP_USR-7612629650074174-050422-b04a89099a51578583e8965efd6827f1__LB_LC__-249839521");
    
    $url = "/merchant_orders/".$id;
    $info = $mp->get($url);

    echo "Dados completos: <br>";
    print_r($info);
    echo "<br><br><br>";

    echo "Dados do pagamento: <br>";
    print_r($info["response"]["payments"]);
    echo "<br><br><br>";

    echo "Dados de envio: <br>";
    print_r($info["response"]["shipments"]);
    
?>