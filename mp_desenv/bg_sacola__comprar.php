<?php
    //ini_set('display_errors','on');
    //error_reporting(E_ALL);

    header('Content-Type: text/html; charset=utf-8');
    session_start();
    include("/var/www/mp_desenv/bg_conexao_bd.php");

    $msg = "Nada foi executado!";
    $funcao = "sacola";
    $opt = "sacola__comprar";
    $erro = 0;
    $code="";

    //Verifica se distribuidor está definido
    //Verifica se usuário está Logado'
    include("/var/www/mp_desenv/bg_protege_php.php");
    $status_log = status_login($resposta); //acredite que $resposta existe, ele está no bg_protege_php
    //echo "status_log: ".$status_log."<br>";
    
    //Se estiver logado executa o código
    if($status_log=="mantem"){
        
        //Exibe o detalhe de um determinado anúncio
        $sql="select t1.ID_PRODUTO, t1.QTDE, t2.valor, t3.A_cm, t3.L_cm, t3.C_cm, t3.peso_grs from tb_carrinho t1 left join tb_prod_distrib t2 on(t1.ID_PRODUTO=t2.id and t1.ID_DISTRIBUIDOR = t2.id_distribuidor) left join tb_produto_base t3 on(t1.ID_PRODUTO = t3.id) where t1.ID_USER = ".$_SESSION["user_id"]." and t1.ID_DISTRIBUIDOR = ".$_SESSION["user_id_distribuidor"].";";
        $consulta = mysqli_query($GLOBALS['con'],$sql);
        if(mysqli_num_rows($consulta)>0){
            $peso_final = 0;
            $cubagem = 0;
            $valor=0;
            for($i=0;$i<mysqli_num_rows($consulta);$i++){
                $row = mysqli_fetch_row($consulta);                
                $peso_final = $peso_final + ($row[6] * $row[1] );
                //height x width x length (centimeters), weight (grams)
                $cubagem = $cubagem + ($row[3]*$row[4]*$row[5])* $row[1];
                $valor = $valor + ($row[1] * $row[2]);
            }
            $lado = ceil(pow($cubagem,(1/3)));
            /*if($peso_final<40){
                $peso_final = 40;
            }*/
            //echo "Lado: " .$lado."<br>";
            if($lado<16){
                $altura = max(2,min(105,$lado*0.068966));
                $largura = max(11,min(105,$lado*0.379310));
                $cumprimento = max(16,min(105,$lado*0.551724));
                if(($altura+$largura+$cumprimento)>200){
                    $altura = $altura - ($altura+$largura+$cumprimento-200);
                }
                $dimensao_peso=$altura."x".$largura."x".$cumprimento.",".$peso_final;
            }elseif($lado>66.66){
                $dimensao_peso="66x66x66,".$peso_final;
            }else{
                $dimensao_peso=$lado."x".$lado."x".$lado.",".$peso_final;
            }
            //echo "Dimensão Final: " .$dimensao_peso."<br>";
            
        }
        
        if($valor<5){
            $erro = 1;
            $msg = utf8_decode("O valor mínimo de compra é R$5,00");
            $opt = "Toast";
        }else{
            $sql_insert="insert into tb_pedido_cabecalho (id_user, id_distr, valor, dimensoes) values (".$_SESSION["user_id"].",".$_SESSION["user_id_distribuidor"].", ".$valor.",'".$dimensao_peso."');";
            mysqli_query($GLOBALS['con'],$sql_insert);   
            $sql_pedido="select pedido from tb_pedido_cabecalho where dt_time=(select max(dt_time) from tb_pedido_cabecalho where id_user=".$_SESSION["user_id"]." and id_distr=".$_SESSION["user_id_distribuidor"].");";
            $consulta_pedido = mysqli_query($GLOBALS['con'],$sql_pedido);
            $ped = mysqli_fetch_row($consulta_pedido);
            $pedido=$ped[0];

            if(mysqli_num_rows($consulta)>0){
                $peso_final = 0;
                $cubagem = 0;
                $valor=0;
                $dimensao_peso="";
                mysqli_data_seek($consulta,0);
                for($j=0;$j<mysqli_num_rows($consulta);$j++){
                    $row = mysqli_fetch_row($consulta);
                    $peso_final = $row[6] * $row[1] ;
                    $cubagem =  ($row[3]*$row[4]*$row[5])* $row[1];
                    $valor = $row[1] * $row[2];
                    $lado = ceil(pow($cubagem,(1/3)));
                    $dimensao_peso=$lado."x".$lado."x".$lado.",".$peso_final; 
                    $sql_insert_item="insert into tb_pedido_itens (pedido, id_user, id_distr, id_produto, qtde, valor, dimensoes) values (".$pedido.",".$_SESSION["user_id"].",".$_SESSION["user_id_distribuidor"].", ".$row[0].", ".$row[1].", ".$valor.",'".$dimensao_peso."');";
                    //echo $sql_insert_item;
                    mysqli_query($GLOBALS['con'],$sql_insert_item);
                    $peso_final = 0;
                    $cubagem = 0;
                    $valor=0;
                    $dimensao_peso="";
                }
            }
        }
    }else{
        $erro = 1;
        $msg = utf8_decode("Usuário não autenticado");
        $opt = "Toast";
    }
    $msg=utf8_encode($msg);
    mysqli_close($GLOBALS['con']);
    $envia_resposta = array("erro"=>$erro,"msg"=>$msg,"funcao"=>$funcao, "opt"=>$opt);
    print json_encode($envia_resposta, JSON_PRETTY_PRINT);
?>