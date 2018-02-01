<?php   
//    ini_set('display_errors','on');
//    error_reporting(E_ALL);
    session_start();
    include("/var/www/mp/bg_conexao_bd.php");
    include("/var/www/mp/bg_protege_php.php");
    header('Content-Type: text/html; charset=utf-8');
    include("/var/www/mp/bg_funcoes_genericas.php");
    $status_log = status_login($resposta);
    $erro = 0;
    $msg = "Nada foi Executado";
    if($status_log=="mantem"){
        require_once ('sdk-mercadopago/lib/mercadopago.php');
        $USER_ID_DISTRIBUIDOR=$_SESSION["user_id_distribuidor"];
        $sql_consulta="select resp_frete from tb_frete where id_distribuidor='".$_SESSION["user_id_distribuidor"]."';";
        $consulta = mysqli_query($GLOBALS['con'],$sql_consulta);
        $dados = mysqli_fetch_row($consulta);
        $tipo_frete=$dados[0];
        $sql_consulta1="select access_token from tb_auth_mercadopago where user_id_myde=".$_SESSION["user_id_distribuidor"]." and dt_time=(select max(dt_time) from tb_auth_mercadopago where user_id_myde=".$_SESSION["user_id_distribuidor"].");";
        $consulta1 = mysqli_query($GLOBALS['con'],$sql_consulta1);        
        $dados1 = mysqli_fetch_row($consulta1);
        $token=$dados1[0];
        switch ($_GET["fn"]) {
            case "produto_detalhe":
                $funcao = "produto_detalhe";
                $opt = "produto_detalhe__calcular_frete";
                $cep=$_GET["cep"];
                $qtde=$_GET["qtde"];
                $id=$_GET["id"];
                $sql_consulta2="select t2.A_cm, t2.L_cm, t2.C_cm, t2.peso_grs, t1.valor from tb_prod_distrib t1 left join tb_produto_base t2 on (t1.id=t2.id) where t1.id='".$id."' and t1.id_distribuidor='".$USER_ID_DISTRIBUIDOR."';";
                $consulta2 = mysqli_query($GLOBALS['con'],$sql_consulta2);
                $dados2 = mysqli_fetch_row($consulta2);   
                $dimensao=ceil(pow(($dados2[0]*$dados2[1]*$dados2[2]),(1/3)));
                $peso=$dados2[3];
                $valor=$dados2[4];
                $dimensao_peso=$dimensao."x".$dimensao."x".$dimensao.",".$peso;
                $erro=0;            
            break;
            case "sacola":
                $funcao = "sacola";
                $opt = "sacola__calcular_frete";
                $cep=$_GET["cep"];
                $teste = get_all_get();
                $vet_id = [];
                $vet_id_qtde = [];
                foreach($teste as $key=>$val){
                    if(strpos($key, 'qtde_') !== false){
                        array_push($vet_id,intval(str_replace('qtde_', '', $key)));
                        $vet_id_qtde[str_replace('qtde_', '', $key)] = ($val == null) ? 0 : intval($val);
                    }
                }
                $ids = join(",",$vet_id); 
                $sql="select t1.id, t2.A_cm, t2.L_cm, t2.C_cm, t2.peso_grs, t1.valor from tb_prod_distrib t1 left join tb_produto_base t2 on (t1.id=t2.id) where t1.id in (".$ids.") and t1.id_distribuidor=".$_SESSION["user_id_distribuidor"].";";
                $consulta = mysqli_query($GLOBALS['con'],$sql);
                if(mysqli_num_rows($consulta)>0){
                    $peso_final = 0;
                    $cubagem = 0;
                    $valor=0;
                    for($i=0;$i<mysqli_num_rows($consulta);$i++){
                        $row = mysqli_fetch_row($consulta);                
                        $peso_final = $peso_final + ($row[4] * $vet_id_qtde[strval($row[0])]);
                        $cubagem = $cubagem + ($row[1]*$row[2]*$row[3])* $vet_id_qtde[strval($row[0])];
                        $valor = $valor + ($row[5] * $vet_id_qtde[strval($row[0])]);
                    }
                    $lado = ceil(pow($cubagem,(1/3)));
                    $dimensao_peso=$lado."x".$lado."x".$lado.",".$peso_final;
                }
                $erro=0;
            break;
        }
        $frete = calcular_frete($token, $cep, $dimensao_peso, $valor, $tipo_frete);
        $msg="";
        foreach($frete as $shipping_option) {
                $value = $shipping_option['shipping_method_id'];
                $name = $shipping_option['name'];
                $checked = $shipping_option['display'] == "recommended" ? "checked='checked'" : "";
                $shipping_speed = $shipping_option['estimated_delivery_time']['shipping'];
                $estimated_delivery = $shipping_speed < 24 ? 1 : ceil($shipping_speed / 24); //from departure, estimated delivery time
                $cost = $shipping_option['cost'];
                $msg=$cost;
        }
    }else{
            $erro=1;
            $msg="Fazer Cadastro.";
    }

function calcular_frete($token, $cep, $dimensao_peso, $valor, $tipo_frete){
            //    $GLOBALS['mp'] = new MP('APP_USR-7612629650074174-050422-b04a89099a51578583e8965efd6827f1__LB_LC__-249839521'); //chave do vendendor
            $mp = new MP($token); //chave do vendendor  
            if($tipo_frete==1){
                    $params = array(
                    "dimensions" => $dimensao_peso, //valores dos produtos somados
                    "zip_code" => $cep,       //cep destino
                    "item_price"=> $valor,          //valor somado
                    "free_method" => "100009"   // "free_method" => "100009"
                    );
            }else{
                    $params = array(
                    "dimensions" => $dimensao_peso, //valores dos produtos somados
                    "zip_code" => $cep,       //cep destino
                    "item_price"=> $valor          //valor somado
                    );        
            }
            $response = $mp->get("/shipping_options", $params);
            $shipping_options = $response['response']['options']; 


            return $shipping_options;
    }
    

    $envia_resposta = array("erro"=>$erro, "msg"=>$msg, "funcao"=>$funcao, "opt"=>$opt);
    print json_encode($envia_resposta, JSON_PRETTY_PRINT);
?>