<?php
require __DIR__ . '/vendor/autoload.php';

class fidelity{
	
		public function read_file_into_array()
		{
                        print "Reading column headings into array";
                        $rowData = Array();
			$row=1;
			if (($handle = fopen("./documents/Portfolio_Positions_Jun-06-2025.csv", "r")) !== FALSE) {
				while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                                    array_push($rowData, $data);
				}
				fclose($handle);
			}
			return $rowData;
		}
                
                function clean($string) {
                    $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.

                    return preg_replace('/[^A-Za-z0-9]/', '', $string); // Removes special chars.
                 }
}




/*MAIN*/
$fi= new fidelity();


/*Parse column headers into Array, first row of file*/
$result = $fi->read_file_into_array();
$Headers = Array();
foreach($result[0] as $header)
{
    print "Header: " . $header ."\n";
    array_push($Headers,$fi->clean($header));
}


//for each stock in the list
$stockArray = Array();
for ($x = 1; $x <= sizeOf($result); $x++) {
    
    //for each stocks parameter (~ Header)
    foreach($result[$x] as $k=>$item)
    {
        print $Headers[$k] . "==> " . "" . $item ."";
        
    }
  
    print "\n";
}






?>