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

        // save new data
        if (isset($_POST['submit'])){
            //print_r($_POST);
            //exit;
            $sort   = $_POST['sort'];
            $level  = mysqli_real_escape_string($conn, $_POST['level']);
      
            $sq1 = "select *
                    from levels
                    where level = '$level'";
            $re1 = $conn->query($sq1);
      
            if ($re1->num_rows > 0){
              echo "<script type='text/javascript'>
                          alert('Sorry! Data already available.');
                          window.location='manage-activity.php';
                      </script>";
              exit();
            } else {
              // simpan baru
              $sq2 = "insert into levels (sort,
                                          level) 
                              values ('$sort',
                                      '$level')";
              //echo $sq2;
              if ($conn->query($sq2) === TRUE) {
                echo "<script type='text/javascript'>
                          alert('Data save successfully.');
                          window.location='manage-level.php';
                      </script>";
                exit();
              }
            }
        } // end save new data

        // update data
        if (isset($_POST['update'])){
            
            $id         = $_POST['id'];
            $activity   = mysqli_real_escape_string($conn, $_POST['activity']);

            $sql = "UPDATE levels
                    SET level = '$activity'
                    WHERE id = '$id'";
            if ($conn->query($sql) === TRUE){
                echo "<script type='text/javascript'>
                          alert('Data update successfully.');
                          window.location='manage-level.php';
                      </script>";
                exit();
            } else {}
            
            //print_r($_POST);
            //exit;
        }

        // delete
        if ($n == 3){
            //print_r($_GET);
            //exit;
            $id = $_GET['id'];
            $s1 = "delete from levels where id = '$id'";
      
            if ($conn->query($s1) === TRUE){
              echo "<script type='text/javascript'>
                            window.location='manage-level.php';
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
                            <div class="page-pretitle">Manage</div>
                            <h2 class="page-title">Level</h2>
                        </div>
                    </div>
                    <div class="row">                        
                        <div class="col-md-12 col-lg-12 mt-3">
                            <div class="card">
                                <div class="card-header">List of available levels</div>
                                <div class="card-body">
                                    <p class="card-title"></p>
                                    <table class="table table-hover" id="dataTables-example" width="100%">
                                        <thead>
                                            <tr>
<!--                                                <th>#</th>-->
                                                <th>LEVEL</th>
                                                <th>ACTIVITY</th>
                                                <th>STATUS</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                                $sq1 = "select *
                                                        from levels
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
                                                <!--<td><?=$rw1['sort'];?></td>-->
                                                <td><?=$rw1['level'];?></td>
                                                <td><?=$status;?></td>
                                                <td>
                                                    <a href="?n=1&id=<?=$rw1['id'];?>" title="Update/Edit"><i class="fa fa-edit"></i></a>
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
                    <form action="" method="post">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="card">
                                    <div class="card-header">Insert new levels</div>
                                    <div class="card-body">
                                        <form accept-charset="utf-8">
                                            <div class="mb-3">
                                                <label for="sort" class="form-label">Level</label>
                                                <input type="number" name="sort" class="form-control">
                                            </div>
                                            <div class="mb-3">
                                                <label for="password" class="form-label">Activity</label>
                                                <textarea name="level" id="" cols="30" rows="5" class="form-control" required></textarea>
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
                    </form>                    
                    <?php } 
                        else if ($n == 1) {
                            $id = $_GET['id'];

                            $sq2 = "SELECT *
                                    FROM levels
                                    WHERE id = '$id'";
                            $rs2 = $conn->query($sq2);

                            $rw2 = $rs2->fetch_assoc();
                    ?>
                    <form action="" method="post">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="card">
                                    <div class="card-header">Edit/Update activity</div>
                                    <div class="card-body">
                                        <form accept-charset="utf-8">
                                            <div class="mb-3">
                                                <label for="password" class="form-label">Level</label>
                                                <input type="text" name="sort" value="<?=$rw2['sort'];?>" class="form-control" required >
                                            </div>
                                            <div class="mb-3">
                                                <label for="password" class="form-label">Activity</label>
                                                <input type="hidden" name="id" value="<?=$_GET['id'];?>">
                                                <textarea name="activity" id="" cols="30" rows="5" class="form-control" required><?=$rw2['level'];?></textarea>
                                            </div>
                                            <div class="mb-3">
                                                <input type="submit" name="update" value="Update Data" class="btn btn-primary">
                                                <input type="reset" class="btn btn-secondary" value="Reset">
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>                       
                        </div>
                    </form>
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

    <script type="text/javascript">
        $(document).ready(function() {
            $('#example1').DataTable();
        } );
    </script>

</body>

</html>