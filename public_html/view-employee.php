<?php
include_once 'includes/mysql_connect.php';
include_once 'includes/functions.php';
secure_session_start();
if (login_check($mysqli) == false || isCompany($mysqli) == false) 
{
    header('Location: ./?error=3'); /*zmiana z .. na . */
}
$id_company_user = $_SESSION['user_id'];
$id_company = $mysqli->query("SELECT id_company id FROM companies WHERE id_user=$id_company_user")->fetch_assoc()['id'];
$professions = $mysqli->query("SELECT title t FROM professions WHERE id_company=$id_company"); //Retrieve list for combobox
if (isset($_GET['id'])) {
    //Show properties of existing user
    $id_employee = $mysqli->real_escape_string($_GET['id']);
    $currentEmployee = $mysqli->query("SELECT e.first_name, e.surname, p.title, e.phone, e.street, e.postal_code, e.city, e.salary_standard, e.salary_overtime, u.email FROM employees e, professions p, users u WHERE p.id_profession=e.id_profession and e.id_user = u.id_user and id_employee=$id_employee;")->fetch_assoc();
    $first_name = htmlspecialchars($currentEmployee['first_name']);
    $surname = htmlspecialchars($currentEmployee['surname']);
    $profession = htmlspecialchars($currentEmployee['title']);
    $email = htmlspecialchars($currentEmployee['email']);
    $phone = htmlspecialchars($currentEmployee['phone']);
    $street = htmlspecialchars($currentEmployee['street']);
    $postal_code = htmlspecialchars($currentEmployee['postal_code']);
    $city = htmlspecialchars($currentEmployee['city']);
    $salary_standard = htmlspecialchars($currentEmployee['salary_standard']);
    $salary_overtime = htmlspecialchars($currentEmployee['salary_overtime']);
    $pageTitle = htmlspecialchars($first_name)." ".htmlspecialchars($surname);
}
else {
    //Add new user, show empty form
    $first_name = "";
    $surname = "";
    $profession = "";
    $email = "";
    $phone = "";
    $street = "";
    $postal_code = "";
    $city = "";
    $salary_standard = "";
    $salary_overtime = "";
    $pageTitle = "Dodaj pracownika";
}
if(isset($_POST['existing_user_id'])) {
    //Update existing employee
    $id_employee = $mysqli->real_escape_string($_POST['existing_user_id']);
    $first_name = $mysqli->real_escape_string($_POST['first_name']);
    $surname = $mysqli->real_escape_string($_POST['surname']);
    $profession = $mysqli->real_escape_string($_POST['profession']);
    $email = $mysqli->real_escape_string($_POST['email']);
    $phone = $mysqli->real_escape_string($_POST['phone']);
    $street = $mysqli->real_escape_string($_POST['street']);
    $postal_code = $mysqli->real_escape_string($_POST['postal_code']);
    $city = $mysqli->real_escape_string($_POST['city']);
    $salary_standard = $mysqli->real_escape_string(str_replace(',', '.', $_POST['salary_standard']));
    $salary_overtime = $mysqli->real_escape_string(str_replace(',', '.', $_POST['salary_overtime']));
    $mysqli->autocommit(FALSE);
    $mysqli->query("SET TRANSACTION ISOLATION LEVEL REPEATABLE READ");
    $checkExisting = $mysqli->query("SELECT id_employee FROM employees WHERE id_employee = '$id_employee'")->fetch_assoc(); //check if employee with this id exist and we can update it
    $id_user = $mysqli->query("SELECT id_user id FROM employees WHERE id_employee = $id_employee")->fetch_assoc()['id'];
    $checkRepeat = $mysqli->query("SELECT id_user id FROM users WHERE email = '$email' AND NOT id_user = $id_user")->fetch_assoc();
    print_r($mysqli->error);
    if ($checkRepeat) {
        $mysqli->commit();
        header('Location: ./view-employee.php?id='.$id_employee.'&error=4'); //Taki e-mail już istnieje /*zmiana z .. na . */
    }
    elseif($checkExisting) {
        $id_profession = ($mysqli->query("SELECT id_profession id FROM professions WHERE title='$profession' AND id_company=$id_company"))->fetch_assoc()['id'];
        $mysqli->query("UPDATE employees SET first_name='$first_name', surname='$surname', id_profession=$id_profession, phone='$phone', street='$street', postal_code='$postal_code', city='$city', salary_standard=$salary_standard, salary_overtime=$salary_overtime WHERE id_employee=$id_employee");
        $mysqli->commit();
        header('Location: ./employees.php?info=2'); //info: zaktualizowano dane pracownika /*zmiana z .. na . */
    }
    else {
        $mysqli->commit();
        header('Location: ./employees.php'); //Nie ma takiego użytkownika, błędne existing_user_id /*zmiana z .. na . */
    }
}
elseif(isset($_POST['first_name'], $_POST['surname'], $_POST['email'], $_POST['password'], $_POST['salary_standard'], $_POST['salary_overtime']))
{
    //Insert new employee
    $first_name = $mysqli->real_escape_string($_POST['first_name']);
    $surname = $mysqli->real_escape_string($_POST['surname']);
    $profession = $mysqli->real_escape_string($_POST['profession']);
    $email = $mysqli->real_escape_string($_POST['email']);
    $phone = $mysqli->real_escape_string($_POST['phone']);
    $street = $mysqli->real_escape_string($_POST['street']);
    $postal_code = $mysqli->real_escape_string($_POST['postal_code']);
    $city = $mysqli->real_escape_string($_POST['city']);
    $salary_standard = $mysqli->real_escape_string(str_replace(',', '.', $_POST['salary_standard']));
    $salary_overtime = $mysqli->real_escape_string(str_replace(',', '.', $_POST['salary_overtime']));
    $password = $mysqli->real_escape_string($_POST['password']);
    $password = password_hash($password, PASSWORD_DEFAULT);
    $mysqli->autocommit(FALSE);
    $mysqli->query("SET TRANSACTION ISOLATION LEVEL REPEATABLE READ");
    $checkRepeat = $mysqli->query("SELECT id_user FROM users WHERE email='$email'")->fetch_assoc();
    if($checkRepeat) {
        $mysqli->commit();
        header('Location: ./view-employee.php?error=4'); //error: Takie konto już istnieje, powtórka adresu e-mail. /*zmiana z .. na . */
    }
    else {
        $mysqli->query("INSERT INTO users (email, password_hash) values ('$email', '$password');");
        $id_user = $mysqli->query("SELECT LAST_INSERT_ID()")->fetch_assoc()['LAST_INSERT_ID()'];
        $id_profession = $mysqli->query("SELECT id_profession id FROM professions WHERE title='$profession' AND id_company=$id_company")->fetch_assoc()['id'];
        $mysqli->query("INSERT INTO employees (first_name, surname, id_profession, phone, street, postal_code, city, salary_standard, salary_overtime, id_user) values ('$first_name', '$surname', $id_profession, '$phone', '$street', '$postal_code', '$city', $salary_standard, $salary_overtime, $id_user);");

        $mysqli->commit();
       header('Location: ./employees.php?info=1'); //info: Założono nowe konto /*zmiana z .. na . */
    }
}
?>

<!DOCTYPE html>
<html lang="pl-PL">
    <head>
        <meta charset="utf-8" />
        <link rel="stylesheet" href="static/style.css">
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css" integrity="sha384-UHRtZLI+pbxtHCWp1t77Bi1L4ZtiqrqD80Kn4Z8NTSRyMA2Fd33n5dQ8lWUE00s/" crossorigin="anonymous">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
        <title><?php echo $pageTitle ?></title>
    </head>
    <body>
        <nav class="navbar-default" role="navigation">
            <ul id="side-menu" class="nav">
                <li class="">
                    <a href="./dashboard.php">
                        <i class="fa fa-th"></i>Przegląd
                    </a>
                </li>
                <li class="active">
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
                    <h2><?php echo $pageTitle ?></h2>
                </div>
                <ul class="navbar-top-links">
                    <li class="menu-item">
                        <a href="./settings.php"><i class="fa fa-cog"></i>Ustawienia</a>
                    </li>
                    <li class="menu-item">
                        <a href="./logout.php"><i class="fa fa-sign-out-alt"></i>Wyloguj</a>
                    </li>
                </ul>
            </nav>
            <form class="employee-details-form" action="./view-employee.php" method="post">
                <div class="flexbox-sb">
                    <div class="signup-row">
                        <div class="name">
                            Imię
                        </div>
                        <div class="value">
                            <div class="input-group">
                                <input class="gray-input" type="text" name="first_name" value="<?php echo $first_name; ?>" required>
                            </div>
                        </div>
                    </div>
                    <div class="signup-row">
                        <div class="name">
                            Nazwisko
                        </div>
                        <div class="value">
                            <div class="input-group">
                                <input class="gray-input" type="text" name="surname" value="<?php echo $surname; ?>" required>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="signup-row">
                    <div class="name">
                        Dział
                    </div>
                    <div class="value column-fullwidth">
                        <div class="input-group">
                            <select id="profession-input" class="gray-input" name="profession" value="<?php echo $profession; ?>">
                                <?php 
                                while($prof = $professions->fetch_assoc())
                                {echo '<option value="'.$prof['t'].'">'.$prof['t'].'</option>';}
                                ?>
      		                </select>
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
                                <input class="gray-input" type="text" name="email" maxlength="255" value="<?php echo $email; ?>" required>
                            </div>
                        </div>
                    </div>
                    <div class="signup-row">
                        <div class="name">
                            Telefon
                        </div>
                        <div class="value">
                            <div class="input-group">
                                <input class="gray-input" type="text" name="phone" maxlength="15" value="<?php echo $phone; ?>">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="signup-row">
                    <div class="name">
                        Ulica
                    </div>
                    <div class="value column-fullwidth">
                        <div class="input-group">
                            <input class="gray-input" type="text" name="street" value="<?php echo $street; ?>">
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
                                <input class="gray-input" type="text" name="postal_code" maxlength="10" value="<?php echo $postal_code; ?>">
                            </div>
                        </div>
                    </div>
                    <div class="signup-row">
                        <div class="name">
                            Miasto
                        </div>
                        <div class="value">
                            <div class="input-group">
                                <input class="gray-input" type="text" name="city" maxlength="70" value="<?php echo $city; ?>">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="signup-row">
                    <div class="name">
                        Hasło
                    </div>
                    <div class="value column-fullwidth">
                        <div class="input-group">
                            <?php if(!isset($_GET['id'])) { //new employee ?>
                                <input class="gray-input" type="password" name="password" minlength="5" required>
                            <?php } else { //modify existing employee ?> 
                                <input class="gray-input" type="password" name="password" style="background-color: #f5f5f5" disabled="">
                                <input type="text" name="existing_user_id" style="visibility: hidden" value="<?php echo htmlspecialchars($_GET['id']); ?>" hidden />
                            <?php }?>
                        </div>
                    </div>
                </div>
                <div class="flexbox-sb">
                    <div class="signup-row">
                        <div class="name">
                            Stawka godz.
                        </div>
                        <div class="value">
                        <div class="input-group">
                            <input class="gray-input" type="currency" name="salary_standard" value="<?php echo $salary_standard; ?>" required>
                        </div>
                        </div>
                    </div>
                    <div class="signup-row">
                        <div class="name">
                            Stawka nadgodz.
                        </div>
                        <div class="value">
                            <div class="input-group">
                                <input class="gray-input" type="currency" name="salary_overtime" value="<?php echo $salary_overtime; ?>" required>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="save-btn employee-btn">
                    <input type="submit" value="Zapisz">
                </div>
            </form>

            <?php
                if (isset($_GET['error'])) { ?>
				<div class="alert alert-info" id="alert" role="alert">
                    <?php if($_GET['error']==4) echo 'Taki e-mail już istnieje.'; ?> 
				</div>
		    <?php } ?>
        </div>
        <script>
            var currencyInput = document.querySelector('input[type="currency"]');
            var currency = 'PLN';

            currencyInput.addEventListener('focus', onFocus); /*funkcja onFocus będie wykonywana wtedy gdy bedzie focus*/
            currencyInput.addEventListener('blur', onBlur);  /*analogicznie*/

            function localStringToNumber( s ){  /*zamiana przecinka na kropke, usunięcie wszystkich innych znaków*/
                s = String(s).replace(",", ".");
                return Number(String(s).replace(/[^0-9.-]+/g,""));
            }

            function onFocus(e){
            var value = e.target.value;
            //e.target.value = value ? localStringToNumber(value) : '';
            }

            function onBlur(e){  /*gdy straci fokus*/
            var value = e.target.value;

            const options = {   /* zaokrągla*/
                minimumFractionDigits : 2,
                maximumFractionDigits : 2
            }
            
            e.target.value = value 
                ? localStringToNumber(value).toLocaleString(undefined, options)
                : ''    /* zamienia na cyfry*/
            }
        </script>
    </body>
</html>