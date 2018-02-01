<?php
  header('Content-Type: text/html; charset=utf-8');

$login= $_POST['login'];
$senha= md5 ($_POST['senha']);
$connect = mysql_connect('makebit', 'usersql', 'SQLqsenha123');
$db = mysql_select_db('cadastro');
$query_select= "SELECT * from Usuarios WHERE login ='$login'";
$select = mysql_query($query_select,$connect);
$array = mysql_fetch_array($select);
$logarray= $array['login'];

if($login == "" || $login == NULL ):
   echo "<script language='javascript' type='text/javascript'>alert('O campo login deve ser preenchido');
   window.location.href='cadastro.html';</script>";

elseif($logarray == $login):
    echo "<script language='javascript' type='text/javascript'>alert('Esse login já existe');
     window.location.href='cadastro.html';</script>";
        die();
else:
  echo"<script language='javascript' type='text/javascript'>alert('Não foi possível cadastrar esse usuário');
  window.location.href='cadastro.html'</script>";



?>
