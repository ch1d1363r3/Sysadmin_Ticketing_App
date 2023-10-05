<?php
################################################################################
# @Name : parameters.php
# @Description : admin parameters
# @Call : /admin.php
# @Parameters : 
# @Author : Flox
# @Create : 12/01/2011
# @Update : 29/07/2021
# @Version : 3.2.15
################################################################################

//initialize variables 
if(!isset($extensionFichier)) $extensionFichier = '';
if(!isset($id_)) $id_ = '';
if(!isset($logo)) $logo = '';
if(!isset($filename)) $filename = '';
if(!isset($file_rename)) $file_rename = '';
if(!isset($mail_auto)) $mail_auto = '';
if(!isset($user_advanced)) $user_advanced= '';
if(!isset($mail_auth)) $mail_auth= '';
if(!isset($mail_secure)) $mail_secure= '';
if(!isset($nomorigine)) $nomorigine = '';
if(!isset($action)) $action = '';
if(!isset($error)) $error = '';

if(!isset($_POST['submit_general'])) $_POST['submit_general'] = '';
if(!isset($_POST['submit_connector'])) $_POST['submit_connector'] = '';
if(!isset($_POST['submit_function'])) $_POST['submit_function'] = '';

//default values
if($_GET['tab']=='') $_GET['tab'] = 'general';

//clean old files
if(file_exists('./fileupload.php')){unlink('./fileupload.php');}

//display system error 
if($rparameters['system_error']){echo DisplayMessage('error',T_("Des erreurs système importantes ont été détectées, corriger les points de couleur rouge affichés dans Administration > Système"));}

//clean local backup
if($_GET['action']!='backup' && !$_GET['download_backup'])
{
	$backup_zip_file  = glob('backup/*backup_gestsup*.{zip}', GLOB_BRACE);
	$backup_sql_file  = glob('_SQL/*backup-gestsup*.{sql}', GLOB_BRACE);
	//test if backup files exist
	if($backup_zip_file || $backup_sql_file)
	{
		//check if folder is writeable delete files
		if(is_writable('backup') && is_writable('_SQL'))
		{
			foreach($backup_zip_file as $file){unlink($file);}
			foreach($backup_sql_file as $file){unlink($file);}
		} else {
			echo DisplayMessage('error',T_('Les repertoires "backup" et "_SQL" ne sont pas accèssible en écriture et contiennent des sauvegardes locales à supprimer. Réaliser la suppression manuellement'));
		}
	}
}

?>
<div class="page-header position-relative">
	<h1 class="page-title text-primary-m2 ml-3" >
		<i class="fa fa-cog"><!----></i> <?php echo T_("Paramètres de l'application"); ?>
	</h1>
</div>
<div class="col-sm-12">	
	<div class="tabs-above shadow">
		<ul class="nav nav-tabs nav-justified" id="myTab">
			<li class="nav-item mr-1px">
				<a class="nav-link <?php if($_GET['tab']=='general') {echo 'active';} ?>" href="./index.php?page=admin&amp;subpage=parameters&amp;tab=general">
					<i class="fa fa-wrench text-success"><!----></i>
					<?php echo T_('Général'); ?>
				</a>
			</li>
			<li class="nav-item mr-1px">
				<a class="nav-link <?php if($_GET['tab']=='connector') {echo 'active';} ?>" href="./index.php?page=admin&amp;subpage=parameters&amp;tab=connector">
					<i class="fa fa-link text-primary-m2"><!----></i>
					<?php echo T_('Connecteurs'); ?>
				</a>
			</li>
			<li class="nav-item mr-1px">
				<a class="nav-link <?php if($_GET['tab']=='function') {echo 'active';} ?>" href="./index.php?page=admin&amp;subpage=parameters&amp;tab=function">
					<i class="fa fa-tasks text-warning"><!----></i>
					<?php echo T_('Fonctions'); ?>
				</a>
			</li>
			<li class="nav-item mr-1px">
				<a class="nav-link <?php if($_GET['tab']=='plugin') {echo 'active';} ?>" href="./index.php?page=admin&amp;subpage=parameters&amp;tab=plugin">
					<i class="fa fa-puzzle-piece text-purple"><!----></i>
					<?php echo T_('Plugins'); ?>
					<sup><?php echo T_('bêta'); ?></sup>
				</a>
			</li>
		</ul>
		<div class="tab-content" style="background-color:#FFF;">
			<?php 
				if($_GET['tab']=='general') {require('admin/parameters/general.php');}
				if($_GET['tab']=='connector') {require('admin/parameters/connector.php');}
				if($_GET['tab']=='function') {require('admin/parameters/function.php');}
				if($_GET['tab']=='plugin') {require('admin/parameters/plugin.php');}
			?>
		</div>
	</div>
</div>

<!-- parameters scripts  -->
<script type="text/javascript" src="js/parameters.js"></script>