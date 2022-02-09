<?php
require('../helper/function.php');

$auth = new Auth($db);
$dev = new Home($db);
$ar = new Autoresponder($db);
if (!$auth->isLogin()) {
    header('Location: login.php');
    exit;
}

if (isset($_POST['submit'])) {
    $ar->addAutoRespond($_POST);
}
if (isset($_POST['delete'])) {
    $ar->deleteAutoRespond($_POST['id']);
}
require('templates/header.php');
require('templates/sidebar.php');
?>

<div class="app-content">
    <link href="../assets/plugins/datatables/datatables.min.css" rel="stylesheet">
    <link href="../assets/plugins/select2/css/select2.css" rel="stylesheet">
    <div class="content-wrapper">
        <div class="container">
            <h2 class="my">Auto responder</h2>
            <?php if (isset($_SESSION['alert'])) : ?>
                <div class="alert alert-<?= $_SESSION['alert']['color'] ?> alert-style-light mt-5" role="alert">
                    <?= $_SESSION['alert']['msg'] ?>
                </div>
                <?php unset($_SESSION['alert']) ?>
            <?php endif; ?>
            <div class="row">
                <div class="col">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between">
                            <h5 class="card-title">Lists auto respond</h5>
                            <button type="button" class="btn btn-primary " data-bs-toggle="modal" data-bs-target="#addAutoRespond"><i class="material-icons-outlined">add</i>Add Rule</button>
                        </div>
                        <div class="card-body">
                            <table id="datatable1" class="display" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>Sender</th>
                                        <th>Keyword</th>
                                        <th>Media</th>
                                        <th>Response</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($ar->byUser() as $a) : ?>

                                        <tr>

                                            <td><?= $a['nomor']; ?> </td>
                                            <td><?= $a['keyword']; ?> </td>
                                            <td><?= $a['media']; ?> </td>
                                            <td><?= $a['response']; ?> </td>
                                            <td>
                                                <form action="" method="POST">
                                                    <input type="hidden" name="id" value="<?= $a['id']; ?>">
                                                    <button type="submit" name="delete" class="btn btn-danger btn-sm"><i class="material-icons">delete_outline</i></button>
                                                </form>

                                            </td>
                                        </tr>
                                    <?php endforeach; ?>

                                </tbody>

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
<div class="modal fade" id="addAutoRespond" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Add Auto Reply Rule</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="" method="POST" enctype="multipart/form-data">
                    <select name="nomor" class="js-states form-control" tabindex="-1" style="display: none; width: 100%" required>
                        <?php foreach ($dev->allDevices() as $d) : ?>
                            <option value="<?= $d['nomor'] ?>"><?= $d['nomor'] ?></option>
                        <?php endforeach; ?>
                    </select>
                    <label for="keyword" class="form-label">Keyword</label>
                    <input type="text" name="keyword" class="form-control" id="keyword" required>
                    <label for="responmedia" class="form-label">Response Media</label>
                    <input type="file" name="media" class="form-control" id="responmedia">
                    <div id="emailHelp" class="form-text">Optional, *Pdf,doc,jpg,png</div>
                    <label for="Respond pesan" class="form-label">Response Message</label>
                    <input type="text" name="respond" class="form-control" id="Respond pesan" required>
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
<script>
    function scan(nomor) {
        window.location = "scan.php?nomor=" + nomor
    }
</script>