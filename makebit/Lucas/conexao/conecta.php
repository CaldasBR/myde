
<?php
    header('Content-type: text/html; charset=utf-8');

    $host = "makebit.com.br";
    $usuario ="usersql";
    $senha = "SQLqsenha123";
    $bd= "makebit";

    $mysql=new mysqli($host, $usuario, $senha, $bd);

    if($mysql -> conect_errno){
        echo "Ops houve um erro (". $mysql -> conect_errno . ") ". $mysql -> conect_error . "<br>";
    }
    else{
      echo "Parabéns a conexão funciona <br>";
    }

    $sql = "SELECT * from candlestick limit 10";
    $result = mysqli_query($mysql,$sql);

    if ($result):
        while($dados = mysqli_fetch_row($result)){
            var_dump($dados);

<<<<<<< HEAD
            echo "<br><br><br>";
            echo "Broker: " . $dados[0] . "<br>";
            echo "Market: " . $dados[1] . "<br>";
            echo "Data: " . $dados[2] . "<br>";
            echo "etc...<br><br>"
        }
    }else{
=======
    }else:
>>>>>>> 36e2ff1395423b3e9440a8635cf21da9681dea2c
        echo "A consulta não retornou nenhum resultado";
    endif;

    echo "<br><br>Lucas agora vai! e o commit tmb foi! Resolvido =) <br>";
    echo "beleza muito obrigado <br>";
    echo "teste sintaxe ok <br>";
    mysqli_close($mysql);

?>
