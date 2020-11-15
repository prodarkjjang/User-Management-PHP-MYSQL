<?php
session_start();
error_reporting(0);
include('includes/config.php');
if(strlen($_SESSION['alogin'])==0)
	{	
header('location:index.php');
}
else{

if(isset($_GET['edit']))
	{
		$editid=$_GET['edit'];
	}

  if(isset($_GET['eventid']))
	{
		$eventid=$_GET['eventid'];
	}

if(isset($_POST['submit']))
  {
	$discordName=$_POST['discordName'];
	$fullName=$_POST['fullName'];
	$paypalEmail=$_POST['paypalEmail'];
	$shippingAddress=$_POST['shippingAddress'];
	$shippingOptions=$_POST['shippingOptions'];
  $phoneNo=$_POST['phoneNo'];
  $comments=$_POST['comments'];
  $registerDateTime=$_POST['registerDateTime'];
  $status=$_POST['status'];
  $idedit=$_POST['idedit'];

    if ($idedit != '') {
        $sql="UPDATE participants SET discordName=(:discordName), fullName=(:fullName), paypalEmail=(:paypalEmail), shippingAddress=(:shippingAddress), shippingOptions=(:shippingOptions), phoneNo=(:phoneNo), comments=(:comments), registerDateTime=(:registerDateTime), status=(:status) WHERE id=(:idedit)";
    } else {
        $sql="INSERT INTO participants (eventId, discordName, fullName, paypalEmail, shippingAddress, shippingOptions, phoneNo, comments, registerDateTime, status) VALUES ((:eventid), (:discordName), (:fullName), (:paypalEmail), (:shippingAddress), (:shippingOptions), (:phoneNo), (:comments), (:registerDateTime), (:status))";
    }
	$query = $dbh->prepare($sql);
	$query-> bindParam(':discordName', $name, PDO::PARAM_STR);
	$query-> bindParam(':fullName', $email, PDO::PARAM_STR);
	$query-> bindParam(':paypalEmail', $platform, PDO::PARAM_STR);
	$query-> bindParam(':shippingAddress', $username, PDO::PARAM_STR);
	$query-> bindParam(':shippingOptions', $description, PDO::PARAM_STR);
  $query-> bindParam(':phoneNo', $phoneNo, PDO::PARAM_STR);
  $query-> bindParam(':comments', $comments, PDO::PARAM_STR);
  $query-> bindParam(':registerDateTime', $registerDateTime, PDO::PARAM_STR);
  $query-> bindParam(':status', $status, PDO::PARAM_STR);
  if ($idedit != '') {
      $query-> bindParam(':idedit', $idedit, PDO::PARAM_STR);
      $query->execute();
      $msg="Information Updated Successfully";
  } else {
      $query-> bindParam(':eventid', $eventid, PDO::PARAM_STR);
      $query->execute();
      $msg="New Participant Added Successfully";
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
	
	<title>Edit Participant</title>

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

	<script type= "text/javascript" src="../vendor/countries.js"></script>
	<style>
.errorWrap {
    padding: 10px;
    margin: 0 0 20px 0;
	background: #dd3d36;
	color:#fff;
    -webkit-box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
    box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
}
.succWrap{
    padding: 10px;
    margin: 0 0 20px 0;
	background: #5cb85c;
	color:#fff;
    -webkit-box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
    box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
}
		</style>
</head>

<body>
<?php
		$sql = "SELECT * from participants where id = :editid";
		$query = $dbh -> prepare($sql);
		$query->bindParam(':editid',$editid,PDO::PARAM_INT);
		$query->execute();
		$result=$query->fetch(PDO::FETCH_OBJ);
		$cnt=1;	
?>
	<?php include('includes/header.php');?>
	<div class="ts-main-content">
	<?php include('includes/leftbar.php');?>
		<div class="content-wrapper">
			<div class="container-fluid">
				<div class="row">
					<div class="col-md-12">
						<h3 class="page-title">Edit participant: <?php echo htmlentities($result->discordName); ?></h3>
						<div class="row">
							<div class="col-md-12">
								<div class="panel panel-default">
									<div class="panel-heading">Edit Info</div>
<?php if($error){?><div class="errorWrap"><strong>ERROR</strong>:<?php echo htmlentities($error); ?> </div><?php } 
				else if($msg){?><div class="succWrap"><strong>SUCCESS</strong>:<?php echo htmlentities($msg); ?> </div><?php }?>

<div class="panel-body">
<form method="post" class="form-horizontal" enctype="multipart/form-data" name="imgform">
    <div class="form-group">
        <label class="col-sm-2 control-label">discordName<span style="color:red">*</span></label>
        <div class="col-sm-4">
            <input type="text" name="discordName" class="form-control" required value="<?php echo htmlentities($result->discordName);?>">
        </div>
        <label class="col-sm-2 control-label">fullName<span style="color:red">*</span></label>
        <div class="col-sm-4">
            <input type="text" name="fullName" class="form-control" required value="<?php echo htmlentities($result->fullName);?>">
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-2 control-label">paypalEmail<span style="color:red">*</span></label>
        <div class="col-sm-4">
            <input type="email" name="paypalEmail" class="form-control" required value="<?php echo htmlentities($result->paypalEmail);?>">
        </div>
        <label class="col-sm-2 control-label">shippingAddress<span style="color:red">*</span></label>
        <div class="col-sm-4">
            <input type="text" name="shippingAddress" class="form-control" required value="<?php echo htmlentities($result->shippingAddress);?>">
        </div>
    </div>


    <div class="form-group">
        <label class="col-sm-2 control-label">shippingOptions<span style="color:red">*</span></label>
        <div class="col-sm-4">
          <input type="text" name="shippingOptions" class="form-control" value="<?php echo htmlentities($result->shippingOptions);?>">
        </div>

        <label class="col-sm-2 control-label">phoneNo<span style="color:red">*</span></label>
        <div class="col-sm-4">
            <input type="text" name="phoneNo" class="form-control" required value="<?php echo htmlentities($result->phoneNo);?>">
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-2 control-label">comments<span style="color:red">*</span></label>
        <div class="col-sm-4">
          <input type="text" name="comments" class="form-control" value="<?php echo htmlentities($result->comments);?>">
        </div>

        <label class="col-sm-2 control-label">registerDateTime<span style="color:red">*</span></label>
        <div class="col-sm-4">
            <input type="text" name="registerDateTime" class="form-control" required value="<?php echo htmlentities($result->registerDateTime);?>">
        </div>
    </div>

    <div class="form-group">
        <div class="col-sm-8 col-sm-offset-2">
            <img src="../images/<?php echo htmlentities($result->image);?>" width="150px"/>
            <input type="hidden" name="image" value="<?php echo htmlentities($result->image);?>" >
            <input type="hidden" name="idedit" value="<?php echo htmlentities($result->id);?>" >
        </div>
    </div>

    <div class="form-group">
        <div class="col-sm-8 col-sm-offset-2">
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
				 $(document).ready(function () {          
					setTimeout(function() {
						$('.succWrap').slideUp("slow");
					}, 3000);
					});
	</script>

</body>
</html>
<?php } ?>