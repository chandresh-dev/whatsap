<?php
require('../helper/function.php');

$auth = new Auth($db);
$home = new Home($db);
$msg = new Messages($db);
if (!$auth->isLogin()) {
    header('Location: login.php');
    exit;
}

$validity = true;
if(!$auth->validity()){
    $validity = false;
}

if (isset($_POST['sendMsg'])) {
    $msg->sendMsg($_POST);
}

if (isset($_POST['sendButton'])) {
    $msg->sendButton($_POST);
}
if (isset($_POST['sendImg'])) {
    $msg->sendImg($_POST);
}
if (isset($_POST['sendDocument'])) {
    $msg->sendDocument($_POST);
}
require('templates/header.php');
require('templates/sidebar.php');
?>
<link href="../assets/plugins/select2/css/select2.css" rel="stylesheet">
<div class="app-content">
    <div class="content-wrapper">
        <div class="container">
            <h2 class="my-5">Test Kirim</h2>
            <?php if (isset($_SESSION['alert'])) : ?>
                <div class="alert alert-<?= $_SESSION['alert']['color'] ?> alert-style-light mt-5" role="alert">
                    <?= $_SESSION['alert']['msg'] ?>
                </div>
                <?php unset($_SESSION['alert']) ?>
            <?php endif; ?>
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Text Message</h5>
                        </div>
                        <div class="card-body">

                            <div class="example-container">
                                <div class="example-content">
                                    <form action="" method="POST" id="formSendMsg">
                                        <label for="textmessage" class="form-label">Sender</label>
                                        <select name="sender" id="" class="form-control" style="width: 100%;" required>
                                            <?php foreach ($home->allDevices() as $d) : ?>
                                                <option value="<?= $d['nomor'] ?>"><?= $d['nomor'] ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <label for="number" class="form-label">Number</label>
                                        <input type="text" name="number" class="form-control" id="number" required>
                                        <label for="textmessage" class="form-label">Message</label>
                                        <input type="text" name="message" class="form-control" id="textmessage" required>
                                        <button type="submit" name="<?php if($validity){echo 'sendMsg'; } ?>" class="btn btn-success mt-3"><i class="material-icons-outlined">send</i>Send</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Image Message</h5>
                        </div>
                        <div class="card-body">

                            <div class="example-container">
                                <div class="example-content">
                                    <form action="" method="POST" id="formSendMsg">
                                        <label for="textmessage" class="form-label">Sender</label>
                                        <select name="sender" id="" class="form-control" style="width: 100%;" required>
                                            <?php foreach ($home->allDevices() as $d) : ?>
                                                <option value="<?= $d['nomor'] ?>"><?= $d['nomor'] ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <label for="number" class="form-label">Number</label>
                                        <input type="text" name="number" class="form-control" id="number" required>
                                        <label for="textmessage" class="form-label">Message</label>
                                        <input type="text" name="message" class="form-control" id="textmessage" required>
                                        <label for="image" class="form-label">Image Url</label>
                                        <input type="text" name="image" class="form-control" id="image" required>

                                        <button type="submit" name="<?php if($validity){echo 'sendImg'; } ?>" class="btn btn-success mt-3"><i class="material-icons-outlined">send</i>Send</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>




                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Message with Button</h5>
                        </div>
                        <div class="card-body">

                            <div class="example-container">
                                <div class="example-content">
                                    <form action="" method="POST" id="formSendMsg">
                                        <label for="textmessage" class="form-label">Sender</label>
                                        <select name="sender" id="" class="form-control" style="width: 100%;" required>
                                            <?php foreach ($home->allDevices() as $d) : ?>
                                                <option value="<?= $d['nomor'] ?>"><?= $d['nomor'] ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <label for="number" class="form-label">Number</label>
                                        <input type="text" name="number" class="form-control" id="number" required>
                                        <label for="textmessage" class="form-label">Message</label>
                                        <input type="text" name="message" class="form-control" id="textmessage" required>
                                        <label for="footer" class="form-label">Footer ( Bottom Text )</label>
                                        <input type="text" name="footer" class="form-control" id="footer" required>
                                        <label for="button1" class="form-label">Button 1</label>
                                        <input type="text" name="button1" class="form-control" id="button1" required>
                                        <label for="button2" class="form-label">Button 2</label>
                                        <input type="text" name="button2" class="form-control" id="button2" required>
                                        <button type="submit" name="<?php if($validity){echo 'sendButton'; } ?>" class="btn btn-success mt-3"><i class="material-icons-outlined">send</i>Send</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Message with Document</h5>
                        </div>
                        <div class="example-container">
                            <div class="example-content">
                                <form action="" method="POST" id="formSendMsg">
                                    <label for="textmessage" class="form-label">Sender</label>
                                    <select name="sender" id="" class="form-control" style="width: 100%;" required>
                                        <?php foreach ($home->allDevices() as $d) : ?>
                                            <option value="<?= $d['nomor'] ?>"><?= $d['nomor'] ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <label for="number" class="form-label">Number</label>
                                    <input type="text" name="number" class="form-control" id="number" required>

                                    <label for="file" class="form-label">File Url</label>
                                    <input type="text" name="file" class="form-control" id="file" required>

                                    <button type="submit" name="<?php if($validity){echo 'sendDocument'; } ?>" class="btn btn-success mt-3"><i class="material-icons-outlined">send</i>Send</button>
                                </form>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
</div>
<script src="../assets/js/pages/select2.js"></script>
<script src="../assets/plugins/select2/js/select2.full.min.js"></script>
<script>

</script>
<?php
require('templates/footer.php');
?>