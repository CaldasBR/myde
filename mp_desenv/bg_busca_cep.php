<?php
    //ini_set('display_errors','on');
	//error_reporting(E_ALL);

    session_start();
	header('Content-Type: text/html; charset=utf-8');
	include("/var/www/mp_desenv/bg_conexao_bd.php");

    $funcao = "pagamento";
    $erro = 0;
    $msg = "Nada foi executado!";
    $opt = "";

    //Verifica se usuário está Logado
    include("/var/www/mp_desenv/bg_protege_php.php");
    $status_log = status_login($resposta); //acredite que $resposta existe, ele está no bg_protege_php
    //echo "status_log: ".$status_log."<br>";
    
    //Se estiver logado executa o código
    if($status_log=="mantem"){
        if(isset($_GET["fn"])){
            $fn = addslashes($_GET["fn"]);
            $funcao = $fn;
            switch ($fn){
                case 'busca_cep':
                    if(isset($_GET["cep"])){
                        $cep = addslashes($_GET["cep"]);
                        //echo "Processo para buscar quem é o cliente logado no sistema. <br>";
                        $sql = "select t1.UF,t2.nome as estado,t1.cidade_nome,t1.bairro_nome,t1.logracompl from map_enderecos t1 left join map_estados t2 on (t1.uf = t2.uf) where cep='" . $cep . "';";
                        //echo "SQL: ".$sql."<br>";
                        $query = mysqli_query($GLOBALS['con'],$sql);
                        if(mysqli_num_rows($query)>0){
                            $row = mysqli_fetch_row($query);
                            //echo "conteudo row:<br>";
                            //var_dump($row);
                            $erro = 0;
                            $msg = array(
                                    "uf" => utf8_encode($row[0])
                                    ,"estado" => utf8_encode($row[1])
                                    ,"cidade" => utf8_encode($row[2])
                                    ,"bairro" => utf8_encode($row[3])
                                    ,"logradouro" => utf8_encode($row[4])
                            );
                            //echo "conteudo msg:<br>";
                            //var_dump($msg);
                            $opt = "atualiza_endereco_formulario";
                        }else{
                            $erro = 0;
                            $msg = "Cep não localizado.";
                            $opt = "limpa_endereco_formulario";
                        }
                    }else{
                        $erro = 1;
                        $msg = "Informar dados completos.";
                    }
                    break;
            }
        }
    }else{
        $erro = 1;
        $msg = "Usuário não autenticado.";
        $opt = "expulsa";
    }

    $envia_resposta = array("erro"=>$erro,"msg"=>$msg,"funcao"=>$funcao,"opt"=>$opt);
    //echo "conteudo envia_resposta:<br>";
    //var_dump($envia_resposta);
    print json_encode($envia_resposta, JSON_PRETTY_PRINT);
?>