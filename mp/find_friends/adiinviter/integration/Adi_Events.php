<?php
    /*+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+
    | AdiInviter Pro (http://www.adiinviter.com)                                                |
    +-------------------------------------------------------------------------------------------+
    | @license    For full copyright and license information, please see the LICENSE.txt        |
    + @copyright  Copyright (c) 2015 AdiInviter Inc. All rights reserved.                       +
    | @link       http://www.adiinviter.com                                                     |
    + @author     AdiInviter Dev Team                                                           +
    | @docs       http://www.adiinviter.com/docs                                                |
    + @support    Email us at support@adiinviter.com                                            +
    | @contact    http://www.adiinviter.com/support                                             |
    +-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+*/

    /*
    x-x-x-x-x-x-x-x-x-x-x-x-x-x-x-x-x-x-x-x-x-x-x-x-x-x-x-x-x-x-x-x-x-x-x-x-x-x-x-x-x-x
    IMPLEMENT OR MODIFY PROPERTIES OF THIS CLASS TO CREATE CUSTOM FUNCTIONALITIES OR
    TO OVERRIDE DEFAULT BEHAVIOUR OF ADIINVITER PRO SYSTEM.
    x-x-x-x-x-x-x-x-x-x-x-x-x-x-x-x-x-x-x-x-x-x-x-x-x-x-x-x-x-x-x-x-x-x-x-x-x-x-x-x-x-x
    */

    class Adi_Events extends Adi_Events_Platform
    {
    	// Implement Event Listener functions here.
        /**
        * This function is called before sending email invitation to every single selected contact.
        *
        * @param    data         array : Associative array containing following information :
        *
        *   $data["receiver_id"]       : Email address of the selected contact.
        *   $data["receiver_name"]     : Name of the selected contact.
        *   $data["service_info"]      : Information about the importer service.
        *   $data["campaign_id"]       : Unique ID for Campaign. Empty string for regular invitation
        *   $data["content_id"]        : Content ID. Empty string for regular invitations.
        *   $data["subject"]           : Invitation email subject.
        *   $data["body"]              : Invitation email body.
        *
        * @return   boolean      true  : Send invitation to current contact.
        * @return   boolean      false : Do not send invitation to current contact.
        **/

        function event_before_sending_invitation($data)
        {
            if(isset($_SESSION["user_id"])){
                include_once("/var/www/mp/bg_conexao_bd.php");

                $sql = "INSERT INTO tb_indicacao
                        (id_distribuidor,nome,email)
                        VALUES (".$_SESSION["user_id"].",'".$data["receiver_name"]."','".$data["receiver_id"]."');";
                $query = mysqli_query($GLOBALS['con'],$sql);

                /*echo "Esse é o SQL: " . $sql . "<br>";
                var_dump($_SESSION["user_id"]);
                var_dump($data["receiver_name"]);
                var_dump($data["receiver_id"]);
                echo "<br><br>";*/
            }
        }
    }

?>
