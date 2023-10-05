<?php
################################################################################
# @Name : /core/calendar.php
# @Description : update event in db
# @Call : /calendar.php
# @Parameters : 
# @Author : Flox
# @Create : 19/02/2018
# @Update : 14/04/2021
# @Version : 3.2.15 p2
################################################################################

//security check
if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') 
{

	//db connection
	require "./../connect.php";
	require_once(__DIR__."/../core/init_get.php");

	//initialize variable
	if(!isset($_POST['title'])) $_POST['title'] = '';
	if(!isset($_POST['allday'])) $_POST['allday'] = '';

	//secure variable
	$_POST['title']=htmlspecialchars($_POST['title']);

	$db->exec('SET sql_mode = ""');

	if($_POST['action']=='update_title')
	{
		$qry=$db->prepare("UPDATE tevents SET title=:title WHERE id=:id");
		$qry->execute(array(':title'=>htmlspecialchars_decode($_POST['title'], ENT_QUOTES),':id'=>$_POST['id']));
	}
	if($_POST['action']=='move_event' || $_POST['action']=='resize_event') {
		$qry=$db->prepare("UPDATE tevents SET title=:title, date_start=:date_start, date_end=:date_end, allday=:allday WHERE id=:id");
		$qry->execute(array(':title'=>htmlspecialchars_decode($_POST['title'], ENT_QUOTES),':date_start'=>$_POST['start'],':date_end'=>$_POST['end'],':allday'=>$_POST['allday'],':id'=>$_POST['id']));
	} 
	if($_POST['action']=='delete_event')
	{
		$qry = $db->prepare("DELETE FROM tevents WHERE id=:id");
		$qry->execute(array(':id'=>$_POST['id']));
	}
	if($_POST['action']=='add_event')
	{
		$qry = $db->prepare("INSERT INTO tevents (technician,title, date_start, date_end, allday, className) VALUES (:technician, :title, :start, :end, :allday, 'badge-primary')");
		$qry->execute(array(':technician'=>$_POST['technician'],':title'=>htmlspecialchars_decode($_POST['title']), ':start'=>$_POST['start'], ':end'=>$_POST['end'], ':allday'=>$_POST['allday']));
		echo json_encode(array("event_id" => $db->lastInsertId()));
	}
} else {
	echo json_encode(array("status" => "failed"));
}
?>