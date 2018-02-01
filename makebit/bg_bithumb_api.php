<?php
    ini_set('display_errors','on');
    error_reporting(E_ALL);

    header('Content-Type: text/html; charset=utf-8');
    echo '<meta name="viewport" content="width=device-width, initial-scale=1.0" />';

    include("/var/www/makebit/bg_funcoes_genericas.php");

    require("bg_bithumb_xcoin_client.php");

    function informacoes_conta($api){
        echo "<h2>Informações da conta: </h2><br>";
        $rgParams['currency'] = 'BTC';
        $result = $api->xcoinApiCall("/info/account", $rgParams);
        print_r($result);
        echo "<br><br>";
    }

    function informacoes_saldo($api){
        echo "<h2>Informações do saldo: </h2><br>";
        $rgParams['currency'] = 'ALL';
        $result = $api->xcoinApiCall("/info/balance", $rgParams);
        print_r($result);
        unset($rgParams);
        echo "<br><br>";
    }

    function informacoes_cotacao_moedas_user($api,$moedas){
        echo "<h2>Cotações de cada moeda feitas pelo cliente : </h2><br>";
        foreach ($moedas as $coin){
            echo "<strong>$coin</strong><br>";
            $rgParams['order_currency'] = $coin;
            $rgParams['payment_currency'] = 'KRW';
            $result = $api->xcoinApiCall("/info/ticker", $rgParams);
            print_r($result);
            echo "<br><br>";
            unset($rgParams);
        }
    }

    function historico_transacoes_moeda($api,$moedas){
        echo "<h2>Histórico de transações: </h2><br>";
        foreach ($moedas as $coin){
            echo "<strong>$coin</strong><br>";
            $rgParams['currency'] = $coin;
            $rgParams['searchGb'] = '0';
            $result = $api->xcoinApiCall("/info/user_transactions", $rgParams);
            print_r($result);
            echo "<br><br>";
            unset($rgParams);
        }
    }

    function enviar_ordem($api,$tipo,$moeda,$valor,$quantidade){
        echo "<h2>Enviando ordem de: ".$tipo."</h2><br>";

        $erro = 0;
        $moedas_validas = ['BTC', 'ETH', 'DASH', 'LTC', 'ETC', 'XRP', 'BCH', 'XMR', 'ZEC', 'QTUM', 'BTG', 'EOS'];

        if (in_array($moeda, $moedas_validas)){
            switch($moeda){
                case 'BTC':
                    $minimo = 0.001;
                    break;
                case 'ETH':
                    $minimo = 0.01;
                    break;
                case 'DASH':
                    $minimo = 0.01;
                    break;
                case 'LTC':
                    $minimo = 0.01;
                    break;
                case 'ETC':
                    $minimo = 0.1;
                    break;
                case 'XRP':
                    $minimo = 10;
                    break;
                case 'BCH':
                    $minimo = 0.001;
                    break;
                case 'XMR':
                    $minimo = 0.01;
                    break;
                case 'ZEC':
                    $minimo = 0.001;
                    break;
                case 'QTUM':
                    $minimo = 0.1;
                    break;
                case 'BTG':
                    $minimo = 0.01;
                    break;
                case 'EOS':
                    $minimo = 1;
                    break;
                default:
                    $erro = 1;
                    echo "Moeda não encontrada para avaliar volume mínimo.<br>";
                    break;
            }

            if($quantidade<$minimo){
                $erro = 1;
                echo "A quantidade solicitada de compra foi de :".$quantidade." e o volume mínimo permitido é: ".$minimo."<br>";
            }
        }else{
            $erro = 1;
            echo "Erro moeda: " . $erro . "<br>";
        }

        if($valor<=0){
            $erro = 1;
            echo "O valor solicitado é inválido<br>";
        }

        switch($tipo){
            case 'Compra':
                $type = 'bid';
                break;
            case 'Venda':
                $type = 'ask';
                break;
            default:
                $erro = 1;
                echo "O tipo '".$tipo."' é inválido<br>";
                break;
        }

        if($erro==0){
            echo "Moeda: <strong>$moeda</strong><br>";

            $rgParams['order_currency'] = $moeda;
            $rgParams['Payment_currency'] = 'KRW';
            $rgParams['units'] = $quantidade;
            $rgParams['price'] = $valor;
            $rgParams['type'] = $type;
            $result = $api->xcoinApiCall("/trade/place", $rgParams);
            print_r($result);
            echo "<br><br>";
            unset($rgParams);
        }else{
            echo "Paramentros inválidos, por favor verifique<br><br>";
        }
    }

    function ordens_pendentes($api,$moedas,$qtde_dias){
        echo "<h2>Todas as ordens de cada moeda de compra(bid) e venda(ask): </h2><br>";
        $type = ['bid','ask'];
        foreach ($moedas as $coin){
            echo "<strong>$coin</strong><br>";
            //$rgParams['order_id'] = 0;
            //$rgParams['order_id'] = 1514286186778287;
            $rgParams['count'] = 100;
            $time = '-' . 1440*$qtde_dias . ' minutes';
            echo "Time: $time <br>";
            $rgParams['after'] = strtotime($time);
            $rgParams['currency'] = $coin;
            foreach ($type as $tipo){
                echo "<strong>$tipo</strong><br>";
                $rgParams['type'] = $tipo;
                echo "Parametros: <br>";
                print_r($rgParams);
                $result = $api->xcoinApiCall("/info/orders", $rgParams);
                print_r($result);
                echo "<br><br>";
            }
            unset($rgParams);
        }
    }


    // ######## Chaves para autenticação ##########
    $api = new XCoinAPI("96b0daebcabe11dd8b40769383dad953", "9e77ce87ace1f9b681e6c19a33579393");

    // ######## Moedas a serem utilizadas ##########
    $moedas = ['BTC', 'ETH', 'DASH', 'LTC', 'ETC', 'XRP', 'BCH', 'XMR', 'ZEC', 'QTUM', 'BTG', 'EOS'];

    // ######## Funções a serem executadas ##########

    /*
    informacoes_conta($api);
    informacoes_saldo($api);
    informacoes_cotacao_moedas_user($api,$moedas);
    historico_transacoes_moeda($api,$moedas);
    //enviar_ordem($api,'Venda','QTUM',84000,11.5);
    */

    //ordens_pendentes($api,$moedas,7);


?>
