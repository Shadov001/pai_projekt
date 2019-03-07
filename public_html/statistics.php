<?php
include_once 'includes/mysql_connect.php';
include_once 'includes/functions.php';
secure_session_start();
if (login_check($mysqli) == false || isCompany($mysqli) == true)
{
    header('Location: ./?error=3');  /*zmiana z .. na . */
}
$id_user = $_SESSION['user_id'];
$id_employee = $mysqli->query("SELECT id_employee id FROM employees WHERE id_user=$id_user")->fetch_assoc()['id'];
$working_days = $mysqli->query("SELECT dayofweek(entry_day) week, entry_day, d_seconds FROM daily_working_time WHERE id_employee = $id_employee");
$salary = $mysqli->query("SELECT salary_standard standard, salary_overtime overtime from employees WHERE id_employee = $id_employee")->fetch_assoc();
?>
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
            <div class="wrap-login">
                <div class="flat-btn" style="margin: -60px 0px 22px -52px;">
                    <a class=""href="dashboard.php"><i class="fa fa-arrow-left"></i></a>
                </div>
                <span class="login-form-title">
					Statystyki
                </span>
                <table class="stats-table">
                    <thead>
                        <th>Dzień</th>
                        <th>Czas pracy</th>
                        <th>Zarobki</th>
                    </thead>
                    <tbody>
                        <?php while($e = $working_days->fetch_assoc()) { 
                            if($e['week']==1 || $e['week']==7) {
                                //Sunday or Saturday
                                $money = $salary['overtime']*($e['d_seconds']/3600); 
                            }
                            else {
                                if(($e['d_seconds']/3600)<=8) {
                                    $money = $salary['standard']*($e['d_seconds']/3600); 
                                }
                                else {
                                    $money = $salary['standard']*8 + $salary['overtime']*(($e['d_seconds']/3600)-8);
                                }
                            }?>
                        <tr class="event-row">
                            <td><?php echo $e['entry_day']; ?></td>
                            <td><?php echo sprintf('%02d:%02d', ($e['d_seconds']/3600),($e['d_seconds']/60%60)); ?></td>
                            <td><?php echo round($money,2); ?></td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
		</div>
	</div>
</body>
</html>