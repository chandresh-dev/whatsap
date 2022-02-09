<?php
require('../helper/function.php');

$auth = new Auth($db);
$u = new Home($db);
//$contact = new Contact($db);

//echo $auth;
if (!$auth->isLogin()) {
    header('Location: login.php');
    exit;
}

if (isset($_POST['submit'])) {
 $u->addAssign($_POST);
}

if (isset($_POST['update'])) {
 $u->updateAssign($_POST);
}
if (isset($_POST['delete'])) {
    $u->deleteAssign($_POST['id']);
}
// if (isset($_POST['export'])) {
//     $contact->export($_POST);
// }
// if (isset($_POST['delete'])) {
//     $contact->deleteNumber($_POST['id']);
// }

// if (isset($_POST['import'])) {
//     $contact->import($_FILES['fileexcel']);
// }
// if (isset($_POST['generate'])) {
//     $contact->getContacts($_POST['nomor']);
// }
// if (isset($_POST['deleteAll'])) {
//     $contact->deleteAll();
// }
require('templates/header.php');
require('templates/sidebar.php');
?>

<div class="app-content">
    <link href="../assets/plugins/datatables/datatables.min.css" rel="stylesheet">
    <link href="../assets/plugins/select2/css/select2.css" rel="stylesheet">
    <div class="content-wrapper">
        <div class="container">
            <h2 class="my">Assign's List</h2>
            <?php if (isset($_SESSION['alert'])) : ?>
                <div class="alert alert-<?= $_SESSION['alert']['color'] ?> alert-style-light mt-5" role="alert">
                    <?= $_SESSION['alert']['msg'] ?>
                </div>
                <?php unset($_SESSION['alert']) ?>
            <?php endif; ?>
      <div class="card-header d-flex justify-content-between">
          <!--       <form action="" method="POST">

                    <button type="submit" name="deleteAll" class="btn btn-danger "><i class="material-icons-outlined">contacts</i>Delete All</button>
                </form>
                <button type="button" class="btn btn-primary " data-bs-toggle="modal" data-bs-target="#selectNomor"><i class="material-icons-outlined">contacts</i>Import From WhatsApp</button>
                <div class="d-flex justify-content-right">
                    <form action="" method="POST">
                        <button type="submit" name="export" class="btn btn-warning "><i class="material-icons">download</i>Export (xlsx)</button>
                    </form>
                    <button type="button" class="btn btn-primary mx-2" data-bs-toggle="modal" data-bs-target="#importExcel"><i class="material-icons-outlined">upload</i>Import (xlsx)</button> -->
                    <button type="button" class="btn btn-primary " data-bs-toggle="modal" data-bs-target="#addAssign"><i class="material-icons-outlined">add</i>Add Assign</button>
                </div>
            </div> 
            <div class="row">
                <div class="col">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between">
                            <h5 class="card-title">Assign's List</h5>
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
                                        <th>Packeage</th>
                                        <th>Amount</th>
<th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($u->getAllAssign() as $c) : ?>
                                        <tr>
                                           
                                      
                                             <td><?=  $c['user_ids'] ?></td>
                                            <td><?= $c['aa_pkg'] ?></td>
                                      
                                              <td>
<?= $c['aa_amt'] ?>


                                              </td>   <td>      <form action="" method="POST">
                                                    <input type="hidden" name="id" value="<?= $c['aa_id']; ?>">
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





<div class="modal fade" id="editAssign" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Edit Assign</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="" method="POST" enctype="multipart/form-data">
                  
                      <input type="hidden" name="uid" class="form-control" id="uid" required>
                    <label for="Assignname" class="form-label">Assign Name</label>
                    <input type="text" name="uname" class="form-control" id="uname" required>
                       <label for="email" class="form-label">Email</label>
                    <input type="text" name="emailid" class="form-control" id="emailid" required>
                    <label for="nomor" class="form-label">Mobile</label>
                    <input type="text" name="mnum" class="form-control" id="mnum" required>
                <!--     <label for="country" class="form-label">Country</label>
        
                    <select name="country" id="country" class="form-control" tabindex="-1" style="display: none; width: 100%" >
                        <?php foreach ($u->allCountry() as $d) : ?>
                            <option value="<?= $d['id']; ?>"><?= $d['country_name']; ?></option>
                        <?php endforeach; ?>
                    </select> -->
                  
              <!-- <?php if($values1->id == $billing[0]->city) echo 'selected'; ?> -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" name="update" class="btn btn-primary">Update Assign</button>
                </form>
            </div>
        </div>
    </div>
</div>



<!-- Modal -->
<div class="modal fade" id="addAssign" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Add New Assign</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="" method="POST" enctype="multipart/form-data">
                  
                       <label for="users" class="form-label">Assign User Name</label>
        
                    <select name="uname" id="uname" class="form-control" tabindex="-1" style="display: none; width: 100%" >
                        <?php foreach ($u->getAllUser() as $d) : ?>
                            <option value="<?php echo $d['u_name']; ?>" ><?php echo $d['u_name']; ?></option>
                        <?php endforeach; ?>
                    </select>
                    <label for="Assignname" class="form-label">Assign Package</label>
                    <br>
                     
  <input type="radio" id="1" name="pkg" value="1 month"
         checked  required>
  <label for="1">1 month</label>

  <input type="radio" id="2" name="pkg" value="6 month">
  <label for="2">6 month</label>

  <input type="radio" id="3" name="pkg" value="1 year">
  <label for="3">1 year</label>
    <input type="radio" id="4" name="pkg" value="Lifetime">
  <label for="4">Lifetime</label>
<br>
                    <label for="amount" class="form-label">Amount</label>
                    <input type="number" name="amount" class="form-control" id="amount" required>
                                            
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" name="submit" class="btn btn-primary">Add Assign</button>
                </form>
            </div>
        </div>
    </div>
</div>




<!-- <div class="modal fade" id="importExcel" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
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
</div> -->
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
<script type="text/javascript">
    $(document).on("click", ".modal-trigger", function () {
  var uid = $(this).data('uid');
  var uname = $(this).data('uname');
  var uemail = $(this).data('uemail');
  var mnum = $(this).data('mnum');
   var country = $(this).attr('country');
  $(".modal-content #uid").val(uid);
  $("#uname").val(uname);
  $("#emailid").val(uemail);
    $("#mnum").val(mnum);
$("#country").val(country);

});

</script>
<!--  <script type="text/javascript">
$(document).on('click','.status_checks',function(){
      var status = ($(this).hasClass("btn-success")) ? '0' : '1';
      var msg = (status=='0')? 'Deactivate' : 'Activate';
      if(confirm("Are you sure to "+ msg)){
        var current_element = $(this);
          var id = $(current_element).attr('data');
  
      url = "http://localhost/whatsap/php/Home.php/Assignactivation()";
     
        $.ajax({
          type:"POST",
          url: url,
          data: {id:id,status:status},
          success: function(data)
          {   
            
            location.reload();
          }
        });
      }      
    });
</script> -->

<script type="text/javascript">
$(document).on('click','.status_checks',function(){
      var status = ($(this).hasClass("success")) ? '0' : '1';
      var msg = (status=='0')? 'Deactivate' : 'Activate';
      if(confirm("Are you sure to "+ msg)){
        var current_element = $(this);
        url = "../php/Assign.php";
        $.ajax({
          type:"POST",
          url: url,
          data: {id:$(current_element).attr('data'),status:status},
          success: function(data)
          {   
           // alert(data);
            location.reload();
          }
        });
      }      
    });

$(document).on('click','.status_yesno',function(){
      var status = ($(this).hasClass("success")) ? '0' : '1';
      var msg = (status=='0')? 'No' : 'Yes';
      if(confirm("Are you sure to "+ msg)){
        var current_element = $(this);
        url = "../php/Yesno.php";
        $.ajax({
          type:"POST",
          url: url,
          data: {id:$(current_element).attr('data'),status:status},
          success: function(data)
          {   
           // alert(data);
            location.reload();
          }
        });
      }      
    });



</script>
<!-- <script>
    function scan(nomor) {
        window.location = "scan.php?nomor=" + nomor
    }
</script> -->