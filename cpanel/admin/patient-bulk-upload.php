<?php
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
    ini_set('session.save_path', '/var/lib/php/sessions');
?>

<?php
    require_once('includes/connection.php');

    if (isset($_POST['btnUpload'])){
        if($_FILES['csv_data']['name']){

            $arrFileName = explode('.',$_FILES['csv_data']['name']);

            $jumlah = 0;
            if($arrFileName[1] == 'csv'){
                $handle = fopen($_FILES['csv_data']['tmp_name'], "r");
                while (($data = fgetcsv($handle, 3500, ",")) !== FALSE) {

                    $mrn        = $data[0];
                    $name       = mysqli_real_escape_string($conn,$data[1]);
                    $gender     = mysqli_real_escape_string($conn,$data[2]);
                    $idnumber   = mysqli_real_escape_string($conn,$data[5]);
                    $phone      = mysqli_real_escape_string($conn,$data[6]);


                    //echo $mrn . ' | ' . $name . ' | ' . $gender . ' | ' . $idnumber . ' | ' . $phone;

                    $year = substr($idnumber, 0,2);
                    $month = substr($idnumber, 2,2);
                    $day = substr($idnumber, 4,2);

                    $result = $year;
                    $year = '20'.$result;

                    $year = (int) $year;
                    if($year > date('Y')) {
                        $year = $year - 100;
                    }

                    $birthdate = $day . '-' . $month . '-' . $year;

                    // get age
                    $age = date('Y') - $year;

                    $sql = "INSERT INTO patients (mrn, name, gender, birthdate, age, idnumber, phone) VALUES ('$mrn', '$name', '$gender', '$birthdate', '$age', '$idnumber', '$phone')";
                    $result = $conn->query($sql);

                    ++$jumlah;


                }
                fclose($handle);
                //print "Import done";
                echo "<script type='text/javascript'>
                            alert('Patient data uploaded successfully.');
                            window.location='patient.php';
                      </script>";
                exit();
            }
        }

        //echo '<br><br><br>Jumlah Disimpan: ' . $jumlah;
    }
?>