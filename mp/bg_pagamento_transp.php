<?php

    ini_set('display_errors','on');
	error_reporting(E_ALL);

    header('Content-Type: text/html; charset=utf-8');
    include("/var/www/mp/bg_funcoes_genericas.php");

	//  Configurações do Script
    $funcao = "pagamento_loja";
	$erro = 0;
	$msg = "Nada foi executado!";
	$opt = "";

    //Verifica se usuário está Logado
    include("/var/www/mp/bg_protege_php.php");
    $status_log = status_login($resposta);

    if($status_log=="mantem"){
        if(isset($_GET["fn"]) || isset($_POST["fn"])){
            if(isset($_GET["fn"])){
                $fn = addslashes($_GET["fn"]);
            }else{
                $fn = addslashes($_POST["fn"]);
            }
            
            switch ($fn){
                case 'pagar_distrib':
                    criar_pedido_pgto();
                break;
            }
        }else{
            $erro = 1;
            $msg = "Informar dados completos.";
        }
    }else{
        $erro = 1;
        $msg = "Usuário não autenticado.";
        $opt = "expulsa";
    }

    function criar_pedido_pgto(){
        $sql = "
            select
                a.id
                ,c.titulo
                ,c.descricao_titulo
                ,c.imagem1
                ,c.A_cm
                ,c.L_cm
                ,c.C_cm
                ,b.QTDE
                ,a.valor
                ,d.nome
                ,d.SOBRENOME
                ,d.NOME_COMPLETO
                ,d.cpf
                ,d.email
                ,d.cel
                ,e.dimensoes
                ,d.cep
                ,e.pedido as id_pedido
            from
                tb_prod_distrib a
            inner join
                tb_pedido_itens b
            on (a.id_distribuidor=b.id_distr
                and a.id=b.ID_PRODUTO
                and ".$_SESSION["user_id"]."=b.ID_USER)
            inner join
                tb_produto_base c
            on (a.id = c.id)
            inner join
                tb_usuarios d
            on (b.ID_USER = d.ID) 
            inner join tb_pedido_cabecalho e 
            on (e.pedido=b.pedido)
            where e.dt_time=(select max(dt_time) from tb_pedido_cabecalho where id_user=".$_SESSION["user_id"]." and id_distr=".$_SESSION["user_id_distribuidor"].");";
        
        include("/var/www/mp/bg_conexao_bd.php");
        $query = mysqli_query($GLOBALS['con'],$sql);
        
        if(mysqli_num_rows($query)>0){
            $preference_data = [];
            $preference_data["items"] = [];
            $preference_data["payer"] = [];
            $preference_data["shipments"] = [];
            $preference_data["back_urls"] = array(
                "success" => "https://queromarita.com.br/pedidos.html",//Approved payment URL
                "pending" => "https://queromarita.com.br/pedidos.html",//Pending payment URL 
                "failure" => "https://queromarita.com.br/pedidos.html"//Canceled payment URL 
            );
            $preference_data["notification_url"] = "https://queromarita.com.br/bg_answer_mercpago.php";
            $preference_data["marketplace"] = "https://queromarita.com.br";
            $preference_data["auto_return"] = "all";
            
            for($i=0;$i<mysqli_num_rows($query);$i++){
                $row = mysqli_fetch_row($query);
                $item = array(
                    "title" => utf8_encode($row[1]),
                    "description" => utf8_encode($row[2]),
                    "category_id"=> "fashion",
                    "picture_url"=> "https://queromarita.com.br/" . $row[3],
                    "quantity" => intval($row[7]),
                    "unit_price" => floatval($row[8]),
                    "currency_id" => "BRL"
                );
                array_push($preference_data["items"],$item);
            }
            
            //echo "id pgto queromarita: " . $row[17] . "<br>";
            $preference_data["external_reference"] = $row[17];
            
            $preference_data["payer"] = array(
                "name"=>$row[9],
                "surname"=>$row[10],
                "identification"=> array(
                    "type"=>"cpf",
                    "number"=>$row[12]
                ),
                "email"=>$row[13]
            );
            
            $preference_data["shipments"] = array(
                "mode" => "me2", // custom, me2
                "local_pickup" => true,// true,false
                "dimensions" => $row[15],
                "default_shipping_method"=> 182, //100009(normal), 182(expresso)
                //"free_methods" => array( //UTILIZAR NO CASO DE FRETE GRATUITO
                    //"id"=> 182,//100009(normal), 182(expresso)
                //),
                //"free_shipping" => //true, false
                "receiver_address" => array(
                    "zip_code" => $row[16]
                    //street_name => "Rua Fonte Boa",
                    //street_number => "398",
                    //floor => "",
                    //apartment => ""
                )
            );
            
            //Atualiza o status na tabela tb_pedido_cabecalho informando que deu início ao pagamento junto ao MercadoPago
            $sql = "UPDATE tb_pedido_cabecalho tb_pedido_cabecalho status_pgto='Solicitado MP' where pedido = ".$row[17].";";
            include("/var/www/mp/bg_conexao_bd.php");
            $query = mysqli_query($GLOBALS['con'],$sql);
            
            //Busca o token de pagamento do Distribuidor
            $sql = "SELECT access_token, refresh_token from tb_auth_mercadopago where user_id_myde=".$_SESSION['user_id_distribuidor']." and dt_time=(select max(dt_time) as dt_time from tb_auth_mercadopago where user_id_myde=".$_SESSION['user_id_distribuidor'].");";
            include("/var/www/mp/bg_conexao_bd.php");
            $query = mysqli_query($GLOBALS['con'],$sql);
            $row = mysqli_fetch_row($query);
            
            $token_MP = $row[0];
            
            //var_dump($preference_data);
            require_once ('sdk-mercadopago/lib/mercadopago.php');
            //$mp = new MP('7612629650074174', 'V13ESd4LPXGp9I1PfDioLWg5PAzHzSAo');
            //$mp = new MP('APP_USR-7612629650074174-051222-fe80bfa58e59ca7a4156dd517e63d675__LA_LB__-249839521');
            $mp = new MP($row[0]);
            $preference = $mp->create_preference($preference_data);
            $redir = $preference['response']['init_point'];
            //$redir = $preference['response']['sandbox_init_point'];
            //var_dump($preference);
            //echo "vai para: " . $redir . "<br>"; 
            header('location: ' . $redir);
        }  
    }

	$envia_resposta = array("erro"=>$erro,"msg"=>$msg,"funcao"=>$funcao,"opt"=>$opt);
	print json_encode($envia_resposta);
?>