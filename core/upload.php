<?php
################################################################################
# @Name : upload.php
# @Description : upload attached files 
# @Call : ticket.php
# @Parameters : 
# @Author : Flox
# @Create : 12/08/2013
# @Update : 14/12/2020
# @Version : 3.2.7
################################################################################

//initialize variables 
if(!isset($_FILES['file']['name'])) {$_FILES['file']['name']='';}

//create ticket directory if not exist
if(!is_dir("./upload/ticket"))  {mkdir('./upload/ticket', 0777, true);}

if($_FILES['file']['name'] && $_GET['id'])
{
	$real_filename=preg_replace("/[^A-Za-z0-9\_\-\.\s+]/", '', $_FILES['file']['name']);
    if(CheckFileExtension($real_filename)==true) {
		//create upload folder if not exist
        $target_folder='./upload/ticket/';
		//generate storage filename
		$storage_filename=$_GET['id'].'_'.md5(uniqid());
		//extract extension 
		$extension=new SplFileInfo($_FILES['file']['name']);
		$extension=$extension->getExtension();
		//if image file compressed it
		if(($extension=='jpg' || $extension=='jpeg') && extension_loaded('gd'))
		{
			if(compressImage($_FILES['file']['tmp_name'], $target_folder.$storage_filename, 70)) 
			{
				//content check
				$file_content = file_get_contents($target_folder.$storage_filename, true);
				if(preg_match('{\<\?php}',$file_content) || preg_match('/system\(/',$file_content)) {
					unlink($target_folder.$storage_filename); //remove file
					echo DisplayMessage('error',T_("Fichier interdit"));
				} else {
					$uid=md5(uniqid());
					$qry=$db->prepare("INSERT INTO `tattachments` (`uid`,`ticket_id`,`storage_filename`,`real_filename`) VALUES (:uid,:ticket_id,:storage_filename,:real_filename)");
					$qry->execute(array('uid' => $uid,'ticket_id' => $_GET['id'],'storage_filename' => $storage_filename,'real_filename' => $real_filename));
				} 
			} else {
				echo DisplayMessage('error',T_("Compression image impossible"));
			}
		} else {
			if(move_uploaded_file($_FILES['file']['tmp_name'], $target_folder.$storage_filename))
			{
				//content check
				$file_content = file_get_contents($target_folder.$storage_filename, true);
				if(preg_match('{\<\?php}',$file_content) || preg_match('/system\(/',$file_content)) {
					unlink($target_folder.$storage_filename); //remove file
					echo DisplayMessage('error',T_("Fichier interdit"));
					if($rparameters['log'] && is_numeric($_GET['id'])) {logit('security','File upload blocked on ticket '.$_GET['id'],$_SESSION['user_id']);}
				} else {
					$uid=md5(uniqid());
					$qry=$db->prepare("INSERT INTO `tattachments` (`uid`,`ticket_id`,`storage_filename`,`real_filename`) VALUES (:uid,:ticket_id,:storage_filename,:real_filename)");
					$qry->execute(array('uid' => $uid,'ticket_id' => $_GET['id'],'storage_filename' => $storage_filename,'real_filename' => $real_filename));
				} 
			} else {
				echo DisplayMessage('error',T_("Transfert fichier impossible"));
			}
		}
    } else {
		echo DisplayMessage('error',T_("Fichier interdit"));
		if($rparameters['log'] && is_numeric($_GET['id'])) {logit('security','Blacklisted file "'.$real_filename.'" blocked on ticket '.$_GET['id'],$_SESSION['user_id']);}
	}
} 
?>