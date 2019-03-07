<?php
include 'includes/mysql_connect.php';
include_once 'includes/functions.php';
 
secure_session_start(); // Custom function for starting a PHP session.
 
if (isset($_POST['email'], $_POST['password'])) 
{
    $login = $_POST['email'];
    $password = $_POST['password'];
    try
    {
        login($login, $password, $mysqli);
        header('Location: ./dashboard.php');   /*zmiana z .. na . */
    }
    catch(IncorrectPasswordException $e) 
    {
        // Login failed, password not correct
        header('Location: ./index.php?error=1');  /*zmiana z .. na . */
    }
    catch(IncorrectNameException $e) 
    {
        // Login failed, user don't exist
        header('Location: ./index.php?error=2');  /*zmiana z .. na . */
    }
    catch(BruteForceException $e) {
        // Login failed, brute force
        header('Location: ./index.php?error=5'); /*zmiana z .. na . */
    }
}
else 
{
    // The correct POST variables were not sent to this page. 
    echo 'Invalid Request';
}