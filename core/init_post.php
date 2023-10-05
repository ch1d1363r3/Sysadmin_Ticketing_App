<?php
################################################################################
# @Name : init_post.php
# @Description : init and secure all app var
# @Call : 
# @Parameters : 
# @Author : Flox
# @Create : 08/11/2019
# @Update : 14/09/2021
# @Version : 3.2.15 p3
################################################################################

//POST var definition
$all_post_var=array(
	'date',
	'selectrow',
	'ticket',
	'technician',
	'technician_group',
	'title',
	'description',
	'resolution',
	'Submit',
	'userid',
	'company',
	'user',
	'category',
	'subcat',
	'asset',
	'place',
	'service',
	'u_service',
	'sender_service',
	'agency',
	'date_create',
	'date_hope',
	'date_res',
	'date_start',
	'date_end',
	'state',
	'priority',
	'criticality',
	'type',
	'u_group',
	't_group',
	'Modifier',
	'Ajouter',
	'cat',
	'model',
	'ip',
	'wifi',
	'manufacturer',
	'name',
	'confirm',
	'number',
	'observer',
	'observer1',
	'observer2',
	'observer3',
	'billable',
	'keywords',
	'date_modif',
	'mail',
	'upload',
	'type_answer',
	'modify',
	'quit',
	'quit',
	'time_hope',
	'cancel',
	'ticket_places',
	'private',
	'asset_id',
	'u_agency',
	'addcalendar',
	'addevent',
	'user_validation',
	'user_validation_date',
	'time'
);

//action on all post var
foreach($all_post_var as $post_var) {
	//init var
	if(!isset($_POST[$post_var])){$_POST[$post_var]='';}
	//secure var
	if($_GET['table']!='tservices') {$_POST[$post_var]=htmlspecialchars($_POST[$post_var], ENT_QUOTES, 'UTF-8');} // bug ldap sync service #4995
}


//init
if(!isset($_POST['text'])){$_POST['text']='';}
if(!isset($_POST['text2'])){$_POST['text2']='';}

//secure
$_POST['text']=str_replace('<script>','',$_POST['text']);
$_POST['text2']=str_replace('<script>','',$_POST['text2']);

if(preg_match('/<script>/', strtolower($_POST['text']))) {echo $_POST['text']='';}
if(preg_match('/<script>/', strtolower($_POST['text2']))) {echo $_POST['text2']='';}

?>