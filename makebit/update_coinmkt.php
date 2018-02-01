<?php
    //ini_set('display_errors','on');
    //error_reporting(E_ALL);
    header('Content-type: text/html; charset=utf-8');


    $erro=0;
    $msg="Nada executado";
    $opt="Nenhuma Opt";
    $GLOBALS['funcao']="update_CoinMarketCap";

    function update_coinmkt(){
        //#############################################################################
        //################### RAKING DE MERCADO coinmarketcap #########################
        //#############################################################################
        $response = file_get_contents('https://api.coinmarketcap.com/v1/ticker/?convert=brl&limit=15');
        //if $response is JSON, use json_decode to turn it into php array:
        $response = json_decode($response);
        var_dump($response);
        //echo "<br><br><br>";


        foreach ($response as $moeda) {
            $vet_campos = [];
            $vet_valores = [];
            foreach ($moeda as  $campo => $valor) {
                //var_dump($moeda);
                //echo "<strong>{$campo}</strong>: {$valor}<br>";
                 array_push($vet_campos,$campo);
                 array_push($vet_valores,$valor);
            }

            include("/var/www/makebit/bg_conexao_bd.php");
            $sql = "insert into cotacoes_coinmkt (";
            $sql = $sql . implode(', ',$vet_campos).")";
            $sql = $sql . " VALUES('" . implode("','",$vet_valores)."');";
            echo "Conteudo do SQL: {$sql}";
            $consulta = mysqli_query($GLOBALS['con'],$sql);
            
            mysqli_close($GLOBALS['con']);

            $erro=0;
            $msg="Dados de CoinMarketCap atualizados!";
            $opt="";
        }

        $envia_resposta = array("erro"=>$erro,"msg"=>$msg,"funcao"=>$GLOBALS['funcao'],"opt"=>$opt,"now"=>date("Y/m/d").'-'.date("h:i:sa"));
        print json_encode($envia_resposta, JSON_PRETTY_PRINT);
        echo "<br>";
    }

    update_coinmkt();
?>
