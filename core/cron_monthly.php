<?php
################################################################################
# @Name : cron_monthly.php
# @Description : execute tasks in time interval
# @Call : ./index.php
# @Parameters : 
# @Author : Flox
# @Create : 20/10/2020
# @Update : 14/04/2020
# @Version : 3.2.10
################################################################################

//update last execution time
if($rparameters['cron_monthly']!='12') {$new_month=$rparameters['cron_monthly']+1;} else {$new_month=1;}
$qry=$db->prepare("UPDATE `tparameters` SET `cron_monthly`=:cron_monthly");
$qry->execute(array('cron_monthly' => $new_month));

//auto clean logs
$qry=$db->prepare("DELETE FROM tlogs WHERE DATE(`date`) < (curdate() - INTERVAL 365 DAY)");
$qry->execute();
?>