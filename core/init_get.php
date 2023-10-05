<?php
################################################################################
# @Name : init_get.php
# @Description : init and secure all app var
# @Call : ./index.php
# @Parameters : 
# @Author : Flox
# @Create : 07/11/2019
# @Update : 10/05/2021
# @Version : 3.2.15 p1
################################################################################

//GET var definition
$all_get_var=array(
	'page',
	'id',
	'userid',
	'action',
	'keywords',
	'technician',
	'u_group',
	't_group',
	'ticket',
	'category',
	'subcat',
	'asset',
	'place',
	'service',
	'sender_service',
	'agency',
	'cursor',
	'searchengine',
	'company',
	'user',
	'date_create',
	'date_res',
	'date_modif',
	'date_hope',
	'date_start',
	'date_end',
	'date_range',
	'view',
	'state',
	'priority',
	'title',
	'criticality',
	'type',
	'place',
	'way',
	'order',
	'techread',
	'userread',
	'companyview',
	'techgroup',
	'userkeywords',
	'rightkeywords',
	'procedurekeywords',
	'download',
	'download_file',
	'download_backup',
	'subpage',
	'ldap',
	'disable',
	'tab',
	'assetkeywords',
	'profileid',
	'findip',
	'findip2',
	'iface',
	'sn_internal',
	'ip',
	'netbios',
	'user',
	'model',
	'description',
	'date_stock',
	'date_end_warranty',
	'department',
	'location',
	'virtual',
	'warranty',
	'delimg',
	'asset',
	'event',
	'hide',
	'planning',
	'token',
	'lang',
	'viewid',
	'warranty',
	'user_id',
	'key',
	'procedure',
	'edit',
	'delete_file',
	'task_action',
	'task_id',
	'threaddelete',
	'threadedit',
	'lock_thread',
	'unlock_thread',
	'cat',
	'editcat',
	'edituserid',
	'table',
	'ldaptest',
	'delete_imap_service',
	'deletequestion',
	'value',
	'profile',
	'object',
	'install_update',
	'deleteview',
	'attachmentdelete',
	'delete_assoc_service',
	'delete_assoc_agency',
	'fromnew',
	'iptoping',
	'scan',
	'month',
	'year',
	'userid',
	'time',
	'warranty_type',
	'warranty_time',
	'down',
	'hide_disabled_values',
	'sidebar_collapsed',
	'post'
);

//action on all get var
foreach($all_get_var as $get_var) {
	//init var
	if(!isset($_GET[$get_var])){$_GET[$get_var]='';}
	//secure var
    $_GET[$get_var]=htmlspecialchars($_GET[$get_var], ENT_QUOTES, 'UTF-8');
}
//check numeric id 
if($_GET['id'] && !is_numeric($_GET['id'])) {echo 'ERROR : incorrect value'; exit;}
//check page 
$page_white_list=array('dashboard','ticket','preview_mail','asset_list','asset','asset_stock','procedure','calendar','project','stat','admin','admin/user','changelog','register','forgot_pwd','plugin','test');
//include plugin
$section='page_white_list';
if(file_exists('plugin.php') && !preg_match('#db_name=\'\'#',file_get_contents('connect.php'))) {include('plugin.php');}
if(!in_array($_GET['page'],$page_white_list)) {$_GET['page']='';} 
//check subpage 
$subpage_white_list=array('system','infos','pwd_recovery','backup','group','profile','update','user','parameters','phpinfos','list','log');
if(!in_array($_GET['subpage'],$subpage_white_list)) {$_GET['subpage']='';} 
//order white list  
$order_white_list=array(
	'id',
	'technician',
	'company',
	'user',
	'type',
	'category',
	'subcat',
	'asset_id',
	'place',
	'service',
	'u_service',
	'u_agency',
	'title',
	'date_hope',
	'date_create',
	'date_res',
	'date_modif',
	'time',
	'criticality',
	'state',
	'tstates.number, tincidents.priority, tincidents.criticality, tincidents.date_create',
	'tstates.number, tincidents.priority, tincidents.criticality, tincidents.date_hope',
	'tstates.number, tincidents.date_hope, tincidents.priority, tincidents.criticality',
	'tstates.number, tincidents.date_hope, tincidents.criticality, tincidents.priority',
	'states.number, tincidents.criticality, tincidents.date_hope, tincidents.priority',
	'sn_internal',
	'tusers.lastname',
	'tassets_iface.ip',
	'tassets_iface.ip',
	'ABS(sn_internal)',
	'ip',
	'netbios',
	'tusers.lastname ASC, tusers.firstname',
	'tusers.lastname DESC, tusers.firstname',
	'model',
	'description',
	'department',
	'location',
	'date_stock',
	'date_end_warranty',
	'login',
	'tusers.lastname',
	'lastname',
	'tservices.name',
	'tusers.mail',
	'phone',
	'profile',
	'last_login',
	'',
);
if(!in_array($_GET['order'],$order_white_list)) {$_GET['order']='id';} 

//admin table white list
if($_GET['page']=='admin')
{
	$table_white_list=array(
		'tcategory',
		'tsubcat',
		'tagencies',
		'tservices',
		'tpriority',
		'tcriticality',
		'ttypes',
		'ttypes_answer',
		'tstates',
		'tplaces',
		'ttemplates',
		'tcompany',
		'ttime',
		'tassets_type',
		'tassets_manufacturer',
		'tassets_model',
		'tassets_state',
		'tassets_location',
		'tassets_iface_role',
		'tassets_network',
	);
	if(!in_array($_GET['table'],$table_white_list)) {$_GET['table']='tcategory';} 
}
?>