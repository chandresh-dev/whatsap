<?php
require('../helper/function.php');
$username = $_SESSION['login']['username'];
$msg = new Messages($db);
$home = new Home($db);

if (isset($_POST)) {
    switch ($_POST['all']) {
        case 0;
            $listnumb = $_POST['selected'];
            break;
        case 1;
            $getnum = mysqli_query($db, "SELECT * FROM nomor WHERE make_by = '$username' LIMIT 100");
            $result = [];
            $i=0;
            
            while ($n = mysqli_fetch_array($getnum)) {
                $result[$i] = $n['nomor'];
                $i++;
            }
            
            $listnumb = $result;
            break;
    }
    
    $chunk = $home->getInfo('chunk');
    if (count($listnumb) > $chunk) {
        setFlashMsg('danger', 'Nomor tujuan melebihi maksimal blast,silahkan cek pengaturan untuk ubah jumlah maximal!');
        echo json_encode(false);
        die;
    }
    
    $sender = $_POST['sender'];
    $cekk = mysqli_query($db, "SELECT * FROM device WHERE nomor = '$sender' ");
    $makeby = mysqli_fetch_array($cekk)['pemilik'];
    if ($_POST['media'] === "") {
            
        $data['sender'] = $sender;
        $data['number'] = $listnumb;
        $data['message'] = $_POST['pesan'];
        $data['footer'] = $_POST['msgfooter'];
        $data['button1'] = $_POST['btn1'];
        $data['button2'] = $_POST['btn2'];
        
        $d = $msg->sendBulkButton($data);
            
    } else {
        $d = $msg->config('send-blast-media', [
            'makeby' => $makeby,
            'sender' => $sender,
            'message' => $_POST['pesan'],
            'lists' => $listnumb,
            'delay' => $_POST['delay'],
            'media' => $_POST['media']
        ]);
    }
    if ($d['status'] === true) {
        setFlashMsg('success', $d['msg']);
        echo json_encode(true);
        die;
    }
    setFlashMsg('danger', 'Gagal kirim, Pesan : ' . $d['msg']);
    echo json_encode(false);
    die;
} else {
    die('Akses denied!');
}
