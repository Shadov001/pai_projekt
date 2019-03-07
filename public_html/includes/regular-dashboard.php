<?php
$id_user = $_SESSION['user_id'];
$id_employee = $mysqli->query("SELECT id_employee id FROM employees WHERE id_user=$id_user")->fetch_assoc()['id'];
//If employee is in office, last_status = 0, if left office, last_status = 1
$last_status = $mysqli->query("SELECT isexit FROM entries WHERE id_employee=$id_employee ORDER BY entry_date DESC LIMIT 1")->fetch_assoc();
if($last_status) {
    $last_status = $last_status['isexit'];
}
else {
    $last_status = 1;
}

if(isset($_GET['task'])) {
    if($_GET['task']=='in' && $last_status == 1) {
        $mysqli->query("INSERT INTO entries (entry_date, id_employee, isexit) VALUES (now(), (select id_employee from employees where id_user=$id_user), 0)");
        header('Location: ./dashboard.php'); /*zmiana z .. na . */
    }
    elseif($_GET['task']=='out' && $last_status == 0) {
        $mysqli->query("INSERT INTO entries (entry_date, id_employee, isexit) VALUES (now(), (select id_employee from employees where id_user=$id_user), 1)");
        header('Location: ./dashboard.php'); /*zmiana z .. na . */
    }
}
?>
<!DOCTYPE html>
<html lang="pl-PL">
    <head>
        <meta charset="utf-8" />
        <link rel="stylesheet" href="static/style.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
        <title>Przegląd</title>
    </head>
<body>
	<div class="limiter">
		<div class="container-login">
        <?php
			if (isset($_GET['info']) && $_GET['info']==1) { ?>
				<div class="alert alert-success" id="alert" role="alert">
					  Hasło zostało zmienione. 
				</div>
		<?php } ?>
            <div class="wrap-login">
                <div class="in-out-buttons">
                    <ul>
						<li class="">
                            <a <?php if($last_status==1) echo 'href="./dashboard.php?task=in"'; else echo 'class="inactive"'; ?>>Wejście</a>
                        </li>
                        <li class="">
                            <a <?php if($last_status==0) echo 'href="./dashboard.php?task=out"'; else echo 'class="inactive"'; ?>>Wyjście</a>
                        </li>
                    </ul>
                </div>
                <nav class="navbar-regular" role="navigation">
                    <ul class="flexbox-sb">
                        <li class="">
                            <a href="./settings.php">Ustawienia</a>
                        </li>
                        <li class="">
                            <a href="./statistics.php">Statystyki</a>
                        </li>
                        <li class="">
                            <a href="./logout.php">Wyloguj</a>
                        </li>
                    </ul>
                </nav>
            </div>
		</div>
	</div>
</body>
</html>