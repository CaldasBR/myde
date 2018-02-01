<?php
    //ini_set('display_errors','on');
	//error_reporting(E_ALL);

	header('Content-Type: text/html; charset=utf-8');

    $funcao = "graficos";
    $erro = 0;
    $msg = "Nada foi executado!";
    $opt = "";

	include("/var/www/makebit/bg_funcoes_genericas.php");

    if(isset($_GET["fn"]) || isset($_POST["fn"])){
        if(isset($_GET["fn"])){
            $fn = addslashes($_GET["fn"]);
        }else{
            $fn = addslashes($_POST["fn"]);
        }

        switch ($fn){
            case 'candlestick_moeda':
				if(isset($_GET["moeda"]) || isset($_POST["moeda"])){
			        if(isset($_GET["moeda"])){
		            	$moeda = addslashes($_GET["moeda"]);
					}else{
						$moeda = addslashes($_POST["moeda"]);
					}
		        }else{
		            $moeda = "";
		        }
				if(isset($_GET["janela"]) || isset($_POST["janela"])){
			        if(isset($_GET["janela"])){
		            	$janela = addslashes($_GET["janela"]);
					}else{
						$janela = addslashes($_POST["janela"]);
					}
		        }else{
		            $janela = "";
		        }

                $sql = "select moeda, dt_cotacao,prc_minimo, prc_maximo, prc_soma,
				 qtde_ordens, qtde_traded, prc_media, prc_abertura, prc_fechamento
				 from bithumb_transactions where moeda = '".$moeda."' order by dt_cotacao asc ;";

                //echo "SQL: ".$sql;
                include("/var/www/makebit/bg_conexao_bd.php");
                $query = mysqli_query($GLOBALS['con'],$sql);

				$vet_tempo = [];

				$vet_prc_min_analit = [];
				$vet_prc_max_analit = [];
				$vet_prc_soma_analit = [];
				$vet_prc_qtde_ordens_analit = [];
				$vet_prc_qtde_traded_analit = [];
				$vet_prc_abertura_analit = [];
				$vet_prc_fechamento_analit = [];

				$vet_tempo_agrup = [];
				$vet_prc_min_agrup = [];
				$vet_prc_max_agrup = [];
				$vet_prc_soma_agrup = [];
				$vet_prc_qtde_ordens_agrup = [];
				$vet_prc_qtde_traded_agrup = [];
				$vet_prc_abertura_agrup = [];
				$vet_prc_fechamento_agrup = [];

                if(mysqli_num_rows($query)>0){
					for($i=0;$i<mysqli_num_rows($query);$i++){
						$row = mysqli_fetch_row($query);
						//var_dump($row);
						$linha = array(
							"moeda"=>$row[0]
							,"dt_cotacao"=>$row[1]
							,"prc_minimo"=>$row[2]
							,"prc_maximo"=>$row[3]
							,"prc_soma"=>$row[4]
							,"qtde_ordens"=>$row[5]
							,"qtde_traded"=>$row[6]
							,"prc_media"=>$row[7]
							,"prc_abertura"=>$row[8]
							,"prc_fechamento"=>$row[9]
						);

						array_push($vet_tempo,$row[1]);
						array_push($vet_prc_min_analit,$row[2]);
						array_push($vet_prc_max_analit,$row[3]);
						array_push($vet_prc_soma_analit,$row[4]);
						array_push($vet_prc_qtde_ordens_analit,$row[5]);
						array_push($vet_prc_qtde_traded_analit,$row[6]);
						array_push($vet_prc_abertura_analit,$row[8]);
						array_push($vet_prc_fechamento_analit,$row[9]);
					}

					$novo_vet_tempo = agrupa_tempo('dias', 1, $vet_tempo);

					//Função para agrupar as referências iguais (com médias, somas, contagens, máximos, minimos, etc)
					for($i = 0; $i < sizeof($novo_vet_tempo);$i++){
						if($i<1 || $novo_vet_tempo[$i]!=$novo_vet_tempo[$i-1]){
							if($i>=1){
								$vet_tempo_agrup = [];
								$vet_prc_min_agrup = [];
								$vet_prc_max_agrup = [];
								$vet_prc_soma_agrup = [];
								$vet_prc_qtde_ordens_agrup = [];
								$vet_prc_qtde_traded_agrup = [];
								$vet_prc_abertura_agrup = [];
								$vet_prc_fechamento_agrup = [];

								array_push($vet_tempo_agrup,$novo_vet_tempo[$i-1]);
								array_push($vet_prc_min_agrup,min_arr($vet_prc_min_analit_temp));
								array_push($vet_prc_max_agrup,max_arr($vet_prc_max_analit_temp));
								array_push($vet_prc_soma_agrup,sum_arr($vet_prc_soma_analit_temp));
								array_push($vet_prc_qtde_ordens_agrup,sum_arr($vet_prc_qtde_ordens_analit_temp));
								array_push($vet_prc_qtde_traded_agrup,sum_arr($vet_prc_qtde_traded_analit_temp));
								array_push($vet_prc_abertura_agrup,first_arr($vet_prc_abertura_analit_temp));
								array_push($vet_prc_fechamento_agrup,last_arr($vet_prc_fechamento_analit_temp));
							}
							$vet_prc_min_analit_temp = [];
							$vet_prc_max_analit_temp = [];
							$vet_prc_soma_analit_temp = [];
							$vet_prc_qtde_ordens_analit_temp = [];
							$vet_prc_qtde_traded_analit_temp = [];
							$vet_prc_abertura_analit_temp = [];
							$vet_prc_fechamento_analit_temp = [];
						}else{
							array_push($vet_prc_min_analit_temp,$vet_prc_min_analit[$i]);
							array_push($vet_prc_max_analit_temp,$vet_prc_max_analit[$i]);
							array_push($vet_prc_soma_analit_temp,$vet_prc_soma_analit[$i]);
							array_push($vet_prc_qtde_ordens_analit_temp,$vet_prc_qtde_ordens_analit[$i]);
							array_push($vet_prc_qtde_traded_analit_temp,$vet_prc_qtde_traded_analit[$i]);
							array_push($vet_prc_abertura_analit_temp,$vet_prc_abertura_analit[$i]);
							array_push($vet_prc_fechamento_analit_temp,$vet_prc_fechamento_analit[$i]);
						}
					}

					$consolida_msg = [];
					//Reagrupa em um unico vetor
					for($i = 0; $i < sizeof($vet_tempo_agrup);$i++){
						$linha = array(
							"moeda"=>$row[0]
							,"dt_cotacao"=>$vet_tempo_agrup[$i]
							,"prc_minimo"=>$vet_prc_min_agrup[$i]
							,"prc_maximo"=>$vet_prc_max_agrup[$i]
							,"prc_soma"=>$vet_prc_soma_agrup[$i]
							,"qtde_ordens"=>$vet_prc_qtde_ordens_agrup[$i]
							,"qtde_traded"=>$vet_prc_qtde_traded_agrup[$i]
							,"prc_media"=>$vet_tempo_agrup[$i]
							,"prc_abertura"=>$vet_prc_abertura_agrup[$i]
							,"prc_fechamento"=>$vet_prc_fechamento_agrup[$i]
						);
						array_push($consolida_msg,$linha);
					}

					echo "<br><br>";
					var_dump($vet_tempo_agrup);
					echo "<br><br>";
					var_dump($vet_prc_min_agrup);
					echo "<br><br>";
					var_dump($vet_prc_max_agrup);
					echo "<br><br>";
					var_dump($vet_prc_soma_agrup);
					echo "<br><br>";
					var_dump($vet_prc_qtde_ordens_agrup);
					echo "<br><br>";
					var_dump($vet_prc_qtde_traded_agrup);
					echo "<br><br>";
					var_dump($vet_prc_abertura_agrup);
					echo "<br><br>";
					var_dump($vet_prc_fechamento_agrup);
					echo "<br><br>";

					$erro=0;
					$msg = $consolida_msg;
					$opt='exibe_array';
                }else{
                    $erro=1;
                    $msg='Sem dados na consulta.';
                    $opt='Toast';
                }
            break;
		}
	}

    $envia_resposta = array("erro"=>$erro,"msg"=>$msg,"funcao"=>$funcao,"opt"=>$opt);
    print json_encode($envia_resposta, JSON_PRETTY_PRINT);
?>
