<?php
session_start();
error_reporting(0);
include('includes/config.php');
if (strlen($_SESSION['alogin']) == 0) {
    header('location:index.php');
} else {

    if (isset($_GET['eventid'])) {
        $eventid = $_GET['eventid'];

        $sql = "SELECT winnerCount,id from events where id = :eventid";
        $query = $dbh->prepare($sql);
        $query->bindParam(':eventid', $eventid, PDO::PARAM_STR);
        $query->execute();
        $results = $query->fetchAll(PDO::FETCH_OBJ);
        $winnerCount = $results[0]->winnerCount;
        
        $sql = "DELETE FROM winners WHERE eventId = :eventid";
        $query = $dbh->prepare($sql);
        $query->bindParam(':eventid', $eventid, PDO::PARAM_STR);
        $query->execute();

        $sql = "INSERT INTO winners (participantId, eventId, status, createdDateTime, updatedDateTime) 
                SELECT id, 1, 1, now(), now() FROM participants a 
                WHERE eventId = :eventid 
                AND NOT EXISTS ( 
                    SELECT 1 FROM blacklistusers b 
                    WHERE  b.email = a.paypalEmail 
                ) 
                AND NOT EXISTS ( 
                    SELECT 1 FROM blacklistusers b 
                    WHERE  b.phoneNo = a.phoneNo 
                ) 
                AND status = 1 
                ORDER BY RAND() 
                LIMIT :winnerCount
                ";
        $query = $dbh->prepare($sql);
        $query->bindParam(':eventid', $eventid, PDO::PARAM_STR);
        $query->bindParam(':winnerCount', $winnerCount, PDO::PARAM_INT);
        $query->execute();
        print_r($query->errorInfo());
        $msg = "Changes Successfully";
    }
}
