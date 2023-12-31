<?php
################################################################################
# @Name : connect.php
# @Description : database connection parameters
# @Call : 
# @Parameters : 
# @Author : Flox
# @Create : 07/03/2007
# @Update : 18/03/2020
# @Version : 3.2.0
################################################################################

//database connection parameters
$host='localhost'; //SQL server name
$port='3306'; //SQL server port
$db_name=''; //database name
$charset='utf8'; //database charset default utf8
$user=''; //database user name
$password=''; //database password

//database connection
try {$db = new PDO("mysql:host=$host;port=$port;dbname=$db_name;charset=$charset", "$user", "$password" , array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));}
catch (Exception $e)
{die('Error : ' . $e->getMessage());}
?>