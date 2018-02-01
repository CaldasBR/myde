<?php
    ini_set('display_errors','on');
	error_reporting(E_ALL);

	header('Content-Type: text/html; charset=utf-8');
	include("/var/www/mp_desenv/bg_conexao_bd.php");
    include("/var/www/mp_desenv/bg_funcoes_genericas.php");

	//  Configurações do Script
    $funcao = "estoque";
	$erro = 0;
	$msg = "Nada foi executado!";
	$opt = "";
    $opt2 = "";

    //Verifica se usuário está Logado
    include("/var/www/mp_desenv/bg_protege_php.php");
    $status_log = status_login($resposta);

    if($status_log=="mantem"){
        if(isset($_GET["fn"]) || isset($_POST["fn"])){
            if(isset($_GET["fn"])){
                $fn = addslashes($_GET["fn"]);
            }else{
                $fn = addslashes($_POST["fn"]);
            }
            if($_SESSION["user_access"] == 'vendedor' || $_SESSION["user_access"] == 'administrador'){
                switch ($fn){
                    case 'ler_estoque':
                        ler_estoque();
                        $opt = 'mostra_estoque';
                    break;
                    case 'salvar_estoque':
                        salvar_estoque();
                    break;
                }
            }else{
                $erro = 0;
                $msg = utf8_decode("Você não possui autorização para ver essa página.");
                $opt = "expulsa";
            }
        }else{
            $erro = 1;
            $msg = utf8_decode("Informar dados completos.");
        }
    }else{
        $erro = 1;
        $msg = utf8_decode("Usuário não autenticado.");
        $opt = "expulsa";
    }

    function salvar_estoque(){
        global $erro,$msg,$opt,$opt2;
        
        $teste = get_all_get();
        //print_r($teste);
        
        $vet_est_min = [];
        $vet_est_atual = [];
        $vet_valor = [];
        $vet_valor_min = [];
        $vet_select = [];
        $vet_frete = [];
        
        $sql = "select id,valor_minimo from tb_produto_base;";
        //echo "SQL: " . $sql . "<br><br>";
        include("/var/www/mp_desenv/bg_conexao_bd.php");
        $query = mysqli_query($GLOBALS['con'],$sql);
        if(mysqli_num_rows($query)>0){
            for($i=0;$i<mysqli_num_rows($query);$i++){
                $row = mysqli_fetch_row($query);                
                $vet_valor_min[$row[0]] = $row[1];
            }
        }
        
        foreach($teste as $key=>$val){
            if(strpos($key, 'est_min_') !== false){
                $vet_est_min[str_replace('est_min_', '', $key)] = ($val == null) ? 0 : $val;
            }
            if(strpos($key, 'est_atual_') !== false){
                $vet_est_atual[str_replace('est_atual_', '', $key)] = ($val == null) ? 0 : $val;
            }    

            if(strpos($key, 'valor_') !== false){
                $vet_valor[str_replace('valor_', '', $key)] = ($val == null) ? 0 : tofloat($val);
            }

            if(strpos($key, 'select_') !== false){
                if ($val == 'true'){
                    $vet_select[str_replace('select_', '', $key)] = 1;
                }else{
                    $vet_select[str_replace('select_', '', $key)] = 0;
                }
            }
            
            if(strpos($key, 'frete_') !== false){
                if ($val == 'true'){
                    $vet_frete[str_replace('frete_', '', $key)] = 1;
                }else{
                    $vet_frete[str_replace('frete_', '', $key)] = 0;
                }
            }
        }
        
        /*
        echo "vet_valor_min: <br>";
        print_r($vet_valor_min);
        
        echo "<br><br>";
        echo "vet_valor: <br>";
        print_r($vet_valor);
        */
        
        //echo "vet_valor: <br>";
        //print_r($vet_valor);
        
        //echo "vet_valor_min: <br>";
        //print_r($vet_valor_min);
        
        foreach($vet_valor as $key2=>$val2){
            if($vet_valor[$key2]==0){
                $vet_valor[$key2] = $vet_valor_min[$key2];
            }
            if($vet_valor[$key2]<$vet_valor_min[$key2]){
                $erro = 1;
                $msg = utf8_decode("O valor de venda não pode ser menor que o valor mínimo.");
                $opt = "Toast";
            }
        }

        
        if($erro==0){
            foreach($vet_select as $prod_id=>$conteudo){
                if($conteudo==0){
                    $sql = "DELETE from tb_prod_distrib WHERE id_distribuidor=".$_SESSION["user_id"]." and id=".$prod_id.";";
                    include("/var/www/mp_desenv/bg_conexao_bd.php");
                    mysqli_query($GLOBALS['con'],$sql);
                    //echo "SQL1: ". $sql . "<br><br>";
                }else{
                    $sql = "SELECT id from tb_prod_distrib WHERE id_distribuidor=".$_SESSION["user_id"]." and id=".$prod_id.";";
                    include("/var/www/mp_desenv/bg_conexao_bd.php");
                    $existe = mysqli_query($GLOBALS['con'],$sql);

                    if(mysqli_num_rows($existe)>0){
                        $sql2 = "UPDATE tb_prod_distrib SET valor=".$vet_valor[$prod_id]." ,estoque=".$vet_est_atual[$prod_id]." ,estoque_min=".$vet_est_min[$prod_id].", frete=".$vet_frete[$prod_id]." WHERE id_distribuidor=".$_SESSION["user_id"]." and id=".$prod_id.";";
                    }else{
                        $sql2 = "INSERT INTO tb_prod_distrib (id, id_distribuidor,valor,estoque,estoque_min) VALUES(".$prod_id.",".$_SESSION["user_id"].",".$vet_valor[$prod_id].",".$vet_est_atual[$prod_id].",".$vet_est_min[$prod_id].");";
                    }
                    include("/var/www/mp_desenv/bg_conexao_bd.php");
                    mysqli_query($GLOBALS['con'],$sql2);
                    //echo "SQL2: ". $sql2 . "<br><br>";
                }
            }

            //ler_estoque();
            $erro = '0';
            $msg = utf8_decode('Estoque atualizado com sucesso');
            $opt = 'Toast';
            $opt2 = 'refresh';
        }
    }


    function ler_estoque(){
        global $erro,$msg,$opt;
        $sql = "select a.id ,CASE WHEN (b.valor IS NOT NULL) THEN true ELSE false END as vender
                ,a.imagem1,a.titulo,a.valor_minimo,b.valor as valor_venda,b.estoque_min, b.estoque as estoque_atual, b.frete from tb_produto_base a left join tb_prod_distrib b
                on(a.id = b.id and ".$_SESSION["user_id"]."=b.id_distribuidor);";
            
        //echo "SQL: " . $sql . "<br><br>";
        include("/var/www/mp_desenv/bg_conexao_bd.php");
        $query = mysqli_query($GLOBALS['con'],$sql);

        $msg = "";
        if(mysqli_num_rows($query)>0){
            $erro = 0;
            for($i=0;$i<mysqli_num_rows($query);$i++){
                $row = mysqli_fetch_row($query);

                //echo "conteudo do vender: " . $row[1] . "<br><br>";

                //<input type="checkbox" id="filled-in-box" checked="checked" />
                
                $msg = $msg . '
                <tr>
                    <td>
                        <p class="center-align">
                            <input type="checkbox" id="select_'.$row[0].'"';

                if($row[1]==1){
                    $msg = $msg . ' checked="checked"';
                }

                $msg = $msg . '
                            />
                            <label for="select_'.$row[0].'"></label>
                        </p>
                    </td>
                    <td>
                        <img src="'.$row[2].'" alt="" class="img_item center" style="width: 100px;	height: 100px;">
                    </td>
                    <td>
                        <p class="left_align truncate">'.$row[3].'</p>
                    </td> <!-- Limitar caracteres para truncar -->   
                    <td>R$ '.number_format($row[4],2,",",".").'</td>
                    <td>
                        <div class="container input">
                            <input type="text" id="valor_'.$row[0].'" value="'.number_format($row[5],2,",",".").'" style="text-align: center;" />
                        </div>
                    </td>
                    <td>
                        <div class="container input">
                            <input type="text" id="est_min_'.$row[0].'" value="'.$row[6].'" style="text-align: center;"/>
                        </div>
                    </td>
                    <td>
                        <div class="container input">
                            <input type="text" id="est_atual_'.$row[0].'" value="'.$row[7].'" style="text-align: center;"/>
                        </div>
                    </td>
                    <td>';

                    //echo "atual: ".$row[7]."<br>";
                    //echo "minimo: ".$row[6]."<br>";
                    if($row[7]<$row[6]){
                        $msg = $msg . '
                            <p>Abaixo</p>
                            <img src="media/imagens/exclamation-icon.png" alt="" class="img_item center" style="width: 20px; height: 20px;">';
                    }else{
                        $msg = $msg . '
                            <p>Adequado</p>
                            <i class="material-icons green-text">done_all</i>
                        ';
                    }
                
                    $msg = $msg . '                            
                        </td>
                        <td>
                            <p class="center-align">
                                <input type="checkbox" id="frete_'.$row[0].'"';

                    if($row[8]==1){
                        $msg = $msg . ' checked="checked"';
                    }
                        $msg = $msg . '
                                />
                                <label for="frete_'.$row[0].'"></label>
                            </p>
                        </td>
                    </tr>';
            }
        }else{
            $erro=1;
            $msg = utf8_decode("Não encontrei nenhum produto no banco de dados.");
            $opt = "Toast";
        }
    }

    $msg = utf8_encode($msg);
	$envia_resposta = array("erro"=>$erro,"msg"=>$msg,"funcao"=>$funcao,"opt"=>$opt,"opt2"=>$opt2);
	print json_encode($envia_resposta);
?>