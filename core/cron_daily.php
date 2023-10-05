<?php
################################################################################
# @Name : cron_daily.php
# @Description : execute tasks in time interval
# @Call : ./index.php
# @Parameters : 
# @Author : Flox
# @Create : 09/05/2019
# @Update : 27/05/2021
# @Version : 3.2.12
################################################################################

//update last execution time
$new_date=date_create($rparameters['cron_daily']);
date_add($new_date, date_interval_create_from_date_string('1 days'));
$new_date=date_format($new_date, 'Y-m-d');
$qry=$db->prepare("UPDATE `tparameters` SET `cron_daily`=:cron_daily");
$qry->execute(array('cron_daily' => $new_date));

//autoclose ticket parameter
if($rparameters['ticket_autoclose']) {require('./core/auto_close.php');}

//user validation parameter
if($rparameters['user_validation']) {require('./core/user_validation.php');}

//auto clean token 1 day old
$qry=$db->prepare("DELETE FROM `ttoken` WHERE `date` <= DATE_SUB(NOW(), INTERVAL 1 DAY);");
$qry->execute();

//clean login ip attempt
$qry=$db->prepare("DELETE FROM `tauth_attempts`");
$qry->execute();
?>