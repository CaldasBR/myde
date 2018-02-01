<?php
	header('Content-Type: text/html; charset=utf-8');

	//ini_set('display_errors','on');
	//error_reporting(E_ALL);

	session_start();

	$string="Nada foi executado";
	// ### Include com possibilidade de conexão com banco de dados ###
	require_once "/var/www/mp_desenv/bg_conexao_bd.php";

	function onLogin(){
		// Include detect.
		require_once '/var/www/mp_desenv/bg_biblioteca_detect.php';
	
		if(isset($_SESSION['user_id'])){
			$user_id = $_SESSION['user_id'];
			
			$deviceType = Detect::deviceType();
			$ip = Detect::ip();
			$ipHostname = Detect::ipHostname();
			$ipOrg = Detect::ipOrg();
			$ipCountry = Detect::ipCountry();
			$os = Detect::os();
			$browser = Detect::browser();
			$brand=Detect::brand();
			$UID = $_SERVER['HTTP_USER_AGENT']."-->".$os;
			$token = rand(0,9).rand(0,9).rand(0,9).rand(0,9).rand(0,9).rand(0,9).rand(0,9).rand(0,9).rand(0,9);
			$ID_sha256 = hash_hmac('sha256', $user_id, "ninguempoderoubar");
			$UID_sha256 = hash_hmac('sha256', $UID, "ninguempoderoubar");
			$token_sha256 = hash_hmac('sha256', $token, "ninguempoderoubar");
			$KEY = hash_hmac('sha256', $ID_sha256.$token_sha256.$UID_sha256, "ninguempoderoubar");
			$sql="INSERT INTO tb_login (id_user,dt_time, token, IP, device_type, ipHostname, ipOrg, ipCountry, os, browser, brand, UID, KEY_login, status)
					SELECT * FROM(
                        SELECT ".$user_id.",now(),'".$token_sha256."','".$ip."','".$deviceType."','".$ipHostname."','".$ipOrg."','".$ipCountry."','".$os."','".$browser."','".$brand."','".$UID."','".$KEY."','login')
                    tmp
					WHERE NOT EXISTS (SELECT id FROM tb_login WHERE id_user=".$user_id." and token='".$token_sha256."' and KEY_login='".$KEY."' and status<>'logoff');";
			$consulta=mysqli_query($GLOBALS['con'], $sql);
            $consulta=mysqli_query($GLOBALS['con'], $sql);
            //echo "SQL: ".$sql."<br>";
			setcookie('MYDE_REMENBERME_ID', $ID_sha256,time() + (86400 * 30), "/","desenv.queromarita.com.br");
			setcookie('MYDE_REMENBERME_TOKEN', $token_sha256,time() + (86400 * 30), "/","desenv.queromarita.com.br");
            //echo "Criei";
			$string = 'redirect';
            //echo $string;
			return $string;
            
		}else{
			$string = 'Usuário não autenticado.';
            expulsaVisitante();
            //$string = 'index.html';
			//echo $string;
            return $string;
			//echo("redirect 1");
		}
	}

	
	function updateTime($token){
		$user_id = $_SESSION['user_id'];
		$sql="UPDATE tb_login SET dt_time_update=now() WHERE id_user=".$user_id." and token='".$token."'and status = 'login' and id >=1;";
		//echo("SQL: ".$sql."<br>");
		//echo("user-id: ".$user_id."<br>");
		//echo("token: ".$token."<br>");
		$consulta=mysqli_query($GLOBALS['con'], $sql);
		//echo ("<br>Registro Atualizado<br>");
	}

	function rememberMe(){
		// Include detect.
		require_once '/var/www/mp_desenv/bg_biblioteca_detect.php';	
	
		$cookie_ID = isset($_COOKIE['MYDE_REMENBERME_ID']) ? $_COOKIE['MYDE_REMENBERME_ID'] : '';
		$cookie_TOKEN = isset($_COOKIE['MYDE_REMENBERME_TOKEN']) ? $_COOKIE['MYDE_REMENBERME_TOKEN'] : '';
		//echo "cookie_ID: " . $cookie_ID."<br>";
		//echo "cookie_TOKEN: " . $cookie_TOKEN."<br>";
		
		$os = Detect::os();
		$UID = $_SERVER['HTTP_USER_AGENT']."-->".$os;
		$UID_sha256 = hash_hmac('sha256', $UID, "ninguempoderoubar");
		//echo("UID: ".$UID."<br>");
		//echo("UID_sha256: ".$UID_sha256."<br>");
		if($cookie_ID != "" and $cookie_TOKEN != ""){
			$sql1 = "SELECT DISTINCT id,id_user,key_login,status FROM tb_login WHERE token='".$cookie_TOKEN."' and status='logoff';";
			//echo("sql1: ".$sql1."<br>");
			$resultado1 = mysqli_query($GLOBALS['con'],$sql1);
            //echo "cookie_TOKEN: ".$cookie_TOKEN."<br>";
            //$dados = mysqli_fetch_row($resultado1);
            //echo "key banco1: ".$dados[2]."<br>";
			if(mysqli_num_rows($resultado1)>0){
				$string = 'Usuário já efetuou logoff';
                expulsaVisitante();
				//echo $string;
                return $string;
			}else{
				$sql5 = "SELECT DISTINCT id,id_user,key_login,status FROM tb_login WHERE token='".$cookie_TOKEN."' and status='login';";
				//echo("sql5: ".$sql5."<br>");
				$resultado1 = mysqli_query($GLOBALS['con'],$sql5);
				if(mysqli_num_rows($resultado1)>0){
					for($i=0;$i<mysqli_num_rows($resultado1);$i++){
						$dados = mysqli_fetch_row($resultado1);
						//echo ("ID: ".$dados[0]."<br>");
						$ID_sha256 = hash_hmac('sha256', $dados[1], "ninguempoderoubar");
						//echo("ID_sha256: ".$ID_sha256."<br>");
						$KEY = hash_hmac('sha256', $ID_sha256.$cookie_TOKEN.$UID_sha256, "ninguempoderoubar");
						//echo "key2: ".$KEY."<br>";
						//echo "key banco2: ".$dados[2]."<br>";
						if($KEY==$dados[2]){
							//echo("entrou no if<br>");
							$check_user_query = "select id,NOME,SOBRENOME,NOME_COMPLETO,CEL,access_group,id_distribuidor,email from tb_usuarios WHERE id=(SELECT id_user FROM tb_login WHERE token='".$cookie_TOKEN."' and key_login='".$KEY."' and status = 'login');";
							//echo "Sintaxe select: $check_user_query";
							$check_user = mysqli_query($GLOBALS['con'],$check_user_query);
							$row = mysqli_fetch_row($check_user);
							$_SESSION['user_id'] = $row[0];
                            $_SESSION['user_nome'] = $row[1];
                            $_SESSION['user_sobrenome'] = $row[2];
                            $_SESSION['user_nome_compl'] = $row[3];
                            $_SESSION['user_cel'] = $row[4];
                            $_SESSION['user_access'] = $row[5];
                            $_SESSION['user_id_distribuidor'] = $row[6];
                            $_SESSION['user_email'] = $row[7];
                            $_SESSION['user_picture'] = '/media/upload/user_'.md5($row[0].$row[1]).'.jpg';
							$user_id = $row[0];
							$sql="SELECT * from tb_login WHERE id_user=".$user_id." and token='".$cookie_TOKEN."' and KEY_login='".$KEY."' and status='login';";
							$consulta=mysqli_query($GLOBALS['con'], $sql);
							updateTime($cookie_TOKEN);
							//exibe session
							//echo "<pre>";
								//var_dump($_SESSION);
							//echo "</pre>";
							$string = 'redirect';
                            //echo $string;
                            return $string;

						}else{
							$string = 'index.html';
							unset($_COOKIE['MYDE_REMENBERME_ID']);
							setcookie('MYDE_REMENBERME_ID', '', time() - 3600, '/','desenv.queromarita.com.br');
							unset($_COOKIE['MYDE_REMENBERME_TOKEN']);
							setcookie('MYDE_REMENBERME_TOKEN', '', time() - 3600, '/','desenv.queromarita.com.br');
                            expulsaVisitante();
                            //echo $string;
                            return $string;
							//echo("redirect 2");
							//echo ("Chave não bate, vai para acesso.html;");
						}
					}
				}else{
					unset($_COOKIE['MYDE_REMENBERME_ID']);
					setcookie('MYDE_REMENBERME_ID', '', time() - 3600, '/',"desenv.queromarita.com.br");
					unset($_COOKIE['MYDE_REMENBERME_TOKEN']);
					setcookie('MYDE_REMENBERME_TOKEN', '', time() - 3600, '/',"desenv.queromarita.com.br");
					//$string = 'Erro de registro bd';
                    expulsaVisitante();
                    $string = 'index.html';
                    //echo $string;
                    return $string;

				}
			}
		}else{
			$string = onLogin();
            //echo $string;
            return $string;
			//echo ("Não tem cookie, vai para onLogin();");
		}
	}

	function expulsaVisitante(){
  		// Remove as variáveis da sessão (caso elas existam)
		unset($_SESSION['user_id'], $_SESSION['user_nome'], $_SESSION['user_sobrenome'], $_SESSION['user_nome_compl'], $_SESSION['user_cel'],$_SESSION['user_access'],$_SESSION['user_id_distribuidor'],$_SESSION['user_email'],$_SESSION['user_picture']);
		// Manda pra tela de login
	    //echo '<META http-equiv="refresh" content="1;URL=index.html">';
        //echo "<script> window.location.replace('http://queromarita.com.br'); </script>";
	}

    //rememberMe();
    //onLogin();

?>