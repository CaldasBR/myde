<?php
    ini_set('display_errors','on');
    error_reporting(E_ALL);

    /*function curl($url) {
        $ch = curl_init();  // Initialising cURL
        curl_setopt($ch, CURLOPT_URL, $url);    // Setting cURL's URL option with the $url variable passed into the function
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE); // Setting cURL's option to return the webpage data
        $data = curl_exec($ch); // Executing the cURL request and assigning the returned data to the $data variable
        curl_close($ch);    // Closing cURL
        return $data;   // Returning the data from the function
    }

    $a = curl('http://www2.bmf.com.br/pages/portal/bmfbovespa/boletim1/TxRef1.asp?Data=16/10/2017&Data1=20171016&slcTaxa=Pre');
    */
    include("simple_html_dom.php");

    $html = new simple_html_dom();

    // Load a file 
    $html->load_file('http://www2.bmf.com.br/pages/portal/bmfbovespa/boletim1/TxRef1.asp?Data=16/10/2017&Data1=20171016&slcTaxa=Pre');
    //$this->loadModel('Program');
    //$programs = $this->Program->find('list');

    $table = [];
    foreach($html->find('tr') as $row) {
        var_dump($row);
        echo "<br><br>";

        /*$time = $row->find('td',0)->plaintext;
        $artist = $row->find('td',1)->plaintext;
        $title = $row->find('td',2)->plaintext;

        $table[$artist][$title] = true;*/
    }

    /*echo '<pre>';
    print_r($table);
    echo '</pre>';*/
?>
