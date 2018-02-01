<?php
//ini_set('display_errors','on');
//error_reporting(E_ALL);

header('Content-Type: text/html; charset=utf-8');
session_start();
include("/var/www/mp_desenv/bg_conexao_bd.php");

$msg_temp = "";
$funcao = "pedidos";
$opt = "pedidos__consultar";
$erro = 0;


//Verifica se os campos que são obrigatórios estão preenchidos

$id=$_SESSION["user_id"];
include("/var/www/mp_desenv/bg_protege_php.php");
$status_log = status_login($resposta); //acredite que $resposta existe, ele está no bg_protege_php
//echo "status_log: ".$status_log."<br>";

//Se estiver logado executa o código
if($status_log=="mantem"){
    $sql_ped_cab = "select t1.pedido, t1.DT_TIME, t1.valor, t2.NOME_COMPLETO from  tb_pedido_cabecalho t1 left join tb_usuarios t2 on (t1.id_distr=t2.ID) where t1.id_user = " . $id . " order by t1.DT_TIME;";
    $consulta_ped_cab = mysqli_query($GLOBALS['con'],$sql_ped_cab);
    if(mysqli_num_rows($consulta_ped_cab)>0){
        for($i=0;$i<mysqli_num_rows($consulta_ped_cab);$i++){
            $row_ped_cab = mysqli_fetch_row($consulta_ped_cab);       
            $msg_temp=$msg_temp.
                
            '<ul class="collapsible" data-collapsible="accordion">
                <li>
                    <div class="collapsible-header" style="padding: 0px;">
                        <div>
                            <div class="col s4 m4 l4 brown-text brown lighten-4 text-darken-1 truncate left-align">
                                <p>PEDIDO: <b>'.$row_ped_cab[0].'</b></p>
                            </div> 
                            <div class="col s4 m4 l4 brown-text brown lighten-4 text-darken-1 truncate right-align">
                                <p>DATA: <b>'.date("d/m/Y",strtotime($row_ped_cab[1])).'</b></p>
                            </div>
                            <div class="col s4 m4 l4 brown-text brown lighten-4 text-darken-1 truncate center-align">
                                <p><b>INSERIR STATUS DE ENTREGA</b></p>
                            </div>
                            <i class="material-icons" style="color: #8d6e63; position: absolute; margin-top: 35px; margin-left: 6px;">swap_vertical_circle</i>
                        </div>
                    </div>
                    <div class="collapsible-body">
                        <div>
                            <div>
                                <p>Pedido: <b>'.$row_ped_cab[0].'</b></p>
                            </div>
                            <div>
                                <p>Vendedor: <b>'.$row_ped_cab[3].'</b></p>
                            </div>
                            <div>
                                <p>Status do pedido: <b>ATUALIZAR STATUS</b></p>
                            </div>
                            <div>
                                <p>Forma de pagamento: crédito final <b>xxxxx ATUALIZAR</b></p>
                            </div>
                            <div>
                                <p>Total do pedido: <b>R$150,00 ATUALIZAR FRETE</b></p>
                            </div>

                            <table class="bordered centered">
                                <thead>
                                    <tr>
                                        <th>Produto</th>
                                        <th>Descrição</th>
                                        <th>Preço unit</th>
                                        <th>Quantidade</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>';


                $sql_ped_item = "select t1.pedido, t1.valor, t1.qtde, t2.titulo, t2.imagem1 from  tb_pedido_itens t1 left join tb_produto_base t2 on (t1.id_produto=t2.id)   where t1.pedido = ". $row_ped_cab[0] .";";
                $consulta_ped_item = mysqli_query($GLOBALS['con'],$sql_ped_item);
                $valor_pedido=0;
                $qtde_itens=0;
                for($j=0;$j<mysqli_num_rows($consulta_ped_item);$j++){
                    $row_ped_item = mysqli_fetch_row($consulta_ped_item);
                    /*if($j==0){
                    $msg_temp=$msg_temp.'<!--   Frete -->
                        <div class="col l2 s12 collapsible-body" style="border-bottom-color: white">
                            <div class="center"><i class="fa fa-4x fa-truck green-text text-darken-3 ico_anuncio2 center-align" aria-hidden="true"></i></div><br>
                            <div class="center"><p class="center-align txt_pedido_titulo brown-text text-darken-1">FRETE</p></div>
                            <div class="center"><p class="center-align txt_anuncio_qtde grey-text text-darken-3">Rastreio xxxxxxxxxxxx</p></div>
                            <div class="center"><p class="center-align txt_anuncio_qtde grey-text text-darken-3">R$70,00</p></div>
                        </div>';
                        }*/

                    $valor_pedido=$valor_pedido+$row_ped_item[1];
                    $qtde_itens=$qtde_itens+$row_ped_item[2];
                    $valor_unit_format=number_format($row_ped_item[1], 2, ',', '.');
                    $valor_item_format=number_format($row_ped_item[2]*$row_ped_item[1], 2, ',', '.');
                    $msg_temp=$msg_temp.'
                                <tbody>
                                    <tr>
                                        <td>
                                            <img src="'.$row_ped_item[4].'" alt="" class="img_item center" style="width: 100px;	height: 100px;">
                                        </td>
                                        <td>
                                            <p class="left_align truncate">'.utf8_encode($row_ped_item[3]).'</p>
                                        </td>
                                        <td>'.$valor_unit_format.'</td>
                                        <td>'.$row_ped_item[2].'</td>
                                        <td>'.$valor_item_format.'</td>
                                    </tr>
                                </tbody>';



                }    
                $valor_pedido_format=number_format($valor_pedido, 2, ',', '.');
                $msg_temp=$msg_temp.'
                                <tbody>
                                    <tr>
                                        <td>
                                            <i class="fa fa-4x fa-truck green-text ico_anuncio2 center-align" aria-hidden="true"></i>
                                        </td>
                                        <td>
                                            <p class="left_align truncate">Frete</p>
                                        </td>   
                                        <td>50.00</td>
                                        <td>1</td>
                                        <td>50.00</td>
                                    </tr>
                                </tbody>

                                <tbody>
                                    <tr>
                                        <td>
                                            <i class="fa fa-4x fa-shopping-cart brown-text ico_anuncio2 center-align" aria-hidden="true"></i>
                                        </td>
                                        <td>
                                            <p class="left_align truncate">Total</p>
                                        </td>
                                        <td>-</td>
                                        <td>'.$qtde_itens.'</td>
                                        <td>'.$valor_pedido_format.'</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </li>
            </ul>';
        }			
    }
}else{
        $erro = 1;
        $msg_temp = utf8_decode("Usuário não autenticado");
        $opt = "popup";   
    }
$msg = utf8_encode(utf8_decode($msg_temp));
mysqli_close($GLOBALS['con']);
$envia_resposta = array("erro"=>$erro,"msg"=>$msg,"funcao"=>$funcao, "opt"=>$opt);
print json_encode($envia_resposta, JSON_PRETTY_PRINT);
?>