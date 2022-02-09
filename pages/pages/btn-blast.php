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
if (isset($_POST['submit'])) {
    $blast->startBlast($_POST);
}
if (isset($_POST['deleteall'])) {
    $home->deleteBlast();
}
require('templates/header.php');
require('templates/sidebar.php');
?>
<link href="../assets/plugins/select2/css/select2.css" rel="stylesheet">
<link href="../assets/plugins/datatables/datatables.min.css" rel="stylesheet">

<div class="app-content">
    <div class="content-wrapper">
        <div class="container">
            <h2 class="my-5">Bulk Message</h2>
            <?php if (isset($_SESSION['alert'])) : ?>
                <div class="alert alert-<?= $_SESSION['alert']['color'] ?> alert-style-light mt-5" role="alert">
                    <?= $_SESSION['alert']['msg'] ?>
                </div>
                <?php unset($_SESSION['alert']) ?>
            <?php endif; ?>
            <div class="row">
                <div class="card">
                    <div class="card-body">
                        
                        
                                    
                                    
                        <form class="row g-3" method="POST" id="formblast">
                            <div class="col-md-12">
                                <label for="textmessage" class="form-label">Sender</label>
                                <select name="sender" id="sender" class="form-control" style="width: 100%;" required>
                                    <?php foreach ($home->allDevices() as $d) : ?>
                                        <option value="<?= $d['nomor'] ?>"><?= $d['nomor'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-7">
                                <label for="inputEmail4" class="form-label">Number List</label>
                                <div class="thisselect">

                                    <select name="listnumber[]" id="lists" class="form-control" style="width: 100%; height:200px;" multiple="multiple" required>
                                        <?php foreach ($home->contactByUser() as $d) : ?>
                                            <option value="<?= $d['nomor'] ?>"><?= $d['nomor'] ?>( <?= $d['nama'] ?>)</option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-check mt-3">
                                    <input class="form-check-input" id="all" type="checkbox" name="all" id="gridCheck">
                                    <label class="form-check-label" for="gridCheck">
                                       Send to all numbers (In number/contact data) 
                                    </label>
                                </div>
                                <label for="" class="form-label MT-3"> Link images ( JPG,JPEG,PDF ) </label>
                                <input type="text" class="urlmedia form-control" name="media" class="form-control">
                                <p class="text-danger text-small">*Optional</p>
                                <label for="" class="form-label MT-3"> Delay Time </label>
                                <input type="number" class="delay form-control" name="delay" class="form-control">
                            </div>
                            <div class="col-md-5">
                                <label for="inputPassword4" class="form-label">Message</label>
                                <textarea name="pesan" id="pesan" cols="30" rows="10" class="form-control"></textarea>



                            </div>
                            <div class="col-12">
                                <label for="footer" class="form-label">Footer ( Bottom Text )</label>
                                <input type="text" name="footer" class="form-control msgfooter" id="footer" required>
                                <label for="button1" class="form-label">Button 1</label>
                                <input type="text" name="button1" class="form-control btn1" id="button1" required>
                                <label for="button2" class="form-label">Button 2</label>
                                <input type="text" name="button2" class="form-control btn2" id="button2" required>
                            </div>

                            <div class="col-12" id="buttonblast">
                                <button type="submit" id="<?php if($validity){ echo 'startblast';} ?>" name="submit" class="btn btn-primary">Send</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between">
                            <h5 class="card-title">Bulk Message List</h5>
                            <form action="" method="POST">

                                <button type="submit" name="deleteall" class="btn btn-danger" class="btn btn-danger" data-bs-toggle="modal"><i class="material-icons-outlined">remove</i>Delete All</button>
                            </form>
                        </div>
                        <div class="card-body">
                            <table id="datatable1" class="display" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Sender</th>
                                        <th>Destination</th>
                                        <th>Message</th>
                                        <th>Media</th>
                                        <th>Status</th>

                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $i = 1; ?>
                                    <?php foreach ($msg->cekBlastByUser() as $c) : ?>
                                        <?php $i++ ?>
                                        <tr>
                                            <td><?= $i ?></td>
                                            <td><?= $c['sender'] ?></td>
                                            <td><?= $c['nomor'] ?></td>
                                            <td><?= $c['message'] ?></td>
                                            <td><?= $c['media'] ?></td>
                                            <td>
                                                <div class="badge badge-<?= $c['status'] == 'Success' ? 'success' : 'danger' ?>"><?= $c['status'] ?></div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>

                                </tbody>
                                <tfoot></tfoot>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
<script src="../assets/js/pages/datatables.js"></script>
<script src="../assets/js/pages/select2.js"></script>
<script src="../assets/plugins/select2/js/select2.full.min.js"></script>
<script src="../assets/plugins/datatables/datatables.min.js"></script>

<script>
  var cek=0;$("#all").change(function(){this.checked?($("#lists").val([]),$(".thisselect").hide(),$("#lists").attr("disabled",!0),$("#lists").attr("required",!1),cek=1):($("#lists").val([]),$(".thisselect").fadeIn(),$("#lists").attr("disabled",!1),$("#lists").attr("required",!0),cek=0)}),$("#startblast").on("click",a=>{a.preventDefault();var s=[];if($("#lists option:selected").each(function(){s.push($(this).val())}),0==cek&&""==$("#lists").val())return alert("Harap pilih tujuan blast!");if(""==$("#pesan").val())return alert("Pesan wajib diisi!");const e=cek,t=$("#sender").val(),l=$("#pesan").val(),r=$(".msgfooter").val(),i=$(".btn1").val(),n=$(".btn2").val(),d=($(".delay").val(),$(".urlmedia").val()),c={msgfooter:r,btn1:i,btn2:n,selected:s,all:e,sender:t,pesan:l,media:d};$(this).attr("disabled",!0),$("#startblast").html('<span class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true"></span>\n                                                Prossess Blasting...'),$("#sender").attr("disabled",!0),$("#pesan").attr("disabled",!0),$("#lists").attr("disabled",!0),$("#all").attr("disabled",!0),$.ajax({type:"POST",url:"../ajax/sendmsg.php",data:c,success:a=>{window.location=""},error:a=>{alert("Contact Support ( 123 )")}})});
</script>
<?php
require('templates/footer.php');
?>