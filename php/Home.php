<?php


class Home
{
    private $db;
    function __construct($db)
    {
        $this->db = $db;
    }

    public function allDevices()
    {

        $own = $_SESSION['login']['username'];
        $data = mysqli_query($this->db, "SELECT * FROM device WHERE pemilik = '$own' ORDER BY id DESC ");
        $result = [];
        if (mysqli_num_rows($data) > 0) {
            while ($d = mysqli_fetch_array($data)) {
                $result[] = $d;
            }
        }
        return $result;
    }

    public function checkStatus($nomord)
    {
        $check = mysqli_query($this->db, "SELECT connect FROM device WHERE nomor = '$nomord' ");
        return mysqli_fetch_array($check);
    }

    public function getApikey()
    {
        $username = $_SESSION['login']['username'];
        $check = mysqli_query($this->db, "SELECT api_key FROM account WHERE username = '$username' ");
        $d = mysqli_fetch_array($check)['api_key'];
        return $d;
    }

    public function addDevice($post)
    {
        $user = $_SESSION['login']['username'];
        $sender = filter_var(mysqli_real_escape_string($this->db, $_POST['sender']), FILTER_SANITIZE_STRING);
        $urlwebhook = filter_var(mysqli_real_escape_string($this->db, $post['urlwebhook']), FILTER_SANITIZE_URL);
        $cek = mysqli_query($this->db, "SELECT * FROM device WHERE nomor = '$sender'");
        if (mysqli_num_rows($cek) > 0) {
            setFlashMsg('danger', 'Sender Already Exists!!');
            back('home.php');
        }
        if (mysqli_query($this->db, "INSERT INTO device VALUES (null,'$user','$sender','$urlwebhook','0')")) {
            setFlashMsg('success', 'Device Added Successfully');
            back('home.php');
        }
    }

    public function contactByUser()
    {

        $own = $_SESSION['login']['username'];
        $data = mysqli_query($this->db, "SELECT * FROM nomor WHERE make_by = '$own' ORDER BY id DESC ");
        $result = [];
        if (mysqli_num_rows($data) > 0) {
            while ($d = mysqli_fetch_array($data)) {
                $result[] = $d;
            }
        }
        return $result;
    }

    public function deleteDevice($id)
    {

        mysqli_query($this->db, "DELETE FROM device WHERE id = '$id'");
        setFlashMsg('success', 'Deleted Successfuly');
        back('home.php');
    }

    public function getInfo($colom)
    {
        $username = $_SESSION['login']['username'];
        $db = mysqli_query($this->db, "SELECT * FROM account WHERE username ='$username'");
        return mysqli_fetch_array($db)[$colom];
    }

    //blast
    public function deleteBlast()
    {
        $username = $_SESSION['login']['username'];
        if (mysqli_query($this->db, "DELETE FROM blast WHERE make_by = '$username'") === true) {
            setFlashMsg('success', 'Blast List Deleted Successfully');
            back('blast.php');
        }
    }

    public function getContact()
    {
        $username = $_SESSION['login']['username'];
        $c = mysqli_query($this->db, "SELECT * FROM nomor WHERE make_by = '$username'");
        return mysqli_num_rows($c);
    }

    public function getTotalBlast($status)
    {

        $username = $_SESSION['login']['username'];
        $c = mysqli_query($this->db, "SELECT * FROM blast WHERE make_by = '$username' AND status = '$status'");
        return mysqli_num_rows($c);
    }
    public function getTotalSchedule($status)
    {

        $username = $_SESSION['login']['username'];
        $c = mysqli_query($this->db, "SELECT * FROM schedule WHERE make_by = '$username' AND status = '$status'");
        return mysqli_num_rows($c);
    }

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


       public function getAllReseller()
    {
        $username = $_SESSION['login']['username'];
        $query = mysqli_query($this->db, "SELECT * FROM reseller_master WHERE make_by = '$username' ORDER BY user_id DESC");
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



     public function updateUser($post)
    {


 $username = $_SESSION['login']['username'];
   $id = filter_var(mysqli_real_escape_string($this->db, $post['uid']), FILTER_SANITIZE_STRING);
             $uname = filter_var(mysqli_real_escape_string($this->db, $post['uname1']), FILTER_SANITIZE_STRING);
        $emailid = filter_var(mysqli_real_escape_string($this->db, $post['emailid1']), FILTER_SANITIZE_STRING);
        $mnum = filter_var(mysqli_real_escape_string($this->db, $post['mnum1']), FILTER_SANITIZE_STRING);
         $country = filter_var(mysqli_real_escape_string($this->db, $post['country1']), FILTER_SANITIZE_STRING);
          // $upass = filter_var(mysqli_real_escape_string($this->db, $post['upass']), FILTER_SANITIZE_STRING);

        //$username = $_SESSION['login']['username'];

        

        //$username = $_SESSION['login']['username'];

        
       
    
                if (mysqli_query($this->db, "UPDATE user_master SET u_name='$uname',u_email='$emailid',u_num='$mnum',u_country='$country' WHERE user_id=$id")) {
                setFlashMsg('success', 'User Successfully Updated');
                back('users.php');
            }


      
   

}


   public function deleteUser($id)
    {
        if (mysqli_query($this->db, "DELETE FROM user_master WHERE user_id = '$id'")) {
            setFlashMsg('success', 'Deleted Successfully');
            back('users.php');
        }
        setFlashMsg('danger', 'System Error');
        back('users.php');
    }


     public function addReseller($post)
    {


 $username = $_SESSION['login']['username'];
        $uname = filter_var(mysqli_real_escape_string($this->db, $post['uname']), FILTER_SANITIZE_STRING);
        $emailid = filter_var(mysqli_real_escape_string($this->db, $post['emailid']), FILTER_SANITIZE_STRING);
        $mnum = filter_var(mysqli_real_escape_string($this->db, $post['mnum']), FILTER_SANITIZE_STRING);
         $country = filter_var(mysqli_real_escape_string($this->db, $post['country']), FILTER_SANITIZE_STRING);
          $upass = filter_var(mysqli_real_escape_string($this->db, $post['upass']), FILTER_SANITIZE_STRING);

        //$username = $_SESSION['login']['username'];

        
        $cek = mysqli_query($this->db, "SELECT * FROM reseller_master WHERE u_name = '$uname' AND u_email = '$emailid' ");

  if (mysqli_num_rows($cek) > 0) {
                setFlashMsg('danger', 'Email and user with that name already exists!');
                back('reseller.php');
            }else{
    
                if (mysqli_query($this->db, "INSERT INTO reseller_master (u_name, u_email, u_num,u_country,u_pwd,u_status,u_pay,make_by) VALUES ('$uname','$emailid','$mnum','$country','$upass','1','yes','$username')")) {
                setFlashMsg('success', 'User Successfully Added');
                back('reseller.php');
            }


      
    }

}



     public function updateReseller($post)
    {


 $username = $_SESSION['login']['username'];
   $id = filter_var(mysqli_real_escape_string($this->db, $post['uid']), FILTER_SANITIZE_STRING);
        $uname = filter_var(mysqli_real_escape_string($this->db, $post['uname']), FILTER_SANITIZE_STRING);
        $emailid = filter_var(mysqli_real_escape_string($this->db, $post['emailid']), FILTER_SANITIZE_STRING);
        $mnum = filter_var(mysqli_real_escape_string($this->db, $post['mnum']), FILTER_SANITIZE_STRING);
         $country = filter_var(mysqli_real_escape_string($this->db, $post['country']), FILTER_SANITIZE_STRING);
          $upass = filter_var(mysqli_real_escape_string($this->db, $post['upass']), FILTER_SANITIZE_STRING);

        //$username = $_SESSION['login']['username'];

        
       
    
                if (mysqli_query($this->db, "UPDATE reseller_master SET u_name='$uname',u_email='$emailid',u_num='$mnum',u_country='$country' WHERE user_id=$id")) {
                setFlashMsg('success', 'User Successfully Updated');
                back('reseller.php');
            }

}

 public function deleteReseller($id)
    {
        if (mysqli_query($this->db, "DELETE FROM reseller_master WHERE user_id = '$id'")) {
            setFlashMsg('success', 'Deleted Successfully');
            back('reseller.php');
        }
        setFlashMsg('danger', 'System Error');
        back('reseller.php');
    }
        public function getAllAssign()
    {
        $username = $_SESSION['login']['username'];
        $query = mysqli_query($this->db, "SELECT * FROM assign_activation WHERE make_by = '$username' ORDER BY aa_id DESC");
        $results = [];
        if (mysqli_num_rows($query) > 0) {
            while ($result = mysqli_fetch_array($query)) {
                $results[] = $result;
            }
        }

        return $results;
    }

     public function addAssign($post)
    {


 $username = $_SESSION['login']['username'];
        $uname = filter_var(mysqli_real_escape_string($this->db, $post['uname']), FILTER_SANITIZE_STRING);
        $pkg = filter_var(mysqli_real_escape_string($this->db, $post['pkg']), FILTER_SANITIZE_STRING);
        $amount = filter_var(mysqli_real_escape_string($this->db, $post['amount']), FILTER_SANITIZE_STRING);
        
        //$username = $_SESSION['login']['username'];

        
        $cek = mysqli_query($this->db, "SELECT * FROM assign_activation WHERE user_ids = '$uname' ");

  if (mysqli_num_rows($cek) > 0) {
                setFlashMsg('danger', 'User with that name already exists!');
                back('assignactive.php');
            }else{
    
                if (mysqli_query($this->db, "INSERT INTO assign_activation (user_ids, aa_pkg, aa_amt,make_by) VALUES ('$uname','$pkg','$amount','$username')")) {
                setFlashMsg('success', 'User Successfully Added');
                back('assignactive.php');
            }


      
    }

}
//  public function useractivation($data)
//     {

// $id=filter_var(mysqli_real_escape_string($this->db, $post['id']),FILTER_SANITIZE_STRING);
// $status=filter_var(mysqli_real_escape_string($this->db, $post['id']),FILTER_SANITIZE_STRING);
// $sql=mysqli_query($this->db,"UPDATE user_master SET u_status='$status' WHERE user_id='$id'");
// echo 1;
// }

 public function deleteAssign($id)
    {
        if (mysqli_query($this->db, "DELETE FROM assign_activation WHERE aa_id = '$id'")) {
            setFlashMsg('success', 'Deleted Successfully');
            back('assignactive.php');
        }
        setFlashMsg('danger', 'System Error');
        back('assignactive.php');
    }





}




?>