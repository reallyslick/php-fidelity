<?php
require __DIR__ . '/vendor/autoload.php';

class fidelity{
    
                private $rowData = Array();
                private $Headers = Array();
	
                //each row of the file is converted into an array
		public function read_file_into_array($filename)
		{
                        print "Reading column headings into array";
             
			$row=1;
			if (($handle = fopen("./documents/$filename", "r")) !== FALSE) {
				while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                                    if(empty($data[0])) // don't include blank data
                                    {
                                        break;
                                    }
                                
                                    array_push($this->rowData, $data);
				}
				fclose($handle);
			}
                        
                        $this->setHeaders();
                        return $this->rowData;
		}
                
                //each row of the file is converted into an array
		public function getFiles()
		{
                    $directory = './documents/';
                    $files = array_diff(scandir($directory), array('..', '.'));
                        print "Reading column headings into array\n";
                        return $files;
		}
                
                
                //the first row is the headers
                public function setHeaders()
                {
                    /*Parse column headers into Array, first row of file*/
                    $result = $this->rowData;
                    $Headers = Array();
                    foreach($result[0] as $header)
                    {
                        //print "Header: " . $header ."\n";
                        array_push($this->Headers,$this->clean($header));
                    }
                    
                }
                
                public function convertRowToKV($row)
                {
                    
                    $kvArray= Array();
                    foreach($this->Headers as $k=>$header)
                    {
                        
                        $kvArray[$header] = empty($row[$k]) ? "" : preg_replace('/[^A-Za-z0-9\-\.]/', '', $row[$k]);
                    }
                    
                    return $kvArray;
                    
                }
                
                function sendPostRequest($data) {
                    
                    $url = "https://cyrusbavarian.com/cynoteapi2/index.php/fidelity/FileInsert";
                            
                    // Initialize cURL session
                    $ch = curl_init($url);

                    // Set cURL options
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
                    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

                    // Execute the request and get the response
                    $response = curl_exec($ch);

                    // Handle errors
                    if ($response === false) {
                        return 'Error: ' . curl_error($ch);
                    }

                    // Close the cURL session
                    curl_close($ch);

                    return $response;
                }
                
                function sendGetRequest($params = []) {
                    // Build the query string from the $params array
                    $queryString = http_build_query($params);

                    // Create the full URL
                    $url = "https://cyrusbavarian.com/cynoteapi2/index.php/fidelity/fileexist" . '?' . $queryString;

                    // Send the GET request and return the response
                    $response = file_get_contents($url);
                    return $response;
                }


               

                
                
                function clean($string) {
                    $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.

                    return preg_replace('/[^A-Za-z0-9]/', '', $string); // Removes special chars.
                 }
}




/*MAIN*/
$fi= new fidelity();





$files = $fi->getFiles();


foreach($files as $fileName)
{
    //check to see if file was already inserted
    $fileExists = $fi->sendGetRequest(Array("filename"=>$fileName));
    $resultArray = json_decode($fileExists,true);
    //echo $fileExists;
    if($resultArray["fileExists"])
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