<?php

  header ('content-type: text/html; charset=utf-8');

  $bolsa = "Rosa";

  if(!empty($bolsa)):

     if($bolsa == "Vermelha"):
       echo "Amor, comprei a bolsa vermelha";
     elseif($bolsa == "Preta"):
       echo "Amor, comprei a bolsa que voce queria";
    else:
      echo "Amor, nao tinha a bolsa que queria <br>";
      $amor = false;
       if($amor):
         echo "Okay amor!";
       else:
         echo "Se vira !";
      endif;
    endif;
  endif;

  echo "porque nao esta funcionando? <hr>";
  echo "testando commit";


?>
