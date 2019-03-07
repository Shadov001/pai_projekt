<?php
include_once 'includes/mysql_connect.php';
if (preg_match(",\A[a-z0-9!#$%&'*+/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&'*+/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\z,", $_POST['str'])) {
    $email = $_POST['str'];
    $result = $mysqli->query("SELECT email FROM users WHERE email='$email'")->fetch_assoc(); /*niepotrzebny realescapestring bo wiadomo ze juz ma forme maila*/
    if($result) {
        echo "0";
    }
    else {
        echo "1";
    }
} 
else {
    echo "0";
}

?>