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


/*





can you interpret this quarterly report for me: SKYWATER TECHNOLOGY, INC. Condensed Consolidated Balance Sheets (Unaudited) March 30, 2025		December 29, 2024 (in thousands, except per share data) Assets			 Current assets			 Cash and cash equivalents	51,234	18,844 	 Accounts receivable (net of allowance for credit losses of $597 and $279, respectively) 39,108 			54,332 	 Contract assets (net of allowance for credit losses of $16 and $42, respectively) 20,466 			20,890 	 Inventory	14,221 			14,535 	 Prepaid expenses and other current assets	25,322 			23,476 	 Total current assets	150,351 			132,077 	 Property and equipment, net	162,842 			165,431 	 Intangible assets, net	7,786 			7,779 	 Other assets	5,784 			8,488 	 Total assets	326,763	313,775 	 Liabilities and shareholders’ equity			 Current liabilities			 Current portion of long-term debt	4,941	5,073 	 Accounts payable	11,691 			29,590 	 Accrued expenses	27,942 			36,829 	 Short-term financing, net of unamortized debt issuance costs	21,535 			27,669 	 Contract liabilities	61,215 			55,166 	 Total current liabilities	127,324 			154,327 	 Long-term liabilities			 Long-term debt, less current portion and net of unamortized debt issuance costs	33,693 			34,704 	 Long-term contract liabilities	97,264 			51,901 	 Deferred income tax liability, net	603 			632 	 Other long-term liabilities	8,443 			8,721 	 Total long-term liabilities	140,003 			95,958 	 Total liabilities	267,327 			250,285 	 Commitments and contingencies (Note 10)			 Shareholders’ equity			 Preferred stock, $0.01 par value per share (80,000 shares authorized; zero shares issued and outstanding as of March 30, 2025 and December 29, 2024) — 			— 	 Common stock, $0.01 par value per share (200,000 shares authorized; 48,037 and 47,704 shares issued and outstanding as of March 30, 2025 and December 29, 2024, respectively) 484 			478 	 Additional paid-in capital	192,264 			189,132 	 Accumulated deficit	(139,341)			(131,996)	 Total shareholders’ equity, SkyWater Technology, Inc.	53,407 			57,614 	 Noncontrolling interests	6,029 			5,876 	 Total shareholders’ equity	59,436 			63,490 	 Total liabilities and shareholders’ equity	326,763	313,775



interpreting 10-q results example:

💰 Assets
- Cash & cash equivalents surged from $18.8M to $51.2M, a sharp rise suggesting increased liquidity—possibly from operations, financing, or new investment.
- Accounts receivable dropped from $54.3M to $39.1M, which could indicate better collections or reduced sales volume.
- Inventory remained fairly steady, hinting that production or sales haven’t dramatically shifted.
- Prepaid expenses & other current assets saw a modest increase.
- Total current assets rose from $132.1M to $150.4M, showing stronger short-term positioning.
- Property & equipment slightly declined, likely due to depreciation.
- Other assets fell, which might include reduced deferred costs or investments.

🧾 Liabilities
- Accounts payable and accrued expenses dropped significantly—from $29.6M to $11.7M and $36.8M to $27.9M, respectively. This could mean the company paid off more bills or negotiated better terms.
- Short-term financing decreased, which may reflect debt repayments.
- Contract liabilities (revenues received before services rendered) increased—suggesting higher customer prepayments or deferred revenue.
- Long-term contract liabilities nearly doubled, from $51.9M to $97.3M—a major shift that could indicate growing backlog or long-term deals on the books.
- Overall total liabilities grew from $250.3M to $267.3M.

📉 Equity
- Additional paid-in capital increased slightly, likely due to stock issuance or equity compensation.
- Accumulated deficit widened from ($132.0M) to ($139.3M), meaning the company incurred more net losses this quarter.
- Total shareholders’ equity dipped from $63.5M to $59.4M, reflecting weaker bottom-line performance despite liquidity gains.




*/