<?php
require __DIR__ . '/vendor/autoload.php';

class fidelity{
	
		public function read_file_into_array()
		{
                        $rowData = Array();
			$row=1;
			if (($handle = fopen("./documents/Portfolio_Positions_Jun-06-2025.csv", "r")) !== FALSE) {
				while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
					$num = count($data);
					//echo "<p> $num fields in line $row: <br /></p>\n";
					$row++;
					for ($c=0; $c < $num; $c++) {
                                                
						//echo $data[$c] . "<br />\n";
                                                array_push($rowData, $data);
					}
				}
				fclose($handle);
			}
			return $rowData;
		}
}


$fi= new fidelity();

$result = $fi->read_file_into_array();

echo $result[20][7];
?>