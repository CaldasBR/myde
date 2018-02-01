<?php

//alterando o aqrquivo para html
header ('content-type: text/html; charset=utf-8');

//compilação
const DEV_NAME = "Lucas Willian";
const DEV_AGE = 22;
echo "meu nome é " .DEV_NAME ." e tenho " .DEV_AGE;

$lucas = "Lucas Willian";
$idade = 2*10;

echo "{$lucas} tem {$idade} anos de vida";
echo "<hr>";


//estruturas de controle

  $verdade = false;

  if($verdade):
      echo "true";
  else:
      echo "False";
  endif;
   echo "<hr>";

//while enquanto acontecer
   $i = 1;

   while($i <= 10):
     echo "{$i} X 7 =" .$i * 7 ." <br>";
     $i ++;
   endwhile;

   echo "<hr>";
//for each

$teste = ['pedro', 'augusto', 'maicon'];
foreach ($teste as $alunos):
    echo "Os alunos são {$alunos}. <br>";
endforeach;
echo "<hr>";

// testando function

function Tabuada ($numero){
  echo "A tabuada do {$numero} é: <br>";
  for ($x = 1; $x <= 10; $x ++):
      echo "{$numero} x {$x} = " . $numero * $x . "<br> ";
  endfor;
  echo "<hr>";
}

echo Tabuada (5);
echo Tabuada (6);
echo Tabuada (7);


?>
