<?php
include_once 'includes/mysql_connect.php';
include_once 'includes/functions.php';
secure_session_start();
if (login_check($mysqli) == false) {
    header('Location: ./?error=3'); /*zmiana z .. na . */
}
$error = 0;
if (isset($_POST['prev-password'], $_POST['password'])) {          /*co to za if*/
    $id_user = $_SESSION['user_id'];
    $prev_password = $_POST['prev-password'];
    $password = $mysqli->real_escape_string($_POST['password']);
    $db_password = $mysqli->query("SELECT password_hash p FROM users WHERE id_user=$id_user")->fetch_assoc()['p']; //Receive previous password from db
    if (password_verify($prev_password, $db_password)) {
        $password_h = password_hash($password, PASSWORD_DEFAULT); //hash new password
        $mysqli->query("UPDATE users SET password_hash='$password_h' WHERE id_user=$id_user;"); //Replace password in DB
        login($_SESSION['username'], $password, $mysqli); //Login with new password
        header('Location: ./dashboard.php?info=1'); //info: Hasło zostało zmienione
    }
    else {
        $error = 1; //Aktualne hasło się nie zgadza
    }
}
if (isCompany($mysqli)) {
    include 'includes/admin-settings.php';
}
else {
    include 'includes/regular-settings.php';
}
?>