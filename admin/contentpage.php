<?php
session_start();
error_reporting(0);
include('includes/config.php');
if (strlen($_SESSION['alogin']) == 0) {
    header('location:index.php');
} else {

    if (isset($_REQUEST['content'])) {
        $contentpage = $_REQUEST['content'];

        switch ($contentpage) {
            case 'verifiedlist':
                $tablename = 'verifiedmembers';
                $titlename = 'Verified Users';
                $createnew = 'Create new verified user';
                $columnnames = array("name", "discordId");
                break;
            case 'blacklist':
                $tablename = 'blacklistusers';
                $titlename = 'Blacklist Users';
                $createnew = 'Create new blacklist user';
                $columnnames = array("name", "phoneNo", "email", "description", "platform", "username");
                break;
            case 'events':
                $tablename = 'events';
                $titlename = 'Events';
                $createnew = 'Create new event';
                $columnnames = array("eventName", "startDateTime", "endDateTime", "registerCount", "winnerCount");
                break;
            case 'participants':
                $tablename = 'participants';
                $titlename = 'Participants';
                $createnew = 'Create new participant';
                $columnnames = array("discordName", "fullName", "paypalEmail", "shippingAddress", "shippingOptions", "phoneNo", "comments", "registerDateTime");
                break;
            case 'winners':
                $tablename = 'winners';
                $titlename = 'Winners';
                $createnew = 'Create new winner';
                $columnnames = array("discordName", "fullName", "paypalEmail", "shippingAddress", "shippingOptions", "phoneNo", "comments", "registerDateTime");
                break;
            default:
                echo 'Invalid content page selected.';
                return;
        }

        if (isset($_GET['del'])) {
            $id = $_GET['del'];

            $sql = "DELETE FROM " . $tablename . " WHERE id=:id";
            $query = $dbh->prepare($sql);
            $query->bindParam(':id', $id, PDO::PARAM_STR);
            $query->execute() or die(print_r($query->errorInfo(), true));

            $msg = "Data Deleted successfully";
        }

        if (isset($_REQUEST['unconfirm'])) {
            $aeid = intval($_GET['unconfirm']);
            $memstatus = 1;
            $sql = "UPDATE " . $tablename . " SET status=:status WHERE  id=:aeid";
            $query = $dbh->prepare($sql);
            $query->bindParam(':status', $memstatus, PDO::PARAM_STR);
            $query->bindParam(':aeid', $aeid, PDO::PARAM_STR);
            $query->execute() or die(print_r($query->errorInfo(), true));
            $msg = "Changes Successfully";
        }

        if (isset($_REQUEST['confirm'])) {
            $aeid = intval($_GET['confirm']);
            $memstatus = 0;
            $sql = "UPDATE " . $tablename . " SET status=:status WHERE  id=:aeid";
            $query = $dbh->prepare($sql);
            $query->bindParam(':status', $memstatus, PDO::PARAM_STR);
            $query->bindParam(':aeid', $aeid, PDO::PARAM_STR);
            $query->execute() or die(print_r($query->errorInfo(), true));
            $msg = "Changes Successfully";
        }
    } else {
        echo 'Invalid content page selected.';
        return;
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

                            <?php
                            if (in_array($contentpage, array( 'winners'))) {
                                $sql = "SELECT * from events";
                                $query = $dbh->prepare($sql);
                                $query->execute() or die(print_r($query->errorInfo(), true));
                                $eventresults = $query->fetchAll(PDO::FETCH_OBJ);
                            ?>
                                <form action="generate-winners.php" method="post">
                                    <label for="eventid">Generate winner:</label>
                                    <select id="eventid" name="eventid">
                                        <?php foreach ($eventresults as $eventresult) { ?>
                                            <option value=<?php echo htmlentities($eventresult->id); ?>><?php echo htmlentities($eventresult->eventName); ?></option>
                                        <?php } ?>
                                    </select>
                                    <input type="submit" value="Generate">
                                </form>
                            <?php } ?>

                            <?php
                            if (in_array($contentpage, array('participants', 'winners'))) {
                                $sql = "SELECT * from events";
                                $query = $dbh->prepare($sql);
                                $query->execute() or die(print_r($query->errorInfo(), true));
                                $eventresults = $query->fetchAll(PDO::FETCH_OBJ);
                            ?>
                                <form action="" method="post">
                                    <label for="eventid">View Winners:</label>
                                    <select id="eventid" name="eventid">
                                        <?php foreach ($eventresults as $eventresult) { ?>
                                            <option value=<?php echo htmlentities($eventresult->id); ?>><?php echo htmlentities($eventresult->eventName); ?></option>
                                        <?php } ?>
                                    </select>
                                    <input type="submit">
                                </form>
                            <?php } ?>

                            <form action="editpage.php?editpage=<?php echo $contentpage; ?>" method="post">
                                <input type="submit" name="" class="btn btn-primary btn-sm" value="<?php echo $createnew; ?>" />
                                <!-- <input type="hidden" name="eventid" value=<?php echo $eventresult->id; ?>> -->
                            </form>
                            <form action="uploadexcel.php" method="post">
                                <input type="submit" name="submit" class="btn btn-primary btn-sm" value="Upload excel(WIP)" />
                                <input type="hidden" id="uploadpage" name="uploadpage" value=<?php echo $contentpage; ?>>
                            </form>
                            <form action="php_spreadsheet_export.php" method="post">
                                <input type="submit" name="export" class="btn btn-primary btn-sm" value="Export as excel(WIP)" />
                                <input type="hidden" id="file_type" name="file_type" value="Xlsx">
                                <input type="hidden" id="exportpage" name="exportpage" value=<?php echo $contentpage; ?>>
                            </form>
                            <!-- Zero Configuration Table -->
                            <div class="panel panel-default">
                                <div class="panel-heading">List <?php echo $titlename; ?></div>
                                <div class="panel-body">
                                    <?php if ($error) { ?>
                                        <div class="errorWrap" id="msgshow"><?php echo htmlentities($error); ?> </div>
                                    <?php } else if (isset($_REQUEST['successmsg'])) { ?>
                                        <div class="succWrap" id="msgshow"><?php echo htmlentities($_REQUEST['successmsg']); ?> </div>
                                    <?php } else if ($msg) { ?>
                                        <div class="succWrap" id="msgshow"><?php echo htmlentities($msg); ?> </div>
                                    <?php } ?>
                                    <table id="zctb" class="display table table-striped table-bordered table-hover" cellspacing="0" width="100%">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <?php
                                                foreach ($columnnames as $columnname) {
                                                    echo '<th>' . $columnname . '</th>';
                                                }
                                                ?>
                                                <th>status</th>
                                                <th>action</th>
                                            </tr>
                                        </thead>

                                        <tbody>

                                            <?php
                                            $sql = $contentpage == "winners" ? "SELECT winners.id as wid, winners.status as wstatus, participants.* from " . $tablename : "SELECT * from " . $tablename;
                                            if (in_array($contentpage, array('participants', 'winners'))) {
                                                $sql .= $contentpage == 'participants'
                                                    ? " WHERE eventId=:eventid"
                                                    : " INNER JOIN participants ON winners.participantId = participants.id WHERE winners.eventId=:eventid";
                                            }
                                            $query = $dbh->prepare($sql);
                                            if (in_array($contentpage, array('participants', 'winners'))) {
                                                $eventid = isset($_REQUEST['eventid']) ? $_REQUEST['eventid']: '';
                                                $query->bindParam(':eventid', $eventid, PDO::PARAM_STR);
                                            }
                                            $query->execute() or die(print_r($query->errorInfo(), true));
                                            $results = $query->fetchAll(PDO::FETCH_OBJ);
                                            $cnt = 1;
                                            if ($query->rowCount() > 0) {
                                                foreach ($results as $result) { ?>
                                                    <tr>
                                                        <td><?php echo htmlentities($cnt); ?></td>
                                                        <?php foreach ($columnnames as $columnname) { ?>
                                                            <td><?php echo htmlentities($result->$columnname); ?></td>
                                                        <?php } ?>
                                                        <td>
                                                            <?php if (($contentpage=='winners'?$result->wstatus:$result->status) == 1) { ?>
                                                                <a href="contentpage.php?content=<?php echo $contentpage; ?>&confirm=<?php echo htmlentities($contentpage=='winners'?$result->wid:$result->id); ?>">Active <i class="fa fa-check-circle"></i></a>
                                                            <?php } else { ?>
                                                                <a href="contentpage.php?content=<?php echo $contentpage; ?>&unconfirm=<?php echo htmlentities($contentpage=='winners'?$result->wid:$result->id); ?>">Inactive <i class="fa fa-times-circle"></i></a>
                                                            <?php } ?>
                                                        </td>
                                                        <td>
                                                            <a href="editpage.php?edit=<?php echo $contentpage=='winners'?$result->wid:$result->id; ?>&editpage=<?php echo $contentpage; ?>">&nbsp; <i class="fa fa-pencil"></i></a>&nbsp;&nbsp;
                                                            <a href="contentpage.php?content=<?php echo $contentpage; ?>&del=<?php echo $contentpage=='winners'?$result->wid:$result->id; ?>" onclick="return confirm('Do you want to Delete');"><i class="fa fa-trash" style="color:red"></i></a>&nbsp;&nbsp;
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