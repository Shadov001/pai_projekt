<?php
include_once 'includes/mysql_connect.php';
include_once 'includes/functions.php';
secure_session_start();
if (login_check($mysqli) == false || isCompany($mysqli) == false) 
{
    header('Location: ./?error=3'); //Najpierw należy się zalogować   /*zmiana z .. na . */
}
$id_company_user = $_SESSION['user_id'];
$id_company = $mysqli->query("SELECT id_company id FROM companies WHERE id_user=$id_company_user")->fetch_assoc()['id'];
if(isset($_GET['delete'])) {
    $deleteId = $mysqli->real_escape_string($_GET['delete']);
    $deleteUserId = $mysqli->query("SELECT id_user FROM employees WHERE id_employee=$deleteId")->fetch_assoc()['id_user'];
    $mysqli->query("DELETE FROM entries WHERE id_employee=$deleteId");
    $mysqli->query("DELETE FROM employees WHERE id_employee=$deleteId");
    $mysqli->query("DELETE FROM users WHERE id_user=$deleteUserId");
}
if(isset($_POST['profession'])) {
    $add_profession = $mysqli->real_escape_string($_POST['profession']);
    $mysqli->query("INSERT INTO professions (title, id_company) VALUES ('$add_profession', $id_company)");
}
$professions = $mysqli->query("SELECT professions.title, count(e.id_profession) as cn from professions left join (select e.id_profession from employees e, professions p where e.id_profession=p.id_profession and p.id_company=$id_company) e on (professions.id_profession = e.id_profession) where professions.id_company=$id_company group by professions.id_profession order by cn desc"); //Retrieve list
if(isset($_GET['filter'])) {
    $query = $mysqli->real_escape_string($_GET['filter']);
    $query = '%'.$query.'%';  /*jeśli są procenty to znaczy ze fraza moze być gdziekolwiek w imieniu*/
    $employees_list = $mysqli->query("CALL find('$query', $id_company);");  /*procedura używana do filtrowania pracowników (wyszukiwanie*/
}
else {
    $employees_list = $mysqli->query("SELECT e.id_employee, e.first_name, e.surname, p.title from employees e, professions p where e.id_profession=p.id_profession and p.id_company=$id_company");
}
?>
<!DOCTYPE html>
<html lang="pl-PL">
    <head>
        <meta charset="utf-8" />
        <link rel="stylesheet" href="static/style.css">
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css" integrity="sha384-UHRtZLI+pbxtHCWp1t77Bi1L4ZtiqrqD80Kn4Z8NTSRyMA2Fd33n5dQ8lWUE00s/" crossorigin="anonymous">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
        <title>Pracownicy</title>
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
                    <h2>Pracownicy</h2>
                    <div class="row">
                        <div class="flat-btn">
                            <a href="view-employee.php"><i class="fa fa-plus"></i></a>
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
            <form class="filtering-form" action="./employees.php" method="get" onsubmit="return isFilled();">
                    <input id="filter-text" class="filtering-text-input" type="text" placeholder="Wyszukaj pracownika" name="filter">
                    <input type="submit" style="visibility: hidden" hidden />
            </form>
            <table class="employees-wrapper" cellpadding="0" cellspacing="0">
                <thead>
                    <th class="flexbox-sb double-table-header">
                        <span>Imie i nazwisko</span>
                        <span>Dział</span>
                    </th>
                    <th class="table-with-border">Nazwa działu</th>
                </thead>
                <tbody>
                    <tr>
                        <td class="table-outer">
                            <table>
                                <tbody>
                                <?php while($em = $employees_list->fetch_assoc()) { ?>
                                    <tr class="employee-row">
                                        <td>
                                            <input id="employee-checkbox" type="checkbox">
                                        </td>
                                        <td class="cell-25">
                                            <?php echo htmlspecialchars($em['first_name']." ".$em['surname']); ?>
                                        </td>
                                        <td class="cell-10">
                                            <?php echo htmlspecialchars($em['title']); ?>
                                        </td>
                                        <td>
                                            <a class="circle-btn" href="view-employee.php?id=<?php echo $em['id_employee']; ?>"><i class="fa fa-pencil-alt"></i></a>
                                        </td>
                                        <td>
                                            <a class="circle-btn" href="?remove=<?php echo $em['id_employee']; ?>#deletingModal"><i class="fa fa-trash-alt"></i></a>
                                        </td>
                                    </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </td>
                        <td class="table-with-border">
                        <?php while($prof = $professions->fetch_assoc()) { ?>
                            <div class="profession-btn">
                                <a href="#">
                                    <span><?php echo htmlspecialchars($prof['title']); ?></span>
                                    <span><?php echo $prof['cn']; ?></span>
                                </a>
                            </div>
                                <?php } ?>
                            <div class="profession-btn">
                                <a href="#professionModal">
                                    <span style="font-weight: 700; text-align: center; width: 100%;">Dodaj dział</span>
                                </a>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
            <div id="professionModal" class="modalDialog">
                <div>
                    <a href="#close" title="Close" class="close">X</a>
                    <h2>Dodaj dział</h2>
                    <form class="profession-form" action="./employees.php" method="post">
                        <div class="signup-row">
                            <div class="name">
                                Nazwa
                            </div>
                            <div class="value">
                                <div class="input-group">
                                    <input class="gray-input" type="text" name="profession" value="" required>
                                </div>
                            </div>
                        </div>
                        <div class="save-btn">
                            <input type="submit" value="Zapisz">
                        </div>
                    </form>
                </div>
            </div>

            <div id="deletingModal" class="modalDialog">
                <div>
                    <a href="#close" title="Close" class="close">X</a>
                    <h2>Usunąć pracownika?</h2>
                    <div class="save-btn">
                        <a href="?delete=<?php echo $_GET['remove'];?>">Potwiedź</a>
                    </div>
                </div>
            </div>

            <?php
                if (isset($_GET['info'])) { ?>
				<div class="alert alert-info" id="alert" role="alert">
                    <?php if($_GET['info']==1) echo 'Założono nowe konto.';
                          elseif($_GET['info']==2) echo 'Zaktualizowano dane pracownika.'; ?> 
				</div>
		    <?php } ?>
        </div>
        <script>
            function isFilled() {
                if ($('#filter-text').val().length<3) {
                    return false;
                }
                else {
                    return true;
                }
            }
    </script>
    </body>
</html>