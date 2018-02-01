<?php
    //ini_set('display_errors','on');
	//error_reporting(E_ALL);

    session_start();
	header('Content-Type: text/html; charset=utf-8');
	include("/var/www/mp/bg_conexao_bd.php");
    include("/var/www/mp/bg_funcoes_genericas.php");
    include("/var/www/mp/bg_cookie.php");

	//  Configurações do Script
    $funcao = "login_myde";
	$erro = 0;
	$msg = "Nada foi executado!";
	$opt = "";
    $id_distr_cad = 0;

    if(isset($_POST["fn"])){
        $fn = addslashes($_POST["fn"]);
        //echo("FN: ".$fn."<br>");
        switch ($fn){
            case 'logar':
                //echo "entrei no logar <br>";
                $camposOK=true;
                if($camposOK==true && $erro==0){
                    if(filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)){
                        $email = addslashes(strtolower($_POST["email"]));
                    }else{
                        $msg = $msg."O email parece incorreto. ";
                        $erro = 1;
                        $opt = "Toast";
                    }

                    if(isset($_POST["senha"])){
                        if(strlen($_POST["senha"]) <= 7){
                            $erro = 1;
                            $msg = $msg."A senha não está completa. ";
                            $opt = "Toast";
                        }else{
                            $senha = addslashes($_POST["senha"]);
                        }
                    }
                }
                validaemail($email, $senha);
                break;
            case 'logar_aderir':
                //echo "entrei no logar <br>";
                $camposOK=true;
                if($camposOK==true && $erro==0){
                    if(filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)){
                        $email = addslashes(strtolower($_POST["email"]));
                    }else{
                        $msg = $msg."O email parece incorreto. ";
                        $erro = 1;
                        $opt = "Toast";
                    }

                    if(isset($_POST["senha"])){
                        if(strlen($_POST["senha"]) <= 7){
                            $erro = 1;
                            $msg = $msg."A senha não está completa. ";
                            $opt = "Toast";
                        }else{
                            $senha = addslashes($_POST["senha"]);
                        }
                    }
                }
                validaemail($email, $senha);
                break;
            case 'cadastrar':
                $camposOK=true;
                //echo "entrei no cadastrar <br>";
                //Caso os campos obrigatórios estejam preenchidos faz mais algumas validações (email, cpf, nome, telefone)

                if($camposOK==true && $erro==0){

                    if(filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)){
                        $email = addslashes(strtolower($_POST["email"]));
                    }else{
                        $msg = $msg."O email parece incorreto. ";
                        $erro = 1;
                        $opt = "Toast";
                    }

                    if(isset($_POST["cpf"])){
                        $cpf = preg_replace( '/[^0-9]/', '', $_POST["cpf"] );
                        // Verifica se o CPF é válido
                        if(valida_cpf($cpf)==false){
                            $erro = 1;
                            $msg = $msg."O CPF não parece válido. ";
                            $opt = "Toast";
                        }else{
                            $cpf = addslashes($_POST["cpf"]);
                        }
                    }else{
                        $erro = 1;
                        $msg = $msg."O CPF não está preenchido. ";
                        $opt = "Toast";
                    }

                    if(isset($_POST["nome"]) && isset($_POST["sobrenome"])){
                        if(strlen($_POST["nome"]) <= 1 && strlen($_POST["sobrenome"]) <= 1){
                            $erro = 1;
                            $msg = $msg."O nome não está completo. ";
                            $opt = "Toast";
                        }else{
                            $nome = addslashes($_POST["nome"]);
                            $sobrenome = addslashes($_POST["sobrenome"]);
                            $nome_completo = $nome . " " . $sobrenome;
                        }
                    }

                    if(isset($_POST["celular"])){
                        if(strlen($_POST["celular"]) <= 14){
                            $erro = 1;
                            $msg = $msg."O telefone não está completo. ";
                            $opt = "Toast";
                        }else{
                            $celular = addslashes($_POST["celular"]);
                        }
                    }

                    if(isset($_POST["senha"])){
                        if(strlen($_POST["senha"]) <= 7){
                            $erro = 1;
                            $msg = $msg."A senha não está completa. ";
                            $opt = "Toast";
                        }else{
                            $senha = addslashes($_POST["senha"]);
                        }
                    }
                }

                if($camposOK==true && $erro==0){
                    //Verifica se esse email já está cadastrado
                    $sql_consulta = "select email from tb_usuarios where email='".$email."' limit 1;";
                    $consulta = mysqli_query($GLOBALS['con'],$sql_consulta);
                    if(mysqli_num_rows($consulta)>0){
                        $erro = 1;
                        $msg = $msg."Não foi possível efetuar o cadastro pois o email já foi cadastrado, favor corrigir o email ou procurar a sessão esqueci minha senha. ";
                        $opt = "Toast";
                    }

                    //Verifica se esse CPF já está cadastrado
                    $sql_consulta = "select CPF from tb_usuarios where CPF='".$cpf."' limit 1;";
                    $consulta = mysqli_query($GLOBALS['con'],$sql_consulta);
                    if(mysqli_num_rows($consulta)>0){
                        $erro = 1;
                        $msg = $msg."Não foi possível efetuar o cadastro pois o CPF já foi cadastrado, favor corrigir o CPF ou procurar a sessão esqueci minha senha. ";
                        $opt = "Toast";
                    }
                }


                //#########################################################################################
                //                                  NÃO HAVENDO ERRO, INCLUI NA TABELA
                //#########################################################################################

                $token = rand(0,9).rand(0,9).rand(0,9).rand(0,9).rand(0,9).rand(0,9).rand(0,9).rand(0,9).rand(0,9);

                if($camposOK == true && $erro == 0){
                    $sql1 = "SELECT NOME,id FROM tb_usuarios WHERE ID IN
                    (
                        select id_distribuidor from tb_indicacao
                        where email = '". $email."' and
                        dt_time in
                        (
                            select min(dt_time) as dt_time from tb_indicacao where email = '". $email."'
                        )
                    );";
                    //echo $sql1 . "<br>";
                    if(isset($_POST["id_distr"])){
                        $id_distr = $_POST["id_distr"];
                    }else{
                        $id_distr = 1;
                    }

                    $sql = "insert into tb_usuarios (email,NOME,SOBRENOME,NOME_COMPLETO,CPF,CEL,SENHA,TOKEN,id_distribuidor,access_group) values('".$email."','".$nome."','".$sobrenome."', '".$nome_completo."','".$cpf."','".$celular."','".$senha."','".$token."','".$id_distr."','default');";
                    mysqli_query($GLOBALS['con'],$sql);
                    //echo $sql . "<br>";

                    //Valida se foi cadastrado
                    $check_user_query = "select id,NOME,SOBRENOME,NOME_COMPLETO,CEL,access_group,id_distribuidor from tb_usuarios WHERE email = '".$email."';";
                    //echo "check_user_query: ".$check_user_query."<br>";
                    $consulta = mysqli_query($GLOBALS['con'],$check_user_query);
                    if(mysqli_num_rows($consulta)>0){
                        for($i=0;$i<mysqli_num_rows($consulta);$i++){
                            $row = mysqli_fetch_row($consulta);
                            if($row[0] >= 1){
                                //copia imagem padrão para pasta do user id no servidor
                                $exec = shell_exec("cp /var/www/mp/media/imagens/default.jpg /var/www/mp/media/upload/user_".md5($row[0].$row[1]).".jpg");
                                //Atualiza imagem no banco de dados
                                $update_user_query = "update tb_usuarios set imagem ='/media/upload/user_" .md5($row[0].$row[1]) . ".jpg' WHERE email = '$email';";
                                mysqli_query($GLOBALS['con'],$update_user_query);

                                //Gera session com dados do cadastro
                                session_start();
                                $_SESSION['user_id'] = $row[0];
                                $_SESSION['user_nome'] = $row[1];
                                $_SESSION['user_sobrenome'] = $row[2];
                                $_SESSION['user_nome_compl'] = $row[3];
                                $_SESSION['user_cel'] = $row[4];
                                $_SESSION['user_email'] = $email;
                                $_SESSION['user_access'] = $row[5];
                                $_SESSION['user_id_distribuidor'] = $row[6];
                                $_SESSION['user_picture'] = '/media/upload/user_'.md5($row[0].$row[1]).'.jpg';
                                //rememberMe();
                                $resp_login = onLogin();
                                //echo "resp_login: " . $resp_login . "<br>";
                                $erro = 0;
                                $msg = true;
                                $opt = "logado";
                                $dados = array("1"=>1);
                                include_once("/var/www/mp/bg_enviaemail.php");
                                $resposta = define_envia('boas_vindas_comprador',$dados);
                                $resposta = define_envia('novo_cliente',$dados);

                                //$erro = 0;
                                //$msg = "Cadastrado com sucesso! =) ";
                                //$opt = 'logado';
                            }
                        }
                    }else{
                        $erro = 1;
                        $msg = "Erro na inclusão de registro no banco de dados.";
                        $opt = 'Toast';
                    }
                    mysqli_close($GLOBALS['con']);

                }else{
                    $erro = 1;
                    $msg = "Erro na solicitação de cadastro. ".$msg;
                    $opt = "Toast";
                }
                break;

            case 'verificar':
                //echo "entrei no verificar <br>";
                if(isset($_POST["email"])){
                    $email = addslashes(strtolower($_POST["email"]));
                    //echo "email:".$email."<br>";
                    if(isset($_POST["senha"])){
                        $senha = addslashes($_POST["senha"]);
                    }else{
                        $senha = null;
                    }
                    //echo "vou validar o email <br>";
                    if(isset($_POST["distr"])){
                        $id_distr = addslashes($_POST["distr"]);
                    }
                    validaemail($email,$senha,$id_distr);
                }else{
                    $erro = 1;
                    $msg = "Por favor, verifique o email.";
                    $opt = "Toast";
                }
                break;
            case 'cad_distr_verificar':
                if(isset($_POST["email"])){
                    $email = addslashes(strtolower($_POST["email"]));
                    //echo "email:".$email."<br>";
                    if(isset($_POST["senha"])){
                        $senha = addslashes($_POST["senha"]);
                    }else{
                        $senha = null;
                    }
                    //echo "vou validar o email <br>";
                    cad_distr_validaemail($email,$senha);
                }else{
                    $erro = 1;
                    $msg = "Por favor, verifique o email.";
                    $opt = "Toast";
                }
                break;
            case 'session':
                /*if(isset($_SESSION['user_id'])){
                   $resposta='redirect';
                }else{
                    $resposta='';
                }*/

                $resposta = rememberMe();
                //$resposta='';
                //echo "Resposta: ". $resposta . "<br>";
                if($resposta==='redirect'){
                    $erro = 0;
                    $msg = true;
                    $opt = "jump_modal";
                }else if($resposta==='index.html'){
                    $erro = 0;
                    $msg = 'index.html';
                    $opt = "expulsa";
                }else{
                    $erro = 0;
                    $msg = false;
                    $opt = "jump_modal";
                }

                break;
            case 'distribuidor_cadastrar':
                $camposOK=true;
                //echo "entrei no cadastrar <br>";
                //Caso os campos obrigatórios estejam preenchidos faz mais algumas validações (email, cpf, nome, telefone)

                if($camposOK==true && $erro==0){

                    if(filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)){
                        $email = addslashes(strtolower($_POST["email"]));
                    }else{
                        $msg = $msg."O email parece incorreto. ";
                        $erro = 1;
                        $opt = "Toast";
                    }

                    if(isset($_POST["cpf"])){
                        $cpf = preg_replace( '/[^0-9]/', '', $_POST["cpf"] );
                        // Verifica se o CPF é válido
                        if(valida_cpf($cpf)==false){
                            $erro = 1;
                            $msg = $msg."O CPF não parece válido. ";
                            $opt = "Toast";
                        }else{
                            $cpf = addslashes($_POST["cpf"]);
                        }
                    }else{
                        $erro = 1;
                        $msg = $msg."O CPF não está preenchido. ";
                        $opt = "Toast";
                    }

                    if(isset($_POST["nome"]) && isset($_POST["sobrenome"])){
                        if(strlen($_POST["nome"]) <= 1 && strlen($_POST["sobrenome"]) <= 1){
                            $erro = 1;
                            $msg = $msg."O nome não está completo. ";
                            $opt = "Toast";
                        }else{
                            $nome = addslashes($_POST["nome"]);
                            $sobrenome = addslashes($_POST["sobrenome"]);
                            $nome_completo = $nome . " " . $sobrenome;
                        }
                    }

                    if(isset($_POST["celular"])){
                        if(strlen($_POST["celular"]) <= 13){
                            $erro = 1;
                            $msg = $msg."O telefone não está completo. ";
                            $opt = "Toast";
                        }else{
                            $celular = addslashes($_POST["celular"]);
                        }
                    }

                    if(isset($_POST["senha"])){
                        if(strlen($_POST["senha"]) <= 7){
                            $erro = 1;
                            $msg = $msg."A senha não está completa. ";
                            $opt = "Toast";
                        }else{
                            $senha = addslashes($_POST["senha"]);
                        }
                    }
                }

                if($camposOK==true && $erro==0){
                    //Verifica se esse email já está cadastrado
                    $sql_consulta = "select email from tb_usuarios where email='".$email."' limit 1;";
                    $consulta = mysqli_query($GLOBALS['con'],$sql_consulta);
                    if(mysqli_num_rows($consulta)>0){
                        $erro = 1;
                        $msg = $msg."Não foi possível efetuar o cadastro pois o email já foi cadastrado, favor corrigir o email ou procurar a sessão esqueci minha senha. ";
                        $opt = "Toast";
                    }

                    //Verifica se esse CPF já está cadastrado
                    $sql_consulta = "select CPF from tb_usuarios where CPF='".$cpf."' limit 1;";
                    $consulta = mysqli_query($GLOBALS['con'],$sql_consulta);
                    if(mysqli_num_rows($consulta)>0){
                        $erro = 1;
                        $msg = $msg."Não foi possível efetuar o cadastro pois o CPF já foi cadastrado, favor corrigir o CPF ou procurar a sessão esqueci minha senha. ";
                        $opt = "Toast";
                    }
                }


                //#########################################################################################
                //                                  NÃO HAVENDO ERRO, INCLUI NA TABELA
                //#########################################################################################

                $token = rand(0,9).rand(0,9).rand(0,9).rand(0,9).rand(0,9).rand(0,9).rand(0,9).rand(0,9).rand(0,9);

                if($camposOK == true && $erro == 0){
                    $sql1 = "SELECT NOME,id FROM tb_usuarios WHERE ID IN
                    (
                        select id_distribuidor from tb_indicacao
                        where email = '". $email."' and
                        dt_time in
                        (
                            select min(dt_time) as dt_time from tb_indicacao where email = '". $email."'
                        )
                    );";
                    //echo $sql1 . "<br>";
                    if(isset($_POST["id_distr"])){
                        $id_distr = $_POST["id_distr"];
                    }else{
                        $id_distr = 1;
                    }

                    $sql = "insert into tb_usuarios (email,NOME,SOBRENOME,NOME_COMPLETO,CPF,CEL,SENHA,TOKEN,access_group) values('".$email."','".$nome."','".$sobrenome."', '".$nome_completo."','".$cpf."','".$celular."','".$senha."','".$token."','default');";
                    mysqli_query($GLOBALS['con'],$sql);
                    //echo $sql . "<br>";

                    //Valida se foi cadastrado
                    $check_user_query = "select id,NOME,SOBRENOME,NOME_COMPLETO,CEL,access_group,id_distribuidor from tb_usuarios WHERE email = '".$email."';";
                    //echo "check_user_query: ".$check_user_query."<br>";
                    $consulta = mysqli_query($GLOBALS['con'],$check_user_query);
                    if(mysqli_num_rows($consulta)>0){
                        for($i=0;$i<mysqli_num_rows($consulta);$i++){
                            $row = mysqli_fetch_row($consulta);
                            if($row[0] >= 1){
                                //copia imagem padrão para pasta do user id no servidor
                                $exec = shell_exec("cp /var/www/mp/media/imagens/default.jpg /var/www/mp/media/upload/user_".md5($row[0].$row[1]).".jpg");
                                //Atualiza imagem no banco de dados
                                $update_user_query = "update tb_usuarios set imagem ='/media/upload/user_" .md5($row[0].$row[1]) . ".jpg' WHERE email = '".$email."';";
                                mysqli_query($GLOBALS['con'],$update_user_query);

                                //Gera session com dados do cadastro
                                session_start();
                                $_SESSION['user_id'] = $row[0];
                                $_SESSION['user_nome'] = $row[1];
                                $_SESSION['user_sobrenome'] = $row[2];
                                $_SESSION['user_nome_compl'] = $row[3];
                                $_SESSION['user_cel'] = $row[4];
                                $_SESSION['user_email'] = $email;
                                $_SESSION['user_access'] = $row[5];
                                $_SESSION['user_id_distribuidor'] = $row[6];
                                $_SESSION['user_picture'] = '/media/upload/user_'.md5($row[0].$row[1]).'.jpg';
                                //rememberMe();
                                $resp_login = onLogin();
                                //echo "resp_login: " . $resp_login . "<br>";
                                $erro = 0;
                                $msg = true;
                                $opt = "distribuidor_aut_mercadopago";

                                //$erro = 0;
                                //$msg = "Cadastrado com sucesso! =) ";
                                //$opt = 'logado';
                            }
                        }
                    }else{
                        $erro = 1;
                        $msg = "Erro na inclusão de registro no banco de dados.";
                        $opt = 'Toast';
                    }
                    mysqli_close($GLOBALS['con']);

                }else{
                    $erro = 1;
                    $msg = "Erro na solicitação de cadastro. ".$msg;
                    $opt = "Toast";
                }
                break;
        }
    }else{
        $erro = 1;
        $msg = "Dados incompletos, favor preencher o cadastro novamente.";
        $opt = "Toast";
    }

    function validaemail($mail,$passwd,$id_distr){
        global $funcao, $erro, $msg, $opt, $id_distr_cad;
        //echo "entrei no valida email <br>";
        // Procura usuário no banco de dados de indicação
        $sql1 = "SELECT NOME,ID FROM tb_usuarios WHERE ID IN
        (
            select id_distribuidor from tb_indicacao
            where email = '". $mail."' and
            dt_time in
            (
                select min(dt_time) as dt_time from tb_indicacao where email = '". $mail."'
            )
        );";

        //Procura no banco de dados de cadastrados
        $sql2 = "select id,id_distribuidor from tb_usuarios where email = '". $mail."';";

        //Procura no banco de dados de cadastrados e verifica se a senha está correta
        $sql2s = "select id,NOME,SOBRENOME,NOME_COMPLETO,CEL,access_group,id_distribuidor from tb_usuarios where email = '". $mail."' and  senha ='". $passwd ."';";

        if($id_distr!=null){
            //Procura os dados do distribuidor que fez a indicação por link
            $sql3 = "select id, nome, email from tb_usuarios where id=" . $id_distr . ";";
            $query3 = mysqli_query($GLOBALS['con'],$sql3);
            if(mysqli_num_rows($query3)>0){
                $row_distr = mysqli_fetch_row($query3);
            }
        }
        //echo "SQL1: " . $sql1."<br>";
        //echo "SQL2: " . $sql2."<br>";
        //echo "SQL2s: " . $sql2s."<br>";
        //echo "SQL3: " . $sql3."<br>";
        //var_dump($row_distr);

        $query1 = mysqli_query($GLOBALS['con'],$sql1);
        $query2 = mysqli_query($GLOBALS['con'],$sql2);
        $query2s = mysqli_query($GLOBALS['con'],$sql2s);

        //echo "num_rows(query1): " . mysqli_num_rows($query1) . "<br>";
        //echo "num_rows(query2): " .  mysqli_num_rows($query2) . "<br>";
        //echo "num_rows(query2s): " .  mysqli_num_rows($query2s) . "<br>";
        //mysqli_close($GLOBALS['con']);

        if(mysqli_num_rows($query2)>0){
            if(mysqli_num_rows($query2s)>0){
                //echo "carrogou a session com o login <br>";
                $row = mysqli_fetch_row($query2s);
                $_SESSION['user_id'] = $row[0];
                $_SESSION['user_nome'] = $row[1];
                $_SESSION['user_sobrenome'] = $row[2];
                $_SESSION['user_nome_compl'] = $row[3];
                $_SESSION['user_cel'] = $row[4];
                $_SESSION['user_email'] = $mail;
                $_SESSION['user_access'] = $row[5];
                $_SESSION['user_id_distribuidor'] = $row[6];
                $_SESSION['user_picture'] = '/media/upload/user_'.md5($row[0].$row[1]).'.jpg';
                //rememberMe();
                $resp_login = onLogin();
                //echo "resp_login: " . $resp_login . "<br>";
                $erro = 0;
                $msg = true;
                $opt = 'logado';
            }else{
                if($passwd == null){
                    $erro = 0;
                    $msg = 'Usuário já cadastrado, por favor efetue o login.';
                    $opt = 'login';
                }else{
                    $erro = 1;
                    $msg = 'Senha incorreta, por favor tente novamente.';
                    $opt = 'Toast';
                }
            }
        }else{
            if(mysqli_num_rows($query1)>0){
                $row = mysqli_fetch_row($query1);
                $msg = 'Parabéns, você foi indicado por ' . $row[0] . ' para ter acesso a um clube de vendas exclusivo e poder desfrutar de produtos únicos que contribuirão com sua saúde e bem estar.';
                $id_distr_cad = $row[1];
                $opt = 'cadastrar_indicado';
                //echo "contudo sql para extra : <br>";
                //var_dump($row);
            }else{
                if(isset($row_distr)){
                    $msg = 'Parabéns, você foi indicado por ' . $row_distr[1] . ' para ter acesso a um clube de vendas exclusivo e poder desfrutar de produtos únicos que contribuirão com sua saúde e bem estar.';
                    $id_distr_cad = $row_distr[0];
                    $opt = 'cadastrar_indicado';
                }else{
                    $msg = 'Como você não possui indicações, escolha um distribuidor';
                    $id_distr_cad = 1;
                    $opt = 'cadastrar_nao_indicado';
                }
            }
        }
        //echo "msg: ".$msg."<br>";
        //echo "opt: ".$opt."<br>";
    }

    function cad_distr_validaemail($mail,$passwd){
        global $funcao, $erro, $msg, $opt, $id_distr_cad;
        //Procura no banco de dados de cadastrados
        $sql1 = "select id,id_distribuidor from tb_usuarios where email = '". $mail."';";

        //Procura no banco de dados de cadastrados e verifica se a senha está correta
        $sql1s = "select id,NOME,SOBRENOME,NOME_COMPLETO,CEL,access_group,id_distribuidor from tb_usuarios where email = '". $mail."' and  senha ='". $passwd ."';";

        //echo "SQL1: " . $sql1."<br>";
        //echo "SQL2: " . $sql2."<br>";

        $query1 = mysqli_query($GLOBALS['con'],$sql1);
        $query1s = mysqli_query($GLOBALS['con'],$sql1s);
        //echo "num_rows(query1): " . mysqli_num_rows($query1) . "<br>";
        //echo "num_rows(query2): " .  mysqli_num_rows($query2) . "<br>";
        //echo "num_rows(query2s): " .  mysqli_num_rows($query2s) . "<br>";
        //mysqli_close($GLOBALS['con']);

        if(mysqli_num_rows($query1)>0){
            if(mysqli_num_rows($query1s)>0){
                //echo "carrogou a session com o login <br>";
                $row = mysqli_fetch_row($query1s);
                $_SESSION['user_id'] = $row[0];
                $_SESSION['user_nome'] = $row[1];
                $_SESSION['user_sobrenome'] = $row[2];
                $_SESSION['user_nome_compl'] = $row[3];
                $_SESSION['user_cel'] = $row[4];
                $_SESSION['user_email'] = $mail;
                $_SESSION['user_access'] = $row[5];
                $_SESSION['user_id_distribuidor'] = $row[6];
                $_SESSION['user_picture'] = '/media/upload/user_'.md5($row[0].$row[1]).'.jpg';
                //rememberMe();
                $resp_login = onLogin();
                //echo "resp_login: " . $resp_login . "<br>";
                $erro = 0;
                $msg = true;
                $opt = 'logado';
            }else{
                if($passwd == null){
                    $erro = 1;
                    $msg = 'Vocẽ já está cadastrado, por favor efetue o login.';
                    $opt = 'login';
                }else{
                    $erro = 1;
                    $msg = 'Senha incorreta, por favor tente novamente.';
                    $opt = 'Toast';
                }
            }
        }else{
            $row = mysqli_fetch_row($query1);
            $msg = 'Efetuar seu cadastro é o primeiro passo. Vamos lá?';
            $id_distr_cad = 1;
            $opt = 'cadastrar_distribuidor';
        }
    }

	$envia_resposta = array("erro"=>$erro,"msg"=>$msg,"funcao"=>$funcao,"opt"=>$opt,"id_distr_cad"=>$id_distr_cad);
	print json_encode($envia_resposta);
?>
