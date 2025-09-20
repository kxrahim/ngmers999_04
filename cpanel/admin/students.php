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
        //require_once('includes/connection_old.php');
        require_once('includes/connection.php');
        require_once('includes/functions.php');

        if (isset($_GET['n'])){
            $n = $_GET['n'];
        } else {
            $n = 0;
        }
    ?>

    <?php
        // add student
        if (isset($_POST['addStudent'])){
            //print_r($_POST);
            //exit;

            // Array ( [assessor_id] => 6 [cohort_id] => 3 [fullname] => malik rahman [email] => 0000000456@student.imu.edu.my [addStudent] => Save Data )
            $salutation_id  = $_POST['salutation_id'];
            $cohort_id      = $_POST['cohort_id'];
            $fullname       = mysqli_real_escape_string($conn, $_POST['fullname']);
            $email          = $_POST['email'];
            $studentId      = $_POST['student_id'];
            $pass           = "dentapp@imu";
            $password       = md5($pass);

            // check if user available
            $sn2 = "select *
                    from users
                    where studentId = '$studentId' or email = '$email'";
            $rsn2 = $conn->query($sn2);

            if ($rsn2->num_rows > 1){
                echo "<script type='text/javascript'>
                          alert('Sorry! User already existed.');
                          window.location='user.php';
                      </script>";
                exit();
            } else {
                $sn1 = "insert into users (salutation_id,
                                       studentId,
                                       fullname,
                                       email,
                                       username,
                                       password,
                                       role_id,
                                       cohort_id)
                           values ('$salutation_id',
                                   '$studentId',
                                   '$fullname',
                                   '$email',
                                   '$email',
                                   '$password',
                                   '3',
                                   '$cohort_id')";
                if ($conn->query($sn1) === TRUE){
                    echo "<script type='text/javascript'>
                              alert('User created successfully.');
                              window.location='user.php';
                          </script>";
                    exit();
                }
            }
        }
        // end add student

        // add assessor
        if (isset($_POST['addAssessor'])){
//            print_r($_POST);
//            exit;

            $salutation_id  = $_POST['salutation_id'];
            $cohort_id      = 0;
            $fullname       = mysqli_real_escape_string($conn, $_POST['fullname']);
            $email          = $_POST['email'];
            $studentId      = '0';
            $pass           = "dentapp@imu";
            $password       = md5($pass);

            // check if user available
            $sn2 = "select *
                    from users
                    where email = '$email'";
            $rsn2 = $conn->query($sn2);

            if ($rsn2->num_rows > 1){
                echo "<script type='text/javascript'>
                          alert('Sorry! User already existed.');
                          window.location='user.php';
                      </script>";
                exit();
            } else {
                $sn1 = "insert into users (salutation_id,
                                           studentId,
                                           fullname,
                                           email,
                                           username,
                                           password,
                                           role_id,
                                           cohort_id)
                               values ('$salutation_id',
                                       '$studentId',
                                       '$fullname',
                                       '$email',
                                       '$email',
                                       '$password',
                                       '2',
                                       '$cohort_id')";
                if ($conn->query($sn1) === TRUE){
                    echo "<script type='text/javascript'>
                              alert('User created successfully.');
                              window.location='user.php';
                          </script>";
                    exit();
                }
            }
        }

        // upload bulk students
        if (isset($_POST['addBulkStudent'])){

            $count = 0;
            $file = $_FILES["file"]["tmp_name"];
            $file_open = fopen($file,"r");
            while(($csv = fgetcsv($file_open, 1000, ",")) !== false)
            {
                $count++;

                if ($count == 1) { continue; }

                $salutation_id  = $csv[2];
                $cohort_id      = $csv[1];
                $fullname       = $csv[3];
                $email          = $csv[4];
                $studentId      = $csv[0];
                $pass           = "dentapp@imu";
                $password       = md5($pass);

                //echo $salutation_id . ' | ' . $cohort_id . ' | ' . $fullname . ' | ' . $email . ' | ' . $studentId . ' | ' . $password;
               // echo '<hr>';

                $sq1 = "select *
                        from users
                        where email = '$email'";
                $rs1 = $conn->query($sq1);

                if ($rs1->num_rows > 0){

                } else {
                    $sn2 = "insert into users (salutation_id,
                                       studentId,
                                       fullname,
                                       email,
                                       username,
                                       password,
                                       role_id,
                                       cohort_id)
                           values ('$salutation_id',
                                   '$studentId',
                                   '$fullname',
                                   '$email',
                                   '$email',
                                   '$password',
                                   '3',
                                   '$cohort_id')";
                    $rs2 = $conn->query($sn2);

                    //echo $sn2;
                    //echo '<br>';
                }

            } // tutup masukkan data

            //exit;

            echo 'Jumlah Data Dimasukkan: ' . $count;

            echo "<script type='text/javascript'>
                              alert('Students upload successfully.');
                              window.location='user.php';
                          </script>";
            exit();
        }

        // upload bulk assessor/lecturer
        if (isset($_POST['addBulkAssessor'])){

            $count = 0;
            $file = $_FILES["file"]["tmp_name"];
            $file_open = fopen($file,"r");

            $data = 0;
            while(($csv = fgetcsv($file_open, 1000, ",")) !== false)
            {
                $count++;

                if ($count == 1) { continue; }

                $salutation_id  = $csv[2];
                $cohort_id      = $csv[1];
                $fullname       = $csv[3];
                $email          = $csv[4];
                $studentId      = $csv[0];
                $pass           = "dentapp@imu";
                $password       = md5($pass);

//                echo $salutation_id . ' | ' . $cohort_id . ' | ' . $fullname . ' | ' . $email . ' | ' . $studentId . ' | ' . $password;
//                echo '<hr>';

                $sq1 = "select *
                        from users
                        where email = '$email'";
                $rs1 = $conn->query($sq1);

                if ($rs1->num_rows > 0){

                } else {
//                    echo $salutation_id . ' | ' . $cohort_id . ' | ' . $fullname . ' | ' . $email . ' | ' . $studentId . ' | ' . $password;
//                    echo '<hr>';

                    ++$data;

                    $sn2 = "insert into users (salutation_id,
                                           studentId,
                                           fullname,
                                           email,
                                           username,
                                           password,
                                           role_id,
                                           cohort_id)
                               values ('$salutation_id',
                                       '$studentId',
                                       '$fullname',
                                       '$email',
                                       '$email',
                                       '$password',
                                       '2',
                                       '$cohort_id')";
                    $rs2 = $conn->query($sn2);
                }

            } // tutup masukkan data

//            echo '<br><b>Jumlah Data: ' . $data . '</b>';
//            exit;

            echo "<script type='text/javascript'>
                                  alert('Assessor/Lecturers upload successfully.');
                                  window.location='user.php';
                              </script>";
            exit();
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
                            <h2 class="page-title">Student</h2>
                        </div>
                    </div>

                    <?php
                        if ($n == 0) {
                    ?>
                    <div class="row">
                        <div class="col-md-12 col-lg-12 mt-3">
                            <div class="card">
                                <div class="card-header">Filter students</div>
                                <div class="card-body">
                                    <form accept-charset="utf-8" method="post" action="">
                                            <div class="row">
                                                <div class="mb-6 col-md-4">
                                                    <label for="" class="form-label">Cohort</label>
                                                    <select name="cohort_filter" id="" class="form-control">
                                                        <option value="0"></option>
                                                        <?php
                                                            $sq5 = "select * from cohorts order by id asc";
                                                            $rs5 = $conn->query($sq5);
                                                            while ($rw5 = $rs5->fetch_assoc()){
                                                        ?>
                                                        <option value="<?=$rw5['id']?>"><?=$rw5['cohort']?></option>
                                                        <?php
                                                            }
                                                        ?>
                                                    </select>
                                                    <button class="btn btn-info mt-2" type="submit" name="btnFilter2"> Filter </button>
                                                </div>
                                            </div>
                                    </form>

                                    <?php
                                        if (isset($_POST['btnFilter2'])){
                                            $condition = '';
                                            $nCohort = '';
                                            if ($_POST['cohort_filter'] > 0){
                                                $nCohort = $_POST['cohort_filter'];

                                                $condition = " AND cohort_id = " . $nCohort;
                                            }
                                        }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12 col-lg-12 mt-3">
                            <div class="card">
                                <div class="card-header">List of available users</div>
                                <div class="card-body">
                                    <p class="card-title"></p>
                                    <table class="table table-hover" id="dataTables-example" width="100%">
                                        <thead>
                                            <tr>
                                                <th>FULL NAME</th>
                                                <th>E-MAIL</th>
                                                <th>COHORT/INTAKE</th>
                                                <th>STATUS</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                                $sq1 = "select *, a.id as uid, a.status as ustatus
                                                        from users as a
                                                        join cohorts as b on a.cohort_id = b.id
                                                        where a.status = '1' and a.role_id = 3" . $condition . "
                                                        order by a.fullname asc";
                                                $rs1 = $conn->query($sq1);

                                                $num = 0;
                                                while ($rw1 = $rs1->fetch_assoc()){
                                                    if ($rw1['ustatus'] == '1'){
                                                        $status = 'Active';
                                                    } else {
                                                        $status = 'Inactive';
                                                    }
                                            ?>
                                            <tr>
                                                <td><?=getSalutation($conn, $rw1['salutation_id']);?> <?=strtoupper($rw1['fullname']);?></td>
                                                <td><?=$rw1['email'];?></td>
                                                <td><?=$rw1['cohort'];?></td>
                                                <td><?=$status;?></td>
                                                <td>
                                                    <a href="?n=3&id=<?=$rw1['id'];?>" title="Delete"><i class="fa fa-trash" onClick="javascript:return confirm('Are you sure to delete this?');"></i></a>
                                                </td>
                                            </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>

                                    <!-- Button trigger modal -->
                                    <a href="?n=1" class="btn btn-primary"> Add new student </a>
                                    <a href="?n=2" class="btn btn-primary"> Add new assessor/lecturer </a>
                                    <a href="?n=3" class="btn btn-info"> Add bulk students </a>
                                    <a href="?n=4" class="btn btn-info"> Add bulk assessor/lecture </a>

                                </div>



                            </div>
                        </div>
                    </div>
                    <?php } ?>

                    <?php if ($n == 1) { ?>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card">
                                <div class="card-header">Insert new user</div>
                                <div class="card-body">
                                    <form accept-charset="utf-8" method="post" action="">
                                        <div class="row">
                                            <div class="mb-6 col-md-4">
                                                <label for="" class="form-label">Student ID</label>
                                                <input type="text" name="student_id" placeholder="00000012345" class="form-control" required>

                                            </div>

                                            <div class="mb-6 col-md-4">
                                                <label for="" class="form-label">Cohort</label>
                                                <select name="cohort_id" id="cohort_id" class="form-control">
                                                    <option value=""></option>
                                                    <?php
                                                    $sql02 = "select *
                                                          from cohorts
                                                          order by cohort asc";
                                                    $rst02 = $conn->query($sql02);
                                                    while ($row02 = $rst02->fetch_assoc()){
                                                        ?>
                                                        <option value="<?=$row02['id'];?>"><?=$row02['cohort'];?></option>
                                                        <?php
                                                    }
                                                    ?>
                                                </select>
                                            </div>

                                            <div class="mb-6 col-md-4">
                                                <label for="" class="form-label">Salutation</label>
                                                <select name="salutation_id" id="salutation_id" class="form-control">
                                                    <option value=""></option>
                                                    <?php
                                                    $sql01 = "select *
                                                          from salutations
                                                          order by salutation asc";
                                                    $rst01 = $conn->query($sql01);
                                                    while ($row01 = $rst01->fetch_assoc()){
                                                        ?>
                                                        <option value="<?=$row01['id'];?>"><?=$row01['salutation'];?></option>
                                                        <?php
                                                    }
                                                    ?>
                                                </select>
                                            </div>


                                        </div>


                                        <div class="mb-3">
                                            <label for="email" class="form-label">Full name</label>
                                            <input type="text" name="fullname" placeholder="Fullname" class="form-control" required  oninput="this.value = this.value.toUpperCase()">
                                        </div>
                                        <div class="mb-3">
                                            <label for="password" class="form-label">Email</label>
                                            <input type="email" name="email" placeholder="sample@noemail.com" class="form-control" >
                                        </div>
                                        <div class="mb-3">
                                            <input type="submit" name="addStudent" value="Save Data" class="btn btn-primary">
                                            <a href="?n=0" class="btn btn-secondary"> Cancel </a>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php } else if ($n == 2) { ?>
                    <div class="row">
                            <div class="col-lg-12">
                                <div class="card">
                                    <div class="card-header">Insert new assessor/lecturer</div>
                                    <div class="card-body">
                                        <form accept-charset="utf-8" method="post" action="">
                                            <div class="row">
                                                <div class="mb-6 col-md-2">
                                                    <label for="" class="form-label">Salutation</label>
                                                    <select name="salutation_id" id="salutation_id" class="form-control">
                                                        <option value=""></option>
                                                        <?php
                                                        $sql01 = "select *
                                                          from salutations
                                                          order by salutation asc";
                                                        $rst01 = $conn->query($sql01);
                                                        while ($row01 = $rst01->fetch_assoc()){
                                                            ?>
                                                            <option value="<?=$row01['id'];?>"><?=$row01['salutation'];?></option>
                                                            <?php
                                                        }
                                                        ?>
                                                    </select>
                                                </div>

                                                <div class="mb-6 col-md-10">
                                                    <label for="" class="form-label">Full name</label>
                                                    <input type="text" name="fullname" placeholder="Fullname" class="form-control" required>
                                                </div>

                                            </div>

                                            <div class="mb-3 mt-2">
                                                <label for="password" class="form-label">Email</label>
                                                <input type="email" name="email" placeholder="sample@noemail.com" class="form-control" >
                                            </div>
                                            <div class="mb-3">
                                                <input type="submit" name="addAssessor" value="Save Data" class="btn btn-primary">
                                                <a href="?n=0" class="btn btn-secondary"> Cancel </a>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php } else if ($n == 3) { ?>
                    <div class="row">
                        <div class="col-md-12 col-lg-12 mt-3">
                            <div class="card">
                                <div class="card-header">Add bulk students</div>
                                <div class="card-body">
                                    <form accept-charset="utf-8" method="post" action="" enctype="multipart/form-data">
                                        <div class="mb-3">
                                            <label for="email" class="form-label">Student File</label>
                                            <input type="file" name="file" placeholder="file" class="form-control" required>
                                            <small class="text-danger">* File must be in .csv format.</small>
                                        </div>

                                        <div class="mb-3">
                                            <input type="submit" name="addBulkStudent" value="Upload Data" class="btn btn-primary">
                                            <a href="?n=0" class="btn btn-secondary"> Cancel </a>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php } else if ($n == 4) { ?>
                    <div class="row">
                            <div class="col-md-12 col-lg-12 mt-3">
                                <div class="card">
                                    <div class="card-header">Add bulk assessors/lecturers</div>
                                    <div class="card-body">
                                        <form accept-charset="utf-8" method="post" action="" enctype="multipart/form-data">
                                            <div class="mb-3">
                                                <label for="email" class="form-label">Assessor/Lecturer File</label>
                                                <input type="file" name="file" placeholder="file" class="form-control" required>
                                                <small class="text-danger">* File must be in .csv format.</small>
                                            </div>

                                            <div class="mb-3">
                                                <input type="submit" name="addBulkAssessor" value="Upload Data" class="btn btn-primary">
                                                <a href="?n=0" class="btn btn-secondary"> Cancel </a>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php } else {} ?>
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
