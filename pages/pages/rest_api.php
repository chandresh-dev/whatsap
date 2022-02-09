<?php
require('../helper/function.php');

$auth = new Auth($db);
$home = new Home($db);

if (!$auth->isLogin()) {
    header('Location: login.php');
    exit;
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
            <h2 class="my-5">Rest API</h2>
            <?php if (isset($_SESSION['alert'])) : ?>
                <div class="alert alert-<?= $_SESSION['alert']['color'] ?> alert-style-light mt-5" role="alert">
                    <?= $_SESSION['alert']['msg'] ?>
                </div>
                <?php unset($_SESSION['alert']) ?>
            <?php endif; ?>
            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th scope="col">Method</th>
                                    <th scope="col">POST</th>
                                </tr>
                                <tr>
                                    <th scope="col">Type</th>
                                    <th scope="col">JSON / Urlencoded</th>
                                </tr>
                            </thead>

                        </table>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-body">
                            <p class="card-description">Rest Api </p>
                            <div class="example-container">
                                <div class="example-content">
                                    <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link active" id="pills-home-tab" data-bs-toggle="pill" data-bs-target="#SendMsg" type="button" role="tab" aria-controls="pills-home" aria-selected="true">Text Message</button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link" id="pills-profile-tab" data-bs-toggle="pill" data-bs-target="#SendImg" type="button" role="tab" aria-controls="pills-profile" aria-selected="false">
                                                Message with Image
                                            </button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link" id="pills-contact-tab" data-bs-toggle="pill" data-bs-target="#SendButton" type="button" role="tab" aria-controls="pills-contact" aria-selected="false">Message with Button </button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link" id="pills-contact-tab" data-bs-toggle="pill" data-bs-target="#SendDocument" type="button" role="tab" aria-controls="pills-contact" aria-selected="false">Message With Document </button>
                                        </li>
                                    </ul>
                                    <div class="tab-content" id="pills-tabContent">
                                        <div class="tab-pane fade active show" id="SendMsg" role=" tabpanel" aria-labelledby="pills-home-tab">
                                            <div class="alert alert-secondary">ENDPOINT : <?= URL_WA ?>send-message</div>
                                            <table class="table">
                                                <thead>
                                                    <th scope="col">Parameter</th>
                                                    <th scope="col">Value</th>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>api_key</td>
                                                        <td><span class="badge badge-secondary"><?= $_SESSION['login']['api_key'] ?></span></td>
                                                    </tr>
                                                    <tr>

                                                        <td>sender</td>
                                                        <td>Sender/Device Number ( Use Country Code with out + Sign ) </td>

                                                    </tr>
                                                    <tr>

                                                        <td>number</td>
                                                        <td>Destination Number</td>

                                                    </tr>
                                                    <tr>
                                                        <th>message</th>
                                                        <td>Message Text</td>

                                                    </tr>
                                                </tbody>
                                            </table>

                                        </div>
                                        <div class="tab-pane fade" id="SendImg" role="tabpanel" aria-labelledby="pills-profile-tab">
                                            <div class="alert alert-secondary">ENDPOINT : <?= URL_WA ?>send-image</div>
                                            <table class="table">
                                                <thead>
                                                    <th scope="col">Parameter</th>
                                                    <th scope="col">Value</th>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>api_key</td>
                                                        <td><span class="badge badge-secondary"><?= $_SESSION['login']['api_key'] ?></span></td>
                                                    </tr>
                                                    <tr>

                                                        <td>sender</td>
                                                        <td>Sender/Device Number ( Use Country Code with out + Sign )</td>

                                                    </tr>
                                                    <tr>

                                                        <td>number</td>
                                                        <td>Destination Number</td>

                                                    </tr>
                                                    <tr>
                                                        <th>message</th>
                                                        <td>Message Text</td>

                                                    </tr>
                                                    <tr>
                                                        <th>url</th>
                                                        <td>Link Image , (  JPG,PNG,JPEG )</td>

                                                    </tr>
                                                </tbody>
                                            </table>

                                        </div>
                                        <div class="tab-pane fade" id="SendButton" role="tabpanel" aria-labelledby="pills-contact-tab">
                                            <div class="alert alert-secondary">ENDPOINT : <?= URL_WA ?>send-button</div>
                                            <table class="table">
                                                <thead>
                                                    <th scope="col">Parameter</th>
                                                    <th scope="col">Value</th>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>api_key</td>
                                                        <td><span class="badge badge-secondary"><?= $_SESSION['login']['api_key'] ?></span></td>
                                                    </tr>
                                                    <tr>

                                                        <td>sender</td>
                                                        <td>Sender/Device Number ( Use Country Code with out + Sign ) </td>

                                                    </tr>
                                                    <tr>

                                                        <td>number</td>
                                                        <td>Destination Number</td>

                                                    </tr>
                                                    <tr>
                                                        <th>message</th>
                                                        <td>Message Text</td>

                                                    </tr>
                                                    <tr>
                                                        <th>footer</th>
                                                        <td>Bottom Of Message</td>

                                                    </tr>
                                                    <tr>
                                                        <th>button1</th>
                                                        <td>Button1 Text</td>

                                                    </tr>
                                                    <tr>
                                                        <th>button2</th>
                                                        <td>Button2 Text</td>

                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="tab-pane fade" id="SendDocument" role="tabpanel" aria-labelledby="pills-contact-tab">
                                            <div class="alert alert-secondary">ENDPOINT : <?= URL_WA ?>send-document</div>
                                            <table class="table">
                                                <thead>
                                                    <th scope="col">Parameter</th>
                                                    <th scope="col">Value</th>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>api_key</td>
                                                        <td><span class="badge badge-secondary"><?= $_SESSION['login']['api_key'] ?></span></td>
                                                    </tr>
                                                    <tr>

                                                        <td>sender</td>
                                                        <td>Sender/Device Number ( Use Country Code with out + Sign )</td>

                                                    </tr>
                                                    <tr>

                                                        <td>number</td>
                                                        <td>Destination Number</td>

                                                    </tr>
                                                    <tr>
                                                        <th>url</th>
                                                        <td>Link Document(PDF, DOC,DOCX ,XLS and XLSX )</td>

                                                    </tr>

                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <div class="alert alert-secondary">


                                    <!-- HTML generated using highlightmycode -->
                                    <div style="background: #ffffff; overflow:auto;width:auto;border:solid gray;border-width:.1em .1em .1em .8em;padding:.2em .6em;">
                                        <pre style="margin: 0; line-height: 125%"><span style="color: #808080">// respon sukses</span>
{
  <span style="background-color: #fff0f0">&quot;status&quot;</span> <span style="color: #303030">:</span> <span style="color: #008000; font-weight: bold">true</span>,
  <span style="background-color: #fff0f0">&quot;msg&quot;</span> <span style="color: #303030">:</span> <span style="background-color: #fff0f0">&quot;Message Sent Successfully&quot;</span>
}

<span style="color: #808080">// Response Failed</span>
{
  <span style="background-color: #fff0f0">&quot;status&quot;</span> <span style="color: #303030">:</span> <span style="color: #008000; font-weight: bold">false</span>,
  <span style="background-color: #fff0f0">&quot;msg&quot;</span> <span style="color: #303030">:</span> <span style="background-color: #fff0f0">&quot;Message&quot;</span>
}

<span style="color: #808080">// Message If Failed</span>
 <span style="color: #808080">// &quot;Incorrect Parameter&quot;</span>
 <span style="color: #808080">// &quot;Wrong Api key&quot;</span>
<span style="color: #808080">// &quot;Please Scan QR Code Before Using&quot;</span>
</pre>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<?php
require('templates/footer.php');
?>