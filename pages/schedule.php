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
    $msg->storeSchedule($_POST);
}
if (isset($_POST['deletepending']) || isset($_POST['deletesuccess']) || isset($_POST['deletefailed'])) {
    $msg->dropSchedule($_POST);
}

require('templates/header.php');
require('templates/sidebar.php');
?>

<div class="app-content">
    <link href="../assets/plugins/datatables/datatables.min.css" rel="stylesheet">
    <link href="../assets/plugins/select2/css/select2.css" rel="stylesheet">
    <div class="content-wrapper">
        <div class="container">
            <h2 class="my">Schedule Message</h2>
            <?php if (isset($_SESSION['alert'])) : ?>
                <div class="alert alert-<?= $_SESSION['alert']['color'] ?> alert-style-light mt-5" role="alert">
                    <?= $_SESSION['alert']['msg'] ?>
                </div>
                <?php unset($_SESSION['alert']) ?>
            <?php endif; ?>

            <div class="card-header d-flex justify-content-between">

            </div>
            <div class="row">
                <div class="col">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between">
                            <h5 class="card-title">Daftar Schedule</h5>
                            <div class="d-flex justify-content-between">
                                <form action="" method="POST">
                                    <button type="submit" name="deletepending" class="btn btn-warning mx-2"><i class="material-icons-outlined">delete</i>Delete ( Pending )</button>
                                </form>
                                <form action="" method="POST">

                                    <button type="submit" name="deletesuccess" class="btn btn-danger mx-2"><i class="material-icons-outlined">delete</i>Delete ( Success )</button>
                                </form>
                                <form action="" method="POST">

                                    <button type="submit" name="deletefailed" class="btn btn-secondary mx-2"><i class="material-icons-outlined">delete</i>Delete ( Failed )</button>
                                </form>
                                <?php if($validity){ ?>
                                <button type="button" class="btn btn-primary " data-bs-toggle="modal" data-bs-target="#addSchedule"><i class="material-icons-outlined">add</i>Add Schedule</button>
                                <?php } ?>

                            </div>
                        </div>
                        <div class="card-body">
                            <table id="datatable1" class="display" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>Sender</th>
                                        <th>Number</th>
                                        <th>Message</th>
                                        <th>Media</th>
                                        <th>Time</th>
                                        <th>Status</th>

                                    </tr>
                                </thead>
                                <tbody>

                                    <?php foreach ($msg->cekScheduleByUser() as $s) : ?>
                                        <tr>
                                            <td><?= $s['sender'] ?></td>
                                            <td><?= $s['nomor'] ?></td>
                                            <td><?= $s['pesan'] ?></td>
                                            <td><?= $s['media'] ?></td>
                                            <td><?= tgl_indo($s['jadwal']) ?></td>
                                            <td>
                                                <div class="badge badge-<?= $s['status'] == 'Success' ? 'success' : 'warning' ?>"><?= $s['status'] ?></div>
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

<!-- modal tambah data -->
<!-- Modal -->
<div class="modal fade" id="addSchedule" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Add Schedule</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="" method="POST">
                    <label for="inputEmail4" class="form-label">sender</label>
                    <select name="sender" id="" class="form-control" style="width: 100%;" required>
                        <?php foreach ($home->allDevices() as $d) : ?>
                            <option value="<?= $d['nomor'] ?>"><?= $d['nomor'] ?></option>
                        <?php endforeach; ?>
                    </select>
                    <label for="inputEmail4" class="form-label">Select Recepients Number</label>
                    <div class="thisselect">

                        <select name="listnumber[]" id="lists" class="form-control" style="width: 100%; height:200px;" multiple="multiple" required>
                            <?php foreach ($home->contactByUser() as $d) : ?>
                                <option value="<?= $d['nomor'] ?>"><?= $d['nomor'] ?>( <?= $d['nama'] ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <label for="message" class="form-label">Message</label>
                    <textarea name="message" class="form-control" id="" cols="20" rows="10" required></textarea>
                    <label for="nomor" class="form-label">Media (JPG/JPEG/PNG/PDF/DOC/DOCX) </label>
                    <input type="text" name="media" class="form-control" id="nomor">
                    <label for="nomor" class="form-label">Delivery Date</label>
                    <input type="date" name="date" class="form-control" id="nomor" required>
                    <label for="nomor" class="form-label">Delivery Time</label>
                    <input type="time" name="time" class="form-control" id="nomor" required>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" name="submit" class="btn btn-primary">Save</button>
                </form>
            </div>
        </div>
    </div>
</div>
<!--  -->
<!-- Javascripts -->
<script src="../assets/plugins/jquery/jquery-3.5.1.min.js"></script>
<script src="../assets/plugins/bootstrap/js/popper.min.js"></script>
<script src="../assets/plugins/bootstrap/js/bootstrap.min.js"></script>
<script src="../assets/plugins/perfectscroll/perfect-scrollbar.min.js"></script>
<script src="../assets/plugins/pace/pace.min.js"></script>
<script src="../assets/plugins/highlight/highlight.pack.js"></script>
<script src="../assets/plugins/datatables/datatables.min.js"></script>
<script src="../assets/js/main.min.js"></script>
<script src="../assets/js/custom.js"></script>
<script src="../assets/js/pages/datatables.js"></script>
<script src="../assets/js/pages/select2.js"></script>
<script src="../assets/plugins/select2/js/select2.full.min.js"></script>