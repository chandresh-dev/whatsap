<?php
// error_log("Error message\n", 3, "../php.log");

class Messages
{

    private $url;
    private $key;
    private $db;
    function __construct($db)
    {
        $this->url = URL_WA;
        $this->db = $db;
        $h = new Home($db);
        $this->key = $h->getApikey();
    }

    public function config($url, $data)
    {


        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->url.$url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => http_build_query($data),
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/x-www-form-urlencoded'
            ),
        ));
       
        $response = curl_exec($curl);
        

curl_close($curl);
            
return json_decode($response, true);
    }

    public function sendMsg($post)
    {
        
        
        $res = $this->config('send-message', [
            'api_key' => $this->key,
            'sender' => $post['sender'],
            'number' => $post['number'],
            'message' => $post['message']
        ]);

        if ($res['status'] === true) {
            setFlashMsg('success', 'Message Sent Successfully');
            back('messages.php');
        } else if ($res['status'] === false) {

            setFlashMsg('danger', $res['data']['msg']);
            back('messages.php');
        }
    }
    public function sendBulkButton($post)
    {
        $res = "";
        
        for($i=0; $i<sizeof($post['number']); $i++){
            $url = 'send-button?';
            // $params = 'sender=' . $post['sender'] . '&api_key=' . $this->key . '&number=' . $post['number'] . '&message=' . $post['message'] . '&footer=' . $post['footer'] . '&button1=' . $post['button1'] . '&button2=' . $post['button2'];
            $res = $this->config($url,  [
                'api_key' => $this->key,
                'sender' => $post['sender'],
                'number' => $post['number'][$i],
                'message' => $post['message'],
                'footer' => $post['footer'],
                'button1' => $post['button1'],
                'button2' => $post['button2']
            ]);
        }
        
        return $res;
    }
    public function sendButton($post)
    {

        $url = 'send-button?';
        // $params = 'sender=' . $post['sender'] . '&api_key=' . $this->key . '&number=' . $post['number'] . '&message=' . $post['message'] . '&footer=' . $post['footer'] . '&button1=' . $post['button1'] . '&button2=' . $post['button2'];
        $res = $this->config($url,  [
            'api_key' => $this->key,
            'sender' => $post['sender'],
            'number' => $post['number'],
            'message' => $post['message'],
            'footer' => $post['footer'],
            'button1' => $post['button1'],
            'button2' => $post['button2']
        ]);
        if ($res['status'] === true) {
            setFlashMsg('success', 'Successfully Sent');
            back('messages.php');
        } else if ($res['status'] === false) {

            setFlashMsg('danger', $res['data']['msg']);
            back('messages.php');
        }
    }
    public function sendImg($post)
    {

        $url = 'send-image';
        // $params = 'sender=' . $post['sender'] . '&api_key=' . $this->key . '&number=' . $post['number'] . '&message=' . $post['message'] . '&footer=' . $post['footer'] . '&button1=' . $post['button1'] . '&button2=' . $post['button2'];
        $res = $this->config($url,  [
            'api_key' => $this->key,
            'sender' => $post['sender'],
            'number' => $post['number'],
            'message' => $post['message'],
            'url' => $post['image'],

        ]);
        if ($res['status'] === true) {
            setFlashMsg('success', 'Successfully Sent');
            back('messages.php');
        } else if ($res['status'] === false) {

            setFlashMsg('danger', $res['data']['msg']);
            back('messages.php');
        }
    }
    public function sendDocument($post)
    {

        $url = 'send-document';
        // $params = 'sender=' . $post['sender'] . '&api_key=' . $this->key . '&number=' . $post['number'] . '&message=' . $post['message'] . '&footer=' . $post['footer'] . '&button1=' . $post['button1'] . '&button2=' . $post['button2'];
        $res = $this->config($url,  [
            'api_key' => $this->key,
            'sender' => $post['sender'],
            'number' => $post['number'],
            'url' => $post['file'],

        ]);
        if ($res['status'] === true) {
            setFlashMsg('success', 'Successfully Sent');
            back('messages.php');
        } else if ($res['status'] === false) {

            setFlashMsg('danger', $res['data']['msg']);
            back('messages.php');
        }
    }

    public function cekBlastByUser()
    {
        $username = $_SESSION['login']['username'];
        $cek = mysqli_query($this->db, "SELECT * FROM blast WHERE make_by = '$username'");
        $result = [];
        if (mysqli_num_rows($cek) > 0) {
            while ($d = mysqli_fetch_array($cek)) {
                $result[] = $d;
            }
        }
        return $result;
    }

    public function cekScheduleByUser()
    {
        $username = $_SESSION['login']['username'];
        $cek = mysqli_query($this->db, "SELECT * FROM schedule WHERE make_by = '$username' ORDER BY id DESC");
        $result = [];
        if (mysqli_num_rows($cek) > 0) {
            while ($d = mysqli_fetch_array($cek)) {
                $result[] = $d;
            }
        }
        return $result;
    }

    public function storeSchedule($post)
    {
        $username = $_SESSION['login']['username'];
        $sender = filter_var(mysqli_real_escape_string($this->db, $_POST['sender']), FILTER_SANITIZE_STRING);
        $message = filter_var(mysqli_real_escape_string($this->db, $_POST['message']), FILTER_SANITIZE_STRING);
        $media = filter_var(mysqli_real_escape_string($this->db, $_POST['media']), FILTER_SANITIZE_STRING);
        $schedule = date('Y-m-d H:i:s', strtotime($_POST['date'] . ' ' . $_POST['time']));

        $cekScheduleNum = mysqli_query($this->db, "SELECT * FROM schedule WHERE make_by = '$username' AND status = 'Pending' ");
        if (mysqli_num_rows($cekScheduleNum) >= 100) {
            setFlashMsg('error', 'You already have 100 pending schedule messages!!');
            back('schedule.php');
        }
        $timestamp = strtotime($schedule);
        //   var_dump($timestamp);

        foreach ($_POST['listnumber'] as $num) {
            $insert = mysqli_query($this->db, "INSERT INTO schedule VALUES(null,'$sender','$num','$message','$media','$schedule','$timestamp','Pending','$username')");
        }
        setFlashMsg('success', 'Successfully Scheduled');
        back('schedule.php');
    }

    public function deleteSchedule($status)
    {
        if (mysqli_query($this->db, "DELETE FROM schedule WHERE status = '$status' ") === true) {
            setFlashMsg('success', 'Schedule Deleted Successfully' . $status);
            back('schedule.php');
        }
    }

    public function dropSchedule($post)
    {
        if (isset($_POST['deletepending'])) {
            $this->deleteSchedule('Pending');
        } else if (isset($_POST['deletesuccess'])) {
            $this->deleteSchedule('Success');
        } else if (isset($_POST['deletefailed'])) {
            $this->deleteSchedule('Failed');
        }
    }
}
