<?php


if ( !isset($_SESSION) )
        session_start();
    
if ( !isset($_SESSION["settings"]) )
    $_SESSION["settings"] = parse_ini_file("code/config.ini",true);

if ( true || $_SESSION["settings"]["debug"]["showPhpErrors"] )
{
    error_reporting(E_ALL);
    ini_set('display_errors', 'On');
}
    
if (!isset($_SESSION['currentUserId']))
    exit("Error");
else
    $userId = $_SESSION['currentUserId'];

include "../code/dbase.php";
    
Dbase::Connect();
$userInfo = Dbase::GetUserInfo($userId);

if ($userInfo['role_code'] != 'in')
    exit("Error");
    
$dump = Dbase::DumpCSV();
Dbase::Disconnect();
$content = $dump;
$length = strlen($content);

$filename = "youspeak_".date("Ymd")."_".date("His").".sql";

header('Content-Description: File Transfer');
header('Content-Type: text/plain');
header('Content-Disposition: attachment; filename='.$filename);
header('Content-Transfer-Encoding: binary');
header('Content-Length: ' . $length);
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Expires: 0');
header('Pragma: public');

exit ($content);

$res = mysql_query("SHOW TABLES");
while ( $row = mysql_fetch_array($res) )
    $tableNames[] = $row[0];
    
$tables = array();
    
foreach ($tableNames as $tableName)
{

    $tables[$tableName] = array();
    
    $res = mysql_query("SELECT * FROM $tableName");
    while ( $row = mysql_fetch_array($res) )
        $rows[] = $row;
    
    // column nmaes
    foreach ($rows[0] as $k=>$v)
        if (is_numeric($k)) continue;
        else $tables[$tableName][$k] = array();
    
    foreach ($rows as $i=>$row)
    {
        $rowValues[$i] = null;
        
        foreach ($row as $k=>$v)
            if (is_numeric($k)) continue;
            else $tables[$tableName][$k][] = $v;
    }
}

// table names in first row
foreach ($tables as $tName => $t)
foreach ($t as $cName => $c)
    echo "$tName,";

echo "\n";

// column names in 2nd row
foreach ($tables as $tName => $t)
foreach ($t as $cName => $c)
    echo "$cName,";

// transform data
$rows = array();

?>

