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

    if (isset($_REQUEST['uploadpage'])) {
        $uploadpage = $_REQUEST['uploadpage'];
        switch ($uploadpage) {
            case 'verifiedlist':
                $tablename = 'verifiedmembers';
                $titlename = 'Verified Users';
                $columnnames = array("name" => "0", "discordId" => "2");
                $startRow = 0;
                break;
            case 'blacklist':
                $tablename = 'blacklistusers';
                $titlename = 'Blacklist Users';
                $createnew = 'Create new blacklist user';
                $columnnames = array("name" => "0", "email" => "1", "platform" => "2", "username" => "3", "description" => "4", "phoneNo" => "5");
                $startRow = 3;
                break;
            case 'participants':
                $tablename = 'participants';
                $titlename = 'Participants';
                $createnew = 'Create new participant';
                $columnnames = array("discordName" => "1", "fullName" => "2", "paypalEmail" => "3", "shippingAddress" => "4", "shippingOptions" => "7", "phoneNo" => "5", "comments" => "8", "registerDateTime" => "0");
                $startRow = 1;

                break;
            default:
                echo 'Invalid upload page selected.';
                return;
        }
    } else {
        echo 'Invalid upload page selected.';
        return;
    }


    if (isset($_POST["import"])) {
        if ($_FILES["file"]["name"] != '') {
            $allowedFileType = [
                'application/vnd.ms-excel',
                'text/xls',
                'text/xlsx',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
            ];
            if (in_array($_FILES["file"]["type"], $allowedFileType)) {

                $allowed_extension = array('xls', 'xlsx', 'csv');
                $file_array = explode(".", $_FILES['file']['name']);
                $file_extension = end($file_array);
                if ($file_extension == 'xlsx') {
                    $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
                } else if ($file_extension == 'xls') {
                    $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xls();
                } else if ($file_extension == 'csv') {
                    $reader = new \PhpOffice\PhpSpreadsheet\Reader\Csv();
                }

                $spreadSheet = $reader->load($_FILES['file']['tmp_name']);

                $data = $spreadSheet->getActiveSheet()->toArray();

                $currentRow = 0;

                $sql = "DELETE FROM " . $tablename;
                if (isset($_REQUEST["eventid"])) {
                    $sql .= " WHERE eventid = " . $_REQUEST["eventid"];
                }

                $query = $dbh->prepare($sql);
                $query->execute() or die(print_r($query->errorInfo(), true));
                foreach ($data as $row) {
                    if ($currentRow >= $startRow) {
                        $sql = "INSERT INTO " . $tablename . " (";
                        foreach ($columnnames as $columnname => $columnvalue) {
                            $sql .= $columnname . ", ";
                        }
                        $sql .= "createdDateTime, updatedDateTime";
                        if (isset($_REQUEST["eventid"])) {
                            $sql .= ", eventId";
                        }
                        $sql .= ") ";
                        $sql .= "VALUES (";
                        foreach ($columnnames as $columnname => $columnvalue) {
                            $sql .= "(:" . $columnname . "), ";
                        }
                        $sql .= "NOW(), NOW()";
                        if (isset($_REQUEST["eventid"])) {
                            $sql .= ", " . $_REQUEST["eventid"];
                        }
                        $sql .= ")";
                        $query = $dbh->prepare($sql);
                        foreach ($columnnames as $columnname => $columnvalue) {
                            $sql .= "(:" . $columnname . "), ";
                            $query->bindParam(':' . $columnname, $row[$columnvalue], PDO::PARAM_STR);
                        }
                        $query->execute() or die(print_r($query->errorInfo(), true));
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
    }
?>

    <!-- The View -->
    <!doctype html>
    <html lang="en" class="no-js">

    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1">
        <meta name="description" content="">
        <meta name="author" content="">
        <meta name="theme-color" content="#3e454c">

        <title>Manage <?php echo $titlename; ?></title>

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
        <?php include('includes/header.php'); ?>

        <div class="ts-main-content">
            <?php include('includes/leftbar.php'); ?>
            <div class="content-wrapper">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-12">
                            <h2 class="page-title">Manage <?php echo $titlename; ?></h2>

                            <!-- Zero Configuration Table -->
                            <div class="panel panel-default">
                                <div class="panel-heading">List <?php echo $titlename; ?></div>
                                <div class="panel-body">
                                    <?php if ($error) { ?>
                                        <div class="errorWrap" id="msgshow"><?php echo htmlentities($error); ?> </div>
                                    <?php } else if ($msg) { ?>
                                        <div class="succWrap" id="msgshow"><?php echo htmlentities($msg); ?> </div>
                                    <?php } ?>
                                    <div class="outer-container">
                                        <form action="" method="post" name="frmExcelImport" id="frmExcelImport" enctype="multipart/form-data">
                                            <div>
                                                <label>Choose Excel File</label>
                                                <input type="file" name="file" id="file">
                                                <input type="hidden" id="uploadpage" name="uploadpage" value=<?php echo $uploadpage; ?>>
                                                <?php
                                                if (in_array($uploadpage, array('participants', 'winners'))) {
                                                    $sql = "SELECT * from events";
                                                    $query = $dbh->prepare($sql);
                                                    $query->execute() or die(print_r($query->errorInfo(), true));
                                                    $eventresults = $query->fetchAll(PDO::FETCH_OBJ);
                                                ?>
                                                    <label>Choose an event:</label><br>
                                                    <select id="eventid" name="eventid">
                                                        <?php foreach ($eventresults as $eventresult) { ?>
                                                            <option value=<?php echo htmlentities($eventresult->id); ?>><?php echo htmlentities($eventresult->eventName); ?></option>
                                                        <?php } ?>
                                                    </select><br>
                                                <?php } ?>
                                                <button type="submit" id="submit" name="import" class="btn-submit">Import</button>
                                            </div>
                                        </form>
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
                // setTimeout(function() {
                //     $('.succWrap').slideUp("slow");
                // }, 3000);
            });
        </script>

    </body>

    </html>
<?php } ?>