<?php
    ini_set('display_errors','on');
    error_reporting(E_ALL);

    header('Content-Type: text/html; charset=utf-8');
    echo '<meta name="viewport" content="width=device-width, initial-scale=1.0" />';

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
                echo "Dados atualizados!<br>";
                echo '<script>function voltar(){window.location.href="http://makebit.com.br/bg_cotacao_foxbit.php";}</script>';
                echo '<button onclick="voltar();">Voltar</button>';
            }
        break;
        default:
            function curl($url) {
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
                $url1=$_SERVER['REQUEST_URI'];
                header("Refresh: 60; URL=$url1");
                echo "<title>Foxbit: R$ ". $response->last."</title>";
                echo '
                    <form action="bg_cotacao_foxbit.php?fn=update&loss='.$row[0].'&gain='.$row[1].'">
                        <input type="hidden" name="fn" value="update">
                        Stop LOSS: <input type="text" name="loss" value="'.$row[0].'"><br>
                        Stop GAIN: <input type="text" name="gain" value="'.$row[1].'"><br>
                        Fator ajuste: <input type="text" name="fator" value="'.$row[2].'"><br>
                        <input type="submit" value="Atualizar">
                    </form>

                ';

                echo "<hr>";
                echo "<strong>Cotação atual: " . $response->last . "</strong><br><br>";
                echo "Máximo: " . $response->high . "<br>";
                echo "Mínimo: " . $response->low . "<br>";
                echo "Última Compra: " . $response->buy . "<br>";
                echo "Última Venda: " . $response->sell . "<br>";
                echo "Volume total negociado: " . $response->vol . "<br>";
                echo "<hr>";

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
            
            mysqli_close($GLOBALS['con']);
    }
?>
