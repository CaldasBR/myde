<?php
    ini_set('display_errors','on');
	error_reporting(E_ALL);

	header('Content-Type: text/html; charset=utf-8');
	include("/var/www/mp/bg_conexao_bd.php");
    include("/var/www/mp/bg_funcoes_genericas.php");

	//  Configurações do Script
    $funcao = "carrega_grafs";
	$erro = 0;
	$msg = "Nada foi executado!";
	$opt = "";

    //Verifica se usuário está Logado
    include("/var/www/mp/bg_protege_php.php");
    $status_log = status_login($resposta);

    if($status_log=="mantem"){
        if(isset($_GET["fn"]) || isset($_POST["fn"])){
            if(isset($_GET["fn"])){
                $fn = addslashes($_GET["fn"]);
            }else{
                $fn = addslashes($_POST["fn"]);
            }

            carregar_graficos($fn);
        }
    }

    function carregar_graficos($grafico){
        global $erro,$msg,$opt;

        //,year(a.date_created)*100+month(a.date_created) as mes_venda

        switch($grafico){
            case 'graf_indicacoes':
                $sql = "select service_used, count(*) as qtde from adiinviter where inviter_id =" .$_SESSION["user_id"]. " group by service_used;";
            break;
            case 'graf_indicacoes_status':
                $sql = "select case invitation_status when 'blocked' then 'Bloqueou' when 'invitation_sent' then 'Convidado' when 'waiting' then 'Em envio' end as status, count(*) as qtde_indicada  from adiinviter  where inviter_id = ".$_SESSION["user_id"]." group by invitation_status;";
            break;
            case 'graf_indicacoes_bloqueado':
                $sql = "select receiver_name as nome, receiver_email as email from adiinviter where invitation_status = 'blocked' and inviter_id = ".$_SESSION["user_id"].";";
            break;
            case 'graf_vendas_qtde_dia':
                $sql = "select
                    date(a.date_created) as data_venda
                    ,count(a.payment_id) as qtde
                from
                    tb_pgto_mercpago a
                left join
                    tb_usuarios b
                on(a.id_user=b.id)
                where a.status = 'approved' and ID_DISTRIBUIDOR=".$_SESSION["user_id"]."
                group by
                    date(a.date_created);";
            break;
            case 'graf_vendas_valor_dia':
                $sql = "select
                    date(a.date_created) as data_venda
                    ,sum(a.net_received_amount) as valor_recebido
                from
                    tb_pgto_mercpago a
                left join
                    tb_usuarios b
                on(a.id_user=b.id)
                where a.status = 'approved' and ID_DISTRIBUIDOR=".$_SESSION["user_id"]."
                group by
                    date(a.date_created);";
            break;
            case 'graf_vendas_qtde_mes':
                $sql = "select
                    CONCAT(year(a.date_created), ' - ',month(a.date_created)) as mes_venda
                    ,count(a.payment_id) as qtde
                from
                    tb_pgto_mercpago a
                left join
                    tb_usuarios b
                on(a.id_user=b.id)
                where a.status = 'approved' and ID_DISTRIBUIDOR=".$_SESSION["user_id"]."
                group by
                    CONCAT(year(a.date_created), ' - ',month(a.date_created));";
            break;
            case 'graf_vendas_valor_mes':
                $sql = "select
                    CONCAT(year(a.date_created), ' - ',month(a.date_created)) as mes_venda
                    ,sum(a.net_received_amount) as valor_recebido
                from
                    tb_pgto_mercpago a
                left join
                    tb_usuarios b
                on(a.id_user=b.id)
                where a.status = 'approved' and ID_DISTRIBUIDOR=".$_SESSION["user_id"]."
                group by
                    CONCAT(year(a.date_created), ' - ',month(a.date_created));";
            break;
        }

        include("/var/www/mp/bg_conexao_bd.php");
        $query = mysqli_query($GLOBALS['con'],$sql);

        $dados = [];
		$campos = [];
		$linhas = [];

        $fieldinfo=mysqli_fetch_fields($query);

        foreach($fieldinfo as $val){
            array_push($campos,$val->name);
        }
        array_push($dados,$campos);

        if(mysqli_num_rows($query)>0){
            for($i=0;$i<mysqli_num_rows($query);$i++){
                $row = mysqli_fetch_row($query);
                foreach ($row as $key => $value){
                    if (is_numeric($value)) {
                        $row[$key] = (float)$value;
                    }
                }
                array_push($dados,$row);
            }
        }

        $msg=$dados;
        $erro=0;
        $opt=$grafico;
    }

	$envia_resposta = array("erro"=>$erro,"msg"=>$msg,"funcao"=>$funcao,"opt"=>$opt);
	print json_encode($envia_resposta);
?>
