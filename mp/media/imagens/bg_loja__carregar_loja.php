<?php
    //ini_set('display_errors','on');
    //error_reporting(E_ALL);

    header('Content-Type: text/html; charset=utf-8');
    session_start();
    include("/var/www/mp/bg_conexao_bd.php");

    $msg = "Nada foi executado!";
    $funcao = "loja";
    $opt = "loja__carregar_loja";
    $erro = 0;
    $code="";

    //Verifica se distribuidor está definido
    //Verifica se usuário está Logado'
    include("/var/www/mp/bg_protege_php.php");
    $status_log = status_login($resposta); //acredite que $resposta existe, ele está no bg_protege_php
    //echo "status_log: ".$status_log."<br>";
    
    //Se estiver logado executa o código
    if($status_log=="mantem"){
        
        //Exibe o detalhe de um determinado anúncio
        $sql_consulta1 = "select t1.titulo, t1.descricao_texto1, t1.descricao_texto2, t1.imagem1, t2.valor, t4.parcelas, t1.descricao_texto3, t1.imagem2, t1.imagem3, t1.id, t5.resp_frete from  tb_produto_base t1 left join tb_prod_distrib t2 on(t1.id=t2.id) left join tb_forma_pgto t3 on(t2.ID_DISTRIBUIDOR=t3.ID_DISTRIBUIDOR) left join tb_lst_forma_pgto t4 on (t3.ID_FORM_PGTO=t4.id) left join tb_frete t5 on(t2.id_distribuidor=t5.id_distribuidor) where t2.id_distribuidor = ". $_SESSION["user_id_distribuidor"] . ";";
        //echo "Essa é a consulta: " . $sql_consulta1 . "<br>";
        $consulta1 = mysqli_query($GLOBALS['con'],$sql_consulta1);        
        
        if(mysqli_num_rows($consulta1)>0){
            //Se existir um anúncio com esse ID faz as demais consultas...

            //$envia_dados = array();
            for($i=0;$i<mysqli_num_rows($consulta1);$i++){
                $dados1 = mysqli_fetch_row($consulta1);
                $code = $code.'
                    <div class="col s12 m3 anuncio">
                    <a href="javascript:void(0);" onclick="entrar_produto(\''.$dados1["9"].'\');"
                        <div class="icon-block hoverable" style="padding-top:5px;padding-bottom:5px;">
                            <p class="center txt_anuncio_titulo brown-text text-darken-1">'.$dados1["0"].'</p>
                            <div class="center">
                                <img src="'.$dados1["3"].'" alt="" class="img_anuncio center">
                            </div>
                            <p class="center txt_anuncio">'.$dados1["1"].'</p>
                            <div class="row">
                                <div class="center">
                                    <p class="txt_anuncio_qtde grey-text text-darken-3">1 unidade com 100g</p>
                                </div>
                            </div>
                            <div class="row center">
                                <a href="javascript:void(0);" onclick="entrar_produto(\''.$dados1["9"].'\');" class="waves-effect waves-light btn orange darken-4">R$ '.$dados1["4"].'</a>
                            </div>
                            <div class="row">';
                            if($dados1["10"]=="1")
                            {
                                $frete='Frete Grátis';
                                $code = $code.
                                    '<i class="fa fa-2x fa-truck green-text text-darken-3 ico_anuncio" aria-hidden="true"></i>                
                                    <p class="txt_anuncio_condicao1 green-text text-darken-3">'.$frete.'</p>';
                            }
                           
                              $code = $code.'      
                            </div>
                        </div>
                        </a>
                    </div>';
                $titulo =  $dados1[0];
                $detalhe_texto1 =  $dados1[1];
                $detalhe_texto2 =  $dados1[2];
                $imagem1 = $dados1[3];
                $detalhe_preco =  $dados1[4];
                $parcelas =  $dados1[5];   
                $detalhe_texto3 =  $dados1[6];
                $imagem2 = $dados1[7];
                $imagem3 = $dados1[8];
            }
        }
    }else{
        $erro = 1;
        $msg = "Usuário não autenticado";
        $opt = "popup";
    }

    mysqli_close($GLOBALS['con']);
    $envia_resposta = array("erro"=>$erro,"code"=>$code,"funcao"=>$funcao, "opt"=>$opt, "titulo"=>$titulo, "detalhe_texto1"=>$detalhe_texto1, "detalhe_texto2"=>$detalhe_texto2, "imagem1" => $imagem1, "detalhe_preco" => $detalhe_preco, "parcelas" => $parcelas, "detalhe_texto3"=>$detalhe_texto3, "imagem2" => $imagem2, "imagem3" => $imagem3);
    print json_encode($envia_resposta, JSON_PRETTY_PRINT);
?>