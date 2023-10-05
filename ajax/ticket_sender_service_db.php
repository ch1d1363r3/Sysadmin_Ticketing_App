<?php
################################################################################
# @Name : ticket_sender_service_db.php
# @Description : send service of current user selected on ticket to populate sender service field
# @Call : ./ticket.php
# @Parameters :  
# @Author : Flox
# @Create : 19/09/2021
# @Update : 19/09/2021
# @Version : 3.2.14 p1
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
    $service_arr=array();

    //secure string
    $_POST['UserId']=htmlspecialchars($_POST['UserId'], ENT_QUOTES, 'UTF-8');

    //put each db value in array 
    $qry=$db->prepare("SELECT `id`,`name` FROM `tservices` WHERE `id` IN (SELECT `service_id` FROM `tusers_services` WHERE `user_id`=:user_id) ORDER BY `name`");
    $qry->execute(array('user_id' => $_POST['UserId']));
    while($service=$qry->fetch()) 
    {
       $service_arr[] = array("id" => $service['id'], "name" => $service['name']);
    }
    $qry->closeCursor();
    //return array
    echo json_encode($service_arr);

    //close database access
	$db = null;
} else {
	echo json_encode(array("status" => "failed"));
}
?>