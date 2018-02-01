<?php
//auterando documento para html
  header('content-type: text/html; charset=utf-8');
  echo "<h1> Aula: Delimitadores de Código </h1>";
//variaveis normais
  $filipe= "Filipe Caldas";
  $idade= 30;
  $esposa="amanda";

  echo "meu nome é {$filipe}, tenho {$idade} anos e estou casado com {$esposa}.<hr>";
// desgraça de variavel que nao serve pra nada mais conhecida como  referencia

$var = "empresa";
$$var = "myde";

echo "minha {$var} é a {$empresa}";
echo "<hr>";

// fixando variaveis
$numero = 31;
$nomeDoAluno = "Marcos";
$nomeDaProfessora = "Larissa";
$$nomeDaProfessora = "Alessandra";

echo "o número do {$nomeDoAluno} é o {$numero} <br>";
echo "E {$nomeDoAluno} esta tendo aula com a {$nomeDaProfessora}.";
echo "o sobrenome da professora {$nomeDaProfessora} é {$Larissa}. <br> ";

var_dump($numero);

?>
