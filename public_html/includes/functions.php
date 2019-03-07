<?php
class BruteForceException extends Exception { }     /*exteption - klasa abstrakcyjna i po tym musi dziedziczyć każdy wyjątek*/
class IncorrectPasswordException extends Exception { }
class IncorrectNameException extends Exception { }
 
function secure_session_start() {        /* nakładka na session_start , ktora dodaje dodatkowe parametry pozwalające lepiej zabezpieczyć plik cookie*/  
    $session_name = 'sec_session_id';
    $secure = false;  /* w fazie development- false, w fazie production - true */
    // This stops JavaScript being able to access the session id. - kod js nie ma dostepu do pliku cookie
    $httponly = true;  
    // Forces sessions to only use cookies.
    if (ini_set('session.use_only_cookies', 1) === FALSE) {
        header("Location: ./index.php?err=Could not initiate a safe session (ini_set)"); /*wyrzuci przy niestandardowych ustawieniach przegladarki*/
        exit();
    }
    // Gets current cookies params.
    $cookieParams = session_get_cookie_params();
    session_set_cookie_params($cookieParams["lifetime"], $cookieParams["path"], $cookieParams["domain"], $secure, $httponly);
    // Sets the session name to the one set above.
    session_name($session_name);
    session_start();            // Start the PHP session 
    session_regenerate_id();    // regenerate the session, delete the old one. 
}

function login($email, $password, $mysqli) {
    // Using prepared statements means that SQL injection is not possible. 
    if ($stmt = $mysqli->prepare("SELECT id_user, email, password_hash FROM users WHERE email = ? LIMIT 1")) 
    {
        $stmt->bind_param('s', $email);  // Bind "$email" to parameter.
        $stmt->execute();    // Execute the prepared query.
        $stmt->store_result();
 
        //assign result to three variables
        $stmt->bind_result($user_id, $email, $db_password);
        $stmt->fetch();
 
        if ($stmt->num_rows == 1) //If username exist in DB, stmt has 1 row, otherwise is null
        {
            if (isBruteforce($user_id, $mysqli)) {
                // Account is locked 
                throw new BruteForceException;
            } 
            else {
            // Check if the password in the database matches
            // the password the user submitted. We are using
            // the password_verify function to avoid timing attacks.
                if (password_verify($password, $db_password)) 
                {
                        // Password is correct!
                        // Get the user-agent string of the user.
                        $user_browser = $_SERVER['HTTP_USER_AGENT'];
                        // XSS protection as we might print this value
                        $user_id = preg_replace("/[^0-9]+/", "", $user_id);
                        $_SESSION['user_id'] = $user_id;
                        // XSS protection as we might print this value
                       // $email = preg_replace("/[^a-zA-Z0-9_\-]+/",  "", $email);
                        $email = htmlspecialchars($email);  /* maskuje nawiazy ostre, czyli nie moze być kodu html*/
                        $_SESSION['username'] = $email;
                        $_SESSION['login_string'] = hash('sha512', 
                                $db_password . $user_browser);
                        // Login successful.
                        return 0;
                    }
                else 
                {
                    // Password is not correct
                    $mysqli->query("UPDATE users SET attempts=attempts+1 WHERE email='$email'");
                    throw new IncorrectPasswordException; /* wyrzucany wyjątek */
                }
            }
        }
        else 
        {
            // No user exists.
            throw new IncorrectNameException;
        }
    }
}

function login_check($mysqli) {
    // Check if all session variables are set 
    if (isset($_SESSION['user_id'], $_SESSION['username'], $_SESSION['login_string'])) {
 
        $user_id = $_SESSION['user_id'];
        $login_string = $_SESSION['login_string'];
        $username = $_SESSION['username']; /*przypisuje do zmiennych zeby dało się dać do selekta*/
 
        // Get the user-agent string of the user.
        $user_browser = $_SERVER['HTTP_USER_AGENT']; 
 
        if ($stmt = $mysqli->prepare("SELECT password_hash 
                                      FROM users 
                                      WHERE id_user = ? LIMIT 1")) {
            // Bind "$user_id" to parameter. 
            $stmt->bind_param('i', $user_id);
            $stmt->execute();   // Execute the prepared query.
            $stmt->store_result();
 
            if ($stmt->num_rows == 1) {
                // If the user exists get variables from result.
                $stmt->bind_result($password);
                $stmt->fetch();
                $login_check = hash('sha512', $password . $user_browser);
 
                if (hash_equals($login_check, $login_string) )
                {
                    // Logged In!
                    return true;
                } 
                else 
                {
                    // Not logged in, incorrect hash
                    return false;
                }
            } 
            else 
            {
                // Not logged in, id_user doesn't exist, 0 rows
                return false;
            }
        } 
        else 
        {
            // Not logged in, cannot prepare sql
            return false;
        }
    } 
    else 
    {
        // Not logged in, session variables not set
        return false;
    }
}

function isBruteforce($id_user, $mysqli) {    /*jak złe hasło to attempt+1 , event w bazie co godzine resetuje próby*/ 

    $id_user = $mysqli->real_escape_string($id_user);

    // Receive number of attempts with incorrect password
    $attempts = $mysqli->query("SELECT attempts a FROM users WHERE id_user=$id_user;")->fetch_assoc()['a'];
    
    if($attempts > 5) {
        return true;
    }
    else {
        return false;
    }
}

function isCompany($mysqli) {  /* bierze user_id, sprawdza czy jest id_company dla takiego user_id; jeśli nie to znaczy ze jest to employee*/ 
    $user_id = $_SESSION['user_id'];
    $result = $mysqli->query("SELECT id_company FROM companies WHERE id_user=$user_id")->fetch_assoc();
    if($result) return true;
    else return false;
}