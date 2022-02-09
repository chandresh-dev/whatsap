<?php
// V5.0.0 MPWA 
// https://m-pedia.id
// contact 6282298859671 / ilmansunannudin2@gmail.com if you need any help. 
date_default_timezone_set('Asia/Calcutta');
class Auth
{
   
    private $db;
    public $error;
    public $sesi;
    function __construct($db)
    {
        $this->db = $db;
    }



    public function login($username, $password)
    {
        $user = filter_var(mysqli_real_escape_string($this->db, $username), FILTER_SANITIZE_STRING);
        $pass = filter_var(mysqli_real_escape_string($this->db, $password), FILTER_SANITIZE_STRING);

        $check = mysqli_query($this->db, "SELECT * FROM account WHERE username = '$user' ");
        if (mysqli_num_rows($check) > 0) {
            $datauser = mysqli_fetch_assoc($check);

            if (password_verify($pass, $datauser['password'])) {
                $_SESSION['login'] = $datauser;
                $this->sesi = $_SESSION['login'];
                header('Location: home.php');
                exit;
            } else {

                $_SESSION['alert'] = ['color' => 'danger', 'msg' => 'Wrong Username Or Password!'];
                header('Location: login.php');
                exit;
            }
        } else {
            $_SESSION['alert'] = ['color' => 'danger', 'msg' => 'Wrong Username Or Password!!'];
            header('Location: login.php');
            exit;
        }
    }


    public function register($post)
    {   $name = filter_var(mysqli_real_escape_string($this->db, $post['name']), FILTER_SANITIZE_STRING);
        $username = filter_var(mysqli_real_escape_string($this->db, $post['username']), FILTER_SANITIZE_STRING);
        $mobile = filter_var(mysqli_real_escape_string($this->db, $post['mobile']), FILTER_SANITIZE_STRING);
        //$password = filter_var(mysqli_real_escape_string($this->db, $post['password']), FILTER_SANITIZE_STRING);//
        
        
        $date = date("Y-m-d H:i:s");

        $check = mysqli_query($this->db, "SELECT * FROM account WHERE username = '$$mobile' OR mobile = '$mobile'");
        if (mysqli_num_rows($check) > 0) {
            $_SESSION['alert'] = ['color' => 'danger', 'msg' => 'Email or WhatsApp Number Already Exists !!!'];
        } else {
            $password = rand(100000,999999);
            $apikey =  apikey();
            $newpass = password_hash($password, PASSWORD_DEFAULT);
            if (mysqli_query($this->db, "INSERT INTO account(`name`, username, mobile, `password`, api_key, `level`, chunk) VALUES ('$name','$username','$mobile','$newpass','$apikey','2','100')")) {
                $validity = date('Y-m-d H:i:s', strtotime($date . ' +1 day'));
                mysqli_query($this->db, "INSERT INTO validity(`username`, `validity`) VALUES ('$username', '$validity')");
                $_SESSION['alert'] = ['color' => 'success', 'msg' => 'Register Successfully, Please login'];
                $url = "https://v3.dizitalsurface.com/send-button";
                $parameter = json_encode(array("number" => $mobile, "api_key" => "i3TvTqYzF1nvz0bgZmon", "sender" => "918109031696", 
                "message" => "
Dear *".$name."* 
You Have Successfully Registered on *API37 - Button API*
Your Login Creditials Are Below
*Email* : $username
*Password* : $password
*Mobile* : $mobile",
"footer" => "API37 - Button API",
"button1" => "Support", "button2" => "Login Now v3.dizitalsurface.com/index.php"));
                
                $ch = curl_init($url);            
                curl_setopt($ch, CURLOPT_POSTFIELDS, $parameter);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $result = curl_exec($ch);
                curl_close($ch);
              
  
                header('Location: login.php');
                exit;
            }
        }
    }

    public function isLogin()
    {
        if (isset($_SESSION['login']) && $_SESSION['login'] != '') {
            return true;
        }
        return false;
    }

    public function validity()
    {   
        $username = $_SESSION['login']['username'];
        if ($cek = mysqli_query($this->db, "SELECT validity, device FROM validity WHERE username = '$username' ORDER BY created_at DESC LIMIT 1")) {
            
            if (mysqli_num_rows($cek) > 0) {
                $output = mysqli_fetch_array($cek);
                $validity = $output['validity'];
                $device = $output['device'];
                
                $currendDateTime = date('m/d/Y h:i:s');
                
                $_SESSION['validity'] = $validity;
                $_SESSION['device'] = $device;
                
                if (strtotime($validity) > strtotime($currendDateTime)) {
                    return true;
                }else{
                    mysqli_query($this->db, "DELETE FROM device WHERE pemilik='$username'");
                    setFlashMsg('danger', 'Validity Expiry');
                    return false;
                }
                
            }else{
                setFlashMsg('danger', 'Server Error!! Try Again Later');
                return false;
            }
        }else{
            setFlashMsg('danger', 'Server Error!! Try Again Later');
            return false;
        }
    }
    
    public function changechunk($chunk)
    {
        $username = $_SESSION['login']['username'];
        if (mysqli_query($this->db, "UPDATE account SET chunk = '$chunk' WHERE username = '$username' ") === true) {
            setFlashMsg('success', 'The maximum sending mass messages has been successfully changed');
            back('pengaturan.php');
        }
    }
    public function getNewKey()
    {
        $username = $_SESSION['login']['username'];
        $newkey = apikey();
        if (mysqli_query($this->db, "UPDATE account SET api_key = '$newkey' WHERE username = '$username'") === true) {
            setFlashMsg('success', 'Successfully Changed');
            back('pengaturan.php');
        }
    }
    // 
    public function changepass($post)
    {
        $username = $_SESSION['login']['username'];
        if (strlen($post['newpass']) < 6) {
            setFlashMsg('danger', 'Password must be greater than 6 character!');
            back('pengaturan.php');
        }
        if ($post['newpass'] != $post['confnewpass']) {
            setFlashMsg('danger', 'Confirm password does not match!');
            back('pengaturan.php');
        }
        $cek = mysqli_query($this->db, "SELECT * FROM account WHERE username = '$username'");
        if (mysqli_num_rows($cek) > 0) {
            $oldpasss = mysqli_fetch_array($cek)['password'];
            if (password_verify($post['oldpass'], $oldpasss)) {
                $newpassword = password_hash($post['newpass'], PASSWORD_DEFAULT);
                if (mysqli_query($this->db, "UPDATE account SET password = '$newpassword' WHERE username = '$username' ") === true) {
                    setFlashMsg('success', 'Password Changed Successfully');
                    session_destroy();
                    back('login.php');
                }
                setFlashMsg('danger', 'Server Error!! Try Again Later');
                back('pengaturan.php');
            }
            setFlashMsg('danger', 'Old Password Does not Match!!');
            back('pengaturan.php');
        }
    }


    public function logout()
    {
        session_destroy();
        back('login.php');
    }
}
