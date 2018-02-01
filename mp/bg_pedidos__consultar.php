
<?php
//ini_set('display_errors','on');
//error_reporting(E_ALL);

header('Content-Type: text/html; charset=utf-8');
session_start();
include("/var/www/mp/bg_conexao_bd.php");

$msg_temp = "";
$funcao = "pedidos";
$opt = "pedidos__consultar";
$erro = 0;

//Verifica se os campos que são obrigatórios estão preenchidos

$id=$_SESSION["user_id"];
include("/var/www/mp/bg_protege_php.php");
$status_log = status_login($resposta); //acredite que $resposta existe, ele está no bg_protege_php
//echo "status_log: ".$status_log."<br>";

//Se estiver logado executa o código
if($status_log=="mantem"){
    if($_SESSION["user_access"]=="vendedor" || $_SESSION["user_access"]=="administrador"){
        $sql_ped_cab = "select t1.pedido, t1.DT_TIME, t1.valor, t2.NOME_COMPLETO, t3.status, t3.shipping_cost, t3.total_paid_amount, t3.transaction_amount, t3.payment_type, t4.shipment_type, t2.email, t2.cel from  tb_pedido_cabecalho t1 left join tb_usuarios t2 on (t1.id_user=t2.ID) inner join tb_pagamento_loja t3 on (t1.pedido=t3.order_id)  left join tb_pagamento_orders t4 on (t3.external_reference=t4.external_reference) where t1.id_distr = " . $id . " order by t1.DT_TIME;";
        $txt_ajust_user="Comprador";
    }else{
        $sql_ped_cab = "select t1.pedido, t1.DT_TIME, t1.valor, t2.NOME_COMPLETO, t3.status, t3.shipping_cost, t3.total_paid_amount, t3.transaction_amount, t3.payment_type, t4.shipment_type from  tb_pedido_cabecalho t1 left join tb_usuarios t2 on (t1.id_distr=t2.ID) inner join tb_pagamento_loja t3 on (t1.pedido=t3.order_id) left join tb_pagamento_orders t4 on (t3.external_reference=t4.external_reference) where t1.id_user = " . $id . " order by t1.DT_TIME;";
        $txt_ajust_user="Vendedor";
    }
    $consulta_ped_cab = mysqli_query($GLOBALS['con'],$sql_ped_cab);
    if(mysqli_num_rows($consulta_ped_cab)>0){
        for($i=0;$i<mysqli_num_rows($consulta_ped_cab);$i++){
            $row_ped_cab = mysqli_fetch_row($consulta_ped_cab);
            $valor_frete = $row_ped_cab[5];
            $valor_total= $row_ped_cab[6];
            $valor_produtos= $row_ped_cab[7];
            if($_SESSION["user_access"]=="vendedor" || $_SESSION["user_access"]=="administrador"){
                $tp_entrega=$row_ped_cab[9];
                if ($tp_entrega=='shipping') {
                    $txt_frete='Frete: <b>Imprimir Etiqueta de Envio</b>';
                }else {
                    $txt_frete='Frete: <b>Combinar entrega com comprador</b>';
                }
            }
//ajuste de status
            switch ($row_ped_cab[4]) {
                case "pending":
                    $status_Ped= "AGUARD. CONFIRM. DE PAGAMENTO";
                    break;
                case "approved":
                    $status_Ped= "PAGAMENTO CONFIRMADO";
                    break;
                case "in_process":
                    $status_Ped= "AGUARD. CONFIRM. DE PAGAMENTO";
                    break;
                case "in_mediation":
                    $status_Ped= "EM MEDIAÇÃO";
                    break;
                case "rejected":
                    $status_Ped= "AGUARD. FORMA DE PAGAMENTO";
                    break;
                case "cancelled":
                    $status_Ped= "PEDIDO CANCELADO";
                    break;
                case "refunded":
                    $status_Ped= "PAGAMENTO DEVOLVIDO";
                    break;
                case "charged_back":
                    $status_Ped= "PAGAMENTO DEVOLVIDO";
                    break;
            }
            switch ($row_ped_cab[8]) {
                case "ticket":
                    $forma_pgto= "BOLETO";
                    break;
                case "credit_card":
                    $forma_pgto= "CARTÃO DE CRÉDITO";
                    break;
            }
            $valor_frete_format=number_format($valor_frete,2,',','.');
            $valor_total_format=number_format($valor_total,2,',','.');
            //Msgm retorno
            $msg_temp=$msg_temp.

            '<ul class="collapsible" data-collapsible="accordion">
                <li>
                    <div class="collapsible-header" style="padding: 0px;">
                        <div>
                            <div class="col s4 m4 l4 brown-text brown lighten-4 text-darken-1 truncate left-align">
                                <i class="material-icons" style="color: #8d6e63; margin-right: 10px; margin-top:15px;">swap_vertical_circle</i>
                                <p>PEDIDO: <b>'.$row_ped_cab[0].'</b></p>
                            </div>
                            <div class="col s4 m4 l4 brown-text brown lighten-4 text-darken-1 truncate right-align">
                                <p>DATA: <b>'.date("d/m/Y",strtotime($row_ped_cab[1])).'</b></p>
                            </div>
                            <div class="col s4 m4 l4 brown-text brown lighten-4 text-darken-1 truncate center-align">
                                <p><b>'.$status_Ped.'</b></p>
                            </div>
                        </div>
                    </div>
                    <div class="collapsible-body">
                        <div>
                            <div>
                                <p>Pedido: <b>'.$row_ped_cab[0].'</b></p>
                            </div>';
                            if($_SESSION["user_access"]=="vendedor" || $_SESSION["user_access"]=="administrador"){
                                $msg_temp=$msg_temp.'
                                <div>
                                    <p>'.$txt_ajust_user.': <b>'.$row_ped_cab[3].' - '.$row_ped_cab[10].' - '.$row_ped_cab[11].'</b></p>
                                </div>';
                            }else{
                                $msg_temp=$msg_temp.'
                                <div>
                                    <p>'.$txt_ajust_user.': <b>'.$row_ped_cab[3].'</b></p>
                                </div>';
                            }
                            if($_SESSION["user_access"]=="vendedor" || $_SESSION["user_access"]=="administrador")
                            {
                                $msg_temp=$msg_temp.
                                '<div>
                                    <p>'.$txt_frete.'</p>
                                </div>';
                            }
                            $msg_temp=$msg_temp.
                            '<div>
                                <p>Status do pedido: <b>'.$status_Ped.'</b></p>
                            </div>
                            <div>
                                <p>Forma de pagamento: <b>'.$forma_pgto.'</b></p>
                            </div>
                            <div>
                                <p>Total do pedido: <b>R$'.$valor_total_format.'</b></p>
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
                    $valor_unit_format=number_format($row_ped_item[1]/$row_ped_item[2], 2, ',', '.');
                    $valor_item_format=number_format($row_ped_item[1], 2, ',', '.');
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
                if ($tp_entrega=='shipping') {
                    $msg_temp=$msg_temp.'
                        <tbody>
                            <tr>
                                <td>
                                    <i class="fa fa-4x fa-truck green-text ico_anuncio2 center-align" aria-hidden="true"></i>
                                </td>
                                <td>
                                    <p class="left_align truncate">Frete</p>
                                </td>
                                <td>'.$valor_frete_format.'</td>
                                <td>1</td>
                                <td>'.$valor_frete_format.'</td>
                            </tr>
                        </tbody>';
                }
                $msg_temp=$msg_temp.'
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
                                        <td>'.$valor_total_format.'</td>
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
