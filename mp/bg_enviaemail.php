<?php
    //ini_set('display_errors','on');
	//error_reporting(E_ALL);
    //header('Content-Type: text/html; charset=utf-8');

    function define_envia($fn,$email){

        include("/var/www/mp/bg_conexao_bd.php");
        include_once("/var/www/mp/bg_funcoes_genericas.php");

        switch ($fn){
            case 'esqueci':
                $sql = "SELECT id,nome, email, token from tb_usuarios where email='".$email."';";
                //echo "SQL: " . $sql . "<br>";
                include("/var/www/mp/bg_conexao_bd.php");
                $query = mysqli_query($GLOBALS['con'],$sql);

                if(mysqli_num_rows($query)>0){
                    $row = mysqli_fetch_row($query);
                    $txtAssunto = "QueroMarita: Instruções para restaurar seu acesso";
                    $txtPara = array($email);
                    $txtMensagem =
                        '<head>
                            <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
                        </head>
                        <h1 id="mcetoc_1bgs8lahu0" style="text-align: left;"><img src="http://queromarita.com.br/media/imagens/Marita_logo-1-100x100.png" alt="Logo_Marita" width="100" height="100" /> &nbsp;<strong><span style="color: #3d1e04;">Quero Marita</span></strong></h1>
                        <p>&nbsp;</p>
                        <p><span style="color: #575757;"><strong>Este email foi enviado para voc&ecirc; atrav&eacute;s do nosso site para dar instru&ccedil;&otilde;es de recupera&ccedil;&atilde;o do seu acesso.</strong></span></p>
                        <p><strong><span style="color: #3d1e04;"><span style="color: #575757;">Caso voc&ecirc; n&atilde;o tenha solicitado esse procedimento clique</span> <span style="color: #0000ff;"><a style="color: #0000ff;" href="http://queromarita.com.br/acesso.html?fn=naofui&amp;id='.$row[0].'">aqui</a></span>.</span></strong></p>
                        <p><span style="color: #575757;"><strong>Para cadastrar uma nova senha, acesse o link abaixo:</strong></span></p>
                        <p><span style="color: #0000ff; background-color: #d5f7f4;"><a style="color: #0000ff; background-color: #d5f7f4;" href="http://queromarita.com.br/acesso.html?fn=reset&amp;id='.$row[0].'&amp;token='.md5($row[3]).'"><strong>RESTAURAR ACESSO</strong></a></span></p>
                        <p><strong><span style="color: #575757;">Estamos sempre dispon&iacute;veis para atender voc&ecirc; atrav&eacute;s do email <span style="color: #0000ff;"><a style="color: #0000ff;" href="mailto:contato@myde.com.br">contato@myde.com.br</a></span>, ou atrav&eacute;s do nosso chat em nossa</span><span style="color: #575757;"><span style="color: #0000ff;"> <a style="color: #0000ff;" href="https://queromarita.com.br/">p&aacute;gina inicial.</a></span></span></strong></p>
                        <p>&nbsp;</p>
                        <p><span style="color: #575757;"><strong>Ateciosamente,</strong></span></p>
                        <p><span style="color: #575757;"><strong>Equipe QueroMarita</strong></span></p>
                        <p>&nbsp;</p>
                        <p>&nbsp;</p>
                        <p><span style="color: #999999;"><strong>Propriedade exclusiva da empresa&nbsp;</strong></span><strong><span style="color: #999999;">&nbsp; </span><br /></strong></p>
                        <p><strong><img src="http://queromarita.com.br/media/imagens/myde_texto.png" alt="Myde" width="104" height="33" /></strong></p>
                        <p>&nbsp;</p>';

                    $envio = enviar_email($txtPara, $txtAssunto, $txtMensagem);
                    if($envio==true){
                        $erro = 0;
                        $msg = "Você receberá um email com as instruções.";
                        $opt = "Toast";
                    }else{
                        $erro = 1;
                        $msg = "Erro no envio do email.";
                        $opt = "Toast";
                    }
                }else{
                    $erro = 1;
                    $msg = "Este email não está cadastrado em nosso site.";
                    $opt = "Toast";
                }

                $funcao = $fn;
                $envia_resposta = json_encode(array("erro"=>$erro,"msg"=>$msg,"funcao"=>$funcao,"opt"=>$opt));
                return $envia_resposta;
            break;
            case 'contato':
                //var_dump($email);
                $dados = $email;
                //var_dump($dados);

                $nome = $dados["nome"];
                $email = $dados["email"];
                $fone = $dados["fone"];
                $texto = $dados["texto"];
                $autorizo = $dados["autorizo"];

                $sql = "INSERT INTO tb_contatos (nome, email, cel, mensagem, autorizo)
                    VALUES ('".$nome."','".$email."','".$fone."','".$texto."','".$autorizo."');";

                //echo "SQL: " . $sql . "<br>";
                include("/var/www/mp/bg_conexao_bd.php");
                $query = mysqli_query($GLOBALS['con'],$sql);

                $row = mysqli_fetch_row($query);
                $txtAssunto = "[contato] ".$nome." - ".$email. " - " . $fone;
                $txtPara = array('contato@myde.com.br');
                $txtMensagem =
                    '<head>
                        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
                    </head>
                    <body>
                        <h3>Solicita&ccedil;&atilde;o de contato</h3>
                        <br>
                        <h5>Por gentileza entrar em contato com:</h5>
                        <table style="width:100%">
                            <tr>
                                <th>NOME</th>
                                <th>EMAIL</th>
                                <th>TELEFONE</th>
                                <th>MENSAGEM</th>
                                <th>AUTORIZA PUBLICIDADE</th>
                            </tr>
                            <tr>
                                <td>'.$nome.'</td>
                                <td>'.$email.'</td>
                                <td>'.$fone.'</td>
                                <td>'.$texto.'</td>
                                <td>'.$autorizo.'</td>
                            </tr>
                        </table>
                    </body>
                    ';

                $envio = enviar_email($txtPara, $txtAssunto, $txtMensagem);
                if($envio==true){
                    $erro = 0;
                    $msg = "Seu email foi enviado, em breve entraremos em contato.";
                    $opt = "Toast";
                }
                $funcao = $fn;
                $envia_resposta = json_encode(array("erro"=>$erro,"msg"=>$msg,"funcao"=>$funcao,"opt"=>$opt));
                return $envia_resposta;
            break;
            case 'pedido_efetuado':
                $dados = $email;
                //var_dump($dados);

                $id_user = $dados["id_user"];
                $email = $dados["email"];
                $nome = $dados["nome"];
                $id_distr = $dados["id_distr"];

                $sql_pedido="select pedido from tb_pedido_cabecalho where dt_time=(select max(dt_time) from tb_pedido_cabecalho where id_user=".$id_user." and id_distr=".$id_distr.");";
                $consulta_pedido = mysqli_query($GLOBALS['con'],$sql_pedido);
                $ped = mysqli_fetch_row($consulta_pedido);
                $pedido=$ped[0];


                $txtAssunto = "QueroMarita: Ótima escolha! Agradecemos pela sua compra.";
                $txtPara = array($email);
                $txtMensagem =
                    '<head>
                        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
                    </head>
                    <body><h1 id="mcetoc_1bgs8lahu0" style="text-align: left;"><img src="http://queromarita.com.br/media/imagens/Marita_logo-1-100x100.png" alt="Logo_Marita" width="100" height="100" />&nbsp;<strong><span style="color: #3d1e04;">Quero Marita</span></strong></h1>
                    <br>
                    <p style="text-align: left;">Ol&aacute;, '.$nome.'</p>
                    <br>
                    <p><span style="font-weight: 400;">Seu pedido <span style="color: #0000ff;">'.$pedido.'</span> no site QUEROMARITA foi efetuado com sucesso.</span></p>
                    <p><span style="font-weight: 400;">Aguardamos a confirma&ccedil;&atilde;o de pagamento para efetuar o envio. </span><span style="font-weight: 400;">Fique atento aos emails pois essa confirma&ccedil;&atilde;o deve acontecer em breve.</span></p>
                    <p><span style="font-weight: 400;">Caso queira consultar o status do seu pedido no site </span><a href="https://queromarita.com.br/pedidos.html">clique aqui</a></p>
                    <p>&nbsp;</p>
                    <p><em><strong>PEDIDO: '.$pedido.'</strong></em></p>
                    <p><em><strong>STATUS: PEDIDO REALIZADO</strong></em></p>
                    <p><em><strong>PRODUTOS:</strong></em></p>';

                    $sql_ped_item = "select t1.valor, t1.qtde, t2.titulo from  tb_pedido_itens t1 left join tb_produto_base t2 on (t1.id_produto=t2.id) where t1.pedido = ". $pedido .";";
                    $consulta_ped_item = mysqli_query($GLOBALS['con'],$sql_ped_item);
                    $valor_pedido=0;
                    $qtde_itens=0;
                    for($j=0;$j<mysqli_num_rows($consulta_ped_item);$j++){
                        $row_ped_item = mysqli_fetch_row($consulta_ped_item);
                        $valor_pedido=$valor_pedido+$row_ped_item[0]*$row_ped_item[1];
                        $qtde_itens=$qtde_itens+$row_ped_item[1];
                        $valor_unit_format=number_format($row_ped_item[0], 2, ',', '.');
                        $valor_item_format=number_format($row_ped_item[1]*$row_ped_item[0], 2, ',', '.');
                        $txtMensagem =$txtMensagem .'<p><em><strong>'.$row_ped_item[2].',</strong></em></p>';
                    }
                    $valor_pedido_format=number_format($valor_pedido, 2, ',', '.');
                    $txtMensagem =$txtMensagem .'
                    <br>
                    <p style="text-align: justify;">At&eacute; breve e boas compras !</p>
                    <p style="text-align: justify;">Equipe <a href="https://queromarita.com.br">queromarita.com.br</a></p>
                    <p>&nbsp;</p>
                    <p>&nbsp;<span style="color: #999999;"><strong>Propriedade exclusiva da empresa&nbsp;</strong></span><strong><span style="color: #999999;">&nbsp; </span><br></strong></p>
                    <p><strong><img src="http://queromarita.com.br/media/imagens/myde_texto.png" alt="Myde" width="104" height="33"></strong></p>
                    <p>&nbsp;</p>
                    </body>
                    ';
                    $envio = enviar_email($txtPara, $txtAssunto, $txtMensagem);
                    if($envio==true){
                        $erro = 0;
                        $msg = "Seu email foi enviado, em breve entraremos em contato.";
                        $opt = "Toast";
                    }
                    $funcao = $fn;
                    $envia_resposta = json_encode(array("erro"=>$erro,"msg"=>$msg,"funcao"=>$funcao,"opt"=>$opt));
                    return $envia_resposta;
            break;
            case 'pagamento_confirmado':
				 $dados = $email;
                //var_dump($dados);
                $order = $dados["order"];
                $email = $dados["email"];
                $nome = $dados["nome"];

                $txtAssunto = "QueroMarita: Pagamento CONFIRMADO para o pedido [".$order."]";
                $txtPara = array($email);
                $txtMensagem =
                    '<head>
                    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
                    </head>
                    <body><h1 id="mcetoc_1bgs8lahu0" style="text-align: left;"><img src="http://queromarita.com.br/media/imagens/Marita_logo-1-100x100.png" alt="Logo_Marita" width="100" height="100" /> &nbsp;<strong><span style="color: #3d1e04;">Quero Marita</span></strong></h1>
                    <br>
                    <p style="text-align: left;">Ol&aacute;, '.$nome.'</p>
                    <br>
                    <p><span style="font-weight: 400;">O pagamento do seu pedido <strong>'.$order.'</strong> foi <strong>CONFIRMADO</strong> e seu revendedor j&aacute; foi comunicado.</span></p>
                    <p><span style="font-weight: 400;">Agora falta pouco para receb&ecirc;-lo no endereço indicado. O tempo de entrega varia de acordo com a sua regi&atilde;o.</span></p>
                    <p><span style="font-weight: 400;">Fique atento aos emails pois enviaremos atualiza&ccedil;&atilde;o sobre o status do seu pedido.</span></p>
                    <p><span style="font-weight: 400;">Caso queira acompanhar o seu pedido pelo site, </span><a href="https://queromarita.com.br/pedidos.html">clique aqui</a></p>
                    <br>
                    <p style="text-align: justify;">At&eacute; breve,</p>
                    <p style="text-align: justify;">Equipe <a href="https://queromarita.com.br">queromarita.com.br</a></p>
                    <p>&nbsp;</p>
                    <p><span style="color: #999999;"><strong>Propriedade exclusiva da empresa&nbsp;</strong></span><strong><span style="color: #999999;">&nbsp; </span><br></strong></p>
                    <p><strong><img src="http://queromarita.com.br/media/imagens/myde_texto.png" alt="Myde" width="104" height="33"></strong></p>
                    <p>&nbsp;</p>
                    </body>
                    ';
                    $envio = enviar_email($txtPara, $txtAssunto, $txtMensagem);
                    if($envio==true){
                        $erro = 0;
                        $msg = "Seu email foi enviado, em breve entraremos em contato.";
                        $opt = "Toast";
                    }
                    $funcao = $fn;
                    $envia_resposta = json_encode(array("erro"=>$erro,"msg"=>$msg,"funcao"=>$funcao,"opt"=>$opt));
                    return $envia_resposta;
            break;
            case 'envio_realizado':
				$dados = $email;
                //var_dump($dados);
                $order = $dados["order"];
                $email = $dados["email"];
                $nome = $dados["nome"];

                $txtAssunto = "QueroMarita: Envio realizado para o pedido [".$order."]";
                $txtPara = array($email);
                $txtMensagem =
                    '<head>
                        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
                    </head>
                    <body><h1 id="mcetoc_1bgs8lahu0" style="text-align: left;"><img src="http://queromarita.com.br/media/imagens/Marita_logo-1-100x100.png" alt="Logo_Marita" width="100" height="100" /> &nbsp;<strong><span style="color: #3d1e04;">Quero Marita</span></strong></h1>
                    <br>
                    <p style="text-align: left;">Olá, '.$nome.'</p>
                    <br>
                    <p><span style="font-weight: 400;">O seu pedido <strong>'.$order.'</strong> foi <strong>ENVIADO</strong> pelo revendedor e logo chegará ao endereço indiindicado.</span></p>
                    <p><span style="font-weight: 400;">A previsão de entrega varia de acordo com a sua região.</span></p>
                    <p><span style="font-weight: 400;">Caso queira acompanhar o seu pedido pelo site, </span><a href="https://queromarita.com.br/pedidos.html">clique aqui</a>.</p>
                    <br>
                    <p><span style="font-weight: 400;">Agradecemos pela sua compra !</span></p>
                    <p style="text-align: justify;">Até breve,</p>
                    <p style="text-align: justify;">Equipe <a href="https://queromarita.com.br">queromarita.com.br</a></p>
                    <p>&nbsp;</p>
                    <p><span style="color: #999999;"><strong>Propriedade exclusiva da empresa&nbsp;</strong></span><strong><span style="color: #999999;">&nbsp; </span><br></strong></p>
                    <p><strong><img src="http://queromarita.com.br/media/imagens/myde_texto.png" alt="Myde" width="104" height="33"></strong></p>
                    <p>&nbsp;</p>
                    </body>';
                    $envio = enviar_email($txtPara, $txtAssunto, $txtMensagem);
                    if($envio==true){
                        $erro = 0;
                        $msg = "Seu email foi enviado, em breve entraremos em contato.";
                        $opt = "Toast";
                    }
                    $funcao = $fn;
                    $envia_resposta = json_encode(array("erro"=>$erro,"msg"=>$msg,"funcao"=>$funcao,"opt"=>$opt));
                    return $envia_resposta;
            break;
            case 'indicacao':
				$dados = $email;
                //var_dump($dados);
                $nome = $dados["nome"];
                $email = $dados["email"];

                $txtAssunto = "QueroMarita: Parabens,".$nome."! Você foi indicado por ".$_SESSION['user_nome'].".";
                $txtPara = array($email);
                $txtMensagem =
                '<head>
                    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
                </head>
                <body>
                <h1 id="mcetoc_1bgs8lahu0" style="text-align: left;"><img src="http://queromarita.com.br/media/imagens/Marita_logo-1-100x100.png" alt="Logo_Marita" width="100" height="100" /> &nbsp;<strong><span style="color: #3d1e04;">Quero Marita</span></strong></h1>
                <p>&nbsp;</p>
                <p><span style="font-weight: 400;">Ol&aacute; '.$nome.',  parab&eacute;ns!</span></p>
                <p>&nbsp;</p>
                <p><span style="font-weight: 400;">Recebemos uma indica&ccedil;&atilde;o sua do revendedor </span><strong>'.$_SESSION["user_nome_compl"].'</strong><span style="font-weight: 400;"> para fazer parte de um clube fechado de venda e compra dos produtos Marita.</span></p>
                <p>&nbsp;<span style="font-weight: 400;">Acesse a loja no site &nbsp;</span><a href="http://queromarita.com.br/"><span style="font-weight: 400;">queromarita.com.br</span></a><span style="font-weight: 400;"> , fa&ccedil;a o cadastro conhe&ccedil;a a linha de produtos naturais e seus benef&iacute;cios &agrave; sa&uacute;de e &agrave; est&eacute;tica. &Eacute; muito simples e r&aacute;pido. </span></p>
                <p>&nbsp;</p>
                <p>&nbsp;<span style="font-weight: 400;">Esperamos que goste, boas compras!</span></p>
                <p><span style="font-weight: 400;">Em caso de d&uacute;vidas por favor entre em contato com seu revendedor atrav&eacute;s do e-mail: </span><a href="mailto:'.$_SESSION['user_email'].'">'.$_SESSION['user_email'].'</a><span style="font-weight: 400;"> &nbsp;ou telefone: '.$_SESSION['user_cel'].'.</span></p>
                <p>&nbsp;</p>
                <p><span style="font-weight: 400;">At&eacute; breve!</span></p>
                <p><span style="font-weight: 400;">Equipe </span><a href="https://queromarita.com.br/"><span style="font-weight: 400;">queromarita.com.br</span></a></p>
                <p>&nbsp;</p>
                <p><span style="color: #999999;"><strong>Propriedade exclusiva da empresa&nbsp;</strong></span><strong><span style="color: #999999;">&nbsp; </span><br /></strong></p>
                <p><strong><img src="http://queromarita.com.br/media/imagens/myde_texto.png" alt="Myde" width="104" height="33" /></strong></p>
                <p>&nbsp;</p>
                </body>
                ';
                    $envio = enviar_email($txtPara, $txtAssunto, $txtMensagem);
                    if($envio==true){
                        $erro = 0;
                        $msg = "Seu email foi enviado, em breve entraremos em contato.";
                        $opt = "Toast";
                    }
                    $funcao = $fn;
                    $envia_resposta = json_encode(array("erro"=>$erro,"msg"=>$msg,"funcao"=>$funcao,"opt"=>$opt));
                    return $envia_resposta;
            break;
            case 'boas_vindas_comprador':
                $sql_dados_vend="select NOME, email, cel from tb_usuarios where id=".$_SESSION["user_id_distribuidor"].";";
                $consulta_vend = mysqli_query($GLOBALS['con'],$sql_dados_vend);
                $dados_vend = mysqli_fetch_row($consulta_vend);

                $txtAssunto = "QueroMarita: Seja bem-vindo";
                $txtPara = array($_SESSION['user_email']);
                $txtMensagem =
                    '<html><head>
                        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
                    </head>
                    <body><h1 id="mcetoc_1bgs8lahu0" style="text-align: left;"><img src="http://queromarita.com.br/media/imagens/Marita_logo-1-100x100.png" alt="Logo_Marita" width="100" height="100" /> &nbsp;<strong><span style="color: #3d1e04;">Quero Marita</span></strong></h1>
                    <br>
                    <p style="text-align: left;">Ol&aacute;, '.$_SESSION['user_nome'].'</p>
                    <br>
                    <p style="text-align: justify;">Estamos felizes que se juntou a fam&iacute;lia Marita e temos certeza de que nossos produtos mudar&atilde;o sua vida.</p>
                    <p style="text-align: justify;">Este site foi desenvolvido para facilitar a compra do seu produto Marita e tamb&eacute;m seu contato com o revendedor.</p>
                    <p style="text-align: justify;">Em caso de d&uacute;vidas por favor entre em contato <strong>'.$dados_vend[0].'</strong> atrav&eacute;s do telefone: <strong>'.$dados_vend[2].'</strong> do email: <a href="'.$dados_vend[1].'">'.$dados_vend[1].'</a> &nbsp;ou pelo <a href="https://queromarita.com.br/loja.html">site</a> atrav&eacute;s do nosso chat.</p>
                    <p style="text-align: justify;">Esperamos que goste dos produtos e do site. &nbsp;</p>
                    <br>
                    <p style="text-align: justify;">At&eacute; breve e boas compras !</p>
                    <p style="text-align: justify;">Equipe <a href="https://queromarita.com.br">queromarita.com.br</a></p>
                    <p>&nbsp;</p>
                    <p>&nbsp;</p>
                    <p><span style="color: #999999;"><strong>Propriedade exclusiva da empresa&nbsp;</strong></span><strong><span style="color: #999999;">&nbsp; </span><br></strong></p>
                    <p><strong><img src="http://queromarita.com.br/media/imagens/myde_texto.png" alt="Myde" width="104" height="33"></strong></p>
                    <p>&nbsp;</p>
                    </body></html>';
                    $envio = enviar_email($txtPara, $txtAssunto, $txtMensagem);
                    if($envio==true){
                        $erro = 0;
                        $msg = "Seu email foi enviado, em breve entraremos em contato.";
                        $opt = "Toast";
                    }
                    $funcao = $fn;
                    $envia_resposta = json_encode(array("erro"=>$erro,"msg"=>$msg,"funcao"=>$funcao,"opt"=>$opt));
                    return $envia_resposta;
            break;
            case 'boas_vindas_vendedor':
                $txtAssunto = "QueroMarita: Seja bem-vindo";
                $txtPara = array($_SESSION['user_email']);
                $txtMensagem =
                '<head>
                    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
                </head>
                <body><h1 id="mcetoc_1bgs8lahu0" style="text-align: left;"><img src="http://queromarita.com.br/media/imagens/Marita_logo-1-100x100.png" alt="Logo_Marita" width="100" height="100" /> &nbsp;<strong><span style="color: #3d1e04;">Quero Marita</span></strong></h1>
                <br>
                <p style="text-align: left;">Ol&aacute;, '.$_SESSION["user_nome"].'</p>
                <br>
                <p><span style="font-weight: 400;">Que bom que se juntou ao time. Nosso objetivo &eacute; alavancar suas vendas.</span></p>
                <p><span style="font-weight: 400;"><a href="https://queromarita.com.br/estoque.html">Ative aqui</a> sua loja escolhendo quais produtos deseja vender e determine por qual preço.</span></p>
                <p><span style="font-weight: 400;"><a href="https://queromarita.com.br/cadastro.html">Complete seu cadastro</a> para exibir seu cart&atilde;o de apresenta&ccedil;&atilde;o e suas redes sociais.</span></p>
                <br>
                <p><span style="font-weight: 400;">Conheça todos os nossos benefícios, <a href="https://queromarita.com.br/vendedor.html"> aqui.</a></span></p>
                <p>&nbsp;</p>
                <p><span style="font-weight: 400;">Em caso de d&uacute;vidas por favor entre em contato atrav&eacute;s dos emails: </span><a href="mailto:contato@myde.com.br">contato@myde.com.br</a> <span style="font-weight: 400;">&nbsp;ou pelo site <a href="https://queromarita.com.br">queromarita.com.br</a> atrav&eacute;s do nosso chat.</span></p>
                <br>
                <p><span style="font-weight: 400;">At&eacute; breve e boa sorte!</span></p>
                <p style="text-align: justify;">Equipe <a href="https://queromarita.com.br">queromarita.com.br</a></p>
                <p>&nbsp;</p>
                <p><span style="color: #999999;"><strong>Propriedade exclusiva da empresa&nbsp;</strong></span><strong><span style="color: #999999;">&nbsp; </span><br></strong></p>
                <p><strong><img src="http://queromarita.com.br/media/imagens/myde_texto.png" alt="Myde" width="104" height="33"></strong></p>
                <p>&nbsp;</p>
                </body>';
                    $envio = enviar_email($txtPara, $txtAssunto, $txtMensagem);
                    if($envio==true){
                        $erro = 0;
                        $msg = "Seu email foi enviado, em breve entraremos em contato.";
                        $opt = "Toast";
                    }
                    $funcao = $fn;
                    $envia_resposta = json_encode(array("erro"=>$erro,"msg"=>$msg,"funcao"=>$funcao,"opt"=>$opt));
                    return $envia_resposta;
            break;
            case 'nova_venda':

                $dados = $email;
                $id_user = $dados["id_user"];
                $id_distr = $dados["id_distr"];

                $sql_pedido="select pedido from tb_pedido_cabecalho where dt_time=(select max(dt_time) from tb_pedido_cabecalho where id_user=".$id_user." and id_distr=".$id_distr.") ;";
                $consulta_pedido = mysqli_query($GLOBALS['con'],$sql_pedido);
                $ped = mysqli_fetch_row($consulta_pedido);
                $pedido=$ped[0];

                $sql_dados_vend="select NOME, email from tb_usuarios where id=".$id_distr.";";
                $consulta_vend = mysqli_query($GLOBALS['con'],$sql_dados_vend);
                $dados_vend = mysqli_fetch_row($consulta_vend);

                $sql_dados_comp="select NOME_COMPLETO, EMAIL, cel from tb_usuarios where id=".$id_user.";";
                $consulta_comp = mysqli_query($GLOBALS['con'],$sql_dados_comp);
                $dados_comp = mysqli_fetch_row($consulta_comp);

                $txtAssunto = "QueroMarita: O pedido ".$pedido." foi efetuado por um cliente.";
                $txtPara = array($dados_vend[1]);
                $txtMensagem =
                    '<head>
                        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
                    </head>
                    <body><h1 id="mcetoc_1bgs8lahu0" style="text-align: left;"><img src="http://queromarita.com.br/media/imagens/Marita_logo-1-100x100.png" alt="Logo_Marita" width="100" height="100" /> &nbsp;<strong><span style="color: #3d1e04;">Quero Marita</span></strong></h1>
                    <br>
                    <p style="text-align: left;">Ol&aacute;, '.$dados_vend[0].', temos boas not&iacute;cias!</p>
                    <br>
                    <p><span style="font-weight: 400;">Voc&ecirc; fez uma venda. Veja os detalhes abaixo:<br></span></p>
                    <p><em><strong>PEDIDO: '.$pedido.'</strong></em></p>
                    <p><em><strong>STATUS: AGUARDANDO CONFIRMA&Ccedil;&Atilde;O DE PAGAMENTO</strong></em></p>
                    <p><em><strong>PRODUTOS:</strong></em></p>';

                    $sql_ped_item = "select t1.valor, t1.qtde, t2.titulo from  tb_pedido_itens t1 left join tb_produto_base t2 on (t1.id_produto=t2.id)   where t1.pedido = ". $pedido .";";
                    $consulta_ped_item = mysqli_query($GLOBALS['con'],$sql_ped_item);
                    $valor_pedido=0;
                    $qtde_itens=0;
                    for($j=0;$j<mysqli_num_rows($consulta_ped_item);$j++){
                        $row_ped_item = mysqli_fetch_row($consulta_ped_item);
                        $valor_pedido=$valor_pedido+$row_ped_item[0]*$row_ped_item[1];
                        $qtde_itens=$qtde_itens+$row_ped_item[1];
                        $valor_unit_format=number_format($row_ped_item[0], 2, ',', '.');
                        $valor_item_format=number_format($row_ped_item[1]*$row_ped_item[0], 2, ',', '.');
                        $txtMensagem =$txtMensagem .'<p><em><strong>'.$row_ped_item[2].',</strong></em></p>';
                    }
                    $valor_pedido_format=number_format($valor_pedido, 2, ',', '.');
                    $txtMensagem =$txtMensagem .'
                    <br>
                    <p><em><strong>CLIENTE: '.$dados_comp[0].'</strong></em></p>
                    <p><em><strong>TELEFONE: '.$dados_comp[2].'</strong></em></p>
                    <p><em><strong>EMAIL: '.$dados_comp[1].'</strong></em></p>
                    <br>
                    <p><span style="font-weight: 400;">Voc&ecirc; e o cliente ser&atilde;o notificados assim que o pagamento for confirmado.</span></p>
                    <p><span style="font-weight: 400;">Fique atento aos emails pois essa confirma&ccedil;&atilde;o deve acontecer em breve.</span></p>
                    <p><span style="font-weight: 400;">Acompanhe todos os pedidos no site <a href="https://queromarita.com.br">clique aqui.</a></span></p>
                    <p><span style="font-weight: 400;">Em caso de d&uacute;vidas por favor entre em contato atrav&eacute;s dos emails: </span><a href="mailto:contato@myde.com.br">contato@myde.com.br</a> <span style="font-weight: 400;">&nbsp;ou pelo site <a href="https://queromarita.com.br">queromarita.com.br</a> através do nosso chat.</span></p>
                    <br>
                    <p><span style="font-weight: 400;">At&eacute; breve e boa sorte!</span></p>
                    <p style="text-align: justify;">Equipe <a href="https://queromarita.com.br">queromarita.com.br</a></p>
                    <p>&nbsp;</p>
                    <p><span style="color: #999999;"><strong>Propriedade exclusiva da empresa&nbsp;</strong></span><strong><span style="color: #999999;">&nbsp; </span><br></strong></p>
                    <p><strong><img src="http://queromarita.com.br/media/imagens/myde_texto.png" alt="Myde" width="104" height="33"></strong></p>
                    <p>&nbsp;</p>
                    </body>';

                    $envio = enviar_email($txtPara, $txtAssunto, $txtMensagem);
                    if($envio==true){
                        $erro = 0;
                        $msg = "Seu email foi enviado, em breve entraremos em contato.";
                        $opt = "Toast";
                    }
                    $funcao = $fn;
                    $envia_resposta = json_encode(array("erro"=>$erro,"msg"=>$msg,"funcao"=>$funcao,"opt"=>$opt));
                    return $envia_resposta;
            break;
            case 'liberar_pedido':
				$dados = $email;
                //var_dump($dados);
                $order = $dados["order"];
                $email = $dados["email"];
                $nome = $dados["nome"];
                $envio = $dados["envio"];

                $txtAssunto = "QueroMarita: Já pode enviar os produtos do pedido [".$order."]";
                $txtPara = array($email);
                $txtMensagem =
                    '<head>
                        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
                    </head>
                    <body><h1 id="mcetoc_1bgs8lahu0" style="text-align: left;"><img src="http://queromarita.com.br/media/imagens/Marita_logo-1-100x100.png" alt="Logo_Marita" width="100" height="100" /> &nbsp;<strong><span style="color: #3d1e04;">Quero Marita</span></strong></h1>
                    <br>
                    <p style="text-align: left;">Ol&aacute;, '.$nome.', temos boas not&iacute;cias!</p>
                    <br>
                    <p><span style="font-weight: 400;">Recebemos a <strong>confirma&ccedil;&atilde;o</strong> de pagamento do pedido <strong>'.$order.'</strong></span></p>';

                if($envio=='Postar'){
                    $txtMensagem = $txtMensagem .
                        '<p><span style="font-weight: 400;"><strong><span style="color: #ff0000;">Agora voc&ecirc; j&aacute; pode <strong><a href="https://www.mercadopago.com.br/activities?type=collection&amp;status=approved">imprimir</a></strong> a etiqueta e enviar o produto ao cliente.</span></strong></span></p><strong>
                        <p><span style="font-weight: 400;">O cliente ser&aacute; notificado assim que o pedido for postado.';
                }else{
                    $sql = '
                        SELECT
                            a.Nome_completo
                            ,a.cel
                            ,a.email
                        FROM
                            tb_usuarios a
                        INNER JOIN
                            tb_pedido_cabecalho b
                        ON (a.id = b.id_user)
                        WHERE
                            b.pedido = '.$order.';';
                    include("/var/www/mp/bg_conexao_bd.php");
                    $query = mysqli_query($GLOBALS['con'],$sql);

                    if(mysqli_num_rows($query)>0){
                        $row = mysqli_fetch_row($query);
                        $txtMensagem = $txtMensagem .
                        '<p>Entre em contato com o cliente para combinar a forma de entrega:</p>
                        <p>Nome: '.$row[0].'</p>
                        <p>Celular: '.$row[1].'</p>
                        <p>Email: <a href="mailto:'.$row[2].'">'.$row[2].'</a></p>';
                    }
                }

                $txtMensagem = $txtMensagem .
                    '<br>
                    <p><span style="font-weight: 400;">At&eacute; breve e boa sorte!</span></p>
                    <p style="text-align: justify;">Equipe <a href="https://queromarita.com.br">queromarita.com.br</a></p>
                    <p>&nbsp;</p>
                    <p><span style="color: #999999;"><strong>Propriedade exclusiva da empresa&nbsp;</strong></span><strong><span style="color: #999999;">&nbsp; </span><br></strong></p>
                    <p><strong><img src="http://queromarita.com.br/media/imagens/myde_texto.png" alt="Myde" width="104" height="33"></strong></p>
                    <p>&nbsp;</p>
                    </strong></body>';

                    $envio = enviar_email($txtPara, $txtAssunto, $txtMensagem);
                    if($envio==true){
                        $erro = 0;
                        $msg = "Seu email foi enviado, em breve entraremos em contato.";
                        $opt = "Toast";
                    }
                    $funcao = $fn;
                    $envia_resposta = json_encode(array("erro"=>$erro,"msg"=>$msg,"funcao"=>$funcao,"opt"=>$opt));
                    return $envia_resposta;
            break;
            case 'novo_cliente':
                $sql_dados_vend="select NOME, email from tb_usuarios where id=".$_SESSION["user_id_distribuidor"].";";
                $consulta_vend = mysqli_query($GLOBALS['con'],$sql_dados_vend);
                $dados_vend = mysqli_fetch_row($consulta_vend);

                $sql_dados_comp="select NOME_COMPLETO, EMAIL, cel from tb_usuarios where id=".$_SESSION["user_id"].";";
                $consulta_comp = mysqli_query($GLOBALS['con'],$sql_dados_comp);
                $dados_comp = mysqli_fetch_row($consulta_comp);

                $txtAssunto = "QueroMarita: Novo cliente foi cadastrado na sua loja";
                $txtPara = array($dados_vend[1]);
                $txtMensagem =
                    '<head>
                        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
                    </head>
                    <body><h1 id="mcetoc_1bgs8lahu0" style="text-align: left;"><img src="http://queromarita.com.br/media/imagens/Marita_logo-1-100x100.png" alt="Logo_Marita" width="100" height="100" /> &nbsp;<strong><span style="color: #3d1e04;">Quero Marita</span></strong></h1>
                    <br>
                    <p style="text-align: left;">Ol&aacute;, '.$dados_vend[0].', temos boas not&iacute;cia!</p>
                    <p><span style="font-weight: 400;">Um novo cliente foi cadastrado em sua loja!</span></p>
                    <br>
                    <p><em><strong>CLIENTE: '.$dados_comp[0].'</strong></em></p>
                    <p><em><strong>TELEFONE: '.$dados_comp[2].'</strong></em></p>
                    <p><em><strong>EMAIL: '.$dados_comp[1].'</strong></em></p>
                    <br>
                    <p><span style="font-weight: 400;">Aproveite para entrar em contato com ele agora mesmo e ofere&ccedil;a seus produtos e at&eacute; mesmo para oferecer o plano de neg&oacute;cios.</span></p>
                    <p><span style="font-weight: 400;">Desejamos boas vendas!</span></p>
                    <br>
                    <p style="text-align: justify;">Equipe <a href="https://queromarita.com.br">queromarita.com.br</a></p>
                    <p>&nbsp;</p>
                    <p><span style="color: #999999;"><strong>Propriedade exclusiva da empresa&nbsp;</strong></span><strong><span style="color: #999999;">&nbsp;</span><br></strong></p>
                    <p><strong><img src="http://queromarita.com.br/media/imagens/myde_texto.png" alt="Myde" width="104" height="33"></strong></p>
                    <p>&nbsp;</p>
                    </body>';
                    $envio = enviar_email($txtPara, $txtAssunto, $txtMensagem);
                    if($envio==true){
                        $erro = 0;
                        $msg = "Seu email foi enviado, em breve entraremos em contato.";
                        $opt = "Toast";
                    }
                    $funcao = $fn;
                    $envia_resposta = json_encode(array("erro"=>$erro,"msg"=>$msg,"funcao"=>$funcao,"opt"=>$opt));
                    return $envia_resposta;

            break;
            case 'check_list':
                $txtAssunto = "QueroMarita: Instruções para continuar seu cadastro";
                $txtPara = array($_SESSION["user_email"]);
                $txtMensagem =
                    '<head>
                        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
                    </head>
                    <body>
                    <h1 id="mcetoc_1bgs8lahu0" style="text-align: left;"><img src="http://queromarita.com.br/media/imagens/Marita_logo-1-100x100.png" alt="Logo_Marita" width="100" height="100" /> &nbsp;<strong><span style="color: #3d1e04;">Quero Marita</span></strong></h1>
                    <p>&nbsp;</p>
                    <p><span style="font-weight: 400;">Ol&aacute;, '.$_SESSION["user_nome"].',</span></p>
                    <p>&nbsp;</p>
                    <p><span style="font-weight: 400;">Estamos felizes pelo seu interesse em ser um revendedor Marita.</span></p>
                    <p>&nbsp;<span style="font-weight: 400;">Para concluir sua ades&atilde;o ao site de vendas queromarita.com.br, siga os passos abaixo. </span></p>
                    <p>&nbsp;</p>
                    <ol>
                    <li style="font-weight: 400;"><span style="font-weight: 400;">Entre no site</span><span style="font-weight: 400;"> redefacil </span><span style="font-weight: 400;">e fa&ccedil;a seu cadastro como revendedor autorizado</span></li>
                    <li style="font-weight: 400;"><span style="font-weight: 400;">Acesse o site</span><span style="font-weight: 400;"> queromarita </span><span style="font-weight: 400;">&nbsp;para continuar seu cadastro e clique em <strong>j&aacute; sou um revendedor</strong> </span></li>
                    <li style="font-weight: 400;"><span style="font-weight: 400;">No site queromarita, crie uma conta no </span><span style="font-weight: 400;">mercado pago </span><span style="font-weight: 400;">ou vincule se j&aacute; tiver</span></li>
                    <li style="font-weight: 400;"><span style="font-weight: 400;">Finalize sua ades&atilde;o e crie sua loja online</span></li>
                    </ol>
                    <p>&nbsp;</p>
                    <p><span style="font-weight: 400;">Acesse e comece a vender agora mesmo! </span></p>
                    <p>&nbsp;</p>
                    <p><span style="font-weight: 400;">Desejamos boas vendas!</span></p>
                    <p>&nbsp;</p>
                    <p><strong>At&eacute; breve e boa sorte!</strong></p>
                    <p><strong>Equipe </strong><a href="https://queromarita.com.br/"><strong>queromarita.com.br</strong></a></p>
                    <p><span style="color: #999999;"><strong>Propriedade exclusiva da empresa&nbsp;</strong></span><strong><span style="color: #999999;">&nbsp; </span><br /></strong></p>
                    <p><strong><img src="http://queromarita.com.br/media/imagens/myde_texto.png" alt="Myde" width="104" height="33" /></strong></p>
                    <p>&nbsp;</p>
                    </body>';
                    $envio = enviar_email($txtPara, $txtAssunto, $txtMensagem);
                    if($envio==true){
                        $erro = 0;
                        $msg = "Seu email foi enviado, em breve entraremos em contato.";
                        $opt = "Toast";
                    }
                    $funcao = $fn;
                    $envia_resposta = json_encode(array("erro"=>$erro,"msg"=>$msg,"funcao"=>$funcao,"opt"=>$opt));
                    return $envia_resposta;
            break;
        }
    }

    function enviar_email($para, $assunto, $corpo){
        // Extender a classe do phpmailer para envio do email
        require_once("PHPMailer/class.phpmailer.php");

        $assunto = '=?UTF-8?B?'.base64_encode($assunto).'?=';

        // Definir Usuário e Senha do Gmail de onde partirá os emails
        $de = 'contato@myde.com.br';
        $nomeRemetente = 'QueroMarita (MYDE)';
        define('GUSER', 'contato@myde.com.br');
        define('GPWD', 'MAILqsenha123');

        global $error;
        $mail = new PHPMailer();
        // Montando o Email
        $mail->IsSMTP();            // Ativar SMTP
        $mail->SMTPDebug = 0;        // Debugar: 1 = erros e mensagens, 2 = mensagens apenas
        $mail->SMTPAuth = true;        // Autenticação ativada
        $mail->SMTPSecure = 'tls';    // TLS REQUERIDO pelo GMail
        //$mail->SMTPSecure = 'ssl';    // TLS REQUERIDO pelo GMail
        $mail->Host = 'smtp.gmail.com';    // SMTP utilizado
        $mail->Port = 587;             // A porta 587 deverá estar aberta em seu servidor
        //$mail->Port = 465;            // A porta 587 deverá estar aberta em seu servidor
        $mail->Username = GUSER;
        $mail->Password = GPWD;
        $mail->SetFrom($de, $nomeRemetente);
        //$mail->AddReplyTo($de);
        $mail->Subject = $assunto;
        $mail->Body = $corpo;
        foreach ($para as $destinatario){
            $mail->AddAddress($destinatario);
        }
        $mail->AddBCC("contato@myde.com.br", "Controle MYDE");
        $mail->IsHTML(true);

        // Função Responsável por Enviar o Email
        if(!$mail->Send()){
            //$error = "<font color='red'><b>Mail error: </b></font>".$mail->ErrorInfo;
            return false;
        }else{
            //$error = "<font color='blue'><b>Mensagem enviada com Sucesso!</b></font>";
            return true;
        }
    }

    //$msg = utf8_encode($msg);
	//print json_encode($envia_resposta);
?>
