
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

    echo "br";
    if ($result){
        while($dados = mysqli_fetch_row($result)){
            var_dump($dados);
        }
    }else{
        echo "A consulta não retornou nenhum resultado";
    }

    echo "<br><br>Lucas agora vai! e o commit tmb foi 13!";
    mysqli_close($mysql);
?>
