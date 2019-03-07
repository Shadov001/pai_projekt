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
			<?php
			if (isset($_GET['error'])) { ?>
				<div class="alert alert-danger" id="alert" role="alert">
					  <strong>Błąd </strong> 
					  <?php
						if ($_GET['error']==1) {
							echo "Nieprawidłowe hasło.";
						 }
						 elseif ($_GET['error']==2) {
							echo "Użytkownik o podanym adresie e-mail nie istnieje.";
						 }
						 elseif ($_GET['error']==3) {
							echo "Najpierw należy się zalogować.";
						 }
						 elseif ($_GET['error']==4) {
							echo "Użytkownik o podanym adresie e-mail już istnieje.";
						 }
						elseif ($_GET['error']==5) {
							echo "Zbyt dużo prób logowania.";
						 }
                         else {
                             echo "Wystąpił nieznany błąd.";
                         }
						 ?>
				</div>
					<?php } elseif (isset($_GET['info'])) {?>
						<div class="alert alert-info" id="alert" role="alert">
					  <?php
						if ($_GET['info']==1) {
							echo "Założono nowe konto.";
						 }
						 ?>
				</div>
					<?php } ?>
			<noscript>
			<div class="alert alert-danger" id="alert" role="alert">
					  <strong>Error! </strong> 
					  Enable javascript in your browser configuration.
				</div>	 
			</noscript>
			<div class="wrap-login">
				<form class="login-form validate-form" action="login.php" method="post">
					<span class="login-form-title">
						Zaloguj się
					</span>

					<div class="wrap-input">
						<input class="input" type="text" name="email" placeholder="E-mail">
					</div>

					<div class="wrap-input">
						<input class="input" type="password" id="password" name="password" placeholder="Hasło">
					</div>
                    
					<div class="container-login-form-btn">
						<div class="wrap-login-form-btn">
							<div class="login-form-bgbtn"></div>
							<button class="login-form-btn">
								Potwierdź
							</button>
						</div>
                    </div>
                    <div class="container-invitation">
						<span class="">Nie masz jeszcze konta? <a href="signup.php">Zarejestruj się</a></span>
                    </div>
				</form>
			</div>
		</div>
		<div id="cookie-bar">
		    <div class="cookie-inner">
				<span class="left-side">Strona używa plików cookie.</span>
				<span class="right-side">
					<button id="cookie-button" tabindex="0" onclick="acceptCookies();">Zgadzam się</button>
				</span>
			</div>
		</div>
	</div> 
	<script>
		function acceptCookies() {
			$("#cookie-bar").fadeOut();   // zanikanie przycisku
		}
	</script>
</body>
</html>