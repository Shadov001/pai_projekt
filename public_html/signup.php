<?php
    include_once 'includes/mysql_connect.php';
    include_once 'includes/functions.php';

    if(isset($_POST['title'], $_POST['email'], $_POST['phone'], $_POST['address'], $_POST['postal_code'], $_POST['city'], $_POST['password']))
    {
        $title = $mysqli->real_escape_string($_POST['title']);
        $email = $mysqli->real_escape_string($_POST['email']);
        $phone = $mysqli->real_escape_string($_POST['phone']);
        $street = $mysqli->real_escape_string($_POST['address']);
        $postal_code = $mysqli->real_escape_string($_POST['postal_code']);
        $city = $mysqli->real_escape_string($_POST['city']);
        $password = $mysqli->real_escape_string($_POST['password']);
        $password = password_hash($password, PASSWORD_DEFAULT);
        $mysqli->autocommit(FALSE);
        $mysqli->query("SET TRANSACTION ISOLATION LEVEL REPEATABLE READ");      /*co to za query*/
        $checkRepeat = $mysqli->query("SELECT id_user FROM users WHERE email='$email'")->fetch_assoc();
        if($checkRepeat) {
            $mysqli->commit();
            header('Location: ./?error=4'); //error: Takie konto już istnieje, powtórka adresu e-mail.
        }
        else {
            $mysqli->query("INSERT INTO users (email, password_hash) values ('$email', '$password');");
            $id_user = ($mysqli->query("SELECT LAST_INSERT_ID()"))->fetch_assoc()['LAST_INSERT_ID()'];
            $mysqli->query("INSERT INTO companies (title, phone, street, postal_code, city, id_user) values ('$title', '$phone', '$street', '$postal_code', '$city', $id_user);");
            $mysqli->commit();
            header('Location: ./?info=1'); //info: Założono nowe konto
        }
    }
?>

<!DOCTYPE html>
<html lang="pl-PL">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="static/style.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
</head>
<body>
	
	<div class="limiter">
		<div class="container-login">
            <div class="wrap-login">
                <span class="login-form-title">
					Rejestracja dla firm
				</span>
                <form action="signup.php" method="post">
                    <div class="signup-row">
                        <div class="name">
                            Nazwa firmy
                        </div>
                        <div class="value">
                            <div class="input-group">
                                <input class="gray-input" type="text" name="title" onchange="propertyChanged()" required>
                            </div>
                        </div>
                    </div>
                    <div class="signup-row">
                        <div class="name">
                            E-mail
                        </div>
                        <div class="value">
                            <div class="input-group">
                                <input class="gray-input" type="text" name="email" maxlength="255" id="email-input" onchange="emailChanged()" required> <!--required - bez tego nie przepuści-->
                            </div>
                        </div>
                    </div>
                    <div class="signup-row">
                        <div class="name">
                            Telefon
                        </div>
                        <div class="value">
                            <div class="input-group">
                                <input class="gray-input" type="text" name="phone" maxlength="15" onchange="propertyChanged()">  <!--walidacja danych  - maxlength-->
                            </div>
                        </div>
                    </div>
                    <div class="signup-row">
                        <div class="name">
                            Adres
                        </div>
                        <div class="value">
                            <div class="input-group">
                                <input class="gray-input" type="text" name="address" onchange="propertyChanged()">
                            </div>
                        </div>
                    </div>
                    <div class="signup-row">
                        <div class="name">
                            Kod pocztowy
                        </div>
                        <div class="value">
                            <div class="input-group">
                                <input class="gray-input" type="text" name="postal_code" onchange="propertyChanged()">
                            </div>
                        </div>
                    </div>
                    <div class="signup-row">
                        <div class="name">
                            Miejscowość
                        </div>
                        <div class="value">
                            <div class="input-group">
                                <input class="gray-input" type="text" name="city" onchange="propertyChanged()">
                            </div>
                        </div>
                    </div>
                    <div class="signup-row">
                        <div class="name">
                            Hasło
                        </div>
                        <div class="value">
                            <div class="input-group">
                                <input id="password-input" class="gray-input" type="password" name="password" onchange="propertyChanged()" required>
                            </div>
                        </div>
                    </div>
                    <div class="signup-row">
                        <div class="name">
                            Powtórz hasło
                        </div>
                        <div class="value">
                            <div class="input-group">
                                <input id="passwordrepeat-input" class="gray-input" type="password" name="passwordrepeat" onchange="propertyChanged()" required>
                            </div>
                        </div>
                    </div>
                    <div class="signup-row">
                        <input id="accept-input" type="checkbox" onchange="propertyChanged()" style="margin-right: 5px;">
                        <div class="name" style="width: 80%">
                            Akceptuję regulamin
                        </div>
                    </div>
                    <div class="container-login-form-btn">
						<div class="wrap-login-form-btn">
							<div class="login-form-bgbtn"></div>
							<input type="submit" id="register-btn" class="login-form-btn" value="Potwierdź">
						</div>
                    </div>
                </form>
            </div>
		</div>
	</div>
    <script>
    propertyChanged();
    function propertyChanged(){
        if ($('#accept-input').prop('checked') && $('#password-input').val()==$('#passwordrepeat-input').val()) {  /* jeżli regulamin-accepted i hasło=powtórzhasło to podświetl gradientem*/
            $("#register-btn").prop( "disabled", false );
            $("#register-btn").css("background-color", "transparent");
        }
        else {
            $("#register-btn").prop( "disabled", true );
            $("#register-btn").css("background-color", "gray");
        }
    }
    function emailChanged(){         /*wysyła zapytanie typu post, data- dodatkowe parametry do url, jeżli z validateemail zwróci 0 (email jest w bazie lub nie zgadza sie z regexem) to koloruje na czerwono*/
        $.ajax({            /* przesyłany jest str postem, w validateemail jest porownywany ze znakami*/
            type: 'POST',
            url: './validateEmail.php',
            data: "str=" + $('#email-input').val(),
            success: function(data) {
                if(data==0) {
                    $("#email-input").css("background-color", "#ff6d62");
                }
                else {
                    $("#email-input").css("background-color", "#9dff64");
                }
            }
            });
    }
    </script>
</body>
</html>
