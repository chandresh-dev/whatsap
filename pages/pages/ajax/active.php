<!-- <?php
 //$db= new mysqli('localhost','root','2413@#Ck','amit_db'); 
  private $db;
extract($_POST);
//$user_id=$db->real_escape_string($id);
   $id = filter_var(mysqli_real_escape_string($this->db, $id), FILTER_SANITIZE_STRING);
      $status = filter_var(mysqli_real_escape_string($this->db, $status), FILTER_SANITIZE_STRING);
//$status=$db->real_escape_string($status);
$sql=mysqli_query($this->db,"UPDATE user_master SET u_status='$status' WHERE user_id='$id'");
echo 1;
?> -->

<?php

//session_start();

if ($_POST['id']) {

$link = mysqli_connect("location", "root", "123456", "amit_db");

if (mysqli_connect_error()) {
    echo "Could not connect to database";
    die;
}
$id = mysqli_real_escape_string($link, $id);
      $status = mysqli_real_escape_string($link, $status);
$query = "UPDATE user_master SET u_status='$status' WHERE user_id='$id'";

if (mysqli_query($link, $query)) {
    echo "Success";
} else {
    echo mysqli_error($link);
    echo "Failure";
}

}

?>