<?php


class Users
{
    private $db;
    function __construct($db)
    {
        $this->db = $db;
           
        $h = new Home($db);
    }

  

   

   

    // public function deleteDevice($id)
    // {

    //     mysqli_query($this->db, "DELETE FROM device WHERE id = '$id'");
    //     setFlashMsg('success', 'Deleted Successfuly');
    //     back('Users.php');
    // }

    

    //blast
  
     

       public function getAllUser()
    {
        $username = $_SESSION['login']['username'];
        $query = mysqli_query($this->db, "SELECT * FROM user_master WHERE make_by = '$username' ORDER BY user_id DESC");
        $results = [];
        if (mysqli_num_rows($query) > 0) {
            while ($result = mysqli_fetch_array($query)) {
                $results[] = $result;
            }
        }

        return $results;
    }
        public function allCountry()
    {
        $username = $_SESSION['login']['username'];
        $query = mysqli_query($this->db, "SELECT * FROM countries WHERE make_by = '$username' ORDER BY id ASC");
        $results = [];
        if (mysqli_num_rows($query) > 0) {
            while ($result = mysqli_fetch_array($query)) {
                $results[] = $result;
            }
        }

        return $results;
    }

     public function addUser($post)
    {


 $username = $_SESSION['login']['username'];
        $uname = filter_var(mysqli_real_escape_string($this->db, $post['uname']), FILTER_SANITIZE_STRING);
        $emailid = filter_var(mysqli_real_escape_string($this->db, $post['emailid']), FILTER_SANITIZE_STRING);
        $mnum = filter_var(mysqli_real_escape_string($this->db, $post['mnum']), FILTER_SANITIZE_STRING);
         $country = filter_var(mysqli_real_escape_string($this->db, $post['country']), FILTER_SANITIZE_STRING);
          $upass = filter_var(mysqli_real_escape_string($this->db, $post['upass']), FILTER_SANITIZE_STRING);

        //$username = $_SESSION['login']['username'];

        
        $cek = mysqli_query($this->db, "SELECT * FROM user_master WHERE u_name = '$uname' AND u_email = '$emailid' ");

  if (mysqli_num_rows($cek) > 0) {
                setFlashMsg('danger', 'Email and user with that name already exists!');
                back('users.php');
            }else{
    
                if (mysqli_query($this->db, "INSERT INTO user_master (u_name, u_email, u_num,u_country,u_pwd,u_status,u_pay,make_by) VALUES ('$uname','$emailid','$mnum','$country','$upass','1','yes','$username')")) {
                setFlashMsg('success', 'User Successfully Added');
                back('users.php');
            }


      
    }

}
 public function useractivation($data)
    {
print_r($data);
die();
$id=filter_var(mysqli_real_escape_string($this->db, $post['id']),FILTER_SANITIZE_STRING);
$status=filter_var(mysqli_real_escape_string($this->db, $post['id']),FILTER_SANITIZE_STRING);
$sql=mysqli_query($this->db,"UPDATE user_master SET u_status='$status' WHERE user_id='$id'");
echo 1;
}
}