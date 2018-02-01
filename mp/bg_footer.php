<?php
    //ini_set('display_errors','on');
	//error_reporting(E_ALL);

	header('Content-Type: text/html; charset=utf-8');

    $funcao = "rodape";
    $erro = 0;
    $msg = "Nada foi executado!";
    $opt = "";
    $opt2 = "";

    //Verifica se usuário está Logado
    include("/var/www/mp/bg_protege_php.php");
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
                case 'le_rodape':
                    switch($_SESSION["user_access"]){
                        case 'administrador':
                        case 'vendedor':
                        case 'default':
                            if(isset($_SESSION['user_id_distribuidor'])){
                                $id_distribuidor = addslashes($_SESSION["user_id_distribuidor"]);
                                //echo "ID: ".$id;
                            }else{
                                $erro = 1;
                                $msg = "Nenhum distribuidor selecionado";
                            }
                            //Exibe o detalhe de um determinado anúncio
                            $sql_consulta = 
                                "select
                                    nome_completo
                                    , email
                                    , cel
                                    , pg_facebook
                                    , IMAGEM
                                    , txt_apres
                                from 
                                    tb_usuarios 
                                where 
                                    ID=".$id_distribuidor.";";
                            $consulta = mysqli_query($GLOBALS['con'],$sql_consulta);     
                            $dados = mysqli_fetch_row($consulta);
                                $msg = '<div class="container">
                                <div class="row">
                                    <div class="col l4 s12">
                                        <h5 class="white-text">Sobre nós</h5>
                                        <p class="grey-text text-lighten-2">Somos um site de vendas de produtos da empresa MYDE e desejamos auxiliar você, distribuidor e cliente Marita a ter sucesso em sua vida e negócios.</p>
                                    </div>
                                    <div class="col l3 s12">
                                        <h5 class="white-text">Dúvidas</h5>
                                        <ul>
                                            <li><a href="formulario.html" class="grey-text text-lighten-2" ><u>Entre em contato</u></a></li>
                                            <li><a href="https://www.redefacilbrasil.com.br/web/universidade/apresentacao/" class="grey-text text-lighten-2" ><u>Universidade Rede Fácil</u></a></li>
                                        </ul>
                                    </div>

                                    <div id="distribuidor" class="col l5 s12">
                                        <h5 class="white-text"><a name="fale_distrib" class="white-text">Fale com seu distribuidor</a></h5>
                                        <div class="card small">
                                            <div class="card-image waves-effect waves-block waves-light">
                                                <img id=img_dist_card src="'.$dados[4].'">
                                            </div>
                                            <div class="card-content">
                                                <span class="card-title activator grey-text text-darken-4" id="nome_dist_card">'.$dados[0].'<i class="material-icons right">more_vert</i></span>
                                                <p id="cel_dist_card">cel: '.$dados[2].'</p>
                                                <p>Distribuidor Independente</p>

                                            </div>
                                            <div class="card-reveal">
                                                <span class="card-title grey-text text-darken-4" id="nome_dist_card1">'.$dados[0].'<i class="material-icons right">close</i></span>
                                                <p id="txt_apres_dist_card">'.$dados[5].'</p>
                                                <p id="face_dist_card">face: <a href="javascript:void(0);"onclick="window.open(\''.$dados[3].'\');">'.$dados[3].'</a></p>
                                                <p id="cel_dist_card">cel: '.$dados[2].'</p>
                                                <p id="email_dist_card">email: <a href="mailto:'.$dados[1].'">'.$dados[1].'</a></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <div class="footer-copyright">
                                <div class="container center-align">
                                    Desenvolvimento e propriedade de 
                                        <a class="orange-text text-lighten-3" href="http://www.myde.com.br">
                                            <img src="media/imagens/logo-white-myde-c-2.png" style="height: 30px; width:70px; position: relative; top:10px;">
                                        </a>
                                    . Todos os direitos reservados. Cópia parcial ou total não autorizada.
                                    <br>
                                    <div id="selos" style="margin-top: 30px;">
                                    </div>
                                </div>
                            </div>';
                            break;   
                        default:
                            $msg = '<div class="container">
                                <div class="row">
                                    <div class="col l4 s12">
                                        <h5 class="white-text">Sobre nós</h5>
                                        <p class="grey-text text-lighten-2">Somos um site de vendas de produtos da empresa MYDE e desejamos auxiliar você, distribuidor e cliente Marita a ter sucesso em sua vida e negócios.</p>
                                    </div>
                                    <div class="col l3 s12">
                                        <h5 class="white-text">Dúvidas</h5>
                                        <ul>
                                            <li><a href="formulario.html" class="grey-text text-lighten-2" ><u>Entre em contato</u></a></li>
                                            <li><a href="https://www.redefacilbrasil.com.br/web/universidade/apresentacao/" class="grey-text text-lighten-2" ><u>Universidade Rede Fácil</u></a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>


                            <div class="footer-copyright">
                                <div class="container center-align">
                                    Desenvolvimento e propriedade de 
                                        <a class="orange-text text-lighten-3" href="http://www.myde.com.br">
                                            <img src="media/imagens/logo-white-myde-c-2.png" style="height: 30px; width:70px; position: relative; top:10px;">
                                        </a>
                                    . Todos os direitos reservados. Cópia parcial ou total não autorizada.
                                    <br>
                                    <div id="selos" style="margin-top: 30px;">
                                    </div>
                                </div>
                            </div>';
                            break; 
                            
                    }
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
        $msg = '<div class="container">
                                <div class="row">
                                    <div class="col l4 s12">
                                        <h5 class="white-text">Sobre nós</h5>
                                        <p class="grey-text text-lighten-2">Somos um site de vendas de produtos da empresa MYDE e desejamos auxiliar você, distribuidor e cliente Marita a ter sucesso em sua vida e negócios.</p>
                                    </div>
                                    <div class="col l3 s12">
                                        <h5 class="white-text">Dúvidas</h5>
                                        <ul>
                                            <li><a href="formulario.html" class="grey-text text-lighten-2" ><u>Entre em contato</u></a></li>
                                            <li><a href="https://www.redefacilbrasil.com.br/web/universidade/apresentacao/" class="grey-text text-lighten-2" ><u>Universidade Rede Fácil</u></a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>


                            <div class="footer-copyright">
                                <div class="container center-align">
                                    Desenvolvimento e propriedade de 
                                        <a class="orange-text text-lighten-3" href="http://www.myde.com.br">
                                            <img src="media/imagens/logo-white-myde-c-2.png" style="height: 30px; width:70px; position: relative; top:10px;">
                                        </a>
                                    . Todos os direitos reservados. Cópia parcial ou total não autorizada.
                                    <br>
                                    <div id="selos" style="margin-top: 30px;">
                                    </div>
                            </div>';
        $opt = "expulsa";
    }

    $envia_resposta = array("erro"=>$erro,"msg"=>$msg,"funcao"=>$funcao,"opt"=>$opt,"opt2"=>$opt2);
    print json_encode($envia_resposta, JSON_PRETTY_PRINT);
?>