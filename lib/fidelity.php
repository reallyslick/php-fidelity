<?php


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
                    $url = "https://cyrusbavarian.com/cynoteapi2/index.php/fidelity/getFidelity" . '?' . $queryString;

                    echo $url;
                    // Send the GET request and return the response
                    $response = file_get_contents($url);
                    return $response;
                }


               

                
                
                function clean($string) {
                    $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.

                    return preg_replace('/[^A-Za-z0-9]/', '', $string); // Removes special chars.
                 }
}

?>