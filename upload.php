<?php
$targetDir = "uploads/";
$fileName = basename($_FILES["CSVfile"]["name"]);
$targetFile = $targetDir . $fileName;
$templocation = $_FILES["CSVfile"]["tmp_name"];
move_uploaded_file($templocation, $targetFile);
$outputDir = "outputs/";
$fileOutputName = $outputDir . $fileName;

$fileInputName = $targetFile ;

$curentDate = date("Y-m-d");
$csvLine = file( $fileInputName);
if ( count($csvLine) != 0){
    unset($csvLine[0]);
    $arCSV = array();
    $arProjects = array();
    $arUsers = array();
    $arProjectsUsers = array();
    $arProjectsRows = array();
    foreach ($csvLine as $num => $line){
        $arLine = explode (",",$line);
        $arCSV[$num] = $arLine;
        if (!in_array($arLine[1], $arProjects)){
            $arProjects[] = intval($arLine[1]);
            $arProjectsRows[$arLine[1]][] = $num;
        }
        else{
            $arProjectsRows[$arLine[1]][] = $num;
        }
        if (!in_array($arLine[0], $arUsers)){
            $arUsers[] = intval($arLine[0]);
        }
    }
    
    $arOutput =array();    
    foreach ($arProjectsRows as $num => $project){ 
        $projectID = $num;
        $n = count($project);
        if ($n >= 2){
            for ($c = 0; $c < $n - 1; $c++) {
                $row1 = explode (",",str_replace(" ","",$csvLine[$project[$c]]));
                if (str_contains($row1[3],"NULL")) $row1[3] = $curentDate;
                for ($d = $c + 1; $d < $n; $d++) {
                    $row2 = explode (",",str_replace(" ","",$csvLine[$project[$d]]));
                    if (str_contains($row2[3],"NULL")) $row2[3] = $curentDate;
                    if ($row2[2] >= $row1[2] && $row2[2] <= $row1[3] && $row2[3] > $row1[3]){
                        $date1=date_create($row2[2]);
                        $date2=date_create($row1[3]);
                        $diff=date_diff($date1,$date2);
                        $arOutput[] = [$row1[0],$row2[0],$row2[1],$diff->format("%a")];
                    }
                    else if ($row2[2] >= $row1[2] && $row2[2] < $row1[3] && $row2[3] <= $row1[3]){
                        $date1=date_create($row2[2]);
                        $date2=date_create($row2[3]);
                        $diff=date_diff($date1,$date2);
                        $arOutput[] = [$row1[0],$row2[0],$row2[1],$diff->format("%a")];
                    }
                    else if ($row1[2] >= $row2[2] && $row1[2] <= $row2[3] && $row1[3] > $row2[3]){
                        $date1=date_create($row1[2]);
                        $date2=date_create($row2[3]);
                        $diff=date_diff($date1,$date2);
                        $arOutput[] = [$row1[0],$row2[0],$row2[1],$diff->format("%a")];
                    }
                    else if ($row1[2] >= $row2[2] && $row1[2] < $row2[3] && $row1[3] <= $row2[3]){
                        $date1=date_create($row1[2]);
                        $date2=date_create($row1[3]);
                        $diff=date_diff($date1,$date2);
                        $arOutput[] = [$row1[0],$row2[0],$row2[1],$diff->format("%a")];
                    }
                    else{
                    //    echo "no"; echo nl2br("\n");
                    }
                }
            }
        }
    }   
}       
else{
echo "File is empry.";

}
$firstRow = ['Employee ID #1', 'Employee ID #2', 'Project ID', 'Days worked'];

$html = 
"<html>"
    ."<body>"
        ."<table>"
            ."<tr>"
                ."<th>Employee ID #1</th>"
                ."<th>Employee ID #2</th>"
                ."<th>Project ID</th>"
                ."<th>Days worked</th>"
            ."</tr>";
            foreach ($arOutput as $line) {
                $html .= 
                 "<tr>"
                    ."<td>$line[0]</td>"
                    ."<td>$line[1]</td>"
                    ."<td>$line[2]</td>"
                    ."<td>$line[3]</td>"
                ."</tr>";       
                
            }
$html .= 
"</body>"
."</html>";

echo $html;

//$file = fopen($fileOutputName,"w");
//fputcsv($file, $firstRow);
//foreach ($arOutput as $line) {
//  fputcsv($file, $line);
//}
//
//fclose($file);
//if (!empty($fileOutputName) && file_exists($fileOutputName)){
//    header("Content-Control: public"); 
//    header("Content-Description: File Transfer"); 
//    header("Content-Type: application/octet-stream"); 
//    header("Content-Disposition: attachment; filename=$fileName");
//    
//    readfile ($fileOutputName);
    exit();
//}



?>
