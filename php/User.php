<?php 
$link = mysqli_connect("localhost", "root", "123456", "amit_db");
extract($_POST);
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

?>