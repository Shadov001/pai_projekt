<?php
include_once 'includes/mysql_connect.php';
include_once 'includes/functions.php';
secure_session_start();
if (login_check($mysqli) == false || isCompany($mysqli) == false) 
{
    header('Location: ./?error=3');  /*zmiana z .. na . */
}
$id_company_user = $_SESSION['user_id'];
$id_company = $mysqli->query("SELECT id_company id FROM companies WHERE id_user=$id_company_user")->fetch_assoc()['id'];
$currentDate = date_create(); //today
if(isset($_POST['visibility'])) {
    $_SESSION['column-1'] = isset($_POST['column-1']);
    $_SESSION['column-2'] = isset($_POST['column-2']);
    $_SESSION['column-3'] = isset($_POST['column-3']);
    $_SESSION['column-4'] = isset($_POST['column-4']);
    $_SESSION['column-5'] = isset($_POST['column-5']);
}
if(isset($_GET['date'])) {
    //Specified month
    $currentDate = $mysqli->real_escape_string($_GET['date']); //date is string
    $employees = $mysqli->query("SELECT DISTINCT id_employee, first_name, surname FROM daily_working_time WHERE year(entry_day) = year('$currentDate') AND month(entry_day) = month('$currentDate') AND id_company=$id_company;");
    $currentDate = date_create($currentDate); //convert back to date object
}
else {
    //Current month
    $employees = $mysqli->query("SELECT DISTINCT id_employee, first_name, surname FROM daily_working_time WHERE year(entry_day) = year(CURDATE()) AND month(entry_day) = month(CURDATE()) AND id_company=$id_company;");
}
?>
<!DOCTYPE html>
<html lang="pl-PL">
    <head>
        <meta charset="utf-8" />
        <link rel="stylesheet" href="static/style.css">
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css" integrity="sha384-UHRtZLI+pbxtHCWp1t77Bi1L4ZtiqrqD80Kn4Z8NTSRyMA2Fd33n5dQ8lWUE00s/" crossorigin="anonymous">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
        <title>Raporty</title>
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
                <li class="active">
                    <a>
                        <i class="fa fa-chart-line"></i>Raporty
                    </a>
                </li>
            </ul>
        </nav>
        <div id="page-wrapper">
            <nav class="navbar navbar-static-top flexbox-sb" role="navigation">
                <div class="navbar-header">
                    <h2>Raporty</h2>
                    <div class="row">
                        <div class="month-switch">
                            <div class="flat-btn">
                                <a href="?date=<?php echo date_format(date_sub($currentDate, date_interval_create_from_date_string("1 month")), 'Y-m-d'); ?>"><i class="fa fa-angle-left"></i></a>
                            </div>
                            <div class="month-inner">
                                <?php echo date_format(date_add($currentDate, date_interval_create_from_date_string("1 month")), 'F Y'); ?>
                            </div>
                            <div class="flat-btn">
                                <a href="?date=<?php echo date_format(date_add($currentDate, date_interval_create_from_date_string("1 month")), 'Y-m-d'); date_sub($currentDate, date_interval_create_from_date_string("1 month")); ?>"><i class="fa fa-angle-right"></i></a>
                            </div>
                        </div>
                        <div class="flat-btn">
                            <a onclick="window.print();"><i class="fa fa-print"></i></a>
                        </div>
                        <div class="flat-btn">
                            <a href="#viewingModal"><i class="fa fa-cog"></i></a>
                        </div>
                    </div>
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
            <table class="reports-table">
                <thead>
                    <th></th>
                    <th>Osoba</th>
                    <th <?php if(isset($_SESSION['column-1']) && $_SESSION['column-1'] == false) echo 'style="display:none;"'; ?>>Norma</th>
                    <th <?php if(isset($_SESSION['column-2']) && $_SESSION['column-2'] == false) echo 'style="display:none;"'; ?>>Czas pracy</th>
                    <th <?php if(isset($_SESSION['column-3']) && $_SESSION['column-3'] == false) echo 'style="display:none;"'; ?>>Nadgodziny</th>
                    <th <?php if(isset($_SESSION['column-4']) && $_SESSION['column-4'] == false) echo 'style="display:none;"'; ?>>Obecność</th>
                    <th <?php if(isset($_SESSION['column-5']) && $_SESSION['column-5'] == false) echo 'style="display:none;"'; ?>>Wynagrodzenie</th>
                </thead>
                <tbody>
                    <?php 
                    $i=1;
                    while($e = $employees->fetch_assoc()) {
                        $id_employee = $e['id_employee'];
                        $currentDateString = date_format($currentDate, 'Y-m-d'); //currentDate ToString
                        $days = $mysqli->query("SELECT dayofweek(entry_day) week, entry_day, d_seconds FROM daily_working_time where id_employee=$id_employee and year(entry_day) = year('$currentDateString') and month(entry_day) = month('$currentDateString')");
                        $salary = $mysqli->query("SELECT salary_standard standard, salary_overtime overtime from employees WHERE id_employee = $id_employee")->fetch_assoc();
                        $dayCount = 0;
                        $hourSum = 0;
                        $overtimeSum = 0;
                        $salarySum =0;
                        while($d = $days->fetch_assoc()) {
                            $dayCount++;
                            if($d['week']==1 || $d['week']==7) {
                                //Sunday or Saturday
                                $salarySum += $salary['overtime']*($d['d_seconds']/3600);
                                $overtimeSum += $d['d_seconds'];
                            }
                            else {
                                if(($d['d_seconds']/3600)<=8) {
                                    $salarySum += $salary['standard']*($d['d_seconds']/3600); 
                                    $hourSum += $d['d_seconds']/3600;
                                }
                                else {
                                    $salarySum += $salary['standard']*8 + $salary['overtime']*(($d['d_seconds']/3600)-8);
                                    $hourSum += 8;
                                    $overtimeSum += ($d['d_seconds'])-8*3600;
                                }
                            }
                        }
                        ?>
                    <tr class="report-row">
                        <td><?php echo $i; ?></td>
                        <td><?php echo htmlspecialchars($e['first_name']." ".$e['surname']) ?></td>
                        <td <?php if(isset($_SESSION['column-1']) && $_SESSION['column-1'] == false) echo 'style="display:none;"'; ?>><?php echo $dayCount*8; echo "h" ?></td>
                        <td <?php if(isset($_SESSION['column-2']) && $_SESSION['column-2'] == false) echo 'style="display:none;"'; ?>><?php echo round($hourSum)."h"; ?></td>
                        <td <?php if(isset($_SESSION['column-3']) && $_SESSION['column-3'] == false) echo 'style="display:none;"'; ?>><?php echo sprintf('%02dh:%02dm', ($overtimeSum/3600),($overtimeSum/60%60)); ?></td>
                        <td <?php if(isset($_SESSION['column-4']) && $_SESSION['column-4'] == false) echo 'style="display:none;"'; ?>><?php echo $dayCount; ?></td>
                        <td <?php if(isset($_SESSION['column-5']) && $_SESSION['column-5'] == false) echo 'style="display:none;"'; ?>><?php echo round($salarySum, 2); echo " PLN"; ?></td>
                    </tr>
                    <?php $i++; } ?>
                </tbody>
            </table>
            <div id="viewingModal" class="modalDialog">
                <div>
                    <a href="#close" title="Close" class="close">X</a>
                    <h2>Ustawienia wyświetlania</h2>
                    <form class="profession-form" action="./reports.php" method="post">
                        <div class="signup-row">
                            <input type="text" name="visibility" value="" hidden>
                            <input id="accept-input" name="column-1" type="checkbox" <?php if(isset($_SESSION['column-1']) && $_SESSION['column-1']) echo 'checked'; ?>>Norma
                        </div>
                        <div class="signup-row">
                            <input id="accept-input" name="column-2" type="checkbox" <?php if(isset($_SESSION['column-2']) && $_SESSION['column-2']) echo 'checked'; ?>>Czas pracy
                        </div>
                        <div class="signup-row">
                            <input id="accept-input" name="column-3" type="checkbox" <?php if(isset($_SESSION['column-3']) && $_SESSION['column-3']) echo 'checked'; ?>>Nadgodziny
                        </div>
                        <div class="signup-row">
                            <input id="accept-input" name="column-4" type="checkbox" <?php if(isset($_SESSION['column-4']) && $_SESSION['column-4']) echo 'checked'; ?>>Obecność
                        </div>
                        <div class="signup-row">
                            <input id="accept-input" name="column-5" type="checkbox" <?php if(isset($_SESSION['column-5']) && $_SESSION['column-5']) echo 'checked'; ?>>Wynagrodzenie
                        </div>
                        <div class="save-btn">
                            <input type="submit" value="Zapisz">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </body>
</html>