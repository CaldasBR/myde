<?php
    //ini_set('display_errors','on');
	//error_reporting(E_ALL);

	header('Content-Type: text/html; charset=utf-8');

    //Verifica se usuário está Logado
    include("/var/www/mp/bg_protege_php.php");
    $status_log = status_login($resposta);

    include("/var/www/mp/bg_funcoes_genericas.php");

    $funcao = "customer";
    $erro = 0;
    $msg = "Nada foi executado!";
    $opt = "";

    
    //Se estiver logado executa o código
    if($status_log=="mantem"){
        if(isset($_GET["fn"]) || isset($_POST["fn"])){
            if(isset($_GET["fn"])){
                $fn = addslashes($_GET["fn"]);
            }else{
                $fn = addslashes($_POST["fn"]);
            }
            
            require_once ('sdk-mercadopago/lib/mercadopago.php');
            $mp = new MP ("APP_USR-7612629650074174-050422-b04a89099a51578583e8965efd6827f1__LB_LC__-249839521");
            
            switch ($fn){
                case 'salva_customer':
                    $user_id = $_SESSION['user_id'];
                    $email = $_SESSION['user_email'];
                    $first_name = addslashes($_POST["first_name"]);
                    $last_name = addslashes($_POST["last_name"]);
                    $phone = addslashes($_POST["phone"]);
                    $cpf = addslashes($_POST["cpf"]);
                    $zip_code = addslashes($_POST["zip_code"]);
                    $street_name = addslashes($_POST["street_name"]);
                    $street_number = addslashes($_POST["street_number"]);

                    $street_compl = addslashes($_POST["street_compl"]);
                    $pais = addslashes($_POST["pais"]);
                    $uf = addslashes($_POST["uf"]);
                    $cidade = addslashes($_POST["cidade"]);
                    $bairro = addslashes($_POST["bairro"]);

                    $area_code = substr($phone,1,2);
                    $cel_number = substr($phone,5,10);

                    $sql = "INSERT INTO tb_customers (id_user,email,first_name,last_name,cel,area_code,cel_number,cpf,cep,street_name,street_number,street_compl,pais,uf,cidade,bairro) VALUES (".$user_id.",'".$email."','".$first_name."','".$last_name."','".$phone."','".$area_code."','".$cel_number."','".$cpf."','".$zip_code."','".$street_name."','".$street_number."','".$street_compl."','".$pais."','".$uf."','".$cidade."','".$bairro."');";
                    
                    //echo "SQL: " . $sql;
                    
                    include("/var/www/mp/bg_conexao_bd.php");
                    $query = mysqli_query($GLOBALS['con'],$sql);
                    
                    $sql1 = "UPDATE tb_usuarios SET end_cobranca='".$street_name."',cep=".(int)preg_replace( '/[^0-9]/', '', $zip_code ).", num_imovel='".$street_number."', complemento='".$street_compl."',pais='".$pais."', uf='".$uf."', cidade='".$cidade."', bairro='".$bairro."' where id=".$user_id.";";
                    //echo "sql1: ".$sql1;
                    include("/var/www/mp/bg_conexao_bd.php");
                    $query1 = mysqli_query($GLOBALS['con'],$sql1);
                    
                    $sql2 = "Select id_user,email from tb_customers where id_user=".$user_id.";";
                    //echo "Sql: " . $sql2 . "<br>";
                    
                    $query2 = mysqli_query($GLOBALS['con'],$sql2);
                    if(mysqli_num_rows($query2)>0){
                        $row = mysqli_fetch_row();
                        $erro = 0;
                        $msg = "Dados devidamente salvos!";
                        $opt = "efetuar_pagamento";
                    }else{
                        $erro = 1;
                        $msg = "Recebi os dados e não consegui salvar no BD.";
                        $opt = "Toast";
                    }
                    
                    //echo "esta na tebela? ";
                    //var_dump($row);
                    //echo "MSG: " . $msg . "<br>";
                    
                    $customer = array(
                        'email' => $email
                        ,'first_name' => cleanString($first_name)
                        ,'last_name' => cleanString($last_name)
                        ,'phone' => array(
                            'area_code' => $area_code
                            ,'number' => $cel_number
                        )
                        ,'identification' => array(
                            'type' => 'cpf'
                            ,'number'=>$cpf
                        )
                        ,'address' => array(
                            'zip_code'=>$zip_code
                            ,'street_name'=> cleanString($street_name)
                            ,'street_number'=>(int)preg_replace('/[^0-9]/', '', $street_number)
                        )
                    );
                    
                    $resposta = create_customer($email,$customer);
                    //echo "Respostas temp valid: " . $msg . "<br>";
                    //var_dump($resposta);
                    if($resposta=='atualizado' || $resposta=='criado'){
                        $sql = "SELECT id from tb_card_tokens where id_user=".$_SESSION['user_id']." and date_last_updated = (select max(date_last_updated) as date_last_updated from tb_card_tokens where id_user=".$_SESSION['user_id'].");";
                        
                        include("/var/www/mp/bg_conexao_bd.php");
                        $query = mysqli_query($GLOBALS['con'],$sql);
                        
                        //echo "SQL: ".$sql."<br>" ;
                        if(mysqli_num_rows($query2)>0){
                            $row = mysqli_fetch_row($query);
                            adiciona_cartao_customer($email,$row[0]);    
                            //echo $resposta;
                            $erro = 0;
                            $msg = "Os dados do consumidor foram salvos ou altualizados com sucesso.";
                            $opt = "efetuar_pagamento";
                        }else{
                            $erro = 1;
                            $msg = "Entrar em contato com suporte informando erro: 'FAIL-CARD-CUSTOMER '";
                            $opt = "Toast";
                        }
                    }else{
                            $erro = 1;
                            $msg = "Entrar em contato com suporte informando erro: 'FAIL-CARD-CUSTOMER2 '";
                            $opt = "Toast";
                    }
                break;
            }
        }
    }else{
        $erro = 1;
        $msg = "Usuário não autenticado.";
        $opt = "expulsa";
    }

    function create_customer($email,$customer){
        //echo "<br>entrou no create customer <br>";
        
        global $erro,$msg,$opt;
        
        $filters = array (
            "email" => $email
        );
        
        $mp = new MP ("APP_USR-7612629650074174-050422-b04a89099a51578583e8965efd6827f1__LB_LC__-249839521");
        
        $customer_saved = $mp->get ("/v1/customers/search", $filters);
        if($customer_saved['status'] == 200 && $customer_saved['response']['results'][0]['email'] == $email){
            //echo "update customer. <br>";
            $customer_id = $customer_saved["response"]["results"][0]["id"];
            $customer = array_diff($customer, array('email'));
            unset($customer[0]);
            array_splice($customer, 0, 1);
            //var_dump($customer);
            //echo "Customer ID: " . $customer_id;
            $customer_update = $mp->PUT ("/v1/customers/".$customer_id,$customer);
            
            //echo "resultado do update. <br>";
            //var_dump($customer_update);
            return "atualizado";
        }else{
            //echo "create customer. <br>";
            //echo "como esta o mp. <br>";
            //var_dump($mp);
            
            //echo "conteudo da variavel customer <br>";
            //var_dump($customer);
            
            //$customer_create = $mp->post("/v1/customers", $customer);
            $customer_create = $mp->post ("/v1/customers", array("email" => $email));
            create_customer($email,$customer);
            
            //echo "resultado do create customer <br>";
            //var_dump($customer_create);
            return "criado";
        }
        
    }



    function adiciona_cartao_customer($email,$token_cartao){
        global $erro,$msg,$opt;
        $customer = array (
            "email" => $email
        );
        
        //echo "email: <br>";
        //echo $email . "<br>";
        
        //echo "token_cartao: <br>";
        //echo $token_cartao . "<br>";
        
        //echo "array da pesquisa <br>";
        //var_dump($customer);
        
        $mp = new MP ("APP_USR-7612629650074174-050422-b04a89099a51578583e8965efd6827f1__LB_LC__-249839521");
        
        $saved_customer = $mp->get ("/v1/customers/search", $customer);
        $customer_id = $saved_customer["response"]["results"][0]["id"];
        
        //echo "array do customer <br>";
        //var_dump($saved_customer);
        
        $card = $mp->post ("/v1/customers/".$customer_id."/cards", array("token"=>$token_cartao));
        
        //echo "array do cartao a ser adicionado: <br>";
        //var_dump($card);
        
        $saved_customer = $GLOBALS['mp']->get ("/v1/customers/search", $customer);
        //echo "Cliente salvo: <br>";
        //var_dump($saved_customer);        
    }

    $envia_resposta = array("erro"=>$erro,"msg"=>$msg,"funcao"=>$funcao,"opt"=>$opt);
    print json_encode($envia_resposta, JSON_PRETTY_PRINT);
?>