<?php
################################################################################
# @Name : /plugin.php
# @Description : plugin system
# @Call : /*.php
# @Parameters : $section
# @Author : Flox
# @Create : 21/01/2021
# @Update : 07/04/2021
# @Version : 3.2.9
################################################################################

//connect db
if(!isset($db)) {require_once('connect.php');}

//foreach enabled plugin, add specific section
$qry2=$db->prepare("SELECT `name` FROM `tplugins` WHERE `enable`='1' ");
$qry2->execute();
while($plugin=$qry2->fetch()) 
{
    //login page
    if($section=='index' && $plugin['name']!='availability' && file_exists('plugins/'.$plugin['name'].'/index.php')) {include('plugins/'.$plugin['name'].'/index.php');}
    //page white list
    if($section=='page_white_list' && file_exists('plugins/'.$plugin['name'].'/page_white_list.php')) {include('plugins/'.$plugin['name'].'/page_white_list.php');} 
    //favicon
    if($section=='favicon' && file_exists('plugins/'.$plugin['name'].'/favicon.php')) {include('plugins/'.$plugin['name'].'/favicon.php');} 
    //breadcrumb
    if($section=='breadcrumb' && file_exists('plugins/'.$plugin['name'].'/breadcrumb.php')) {include('plugins/'.$plugin['name'].'/breadcrumb.php');}
    //menu
    if($section=='menu' && file_exists('plugins/'.$plugin['name'].'/menu.php')) {include('plugins/'.$plugin['name'].'/menu.php');} 
    //ticket form
    if($section=='ticket_form' && file_exists('plugins/'.$plugin['name'].'/ticket.php')) {include('plugins/'.$plugin['name'].'/ticket.php');} 
    //ticket js
    if($section=='ticket_js' && file_exists('plugins/'.$plugin['name'].'/js/ticket.js')) {echo '<script type="text/javascript" src="plugins/'.$plugin['name'].'/js/ticket.js"></script>';} 
    //ticket core
    if($section=='ticket_core' && file_exists('plugins/'.$plugin['name'].'/core/ticket.php')) {include('plugins/'.$plugin['name'].'/core/ticket.php');} 
     //parameters connector
     if($section=='connector' && file_exists('plugins/'.$plugin['name'].'/admin/parameters/connector.php')) {include('plugins/'.$plugin['name'].'/admin/parameters/connector.php');} 
}
$qry2->closeCursor();
?>