<?php
    //ini_set('display_errors','on');
	//error_reporting(E_ALL);

    session_start();
	header('Content-Type: text/html; charset=utf-8');

    $funcao = "localiza_distr";
    $erro = 0;
    $msg = "Nada foi executado!";
    $opt = "";

    if(isset($_POST["fn"])){
        $fn = addslashes($_POST["fn"]);
        $funcao = $fn;
        switch ($fn){
            case 'localiza_distr':
                if(isset($_POST["uf"])){
                    $uf = addslashes($_POST["uf"]);
                    $sql = "select uf, cidade, nome_completo, imagem, id from tb_usuarios where uf='".$uf."' and access_group in ('vendedor', 'administrador');";
                    //echo "SQL: " . $sql;
                    
                    include("/var/www/mp/bg_conexao_bd.php");
                    $query = mysqli_query($GLOBALS['con'],$sql);

                    if(mysqli_num_rows($query)<1){
                        $sql = "select uf, cidade, nome_completo, imagem, id from tb_usuarios where access_group in ('administrador');";
                        //echo "SQL: " . $sql;
                        include("/var/www/mp/bg_conexao_bd.php");
                        $query = mysqli_query($GLOBALS['con'],$sql);
                    }
                    
                    $tmp_msg = '<ul class="collection">';
                    for($i=0;$i<mysqli_num_rows($query);$i++){
                        $row = mysqli_fetch_row($query);
                        $tmp_msg = $tmp_msg .
                            '<a href="javascript:void(0);" onclick="seleciona_distr('.$row[4].')">
                                <li class="collection-item avatar">
                                    <img src="'.$row[3].'" alt="" class="circle">
                                    <span class="title">'.utf8_decode($row[2]).'</span>
                                    <p>'.utf8_decode($row[1]).' - ' . utf8_decode($row[0]) . '</p>
                                    <a href="javascript:void(0);" onclick="seleciona_distr('.$row[4].')" class="secondary-
                                    content"></a>
                                 </li>
                            </a>';
                    }
                    $tmp_msg = $tmp_msg . ' </ul>';
                    $opt = "seleciona_distr";
                    $msg=utf8_encode($tmp_msg);
                }
            break;
        }
    }

    $envia_resposta = array("erro"=>$erro,"msg"=>$msg,"funcao"=>$funcao,"opt"=>$opt);
	print json_encode($envia_resposta);
?>