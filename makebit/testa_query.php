<?php
//ini_set('display_errors','on');
//error_reporting(E_ALL);

header('Content-type: text/html; charset=utf-8');
//date_default_timezone_set('America/Sao_Paulo');


$GLOBALS['funcao'] = "testa";
include("/var/www/makebit/bg_funcoes_genericas.php");


function update_foxbit_orderbook(){
    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => "https://api.blinktrade.com/api/v1/BRL/orderbook",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 100,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_HTTPHEADER => array(
            "cache-control: no-cache",
        ),
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    if($err){
        echo "cURL Error #:" . $err;
    }else{

        $response = json_decode($response);

        if ($response != null) {
            //var_dump($response -> bids);
            //echo "<br><br><br>";
            //var_dump($response -> asks);

            //BID = COMPRA
            $bid_price = [];
            $bid_fx_price = [];
            $bid_amount = [];
            $bid_usr_ID = [];
            $bid_curva_valor = [];
            $bid_curva_volume = [];
            $bid_curva_qtde = [];
            $bid_curva_prc_maximo = [];
            $bid_curva_prc_min = [];
            $bid_curva_prc_soma = [];
            $bid_curva_vol_maximo = [];
            $bid_curva_vol_min = [];

            //temporarios
            $bid_prc_temp = [];
            $bid_vol_temp = [];

            //ASK = VENDA
            $ask_price = [];
            $ask_fx_price = [];
            $ask_amount = [];
            $ask_usr_ID = [];
            $ask_curva_valor = [];
            $ask_curva_volume = [];
            $ask_curva_qtde = [];
            $ask_curva_prc_maximo = [];
            $ask_curva_prc_min = [];
            $ask_curva_prc_soma = [];
            $ask_curva_vol_maximo = [];
            $ask_curva_vol_min = [];

            //temporarios
            $ask_prc_temp = [];
            $ask_vol_temp = [];

            foreach($response -> bids as $bids){
                array_push($bid_price,$bids[0]);
                array_push($bid_fx_price,intval($bids[0]/500)*500);
                array_push($bid_amount,$bids[1]);
                array_push($bid_usr_ID,$bids[2]);
            }

            foreach($response -> bids as $asks){
                array_push($ask_price,$asks[0]);
                array_push($ask_fx_price,intval($asks[0]/500)*500);
                array_push($ask_amount,$asks[1]);
                array_push($ask_usr_ID,$asks[2]);
            }


            //Processa BIDS
            $elem_i = 0;
            foreach($bid_fx_price as $bid_fx){
                if($bid_fx != end($bid_curva_valor)){
                    array_push($bid_curva_valor, $bid_fx);
                    array_push($bid_curva_volume, $bid_amount[$elem_i]);
                    array_push($bid_curva_qtde, 1);
                    array_push($bid_curva_prc_maximo, $bid_price[$elem_i]);
                    array_push($bid_curva_prc_min, $bid_price[$elem_i]);
                    array_push($bid_curva_prc_soma, $bid_price[$elem_i]);
                    array_push($bid_curva_vol_maximo, $bid_amount[$elem_i]);
                    array_push($bid_curva_vol_min, $bid_amount[$elem_i]);
                }else{
                    $keys = array_keys($bid_curva_volume);
                    $ultimo_item = end($keys);
                    $bid_curva_volume[$ultimo_item] = end($bid_curva_volume) +  $bid_amount[$elem_i];
                    $bid_curva_qtde[$ultimo_item] = end($bid_curva_qtde) +  1;
                    $bid_curva_prc_min[$ultimo_item] = $bid_price[$elem_i];
                    $bid_curva_prc_soma[$ultimo_item] = end($bid_curva_prc_soma) + $bid_price[$elem_i];
                    if($bid_amount[$elem_i] > end($bid_curva_vol_maximo)){
                        $bid_curva_vol_maximo[$ultimo_item] = $bid_amount[$elem_i];
                    }
                    if($bid_amount[$elem_i] < end($bid_curva_vol_min)){
                        $bid_curva_vol_min[$ultimo_item] = $bid_amount[$elem_i];
                    }
                }
                $elem_i = $elem_i + 1;
            }

            //echo "Processou BIDS";

            //Processa ASKS
            $elem_i = 0;
            foreach($ask_fx_price as $ask_fx){
                if($ask_fx != end($ask_curva_valor)){
                    array_push($ask_curva_valor, $ask_fx);
                    array_push($ask_curva_volume, $ask_amount[$elem_i]);
                    array_push($ask_curva_qtde, 1);
                    array_push($ask_curva_prc_maximo, $ask_price[$elem_i]);
                    array_push($ask_curva_prc_min, $ask_price[$elem_i]);
                    array_push($ask_curva_prc_soma, $ask_price[$elem_i]);
                    array_push($ask_curva_vol_maximo, $ask_amount[$elem_i]);
                    array_push($ask_curva_vol_min, $ask_amount[$elem_i]);
                }else{
                    $keys = array_keys($ask_curva_volume);
                    $ultimo_item = end($keys);
                    $ask_curva_volume[$ultimo_item] = end($ask_curva_volume) +  $ask_amount[$elem_i];
                    $ask_curva_qtde[$ultimo_item] = end($ask_curva_qtde) +  1;
                    $ask_curva_prc_min[$ultimo_item] = $ask_price[$elem_i];
                    $ask_curva_prc_soma[$ultimo_item] = end($ask_curva_prc_soma) + $ask_price[$elem_i];
                    if($ask_amount[$elem_i] > end($ask_curva_vol_maximo)){
                        $ask_curva_vol_maximo[$ultimo_item] = $ask_amount[$elem_i];
                    }
                    if($ask_amount[$elem_i] < end($ask_curva_vol_min)){
                        $ask_curva_vol_min[$ultimo_item] = $ask_amount[$elem_i];
                    }
                }
                $elem_i = $elem_i + 1;
            }

            /*
            echo "<h1>Resultados para salvar no BD: </h1><br><br>";

            echo "<h2>[BIDS]</h2><br>";
            echo "Curva de valores: <br>";
            var_dump($bid_curva_valor);
            echo "<br><br>";

            echo "Curva de volume: <br>";
            var_dump($bid_curva_volume);
            echo "<br><br>";

            echo "Curva de quantidade: <br>";
            var_dump($bid_curva_qtde);
            echo "<br><br>";

            echo "Curva de prc maximo: <br>";
            var_dump($bid_curva_prc_maximo);
            echo "<br><br>";

            echo "Curva de prc minimo: <br>";
            var_dump($bid_curva_prc_min);
            echo "<br><br>";

            echo "Curva de prc soma: <br>";
            var_dump($bid_curva_prc_soma);
            echo "<br><br>";

            echo "Curva de vol maximo: <br>";
            var_dump($bid_curva_vol_maximo);
            echo "<br><br>";

            echo "Curva de vol minimo: <br>";
            var_dump($bid_curva_vol_min);
            echo "<br><br>";




            echo "<h2>[ASKS]</h2><br>";
            echo "Curva de valores: <br>";
            var_dump($ask_curva_valor);
            echo "<br><br>";

            echo "Curva de volume: <br>";
            var_dump($ask_curva_volume);
            echo "<br><br>";

            echo "Curva de quantidade: <br>";
            var_dump($ask_curva_qtde);
            echo "<br><br>";

            echo "Curva de prc maximo: <br>";
            var_dump($ask_curva_prc_maximo);
            echo "<br><br>";

            echo "Curva de prc minimo: <br>";
            var_dump($ask_curva_prc_min);
            echo "<br><br>";

            echo "Curva de prc soma: <br>";
            var_dump($ask_curva_prc_soma);
            echo "<br><br>";

            echo "Curva de vol maximo: <br>";
            var_dump($ask_curva_vol_maximo);
            echo "<br><br>";

            echo "Curva de vol minimo: <br>";
            var_dump($ask_curva_vol_min);
            echo "<br><br>";
            */

            $data_cotacao = date('Y/m/d H:i', strtotime('-1 minutes'));

            for ($i_bid = 0; $i_bid<sizeof($bid_curva_valor); $i_bid++){
                include("/var/www/makebit/bg_conexao_bd.php");
                $sql = "insert into foxbit_orderbook (
                            data_cotacao
                            ,moeda
                            ,tipo
                            ,fx_valor
                            ,qtde_cotacoes
                            ,preco_media
                            ,preco_soma
                            ,preco_maximo
                            ,preco_minimo
                            ,volume_soma
                            ,volume_maximo
                            ,volume_minimo
                        ) VALUES('".$data_cotacao."',
                                'BTC'
                                ,'Compra'
                                ,".$bid_curva_valor[$i_bid].
                                ",".$bid_curva_qtde[$i_bid].
                                ",".$bid_curva_prc_soma[$i_bid]/$bid_curva_qtde[$i_bid].
                                ",".$bid_curva_prc_soma[$i_bid].
                                ",".$bid_curva_prc_maximo[$i_bid].
                                ",".$bid_curva_prc_min[$i_bid].
                                ",".$bid_curva_volume[$i_bid].
                                ",".$bid_curva_vol_maximo[$i_bid].
                                ",".$bid_curva_vol_min[$i_bid].
                                ");";
                //echo "SQL: " . $sql . "<br>";
                $consulta = mysqli_query($GLOBALS['con'],$sql);

            }

            for ($i_ask = 0; $i_ask<sizeof($ask_curva_valor); $i_ask++){
                include("/var/www/makebit/bg_conexao_bd.php");
                $sql = "insert into foxbit_orderbook (
                            data_cotacao
                            ,moeda
                            ,tipo
                            ,fx_valor
                            ,qtde_cotacoes
                            ,preco_media
                            ,preco_soma
                            ,preco_maximo
                            ,preco_minimo
                            ,volume_soma
                            ,volume_maximo
                            ,volume_minimo
                        ) VALUES('".$data_cotacao."',
                                'BTC'
                                ,'Venda'
                                ,".$ask_curva_valor[$i_ask].
                                ",".$ask_curva_qtde[$i_ask].
                                ",".$ask_curva_prc_soma[$i_ask]/$ask_curva_qtde[$i_ask].
                                ",".$ask_curva_prc_soma[$i_ask].
                                ",".$ask_curva_prc_maximo[$i_ask].
                                ",".$ask_curva_prc_min[$i_ask].
                                ",".$ask_curva_volume[$i_ask].
                                ",".$ask_curva_vol_maximo[$i_ask].
                                ",".$ask_curva_vol_min[$i_ask].
                                ");";

                //echo "SQL: ".$sql . "<br><br>";
                $consulta = mysqli_query($GLOBALS['con'],$sql);
            }
            echo "Dados incluídos com sucesso as " . date('Y/m/d H:i') . "<br>";
        }else{
            echo "Não li o objeto, vou fazer um refresh " . date('Y/m/d H:i') . "<br>";

            $url1=$_SERVER['REQUEST_URI'];
            header("Refresh: 3; URL=$url1");
        }
    }
}

update_foxbit_orderbook();

?>
