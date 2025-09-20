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

            $case  = mysqli_real_escape_string($conn, $_POST['observation']);
            $activity_id    = $_POST['activity_id'];
            $level_id       = $_POST['level_id'];
      
            $sq1 = "select *
                    from observations
                    where observation = '$case'";
            $re1 = $conn->query($sq1);

            //echo $sq1;
      
            if ($re1->num_rows > 0){
              echo "<script type='text/javascript'>
                          alert('Sorry! Data already available.');
                          window.location='manage-observation.php';
                      </script>";
              exit();
            } else {
              // simpan baru
              $sq2 = "insert into observations (observation, activity_id, level_id) 
                              values ('$case', '$activity_id', '$level_id')";
              //echo $sq2;
              if ($conn->query($sq2) === TRUE) {
                echo "<script type='text/javascript'>
                          alert('Data save successfully.');
                          window.location='manage-observation.php';
                      </script>";
                exit();
              }
            }
        } // end save new data

        // update data
        if (isset($_POST['update'])){

//            print_r($_POST);
//            exit;

            $id             = $_POST['id'];
            $activity_id    = $_POST['activity_id'];
            $observation    = $_POST['observation'];
            $level_id       = $_POST['level_id'];

            $observation   = mysqli_real_escape_string($conn, $_POST['observation']);

            $sql = "UPDATE observations
                    SET observation = '$observation',
                        activity_id = '$activity_id',
                        level_id = '$level_id'
                    WHERE id = '$id'";
            if ($conn->query($sql) === TRUE){
                echo "<script type='text/javascript'>
                          alert('Data update successfully.');
                          window.location='manage-observation.php';
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
            $s1 = "delete from observations where id = '$id'";
      
            if ($conn->query($s1) === TRUE){
              echo "<script type='text/javascript'>
                            window.location='manage-observation.php';
                      </script>";
              exit();
            }
            //exit;
          }

        // for filtering
        $conditions = '';
        if (isset($_GET['filter'])){
            if ($_GET['activity_id'] > 0){
                $conditions .= ' and activity_id = ' . $_GET['activity_id'];
            }

            if ($_GET['level_id'] > 0){
                $conditions .= ' and level_id = ' . $_GET['level_id'];
            }
//            print_r($_GET);
//            exit;
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
                            <h2 class="page-title">Observation</h2>
                        </div>
                    </div>
                    <div class="row">

                        <div class="col-md-12 col-lg-12 mt-3">
                            <div class="card">
                                <div class="card-header">Filter</div>
                                <div class="card-body">
                                    <form action="" method="get">
                                        <div class="col-lg-12 mb-3">
                                            <label for="" class="mb-2">Activity</label>
                                            <select name="activity_id" id="activity_id" class="form-control">
                                                <option value=""></option>
                                                <?php
                                                    $sql01 = "select *
                                                              from activities
                                                              order by id asc";
                                                    $rst01 = $conn->query($sql01);
                                                    while ($row01 = $rst01->fetch_assoc()){
                                                ?>
                                                <option value="<?=$row01['id'];?>"><?=$row01['activity'];?></option>
                                                <?php
                                                    }
                                                ?>
                                            </select>
                                        </div>
                                        <div class="col-lg-12 mb-3">
                                            <label for="" class="mb-2">Level</label>
                                            <select name="level_id" id="level_id" class="form-control">
                                                <option value=""></option>
                                                <?php
                                                $sql02 = "select *
                                                          from levels
                                                          order by id asc";
                                                $rst02 = $conn->query($sql02);
                                                while ($row02 = $rst02->fetch_assoc()){
                                                    ?>
                                                    <option value="<?=$row02['id'];?>"><?=$row02['level'];?></option>
                                                    <?php
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <button class="btn btn-primary" type="submit" name="filter"> Filter </button>
                                    </form>
                                </div>
                            </div>
                        </div>


                        <div class="col-md-12 col-lg-12 mt-3">
                            <div class="card">
                                <div class="card-header">List of available observations</div>
                                <div class="card-body">
                                    <p class="card-title"></p>
                                    <table class="table table-hover" id="dataTables-example" width="100%">
                                        <thead>
                                            <tr>
<!--                                                <th>#</th>-->
                                                <th>ACTIVITY</th>
                                                <th>OBSERVATION</th>
                                                <th>LEVEL</th>
                                                <th>STATUS</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                                $sq1 = "select *, a.id as oid, b.id as aid, c.id as lid
                                                        from observations  as a 
                                                        join activities as b on a.activity_id = b.id    
                                                        join levels as c on a.level_id = c.id
                                                        where a.status = '1'" . $conditions;
                                                $rs1 = $conn->query($sq1);



                                                //echo $sq1;

                                                $num = 0;
                                                while ($rw1 = $rs1->fetch_assoc()){
                                                    // get level
                                                    $levelInfo = getLevel($conn, $rw1['lid']);

                                                    if ($rw1['status'] == '1'){
                                                        $status = 'Active';
                                                    } else {
                                                        $status = 'Inactive';
                                                    }
                                            ?>
                                            <tr>
<!--                                                <td>--><?//=++$num;?><!--</td>-->
                                                <td><?=$rw1['activity'];?></td>
                                                <td><?=$rw1['observation'];?></td>
                                                <td><?=$levelInfo[1];?></td>
                                                <td><?=$status;?></td>
                                                <td>
                                                    <a href="?n=1&id=<?=$rw1['oid'];?>" title="Update/Edit"><i class="fa fa-edit"></i></a>
                                                    <a href="?n=3&id=<?=$rw1['oid'];?>" title="Delete"><i class="fa fa-trash" onClick="javascript:return confirm('Are you sure to delete this?');"></i></a>
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
                                    <div class="card-header">Insert new observation</div>
                                    <div class="card-body">
                                        <form accept-charset="utf-8">
                                            <div class="mb-3">
                                                <label for="password" class="form-label">Activity</label>
                                                <select name="activity_id" id="" class="form-control" required>
                                                    <option value="">  </option>
                                                    <?php
                                                        $sq1 = "select *
                                                                from activities
                                                                where status = '1'";
                                                        $rs1 = $conn->query($sq1);

                                                        $num = 0;
                                                        while ($rw1 = $rs1->fetch_assoc()){
                                                    ?>
                                                    <option value="<?=$rw1['id'];?>"><?=$rw1['activity'];?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>

                                            <div class="mb-3">
                                                <label for="password" class="form-label">Observation</label>
                                                <textarea name="observation" id="" cols="30" rows="5" class="form-control" required></textarea>
                                            </div>

                                            <div class="mb-3">
                                                <label for="password" class="form-label">Level</label>
                                                <select name="level_id" id="" class="form-control" required>
                                                    <option value="">  </option>
                                                    <?php
                                                    $sq2 = "select *
                                                            from levels
                                                            where status = '1'";
                                                    $rs2 = $conn->query($sq2);

                                                    while ($rw2 = $rs2->fetch_assoc()){
                                                        ?>
                                                        <option value="<?=$rw2['id'];?>"><?=$rw2['level'];?></option>
                                                    <?php } ?>
                                                </select>
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

                            $sq2 = "select *, a.id as oid, b.id as aid, c.id as lid
                                    from observations  as a 
                                    join activities as b on a.activity_id = b.id    
                                    join levels as c on a.level_id = c.id
                                    where a.id = '$id'";
                            $rs2 = $conn->query($sq2);

                            $rw2 = $rs2->fetch_assoc();
                    ?>
                    <form action="" method="post">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="card">
                                    <div class="card-header">Edit/Update observation</div>
                                    <div class="card-body">
                                        <form accept-charset="utf-8">
                                            <div class="mb-3">
                                                <label for="password" class="form-label">Activity</label>
                                                <input type="hidden" class="form-control" name="id" value="<?=$_GET['id'];?>">
                                                <select name="activity_id" id="" class="form-control" required>
                                                    <option value="<?=$rw2['aid'];?>"><?=$rw2['activity'];?></option>
                                                    <?php
                                                    $sq1 = "select *
                                                            from activities
                                                            where id != " . $rw2['lid'];
                                                    $rs1 = $conn->query($sq1);

                                                    $num = 0;
                                                    while ($rw1 = $rs1->fetch_assoc()){
                                                        ?>
                                                        <option value="<?=$rw1['id'];?>"><?=$rw1['activity'];?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>

                                            <div class="mb-3">
                                                <label for="password" class="form-label">Observation</label>
                                                <textarea name="observation" id="" cols="30" rows="5" class="form-control" required><?=$rw2['observation'];?></textarea>
                                            </div>

                                            <div class="mb-3">
                                                <label for="password" class="form-label">Level</label>
                                                <select name="level_id" id="" class="form-control" required>
                                                    <option value="<?=$rw2['aid'];?>"><?=$rw2['level'];?></option>
                                                    <?php
                                                    $sq3 = "select *
                                                            from levels
                                                            where id != " . $rw2['aid'];
                                                    $rs3 = $conn->query($sq3);

                                                    while ($rw3 = $rs3->fetch_assoc()){
                                                        ?>
                                                        <option value="<?=$rw3['id'];?>"><?=$rw3['level'];?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                            <div class="mb-3" style="margin-top:15px;">
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
</body>

</html>