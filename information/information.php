<?php require_once '../database.php';
//question 6
$facility1 = $conn->prepare('SELECT*FROM facilities ORDER BY province ASC, city ASC, facilities.type ASC, capacity ASC');
$facility1->execute();
//question 7

if (isset($_GET['mcn'])) {
    // Retrieve schedule for the specified MCN
    $statement = $conn->prepare("SELECT* FROM workat a join employees b where Fname = '{$_GET['mcn']}' and a.MCN =b.MCN and end_time is null order by a.role asc,b.first_name asc,b.last_name asc ");
    $statement->execute();
    $factoryy = $statement->fetchAll(PDO::FETCH_ASSOC);
}
//9
$facility2 = $conn->prepare("SELECT f.province, f.name, f.capacity, COUNT(i.MCN) AS total_infected
FROM facilities f
LEFT JOIN workat w ON f.name = w.Fname AND f.address = w.Faddress AND f.type = w.Ftype
LEFT JOIN infection_history i ON w.MCN = i.MCN AND i.date >= DATE_SUB(CURRENT_DATE(), INTERVAL 2 WEEK) 
WHERE i.type = 'COVID-19'and role='Doctor'
GROUP BY f.province, f.name, f.capacity
ORDER BY  f.province ASC,total_infected ASC");
$facility2->execute();
//16
$facility3 = $conn->prepare(
    "SELECT first_name, e.last_name, e.city, COUNT( distinct w.Fname) AS num_facilities
FROM employees e
JOIN workat w ON e.MCN = w.MCN
JOIN facilities c ON c.address = w.Faddress AND c.type = w.Ftype
WHERE c.province = 'Quebec' AND w.end_time IS NULL
GROUP BY e.first_name, e.last_name, e.city
ORDER BY city ASC,num_facilities DESC"
);
$facility3->execute();
//14
$facility4 = $conn->prepare(
    "SELECT
    first_name,
    last_name,
    MIN(w.start_time) AS start_career,
    (
        SELECT w2.role
        FROM workat w2
        JOIN employees e2 ON w2.MCN = e2.MCN
        JOIN schedul2 s2 ON e2.MCN = s2.MCN AND w2.Fname = s2.Fname
        WHERE w2.MCN = e.MCN AND w2.start_time = MIN(w.start_time)
        ORDER BY s2.dayly_time
        LIMIT 1
    ) AS role,
    SUM(TIMESTAMPDIFF(HOUR, CAST(SUBSTRING_INDEX(s.dayly_time, '-', 1) AS TIME), CAST(SUBSTRING_INDEX(s.dayly_time, '-', -1) AS TIME))) AS daily_hour,
    SUM(DATEDIFF(NOW(), w.start_time) * TIMESTAMPDIFF(HOUR, CAST(SUBSTRING_INDEX(s.dayly_time, '-', 1) AS TIME), CAST(SUBSTRING_INDEX(s.dayly_time, '-', -1) AS TIME))) AS working_hour_until_now
FROM employees e
JOIN workat w ON e.MCN = w.MCN
JOIN schedul2 s ON e.MCN = s.MCN AND w.Fname = s.Fname
JOIN infection_history a ON a.MCN = s.MCN
WHERE a.type = 'COVID-19' AND a.times >= 3 AND w.end_time IS NULL AND (w.role = 'Doctor' OR w.role = 'Nurse')
GROUP BY first_name, last_name, w.MCN
ORDER BY role ASC, first_name ASC, last_name ASC;"
);
$facility4->execute();
//15
$facility5 = $conn->prepare("SELECT
max(working_hour_until_now) as scheduled_working_hour
FROM (
SELECT
    e.mcn,
    first_name,
    last_name,
    MIN(w.start_time) AS start_career,
    role,
    MAX(DATEDIFF(NOW(), w.start_time) * TIMESTAMPDIFF(HOUR, CAST(SUBSTRING_INDEX(s.dayly_time, '-', 1) AS TIME), CAST(SUBSTRING_INDEX(s.dayly_time, '-', -1) AS TIME))) AS working_hour_until_now
FROM employees e
JOIN workat w ON e.MCN = w.MCN
JOIN schedul2 s ON e.MCN = s.MCN AND w.Fname = s.Fname
JOIN infection_history a ON a.MCN = s.MCN
WHERE a.type = 'COVID-19' AND a.times >= 3 AND w.end_time IS NULL AND w.role = 'Nurse'
GROUP BY first_name, last_name, w.MCN, role
ORDER BY role ASC, first_name ASC, last_name ASC
)As  subquery_alias
");
$facility5->execute();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="../img/logo.png" type="MIME">

    <!-- Bootstrap CSS -->
    <!-- build:css css/main.css-->
    <link rel="stylesheet" href="../node_modules/bootstrap/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../node_modules/bootstrap-social/bootstrap-social.css">
    <link rel="stylesheet" href="../css/styles.css">

    <!-- endbuild -->
    <title>Modify</title>
</head>

<body>
    <!--------------------navigation bar---------------------->
    <nav class="navbar navbar-dark navbar-expand-sm fixed-top">
        <div class="container">
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#Navbar">
                <span class="navbar-toggler-icon"></span>
            </button>
            <a class="navbar-brand mr-auto " href="../index.php"><img src="../img/logo.png" height="30" width="41"
                    class="img-fluid"></a>
            <div class="collapse navbar-collapse" id="Navbar">
                <ul class="navbar-nav mr-auto">
                    <li class="nav-item"><a class="nav-link fa-lg" href="../index.php"> <span
                                class="fa fa-hospital-o "></span> Home</a></li>
                    <li class="nav-item"><a class="nav-link fa-lg" href="./index.php"><span
                                class="fa fa-pencil  "></span>Modify</a></li>
                    <li class="nav-item"><a class="nav-link fa-lg" href="../information/information.php"><span
                                class="fa fa-info "></span>Infomation</a></li>
                    <li class="nav-item"><a class="nav-link fa-lg" href="../schedule.php"><span
                                class="fa fa-calendar-o "></span>Schedule</a></li>
                    <li class="nav-item"><a class="nav-link fa-lg" href="../email.php"><span
                                class="fa fa-envelope-o  "></span>Email</a></li>
                </ul>
            </div>
        </div>
    </nav>
    <!-------------------------------jumbotron---------------------------------->
    <header class="jumbotron">
        <div class="container">
            <div class="row row-header">
                <div class="col-12 col-sm-6 ">
                    <h1>Health Facility Employee Status Tracking System</h1>
                    <h5>The HFESTS system help health care facilities to
                        keep track of their employees’ health status during the COVID-19 pandemic.</h5>
                </div>
                <div class="col-12 col-sm align-items-center">
                    <img src="../img/logo.png" class="image-fluid">

                </div>
            </div>
        </div>
    </header>
    <!-------------------------------jumbotron end---------------------------------->
    <!-------------------------------Content---------------------------------->
    <div class="row row-content align-items-center">
        <div class="col-12">
            <h2>Please select the part you would like to operate on.</h2>
            <!--Accordion-->
            <div id="accordion">
                <!------------------------------------------------------------Employees---------------------------------->
                <!------------------------------------------------------------Employees---------------------------------->
                <div class="card">
                    <div class="card-header" role="tab" id="Employeehead">
                        <div class="d-flex">
                            <h3 class="mb-0">
                                <a data-toggle="collapse" data-target="#Employee">
                                    Question 6 <small></small>
                                </a>
                            </h3>
                        </div>
                    </div>
                    <div role="tabpanel" class="show" id="Employee" data-parent="#accordion">
                        <!----------------------table for employees--------------------------------------->
                        <div class="card-body">
                            <!--table for employees-->
                            <div class="col-12 col-sm-9">
                                <div class="table-responsive">
                                    <!--table can scroll horizontally when using small screen devices-->
                                    <table class="table table-striped">
                                        <!--striped: design a table with alternate rows in different colors-->
                                        <thead class="thead-dark">
                                            <!--render the head dark-->
                                            <th>Name</th>
                                            <th>Address</th>
                                            <th>City</th>
                                            <th>Province</th>
                                            <th>Postal code</th>
                                            <th>Phone number</th>
                                            <th>web address</th>
                                            <th>type</th>
                                            <th>capacity</th>
                                            <th>manager</th>
                                            <th>&nbsp;</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php while ($row = $facility1->fetch(PDO::FETCH_ASSOC, PDO::FETCH_ORI_NEXT)) { ?>
                                                <tr>
                                                    <td>
                                                        <?= $row["name"] ?>
                                                    </td>
                                                    <td>
                                                        <?= $row["address"] ?>
                                                    </td>
                                                    <td>
                                                        <?= $row["city"] ?>
                                                    </td>
                                                    <td>
                                                        <?= $row["province"] ?>
                                                    </td>
                                                    <td>
                                                        <?= $row["postal_code"] ?>
                                                    </td>
                                                    <td>
                                                        <?= $row["phone_number"] ?>
                                                    </td>
                                                    <td>
                                                        <?= $row["web_address"] ?>
                                                    </td>
                                                    <td>
                                                        <?= $row["type"] ?>
                                                    </td>
                                                    <td>
                                                        <?= $row["capacity"] ?>
                                                    </td>
                                                    <td>
                                                    </td>
                                                </tr>
                                            <?php } ?>

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                    </div>
                    <!------------------------------------------------------------information 7---------------------------------->
                    <div class="card">
                        <div class="card-header" role="tab" id="Employeehead">
                            <div class="d-flex">
                                <h3 class="mb-0">
                                    <a data-toggle="collapse" data-target="#Employee">
                                        Question 7 <small></small>
                                    </a>
                                </h3>
                            </div>
                        </div>
                        <div role="tabpanel" class="show" id="Employee" data-parent="#accordion">
                            <!----------------------table for employees--------------------------------------->
                            <div class="card-body">
                                <!--table for employees-->
                                <div class="col-12 col-sm-9">
                                    <div class="table-responsive">
                                        <!--table can scroll horizontally when using small screen devices-->
                                        <tr>
                                            <div class="col-12">
                                                <h2 class="mt-0">Insert employees MCN to get schedule table<span
                                                        class="badge badge-info">Info</span></h2>
                                            </div>
                                            <div class="col-12">
                                                <form method="get" class="form-inline" onsubmit="showTable1();">
                                                    <div class="form-group mr-2">
                                                        <label for="mcn-input"> facility name:</label>
                                                        <input type="text" id="mcn" name="mcn" class="form-control"
                                                            required>
                                                    </div>
                                                    <button type="submit" class="btn btn-info ">
                                                        <span style="font-weight:bold; color: black">Get
                                                            info</span>
                                                    </button>
                                                </form>
                                            </div>
                                        </tr>
                                        <?php if (isset($factoryy)) { ?>
                                            <table class="table table-striped">
                                                <!--striped: design a table with alternate rows in different colors-->
                                                <thead class="thead-dark">
                                                    <!--render the head dark-->
                                                    <th>Factory_name</th>
                                                    <th>First name</th>
                                                    <th>Last name</th>
                                                    <th>start time</th>
                                                    <th>date of birth</th>
                                                    <th>mcn</th>
                                                    <th>telephone number</th>
                                                    <th>role</th>
                                                    <th>address</th>
                                                    <th>city</th>
                                                    <th>Postal code</th>
                                                    <th>citizenship</th>
                                                    <th>Email address</th>
                                                    <th>&nbsp;</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($factoryy as $row) { ?>
                                                        <tr>
                                                            <td>
                                                                <?= $row["Fname"] ?>
                                                            </td>
                                                            <td>
                                                                <?= $row["first_name"] ?>
                                                            </td>
                                                            <td>
                                                                <?= $row["last_name"] ?>
                                                            </td>
                                                            <td>
                                                                <?= $row["start_time"] ?>
                                                            </td>
                                                            <td>
                                                                <?= $row["date_of_birth"] ?>
                                                            </td>
                                                            <td>
                                                                <?= $row["MCN"] ?>
                                                            </td>
                                                            <td>
                                                                <?= $row["telephone_number"] ?>
                                                            </td>
                                                            <td>
                                                                <?= $row["role"] ?>
                                                            </td>
                                                        </tr>
                                                    <?php } ?>
                                                </tbody>
                                            </table>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!------------------------------------------------------------information ---------------------------------->
                        <div class="card">
                            <div class="card-header" role="tab" id="Employeehead">
                                <div class="d-flex">
                                    <h3 class="mb-0">
                                        <a data-toggle="collapse" data-target="#Employee">
                                            Question 9 <small></small>
                                        </a>
                                    </h3>
                                </div>
                            </div>
                            <div role="tabpanel" class="show" id="Employee" data-parent="#accordion">
                                <!----------------------table for employees--------------------------------------->
                                <div class="card-body">
                                    <!--table for employees-->
                                    <div class="col-12 col-sm-9">
                                        <div class="table-responsive">
                                            <!--table can scroll horizontally when using small screen devices-->
                                            <table class="table table-striped">
                                                <!--striped: design a table with alternate rows in different colors-->
                                                <thead class="thead-dark">
                                                    <!--render the head dark-->
                                                    <th>province</th>
                                                    <th>name</th>
                                                    <th>capacity</th>
                                                    <th>total_infected</th>
                                                    <th></th>
                                                    <th>&nbsp;</th>
                                                    <th>&nbsp;</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php while ($row = $facility2->fetch(PDO::FETCH_ASSOC, PDO::FETCH_ORI_NEXT)) { ?>
                                                        <tr>
                                                            <td>
                                                                <?= $row["province"] ?>
                                                            </td>
                                                            <td>
                                                                <?= $row["name"] ?>
                                                            </td>
                                                            <td>
                                                                <?= $row["capacity"] ?>
                                                            </td>
                                                            <td>
                                                                <?= $row["total_infected"] ?>
                                                            </td>
                                                        </tr>
                                                    <?php } ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!------------------------------------------------------------information 2---------------------------------->
                        <div class="card">
                            <div class="card-header" role="tab" id="Employeehead">
                                <div class="d-flex">
                                    <h3 class="mb-0">
                                        <a data-toggle="collapse" data-target="#Employee">
                                            Question 14 <small></small>
                                        </a>
                                    </h3>
                                </div>
                            </div>
                            <div role="tabpanel" class="show" id="Employee" data-parent="#accordion">
                                <!----------------------table for employees--------------------------------------->
                                <div class="card-body">
                                    <!--table for employees-->
                                    <div class="col-12 col-sm-9">
                                        <div class="table-responsive">
                                            <!--table can scroll horizontally when using small screen devices-->
                                            <table class="table table-striped">
                                                <!--striped: design a table with alternate rows in different colors-->
                                                <thead class="thead-dark">
                                                    <!--render the head dark-->
                                                    <th>first_name</th>
                                                    <th>last_name</th>
                                                    <th>city</th>
                                                    <th>num_facilities</th>
                                                    <th></th>
                                                    <th>&nbsp;</th>
                                                    <th>&nbsp;</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php while ($row = $facility3->fetch(PDO::FETCH_ASSOC, PDO::FETCH_ORI_NEXT)) { ?>
                                                        <tr>
                                                            <td>
                                                                <?= $row["first_name"] ?>
                                                            </td>
                                                            <td>
                                                                <?= $row["last_name"] ?>
                                                            </td>
                                                            <td>
                                                                <?= $row["city"] ?>
                                                            </td>
                                                            <td>
                                                                <?= $row["num_facilities"] ?>
                                                            </td>
                                                        </tr>
                                                    <?php } ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!------------------------------------------------------------information 2---------------------------------->
                        <div class="card">
                            <div class="card-header" role="tab" id="Employeehead">
                                <div class="d-flex">
                                    <h3 class="mb-0">
                                        <a data-toggle="collapse" data-target="#Employee">
                                            Question 16 <small></small>
                                        </a>
                                    </h3>
                                </div>
                            </div>
                            <div role="tabpanel" class="show" id="Employee" data-parent="#accordion">
                                <!----------------------table for employees--------------------------------------->
                                <div class="card-body">
                                    <!--table for employees-->
                                    <div class="col-12 col-sm-9">
                                        <div class="table-responsive">
                                            <!--table can scroll horizontally when using small screen devices-->
                                            <table class="table table-striped">
                                                <!--striped: design a table with alternate rows in different colors-->
                                                <thead class="thead-dark">
                                                    <!--render the head dark-->
                                                    <th>first_name</th>
                                                    <th>last_name</th>
                                                    <th>start carear time</th>
                                                    <th>role</th>
                                                    <th>daily_working_hour</th>
                                                    <th>WORKING_HOUR_UNTIL_NOW</th>
                                                    <th></th>
                                                    <th>&nbsp;</th>
                                                    <th>&nbsp;</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php while ($row = $facility4->fetch(PDO::FETCH_ASSOC, PDO::FETCH_ORI_NEXT)) { ?>
                                                        <tr>
                                                            <td>
                                                                <?= $row["first_name"] ?>
                                                            </td>
                                                            <td>
                                                                <?= $row["last_name"] ?>
                                                            </td>
                                                            <td>
                                                                <?= $row["start_career"] ?>
                                                            </td>
                                                            <td>
                                                                <?= $row["role"] ?>
                                                            </td>
                                                            <td>
                                                                <?= $row["daily_hour"] ?>
                                                            </td>
                                                            <td>
                                                                <?= $row["working_hour_until_now"] ?>
                                                            </td>
                                                        </tr>
                                                    <?php } ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!------------------------------------------------------------question15---------------------------------->
                        <div class="card">
                            <div class="card-header" role="tab" id="Employeehead">
                                <div class="d-flex">
                                    <h3 class="mb-0">
                                        <a data-toggle="collapse" data-target="#Employee">
                                            Question 16 <small></small>
                                        </a>
                                    </h3>
                                </div>
                            </div>
                            <div role="tabpanel" class="show" id="Employee" data-parent="#accordion">
                                <!----------------------table for employees--------------------------------------->
                                <div class="card-body">
                                    <!--table for employees-->
                                    <div class="col-12 col-sm-9">
                                        <div class="table-responsive">
                                            <!--table can scroll horizontally when using small screen devices-->
                                            <table class="table table-striped">
                                                <!--striped: design a table with alternate rows in different colors-->
                                                <thead class="thead-dark">
                                                    <!--render the head dark-->
                                                    <th>first_name</th>
                                                    <th>last_name</th>
                                                    <th>start carear time</th>
                                                    <th>role</th>
                                                    <th>daily_working_hour</th>
                                                    <th>WORKING_HOUR_UNTIL_NOW</th>
                                                    <th></th>
                                                    <th>&nbsp;</th>
                                                    <th>&nbsp;</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php while ($row = $facility5->fetch(PDO::FETCH_ASSOC, PDO::FETCH_ORI_NEXT)) { ?>
                                                        <tr>
                                                            <td>
                                                                <?= $row["first_name"] ?>
                                                            </td>
                                                            <td>
                                                                <?= $row["last_name"] ?>
                                                            </td>
                                                            <td>
                                                                <?= $row["start_career"] ?>
                                                            </td>
                                                            <td>
                                                                <?= $row["role"] ?>
                                                            </td>
                                                            <td>
                                                                <?= $row["daily_hour"] ?>
                                                            </td>
                                                            <td>
                                                                <?= $row["working_hour_until_now"] ?>
                                                            </td>
                                                        </tr>
                                                    <?php } ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- jQuery first, then Popper.js, then Bootstrap JS. -->

                        <script src="../node_modules/jquery/dist/jquery.slim.min.js"></script>
                        <script src="../node_modules/popper.js/dist/umd/popper.min.js"></script>
                        <script src="../node_modules/bootstrap/dist/js/bootstrap.min.js"></script>
                        <script src="https://kit.fontawesome.com/da57742d83.js" crossorigin="anonymous"></script>
</body>

</html>