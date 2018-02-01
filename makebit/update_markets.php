<?php
    //ini_set('display_errors','on');
    //error_reporting(E_ALL);
    header('Content-type: text/html; charset=utf-8');


    $erro=0;
    $msg="Nada executado";
    $opt="Nenhuma Opt";
    $GLOBALS['funcao']="update_markets";

    include("/var/www/makebit/bg_funcoes_genericas.php");

    //#############################################################################
    //################### RAKING DE MERCADO coinmarketcap #########################
    //#############################################################################
    function update_moedas_reais(){
        $response = file_get_contents('https://blockchain.info/pt/ticker');
        $response = json_decode($response);
        //var_dump($response);

        foreach ($response as $moeda => $valores_moeda) {
            foreach ($valores_moeda as $field => $content){
                switch($field){
                    case "15m":
                        $price_last_15m = $content;
                    break;
                    case "last":
                        $price_last = $content;
                    break;
                    case "buy":
                        $price_buy = $content;
                    break;
                    case "sell":
                        $price_sell = $content;
                    break;
                    case "symbol":
                        $symbol = $content;
                    break;
                }
            }

            include("/var/www/makebit/bg_conexao_bd.php");
            $sql = "insert into moedas_reais (
                        moeda
                        ,price_last_15m
                        ,price_last
                        ,price_buy
                        ,price_sell
                        ,symbol
                    ) VALUES('".$moeda."',".
                            $price_last_15m.",".
                            $price_last.",".
                            $price_buy.",".
                            $price_sell.",'".
                            $symbol."');";
            $consulta = mysqli_query($GLOBALS['con'],$sql);
            //echo "SQL: {$sql}";
        }

        mysqli_close($GLOBALS['con']);

        $erro=0;
        $msg="Dados de Moedas Reais da Blockchain atualizados!";
        $opt="";

        $envia_resposta = array("erro"=>$erro,"msg"=>$msg,"funcao"=>$GLOBALS['funcao'],"opt"=>$opt,"now"=>date("Y/m/d").'-'.date("h:i:sa"));
        print json_encode($envia_resposta, JSON_PRETTY_PRINT);
        echo "<br>";
    }

    //#############################################################################
    //########################### BITHUMB ORDERBOOK ###############################
    //#############################################################################
    function update_BITHUMB_ORDERBOOK(){
        $moedas = ['BTC', 'ETH', 'DASH', 'LTC', 'ETC', 'XRP', 'BCH', 'XMR', 'ZEC', 'QTUM', 'BTG', 'EOS'];
        foreach ($moedas as $coin){
            $moedas_reais = file_get_contents('https://blockchain.info/pt/ticker');
            $moedas_reais = json_decode($moedas_reais);
            //var_dump($moedas_reais);
            //echo 'Cotações monetárias reais: <br>';
            $coreia = $moedas_reais -> {'KRW'} -> {'last'};
            $real = $moedas_reais -> {'BRL'} -> {'last'};
            //echo 'KRW: ' . $coreia . '<br>';
            //echo 'BRL: ' . $real . '<br>';
            $fator_real = $real / $coreia;
            //$fator_real = 0.003045;
            //echo "Fator BRL/KRW: " . $fator_real . '<br><br>';

            $response = file_get_contents('https://api.bithumb.com' . '/public/orderbook/'. $coin . '?count=50&group_orders=1');
            $response = json_decode($response);
            //var_dump($response);

            if ($response != null) {
                //$dt = Date_timestamp_set(new DateTime(), );
                $dt = new DateTime(strtotime($response -> data -> timestamp), new DateTimeZone('Asia/Seoul'));
                $dt->setTimezone(new DateTimeZone('America/Sao_Paulo'));
                echo 'Horario BR da cotação: '. date_format($dt,"Y/m/d H:i") . '<br>';
                //echo 'Moeda: <strong>' . $response -> data -> order_currency . '</strong><br>';

                //BID = COMPRA
                $bid_price = [];
                $bid_fx_price = [];
                $bid_amount = [];
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

                //Processa BIDS
                $bids = $response -> data -> bids;
                foreach ($bids as $bid_ord){
                    array_push($bid_price,$fator_real *  $bid_ord -> price);
                    array_push($bid_fx_price,intval(($fator_real *  $bid_ord -> price)/20)*20);
                    array_push($bid_amount,$bid_ord -> quantity);
                }
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

                for ($i_bid = 0; $i_bid<sizeof($bid_curva_valor); $i_bid++){
                    include("/var/www/makebit/bg_conexao_bd.php");
                    $sql = "insert into bithumb_orderbook (
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
                                ,fator_brl
                            ) VALUES('".date_format($dt,"Y/m/d H:i")."',
                                    '".$response -> data -> order_currency."'
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
                                    ",".$fator_real.
                                    ");";
                    //echo "SQL: " . $sql . "<br>";
                    $consulta = mysqli_query($GLOBALS['con'],$sql);
                    mysqli_close($GLOBALS['con']);
                }
                //echo "Processou BIDS";

                //Processa askS
                $asks = $response -> data -> asks;
                foreach ($asks as $ask_ord){
                    array_push($ask_price,$fator_real *  $ask_ord -> price);
                    array_push($ask_fx_price,intval(($fator_real *  $ask_ord -> price)/20)*20);
                    array_push($ask_amount,$ask_ord -> quantity);
                }
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

                for ($i_ask = 0; $i_ask<sizeof($ask_curva_valor); $i_ask++){
                    include("/var/www/makebit/bg_conexao_bd.php");
                    $sql = "insert into bithumb_orderbook (
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
                                ,fator_brl
                            ) VALUES('".date_format($dt,"Y/m/d H:i")."',
                                    '".$response -> data -> order_currency."'
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
                                    ",".$fator_real.
                                    ");";
                    //echo "SQL: " . $sql . "<br>";
                    $consulta = mysqli_query($GLOBALS['con'],$sql);
                    mysqli_close($GLOBALS['con']);
                }
                //echo "Processou askS";
            }
        }
    }

    //#############################################################################
    //########################### BITHUMB TRANSACTIONS ############################
    //#############################################################################
    function update_BITHUMB_TRANSACTIONS(){
        $arr_moedas = ['BTC','ETH','DASH','LTC','ETC','XRP','BCH','XMR','ZEC','QTUM','BTG'];
        $moedas_reais = file_get_contents('https://blockchain.info/pt/ticker');
        $moedas_reais = json_decode($moedas_reais);
        //var_dump($moedas_reais);
        //echo 'Cotações monetárias reais: <br>';
        $coreia = $moedas_reais -> {'KRW'} -> {'last'};
        $real = $moedas_reais -> {'BRL'} -> {'last'};
        //echo 'KRW: ' . $coreia . '<br>';
        //echo 'BRL: ' . $real . '<br>';
        $fator_real = $real / $coreia;

        foreach ($arr_moedas as $moeda) {
            $response = file_get_contents('https://api.bithumb.com/public/recent_transactions/'.$moeda.'?count=100');
            $response = json_decode($response);
            //var_dump($response);
            $data = $response->data;
            //var_dump($data);
            //echo "Moeda: ".$moeda."<br>";
            unset($transaction_date);
            unset($price);
            unset($units_traded);
            foreach ($data as $key => $value) {
                foreach ($value as $keyV => $valueV) {
                    if(!isset($$keyV)){
                        $$keyV= [];
                    }
                    //echo "chave: ".$keyV."<br>";
                    //echo "valor: ".$valueV."<br>";
                    //$$keyV = $valueV;
                    if($keyV=="price"){
                        array_push($$keyV,$valueV*$fator_real);
                    }else{
                        array_push($$keyV,$valueV);
                    }

                }
            }

            $price = limpa_data_array($transaction_date, $price, "1 minutes");
            $units_traded = limpa_data_array($transaction_date, $units_traded, "1 minutes");
            $minimo = min_arr($price);
            $maximo = max_arr($price);
            $soma = sum_arr($price);
            $contagem = count_arr($price);
            $qtde_traded = sum_arr($units_traded);
            $med = med_arr($price);
            $abert = last_arr($price);
            $fech = first_arr($price);

            $dt = new DateTime($transaction_date[0], new DateTimeZone('Asia/Seoul'));
            // change the timezone of the object without changing it's time
            $dt->setTimezone(new DateTimeZone('America/Sao_Paulo'));

            /*echo "Horario BR da cotação: ".date_format($dt,"Y/m/d H:i")."<br>";
            echo "Cotações da moeda {$moeda}: <br>";
            echo "Mínimo:".$minimo."<br>";
            echo "Máximo:".$maximo."<br>";
            echo "Soma:".$soma."<br>";
            echo "Contagem:".$contagem."<br>";
            echo "Média:".$med."<br>";
            echo "Abertura:".$abert."<br>";
            echo "Fechamento:".$fech."<br>";


            echo "<br><br><br>";*/
            include("/var/www/makebit/bg_conexao_bd.php");
            $sql = "insert into bithumb_transactions (
                        dt_cotacao
                        ,moeda
                        ,prc_minimo
                        ,prc_maximo
                        ,prc_soma
                        ,qtde_ordens
                        ,qtde_traded
                        ,prc_media
                        ,prc_abertura
                        ,prc_fechamento
                        ,tag1
                    ) VALUES('".date_format($dt,"Y/m/d H:i")."','"
                            .$moeda.
                            "',".$minimo.
                            ",".$maximo.
                            ",".$soma.
                            ",".$contagem.
                            ",".$qtde_traded.
                            ",".$med.
                            ",".$abert.
                            ",".$fech.
                            ",'Tendencia');";
            $consulta = mysqli_query($GLOBALS['con'],$sql);
            //echo "SQL: ".$sql."<br>";

            //##### Marca fundos e Topos
            $sql = "select dt_cotacao, prc_media from bithumb_transactions where dt_cotacao >= '" . date("Y/m/d H:i",strtotime('-5 minutes')) . "' and moeda='".$moeda."' group by dt_cotacao;";
            $consulta = mysqli_query($GLOBALS['con'],$sql);
            //echo "SQL: " . $sql . "<br>";
            if(mysqli_num_rows($consulta)>0){
                $cotacao_prc = [];
                $cotacao_dt = [];
                for($i=0;$i<mysqli_num_rows($consulta);$i++){
                    $row = mysqli_fetch_row($consulta);
                    array_push($cotacao_prc,$row[1]);
                    array_push($cotacao_dt,$row[0]);
                }
                //echo "cotacao_prc: <br>";
                //print_r($cotacao_prc);

                //echo "<br>cotacao_dt: <br>";
                //print_r($cotacao_dt);

                //marca TOPOS e FUNDOS
                for($j=1;$j<sizeof($cotacao_prc)-1;$j++){
                    if($cotacao_prc[$j] < $cotacao_prc[$j-1] and $cotacao_prc[$j] < $cotacao_prc[$j+1]){
                        $sql = "UPDATE bithumb_transactions SET tag1 = 'Fundo' WHERE dt_cotacao='".$cotacao_dt[$j]."' and moeda='".$moeda."';";
                        $consulta = mysqli_query($GLOBALS['con'],$sql);
                        //echo "SQL FUNDO: " . $sql . "<br>";
                    }
                    if($cotacao_prc[$j] > $cotacao_prc[$j-1] and $cotacao_prc[$j] > $cotacao_prc[$j+1]){
                        $sql = "UPDATE bithumb_transactions SET tag1 = 'Topo' WHERE dt_cotacao='".$cotacao_dt[$j]."' and moeda='".$moeda."';";
                        $consulta = mysqli_query($GLOBALS['con'],$sql);
                        //echo "SQL TOPO: " . $sql . "<br>";
                    }
                }
            }
            mysqli_close($GLOBALS['con']);
        }
        $erro=0;
        $msg="Dados de RECENT do BITHUMB atualizados!";
        $opt="";

        $envia_resposta = array("erro"=>$erro,"msg"=>$msg,"funcao"=>$GLOBALS['funcao'],"opt"=>$opt,"now"=>date("Y/m/d").'-'.date("h:i:sa"));
        print json_encode($envia_resposta, JSON_PRETTY_PRINT);
        echo "<br>";
    }

    //#############################################################################
    //########################### FOXBIT TRANSACTIONS ###############################
    //#############################################################################
    function update_foxbit_transactions(){
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.blinktrade.com/api/v1/BRL/trades?since=" . strtotime('-1 minutes') . "&limit=80",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
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
            $response = str_replace(array("[","]"),"",$response);
            $response = str_replace(array("}, {"),"}###{",$response);
            $response2 = explode("###",$response);

            $btid = [];
            $bdate = [];
            $bprice = [];
            $bamount = [];

            $stid = [];
            $sdate = [];
            $sprice = [];
            $samount = [];

            $tid = [];
            $date = [];
            $price = [];
            $amount = [];

            //var_dump($response2);

            foreach($response2 as $elemento){
                $nElemento = json_decode($elemento);
                /*if($nElemento -> side == 'buy'){
                    array_push($btid,$nElemento -> tid);
                    array_push($bdate, date_format(date_timestamp_set(new DateTime(), $nElemento -> date), 'c'));
                    array_push($bprice,$nElemento -> price);
                    array_push($bamount, $nElemento -> amount);
                }else{
                    array_push($stid,$nElemento -> tid);
                    array_push($sdate, date_format(date_timestamp_set(new DateTime(), $nElemento -> date), 'c'));
                    array_push($sprice,$nElemento -> price);
                    array_push($samount, $nElemento -> amount);
                }*/
                array_push($tid,$nElemento -> tid);
                array_push($date, date_format(date_timestamp_set(new DateTime(), $nElemento -> date), 'c'));
                array_push($price,$nElemento -> price);
                array_push($amount, $nElemento -> amount);
            }

            /*
                echo "teste data 1:" . strtotime(date("Y-m-d H:i:s")) . "<br><br>";
                echo "teste data 2:" . strtotime('-2 minutes') . "<br><br>";
            */

            $price = limpa_data_array($date, $price, "1 minutes");
            $units_traded = limpa_data_array($date, $amount, "1 minutes");
            $minimo = min_arr($price);
            $maximo = max_arr($price);
            $soma = sum_arr($price);
            $contagem = count_arr($price);
            $qtde_traded = sum_arr($units_traded);
            $med = med_arr($price);
            $abert = last_arr($price);
            $fech = first_arr($price);

            //$dt = new DateTime($date[0], new DateTimeZone('Asia/Seoul'));
            // change the timezone of the object without changing it's time
            //$dt->setTimezone(new DateTimeZone('America/Sao_Paulo'));

            //echo "Dump1 <br><br>";

            /*echo "<br><br> Transactions ID: <br>";
            var_dump($tid);

            echo "<br><br> Data cotações: <br>";
            var_dump($date);

            echo "<br><br> Valor unitário: <br>";
            var_dump($price);

            echo "<br><br> Quantidade: <br>";
            var_dump($units_traded);

            echo "<br><br>";
            echo "Mínimo:".$minimo."<br>";
            echo "Máximo:".$maximo."<br>";
            echo "Soma:".$soma."<br>";
            echo "Contagem:".$contagem."<br>";
            echo "Quantidade negociada:".$qtde_traded."<br>";
            echo "Valor negociado total aproximado: " . $qtde_traded * $med . "<br>";
            echo "Média: ".$med."<br>";
            echo "Abertura: ".$abert."<br>";
            echo "Fechamento: ".$fech."<br>";
            echo "Data da cotação: " . date_format(date_create($date[0]),"Y/m/d H:i");
            */

            include("/var/www/makebit/bg_conexao_bd.php");
            $sql = "insert into foxbit_transactions (
                        dt_cotacao
                        ,moeda
                        ,prc_minimo
                        ,prc_maximo
                        ,prc_soma
                        ,qtde_ordens
                        ,qtde_traded
                        ,prc_media
                        ,prc_total_tempo
                        ,prc_abertura
                        ,prc_fechamento
                    ) VALUES('".date_format(date_create($date[0]),"Y/m/d H:i")."',
                            'BTC'
                            ,".$minimo.
                            ",".$maximo.
                            ",".$soma.
                            ",".$contagem.
                            ",".$qtde_traded.
                            ",".$med.
                            ",".$qtde_traded * $med.
                            ",".$abert.
                            ",".$fech.
                            ");";

            $consulta = mysqli_query($GLOBALS['con'],$sql);
            mysqli_close($GLOBALS['con']);
            //echo "SQL: ".$sql."<br>";

            $erro=0;
            $msg="Dados de RECENT da FOXBIT atualizados!";
            $opt="";

            $envia_resposta = array("erro"=>$erro,"msg"=>$msg,"funcao"=>$GLOBALS['funcao'],"opt"=>$opt,"now"=>date("Y/m/d").'-'.date("h:i:sa"));
            print json_encode($envia_resposta, JSON_PRETTY_PRINT);
            echo "<br>";
        }
    }

    function limpa_data_array($vetor_hora, $vetor_valor,$tempo){
        $dt_inicio = date_create($vetor_hora[0]);
        //echo "dt_inicio original: ".$dt_inicio->format("Y/m/d H:i")."<br>";

        //date_sub($dt_inicio,date_interval_create_from_date_string($tempo));
        $dt_inicio_ajust = $dt_inicio->format("Y/m/d H:i");

        //echo "dt_inicio ajustada: ".$dt_inicio_ajust."<br>";

        $tamanho = count($vetor_hora);
        $removidos = [];

        for ($i = 0; $i < $tamanho; $i++){

            if($dt_inicio_ajust!=date("Y/m/d H:i",strtotime($vetor_hora[$i]))){
                unset($vetor_valor[$i]);
                //echo "removido! <br><br>";
            }else{
                //echo "mantido! <br><br>";
            }
        }

         return $vetor_valor;
    }

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
                    array_push($bid_fx_price,intval($bids[0]/200)*200);
                    array_push($bid_amount,$bids[1]);
                    array_push($bid_usr_ID,$bids[2]);
                }

                foreach($response -> asks as $asks){
                    array_push($ask_price,$asks[0]);
                    array_push($ask_fx_price,intval($asks[0]/200)*200);
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
                    mysqli_close($GLOBALS['con']);

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
                    mysqli_close($GLOBALS['con']);
                }
                echo "Dados incluídos com sucesso as " . date('Y/m/d H:i') . "<br>";
            }else{
                echo "Não li o objeto, vou fazer um refresh " . date('Y/m/d H:i') . "<br>";

                $url1=$_SERVER['REQUEST_URI'];
                header("Refresh: 3; URL=$url1");
            }
        }
    }

    include("/var/www/makebit/ccxt/ccxt.php");
    date_default_timezone_set('America/Sao_Paulo');

    //Ver a lista de exchanges que a CCXT atua
    //var_dump(ccxt\Exchange::$exchanges);

    /*
    #################################################################################
    ######################## Exibe informações das exchanges ########################
    #################################################################################
    */
    function fetch_exchanges($exchanges,$apiKey,$apiSecret){

        for($i=0;$i<sizeof($exchanges);$i++){
            //echo "Oi " . $i . "<br>";
            $exch = "\\ccxt\\" . $exchanges[$i];
            //echo "exch: " . $exch . "<br>";
            $params = array (
                'apiKey' => $apiKey[$i],
                'secret' => $apiSecret[$i],
            );

            $exchange = new $exch ($params);

            //echo "<h2><strong>".$exchange->name.'</strong><img src="'.$exchange->urls["logo"].'"></h2>';

            //Lista todas as funções da exchange
            //var_dump($exchange);
            //echo "<br>";

            /*
            echo "<strong>ID: </strong>" . $exchange->id . "<br>";
            echo "<strong>Countrie: </strong>" . $exchange->countries . "<br>";
            echo '<strong>Documentação: </strong><a href="' . $exchange->urls["doc"] . '">'.$exchange->urls["doc"].'</a><br>';
            echo "<strong>API public: </strong><br>";
            var_dump($exchange->api['public']);
            echo "<br>";
            echo "<strong>API private: </strong><br>";
            var_dump($exchange->api['private']);
            echo "<br>";
            echo "<strong>hasFetchTickers: </strong>" . $exchange->hasFetchTickers . "<br>";
            echo "<strong>hasFetchOHLCV: </strong>" . $exchange->hasFetchOHLCV . "<br>";
            echo "<strong>timeframes: </strong>" . $exchange->timeframes . "<br>";
            echo "<strong>Markets: </strong><br>";
            var_dump($exchange->markets);
            echo "<br>";
            echo "<strong>Symbols: </strong><br>";
            var_dump($exchange->symbols);
            echo "<br>";
            echo "<strong>Currencies: </strong><br>";
            var_dump($exchange->currencies);
            echo "<br>";
            echo "<strong>Markets_by_id: </strong><br>";
            var_dump($exchange->markets_by_id);
            echo "<br>";
            echo "<strong>Markets: </strong><br>";
            var_dump($exchange->markets);
            echo "<br><br>";

            */

            //### Exibe o saldo da exchange ###
            //fetchBalance($exchange);

            //echo "<h2><strong>".$exchange->name." -> load_markets()</h2></strong>";
            //var_dump ($bithumb->load_markets());
            $markets = $exchange->load_markets();

            foreach ($markets as $key => $value){
                //var_dump($value);
                //$number = 1234.56;
                // Notação Inglesa (padrão)
                //$english_format_number = number_format($number);
                // 1,234
                // Notação Francesa
                //$nombre_format_francais = number_format($number, 2, ',', ' ');
                // 1 234,56
                //$number = 1234.5678;
                // Notação Inglesa com separador de milhar
                //$english_format_number = number_format($number, 2, '.', '');
                // 1234.57

                //echo "<strong>Moeda: " . $key . "<br></strong>";
                //echo "id: " . $value["id"] . "<br>";
                //echo "symbol: " . $value["symbol"] . "<br>";
                //echo "base: " . $value["base"] . "<br>";
                //echo "quote: " . $value["quote"] . "<br>";
                /*if (array_key_exists("maker", $value)) {
                    echo "Taxa quando executora: " . $value['maker'] . "<br>";
                }
                if (array_key_exists("taker", $value)) {
                    echo "Taxa quando executada: " . $value["taker"] . "<br>";
                }*/

                //fetchTicker($exchange,$value["symbol"]);

                /*
                echo "<br><br>";
                echo "Timeframes: <br>";
                var_dump($exchange->timeframes);
                echo "<br><br>";
                */

                //orderBook($exchange,$value["symbol"]);
                candleStick($exchange,$value["symbol"]);
            }
        }
    }


    /*
    #################################################################################
    ########################## Exibe informações dos pares ########################
    #################################################################################
    */
    function fetchTicker($exchange_object,$symbol){
        $ticker = $exchange_object->fetchTicker($symbol);
        //echo "<strong>Cotações:</strong><br>";

        //Lista todos os itens do Ticker
        //var_dump($ticker);

        /*
        echo "Hora da cotação: " . $ticker["datetime"] . "<br>";
        echo "Abertura: " . $ticker["open"] . "<br>";
        echo "Fechamento: " . $ticker["close"] . "<br>";
        echo "Máximo: " . $ticker["high"] . "<br>";
        echo "Mínimo: " . $ticker["low"] . "<br>";
        echo "Média: " . $ticker["average"] . "<br>";
        echo "Venda: " . $ticker["ask"] . "<br>";
        echo "Compra: " . $ticker["bid"] . "<br>";
        echo "Spread: " . $ticker["ask"] - $ticker["bid"];
        echo " (" . ($ticker["ask"] - $ticker["bid"])/$ticker["bid"] . "%)<br>";
        echo "Volume base (1 dia): " . $ticker["baseVolume"] . "<br>";
        */

        $dt = new DateTime($ticker["datetime"]);
        $dt->setTimezone(new DateTimeZone('America/Sao_Paulo'));

        include("/var/www/makebit/bg_conexao_bd.php");

        $spread = $ticker["ask"] - $ticker["bid"];

        $sql = "insert into ticker (exchange, market, datetime, open, close, high, low, average, sell, buy, spread, volume_24h)
        SELECT '".$exchange_object->name."', '".$symbol."', '".date_format($dt,"Y-m-d H:i")."', '".$ticker["open"]."', '".$ticker["close"]."', '".$ticker["high"]."', '".$ticker["low"];

        $sql2 = "', '".$ticker["average"]."', '".$ticker["ask"]."', '".$ticker["bid"]."', '".$spread."', '".$ticker["baseVolume"]."'
                from ticker WHERE NOT EXISTS (
                    SELECT 1 FROM ticker WHERE exchange = '".$exchange_object->name."' and market='".$symbol."' and datetime='".date_format($dt,"Y-m-d H:i")."'
                ) LIMIT 1;";

        $consulta = mysqli_query($GLOBALS['con'],$sql.$sql2);
        echo "SQL: " . $sql.$sql2 . "<br>";
        mysqli_close($GLOBALS['con']);
        //echo date_format($dt,"Y-m-d H:i") .", ". $exchange_object->name . ", " . $symbol . ", Ticker salvo no banco de dados! <br>";
    }

    function orderBook($exchange_object,$symbol){
        $orderBook = $exchange_object->fetchL2OrderBook($symbol);
        echo "<strong>Orderbook agrupado:</strong><br>";

        //Lista todos os itens do Ticker
        var_dump($orderBook);
        echo "<br>";
    }

    function fetchBalance($exchange_object){
        $balance = $exchange_object->fetchBalance();
        echo "<strong>Balance agrupado:</strong><br>";

        //Lista todos os itens do Ticker
        var_dump($balance);
        echo "<br>";
    }

    function candleStick($exchange_object,$symbol){
        if ($exchange_object->hasFetchOHLCV){
            $since_time = strtotime("-2 minutes")*1000;
            $qtde_limit = 1;
            $janela = '1m';
            $resp = $exchange_object->fetch_ohlcv($symbol,$janela,$since_time,$qtde_limit);
            //echo "Candlestick: <br>";
            //var_dump($resp);
            foreach ($resp as $key_cotacao => $value_cotacao) {
                $data = new DateTime();
                $data->setTimestamp($value_cotacao[0]/1000);
                $data->setTimezone(new DateTimeZone('America/Sao_Paulo'));
                /*
                echo "Data formatada: ". date_format($data,"Y-m-d H:i") . "<br>";
                echo "Abertura". $value_cotacao[1] . "<br>";
                echo "Máximo". $value_cotacao[2] . "<br>";
                echo "Mínimo". $value_cotacao[3] . "<br>";
                echo "Fechamento". $value_cotacao[4] . "<br>";
                echo "Volume". $value_cotacao[5] . "<br>";
                */
                include("/var/www/makebit/bg_conexao_bd.php");
                $sql = "insert into candlestick (exchange, market, datetime, open, high, low,close,volume)
                SELECT '".$exchange_object->name."', '".$symbol."', '".date_format($data,"Y-m-d H:i")."', '".$value_cotacao[1]."', '".$value_cotacao[2]."', '".$value_cotacao[3]."', '".$value_cotacao[4];
                $sql2 = "', '".$value_cotacao[5]."'
                        from candlestick WHERE NOT EXISTS (
                            SELECT 1 FROM candlestick WHERE exchange = '".$exchange_object->name."' and market='".$symbol."' and datetime='".date_format($data,"Y-m-d H:i")."'
                        ) LIMIT 1;";
                $consulta = mysqli_query($GLOBALS['con'],$sql.$sql2);
                //echo "SQL: " . $sql.$sql2 . "<br>";
                mysqli_close($GLOBALS['con']);
                echo date_format($data,"Y-m-d H:i") .", ". $exchange_object->name . ", " . $symbol . ", CandleStick salvo no banco de dados! <br>";
            }
        }
    }

    /*
    #################################################################################
    ################################ Executa funções ################################
    #################################################################################
    */

    //echo '<h2>Manual <a href="https://github.com/ccxt/ccxt/wiki/Manual">CCXT</a></h2>';
    //echo "<br>";

    //$$$$$$$$ All Exchanges $$$$$$$$$$$$$
    /*
    $exchanges = ['foxbit', 'mercado','binance','hitbtc2','poloniex'];
    $apiKey = ['1ugWU8ZhddUB2gSGpC10bGiNJArrmDRyOqEohJEXFxs','139ff76e8106aab4296bad471ab5ec9b','WLKm8wbuGXiDLl1x0Sqpjv1l9FvJWaft6v9U4y2dJ8SVmFvO9pajhY7WjJcWwXW3','b5d26c9e59f316a0d35a6c182f0c87c1','7Y5FP6SM-2L408EPJ-O80W6D9G-7PQOKOAH'];
    $apiSecret = ['GKkiFyq3V5hqZGIGzCIH7CGfYsxRiskbJRG3t9WOol0','6fc62251f9fb3ee2c382308c210c2887e49f18788e49608250cbecc258e5a1c8','BWmssqWKUMtGGTsCCU3ZVmyz9vOuzp76ONGplyreEI5yNUP1jUNq4FeypfWNknvn','cc62c88b607cb6d16ffa4a6139a25494','2c82ddbff63843f95104df2a105e2fb5292372f28d8b95c3b112be12a29a7fec65e2b6fcc3700a6b6016ba68b4628b4d5ae138526d386981a6898330fb14893f'];
    */

    $exchanges = ['binance'];
    $apiKey = ['WLKm8wbuGXiDLl1x0Sqpjv1l9FvJWaft6v9U4y2dJ8SVmFvO9pajhY7WjJcWwXW3'];
    $apiSecret = ['BWmssqWKUMtGGTsCCU3ZVmyz9vOuzp76ONGplyreEI5yNUP1jUNq4FeypfWNknvn'];
    
    fetch_exchanges($exchanges,$apiKey,$apiSecret);

    //update_BITHUMB_TRANSACTIONS();
    //update_BITHUMB_ORDERBOOK();

    update_moedas_reais();

    update_foxbit_transactions();
    //update_foxbit_orderbook();

?>
