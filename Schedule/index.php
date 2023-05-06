<?php require_once './../database.php';
//Schedule History
$schedule = $conn->prepare('SELECT*FROM schedule');
$schedule->execute();
//------------------------------------------
//Information searched by MCN
if (isset($_GET['mcn'])) {
    // Retrieve schedule for the specified MCN
    $statement = $conn->prepare("SELECT * FROM schedule WHERE MCN = :MCN ");
    $statement->bindParam(":MCN", $_GET["mcn"]);
    $statement->execute();
    $employee = $statement->fetchAll(PDO::FETCH_ASSOC);

}
//-------------------------------------------
//Information searched by a specific period of time.
//Retrieve the facility from the form submission
//Total hours
?>
<!DOCTYPE html>
<div lang="en">

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
        <title>Schedule</title>
    </head>
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
                    <li class="nav-item"><a class="nav-link fa-lg" href="../Modify/index.php"><span
                                class="fa fa-pencil  "></span>Modify</a></li>
                    <li class="nav-item"><a class="nav-link fa-lg" href="#"><span
                                class="fa fa-info "></span>Infomation</a></li>
                    <li class="nav-item"><a class="nav-link fa-lg" href="./index.php"><span
                                class="fa fa-calendar-o "></span>Schedule</a></li>
                    <li class="nav-item"><a class="nav-link fa-lg" href="#"><span
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
                    <h1>Schedule</h1>
                    <h5>With Schedule, you can easily schedule a time, view your schedule history, search for schedule
                        info by name, find Quebec doctor info, identify the nurse with the longest working hours, and
                        search for a list of all the doctors who have been on schedule to work in the last two weeks by
                        facility. Additionally, you can easily calculate the total hours scheduled for every role in a
                        specific period, allowing you to manage your resources effectively. With Schedule, you'll never
                        miss an appointment again!</h5>
                </div>
                <div class="col-12 col-sm align-items-center">
                    <img src="../img/logo.png" class="image-fluid">

                </div>
            </div>
        </div>
    </header>
    <!-------------------------------jumbotron end---------------------------------->
    <!-------------------------------Content---------------------------------->
    <!-------------------------------Assign/Delete/Edit schedule for an Employee---------------------------------->
    <div class="container">
        <div class="row row-content align-items-center">
            <div class="col-12">
                <h2 class="mt-0">Insert employees MCN to get schedule table<span class="badge badge-info">Info</span>
                </h2>
            </div>
            <div class="col-12">
                <form method="get" class="form-inline">
                    <div class="form-group mr-2">
                        <label for="mcn-input"> MCN:</label>
                        <input type="number" id="mcn" name="mcn" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-info ">
                        <span style="font-weight:bold; color: black">Get info</span>
                    </button>
                </form>
            </div>
            <!-- Display the schedule table -->
            <?php if (isset($employee)) { ?>
                <!--table for schedule-->
                <div class="col-12 col-sm" id="MCNTable">
                    <div class="table-responsive">
                        <!--table can scroll horizontally when using small screen devices-->
                        <table class="table table-striped">
                            <!--striped: design a table with alternate rows in different colors-->
                            <thead class="thead-dark">
                                <!--render the head dark-->
                                <tr>
                                    <th>Reference Number</th>
                                    <th>MCN</th>
                                    <th>Facility Name</th>
                                    <th>Date</th>
                                    <th>Start Time</th>
                                    <th>End Time</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($employee as $row) { ?>
                                    <tr>
                                        <td>
                                            <?php echo $row['reference_number']; ?>
                                        </td>
                                        <td>
                                            <?php echo $row['MCN']; ?>
                                        </td>
                                        <td>
                                            <?php echo $row['name']; ?>
                                        </td>
                                        <td>
                                            <?php echo $row['date']; ?>
                                        </td>
                                        <td>
                                            <?php echo $row['startTime']; ?>
                                        </td>
                                        <td>
                                            <?php echo $row['startTime']; ?>
                                        </td>
                                        <td>
                                            <?php echo $row['endTime']; ?>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                s
            <?php } ?>
            <!-- Add JavaScript to show the table when the button is clicked -->
            <script>
                function showTable1() {
                    document.getElementById("MCNTable").classList.remove("d-none");
                }
            </script>
        </div>
    </div>
    <!-----------------------------------Content end---------------------------------------------------------------->

    <footer class="footer ">
        <div class="container">
            <div class="row">
                <div class="col-4 offset-1 col-sm-2">
                    <h5>Links</h5>
                    <ul class="list-unstyled">
                        <li><a href="../index.php">Home</a></li>
                        <li><a href="Modify/index.php">Modify</a></li>
                        <li><a href="information/index.php">Infomation</a></li>
                        <li><a href="./index.php">Schedule</a></li>
                        <li><a href="email/index.php">Email</a></li>
                    </ul>
                </div>
                <div class="col-7 col-sm-5">
                    <h5>Our Address</h5>
                    <address>
                        1455 Boul. de Maisonneuve Ouest<br>
                        QC H3G 1M8<br>
                        Montréal<br>
                        <i class="fa fa-phone fa-lg"></i>: +1 438 888 8888<br>
                        <i class="fa fa-fax fa-lg"></i>: +1 512 111 2222<br>
                        <i class="fa fa-envelope fa-lg"></i>:
                        <a href="mailto:HFESTS@gmail.com">HFESTS@gmail.com</a>
                    </address>
                </div>
                <div class="col-12 col-sm-4 align-self-center">
                    <div class="text-center">
                        <a class="btn btn-social-icon btn-google" href="http://google.com/+"><i
                                class="fa fa-google-plus"></i></a>
                        <a class="btn btn-social-icon btn-facebook" href="http://www.facebook.com/profile.php?id="><i
                                class="fa fa-facebook"></i></a>
                        <a class="btn btn-social-icon btn-linkedin" href="http://www.linkedin.com/in/"><i
                                class="fa fa-linkedin"></i></a>
                        <a class="btn btn-social-icon btn-twitter" href="http://twitter.com/"><i
                                class="fa fa-twitter"></i></a>
                        <a class="btn btn-social-icon btn-google" href="http://youtube.com/"><i
                                class="fa fa-youtube"></i></a>
                        <a class="btn btn-social-icon" href="mailto:"><i class="fa fa-envelope-o"></i></a>
                    </div>
                </div>
            </div>
            <div class="row justify-content-center">
                <div class="col-auto">
                    <p>© Copyright 2023 Health Facility Employee Status Tracking System</p>
                </div>
            </div>
        </div>
    </footer>
    <!-- jQuery first, then Popper.js, then Bootstrap JS. -->

    <script src="../node_modules/jquery/dist/jquery.slim.min.js"></script>
    <script src="../node_modules/popper.js/dist/umd/popper.min.js"></script>
    <script src="../node_modules/bootstrap/dist/js/bootstrap.min.js"></script>
    <script src="https://kit.fontawesome.com/da57742d83.js" crossorigin="anonymous"></script>

    </body>

    </html>