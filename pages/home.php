<?php
require('../helper/function.php');

$auth = new Auth($db);
$home = new Home($db);
if (!$auth->isLogin()) {
    header('Location: login.php');
    exit;
}

$validity = true;
if(!$auth->validity()){
    $validity = false;
}

if (isset($_POST['submit'])) {
    $home->addDevice($_POST);
}
if (isset($_POST['delete'])) {
    $home->deleteDevice($_POST['deviceId']);
}
require('templates/header.php');
require('templates/sidebar.php');
?>

<div class="app-content">
    <div class="content-wrapper">
        <div class="container">
            <h2 class="my-5">Dashboard1</h2>
            <?php if (isset($_SESSION['alert'])) : ?>
                <div class="alert alert-<?= $_SESSION['alert']['color'] ?> alert-style-light mt-5" role="alert">
                    <?= $_SESSION['alert']['msg'] ?>
                </div>
                <?php unset($_SESSION['alert']) ?>
            <?php endif; ?>
            <div class="row">
                <div class="col-xl-4">
                    <div class="card widget widget-stats">
                        <div class="card-body">
                            <div class="widget-stats-container d-flex">
                                <div class="widget-stats-icon widget-stats-icon-primary">
                                    <i class="material-icons-outlined">Contacts</i>
                                </div>
                                <div class="widget-stats-content flex-fill">
                                    <span class="widget-stats-title">Total Number</span>
                                    <span class="widget-stats-amount"><?= $home->getContact() ?></span>

                                </div>

                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4">
                    <div class="card widget widget-stats">
                        <div class="card-body">
                            <div class="widget-stats-container d-flex">
                                <div class="widget-stats-icon widget-stats-icon-warning">
                                    <i class="material-icons-outlined">message</i>
                                </div>
                                <div class="widget-stats-content flex-fill">
                                    <span class="widget-stats-title">Bulk Message</span>

                                    <span class="widget-stats-info"><?= $home->getTotalBlast('Success') ?> Success</span>
                                    <span class="widget-stats-info"><?= $home->getTotalBlast('Failed') ?> Failed</span>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4">
                    <div class="card widget widget-stats">
                        <div class="card-body">
                            <div class="widget-stats-container d-flex">
                                <div class="widget-stats-icon widget-stats-icon-danger">
                                    <i class="material-icons-outlined">schedule</i>
                                </div>
                                <div class="widget-stats-content flex-fill">
                                    <span class="widget-stats-title">Schedule Message</span>

                                    <span class="widget-stats-info"><?= $home->getTotalSchedule('Success') ?> Success</span>
                                    <span class="widget-stats-info"><?= $home->getTotalSchedule('Failed') ?> Failed</span>
                                    <span class="widget-stats-info"><?= $home->getTotalSchedule('Pending') ?> Pending</span>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php if($validity){ ?>
            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-body">
                            <h5>List Devices</h5>
                            <?php if($_SESSION['device'] > sizeof($home->allDevices())){  ?>
                            <button type="button" class="btn btn-primary " data-bs-toggle="modal" data-bs-target="#addDevice"><i class="material-icons">add</i>Add WhatsApp Profile </button>
                            <?php } ?>
                            <table class="table table-striped">
                                <thead>
                                    <th>Number</th>
                                    <th>link Webhook</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </thead>
                                <tbody>
                                    <?php foreach ($home->allDevices() as $d) : ?>
                                        <tr>

                                            <td><?= $d['nomor'] ?></td>
                                            <td><?= $d['link_webhook'] ?></td>
                                            <td><span class="badge badge-<?= $d['connect'] ? 'success' : 'danger' ?>"><?= $d['connect'] ? 'Connected' : 'Disconnect' ?></span></td>
                                            <td>
                                                <button type="button" class="btn btn-warning " onclick="scan('<?= $d['nomor']; ?>')" style="font-size: 10px;"><i class="material-icons">qr_code</i></button>
                                                <form action="" method="POST">
                                                    <input name="deviceId" type="hidden" value="<?= $d['id'] ?>">
                                                    <button type="delete" name="delete" class="btn btn-danger "><i class="material-icons">delete_outline</i></button>
                                                </form>

                                            </td>
                                        </tr>
                                    <?php endforeach ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <?php } ?>
        </div>
    </div>
</div>
</div>
<div class="modal fade" id="addDevice" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Add Device</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="" method="POST">
                    <label for="sender" class="form-label">Number</label>
                    <input type="number" name="sender" class="form-control" id="nomor" placeholder="62xxx" required>
                    <p class="text-small text-danger">*Mobile Number With Country Code ( without + )</p>
                    <label for="urlwebhook" class="form-label">Link webhook</label>
                    <input type="text" name="urlwebhook" class="form-control" id="urlwebhook">
                    <p class="text-small text-danger">*Optional</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" name="submit" class="btn btn-primary">Add Profile</button>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    function scan(nomor) {
        window.location = "scan.php?nomor=" + nomor
    }
</script>
<?php
require('templates/footer.php');
?>