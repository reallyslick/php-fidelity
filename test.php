<?php
require __DIR__ . '/vendor/autoload.php';

class fidelity{
    
                private $rowData = Array();
                private $Headers = Array();
	
                //each row of the file is converted into an array
		public function read_file_into_array()
		{
                        print "Reading column headings into array";
             
			$row=1;
			if (($handle = fopen("./documents/Portfolio_Positions_Jun-06-2025.csv", "r")) !== FALSE) {
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
                        
                        $kvArray[$header] = empty($row[$k]) ? "" : $row[$k];
                    }
                    
                    return $kvArray;
                    
                }
                
                
                function clean($string) {
                    $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.

                    return preg_replace('/[^A-Za-z0-9]/', '', $string); // Removes special chars.
                 }
}




/*MAIN*/
$fi= new fidelity();


$data = $fi->read_file_into_array();


foreach($data as $row)
{
    echo json_encode($row) . "\n";
    
    $rowArray = $fi->convertRowToKV($row);
    
    foreach($rowArray as $k=>$v)
    {
        echo $k . "=" . $v . "\n" ;
    }
    echo "-----------------\n";
}







?>