<?php
    session_start();

    // Remove cookies.
    if(isset($_COOKIE['MYDE_REMENBERME_ID'])){
        setcookie('MYDE_REMENBERME_ID', '', time() - 3600, '/',"queromarita.com.br");
        unset($_COOKIE['MYDE_REMENBERME_ID']);
        //echo "1";
    }
    if(isset($_COOKIE['MYDE_REMENBERME_TOKEN'])){
        setcookie('MYDE_REMENBERME_TOKEN', '', time() - 3600, '/',"queromarita.com.br");
        unset($_COOKIE['MYDE_REMENBERME_TOKEN']);
        //echo "2";
    }


    // Finally, destroy the session.
	session_destroy();
    //echo "6";

    header('location: https://queromarita.com.br/');
    //echo "7";
?>