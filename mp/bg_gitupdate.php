<?php
require_once("bg_git_config.php");
$content = file_get_contents("php://input");
$json    = json_decode($content, true);
$file    = fopen(LOGFILE, "a");
$time    = time();
$token   = false;

//var_dump($json);
// retrieve the token
if (!$token && isset($_SERVER["HTTP_X_HUB_SIGNATURE"])) {
    list($algo, $token) = explode("=", $_SERVER["HTTP_X_HUB_SIGNATURE"], 2) + array("", "");
} elseif (isset($_SERVER["HTTP_X_GITLAB_TOKEN"])) {
    $token = $_SERVER["HTTP_X_GITLAB_TOKEN"];
} elseif (isset($_GET["token"])) {
    $token = $_GET["token"];
}

date_default_timezone_set("UTC");
fputs($file, date("d-m-Y (H:i:s)", $time) . "\n");
// function to forbid access
function forbid($file, $reason) {
    // explain why
    if ($reason) fputs($file, "=== ERROR: " . $reason . " ===\n");
    fputs($file, "*** ACCESS DENIED ***" . "\n\n\n");
    fclose($file);
    // forbid
    header("HTTP/1.0 403 Forbidden");
    exit;
}
// function to return OK
function ok() {
    ob_start();
    header("HTTP/1.1 200 OK");
    header("Connection: close");
    header("Content-Length: " . ob_get_length());
    ob_end_flush();
    ob_flush();
    flush();
}
// Check for a GitHub signature
if (!empty(TOKEN) && isset($_SERVER["HTTP_X_HUB_SIGNATURE"]) && $token !== hash_hmac($algo, $content, TOKEN)) {
    forbid($file, "No token detected");
} else {
    // check if pushed branch matches branch specified in config
    if ($json["repository"]["name"] === BRANCH) {
        fputs($file, $content . PHP_EOL);
        // ensure directory is a repository
        if (file_exists(DIR . ".git") && is_dir(DIR)) {
            echo '<br>É um diretorio válido! <br>';

           try {
                // pull
                chdir(DIR);
                echo "vai executar o comando: " . GIT . " pull "  .  $json["repository"]["clone_url"];
                shell_exec(GIT . " pull " ."https://CaldasBR:q215463a@github.com/CaldasBR/myde.git");
                // return OK to prevent timeouts on AFTER_PULL
                //ok();
                // execute AFTER_PULL if specified
                /*if (!empty(AFTER_PULL)) {
                    try {
                        shell_exec(AFTER_PULL);
                    } catch (Exception $e) {
                        fputs($file, $e . "\n");
                    }
                }*/
                //fputs($file, "*** AUTO PULL SUCCESFUL ***" . "\n");*/
            } catch (Exception $e) {
                //fputs($file, $e . "\n");
            }
        } else {
//            fputs($file, "=== ERROR: DIR is not a repository ===" . "\n");
        }
    } else{
  //      fputs($file, "=== ERROR: Pushed branch does not match BRANCH ===\n");
    }
}
fputs($file, "\n\n" . PHP_EOL);
fclose($file);
?>
