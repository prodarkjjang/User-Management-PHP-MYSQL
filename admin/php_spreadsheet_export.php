<?php
include '../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;

$connect = new PDO("mysql:host=localhost;dbname=cysm2", "root", "");

if (isset($_REQUEST['exportpage'])) {
    $exportpage = $_REQUEST['exportpage'];
    switch ($exportpage) {
        case 'verifiedlist':
            $tablename = 'verifiedmembers';
            $titlename = 'Verified Users';
            $columnnames = array("name" => "A", "discordId" => "C");
            $file_front_name = "Verified_List";
            break;
        case 'blacklist':
            $tablename = 'blacklistusers';
            $titlename = 'Blacklist Users';
            $editpage = 'edit-blacklist.php';
            $createnew = 'Create new blacklist user';
            $columnnames = array("name" => "A", "email" => "B", "platform" => "C", "username" => "D", "description" => "E", "phoneNo" => "F");
            $file_front_name = "Banned_List";
            break;
        default:
            echo 'Invalid export page selected.';
            return;
    }
} else {
    echo 'Invalid export page selected.';
    return;
}

$query = "SELECT * FROM " . $tablename . " WHERE STATUS = 1 ORDER BY id DESC";

$statement = $connect->prepare($query);

$statement->execute();

$result = $statement->fetchAll();

if (isset($_POST["export"])) {
    $file = new Spreadsheet();

    $active_sheet = $file->getActiveSheet();

    switch ($exportpage) {
        case 'verifiedlist':
            $count = 1;
            break;
        case 'blacklist':
            $active_sheet->setCellValue('A1', 'Banned List');
            $active_sheet->setCellValue('A3', 'Name');
            $active_sheet->setCellValue('B3', 'Email');
            $active_sheet->setCellValue('C3', 'Platform');
            $active_sheet->setCellValue('D3', 'Username');
            $active_sheet->setCellValue('E3', 'Description');
            $active_sheet->setCellValue('F3', 'Contact No');

            $count = 4;
            break;
        default:
            echo 'Invalid export page selected.';
            return;
    }

    foreach ($result as $row) {
        foreach ($columnnames as $columnname => $columnvalue) {
            $active_sheet->setCellValue($columnvalue . $count, $row[$columnname]);
        }
        $count = $count + 1;
    }

    $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($file, $_POST["file_type"]);

    $file_name = $file_front_name . date('m-d-Y_hia') . "." . strtolower($_POST["file_type"]);

    $writer->save($file_name);

    header('Content-Type: application/x-www-form-urlencoded');

    header('Content-Transfer-Encoding: Binary');

    header("Content-disposition: attachment; filename=\"" . $file_name . "\"");

    readfile($file_name);

    unlink($file_name);

    exit;
}
