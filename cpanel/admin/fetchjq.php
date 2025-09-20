<?php
//fetch.php
require_once ('includes/connection.php');

$output = '';
if(isset($_POST["query"]))
{
    $search = mysqli_real_escape_string($conn, $_POST["query"]);
//    $query = "SELECT * FROM tbl_customer
//              WHERE CustomerName LIKE '%".$search."%'
//              OR Address LIKE '%".$search."%'
//              OR City LIKE '%".$search."%'
//              OR PostalCode LIKE '%".$search."%'
//              OR Country LIKE '%".$search."%' ";
    $query = "SELECT * FROM patients 
              WHERE mrn LIKE '%".$search."%'";
}
else
{
    $query = "SELECT * 
              FROM patients 
              ORDER BY mrn";
}
$result = mysqli_query($conn, $query);
if(mysqli_num_rows($result) > 0)
{
    $output .= '
  <div class="table-responsive">
   <table class="table table bordered">
    <tr>
     <th>MRN</th>
     <th>Name</th>
     <th>Gender</th>
     <th>NRIC</th>
     <th>Phone</th>
    </tr>
 ';
    while($row = mysqli_fetch_array($result))
    {
        $output .= '
   <tr>
    <td>'.$row["mrn"].'</td>
    <td>'.$row["name"].'</td>
    <td>'.$row["gender"].'</td>
    <td>'.$row["idnumber"].'</td>
    <td>'.$row["phone"].'</td>
   </tr>
  ';
    }
    echo $output;
}
else
{
    echo 'Data Not Found';
}

?>