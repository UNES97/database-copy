<?php

$sourceHost = 'localhost';
$sourceUser = 'src_user';
$sourcePassword = 'src_pass';
$sourceDatabase = 'src_db';

$destinationHost = 'localhost';
$destinationUser = 'dest_user';
$destinationPassword = 'dest_pass';
$destinationDatabase = 'dest_db';


$source = mysqli_connect($sourceHost, $sourceUser, $sourcePassword); // Cnx server I
mysqli_select_db($source , $sourceDatabase); // DB I (Src)

$destination = mysqli_connect($destinationHost, $destinationUser, $destinationPassword); // Cnx server II
mysqli_select_db($destination , $destinationDatabase); // DB II (Src)

$tables = mysqli_query($source , "SHOW TABLES");

foreach ($tables as $table) {


    // Get the table structure from the Src and create it on Dest server
    $tableInfo = mysqli_fetch_array(mysqli_query($source , "SHOW CREATE TABLE `{$table['Tables_in_source_db']}`  "));
    mysqli_query( $destination , " $tableInfo[1] ");


    mysqli_query($destination , "TRUNCATE TABLE `{$table['Tables_in_source_db']}`");

    // Copy data from Src to Dest
    $result = mysqli_query($source , "SELECT * FROM `{$table['Tables_in_source_db']}`  "); // select Content		

    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
        mysqli_query($destination , "INSERT INTO `{$table['Tables_in_source_db']}` (" . implode(", ", array_keys($row)) . ") VALUES ('" . implode("', '", array_values($row)) . "')");
    }

}

// Close connections
mysqli_close($source);
mysqli_close($destination); 


