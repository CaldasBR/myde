<?php

    function exportar($tabela, $data_min){
        $erro=0;
        switch ($tabela) {
            case 'foxbit_orderbook':
                $where = 'WHERE data_cotacao >= "'.$data_min.'" ORDER BY data_cotacao desc, moeda, tipo ';
                break;
            case 'foxbit_transactions':
                $where = 'WHERE dt_cotacao >= "'.$data_min.'" ORDER BY dt_cotacao desc, moeda ';
                break;
            default:
                $erro=1;
                break;
        }

        if($erro!=1){
            /* vars for export */
            // database table to be exported
            $db_table = $tabela;
            // filename for export
            $csv_filename = $tabela.'.csv';//.date('Y_m_d H:i').'.csv';
            // database variables
            $hostname = "makebit.com.br";
            $user = "usersql";
            $password = "SQLqsenha123";
            $database = "makebit";
            // Database connecten voor alle services
            mysql_connect($hostname, $user, $password)
            or die('Could not connect: ' . mysql_error());

            mysql_select_db($database)
            or die ('Could not select database ' . mysql_error());
            // create var to be filled with export data
            $csv_export = '';
            // query to get data from database
            $query = mysql_query("SELECT * FROM ".$db_table." ".$where);
            $field = mysql_num_fields($query);
            // create line with field names
            for($i = 0; $i < $field; $i++) {
            $csv_export.= mysql_field_name($query,$i).';';
            }
            // newline (seems to work both on Linux & Windows servers)
            $csv_export.= '
            ';
            while($row = mysql_fetch_array($query)) {
            // create line with field values
            for($i = 0; $i < $field; $i++) {
            $csv_export.= $row[mysql_field_name($query,$i)].';';
            //$csv_export.= '"'.$row[mysql_field_name($query,$i)].'",';
            }
            $csv_export.= '
            ';
            }
            // Export the data and prompt a csv file for download
            header("Content-type: text/x-csv");
            header("Content-Disposition: attachment; filename=".$csv_filename."");
            echo($csv_export);

        }else {
            echo "Erro nos paremetros";
        }
    }

    $data_min = date('Y-m-d H:i', strtotime("-240 minutes"));
    exportar('foxbit_orderbook', $data_min);
    //exportar('bithumb_transactions', '2017-12-20');

?>
