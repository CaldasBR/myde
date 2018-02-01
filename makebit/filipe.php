<?php

    ini_set('display_errors','on');
    error_reporting(E_ALL);

    header('Content-Type: text/html; charset=utf-8');
    echo '<meta name="viewport" content="width=device-width, initial-scale=1.0" />';

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

        /*$sql = "insert into ticker (exchange, market, datetime, open, close, high, low, average, sell, buy, spread, volume_24h)
        SELECT '".$exchange_object->name."', '".$symbol."', '".date_format($dt,"Y-m-d H:i")."', '".$ticker["open"]."', '".$ticker["close"]."', '".$ticker["high"]."', '".$ticker["low"];

        $spread = $ticker["ask"] - $ticker["bid"];

        $sql2 = "', '".$ticker["average"]."', '".$ticker["ask"]."', '".$ticker["bid"]."', '".$spread."', '".$ticker["baseVolume"]."'
                from ticker WHERE NOT EXISTS (
                    SELECT 1 FROM ticker WHERE exchange = '".$exchange_object->name."' and market='".$symbol."' and datetime='".date_format($dt,"Y-m-d H:i")."'
                ) LIMIT 1;";
        $consulta = mysqli_query($GLOBALS['con'],$sql.$sql2);
        //echo "SQL: " . $sql.$sql2 . "<br>";
        mysqli_close($GLOBALS['con']);
        */
        echo date_format($dt,"Y-m-d H:i") .", ". $exchange_object->name . ", " . $symbol . ", Ticker salvo no banco de dados! <br>";
    }

    function orderBook($exchange_object,$symbol){
        $orderBook = $exchange_object->fetchL2OrderBook($symbol);
        //echo "<strong>Orderbook agrupado:</strong><br>";

        //Lista todos os itens do Ticker
        var_dump($orderBook);
        echo "<br>";
    }

    function fetchBalance($exchange_object){
        $balance = $exchange_object->fetchBalance();
        //echo "<strong>Balance agrupado:</strong><br>";

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
?>
