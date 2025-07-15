<?php
require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/lib/fidelity.php';




/*MAIN*/
$fi= new fidelity();
$files = $fi->getFiles(); //read csv's in the ./documents directory


foreach($files as $fileName)
{

    if($fi->checkFileExists(Array("filename"=>$fileName)))
    {
        echo "file $fileName already inserted\n";
        
        $fname = "./documents/".$fileName;
        //delete file after inserting...
        if (file_exists($fname)) {
            if (unlink($fname)) {
                echo "File deleted successfully.";
            } else {
                echo "Error deleting the file.";
            }
        } else {
            echo "File does not exist.";
        }
        
        continue;
        
    }
    
    
    //if not insert into database
    echo "Parsing file: " . $fileName . "\n";
    $data = $fi->read_file_into_array($fileName);
    
    // Use regex to extract the date parts (month, day, year)
    preg_match('/([A-Za-z]+)-(\d{2})-(\d{4})/', $fileName, $matches);

    if ($matches) {
        // Convert month name to numeric format
        $month = date('m', strtotime($matches[1])); 
        $day = $matches[2];
        $year = $matches[3];

        // Format as YYYYMMDD
        $formattedDate = "{$year}{$month}{$day}";

        echo $formattedDate; // Output: 20250606
    }


    //each stock is a row, several stocks in a file
    foreach($data as $i => $row)
    {
        echo "\nInserting Data: " . substr(implode(" ",$row), 0, 50) . "...";
        if($i>2) //ignore first 2 rows
        {
            //echo json_encode($row) . "\n";

            $rowArray = $fi->convertRowToKV($row);
            $rowArray["FileName"] = $fileName;
            $rowArray["dtts"] = $formattedDate;
            //echo "\n\n###### Row Array : ".json_encode($rowArray);
            $response = $fi->sendPostRequest($rowArray);

            $ra = json_decode($response, true);
            
            //echo $ra["result"] . "";
            if ($ra["result"]) {
                echo "Success!" . "\n";
            } else {
                echo "fail!" . $response . "\n";
            }
            
            //break;

            sleep(1);
        }
        else
        {
            echo "Ignoring this row...";
        }
    }
    
    

    
}






?>