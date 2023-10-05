<?php
################################################################################
# @Name : ./core/auto_close.php
# @Description : automatic close ticket
# @Call : ./core/cron.php
# @Parameters : 
# @Author : Flox
# @Create : 16/10/2020
# @Update : 16/10/2020
# @Version : 3.2.5
################################################################################

if($rparameters['ticket_autoclose'] && $rparameters['ticket_autoclose_delay']!=0)
{
	if($rparameters['ticket_autoclose_state']==6) //case ticket in state wait user
	{
		$qry=$db->prepare("SELECT `id` FROM `tincidents` WHERE date_create<(NOW() - INTERVAL :delay DAY) AND `state`='6' AND `disable`='0'");
		$qry->execute(array('delay' => $rparameters['ticket_autoclose_delay']));
		while($row=$qry->fetch()) 
		{
			//modify state to 3
			$qry2=$db->prepare("UPDATE `tincidents` SET `state`='3',`date_res`=:date_res  WHERE `id`=:id");
			$qry2->execute(array('id' => $row['id'],'date_res' => date('Y-m-d H:i:s')));
			//insert close thread
			$qry2=$db->prepare("INSERT INTO `tthreads` (`ticket`,`date`,`author`,`type`) VALUES (:ticket,:date,'0','4')");
			$qry2->execute(array('ticket' => $row['id'],'date' => date('Y-m-d H:i:s')));
			//send notifications mails
			if($rparameters['mail_auto'])
			{
				$autoclose=1;
				$_GET['id']= $row['id'];
				require('core/auto_mail.php');
			}
		}
		$qry->closeCursor();
	} else { //case for all states
		$qry=$db->prepare("SELECT `id` FROM `tincidents` WHERE date_create<(NOW() - INTERVAL :delay DAY) AND `state`!='3' AND `disable`='0'");
		$qry->execute(array('delay' => $rparameters['ticket_autoclose_delay']));
		while($row=$qry->fetch()) 
		{
			//modify state to 3
			$qry2=$db->prepare("UPDATE `tincidents` SET `state`='3',`date_res`=:date_res  WHERE `id`=:id");
			$qry2->execute(array('id' => $row['id'],'date_res' => date('Y-m-d H:i:s')));
			//insert close thread
			$qry2=$db->prepare("INSERT INTO `tthreads` (`ticket`,`date`,`author`,`type`) VALUES (:ticket,:date,'0','4')");
			$qry2->execute(array('ticket' => $row['id'],'date' => date('Y-m-d H:i:s')));
			//send notifications mails
			if($rparameters['mail_auto'])
			{
				$autoclose=1;
				$_GET['id']= $row['id'];
				require('core/auto_mail.php');
			}
		}
		$qry->closeCursor();
	}
}
?>