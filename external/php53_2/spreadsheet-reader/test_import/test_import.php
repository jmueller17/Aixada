<?php
/**
 * Parsing files to test `spredsheet-reader` works
 *
 *  * /spreadsheet-reader/test_import/test_import.php?File=test_files/test_file.csv
 *  * /spreadsheet-reader/test_import/test_import.php?File=test_files/test_file.xls
 *  * /spreadsheet-reader/test_import/test_import.php?File=test_files/test_file.xlsx
 *  * /spreadsheet-reader/test_import/test_import.php?File=test_files/test_file.ods
 *
 */
require('../php-excel-reader/excel_reader2.php');
require('../SpreadsheetReader.php');
error_reporting(E_ALL);

$log_path = '';
foreach (array('.csv','.ods','.xls','.xlsx') as $v) {
// foreach (['.xlsx',] as $v) {    
    $file_path = 'test_files/test_file' . $v;
    
    $log_path = 'test_logs/test_file' . $v . '.log';
    
    echo "<pre>\n";
    echo_write('============================================'.PHP_EOL, true);
    echo_write("File: >" . $file_path . "<" . PHP_EOL);
    
    test_import_file($file_path);
    
    echo "</pre>\n";
}

// -------------------------------

function echo_write($content, $start = false) {
    echo $content;
    log_write($content, $start);
}
function log_write($content, $start = false) {   
    global $log_path;
    if ($start) {
        $path = pathinfo($log_path);
        if (!file_exists($path['dirname'])) {
            mkdir($path['dirname'], 0777, true);
        }
    }
    file_put_contents($log_path, $content, ($start ? LOCK_EX : FILE_APPEND | LOCK_EX));
}

function test_import_file($file_path) {
    try
    {
        $Spreadsheet = new SpreadsheetReader($file_path);

        $Sheets = $Spreadsheet -> Sheets();
        foreach ($Sheets as $Index => $Name)
        {
            echo_write('---------------------------------'.PHP_EOL);
            echo_write('*** Sheet >'.$Name.'< *** ' . count($Spreadsheet) . ' rows ***'.PHP_EOL);

            $Spreadsheet -> ChangeSheet($Index);
            foreach ($Spreadsheet as $Key => $Row)
            {
                log_write('Row-' . $Key.': ');
                log_write(str_replace("\n", PHP_EOL, 
                    var_export(
                    array_slice($Row, 0, 5, true),
                    true)
                ));
                log_write(PHP_EOL);
            }
            
            echo_write( '*** End of sheet >'.$Name.'< ***'.PHP_EOL);
        }
        
    }
    catch (Exception $E)
    {
        echo_write( $E -> getMessage());
    }
}
?>
