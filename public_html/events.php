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
if(isset($_GET['filter'])) {
    //Specified person's events
    $query = $mysqli->real_escape_string($_GET['filter']);
    $query = '%'.$query.'%'; //concat wildcard for like clause
    $events = $mysqli->query("SELECT e.first_name, e.surname, t1.tm, t1.isexit, p.title FROM employees e, professions p, (SELECT CAST(entry_date AS TIME) tm, isexit, id_employee from entries) t1 where e.id_employee=t1.id_employee and e.id_profession=p.id_profession and (e.first_name like '$query' or e.surname like '$query' or p.title like '$query') and p.id_company=$id_company");
}
elseif(isset($_GET['date'])) {
    //Specified day's events
    $currentDate = $mysqli->real_escape_string($_GET['date']); //date is string
    $events = $mysqli->query("SELECT e.first_name, e.surname, t1.tm, t1.isexit, p.title FROM employees e, professions p, (SELECT CAST(entry_date AS TIME) tm, isexit, id_employee from entries where cast(entry_date as date) = '$currentDate') t1 where e.id_employee=t1.id_employee and e.id_profession=p.id_profession and p.id_company=$id_company order by tm desc");
    $currentDate = date_create($currentDate); //convert back to date object
}
else {
    //Today's events
    $events = $mysqli->query("SELECT e.first_name, e.surname, t1.tm, t1.isexit, p.title FROM employees e, professions p, (SELECT CAST(entry_date AS TIME) tm, isexit, id_employee from entries where cast(entry_date as date)=cast(now() as date)) t1 where e.id_employee=t1.id_employee and e.id_profession=p.id_profession and p.id_company=$id_company order by tm desc");
}
?>
<!DOCTYPE html>
<html lang="pl-PL">
    <head>
        <meta charset="utf-8" />
        <link rel="stylesheet" href="static/style.css">
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css" integrity="sha384-UHRtZLI+pbxtHCWp1t77Bi1L4ZtiqrqD80Kn4Z8NTSRyMA2Fd33n5dQ8lWUE00s/" crossorigin="anonymous">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
        <title>Zdarzenia</title>
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
                <li class="active">
                    <a>
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
                    <h2>Zdarzenia</h2>
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
            <form class="filtering-form" action="./events.php" method="get">
                    <input id="filter-text" class="filtering-text-input" type="text" minlength=3 placeholder="Wyszukaj pracownika" name="filter">
                    <input type="submit" style="visibility: hidden" hidden />
            </form>
            <div class="day-switch">
                <div class="flat-btn">
                    <a href="?date=<?php echo date_format(date_sub($currentDate, date_interval_create_from_date_string("1 day")), 'Y-m-d'); ?>"><i class="fa fa-angle-left"></i></a>
                </div>
                <div class="day-inner">
                    <?php echo date_format(date_add($currentDate, date_interval_create_from_date_string("1 day")), 'd'); ?>
                    <div>
                        <?php echo date_format($currentDate, 'D'); ?>
                    </div>
                </div>
                <div class="flat-btn">
                    <a href="?date=<?php echo date_format(date_add($currentDate, date_interval_create_from_date_string("1 day")), 'Y-m-d'); ?>"><i class="fa fa-angle-right"></i></a>
                </div>
            </div>
            <table class="events-table">
                <tbody>
                    <?php while($e = $events->fetch_assoc()) { ?>
                    <tr class="event-row">
                        <td><?php echo $e['tm']; ?></td>
                        <?php if($e['isexit']==0) echo '<td class="in">Wejście'; else echo '<td class="out">Wyjście'; ?></td>
                        <td><?php echo $e['first_name']." ".$e['surname']." (".$e['title'].")"; ?></td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </body>
</html>