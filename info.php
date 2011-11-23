<?php
/* creates a compressed zip file */
    $zip = new ZipArchive();
    if ($zip->open('test.zip',ZIPARCHIVE::CREATE) === TRUE) {
      $zip->addFile("dirt.m4a","dirt.m4a");
	  $zip->close();
    }
    
?>

