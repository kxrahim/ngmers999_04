<?php
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
    ini_set('session.save_path', '/var/lib/php/sessions');
?>
<?php
    include("includes/check.php");
?>
<!doctype html>
<!-- 
* Bootstrap Simple Admin Template
* Version: 2.1
* Author: Alexis Luna
* Website: https://github.com/alexis-luna/bootstrap-simple-admin-template
-->
<html lang="en">

<head>
    <?php 
        require_once('includes/main-header.php'); 
        require_once('includes/connection.php');
        require_once('includes/functions.php');

        if (isset($_GET['n'])){
            $n = $_GET['n'];
        } else {
            $n = 0;
        }

        if (isset($_POST['submit'])){
            /*
            array ( [name] => Samuel Wong
                    [mrn] => 12045
                    [gender] => 1
                    [birthdate] => 1999-11-25
                    [age] => 22
                    [idnumber] => 991125-10-1234
                    [phone] => 016-6254544
                    [submit] => Save Data )
            */

            // get data
            $name       = $_POST['name'];
            $mrn        = $_POST['mrn'];
            $gender     = $_POST['gender'];
            $birthdate  = $_POST['birthdate'];
            $age        = $_POST['age'];
            $idnumber   = $_POST['idnumber'];
            $phone      = $_POST['phone'];

            $sql1 = "INSERT INTO patients (name,
                                           mrn,
                                           gender,
                                           birthdate,
                                           age,
                                           idnumber,
                                           phone)
                           VALUES ('$name',
                                   '$mrn',
                                   '$gender',
                                   '$birthdate',
                                   '$age',
                                   '$idnumber',
                                   '$phone')";
        if ($conn->query($sql1) === TRUE){
            echo "<script type='text/javascript'>
                    alert('Data added successfully!');
                    window.location='patient.php';
                </script>";
            exit();
        } else {

        }
            //print_r($_POST);
            exit;
        }

        // delete patient
        if ($n == 3){

            $id = $_GET['id'];

            $sql = "DELETE FROM patients WHERE id = '$id'";

            if ($conn->query($sql) === TRUE){
                echo "<script type='text/javascript'>                    
                    window.location='patient.php';
                </script>";
                exit();
            } else {

            }
            //print_r($_GET);
            exit;
        }
    ?>
</head>

<body>
    <div class="wrapper">
        <!-- sidebar --> 
        <?php
            require_once('includes/admin-sidebar.php');
        ?>
        <div id="body" class="active">
            <!-- navbar navigation component -->
            <?php
                require_once('includes/navbar.php');
            ?>
            <!-- end of navbar navigation -->
            <div class="content">
                <div class="container">
                    <div class="row">
                        <div class="col-md-12 page-header">
                            <div class="page-pretitle">Setting</div>
                            <h2 class="page-title">Patient</h2>
                        </div>
                    </div>
                    <div class="row">                        
                        <div class="col-md-12 col-lg-12 mt-3">
                            <div class="card">
                                <div class="card-header">List of available patients</div>
                                <div class="card-body">
                                    <p class="card-title"></p>
                                    <table class="table table-hover" id="dataTables-example" width="100%">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>MRN</th>
                                                <th>FULL NAME</th>
                                                <th>GENDER</th>
                                                <th>BIRTHDATE</th>
                                                <th>AGE</th>
                                                <th>ID NUMBER</th>
                                                <th>PHONE</th>
                                                <th>STATUS</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                                $sq1 = "select *
                                                        from patients
                                                        where status = '1'";
                                                $rs1 = $conn->query($sq1);

                                                $num = 0;
                                                while ($rw1 = $rs1->fetch_assoc()){
                                                    if ($rw1['status'] == '1'){
                                                        $status = 'Active';
                                                    } else {
                                                        $status = 'Inactive';
                                                    }
                                            ?>
                                            <tr>
                                                <td><?=++$num;?></td>
                                                <td><?=$rw1['mrn'];?></td>
                                                <td><?=$rw1['name'];?></td>
                                                <td><?=getGender($rw1['gender']);?></td>
                                                <td><?=$rw1['birthdate'];?></td>
                                                <td><?=$rw1['age'];?></td>
                                                <td><?=$rw1['idnumber'];?></td>
                                                <td><?=$rw1['phone'];?></td>
                                                <td><?=$status;?></td>
                                                <td>
                                                    <a href="?n=3&id=<?=$rw1['id'];?>" title="Delete"><i class="fa fa-trash" onClick="javascript:return confirm('Are you sure to delete this?');"></i></a>
                                                </td>
                                            </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php if ($n == 0) { ?>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card">
                                <div class="card-header">Insert new patient</div>
                                <div class="card-body">
                                    <form accept-charset="utf-8" method="post" action="">
                                        <div class="mb-3">
                                            <label for="password" class="form-label">Full Name</label>
                                            <input type="text" name="name" placeholder="Full name" class="form-control" >
                                        </div>

                                        <div class="row">
                                            <div class="mb-3 col-md-4">
                                                <label for="email" class="form-label">MRN</label>
                                                <input type="text" name="mrn" placeholder="MRN" class="form-control" required>
                                            </div>
                                            <div class="mb-3 col-md-4">
                                                <label for="email" class="form-label">Gender</label>
                                                <select name="gender" id="" class="form-control" required>
                                                    <option value=""></option>
                                                    <option value="1">Male</option>
                                                    <option value="2">Female</option>
                                                </select>
                                            </div>

                                            <div class="mb-3 col-md-4">
                                                <label for="birthdate" class="form-label">Birthdate</label>
                                                <input type="date" name="birthdate" placeholder="Birthdate" class="form-control" required>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="mb-3 col-md-4">
                                                <label for="email" class="form-label">Age</label>
                                                <input type="number" name="age" placeholder="Age" class="form-control" required>
                                            </div>

                                            <div class="mb-3 col-md-4">
                                                <label for="email" class="form-label">ID Number</label>
                                                <input type="text" name="idnumber" placeholder="e.g: 931022-10-1023" class="form-control" required>
                                            </div>

                                            <div class="mb-3 col-md-4">
                                                <label for="email" class="form-label">Phone Number</label>
                                                <input type="text" name="phone" placeholder="Phone number" class="form-control" required>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <input type="submit" name="submit" value="Save Data" class="btn btn-primary">
                                            <input type="reset" class="btn btn-secondary" value="Reset">
                                        </div>

                                    </form>
                                </div>
                            </div>
                        </div>                       
                    </div>

                    <!-- Bulk upload patient -->
                    <div class="row">
                            <div class="col-lg-12">
                                <div class="card">
                                    <div class="card-header">Patient bulk upload</div>
                                    <div class="card-body">
                                        <form accept-charset="utf-8" method="post" action="patient-bulk-upload.php" enctype="multipart/form-data">
                                            <div class="mb-3">
                                                <label for="password" class="form-label">Upload user file. <small>File must be in <b>*.csv</b> format.</small>.</label>
                                                <input type="file" name="csv_data" placeholder="Full name" class="form-control" >
                                            </div>

                                            <div class="mb-3">
                                                <input type="submit" name="btnUpload" value="Save Data" class="btn btn-primary">
                                                <input type="reset" class="btn btn-secondary" value="Reset">
                                            </div>
                                        </form>
                                        <small><a href="../uploads/patient-template01.csv">Click here</a> to download patient bulk upload template.</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
    <script src="assets/vendor/jquery/jquery.min.js"></script>
    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/vendor/datatables/datatables.min.js"></script>
    <script src="assets/js/initiate-datatables.js"></script>
    <script src="assets/js/script.js"></script>
</body>

</html>