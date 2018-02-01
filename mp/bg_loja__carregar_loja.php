<?php
    //ini_set('display_errors','on');
    //error_reporting(E_ALL);

    //header('Content-Type: text/html; charset=utf-8');
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
        $sql_consulta1 = "select t1.titulo, t1.descricao_titulo, t1.imagem1, t2.valor, t1.id, t5.resp_frete from  tb_produto_base t1 left join tb_prod_distrib t2 on(t1.id=t2.id) left join tb_frete t5 on(t2.id_distribuidor=t5.id_distribuidor) where t2.id_distribuidor = ". $_SESSION["user_id_distribuidor"] . ";";
        //echo "Essa é a consulta: " . $sql_consulta1 . "<br>";
        $consulta1 = mysqli_query($GLOBALS['con'],$sql_consulta1);        
        
        if(mysqli_num_rows($consulta1)>0){
            //Se existir um anúncio com esse ID faz as demais consultas...

            //$envia_dados = array();
            for($i=0;$i<mysqli_num_rows($consulta1);$i++){
                $dados1 = mysqli_fetch_row($consulta1);
                //var_dump($dados1);
                $msg_temp = $msg_temp.'
                    <a href="javascript:void(0);" onclick="entrar_produto(\''.$dados1[4].'\');">
                        <div class="col s12 m3 anuncio">
                            <div class="icon-block hoverable" style="padding-top:5px;padding-bottom:5px;">
                                <p class="center txt_anuncio_titulo brown-text text-darken-1">'.$dados1[0].'</p>
                                <div class="center">
                                    <img src="'.$dados1[2].'" alt="" class="img_anuncio center">
                                </div>
                                <p class="center txt_anuncio grey-text text-darken-3">'.$dados1[1].'</p>
                                <div class="row">
                                <p></p>                                
                                </div>
                                <div class="row center">
                                    <a href="javascript:void(0);" onclick="entrar_produto(\''.$dados1[4].'\');" class="waves-effect waves-light btn orange darken-4">R$ '.str_replace(".", ",",$dados1[3]).'</a>
                                </div>
                                <div class="row">';
                                if($dados1[5]==1)
                                {
                                    $frete=utf8_decode('Frete Grátis');
                                    $msg_temp = $msg_temp.
                                        '<i class="fa fa-2x fa-truck green-text text-darken-3 ico_anuncio" aria-hidden="true"></i>            
                                        <p class="txt_anuncio_condicao1 green-text text-darken-3">'.$frete.'</p>';
                                }
                                $msg_temp = $msg_temp.'
                                </div>
                            </div>
                        </div>
                    </a>';
            }
        }
    }else{
        $erro = 1;
        $msg_temp = "Usuário não autenticado";
        $opt = "expulsa";
    }

    //var_dump($code);

    $msg = utf8_encode($msg_temp);

    //var_dump($code_tratado);
    mysqli_close($GLOBALS['con']);
    $envia_resposta = array("erro"=>$erro,"msg"=>$msg,"funcao"=>$funcao, "opt"=>$opt);
    print json_encode($envia_resposta, JSON_PRETTY_PRINT);
?>