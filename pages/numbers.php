<?php
require('../helper/function.php');

$auth = new Auth($db);
$contact = new Contact($db);

if (!$auth->isLogin()) {
    header('Location: login.php');
    exit;
}

if (isset($_POST['submit'])) {

    $contact->addNumber($_POST);
}

if (isset($_POST['export'])) {
    $contact->export($_POST);
}
if (isset($_POST['delete'])) {
    $contact->deleteNumber($_POST['id']);
}

if (isset($_POST['import'])) {
    $contact->import($_FILES['fileexcel']);
}
if (isset($_POST['generate'])) {
    $contact->getContacts($_POST['nomor']);
}
if (isset($_POST['deleteAll'])) {
    $contact->deleteAll();
}
require('templates/header.php');
require('templates/sidebar.php');
?>

<div class="app-content">
    <link href="../assets/plugins/datatables/datatables.min.css" rel="stylesheet">
    <link href="../assets/plugins/select2/css/select2.css" rel="stylesheet">
    <div class="content-wrapper">
        <div class="container">
            <h2 class="my">Number's List</h2>
            <?php if (isset($_SESSION['alert'])) : ?>
                <div class="alert alert-<?= $_SESSION['alert']['color'] ?> alert-style-light mt-5" role="alert">
                    <?= $_SESSION['alert']['msg'] ?>
                </div>
                <?php unset($_SESSION['alert']) ?>
            <?php endif; ?>

            <div class="card-header d-flex justify-content-between">
                <form action="" method="POST">

                    <button type="submit" name="deleteAll" class="btn btn-danger "><i class="material-icons-outlined">contacts</i>Delete All</button>
                </form>
                <button type="button" class="btn btn-primary " data-bs-toggle="modal" data-bs-target="#selectNomor"><i class="material-icons-outlined">contacts</i>Import From WhatsApp</button>
                <div class="d-flex justify-content-right">
                    <form action="" method="POST">
                        <button type="submit" name="export" class="btn btn-warning "><i class="material-icons">download</i>Export (xlsx)</button>
                    </form>
                    <button type="button" class="btn btn-primary mx-2" data-bs-toggle="modal" data-bs-target="#importExcel"><i class="material-icons-outlined">upload</i>Import (xlsx)</button>
                    <button type="button" class="btn btn-primary " data-bs-toggle="modal" data-bs-target="#addNumber"><i class="material-icons-outlined">add</i>Add Number</button>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between">
                            <h5 class="card-title">Number List</h5>
                            <!-- <button type="button" class="btn btn-danger " data-bs-toggle="modal" data-bs-target="#selectNomor"><i class="material-icons-outlined">contacts</i>Hapus semua</button>
                            <button type="button" class="btn btn-primary " data-bs-toggle="modal" data-bs-target="#selectNomor"><i class="material-icons-outlined">contacts</i>Generate Kontak</button>
                            <div class="d-flex justify-content-right">
                                <form action="" method="POST">
                                    <button type="submit" name="export" class="btn btn-warning "><i class="material-icons">download</i>Export (xlsx)</button>
                                </form>
                                <button type="button" class="btn btn-primary mx-2" data-bs-toggle="modal" data-bs-target="#importExcel"><i class="material-icons-outlined">upload</i>Import (xlsx)</button>
                                <button type="button" class="btn btn-primary " data-bs-toggle="modal" data-bs-target="#addNumber"><i class="material-icons-outlined">add</i>Tambah</button>
                            </div> -->
                        </div>
                        <div class="card-body">
                            <table id="datatable1" class="display" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Number</th>
                                        <th>Message Default</th>
                                        <th>Action</th>

                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($contact->getAllNumbersByUser() as $c) : ?>
                                        <tr>
                                            <td><?= $c['nama'] ?></td>
                                            <td><?= $c['nomor'] ?></td>
                                            <td><?= $c['pesan'] ?></td>
                                            <td>
                                                <form action="" method="POST">
                                                    <input type="hidden" name="id" value="<?= $c['id']; ?>">
                                                    <button type="submit" name="delete" class="btn btn-danger btn-sm"><i class="material-icons">delete_outline</i></button>
                                                </form>
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
<div class="modal fade" id="addNumber" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Add New Number</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="" method="POST" enctype="multipart/form-data">
                    <label for="keyword" class="form-label">Name</label>
                    <input type="text" name="name" class="form-control" id="nama" required>
                    <label for="nomor" class="form-label">Number</label>
                    <input type="number" name="number" class="form-control" id="nomor" required>
                    <label for="message" class="form-label">Default Message</label>
                    <textarea name="message" class="form-control" id="" cols="30" rows="10" required></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" name="submit" class="btn btn-primary">Add Number</button>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="importExcel" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Add Number</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">

                <form action="" method="POST" enctype="multipart/form-data">

                    <p>Format must be xlsx and must have 3 Coloumn(Name, Number ,and Message)</p>
                    <label for="fileexcel" class="form-label">File xlsx</label>
                    <input type="file" name="fileexcel" class="form-control" id="fileexcel" required>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" name="import" class="btn btn-primary">Import</button>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="selectNomor" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Import Contact</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">

                <form action="" method="POST" enctype="multipart/form-data">

                    <p>Which number do you want to get a contact from ?</p>
                    <?php $c = new Home($db); ?>
                    <select name="nomor" id="" class="form-control" tabindex="-1" style="display: none; width: 100%">
                        <?php foreach ($c->allDevices() as $d) : ?>
                            <option value="<?= $d['nomor']; ?>"><?= $d['nomor']; ?></option>
                        <?php endforeach; ?>
                    </select>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" name="generate" class="btn btn-primary">Import Contact</button>
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