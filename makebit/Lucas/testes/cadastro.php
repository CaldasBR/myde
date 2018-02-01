<?php

  header('Content-type: text/html; charset=utf-8');

  $bolsa = "Vermelha";

  if(!empty($bolsa)):
          if($bolsa == "Vermelha"):
            echo "Amor, tem a vermelha";
          elseif($bolsa == "Preta"):
            echo "Amor, comprei a Preta";
          else:
            echo "amor nao tem a cor que voce queria";
              $amor = true;

              if($amor):
                echo "Okay amor obrigada";
              else:
                echo "Se vira";
              endif;
          endif;
  else:
    echo "Nao tem a bolsa que voce queria :/";
  endif;

  ?>
