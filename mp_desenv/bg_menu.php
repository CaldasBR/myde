<?php
    ini_set('display_errors','on');
	error_reporting(E_ALL);

	header('Content-Type: text/html; charset=utf-8');

    $funcao = "menu";
    $erro = 0;
    $msg = "Nada foi executado!";
    $opt = "";
    $opt2 = "";

    //Verifica se usuário está Logado
    include("/var/www/mp_desenv/bg_protege_php.php");
    $status_log = status_login($resposta); //acredite que $resposta existe, ele está no bg_protege_php
    //echo "status_log: ".$status_log."<br>";
    
    //Se estiver logado executa o código
    if($status_log=="mantem"){
        if(isset($_GET["fn"]) || isset($_POST["fn"])){
            if(isset($_GET["fn"])){
                $fn = addslashes($_GET["fn"]);
            }else{
                $fn = addslashes($_POST["fn"]);
            }
            
            switch ($fn){
                case 'le_menu':
                    switch($_SESSION["user_access"]){
                        case 'administrador':
                        $msg = '
                            <div class="nav-wrapper">
                                <!-- Logotipo -->
                                <a id="logo-container" href="index.html" class="brand-logo left">
                                    <img id="img_logo" src="media/imagens/Marita_logo-1-100x100.png" alt="Quero Marita">
                                </a>
                                <!--icone menu celular -->
                                <a href="#" data-activates="nav-mobile" class="button-collapse right"><i class="material-icons">menu</i></a>
                                <!-- Navbar Computador -->
                                <ul class="right hide-on-med-and-down">
                                    <li><a class="dropdown-button" href="#" data-activates="dropdown1"><i class="material-icons left ">perm_identity</i>Conta de '.$_SESSION["user_nome"].'</a></li>
                                    <li><a class="dropdown-button" href="#" data-activates="dropdown2"><i class="material-icons left ">assessment</i>Meu escritório</a></li>
                                    <li><a href="javascript:void(0);" onclick="window.open(\'https://dashboard.tawk.to/login\');"><i class="material-icons left ">textsms</i>Tawk.to</a></li>
                                    <li><a href="javascript:void(0);" onclick="window.open(\'https://www.mercadopago.com.br\');"><i class="material-icons left ">payment</i>Mercado Pago</a></li>
                                </ul>
                                <!-- Sidebar Celular com collapsible -->
                                <ul class="side-nav collapsible" data-collapsible="accordion" id="nav-mobile">
                                    <li>
                                        <a class="collapsible-header waves-effect waves-teal active"><i class="material-icons left ">perm_identity</i>Conta de '.$_SESSION["user_nome"].'</a>
                                        <div class="collapsible-body" style="display: block;">
                                            <ul>
                                                <li><a href="cadastro.html">Meus dados</a></li>
                                                <li><a href="indicados.html">Convidar amigos</a></li>
                                                <!--<li class="divider"></li>-->
                                                <li><a href="bg_logout.php">Sair</a></li>
                                            </ul>
                                        </div>
                                    </li>
                                    <li>
                                        <a class="collapsible-header waves-effect waves-teal active"><i class="material-icons left ">assessment</i>Meu escritório</a>
                                        <div class="collapsible-body" style="display: block;">
                                            <ul>
                                                <li><a href="relatorio_vendas.html">Relatórios</a></li>
                                                <li><a href="editor.html">Editor</a></li>
                                                <li><a href="loja.html">Loja</a></li>
                                                <li><a href="javascript:void(0);" onclick="verificar_sacola();">Carrinho</a></li>
                                                <li><a href="pedidos.html">Pedidos</a></li>
                                                <li><a href="estoque.html">Estoque</a></li>
                                                <li><a href="formulario.html">Contato</a></li>
                                            </ul>
                                        </div>
                                    </li>
                                    <li><a class="collapsible-header waves-effect waves-teal active" href="javascript:void(0);" onclick="window.open(\'https://dashboard.tawk.to/login\');"><i class="material-icons left ">textsms</i>Tawk.to</a></li>
                                    <li><a class="collapsible-header waves-effect waves-teal active" href="javascript:void(0);" onclick="window.open(\'https://www.mercadopago.com.br\');"><i class="material-icons left ">payment</i>Mercado Pago</a></li>
                                </ul>
                            </div>
                            <!-- Dropdown Computador -->
                            <ul id="dropdown1" class="dropdown-content">
                                <li><a href="cadastro.html">Meus dados</a></li>
                                <li><a href="indicados.html">Convidar amigos</a></li>
                                <li class="divider"></li>
                                <li><a href="bg_logout.php">Sair</a></li>
                            </ul>
                            <ul id="dropdown2" class="dropdown-content">
                                <li><a href="relatorio_vendas.html">Relatórios</a></li>
                                <li><a href="editor.html">Editor</a></li>
                                <li><a href="loja.html">Loja</a></li>
                                <li><a  href="javascript:void(0);" onclick="verificar_sacola();">Carrinho</a></li>
                                <li><a href="pedidos.html">Pedidos</a></li>
                                <li><a href="estoque.html">Estoque</a></li>
                                <li><a href="formulario.html">Contato</a></li>
                            </ul>
                            ';
                        break;
                        case 'vendedor':
                        $msg = '
                            <div class="nav-wrapper">
                                <!-- Logotipo -->
                                <a id="logo-container" href="index.html" class="brand-logo left">
                                    <img id="img_logo" src="media/imagens/Marita_logo-1-100x100.png" alt="Quero Marita">
                                </a>
                                <!--icone menu celular -->
                                <a href="#" data-activates="nav-mobile" class="button-collapse right"><i class="material-icons">menu</i></a>
                                <!-- Navbar Computador -->
                                <ul class="right hide-on-med-and-down">
                                    <li><a class="dropdown-button" href="#" data-activates="dropdown1"><i class="material-icons left ">perm_identity</i>Conta de '.$_SESSION["user_nome"].'</a></li>
                                    <li><a class="dropdown-button" href="#" data-activates="dropdown2"><i class="material-icons left ">assessment</i>Meu escritório</a></li>
                                </ul>
                                <!-- Sidebar Celular com collapsible -->
                                <ul class="side-nav collapsible" data-collapsible="accordion" id="nav-mobile">
                                    <li>
                                        <a class="collapsible-header waves-effect waves-teal active"><i class="material-icons left ">perm_identity</i>Conta de '.$_SESSION["user_nome"].'</a>
                                        <div class="collapsible-body" style="display: block;">
                                            <ul>
                                                <li><a href="cadastro.html">Meus dados</a></li>
                                                <li><a href="indicados.html">Convidar amigos</a></li>
                                                <li class="divider"></li>
                                                <li><a href="bg_logout.php">Sair</a></li>
                                            </ul>
                                        </div>
                                    </li>
                                    <li>
                                        <a class="collapsible-header waves-effect waves-teal active"><i class="material-icons left ">assessment</i>Meu escritório</a>
                                        <div class="collapsible-body" style="display: block;">
                                            <ul>
                                                <li><a href="relatorio_vendas.html">Relatórios</a></li>
                                                <li><a href="estoque.html">Produtos/Estoque</a></li>
                                                <li><a href="loja.html">Minha Loja</a></li>
                                                <li><a href="pedidos.html">Pedidos</a></li>
                                                <li><a href="formulario.html">Fale com QueroMarita</a></li>
                                                <li><a href="javascript:void(0);" onclick="window.open(\'https://dashboard.tawk.to/login\');">Chat</a></li>
                                                <li><a href="javascript:void(0);" onclick="window.open(\'https://www.mercadopago.com.br\');">Mercado Pago</a></li>
                                            </ul>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                            <!-- Dropdown Computador -->
                            <ul id="dropdown1" class="dropdown-content">
                                <li><a href="cadastro.html">Meus dados</a></li>
                                <li><a href="indicados.html">Convidar amigos</a></li>
                                <li class="divider"></li>
                                <li><a href="bg_logout.php">Sair</a></li>
                            </ul>
                            <ul id="dropdown2" class="dropdown-content">
                                <li><a href="relatorio_vendas.html">Relatórios</a></li>
                                <li><a href="estoque.html">Produtos/Estoque</a></li>
                                <li><a href="loja.html">Minha Loja</a></li>
                                <li><a href="pedidos.html">Pedidos</a></li>
                                <li><a href="formulario.html">Fale com QueroMarita</a></li>
                                <li><a href="javascript:void(0);" onclick="window.open(\'https://dashboard.tawk.to/login\');">Chat</a></li>
                                <li><a href="javascript:void(0);" onclick="window.open(\'https://www.mercadopago.com.br\');">Mercado Pago</a></li>
                            </ul>
                            ';
                        break;
                        case 'default':
                        $msg = '
                            <div class="nav-wrapper">
                                <!-- Logotipo -->
                                <a id="logo-container" href="index.html" class="brand-logo left">
                                    <img id="img_logo" src="media/imagens/Marita_logo-1-100x100.png" alt="Quero Marita">
                                </a>
                                <!--icone menu celular -->
                                <a href="#" data-activates="nav-mobile" class="button-collapse right"><i class="material-icons">menu</i></a>
                                <!-- Navbar Computador -->
                                <ul class="right hide-on-med-and-down">
                                    <li><a class="dropdown-button" href="#" data-activates="dropdown1"><i class="material-icons left ">perm_identity</i>Conta de '.$_SESSION["user_nome"].'</a></li>
                                    
                                    <li><a href="javascript:void(0);" onclick="verificar_sacola();"><i class="material-icons left ">shopping_cart</i>Carrinho</a></li>
                                    <li><a href="vendedor.html">Quero ser Vendedor</a></li>
                                </ul>
                                <!-- Sidebar Celular com collapsible -->
                                <ul class="side-nav collapsible" data-collapsible="accordion" id="nav-mobile">
                                    <li class="active">
                                        <a class="collapsible-header waves-effect waves-teal active"><i class="material-icons left ">perm_identity</i>Conta de '.$_SESSION["user_nome"].'</a>
                                        <div class="collapsible-body" style="display: block;">
                                            <ul>
                                                <li><a href="cadastro.html">Meus dados</a></li>
                                                <li><a href="pedidos.html">Meus pedidos</a></li>
                                            <!--<li><a href="indicados.html">Convidar amigos</a></li>   -->
                                                <li class="divider"></li>
                                                <li><a href="bg_logout.php">Sair</a></li>
                                            </ul>
                                        </div>
                                    </li>
                                    <li><a href="javascript:void(0);" onclick="verificar_sacola();">Carrinho</a></li>
                                    <li><a class="collapsible-header waves-effect waves-teal active" href="#fale_distrib"><i class="material-icons left ">mail</i>Fale com seu distribuidor</a></li>
                                </ul>
                            </div>
                            <!-- Dropdown Computador -->
                            <ul id="dropdown1" class="dropdown-content">
                                <li><a href="cadastro.html">Meus dados</a></li>
                                <li><a href="/pedidos.html">Meus pedidos</a></li>
                                <!--<li><a href="indicados.html">Convidar amigos</a></li>-->
                                <li class="divider"></li>
                                <li><a href="bg_logout.php">Sair</a></li>
                            </ul>
                            ';
                        break;
                        default:
                            $msg = '<div class="nav-wrapper">
                                <a id="logo-container" href="#" class="brand-logo left">
                                    <img id="img_logo" src="media/imagens/Marita_logo-1-100x100.png" alt="Quero Marita">
                                </a> 
                                <ul class="right hide-on-med-and-down">
                                    <li class="active"><a href="loja.html">Entrar</a></li>
                                    <!-- <li><a href="https://www.redefacilbrasil.com.br/web/cadastro?p=filipe_caldas@msn.com">Cadastrar</a></li>-->
                                    <li><a href="vendedor.html">Seja um revendedor</a></li>

                                </ul>

                                <ul id="nav-mobile" class="side-nav">
                                    <li><a href="loja.html">Comprar</a></li>
                                    <!--<li><a href="https://www.redefacilbrasil.com.br/web/cadastro?p=filipe_caldas@msn.com">Cadastrar</a></li>-->
                                    <li><a href="vendedor.html">Distribuidor</a></li>

                                </ul>


                                <a href="#" data-activates="nav-mobile" class="button-collapse right"><i class="material-icons">menu</i></a>
                            </div>';
                        break;
                    }
                    $erro = 0;
                    $opt = "atualizar_menu";
                break;
            }
        }else{
            $erro = 1;
            $msg = "Solictação Incorreta";
            $opt = "expulsa";
        }
    }else{
        //executa quando o usuário nao está logado
        $erro = 1;
        $msg = '<div class="nav-wrapper">
                    <a id="logo-container" href="#" class="brand-logo left">
                        <img id="img_logo" src="media/imagens/Marita_logo-1-100x100.png" alt="Quero Marita">
                    </a> 
                    <ul class="right hide-on-med-and-down">
                        <li class="active"><a href="loja.html">Entrar</a></li>
                        <!-- <li><a href="https://www.redefacilbrasil.com.br/web/cadastro?p=filipe_caldas@msn.com">Cadastrar</a></li>-->
                        <li><a href="vendedor.html">Seja um revendedor</a></li>

                    </ul>

                    <ul id="nav-mobile" class="side-nav">
                        <li><a href="loja.html">Comprar</a></li>
                        <!--<li><a href="https://www.redefacilbrasil.com.br/web/cadastro?p=filipe_caldas@msn.com">Cadastrar</a></li>-->
                        <li><a href="vendedor.html">Distribuidor</a></li>

                    </ul>


                    <a href="#" data-activates="nav-mobile" class="button-collapse right"><i class="material-icons">menu</i></a>
                </div>';
        $opt = "expulsa";
    }

    $envia_resposta = array("erro"=>$erro,"msg"=>$msg,"funcao"=>$funcao,"opt"=>$opt,"opt2"=>$opt2);
    print json_encode($envia_resposta, JSON_PRETTY_PRINT);
?>