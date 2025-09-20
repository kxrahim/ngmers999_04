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

        if (isset($_GET['n'])){
            $n = $_GET['n'];
        } else {
            $n = 0;
        }

        if (isset($_POST['submit'])){
            //print_r($_POST);
            //exit;

            $role           = $_POST['role'];
            $description    = $_POST['description'];

            $sq1 = "select *
                    from roles
                    where name = '$role'";
            $re1 = $conn->query($sq1);

            if ($re1->num_rows > 0){
                echo "<script type='text/javascript'>
                                  alert('Sorry! Data already available.');
                                  window.location='user-role.php';
                              </script>";
                exit();
            } else {
                $sq2 = "insert into roles (name,
                                            description) 
                              values ('$role',
                                      '$description')";
                //echo $sq2;
                //exit;
                if ($conn->query($sq2) === TRUE) {
                    echo "<script type='text/javascript'>
                          alert('Data save successfully.');
                          window.location='user-role.php';
                      </script>";
                    exit();
                }
            }
        }

    // delete
    if ($n == 3){
        //print_r($_GET);
        //exit;
        $id = $_GET['id'];
        $s1 = "delete from roles where id = '$id'";

        if ($conn->query($s1) === TRUE){
            echo "<script type='text/javascript'>
                            window.location='user-role.php';
                      </script>";
            exit();
        }
        //exit;
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
                            <h2 class="page-title">User Role</h2>
                        </div>
                    </div>
                    <div class="row">                        
                        <div class="col-md-12 col-lg-12 mt-3">
                            <div class="card">
                                <div class="card-header">List of available roles</div>
                                <div class="card-body">
                                    <p class="card-title"></p>
                                    <table class="table table-hover" id="dataTables-example" width="100%">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>ROLE</th>
                                                <th>DESCRIPTION</th>
                                                <th>STATUS</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                                $sq1 = "select *
                                                        from roles
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
                                                <td><?=ucwords($rw1['name']);?></td>
                                                <td><?=$rw1['description'];?></td>
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
                        <form action="" method="post">
                            <div class="col-lg-12">
                                <div class="card">
                                    <div class="card-header">Insert new role</div>
                                    <div class="card-body">
                                        <form accept-charset="utf-8">
                                            <div class="mb-3">
                                                <label for="email" class="form-label">Role</label>
                                                <input type="text" name="role" placeholder="Role" class="form-control" required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="password" class="form-label">Description</label>
                                                <input type="text" name="description" placeholder="Description if needed" class="form-control" >
                                            </div>
                                            <div class="mb-3">
                                                <input type="submit" name="submit" value="Save Data" class="btn btn-primary">
                                                <input type="reset" class="btn btn-secondary" value="Reset">
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </form>
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