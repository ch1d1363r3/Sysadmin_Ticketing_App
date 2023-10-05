<?php
################################################################################
# @Name : ticket_asset_db.php
# @Description : send asset of current user selected on ticket to populate asset field
# @Call : ./ticket.php
# @Parameters :  
# @Author : Flox
# @Create : 24/11/2020
# @Update : 24/11/2020
# @Version : 3.2.6
################################################################################

//security check
if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') 
{
    //db connection
    require('../connect.php');

    //switch SQL MODE to allow empty values
    $db->exec('SET sql_mode = ""');

    //load parameters table
    $qry=$db->prepare("SELECT * FROM `tparameters`");
    $qry->execute();
    $rparameters=$qry->fetch();
    $qry->closeCursor();

    //display error parameter
    if($rparameters['debug']) {
        ini_set('display_errors', 'On');
        ini_set('display_startup_errors', 'On');
        ini_set('html_errors', 'On');
        error_reporting(E_ALL);
    } else {
        ini_set('display_errors', 'Off');
        ini_set('display_startup_errors', 'Off');
        ini_set('html_errors', 'Off');
        error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);
    }

    //init string
    if(!isset($_POST['UserId'])) $_POST['UserId'] = '';
    $asset_arr=array();

    //secure string
    $_POST['UserId']=htmlspecialchars($_POST['UserId'], ENT_QUOTES, 'UTF-8');

    //put each db value in array 
    $qry=$db->prepare("SELECT `id`,`netbios` FROM `tassets` WHERE `user`=:user ORDER BY `netbios`");
    $qry->execute(array('user' => $_POST['UserId']));
    while($asset=$qry->fetch()) 
    {
       $asset_arr[] = array("id" => $asset['id'], "netbios" => $asset['netbios']);
    }
    $qry->closeCursor();
    //return array
    echo json_encode($asset_arr);

    //close database access
	$db = null;
} else {
	echo json_encode(array("status" => "failed"));
}
?>