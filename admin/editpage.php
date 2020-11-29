<?php
session_start();
error_reporting(0);
include('includes/config.php');
if (strlen($_SESSION['alogin']) == 0) {
    header('location:index.php');
} else {

    if (isset($_GET['edit'])) {
        $editid = $_GET['edit'];
    }

    if (isset($_REQUEST['editpage'])) {
        $editpage = $_REQUEST['editpage'];

        switch ($editpage) {
            case 'verifiedlist':
                $tablename = 'verifiedmembers';
                $titlename = 'Verified Users';
                $columnnames = array("name" => "text", "discordId" => "text");
                $columnnrequired = array("name" => 1, "discordId" => 0);
                break;
            case 'blacklist':
                $tablename = 'blacklistusers';
                $titlename = 'Blacklist Users';
                $createnew = 'Create new blacklist user';
                $columnnames = array("name" => "text", "phoneNo" => "text", "email" => "email", "description" => "text", "platform" => "text", "username" => "text");
                $columnnrequired = array("name" => 1, "phoneNo" => 1, "email" => 1, "description" => 0, "platform" => 0, "username" => 0);
                break;
            case 'events':
                $tablename = 'events';
                $titlename = 'Events';
                $createnew = 'Create new event';
                $columnnames = array("eventName" => "text", "startDateTime" => "text", "endDateTime" => "text", "registerCount" => "text", "winnerCount" => "text");
                $columnnrequired = array("eventName" => 1, "startDateTime" => 0, "endDateTime" => 0, "registerCount" => 1, "winnerCount" => 1);
                break;
            case 'participants':
                $tablename = 'participants';
                $titlename = 'Participants';
                $createnew = 'Create new participant';
                $columnnames = array("discordName" => "text", "fullName" => "text", "paypalEmail" => "email", "shippingAddress" => "text", "shippingOptions" => "text", "phoneNo" => "text", "comments" => "text", "registerDateTime" => "text");
                $columnnrequired = array("discordName" => 1, "fullName" => 1, "paypalEmail" => 1, "shippingAddress" => 0, "shippingOptions" => 0, "phoneNo" => 1, "comments" => 0, "registerDateTime" => 1);
                break;
            case 'winners':
                $tablename = 'winners';
                $titlename = 'Winners';
                $createnew = 'Create new winner';
                $columnnames = array("discordName" => "text", "fullName" => "text", "paypalEmail" => "email", "shippingAddress" => "text", "shippingOptions" => "text", "phoneNo" => "text", "comments" => "text", "registerDateTime" => "text");
                $columnnrequired = array("discordName" => 1, "fullName" => 1, "paypalEmail" => 1, "shippingAddress" => 0, "shippingOptions" => 0, "phoneNo" => 1, "comments" => 0, "registerDateTime" => 1);
                break;
            default:
                echo 'Invalid edit page selected.';
                return;
        }
    } else {
        echo 'Invalid edit page selected.';
        return;
    }

    if (isset($_POST['submit'])) {

        if ($editid != '') {
            $sql = $editpage == 'winners' ? "UPDATE winners INNER JOIN participants ON winners.participantid = participants.id SET " : "UPDATE " . $tablename . " SET ";
            foreach ($columnnames as $columnname => $columnvalue) {
                $sql .= $columnname . "=";
                $sql .= "(:" . $columnname . "), ";
            }
            $sql .= $editpage == 'winners' ? "winners.updatedDateTime=NOW() " : "updatedDateTime=NOW() ";
            $sql .= $editpage == 'winners' ? "WHERE winners.id=(:editid)" : "WHERE id=(:editid)";
        } else {
            $sql = $editpage == 'winners' ? "INSERT INTO participants (" : "INSERT INTO " . $tablename . " (";
            foreach ($columnnames as $columnname => $columnvalue) {
                $sql .= $columnname . ", ";
            }
            $sql .= "createdDateTime, updatedDateTime";
            $sql .= isset($_POST['eventid']) ? ", eventId" : "";
            $sql .= ") ";
            $sql .= "VALUES (";
            foreach ($columnnames as $columnname => $columnvalue) {
                $sql .= "(:" . $columnname . "), ";
            }
            $sql .= "NOW(), NOW()";
            $sql .= isset($_POST['eventid']) ? ", " . $_POST['eventid'] : "";
            $sql .= ")";
            $sql .= $editpage == 'winners' ? "; INSERT INTO winners (participantid, eventId, createdDateTime, updatedDateTime) VALUES (LAST_INSERT_ID(), " . $_POST['eventid'] . ", NOW(), NOW())" : "";
        }
        $query = $dbh->prepare($sql);
        foreach ($columnnames as $columnname => $columnvalue) {
            $query->bindParam(':' . $columnname, $_POST[$columnname], PDO::PARAM_STR);
        }
        if ($editid != '') {
            $query->bindParam(':editid', $editid, PDO::PARAM_STR);
            $query->execute() or die(print_r($query->errorInfo(), true));
            $msg = "Updated Successfully";
        } else {
            $query->execute() or die(print_r($query->errorInfo(), true));
            $msg = "New " . $titlename . " Added Successfully";
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

        <title>Edit <?php echo $titlename; ?></title>

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
        $sql = "SELECT * from " . $tablename;

        if (in_array($editpage, array('winners'))) {
            $sql .= " INNER JOIN participants ON winners.participantId = participants.id";
            $sql .= " where winners.id = :editid";
        } else {
            $sql .= " where id = :editid";
        }
        $query = $dbh->prepare($sql);
        $query->bindParam(':editid', $editid, PDO::PARAM_INT);
        $query->execute() or die(print_r($query->errorInfo(), true));
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
                            <h3 class="page-title">Edit <?php echo $titlename; ?>: <?php echo htmlentities($result->name); ?></h3>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="panel panel-default">
                                        <div class="panel-heading">Edit Info</div>
                                        <?php if ($error) { ?><div class="errorWrap"><strong>ERROR</strong>:<?php echo htmlentities($error); ?> </div><?php } else if ($msg) { ?><div class="succWrap"><strong>SUCCESS</strong>:<?php echo htmlentities($msg); ?> </div><?php } ?>

                                        <div class="panel-body">
                                            <form method="post" class="form-horizontal" enctype="multipart/form-data" name="imgform">


                                                <?php
                                                if (in_array($editpage, array('participants', 'winners')) && !isset($_GET['edit'])) {
                                                    $sql = "SELECT * from events";
                                                    $query = $dbh->prepare($sql);
                                                    $query->execute() or die(print_r($query->errorInfo(), true));
                                                    $eventresults = $query->fetchAll(PDO::FETCH_OBJ);
                                                ?>
                                                    <div class="form-group">
                                                        <label class="col-sm-2 control-label">Choose an event:<span style="color:red">*</span></label>
                                                        <div class="col-sm-4">
                                                            <select class="form-control" id="eventid" name="eventid">
                                                                <?php foreach ($eventresults as $eventresult) { ?>
                                                                    <option value=<?php echo htmlentities($eventresult->id); ?>><?php echo htmlentities($eventresult->eventName); ?></option>
                                                                <?php } ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                <?php } ?>


                                                <?php foreach ($columnnames as $columnname => $columnvalue) { ?>
                                                    <div class="form-group">
                                                        <label class="col-sm-2 control-label"><?php echo $columnname; ?><span style="color:red">*</span></label>
                                                        <div class="col-sm-4">
                                                            <input type=<?php echo $columnvalue; ?> name=<?php echo $columnname; ?> class="form-control" <?php echo $columnnrequired[$columnname] == 1 ? "required" : "" ?> value="<?php echo htmlentities($result->$columnname); ?>">
                                                        </div>
                                                    </div>
                                                <?php } ?>

                                                <div class="form-group">
                                                    <div class="col-sm-8 col-sm-offset-2">
                                                        <input type="hidden" name="edit" value="<?php echo htmlentities($result->id); ?>">
                                                        <button class="btn btn-primary" name="submit" type="submit">Save Changes</button>
                                                    </div>
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