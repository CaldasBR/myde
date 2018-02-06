
<?php
    header('Content-type: text/html; charset=utf-8');

    $host = "makebit.com.br";
    $usuario ="usersql";
    $senha = "SQLqsenha123";
    $bd= "makebit";

    $mysql=new mysqli($host, $usuario, $senha, $bd);

    if($mysql -> conect_errno):
        echo "Ops houve um erro (". $mysql -> conect_errno . ") ". $mysql -> conect_error . "<br>";

    else:
        echo "Parabéns a conexão funciona <br>";
    endif;

    $sql = "SELECT * from candlestick limit 10";
    $result = mysqli_query($mysql,$sql);

    if($result):

        while($dados = mysqli_fetch_row($result)){
            var_dump($dados);

            echo "<br><br><br>";
            echo "Broker: " . $dados[0] . "<br>";
            echo "Market: " . $dados[1] . "<br>";
            echo "Data: " . $dados[2] . "<br>";
            echo "Abertura: " . $dados[3] . "<br>";
            echo "Topo: " . $dados[4] . "<br>";
            echo "fundo: " . $dados[5] . "<br>";
            echo "Fechamento: " . $dados[6] . "<br>";
            echo "Volume: " . $dados[7] . "<br>";
            echo "<br><br>";

        }
    else:
        echo "A consulta não retornou nenhum resultado";
    endif;

        mysqli_close($mysql);

?>
