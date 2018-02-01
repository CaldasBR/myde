<?php
    //ini_set('display_errors','on');
    error_reporting(E_ALL);

    //session_start();
    //Verifica se usuário está Logado
    include("/var/www/mp/bg_protege_php.php");
    $status_log = status_login($resposta);

    $erro = 0;
    $msg = "Nada foi executado!";
    $funcao = "upload";
    $opt = "";

    //echo $_POST["fn"];
    if($status_log=="mantem"){
        if(isset($_GET["fn"]) || isset($_POST["fn"])){
            if(isset($_GET["fn"])){
                $fn = addslashes($_GET["fn"]);
            }else{
                $fn = addslashes($_POST["fn"]);
            }
            
            switch ($fn){
                case 'indicacao':
                    //echo "logado!<br><br>";
                    // Pasta onde o arquivo vai ser salvo
                    $_UP['pasta'] = 'uploads/';

                    // Tamanho máximo do arquivo (em Bytes)
                    $_UP['tamanho'] = 1024 * 1024 * 8; // 8Mb

                    // Array com as extensões permitidas
                    $_UP['extensoes'] = array('xlsx', 'xls', 'xlsb');

                    // Renomeia o arquivo? (Se true, o arquivo será salvo como .jpg e um nome único)
                    $_UP['renomeia'] = true;

                    // Array com os tipos de erros de upload do PHP
                    $_UP['erros'][0] = 'Não houve erro';
                    $_UP['erros'][1] = 'O arquivo no upload é maior do que o limite permitido (2MB).';
                    $_UP['erros'][2] = 'O arquivo ultrapassa o limite de tamanho especifiado no HTML';
                    $_UP['erros'][3] = 'O upload do arquivo foi feito parcialmente';
                    $_UP['erros'][4] = 'Não foi feito o upload do arquivo';


                    // Verifica se houve algum erro com o upload. Se sim, exibe a mensagem do erro
                    if ($_FILES['arquivo']['error'] != 0) {
                        die("Não foi possível fazer o upload, erro:" . $_UP['erros'][$_FILES['arquivo']['error']]);
                        exit; // Para a execução do script
                    }
                    //echo "passou 1 <br>";

                    // Caso script chegue a esse ponto, não houve erro com o upload e o PHP pode continuar
                    // Faz a verificação da extensão do arquivo
                    //$extensao = strtolower(end(explode('.', $_FILES['arquivo']['name'])));
                    $extensao = strtolower(end(explode('.', $_FILES['arquivo']['name'])));
                    //IMPORTANTE! o HTML tem que ter p name: <input type="file" name="arquivo">
                    //echo "extensao: <br>";
                    //var_dump($extensao);

                    if(array_search($extensao, $_UP['extensoes']) === false) {
                        $erro = 1;
                        $msg = "Por favor, envie arquivos com as seguintes extensões: xlsx, xls, xlsb";
                        $opt = "Toast";
                        exit;
                    }
                    //echo "passou 2 <br>";

                    // Faz a verificação do tamanho do arquivo
                    if ($_UP['tamanho'] < $_FILES['arquivo']['size']) {
                        $erro = 1;
                        $msg = "O arquivo enviado é muito grande, envie arquivos de até 2MB.";
                        $opt = "Toast";
                        exit;
                    }

                    //echo "passou 3 <br>";
                    // O arquivo passou em todas as verificações, hora de tentar movê-lo para a pasta
                    // Primeiro verifica se deve trocar o nome do arquivo
                    if ($_UP['renomeia'] == true) {
                        // Cria um nome baseado no UNIX TIMESTAMP atual e com extensão .jpg
                        $nome_final = "indic_user_".$_SESSION["user_id"].'.xlsx';
                    }else {
                    // Mantém o nome original do arquivo
                        $nome_final = $_FILES['arquivo']['name'];
                    }

                    //echo "passou 4 <br>";
                    // Depois verifica se é possível mover o arquivo para a pasta escolhida
                    if (move_uploaded_file($_FILES['arquivo']['tmp_name'], $_UP['pasta'] . $nome_final)) {
                        // Upload efetuado com sucesso, exibe uma mensagem e um link para o arquivo
                        $erro = 0;
                        $msg = "Upload efetuado com sucesso!";
                        $opt = "incluir_bd";
                        //echo '<a href="' . $_UP['pasta'] . $nome_final . '">Clique aqui para acessar o arquivo</a>';
                    }else{
                        // Não foi possível fazer o upload, provavelmente a pasta está incorreta
                        $erro = 0;
                        $msg = "Não foi possível salvar o arquivo, tente novamente";
                        $opt = "Toast";
                    }
                    //echo "passou 5 <br>";


                    if($opt=='incluir_bd'){
                        include 'simplexlsx.class.php';
                        $caminho = $_UP['pasta'].$nome_final;
                        //echo "caminho: ". $caminho;
                        $xlsx = new SimpleXLSX($_UP['pasta'].$nome_final);
                        $dados = $xlsx->rows();

                        var_dump($dados);

                        include_once("/var/www/mp/bg_enviaemail.php");
                        $lin_atual ==0;
                        foreach($dados as $linha=>$coluna){
                            if($lin_atual!=0){
                                if(count($coluna)==3){
                                    $sql = "INSERT INTO tb_indicacao (id_distribuidor, nome, email, celular) VALUES (".$_SESSION["user_id"].",'".$coluna[0]."','".$coluna[1]."','".$coluna[2]."');";
                                    include("/var/www/mp/bg_conexao_bd.php");
                                    $query = mysqli_query($GLOBALS['con'],$sql);
                                    //echo "sql: " . $sql . "<br>";

                                    //Enviar email de indicação
                                    $dados = array("nome"=>$coluna[0],"email"=>$coluna[1]);
                                    $resposta = define_envia('indicacao',$dados);
                                }elseif(count($coluna)==2){
                                    $sql = "INSERT INTO tb_indicacao (id_distribuidor, nome, email) VALUES (".$_SESSION["user_id"].",'".$coluna[0]."','".$coluna[1]."');";
                                    include("/var/www/mp/bg_conexao_bd.php");
                                    $query = mysqli_query($GLOBALS['con'],$sql);
                                    //echo "sql: " . $sql . "<br>";

                                    //Enviar email de indicação
                                    $dados = array("nome"=>$coluna[0],"email"=>$coluna[1]);
                                    $resposta = define_envia('indicacao',$dados);
                                }else{
                                    echo "erro variavel coluna: " . print_r($coluna) . "<br>";
                                }
                            }
                            $lin_atual += 1;
                        }
                        header('location: https://queromarita.com.br/indicados.html');
                    }
                break;
                case 'img_perfil':
                    // verifica se foi enviado um arquivo
                    if(isset($_POST['before_size'])){
                        $img_tamanho = $_POST['before_size'];
                        //echo 'O tamanho anterior é: ' . $_POST['size'] . ' Bytes<br/>';

                        if($img_tamanho<=(1024 * 1024 * 20)){ // 8Mb
                            //echo 'O tamanho é adequado <br>';
                        
                            if($_FILES['arquivo']["erro"]==0){
                                if(isset($_FILES['arquivo'])){
                                    /*echo 'Recebi um arquivo <br>';
                                    echo 'Você enviou o arquivo: ' . $_FILES['arquivo']['name'] . '<br/>';
                                    echo 'Este arquivo é do tipo: ' . $_FILES['arquivo']['type'] . '<br/>';
                                    echo 'Seu tamanho é: ' . $_FILES['arquivo']['size'] . ' Bytes<br/>';
                                    echo 'Tmp Name: ' . $_FILES['arquivo']['tmp_name'] . '<br/>';*/
                                }else{
                                    echo 'Não Recebi um arquivo <br>';
                                }
                                
                                //echo 'Salvar imagem <br>';

                                // Pega a extensão e converte a extensão para minúsculo
                                $img_extensao = strtolower(pathinfo($_FILES['arquivo']['name'], PATHINFO_EXTENSION));

                                //Converte a extensão para minúsculo
                                //$extensao = strtolower($img_extensao);

                                // Aceita somente imagens, .jpg;.jpeg;.gif;.png
                                if(strstr('.jpg;.jpeg;.gif;.png', $img_extensao)){

                                    // Cria um nome único para esta imagem
                                    // Evita que duplique as imagens no servidor.
                                    // Evita nomes com acentos, espaços e caracteres não alfanuméricos
                                    $img_nomeNovo = uniqid(time()).'.'.$img_extensao;
                                    //echo "Novo Nome: " . $img_nomeNovo . "<br>";

                                    // Concatena a pasta com o nome
                                    $destino = 'media/upload/'.$img_nomeNovo;
                                    //echo "destino: " . $destino . "<br>";

                                    // tenta mover o arquivo para o destino
                                    if(@move_uploaded_file ( $_FILES['arquivo']['tmp_name'], $destino )) {
                                        //echo 'Arquivo salvo com sucesso em : ' . $destino . '<br>';
                                        
                                        $sql = "UPDATE tb_usuarios SET imagem='".$destino."' WHERE id=".$_SESSION["user_id"].";";
                                        //echo "SQL: " . $sql . "<br>";

                                        include("/var/www/mp/bg_conexao_bd.php");
                                        $query = mysqli_query($GLOBALS['con'],$sql);

                                        $erro = 0;
                                        $msg = $destino;
                                        $opt = "refresh";
                                        //header('location: https://queromarita.com.br/cadastro.html');
                                    }else{
                                        $erro = 1;
                                        $msg = "Erro ao salvar o arquivo. Aparentemente você não tem permissão de escrita.";
                                        $opt = "Toast";
                                    }
                                }else{
                                    $erro = 1;
                                    $msg = "Você pode enviar apenas arquivos jpg, jpeg, gif, png";
                                    $opt = "Toast";
                                }
                            }else{
                                $erro = 1;
                                $msg = "O envio parece incorreto";
                                $opt = "Toast";
                            }
                        }else{
                             $erro = 1;
                             $msg = "O tamanho máximo permitido é de 20MB.";
                             $opt = "Toast";
                        }
                    }else{
                        $erro = 1;
                        $msg = "Houve um erro na transmissão do arquivo. Tem certeza que você enviou algum?";
                        $opt = "Toast";
                    }
                break;
            }
        }else{
            $erro = 1;
            $msg = "Solicitação Incorreta. O tamanho máximo permitido é de 20MB.";
            $opt = "Toast";
        }
    }else{
        $erro = 1;
        $msg = "Usuário não autenticado.";
        $opt = "expulsa";
    }


    $envia_resposta = array("erro"=>$erro,"msg"=>$msg,"funcao"=>$funcao,"opt"=>$opt);
	print json_encode($envia_resposta);
?>
