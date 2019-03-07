<?php
include_once 'includes/mysql_connect.php';
include_once 'includes/functions.php';
secure_session_start();
if (login_check($mysqli) == false) 
{
    header('Location: ./?error=3');   /*zmiana z .. na . */
}
if (isCompany($mysqli)) {
    include 'includes/admin-dashboard.php';
}
else {
    include 'includes/regular-dashboard.php';
}
?>