<?php
require_once '../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

session_start();
error_reporting(0);
include('includes/config.php');
if (strlen($_SESSION['alogin']) == 0) {
    header('location:index.php');
} else {

    if (isset($_REQUEST['unconfirm'])) {
        $aeid = intval($_GET['unconfirm']);
        $memstatus = 1;
        $sql = "UPDATE participants SET status=:status WHERE  id=:aeid";
        $query = $dbh->prepare($sql);
        $query->bindParam(':status', $memstatus, PDO::PARAM_STR);
        $query->bindParam(':aeid', $aeid, PDO::PARAM_STR);
        $query->execute();
        $msg = "Changes Successfully";
    }

    if (isset($_REQUEST['confirm'])) {
        $aeid = intval($_GET['confirm']);
        $memstatus = 0;
        $sql = "UPDATE participants SET status=:status WHERE  id=:aeid";
        $query = $dbh->prepare($sql);
        $query->bindParam(':status', $memstatus, PDO::PARAM_STR);
        $query->bindParam(':aeid', $aeid, PDO::PARAM_STR);
        $query->execute();
        $msg = "Changes Successfully";
    }

    if (isset($_GET['del'])) {
        $id = $_GET['del'];

        $sql = "delete from participants WHERE id=:id";
        $query = $dbh->prepare($sql);
        $query->bindParam(':id', $id, PDO::PARAM_STR);
        $query->execute();

        $msg = "Data Deleted successfully";
    }

    if (isset($_GET['edit'])) {
        $editid = $_GET['edit'];
    }



    if (isset($_POST['submit'])) {

        if ($_FILES["participantfile"]["name"] != '') {
            $allowedFileType = [
                'application/vnd.ms-excel',
                'text/xls',
                'text/xlsx',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
            ];
            if (in_array($_FILES["participantfile"]["type"], $allowedFileType)) {

                $allowed_extension = array('xls', 'xlsx', 'csv');
                $file_array = explode(".", $_FILES['participantfile']['name']);
                $file_extension = end($file_array);
                if ($file_extension == 'xlsx') {
                    $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
                } else if ($file_extension == 'xls') {
                    $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xls();
                } else if ($file_extension == 'csv') {
                    $reader = new \PhpOffice\PhpSpreadsheet\Reader\Csv();
                }
                echo "<div>" . $_FILES['participantfile']['tmp_name'] . "</div>";
                $spreadSheet = $reader->load($_FILES['participantfile']['tmp_name']);
                echo "<div>" . $_FILES['participantfile']['tmp_name'] . "</div>";
                $data = $spreadSheet->getActiveSheet()->toArray();

                $startRow = 1;
                $currentRow = 0;

                $sql = "DELETE FROM participants where eventId = " . $editid;
                $query = $dbh->prepare($sql);
                $query->execute();

                foreach ($data as $row) {
                    if ($currentRow >= $startRow) {
                        $sql = "INSERT INTO participants (eventId, discordName, fullName, paypalEmail, shippingAddress, shippingOptions, phoneNo, comments, registerDateTime, status, createdDateTime, updatedDateTime) 
                  VALUES ((:eventId), (:discordName), (:fullName), (:paypalEmail), (:shippingAddress), (:shippingOptions), (:phoneNo), (:comments), (:registerDateTime), 1, NOW(), NOW())";
                        $query = $dbh->prepare($sql);
                        $query->bindParam(':registerDateTime', $row[0], PDO::PARAM_STR);
                        $query->bindParam(':discordName', $row[1], PDO::PARAM_STR);
                        $query->bindParam(':fullName', $row[2], PDO::PARAM_STR);
                        $query->bindParam(':paypalEmail', $row[3], PDO::PARAM_STR);
                        $query->bindParam(':shippingAddress', $row[4], PDO::PARAM_STR);
                        $query->bindParam(':phoneNo', $row[5], PDO::PARAM_STR);
                        $query->bindParam(':shippingOptions', $row[7], PDO::PARAM_STR);
                        $query->bindParam(':comments', $row[8], PDO::PARAM_STR);
                        $query->bindParam(':eventId', $editid, PDO::PARAM_STR);
                        $query->execute();
                    }
                    $currentRow++;
                }
                $msg = 'Upload success!';
            } else {
                $error = "Invalid File Type. Upload Excel File.";
            }
        } else {
            $error = "Please Select File";
        }





        $eventName = $_POST['eventName'];
        $startDateTime = $_POST['startDateTime'];
        $endDateTime = $_POST['endDateTime'];
        $registerCount = $_POST['registerCount'];
        $winnerCount = $_POST['winnerCount'];
        $status = $_POST['status'];
        $idedit = $_POST['idedit'];

        if ($idedit != '') {
            $sql = "UPDATE events SET eventName=(:eventName), startDateTime=(:startDateTime), endDateTime=(:endDateTime), registerCount=(:registerCount), winnerCount=(:winnerCount), status=(:status), createdDateTime = now(), updatedDateTime = now() WHERE id=(:idedit)";
        } else {
            $sql = "INSERT INTO events (eventName, startDateTime, endDateTime, registerCount, winnerCount, status, createdDateTime, updatedDateTime) VALUES ((:eventName), (:startDateTime), (:endDateTime), (:registerCount), (:winnerCount), (:status), NOW(), NOW())";
        }
        $query = $dbh->prepare($sql);
        $query->bindParam(':eventName', $eventName, PDO::PARAM_STR);
        $query->bindParam(':startDateTime', $startDateTime, PDO::PARAM_STR);
        $query->bindParam(':endDateTime', $endDateTime, PDO::PARAM_STR);
        $query->bindParam(':registerCount', $registerCount, PDO::PARAM_STR);
        $query->bindParam(':winnerCount', $winnerCount, PDO::PARAM_STR);
        $query->bindParam(':status', $status, PDO::PARAM_STR);
        if ($idedit != '') {
            $query->bindParam(':idedit', $idedit, PDO::PARAM_STR);
            $query->execute();
            $msg = "Event Updated Successfully";
            $msg = $currentRow;
        } else {
            $query->execute();
            $msg = "New Event Added Successfully";
        }
    }
?>

    <!doctype html>
    <html lang="en" class="no-js">

    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1">
        <meta name="description" content="">
        <meta name="author" content="">
        <meta name="theme-color" content="#3e454c">

        <title>Edit User</title>

        <!-- Font awesome -->
        <link rel="stylesheet" href="css/font-awesome.min.css">
        <!-- Sandstone Bootstrap CSS -->
        <link rel="stylesheet" href="css/bootstrap.min.css">
        <!-- Bootstrap Datatables -->
        <link rel="stylesheet" href="css/dataTables.bootstrap.min.css">
        <!-- Bootstrap social button library -->
        <link rel="stylesheet" href="css/bootstrap-social.css">
        <!-- Bootstrap select -->
        <link rel="stylesheet" href="css/bootstrap-select.css">
        <!-- Bootstrap file input -->
        <link rel="stylesheet" href="css/fileinput.min.css">
        <!-- Awesome Bootstrap checkbox -->
        <link rel="stylesheet" href="css/awesome-bootstrap-checkbox.css">
        <!-- Admin Stye -->
        <link rel="stylesheet" href="css/style.css">

        <script type="text/javascript" src="../vendor/countries.js"></script>
        <style>
            .errorWrap {
                padding: 10px;
                margin: 0 0 20px 0;
                background: #dd3d36;
                color: #fff;
                -webkit-box-shadow: 0 1px 1px 0 rgba(0, 0, 0, .1);
                box-shadow: 0 1px 1px 0 rgba(0, 0, 0, .1);
            }

            .succWrap {
                padding: 10px;
                margin: 0 0 20px 0;
                background: #5cb85c;
                color: #fff;
                -webkit-box-shadow: 0 1px 1px 0 rgba(0, 0, 0, .1);
                box-shadow: 0 1px 1px 0 rgba(0, 0, 0, .1);
            }
        </style>
    </head>

    <body>
        <?php
        $sql = "SELECT * from events where id = :editid";
        $query = $dbh->prepare($sql);
        $query->bindParam(':editid', $editid, PDO::PARAM_INT);
        $query->execute();
        $result = $query->fetch(PDO::FETCH_OBJ);
        $cnt = 1;
        ?>
        <?php include('includes/header.php'); ?>
        <div class="ts-main-content">
            <?php include('includes/leftbar.php'); ?>
            <div class="content-wrapper">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-12">
                            <h3 class="page-title">Edit Events : <?php echo htmlentities($result->eventName); ?></h3>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="panel panel-default">
                                        <div class="panel-heading">Edit Info</div>
                                        <?php if ($error) { ?><div class="errorWrap"><strong>ERROR</strong>:<?php echo htmlentities($error); ?> </div><?php } else if ($msg) { ?><div class="succWrap"><strong>SUCCESS</strong>:<?php echo htmlentities($msg); ?> </div><?php } ?>

                                        <div class="panel-body">
                                            <form method="post" class="form-horizontal" enctype="multipart/form-data" name="imgform">
                                                <div class="form-group">
                                                    <label class="col-sm-2 control-label">eventName<span style="color:red">*</span></label>
                                                    <div class="col-sm-4">
                                                        <input type="text" name="eventName" class="form-control" required value="<?php echo htmlentities($result->eventName); ?>">
                                                    </div>
                                                    <label class="col-sm-2 control-label">startDateTime<span style="color:red">*</span></label>
                                                    <div class="col-sm-4">
                                                        <input type="text" name="startDateTime" class="form-control" required value="<?php echo htmlentities($result->startDateTime); ?>">
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label class="col-sm-2 control-label">endDateTime<span style="color:red">*</span></label>
                                                    <div class="col-sm-4">
                                                        <input type="text" name="endDateTime" class="form-control" required value="<?php echo htmlentities($result->startDateTime); ?>">
                                                    </div>
                                                    <label class="col-sm-2 control-label">registerCount<span style="color:red">*</span></label>
                                                    <div class="col-sm-4">
                                                        <input type="number" name="registerCount" class="form-control" required value="<?php echo htmlentities($result->registerCount); ?>">
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label class="col-sm-2 control-label">winnerCount<span style="color:red">*</span></label>
                                                    <div class="col-sm-4">
                                                        <input type="number" name="winnerCount" class="form-control" required value="<?php echo htmlentities($result->winnerCount); ?>">
                                                    </div>
                                                    <label class="col-sm-2 control-label">status<span style="color:red">*</span></label>
                                                    <div class="col-sm-4">
                                                        <select name="status" class="form-control" required>
                                                            <option value="<?php echo htmlentities($result->winnerCount); ?>">No Change</option>
                                                            <option value="1">Confirmed</option>
                                                            <option value="0">Un-Confirmed</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label class="col-sm-2 control-label">Participant CSV<span style="color:red">*</span></label>
                                                    <div class="col-sm-4">
                                                        <input type="file" name="participantfile" class="form-control">
                                                    </div>


                                                    <div class="form-group">
                                                        <div class="col-sm-8 col-sm-offset-2">
                                                            <img src="../images/<?php echo htmlentities($result->image); ?>" width="150px" />
                                                            <input type="hidden" name="image" value="<?php echo htmlentities($result->image); ?>">
                                                            <input type="hidden" name="idedit" value="<?php echo htmlentities($result->id); ?>">
                                                        </div>
                                                    </div>


                                                    <div class="form-group">
                                                        <div class="col-sm-8 col-sm-offset-2">
                                                            <button class="btn btn-primary" name="submit" type="submit">Save Changes</button>
                                                        </div>
                                                    </div>

                                            </form>
                                        </div>

                                        <!-- Zero Configuration Table -->
                                        <a href="edit-participants.php?eventid=<?php echo $editid; ?>">Create new participant</a>
                                        <a href="winners.php?eventid=<?php echo $editid; ?>">Generate winners</a>
                                        <div class="panel panel-default">
                                            <div class="panel-heading">List participants</div>
                                            <div class="panel-body">
                                                <?php if ($error) { ?>
                                                    <div class="errorWrap" id="msgshow"><?php echo htmlentities($error); ?> </div><?php
                                                                                                                                } else if ($msg) { ?>
                                                    <div class="succWrap" id="msgshow"><?php echo htmlentities($msg); ?> </div><?php
                                                                                                                                } ?>
                                                <table id="zctb" class="display table table-striped table-bordered table-hover" cellspacing="0" width="100%">
                                                    <thead>
                                                        <tr>
                                                            <th>#</th>
                                                            <th>discordName</th>
                                                            <th>fullName</th>
                                                            <th>paypalEmail</th>
                                                            <th>shippingAddress</th>
                                                            <th>shippingOptions</th>
                                                            <th>phoneNo</th>
                                                            <th>comments</th>
                                                            <th>registerDateTime</th>
                                                            <th>status</th>
                                                            <th>Action</th>
                                                        </tr>
                                                    </thead>

                                                    <tbody>

                                                        <?php $sql = "SELECT * from  participants where eventId = " . $editid;
                                                        $query = $dbh->prepare($sql);
                                                        $query->execute();
                                                        $results = $query->fetchAll(PDO::FETCH_OBJ);
                                                        $cnt = 1;
                                                        if ($query->rowCount() > 0) {
                                                            foreach ($results as $result) { ?>
                                                                <tr>
                                                                    <td><?php echo htmlentities($cnt); ?></td>
                                                                    <td><?php echo htmlentities($result->discordName); ?></td>
                                                                    <td><?php echo htmlentities($result->fullName); ?></td>
                                                                    <td><?php echo htmlentities($result->paypalEmail); ?></td>
                                                                    <td><?php echo htmlentities($result->shippingAddress); ?></td>
                                                                    <td><?php echo htmlentities($result->shippingOptions); ?></td>
                                                                    <td><?php echo htmlentities($result->phoneNo); ?></td>
                                                                    <td><?php echo htmlentities($result->comments); ?></td>
                                                                    <td><?php echo htmlentities($result->registerDateTime); ?></td>
                                                                    <td>
                                                                        <?php if ($result->status == 1) { ?>
                                                                            <a href="edit-events.php?confirm=<?php echo htmlentities($result->id); ?>&edit=<?php echo $editid; ?>">Active <i class="fa fa-check-circle"></i></a>
                                                                        <?php } else { ?>
                                                                            <a href="edit-events.php?unconfirm=<?php echo htmlentities($result->id); ?>&edit=<?php echo $editid; ?>">Inactive <i class="fa fa-times-circle"></i></a>
                                                                        <?php } ?>
                                                                    </td>
                                                                    <td>
                                                                        <a href="edit-participants.php?edit=<?php echo $result->id; ?>&eventid=<?php echo $editid; ?>">&nbsp; <i class="fa fa-pencil"></i></a>&nbsp;&nbsp;
                                                                        <a href="edit-events.php?del=<?php echo $result->id; ?>&edit=<?php echo $editid; ?>" onclick="return confirm('Do you want to Delete');"><i class="fa fa-trash" style="color:red"></i></a>&nbsp;&nbsp;
                                                                    </td>
                                                                </tr>
                                                        <?php $cnt = $cnt + 1;
                                                            }
                                                        } ?>
                                                    </tbody>
                                                </table>
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



        </div>
        </div>
        </div>

        <!-- Loading Scripts -->
        <script src="js/jquery.min.js"></script>
        <script src="js/bootstrap-select.min.js"></script>
        <script src="js/bootstrap.min.js"></script>
        <script src="js/jquery.dataTables.min.js"></script>
        <script src="js/dataTables.bootstrap.min.js"></script>
        <script src="js/Chart.min.js"></script>
        <script src="js/fileinput.js"></script>
        <script src="js/chartData.js"></script>
        <script src="js/main.js"></script>
        <script type="text/javascript">
            $(document).ready(function() {
                setTimeout(function() {
                    $('.succWrap').slideUp("slow");
                }, 3000);
            });
        </script>

    </body>

    </html>
<?php } ?>