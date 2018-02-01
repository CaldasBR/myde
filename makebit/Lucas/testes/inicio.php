<?php

header ('content-type: text/html; charset=utf-8');

$lucas = "Lucas Willian";

echo "meu nome é {$lucas}";

$arr = array();
$arr ['nome']= "João";
$arr ['idade']= 25;
var_dump($arr);

echo "<hr>";
$idade= 25;
$maior = ($idade > 20);
var_dump($maior);

echo "<hr>";

if($maior):
  echo "João tem mais de 20 anos";
endif;

echo "<hr>";


  $i = 1;
  $e = 1;
  while($i <= 10 && $e <=10):
  echo "{$i} X {$e} =" . $i * $e . "<br>";
  $i ++;
  $e ++;
  endwhile;

?>
