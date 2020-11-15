<?php
require_once '../vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
session_start();
error_reporting(0);
include('includes/config.php');
if(strlen($_SESSION['alogin']) == 0) {	
    header('location:index.php');
} 
else {
    if (isset($_POST["export"])) {

        $spreadsheet = new Spreadsheet();

        $active_sheet = $spreadsheet->getActiveSheet();
        
        $active_sheet->setCellValue('A1', 'Banned List');
        $active_sheet->setCellValue('A3', 'Name');
        $active_sheet->setCellValue('B3', 'Email');
        $active_sheet->setCellValue('C3', 'Platform');
        $active_sheet->setCellValue('D3', 'Username');
        $active_sheet->setCellValue('E3', 'Description');
        $active_sheet->setCellValue('F3', 'Contact No');
        
        $currentrow = 4;

        $sql="SELECT * FROM blacklistusers2";
        $query = $dbh->prepare($sql);
        $query->execute();
        $results = $query->fetchAll(PDO::FETCH_OBJ);
        if($query->rowCount() > 0)
        {
            foreach($results as $result) {
                $active_sheet->setCellValue('A' . $currentrow, $result->name);
                $active_sheet->setCellValue('B' . $currentrow, $result->email);
                $active_sheet->setCellValue('C' . $currentrow, $result->platform);
                $active_sheet->setCellValue('D' . $currentrow, $result->username);
                $active_sheet->setCellValue('E' . $currentrow, $result->description);
                $active_sheet->setCellValue('F' . $currentrow, $result->phoneNo);
    
                $currentrow ++;
            }

            $file_name = 'Banned_List.xlsx';
            
            // $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, "Xlsx");
            // //$writer = new Xlsx($spreadsheet);
            // header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
            // header('Content-Disposition: attachment;filename="'.$file_name.'"');
            // $writer->save('php:://output');

            //exit;
           
            $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, "Xlsx");
          
            $writer->save($file_name);
          
            header('Content-Type: application/x-www-form-urlencoded');
          
            header('Content-Transfer-Encoding: Binary');
          
            header("Content-disposition: attachment; filename=\"".$file_name."\"");
          
            readfile($file_name);
          
            unlink($file_name);
          
            exit;
        }
    }
}
