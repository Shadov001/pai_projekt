<!DOCTYPE html>
<html lang="pl-PL">
    <head>
        <meta charset="utf-8" />
        <link rel="stylesheet" href="static/style.css">
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css" integrity="sha384-UHRtZLI+pbxtHCWp1t77Bi1L4ZtiqrqD80Kn4Z8NTSRyMA2Fd33n5dQ8lWUE00s/" crossorigin="anonymous">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">
	    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
        <title>Ustawienia</title>
    </head>
<body>
	<div class="limiter">
		<div class="container-login">
        <?php
			if ($error ==1) { ?>
				<div class="alert alert-danger" id="alert" role="alert">
					  <strong>Błąd </strong> Nieprawidłowe hasło. 
				</div>
		<?php } ?>
            <div class="wrap-login">
                <div class="flat-btn" style="margin: -60px 0px 22px -52px;">
                    <a class=""href="./dashboard.php"><i class="fa fa-arrow-left"></i></a>
                </div>
                <span class="login-form-title">
					Zmiana hasła
                </span>
                <form action="settings.php" method="post" onsubmit="return a();">
                    <div class="signup-row">
                        <div class="name">
                            Stare hasło
                        </div>
                        <div class="value">
                            <div class="input-group">
                                <input class="gray-input" type="password" name="prev-password" required>
                            </div>
                        </div>
                    </div>
                    <div class="signup-row">
                        <div class="name">
                            Nowe hasło
                        </div>
                        <div class="value">
                            <div class="input-group">
                                <input id="password-input" class="gray-input" type="password" minlength="5" name="password" required>
                            </div>
                        </div>
                    </div>
                    <div class="signup-row">
                        <div class="name">
                            Powtórz nowe hasło
                        </div>
                        <div class="value">
                            <div class="input-group">
                                <input id="passwordrepeat-input" class="gray-input" type="password" minlength="5" name="passwordrepeat" required>
                            </div>
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
    function a() {
            if ($('#password-input').val()==$('#passwordrepeat-input').val()) {
                return true;
            }
            else {
                return false;
            }
        }
    </script>
</body>
</html>