<?php 
$id_company_user = $_SESSION['user_id'];
$id_company = $mysqli->query("SELECT id_company id FROM companies WHERE id_user=$id_company_user")->fetch_assoc()['id'];
if(isset($_POST['title'], $_POST['email'])) {
    $title = $mysqli->real_escape_string($_POST['title']);
    $email = $mysqli->real_escape_string($_POST['email']);
    $phone = $mysqli->real_escape_string($_POST['phone']);
    $street = $mysqli->real_escape_string($_POST['street']);
    $postal_code = $mysqli->real_escape_string($_POST['city']);
    $city = $mysqli->real_escape_string($_POST['title']);
    $mysqli->query("UPDATE companies SET phone='$phone', title='$title', street='$street', postal_code='$postal_code', city='$city' WHERE id_company=$id_company");
    $mysqli->query("UPDATE users SET email=$email WHERE id_user=$id_company_user");
}
$settings = $mysqli->query("SELECT c.phone, c.title, c.street, c.postal_code, c.city, u.email FROM companies c, users u WHERE c.id_user=u.id_user and c.id_user=$id_company_user")->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="pl-PL">
    <head>
        <meta charset="utf-8" />
        <link rel="stylesheet" href="static/style.css">
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css" integrity="sha384-UHRtZLI+pbxtHCWp1t77Bi1L4ZtiqrqD80Kn4Z8NTSRyMA2Fd33n5dQ8lWUE00s/" crossorigin="anonymous">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
        <title>Przegląd</title>
    </head>
    <body>
        <nav class="navbar-default" role="navigation">
            <ul id="side-menu" class="nav">
                <li class="">
                    <a href="./dashboard.php">
                        <i class="fa fa-th"></i>Przegląd
                    </a>
                </li>
                <li class="">
                    <a href="./employees.php">
                    <i class="fa fa-users"></i>Pracownicy
                </a>
                </li>
                <li class="">
                    <a href="./events.php">
                    <i class="fa fa-clock"></i>Zdarzenia
                </a>
                </li>
                <li class="">
                    <a href="./reports.php">
                    <i class="fa fa-chart-line"></i>Raporty
                </a>
                </li>
            </ul>
        </nav>
        <div id="page-wrapper">
            <nav class="navbar navbar-static-top flexbox-sb" role="navigation">
                <div class="navbar-header">
                    <h2>Ustawienia</h2>
                </div>
                <ul class="navbar-top-links">
                    <li class="menu-item">
                        <a href="#"><i class="fa fa-cog"></i>Ustawienia</a>
                    </li>
                    <li class="menu-item">
                        <a href="./logout.php"><i class="fa fa-sign-out-alt"></i>Wyloguj</a>
                    </li>
                </ul>
            </nav>
            <?php
			if(isset($_POST['title'], $_POST['email'])) { ?>
				<div class="alert alert-success" id="alert" role="alert">
					  Zapisano ustawienia. 
				</div>
		    <?php } ?>
            <form class="employee-details-form" action="settings.php" method="post" onsubmit="return a();">
                <span>Podstawowe informacje</span>
                <div class="signup-row">
                    <div class="name">
                        Nazwa firmy
                    </div>
                    <div class="value column-fullwidth">
                        <div class="input-group">
                            <input class="gray-input" type="text" name="title" value="<?php echo htmlspecialchars($settings['title']); ?>" required>
                        </div>
                    </div>
                </div>
                <div class="flexbox-sb">
                    <div class="signup-row">
                        <div class="name">
                            E-mail
                        </div>
                        <div class="value">
                            <div class="input-group">
                                <input class="gray-input" type="text" name="email" maxlength="255" value="<?php echo htmlspecialchars($settings['email']); ?>" required>
                            </div>
                        </div>
                    </div>
                    <div class="signup-row">
                        <div class="name">
                            Telefon
                        </div>
                        <div class="value">
                            <div class="input-group">
                                <input class="gray-input" type="text" name="phone" maxlength="15" value="<?php echo htmlspecialchars($settings['phone']); ?>">
                            </div>
                        </div>
                    </div>
                </div>
                <span>Dane adresowe</span>
                <div class="signup-row">
                    <div class="name">
                        Ulica
                    </div>
                    <div class="value column-fullwidth">
                        <div class="input-group">
                            <input class="gray-input" type="text" name="street" value="<?php echo htmlspecialchars($settings['street']); ?>">
                        </div>
                    </div>
                </div>
                <div class="flexbox-sb">
                    <div class="signup-row">
                        <div class="name">
                            Kod pocztowy
                        </div>
                        <div class="value">
                            <div class="input-group">
                                <input class="gray-input" type="text" name="postal_code" maxlength="10" value="<?php echo htmlspecialchars($settings['postal_code']); ?>">
                            </div>
                        </div>
                    </div>
                    <div class="signup-row">
                        <div class="name">
                            Miasto
                        </div>
                        <div class="value">
                            <div class="input-group">
                                <input class="gray-input" type="text" name="city" maxlength="70" value="<?php echo htmlspecialchars($settings['city']); ?>">
                            </div>
                        </div>
                    </div>
                </div>
                <span>Zmiana hasła</form>
                <div class="signup-row">
                    <div class="name">
                        Stare hasło
                    </div>
                    <div class="value">
                        <div class="input-group">
                            <input id="prevpassword-input" class="gray-input" type="password" name="prev-password">
                        </div>
                    </div>
                </div>
                <div class="signup-row">
                    <div class="name">
                        Nowe hasło
                    </div>
                    <div class="value">
                        <div class="input-group">
                        <input id="password-input" class="gray-input" type="password" name="password">
                        </div>
                    </div>
                </div>
                <div class="signup-row">
                    <div class="name">
                        Potwierdź nowe hasło
                    </div>
                    <div class="value">
                        <div class="input-group">
                        <input id="passwordrepeat-input" class="gray-input" type="password" name="passwordrepeat">
                        </div>
                    </div>
                </div>
                <div class="save-btn employee-btn">
                    <input type="submit" value="Zapisz">
                </div>
            </form>
        </div>
        <script>
        function a() {  /* można zostawić pola poprzednie, aktualne, nowe PUSTE , wtedy hasło zostaje niezmienione */
                var prevpassword = $('#prevpassword-input').val();
                var password = $('#password-input').val();
                var passwordrepeat = $('#passwordrepeat-input').val();
                if (prevpassword.length > 0 && password.length > 0 && passwordrepeat.length > 0) {
                    if ($('#password-input').val()==$('#passwordrepeat-input').val()) {
                        return true;
                    }
                    else {
                        return false;
                    }
                }
                else {
                    $("#prevpassword-input").prop( "disabled", true );  /*po zatwierdzeniu formularza, wszystkie pola do hasła dezaktywują się i przez to nie są zawarte w zapytaniu post */
                    $("#password-input").prop( "disabled", true );
                    $("#passwordrepeat-input").prop( "disabled", true );
                    return true;
                }
        }
    </script>
    </body>
</html>