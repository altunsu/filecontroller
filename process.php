#!/usr/bin/php
<?php
#### Get file name to process
$username="****credentials****";
$password="****credentials****";
$database="****credentials****";
$conn = mysql_connect('****credentials***',$username,$password);
@mysql_select_db($database) or die( "Unable to select database");

###### Tell the controller I am busy 
$timenow = date("Y-m-d H:i:s");
$cnt_in = 'UPDATE control SET process_1="1",process_i="'.$timenow.'",process_o="1111-11-11 11:11:11"  where id="1"';
$cnt_in_1 = mysql_query($cnt_in,$conn);

if(! $cnt_in_1 )
{
  goto finish;
}

######### Controller done #######
$query = 'SELECT * from tmp_data ORDER BY id ASC';
$zelda = mysql_query($query,$conn);
$row = mysql_fetch_assoc($zelda);

$betax = count($row['file']);

if( $betax == 0)
{
goto finish;
}

$file_name = $row['file'];
$get_week = substr($file_name,13,12);
$date = new DateTime($get_week);
$week = $date->format("W");
$cdb_name = date("Y")."_".$week;
if(mysql_select_db($cdb_name)){goto wedontneedb;}else{$sql_db  = "CREATE DATABASE $cdb_name;";if (mysql_query($sql_db, $conn)){}else{echo "Error creating table: " . mysql_error($conn);}}

mysql_select_db($cdb_name);
$tsql ="CREATE TABLE main_bck (".
"a_rate double NOT NULL,".
"b_rate double NOT NULL,".
"setup_date datetime NOT NULL,".
"a_ip varchar(15) COLLATE utf8_bin NOT NULL,".
"b_ip varchar(15) COLLATE utf8_bin NOT NULL,".
"a_number varchar(40) COLLATE utf8_bin NOT NULL,".
"b_number varchar(40) COLLATE utf8_bin NOT NULL,".
"a_tech varchar(20) COLLATE utf8_bin NOT NULL,".
"b_tech varchar(20) COLLATE utf8_bin NOT NULL,".
"cnt_time datetime NOT NULL,".
"dsc_time datetime NOT NULL,".
"hangup_cause int(4) NOT NULL,".
"sip_code int(4) NOT NULL,".
"KEY a_rate (a_rate),".
"KEY b_rate (b_rate),".
"KEY setup_date (setup_date),".
"KEY a_number (a_number),".
"KEY b_number (b_number),".
"KEY a_tech (a_tech),".
"KEY b_tech (b_tech),".
"KEY cnt_time (cnt_time),".
"KEY dsc_time (dsc_time),".
"KEY hangup_cause (hangup_cause),".
"KEY sip_code (sip_code)".
") ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin".
"/*!50100 PARTITION BY RANGE (DAYOFWEEK(setup_date))".
"(PARTITION p001 VALUES LESS THAN (2) ENGINE = InnoDB,".
"PARTITION p002 VALUES LESS THAN (3) ENGINE = InnoDB,".
"PARTITION p003 VALUES LESS THAN (4) ENGINE = InnoDB,".
"PARTITION p004 VALUES LESS THAN (5) ENGINE = InnoDB,".
"PARTITION p005 VALUES LESS THAN (6) ENGINE = InnoDB,".
"PARTITION p006 VALUES LESS THAN (7) ENGINE = InnoDB,".
"PARTITION p007 VALUES LESS THAN (8) ENGINE = InnoDB)*/;";
if (mysql_query($tsql, $conn)){}else{echo "Error creating table: " . mysql_error($conn);}
sleep('3');
wedontneedb:

#### Rename to file for process_begining
$rename_file = shell_exec('mv /fbin/tmp_CDR/'.$row['file'].'  /fbin/tmp_CDR/'.$row['file'].'XX');

#### Prepare data for process
$prepare1 = 'cat /fbin/tmp_CDR/'.$row['file'].'XX | awk \'BEGIN{FS=";";OFS=",";ORS="\n"} {print $9,$10,$15,$18,$20,$32,$23,$55,$70,$120,$121,$59,$74}\' > /fbin/tmp_CDR/'.$row['file'].'YY ';
$tanya = shell_exec($prepare1);

#### Strip data from buggies
$clear1 = 'sed \'s/\\\\//g\' /fbin/tmp_CDR/'.$row['file'].'YY > /fbin/tmp_CDR/'.$row['file'].'WW ';
$clear_data = shell_exec($clear1);

#### insert file to db
$query5 = "LOAD DATA LOCAL INFILE '/fbin/tmp_CDR/".$row['file']."WW' INTO TABLE main_bck FIELDS TERMINATED BY ',' LINES TERMINATED BY '\n'";
$herkul = mysql_query($query5,$conn);
  
if(! $herkul )
 {
    goto finish;
 }

mysql_select_db($database);
#### Original CDR file is ready to compress
$query3 = "INSERT INTO  comp_data  (id,file,sha) VALUES (null,'$row[file]XX','$row[sha]')" ;
$lara = mysql_query($query3,$conn);
  
if(! $lara )
 {
    goto finish;;
 }
 
#### delete file_name from list
$query4="DELETE FROM tmp_data where file='".$row['file']."' ";
$peta = mysql_query($query4,$conn);
  
if(! $peta )
 {
    goto finish;
 }
##### Delete file name from list done

#### delete file
$delete_WW = shell_exec('rm /fbin/tmp_CDR/'.$row['file'].'WW');
$delete_YY = shell_exec('rm /fbin/tmp_CDR/'.$row['file'].'YY');


$query2 = "UPDATE FileOnHole SET state='p' WHERE name='".$row['file']."'";
$tanya = mysql_query($query2,$conn);
if(! $tanya)
{
  goto finish;
}

finish:
###### Tell the controller I am free 
$timenow = date("Y-m-d H:i:s");
$cnt_in = 'UPDATE control SET process_1="0",process_i="1111-11-11 11:11:11",process_o="'.$timenow.'"  where id="1"';
$cnt_in_1 = mysql_query($cnt_in,$conn);

if(! $cnt_in_1 )
{
}

mysql_close($conn);

?>