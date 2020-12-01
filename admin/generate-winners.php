<?php
session_start();
error_reporting(0);
include('includes/config.php');
if (strlen($_SESSION['alogin']) == 0) {
    header('location:index.php');
} else {

    if (isset($_REQUEST['eventid'])) {
        $eventid = $_REQUEST['eventid'];

        $sql = "SELECT winnerCount,id from events where id = :eventid";
        $query = $dbh->prepare($sql);
        $query->bindParam(':eventid', $eventid, PDO::PARAM_STR);
        $query->execute() or die(print_r($query->errorInfo(), true));
        $results = $query->fetchAll(PDO::FETCH_OBJ);
        $winnerCount = $results[0]->winnerCount;
        
        $sql = "DELETE FROM winners WHERE eventId = :eventid";
        $query = $dbh->prepare($sql);
        $query->bindParam(':eventid', $eventid, PDO::PARAM_STR);
        $query->execute() or die(print_r($query->errorInfo(), true));
        
        $sql = "INSERT INTO winners (participantId, eventId, status, createdDateTime, updatedDateTime) 
                SELECT id, :eventid, 1, now(), now() FROM participants a 
                WHERE eventId = :eventid 
                AND NOT EXISTS ( 
                    SELECT 1 FROM blacklistusers b 
                    WHERE LOWER(TRIM(a.paypalEmail)) = LOWER(TRIM(b.email))
                    OR REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(TRIM(a.phoneNo), ')', ''), '(', ''), '+', ''), '-', ''), ' ', '') = 
                    REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(TRIM(b.phoneNo), ')', ''), '(', ''), '+', ''), '-', ''), ' ', '')
                ) 
                AND EXISTS ( 
                    SELECT 1 FROM verifiedmembers c 
                    WHERE LOWER(TRIM(a.discordName)) = LOWER(TRIM(c.name))
                ) 
                AND status = 1 
                ORDER BY RAND() 
                LIMIT :winnerCount
                ";
        $query = $dbh->prepare($sql);
        $query->bindParam(':eventid', $eventid, PDO::PARAM_STR);
        $query->bindParam(':winnerCount', $winnerCount, PDO::PARAM_INT);
        $query->execute() or die(print_r($query->errorInfo(), true));
        $msg = "Generated Winners Successfully";   
        //header('location:contentpage.php?content=winners&successmsg='.$msg); 
        echo "<script>location='contentpage.php?content=winners&successmsg=".$msg."'</script>";
 
    }
}
