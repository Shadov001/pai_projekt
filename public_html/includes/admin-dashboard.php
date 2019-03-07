<?php 
$id_company_user = $_SESSION['user_id'];
$id_company = $mysqli->query("SELECT id_company id FROM companies WHERE id_user=$id_company_user")->fetch_assoc()['id'];
$events = $mysqli->query("SELECT CAST(e.entry_date AS TIME) tm, e.isexit, s.first_name, s.surname FROM entries e, employees s, professions p WHERE e.id_employee=s.id_employee and s.id_profession=p.id_profession and p.id_company = $id_company ORDER BY entry_date DESC LIMIT 6");
$longest_work = $mysqli->query("SELECT first_name, surname, d_seconds FROM daily_working_time WHERE d_seconds=(SELECT MAX(d_seconds) FROM daily_working_time) AND id_company=$id_company")->fetch_assoc();
$shortest_work = $mysqli->query("SELECT first_name, surname, d_seconds FROM daily_working_time WHERE d_seconds=(SELECT MIN(d_seconds) FROM daily_working_time) AND id_company=$id_company")->fetch_assoc();
$most_active = $mysqli->query("SELECT first_name, surname FROM days_of_work WHERE id_company=$id_company ORDER BY work_days DESC LIMIT 1;")->fetch_assoc();
$least_active = $mysqli->query("SELECT first_name, surname FROM days_of_work WHERE id_company=$id_company ORDER BY work_days ASC LIMIT 1;")->fetch_assoc();
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
                <li class="active">
                    <a>
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
                    <h2>Przegląd</h2>
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
            <div class="section-title">Statystyki firmy</div>
                <div class="stats-flexbox flexbox-sb">
                    <div>
                        <span>Najwięcej godzin pracy</span><br>
                        <span><?php echo sprintf('%02d:%02d', ($longest_work['d_seconds']/3600),($longest_work['d_seconds']/60%60)); ?></span><br>
                        <span><?php echo $longest_work['first_name']." ".$longest_work['surname']; ?></span>
                    </div>
                    <div>
                        <span>Najmniej godzin pracy</span><br>
                        <span><?php echo sprintf('%02d:%02d', ($shortest_work['d_seconds']/3600),($shortest_work['d_seconds']/60%60)); ?></span><br>
                        <span><?php echo $shortest_work['first_name']." ".$shortest_work['surname']; ?></span>
                    </div>
                    <div>
                        <span>Najmniej obecności</span><br>
                        <span><?php echo $least_active['first_name']." ".$least_active['surname']; ?></span>
                    </div>
                    <div>
                        <span>Najwięcej obecności</span><br>
                        <span><?php echo $most_active['first_name']." ".$most_active['surname']; ?></span>
                    </div>
                </div>
            <div class="section-title">Ostatnie zdarzenia</div>
            <table>
                <tbody>
                    <?php while($e = $events->fetch_assoc()) { ?>
                    <tr class="event-row">
                        <td><?php echo $e['tm']; ?></td>
                        <?php if($e['isexit']==0) echo '<td class="in">Wejście'; else echo '<td class="out">Wyjście'; ?></td>
                        <td><?php echo $e['first_name']." ".$e['surname']; ?></td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </body>
</html>