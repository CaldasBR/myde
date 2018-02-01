<?php

  header('Content_type: text/html; charset=utf-8' );
  include "conecta.php";
  $mysqli = new mysqli($host, $usuario, $senha, $bd);

  $consulta = "SELECT * from candlestick limit 100";
  $con = $mysqli ->query($consulta) or die (mysqli -> error);
  while ($dados = $consulta-> mysqli_fetch_array()){
    echo 'id' . $dados['id'] . '';
    echo 'tabela' . $dados['titulo'] . '';
  }
endwhile;

echo "oque eu achei:". $query->num_rows;

?>
