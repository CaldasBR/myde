<?php
    ini_set('display_errors','on');
    error_reporting(E_ALL);

    header('Content-Type: text/html; charset=utf-8');
    echo '<meta name="viewport" content="width=device-width, initial-scale=1.0" />';

    $funcao = "trade_foxbit";
    $erro = 0;
    $msg = "Nada foi executado!";
    $opt = "";

    $api_key_gl = 'bRVDzLyzfy1fEn2zlHDef45jhg38UZV9V1jF14pjxd8';
    $api_secret_gl = 'CJIsdJrNLIFvGszoVthA0RzhV6BI67ORoORwbSKGbEo';

    if(isset($_GET["fn"])){
        $fn = addslashes($_GET["fn"]);
    }else{
        $fn = 'nada';
    }

    switch ($fn){
        case 'update':
            if(isset($_GET["loss"])&&isset($_GET["gain"])){
                $loss = addslashes($_GET["loss"]);
                $gain = addslashes($_GET["gain"]);
                $fator = addslashes($_GET["fator"]);
                $sql = "UPDATE alerta_FOXBIT SET stop_loss=".$loss.",stop_gain=".$gain.",fator=".$fator.";";
                //echo "SQL: " . $sql . "<br>";
                include("/var/www/makebit/bg_conexao_bd.php");
                $query = mysqli_query($GLOBALS['con'],$sql);
                mysqli_close($GLOBALS['con']);
                echo "Dados atualizados!<br>";
                echo '<script>function voltar(){window.location.href="http://makebit.com.br/filipe.php";}</script>';
                echo '<button onclick="voltar();">Voltar</button>';
            }
        break;
        default:
            //######################## Chamadas para execução #################
            $url1=$_SERVER['REQUEST_URI'];
            header("Refresh: 30; URL=$url1");

            echo "<hr>";
            foxbit_ver_cotacao();
            echo "<hr>";

            foxbit_ver_saldo($api_key_gl,$api_secret_gl);
            echo "<hr>";

            sleep(1);
            foxbit_ver_ordens($api_key_gl,$api_secret_gl,0);
            echo "<hr>";

            /*
            sleep(1);
            foxbit_ver_ordens($api_key_gl,$api_secret_gl,1);
            echo "<hr>";
            */

            /*
            sleep(1);
            foxbit_ver_ordens($api_key_gl,$api_secret_gl,2);
            echo "<br><br>";*/

            //sleep(1);
            //foxbit_cancelar_ordem($api_key_gl,$api_secret_gl,1459157125830,171213174029);
            //echo "<br><br>";

            // ##### Ordem de COMPRA
            /*sleep(1);
            $prc = 56253.03;
            echo "Prc orig: ".$prc."<br>";
            $preco = intval($prc / 0.00000001);
            echo "Ao preço de : " . $preco . "<br>";


            $qtd =4545.97/$prc;
            echo "Qtd orig: ".$qtd."<br>";
            $qtde = intval($qtd / 0.00000001);
            echo "A quantidade de: " . $qtde . "<br>";

            foxbit_enviar_ordem($api_key_gl,$api_secret_gl,'Comprar', $preco, $qtde);
            */

            // ##### Ordem de VENDA
            /*
            sleep(1);
            $prc = 57173.03;
            echo "Prc orig: ".$prc."<br>";
            $preco = intval($prc / 0.00000001);
            echo "Ao preço de : " . $preco . "<br>";


            $qtd = 0.06492553;
            echo "Qtd orig: ".$qtd."<br>";
            $qtde = intval($qtd / 0.00000001);
            echo "A quantidade de: " . $qtde . "<br>";

            foxbit_enviar_ordem($api_key_gl,$api_secret_gl,'Vender', $preco, $qtde);
            */


            break;
    }

    function foxbit_ver_cotacao(){
        function curl($url){
            $ch = curl_init();  // Initialising cURL
            curl_setopt($ch, CURLOPT_URL, $url);    // Setting cURL's URL option with the $url variable passed into the function
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE); // Setting cURL's option to return the webpage data
            $data = curl_exec($ch); // Executing the cURL request and assigning the returned data to the $data variable
            curl_close($ch);    // Closing cURL
            return $data;   // Returning the data from the function
        }

        $a = curl('https://api.blinktrade.com/api/v1/BRL/ticker?crypto_currency=BTC');
        $response = json_decode($a);
        //var_dump($response);

        $sql = "select stop_loss, stop_gain, fator from alerta_FOXBIT";
        include("/var/www/makebit/bg_conexao_bd.php");

        $query = mysqli_query($GLOBALS['con'],$sql);

        if(mysqli_num_rows($query)>0){
            $row = mysqli_fetch_row($query);

            //Exibe página com parametros e cotações
            echo "<title>Foxbit: R$ ". $response->last."</title>";
            echo '
                <form action="filipe.php?fn=update&loss='.$row[0].'&gain='.$row[1].'">
                    <input type="hidden" name="fn" value="update">
                    Stop LOSS: <input type="text" name="loss" value="'.$row[0].'"><br>
                    Stop GAIN: <input type="text" name="gain" value="'.$row[1].'"><br>
                    Fator ajuste: <input type="text" name="fator" value="'.$row[2].'"><br>
                    <input type="submit" value="Atualizar">
                </form>
            ';

            echo "<strong>Cotação atual: " . $response->last . "</strong><br><br>";
            echo "Máximo: " . $response->high . "<br>";
            echo "Mínimo: " . $response->low . "<br>";
            echo "Última Compra: " . $response->buy . "<br>";
            echo "Última Venda: " . $response->sell . "<br>";
            echo "Volume total negociado: " . $response->vol . "<br>";

            //Dispara alerta por email
            include("/var/www/makebit/bg_enviaemail.php");
            if($response->last<$row[0] && !is_null($response->last) ){
                echo "<strong>Atingiu o stop LOSS</strong>";
                define_envia('stop_loss',$response->last,$row[0]);
                $sql = "UPDATE alerta_FOXBIT SET stop_loss=".$row[0]*(1-$row[2]).",stop_gain=".$row[1]*(1-$row[2]).";";
                $query = mysqli_query($GLOBALS['con'],$sql);
            }

            if($response->last>$row[1]  && !is_null($response->last) ){
                echo "<strong>Atingiu o stop GAIN</strong>";
                define_envia('stop_gain',$response->last,$row[1]);
                $sql = "UPDATE alerta_FOXBIT SET stop_loss=".$row[0]*(1+$row[2]).",stop_gain=".$row[1]*(1+$row[2]).";";
                $query = mysqli_query($GLOBALS['con'],$sql);
            }
        }
    }

    function foxbit_ver_saldo($api_key,$api_secret){
        global $erro,$msg,$opt;

        $api_url='https://api.blinktrade.com/tapi/v1/message';
        $nonce=intval(date("YmdHis"));

        $algorithm = 'sha256'; //"md5", "sha256", "haval160,4";
        $signature = hash_hmac($algorithm , $nonce , $api_secret);

        $params = array(
            "MsgType" => "U2"
            ,"BalanceReqID" => 487
        );

        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => "https://api.blinktrade.com/tapi/v1/message",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => json_encode($params),
        CURLOPT_HTTPHEADER => array(
            "apikey: ".$api_key,
            "cache-control: no-cache",
            "content-type: application/json",
            "nonce: ".$nonce,
            "signature: ".$signature
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            echo "cURL Error #:" . $err;
        }else {
            //echo $response;
            $resp = json_decode($response);

            if($resp -> Status == 200){
                echo "ID do cliente: " . $resp -> Responses[0] -> ClientID . "<br>";
                echo '<font  color="blue"> Valor disponível em R$: ' . sprintf("%.2f",$resp -> Responses[0] -> {'4'} -> BRL * 0.00000001) . '</font><br>';
                echo '<font  color="red"> Valor bloqueado em R$: ' . sprintf("%.2f",$resp -> Responses[0] -> {'4'} -> BRL_locked * 0.00000001);
                if($resp -> Responses[0] -> {'4'} -> BRL * 0.00000001 > 0){
                    $divisao = ($resp -> Responses[0] -> {'4'} -> BRL_locked  / $resp -> Responses[0] -> {'4'} -> BRL ) * 100;
                    echo " (".sprintf("%.2f%%",$divisao).") ";
                }else{
                    echo " (0.00%) ";
                }
                echo "</font><br>";
                echo "Valor disponível em BTC: " . $resp -> Responses[0] -> {'4'} -> BTC * 0.00000001 . "<br>";
                echo "Valor disponível em BTC_locked: " . $resp -> Responses[0] -> {'4'} -> BTC_locked * 0.00000001;
                if($resp -> Responses[0] -> {'4'} -> BTC){
                    $divisao = ($resp -> Responses[0] -> {'4'} -> BTC_locked  / $resp -> Responses[0] -> {'4'} -> BTC ) * 100;
                    echo " (".sprintf("%.2f%%",$divisao).") ";
                }else{
                    echo " (0.00%) ";
                }
                echo "<br>";
            }else{
                echo "Status: " . $resp -> Status . "erro na exibição do saldo.<br>";
            }
        }
    }


    function foxbit_ver_ordens($api_key,$api_secret,$page){
        global $erro,$msg,$opt;

        $api_url='https://api.blinktrade.com/tapi/v1/message';
        $nonce=intval(date("YmdHis"));

        //echo "Nonce: " . $nonce . "<br>";

        $algorithm = 'sha256'; //"md5", "sha256", "haval160,4";
        $signature = hash_hmac($algorithm , $nonce , $api_secret);

        //echo "Signature: " . $signature . "<br>";

        $params = array(
            "MsgType" => "U4"
            ,"OrdersReqID" => 487
            ,"Page" => $page
            ,"PageSize" => 50
        );


        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => "https://api.blinktrade.com/tapi/v1/message",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => json_encode($params),
        CURLOPT_HTTPHEADER => array(
            "apikey: ".$api_key,
            "cache-control: no-cache",
            "content-type: application/json",
            "nonce: ".$nonce,
            "signature: ".$signature
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            echo "cURL Error #:" . $err;
        }else {
            //echo $response;
            $resp = json_decode($response);

            if($resp -> Status == 200){
                $resp_obj = $resp -> Responses;
                $resp_arr = $resp_obj[0];
                $resp_ordem = $resp_arr -> OrdListGrp;
                foreach ($resp_ordem as $chave => $item){
                    echo "[ClOrdID] ID da ordem enviado pelo cliente: " . $item[0] . "<br>";
                    echo "[OrderID] ID da ordem enviado pelo Blinktrade: " . $item[1] . "<br>";
                    echo "[CumQty] Quantidade executada dessa ordem: " . $item[2] * 0.00000001 . "<br>";

                    echo "[OrdStatus] Status da ordem: ";
                    switch ($item[3]) {
                        case "0":
                            echo "Novo" . "<br>";
                            break;
                        case "1":
                            echo "Parcialmente completo" . "<br>";
                            break;
                        case "2":
                            echo "Completo" . "<br>";
                            break;
                        case "4":
                            echo "Cancelado" . "<br>";
                            break;
                        case "8":
                            echo "Rejeitado" . "<br>";
                            break;
                        case "A":
                            echo "Novo Pendente" . "<br>";
                            break;
                    }

                    echo "[LeavesQty] Quantidade aberta para execução: " . $item[4] * 0.00000001 . "<br>";
                    echo "[CxlQty] Quantidade cancelada para essa ordem: " . $item[5] * 0.00000001 . "<br>";
                    echo "[AvgPx] Preço médio do que foi completado nessa orderm: " . $item[6] * 0.00000001  . "<br>";
                    echo "[Symbol]: " . $item[7] . "<br>";

                    echo "[Side] Lado: ";
                    switch ($item[8]){
                        case "1":
                            echo "Comprar " . "<br>";
                            break;
                        case "2":
                            echo "Vender " . "<br>";
                            break;
                    }

                    echo "[OrdType] Tipo da ordem: ";
                    switch ($item[9]){
                        case "2":
                            echo "Limitado <br>";
                            break;
                    }

                    echo "[OrderQty] Quantidade da ordem: " . $item[10] * 0.00000001 . "<br>";
                    echo "[Price] Preço por unidade de Bitcoin: " . $item[11] * 0.00000001  . "<br>";
                    echo "[OrderDate] Data da ordem: " . $item[12] . "<br>";
                    echo "[Volume] Volume (quantidade x preço): " . $item[13] * 0.00000001 . "<br>";
                    echo "[TimeInForce] Duração da ordem: ";
                    switch ($item[14]){
                        case "0":
                            echo "Dia <br>";
                            break;
                        case "1":
                            echo "Bom até Cancelar <br>";
                            break;
                        case "4":
                            echo "Completar ou Matar<br>";
                            break;
                    }
                    echo "<br><br>";
                }
            }else{
                echo "Status: " . $resp -> Status . "erro na execução da listagem de ordens.<br>";
            }
        }
    }

    function foxbit_cancelar_ordem($api_key,$api_secret,$OrderID,$ClOrdID){
        global $erro,$msg,$opt;

        $api_url='https://api.blinktrade.com/tapi/v1/message';
        $nonce=intval(date("YmdHis"));

        $algorithm = 'sha256'; //"md5", "sha256", "haval160,4";
        $signature = hash_hmac($algorithm , $nonce , $api_secret);

        $params = array(
            "MsgType" => "F"
            ,"OrderID" => $OrderID
            ,"ClOrdID" => $ClOrdID
        );


        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => "https://api.blinktrade.com/tapi/v1/message",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => json_encode($params),
        CURLOPT_HTTPHEADER => array(
            "apikey: ".$api_key,
            "cache-control: no-cache",
            "content-type: application/json",
            "nonce: ".$nonce,
            "signature: ".$signature
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            echo "cURL Error #:" . $err;
        }else {
            //echo $response;
            $resp = json_decode($response);

            if($resp -> Status == 200){
                $resp_obj = $resp -> Responses;
                $resp_arr = $resp_obj[0];
                echo "[ClOrdID]: " . $resp_arr -> ClOrdID . "<br>";
                    if($resp_arr -> MsgType == "9"){
                        echo "Ordem já não existe mais.<br>";
                    }else{
                        echo "[OrderID] ".$resp_arr -> OrderID."<br>";
                        echo "[ExecType] ".$resp_arr -> ExecType.", ordem de cancelamento<br>";
                        echo "[OrdStatus] Novo status da ordem: ";
                        switch ($resp_arr -> OrdStatus){
                            case "0":
                                echo "Novo" . "<br>";
                                break;
                            case "1":
                                echo "Parcialmente completo" . "<br>";
                                break;
                            case "2":
                                echo "Completo" . "<br>";
                                break;
                            case "4":
                                echo "Cancelado" . "<br>";
                                break;
                            case "8":
                                echo "Rejeitado" . "<br>";
                                break;
                            case "A":
                                echo "Novo Pendente" . "<br>";
                                break;
                        }
                        echo "<br>";

                    }
                echo "<br>";

                //{"Status": 200, "Description": "OK",
                //"Responses": [
                //{"OrderID": 1459156968212, "ExecID": 6360608,
                //"ExecType": "4", "OrdStatus": "4", "LeavesQty": 0,
                //"Symbol": "BTCBRL", "OrderQty": 6279962,
                //"LastShares": 0, "LastPx": 0, "CxlQty": 6279962,
                //"TimeInForce": "1", "CumQty": 0,
                //"MsgType": "8", "ClOrdID": "515376",
                //"OrdType": "2", "Side": "1",
                //"Price": 4850300000000, "ExecSide": "1",
                //"AvgPx": 0},
                //{"MsgType": "U3",
                // "4": {"BRL_locked": 0},
                // "ClientID": 90822692}]}
            }else{
                echo "Status: " . $resp -> Status . "erro na execução do cancelamento da ordem.<br>";
            }
        }
    }

    function foxbit_enviar_ordem($api_key,$api_secret,$acao, $preco, $qtde){
        global $erro,$msg,$opt;

        if(strtoupper($acao) == "COMPRAR"){
            $side = "1";
        }elseif(strtoupper($acao) == "VENDER"){
            $side = "2";
        }

        $api_url='https://api.blinktrade.com/tapi/v1/message';
        $nonce=intval(date("YmdHis"));

        $algorithm = 'sha256'; //"md5", "sha256", "haval160,4";
        $signature = hash_hmac($algorithm , $nonce , $api_secret);

        $params = array(
            "MsgType" => "D"
            ,"ClOrdID" => intval(date("ymdHis"))
            ,"Symbol" => "BTCBRL"
            ,"Side" => $side
            ,"OrdType" => "2"
            ,"Price" => $preco
            ,"OrderQty" => $qtde
            ,"BrokerID" => 4
        );

        //echo "Parametros: ";
        //var_dump($params);

        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => "https://api.blinktrade.com/tapi/v1/message",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => json_encode($params),
        CURLOPT_HTTPHEADER => array(
            "apikey: ".$api_key,
            "cache-control: no-cache",
            "content-type: application/json",
            "nonce: ".$nonce,
            "signature: ".$signature
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            echo "cURL Error #:" . $err;
        }else {
            //echo $response;
            $resp = json_decode($response);

            if($resp -> Status == 200){
                $resp_obj = $resp -> Responses;
                $resp_arr = $resp_obj[0];
                //echo "[ClOrdID]: " . $resp_arr -> OrderID . "<br>";
                if($resp_arr -> OrderID > 0){
                    echo "Ordem criada número: " . $resp_arr -> OrderID . "<br>";
                }else{
                    echo "Ordem não criada, motivo: ";
                    switch ($resp_arr -> OrdRejReason){
                        case "0":
                            echo "Opção do Broker/Exchange";
                            break;
                        case "1":
                            echo "Símbolo desconhecido";
                            break;
                        case "2":
                            echo "Exchange fechada";
                            break;
                        case "3":
                            echo "Ordem excede o limite";
                            break;
                        case "4":
                            echo "Muito tarde para entrar";
                            break;
                        case "5":
                            echo "Ordem desconhecida";
                            break;
                        case "6":
                            echo "Ordem duplicada (ClOrdID)";
                            break;
                        case "7":
                            echo "Duplicado de uma ordem verbalmente comunicada";
                            break;
                        case "8":
                            echo "Ordem velha";
                            break;
                        case "9":
                            echo "É necessário fazer o trade";
                            break;
                        case "10":
                            echo "ID do investidor inválido";
                            break;
                        case "11":
                            echo "Características não suportadas";
                            break;
                        case "12":
                            echo "Opção de fiscalização (vigilância)";
                            break;
                    }
                }
                echo "<br>";

                //{"Status": 200, "Description": "OK", "Responses": [{"OrderID": 1459157017769, "ExecID": 6368380, "ExecType": "0", "OrdStatus": "0", "LeavesQty": 12938, "Symbol": "BTCBRL", "OrderQty": 12938, "LastShares": 0, "LastPx": 0, "CxlQty": 0, "TimeInForce": "1", "CumQty": 0, "MsgType": "8", "ClOrdID": 171212090426, "OrdType": "2", "Side": "1", "Price": 5410405000000, "ExecSide": "1", "AvgPx": 0}, {"MsgType": "U3", "4": {"BRL_locked": 1341022983}, "ClientID": 90822692}]}
            }else{
                echo "Status: " . $resp -> Status . "erro na execução da ordem.<br>";
            }
        }
    }

    foxbit_ver_saldo($api_key_gl,$api_secret_gl);

?>
