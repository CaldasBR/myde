<?php
// VARIÁVEIS ENVIADAS PELO FACEBOOK
// id,name,address,age_range,birthday,devices,email,first_name,gender,last_name,link,locale,religion,meeting_for
	//ini_set('display_errors','on');
	//error_reporting(E_ALL);
	header('Access-Control-Allow-Origin: *');
	include_once("/var/www/chat/bg_conexao_bd.php");
	$img_folder = '/var/www/chat/media/upload/';
	$img_short_folder = '/media/upload/';
	//extract($_POST);
	
	$id = $_POST['id'];
	$name = $_POST['name'];
	$email = $_POST['email'];
	$first_name = $_POST['first_name'];
	$last_name = $_POST['last_name'];
	$full_name = $first_name.' ' .$last_name;
	$gender = $_POST['gender'];
	$link = $_POST['link'];	
	$picture = $_POST['picture'];
	
	if(isset($_POST['age_range'])){
		$age_range = $_POST['age_range'];
	}
	else{
		$age_range = null;
	}
	
	if(isset($_POST['birthday'])){
		$birthday = $_POST['birthday'];
		$birthdaydt = strtotime( $birthday);
		$birthdaydt_txt = date('Y',($birthdaydt)) . '-' . date('m',($birthdaydt)) . '-' . date('d',($birthdaydt));
	}else{
		$birthday =null;
		$birthdaydt = null;
		$birthdaydt_txt = null;
	}
	if(date('Y',($birthdaydt))<1908){
		$birthdaydt_txt = "1908-12-31";
	}
	
	if(isset($_POST['devices'])){
		$devices = $_POST['devices'];
		$devices_lst = "";
		foreach ($devices as $vet_devices) {
			foreach ($vet_devices as $vet_devices_fim) {$devices_lst =  $vet_devices_fim . ", " . $devices_lst;}
			//echo "My Devices: $devices_lst";
		}
	}else{
		$devices = null;
		$devices_lst = null;
	}
	if(isset( $_POST['locale'])){
		$locale = $_POST['locale'];
	}else{
		$locale = null;
	}
	if(isset($_POST['religion'])){
		$religion = $_POST['religion'];
	}else{
		$religion = null;
	}
	if(isset($_POST['interested_in'])){
		$interested_in = $_POST['interested_in'];
		$interested_in_lst = implode(", ", $interested_in);
	}else{
		$interested_in = null;
		$interested_in_lst = null;
	}
	if(isset($_POST['location'])){
		$location = $_POST['location'];
		$location_name = $location['name'];
	}else{
		$location = null;
		$location_name = null;
	}
	if(isset($_POST['relationship_status'])){
		$relationship_status = $_POST['relationship_status'];
	}else{
		$relationship_status = null;
	}
	if(isset($_POST['education'])){
		$education = $_POST['education'];
	}else{
		$education = null;
	}
	if(isset($_POST['likes'])){
		$likes = $_POST['likes'];
		foreach ($likes as $vet_likes => $vet_likes_cluster ) {
			foreach ($vet_likes_cluster as $vet_likes_cluster_item) {
				$likes_name_lst = $vet_likes_cluster_item['name'] . ", " . $likes_name_lst;
				$likes_id_lst = $vet_likes_cluster_item['id'] . ", " . $likes_id_lst;
			}
		} 	
	}else{
		$likes = null;
		$likes_name_lst = null;
		$likes_id_lst  = null;
	}
	if(isset($age_range['min'])){
		$age_range_min = $age_range['min'];
	}else{
		$age_range_min = null;
	}
	if(isset($age_range['max'])){
		$age_range_max = $age_range['max'];
	}else{
		$age_range_max = null;
	}

	
	$token = rand(0,9).rand(0,9).rand(0,9).rand(0,9).rand(0,9).rand(0,9).rand(0,9).rand(0,9).rand(0,9);
	//echo "Token: $token";
		
	//var_dump($_POST);
	//echo "aaaa"; 
	
	$vet_picture = $picture['data'];
	$foto_perfil = $vet_picture['url'];
	
	$education_lst = "";
	foreach ($education as $vet_education => $vet_education_cluster ) {
		$education_lst = $vet_education_cluster['type'] . ", " . $vet_education_cluster['school']['name'] . ", " . $vet_education_cluster['school']['id'] . ", " . $vet_education_cluster['year']['name'] . "#" . $education_lst; 
	}	
	
	$likes_name_lst = "";
	$likes_id_lst = "";
	
	//$devices_lst = $devices_lst . $vet_devices['hw'];

	//VALIDA SE VEIO PELO FORMULÁRIO DE LOGIN, OU SE VEIO PELO FACEBOOK, A VARIÁVEL FROM SÓ VAI ESTAR PREENCHIDA SE VIER PELO FORMULÁRIO
	if(isset($_POST['id']) && !empty($_POST['id']) && isset($_POST['email']) && !empty($_POST['email']) && isset($_POST['link']) && !empty($_POST['link'])){

		$check_user_query = "select source_data,access_group from tbl_users WHERE email = '$email';";
		$check_user = mysqli_query($con,$check_user_query);
		
		if(mysqli_num_rows($check_user) == 0){
			//new user - we need to insert a record
			//id,name,address,age_range,birthday,devices,email,first_name,gender,last_name,link,locale,religion,meeting_for
			//echo "Entrei para INSERT";
			$insert_user_query = "Insert into tbl_users (email,id_fb,full_name,address,age_range_max,age_range_min,birthday,devices,first_name,gender,last_name,link,locale,religion,interested_in,source_data,access_group,picture,relationship,education_history,likes_names,likes_ids,token) VALUES ('$email','$id','$name','$location_name','$age_range_max','$age_range_min','$birthdaydt_txt','$devices_lst','$first_name','$gender','$last_name','$link','$locale','$religion','$interested_in_lst','Facebook','user','atualizar','$relationship_status','$education_lst',\"". $likes_name_lst . "\",'$likes_id_lst','$token');";
			mysqli_query($con,$insert_user_query);
			//echo "insert_user_query:  $insert_user_query <br>";
			
			//Checar se foi realmente cadastrado:
			$check_user_query = "select id,token,email,source_data,access_group from tbl_users WHERE email = '$email' LIMIT 1;";
			$check_user = mysqli_query($con,$check_user_query);
			while($row =  $check_user->fetch_assoc()){
				if($row['id'] >= 1){
					
					$update_user_query = "update tbl_users set picture='". $img_short_folder . "user_" . $row['id'] . ".jpg' WHERE email = '$email';";
					mysqli_query($con,$update_user_query);
					
					$insert_userconfig_query = "Insert into user_config (user_id,alert,Chat_Tp_1, Chat_Tp_2, Chat_Tp_3, ftr_distancia_min, ftr_distancia_max, ftr_sexo, ftr_status, ftr_idade_min, ftr_idade_max, ftr_amigos, ftr_novos ) VALUES (" . $row['id'] . ",1,1,1,1,0,2000,'Todos','Todos',5,120,0,1);";
					mysqli_query($con,$insert_userconfig_query);
					
					// Definimos dois valores na sessão com os dados do login
					session_start();
					$_SESSION['myde_user_id'] = $row['id'];
					$_SESSION['myde_user_firstname'] = $first_name;
					$_SESSION['myde_user_fullname'] = $full_name;
					$_SESSION['myde_user_token'] = $token;
					$_SESSION['myde_user_email'] = $email;
					$_SESSION['myde_user_password'] = "";
					$_SESSION['myde_access'] = $row['access_group'];
					$_SESSION['myde_user_source'] = "Facebook";
					$_SESSION['myde_last_activity'] = time();
				
					file_put_contents($img_folder."user_".$row['id'].".jpg", file_get_contents($foto_perfil));
					$arr = array('status' => 'Cadastrado!','access' => 'user');
					echo json_encode($arr);
				}else {
					$arr = array('status' => 'Erro no cadastro');
					echo json_encode($arr);
				}
			}

		}else{
			//echo "Entrei para ELSE";
			//Checar se a origem do cadastro não foi do Facebook, se não foi, atualiza:
			$check_user_query = "select id,token,email,source_data,access_group from tbl_users WHERE email = '$email' LIMIT 1;";
			$check_user = mysqli_query($con,$check_user_query);
			while($row =  $check_user->fetch_assoc()){
				if($row["source_data"] <> "Facebook"){
					//update
					$update_user_query = "update tbl_users set id_fb='$id',full_name='$name',address='$location_name',age_range_max='$age_range_max',age_range_min='$age_range_min',birthday='$birthdaydt_txt',devices='$devices_lst',interested_in='$interested_in_lst',first_name='$first_name',last_name='$last_name',link='$link',locale='$locale',religion='$religion',gender='$gender',source_data='Facebook',picture='" . $img_short_folder . "user_" . $row['id'] . ".jpg',relationship='$relationship_status',education_history='$education_lst',likes_names=\"" . $likes_name_lst . "\",likes_ids='$likes_id_lst .' WHERE email = '$email';";
					//echo "update_user_query:  $update_user_query <br>";
					mysqli_query($con,$update_user_query);
					
					file_put_contents($img_folder."user_".$row['id'].".jpg", file_get_contents($foto_perfil));
					$arr = array('status' => 'Cadastro atualizado para o Facebook!', 'access' => $row["access_group"]);
					echo json_encode($arr);
				}else{
					$arr = array('status' => 'Sem Atualizacao','access' => $row["access_group"]);
					echo json_encode($arr);			
				}
				session_start();
				$_SESSION['myde_user_id'] = $row['id'];
				$_SESSION['myde_user_firstname'] = $first_name;
				$_SESSION['myde_user_fullname'] = $full_name;
				$_SESSION['myde_user_email'] = $email;
				$_SESSION['myde_user_password'] = "";
				$_SESSION['myde_user_token'] = $row['token'];
				$_SESSION['myde_access'] = $row['access_group'];
				$_SESSION['myde_user_source'] = "Facebook";
				$_SESSION['myde_last_activity'] = time();
			}
		}
		mysqli_close($con);
		//echo json_encode($_POST);
		//echo "Conteúdo da variável check_user_query: $check_user_query";
		//echo "Conteúdo da variável check_user: $check_user";
	}else{
			$arr = array('status' => 'Erro no PHP');
			echo json_encode($arr);
	}
?>