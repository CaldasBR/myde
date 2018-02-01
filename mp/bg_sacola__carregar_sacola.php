<?php
    //ini_set('display_errors','on');
    //error_reporting(E_ALL);

    header('Content-Type: text/html; charset=utf-8');
    session_start();
    include("/var/www/mp/bg_conexao_bd.php");

    $msg = "Nada foi executado!";
    $funcao = "sacola";
    $opt = "sacola__carregar_sacola";
    $erro = 0;
    $code="";

    //Verifica se distribuidor está definido
    //Verifica se usuário está Logado'
    include("/var/www/mp/bg_protege_php.php");
    $status_log = status_login($resposta); //acredite que $resposta existe, ele está no bg_protege_php
    //echo "status_log: ".$status_log."<br>";
    
    //Se estiver logado executa o código
    if($status_log=="mantem"){
        if(isset($_GET["fn"]) || isset($_POST["fn"])){
            if(isset($_GET["fn"])){
                $fn = addslashes($_GET["fn"]);
            }else{
                $fn = addslashes($_POST["fn"]);
            }
            switch ($fn){
                case 'outros':
                    $funcao = "verificar_carrinho";
                    $msg='Direcionar para sacola';
                    $opt = "direcionar_sacola";
                    break;
                case 'sacola':
                    $msg = "Nada foi executado!";
                    $funcao = "sacola";
                    $opt = "sacola__carregar_sacola";
                    break;
            }
        //Exibe o detalhe de um determinado anúncio
            $sql_consulta1 = "select
            t1.imagem1
            ,t1.titulo
            ,sum(t3.qtde) as qtde
            ,t2.valor
            ,t3.ID_DISTRIBUIDOR
            ,t3.ID_STATUS
            ,t1.id
            from 
                tb_produto_base t1
                left join tb_prod_distrib t2
                on(t1.id=t2.id) and (t2.id_distribuidor = ".$_SESSION['user_id_distribuidor'].")
                inner join tb_carrinho t3
                on(t1.id=t3.id_produto)
            where t3.id_user =".$_SESSION['user_id']."
            group by
            t1.imagem1
            ,t1.titulo
            ,t2.valor
            ,t3.ID_DISTRIBUIDOR
            ,t3.ID_STATUS;";
            //echo "Essa é a consulta: " . $sql_consulta1 . "<br>";
            $consulta1 = mysqli_query($GLOBALS['con'],$sql_consulta1);        

            if(mysqli_num_rows($consulta1)>0){
                //Se existir um anúncio com esse ID faz as demais consultas...

                //$envia_dados = array();
                $msg="";
                for($i=0;$i<mysqli_num_rows($consulta1);$i++)
                {
                    $dados1 = mysqli_fetch_row($consulta1);
                    $valor_unit_format = number_format($dados1[3], 2, ',', '.');
                    $valor_total_format = number_format($dados1[3]*$dados1[2], 2, ',', '.');
                    $msg = $msg. '
                        <tr>
                            <td>
                                <img class="img_sacola" src="'.$dados1[0].'">
                                <span class="produto_sacola">'.$dados1[1].'</span>
                            </td>
                            <td>
                                <div class="input-field">
                                    <select id="qtde_'.$dados1[6].'" onchange="atualizar_qtde('.$dados1[6].','.$dados1[3].')">
                                        <option value="" disabled >Ecolha uma opção</option>';
                                        for($c=1;$c<=10;$c++)
                                        {
                                            if($c==$dados1[2])
                                            {
                                             $msg = $msg. '<option value="'.$c.'" selected>'.$c.'</option>';
                                            }
                                            else
                                            {
                                            $msg = $msg. '<option value="'.$c.'" >'.$c.'</option>';
                                            }
                                        }
                                    $msg = $msg. '</select>
                                    <label>Quantidade</label>
                                </div>                            
                                <br>
                                <a href="javascript:void(0);" onclick="remover_produto(\''.$dados1[6].'\');"> Remover</a>
                            </td>
                            <td>
                            <td>
                                <span id="valor_total_'.$dados1[6].'">R$ '.$valor_total_format.'</span>
                            </td>
                        </tr>';
                }
            } else {
                switch ($fn){
                    case 'outros':
                        $erro = 1;
                        $msg = utf8_decode("Não existem produtos no carrinho");
                        $opt = "Toast";    
                    break;
                    case 'sacola':
                        $msg = "Nada foi executado!";
                        $funcao = "sacola";
                        $opt = "sacola__carregar_sacola__sacola_vazia";
                    break;
                }   
            }
        } else{
            $erro = 1;
            $msg = "Solictação Incorreta";
            $opt = "expulsa";
            }
    } else{
        $erro = 1;
        $msg = utf8_decode("Usuário não autenticado");
        $opt = "popup";
    }
    $msg=utf8_encode($msg);    
    mysqli_close($GLOBALS['con']);
    $envia_resposta = array("erro"=>$erro,"msg"=>$msg,"funcao"=>$funcao, "opt"=>$opt);
    print json_encode($envia_resposta, JSON_PRETTY_PRINT);
?>