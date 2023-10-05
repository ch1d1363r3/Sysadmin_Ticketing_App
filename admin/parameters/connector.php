<?php
################################################################################
# @Name : connector.php
# @Description : admin connector parameters
# @Call : /admin/parameters.php
# @Parameters : 
# @Author : Flox
# @Create : 22/09/2020
# @Update : 24/08/2021
# @Version : 3.2.15 p1
################################################################################

//initialize variables 
if(!isset($_POST['mail'])) $_POST['mail']= '';
if(!isset($_POST['mail_username'])) $_POST['mail_username'] = '';
if(!isset($_POST['mail_password'])) $_POST['mail_password'] = '';
if(!isset($_POST['mail_secure'])) $_POST['mail_secure'] = '';
if(!isset($_POST['mail_auth'])) $_POST['mail_auth']= '';
if(!isset($_POST['mail_smtp'])) $_POST['mail_smtp']= '';
if(!isset($_POST['mail_smtp_class'])) $_POST['mail_smtp_class']= '';
if(!isset($_POST['mail_port'])) $_POST['mail_port']= '';
if(!isset($_POST['mail_ssl_check'])) $_POST['mail_ssl_check']= '';

if(!isset($_POST['ldap'])) $_POST['ldap']= '';
if(!isset($_POST['ldap_auth'])) $_POST['ldap_auth']= '';
if(!isset($_POST['ldap_sso'])) $_POST['ldap_sso']= '';
if(!isset($_POST['ldap_type'])) $_POST['ldap_type']= '';
if(!isset($_POST['ldap_login_field'])) $_POST['ldap_login_field']= '';
if(!isset($_POST['ldap_service'])) $_POST['ldap_service']= '';
if(!isset($_POST['ldap_service_url'])) $_POST['ldap_service_url']= '';
if(!isset($_POST['ldap_agency'])) $_POST['ldap_agency']= '';
if(!isset($_POST['ldap_agency_url'])) $_POST['ldap_agency_url']= '';
if(!isset($_POST['ldap_server'])) $_POST['ldap_server']= '';
if(!isset($_POST['ldap_server_url'])) $_POST['ldap_server_url']= '';
if(!isset($_POST['ldap_port'])) $_POST['ldap_port']= '';
if(!isset($_POST['ldap_domain'])) $_POST['ldap_domain']= '';
if(!isset($_POST['ldap_url'])) $_POST['ldap_url']= '';
if(!isset($_POST['ldap_user'])) $_POST['ldap_user']= '';
if(!isset($_POST['ldap_password'])) $_POST['ldap_password']= '';
if(!isset($_POST['ldap_disable_user'])) $_POST['ldap_disable_user']= '';
if(!isset($_POST['test_ldap'])) $_POST['test_ldap']= '';
if(!isset($_POST['dest_agency'])) $_POST['dest_agency']= '';
if(!isset($_POST['from_agency'])) $_POST['from_agency']= '';

if(!isset($_POST['imap'])) $_POST['imap']= '';
if(!isset($_POST['inbox'])) $_POST['inbox']= '';
if(!isset($_POST['imap_server'])) $_POST['imap_server']= '';
if(!isset($_POST['imap_port'])) $_POST['imap_port']= '';
if(!isset($_POST['imap_user'])) $_POST['imap_user']= '';
if(!isset($_POST['imap_ssl_check'])) $_POST['imap_ssl_check']= '';
if(!isset($_POST['imap_password'])) $_POST['imap_password']= '';
if(!isset($_POST['imap_reply'])) $_POST['imap_reply']= '';
if(!isset($_POST['imap_blacklist'])) $_POST['imap_blacklist']= '';
if(!isset($_POST['imap_post_treatment'])) $_POST['imap_post_treatment']= '';
if(!isset($_POST['imap_post_treatment_folder'])) $_POST['imap_post_treatment_folder']= '';
if(!isset($_POST['imap_mailbox_service'])) $_POST['imap_mailbox_service']= '';
if(!isset($_POST['imap_from_adr_service'])) $_POST['imap_from_adr_service']= '';
if(!isset($_POST['imap_inbox'])) $_POST['imap_inbox']= '';
if(!isset($_POST['mailbox_service'])) $_POST['mailbox_service']= '';
if(!isset($_POST['mailbox_service_id'])) $_POST['mailbox_service_id']= '';
if(!isset($_POST['plugin_connector'])) $_POST['plugin_connector']= '';

//delete imap mailbox service association
if($rparameters['imap_mailbox_service'] && $_GET['delete_imap_service'] && $rright['admin'])
{
	$qry=$db->prepare("DELETE FROM tparameters_imap_multi_mailbox WHERE id=:id");
	$qry->execute(array('id' => $_GET['delete_imap_service']));
}

if($_POST['submit_connector'] || $_POST['test_ldap'])
{
	//secure string
	$_POST['mail_smtp']=strip_tags($_POST['mail_smtp']);
	$_POST['mail_smtp']=str_replace('|','',$_POST['mail_smtp']);
	$_POST['mail_username']=strip_tags($_POST['mail_username']);
	$_POST['mail_password']=strip_tags($_POST['mail_password']);
	$_POST['ldap_server']=strip_tags($_POST['ldap_server']);
	$_POST['ldap_server']=str_replace('|','',$_POST['ldap_server']);
	$_POST['ldap_domain']=strip_tags($_POST['ldap_domain']);
	$_POST['ldap_user']=strip_tags($_POST['ldap_user']);
	$_POST['ldap_login_field']=strip_tags($_POST['ldap_login_field']);
	$_POST['ldap_password']=strip_tags($_POST['ldap_password']);
	$_POST['imap_server']=strip_tags($_POST['imap_server']);
	$_POST['imap_server']=str_replace('|','',$_POST['imap_server']);
	$_POST['imap_user']=strip_tags($_POST['imap_user']);
	$_POST['imap_password']=strip_tags($_POST['imap_password']);
	$_POST['imap_blacklist']=strip_tags($_POST['imap_blacklist']);
	
	$qry=$db->prepare("
	UPDATE `tparameters` SET 
	`mail`=:mail,
	`mail_smtp`=:mail_smtp, 
	`mail_smtp_class`=:mail_smtp_class, 
	`mail_port`=:mail_port, 
	`mail_ssl_check`=:mail_ssl_check, 
	`mail_secure`=:mail_secure, 
	`mail_auth`=:mail_auth, 
	`mail_username`=:mail_username, 
	`mail_password`=:mail_password, 
	`ldap`=:ldap, 
	`ldap_auth`=:ldap_auth, 
	`ldap_sso`=:ldap_sso, 
	`ldap_type`=:ldap_type, 
	`ldap_service`=:ldap_service, 
	`ldap_service_url`=:ldap_service_url, 
	`ldap_login_field`=:ldap_login_field, 
	`ldap_agency`=:ldap_agency, 
	`ldap_agency_url`=:ldap_agency_url, 
	`ldap_server`=:ldap_server, 
	`ldap_port`=:ldap_port, 
	`ldap_user`=:ldap_user, 
	`ldap_password`=:ldap_password, 
	`ldap_domain`=:ldap_domain, 
	`ldap_url`=:ldap_url, 
	`ldap_disable_user`=:ldap_disable_user, 
	`imap`=:imap, 
	`imap_server`=:imap_server, 
	`imap_port`=:imap_port, 
	`imap_ssl_check`=:imap_ssl_check, 
	`imap_user`=:imap_user, 
	`imap_password`=:imap_password, 
	`imap_reply`=:imap_reply, 
	`imap_blacklist`=:imap_blacklist, 
	`imap_post_treatment`=:imap_post_treatment, 
	`imap_post_treatment_folder`=:imap_post_treatment_folder, 
	`imap_mailbox_service`=:imap_mailbox_service, 
	`imap_from_adr_service`=:imap_from_adr_service, 
	`imap_inbox`=:imap_inbox
	WHERE `id`=:id
	");
	$qry->execute(array(
		'mail' => $_POST['mail'],
		'mail_smtp' => $_POST['mail_smtp'],
		'mail_smtp_class' => $_POST['mail_smtp_class'],
		'mail_port' => $_POST['mail_port'],
		'mail_ssl_check' => $_POST['mail_ssl_check'],
		'mail_secure' => $_POST['mail_secure'],
		'mail_auth' => $_POST['mail_auth'],
		'mail_username' => $_POST['mail_username'],
		'mail_password' => $_POST['mail_password'],
		'ldap' => $_POST['ldap'],
		'ldap_auth' => $_POST['ldap_auth'],
		'ldap_sso' => $_POST['ldap_sso'],
		'ldap_type' => $_POST['ldap_type'],
		'ldap_service' => $_POST['ldap_service'],
		'ldap_service_url' => $_POST['ldap_service_url'],
		'ldap_login_field' => $_POST['ldap_login_field'],
		'ldap_agency' => $_POST['ldap_agency'],
		'ldap_agency_url' => $_POST['ldap_agency_url'],
		'ldap_server' => $_POST['ldap_server'],
		'ldap_port' => $_POST['ldap_port'],
		'ldap_user' => $_POST['ldap_user'],
		'ldap_password' => $_POST['ldap_password'],
		'ldap_domain' => $_POST['ldap_domain'],
		'ldap_url' => $_POST['ldap_url'],
		'ldap_disable_user' => $_POST['ldap_disable_user'],
		'imap' => $_POST['imap'],
		'imap_server' => $_POST['imap_server'],
		'imap_port' => $_POST['imap_port'],
		'imap_ssl_check' => $_POST['imap_ssl_check'],
		'imap_user' => $_POST['imap_user'],
		'imap_password' => $_POST['imap_password'],
		'imap_reply' => $_POST['imap_reply'],
		'imap_blacklist' => $_POST['imap_blacklist'],
		'imap_post_treatment' => $_POST['imap_post_treatment'],
		'imap_post_treatment_folder' => $_POST['imap_post_treatment_folder'],
		'imap_mailbox_service' => $_POST['imap_mailbox_service'],
		'imap_from_adr_service' => $_POST['imap_from_adr_service'],
		'imap_inbox' => $_POST['imap_inbox'],
		'id' => '1'
		));
	
	//move ticket from agency to another if detected
	if($rparameters['user_agency']==1 && $_POST['from_agency'] && $_POST['dest_agency'])
	{
		//secure string
		$_POST['dest_agency']=strip_tags($_POST['dest_agency']);
		$_POST['from_agency']=strip_tags($_POST['from_agency']);
	
		$qry=$db->prepare("UPDATE `tincidents` SET `u_agency`=:u_agency1 WHERE `u_agency`=:u_agency2");
		$qry->execute(array('u_agency1' => $_POST['dest_agency'],'u_agency2' => $_POST['from_agency']));
	}
	
	//update imap multi mailbox service parameters
	if($rparameters['imap_mailbox_service'])
	{
		//add new association
		if($_POST['mailbox_service'] && $_POST['mailbox_password'] && $_POST['mailbox_service_id'])
		{
			//secure string
			$_POST['mailbox_service']=strip_tags($_POST['mailbox_service']);
			$_POST['mailbox_password']=strip_tags($_POST['mailbox_password']);
		
			$qry=$db->prepare("INSERT INTO `tparameters_imap_multi_mailbox` (`mail`,`password`,`service_id`) VALUES (:mail,:password,:service_id)");
			$qry->execute(array('mail' => $_POST['mailbox_service'],'password' => $_POST['mailbox_password'],'service_id' => $_POST['mailbox_service_id']));
		}
		//crypt password
		$qry=$db->prepare("SELECT `id`,`password` FROM `tparameters_imap_multi_mailbox` WHERE password NOT LIKE '%gs_en%'");
		$qry->execute();
		while($row=$qry->fetch()) 
		{
			//crypt password
			$enc_mailbox_password = gs_crypt($row['password'], 'e', $rparameters['server_private_key']);
			//update tparameters
			$qry2=$db->prepare("UPDATE `tparameters_imap_multi_mailbox` SET `password`=:mail_password WHERE `id`=:id");
			$qry2->execute(array('mail_password' => $enc_mailbox_password,'id' => $row['id']));
		}
		$qry->closeCursor();
	}
	
	//crypt connector password
	if($_POST['mail_password'] && !preg_match('/gs_en/',$_POST['mail_password']))
	{
		//crypt password
		$enc_mail_password = gs_crypt($_POST['mail_password'], 'e', $rparameters['server_private_key']);
		//update tparameters
		$qry=$db->prepare("UPDATE `tparameters` SET `mail_password`=:mail_password WHERE `id`='1'");
		$qry->execute(array('mail_password' => $enc_mail_password));
	}
	if($_POST['ldap_password'] && !preg_match('/gs_en/',$_POST['ldap_password']))
	{
		//crypt password
		$enc_ldap_password = gs_crypt($_POST['ldap_password'], 'e', $rparameters['server_private_key']);
		//update tparameters
		$qry=$db->prepare("UPDATE `tparameters` SET `ldap_password`=:ldap_password WHERE `id`='1'");
		$qry->execute(array('ldap_password' => $enc_ldap_password));
	}
	if($_POST['imap_password'] && !preg_match('/gs_en/',$_POST['imap_password']))
	{
		//crypt password
		$enc_imap_password = gs_crypt($_POST['imap_password'], 'e', $rparameters['server_private_key']);
		//update tparameters
		$qry=$db->prepare("UPDATE `tparameters` SET `imap_password`=:imap_password WHERE `id`='1'");
		$qry->execute(array('imap_password' => $enc_imap_password,));
	}
	if(!$_POST['plugin_connector'])
    {
        //redirect
        $www = './index.php?page=admin&subpage=parameters&tab=connector&ldaptest='.$_POST['test_ldap'].'';
        echo '<script language="Javascript">
        <!--
        document.location.replace("'.$www.'");
        // -->
        </script>'; 
    }
}
?>
<!-- /////////////////////////////////////////////////////////////// connectors part /////////////////////////////////////////////////////////////// -->
<div id="connector" class="tab-pane <?php if($_GET['tab']=='connector') echo 'active'; ?>">
    <form name="connector_form" id="connector_form" enctype="multipart/form-data" method="post" action="">
        <div class="table-responsive">
            <table class="table table table-bordered">
                <tbody>
                    <tr>
                        <td style="width: 150px;" class="text-95 text-default-d3 bgc-secondary-l4">
                            <i class="fa fa-envelope text-blue-m3 pr-1"><!----></i>
                            SMTP :
                        </td>
                        <td class="text-95 text-default-d3">
                            <input type="checkbox" <?php if($rparameters['mail']==1) echo "checked"; ?> name="mail" value="1">
                            <label for="mail" ><?php echo T_('Activer la liaison SMTP'); ?></label>
                            <i title="<?php echo T_("Connecteur permettant l'envoi de mails depuis GestSup vers un serveur de messagerie, afin que les mails puissent être envoyés"); ?>." class="fa fa-question-circle text-primary-m2"><!----></i>
                            <div class="pt-1"></div>
                            <?php
                            if($rparameters['mail']==1) 
                            {
                                echo '
                                <div class="pt-1"></div>
                                <label class="lbl" for="mail_smtp"><i class="fa fa-caret-right text-primary-m2"><!----></i> '.T_('Serveur').' :</label>
                                <input style="width:auto" class="form-control form-control-sm d-inline-block" name="mail_smtp" type="text" value="'.$rparameters['mail_smtp'].'" size="20" />
                                <i title="'.T_('Adresse IP ou Nom de votre serveur de messagerie (Exemple: 192.168.0.1 ou SRVMSG ou smtp.free.fr ou auth.smtp.1and1.fr ou SSL0.OVH.NET ou SMTP.office365.com)').'" class="fa fa-question-circle text-primary-m2"><!----></i>
                                <div class="pt-1"></div>
                                <label class="lbl" for="mail_port"><i class="fa fa-caret-right text-primary-m2"><!----></i> '.T_('Port').' :</label>
                                <select style="width:auto" class="form-control form-control-sm d-inline-block" id="mail_port" name="mail_port" >
                                    <option ';if($rparameters['mail_port']==587) echo "selected "; echo' value="587">587 (TLS)</option>
                                    <option ';if($rparameters['mail_port']==465) echo "selected "; echo' value="465">465 (SSL)</option>
                                    <option ';if($rparameters['mail_port']==25) echo "selected "; echo' value="25">25</option>
                                    <option ';if($rparameters['mail_port']==225) echo "selected "; echo' value="225">225</option>
                                </select>
                                <i title="'.T_('Port du serveur de messagerie par défaut le port 25 est utilisé, pour les connexions sécurisées les ports 465 et 587 sont utilisés. (exemple: OVH port 587, 1&1 port 587, Office 365 port 587, Gmail port 587)').'" class="fa fa-question-circle text-primary-m2"><!----></i>
                                <div class="pt-1"></div>
                                <label class="lbl" for="mail_ssl_check"><i class="fa fa-caret-right text-primary-m2"><!----></i> '.T_('Vérification SSL').' :</label>
                                <select style="width:auto" class="form-control form-control-sm d-inline-block" id="mail_ssl_check" name="mail_ssl_check" >
                                    <option ';if($rparameters['mail_ssl_check']==1) echo "selected "; echo' value="1">'.T_('Activée').'</option>
                                    <option ';if($rparameters['mail_ssl_check']==0) echo "selected "; echo' value="0">'.T_('Désactivée').'</option>
                                </select>
                                <i title="'.T_('Désactivation de la verification du certificat serveur et autorise les certificats auto-signés').'" class="fa fa-question-circle text-primary-m2"><!----></i>
                                <div class="pt-1"></div>
                                <label class="lbl" for="mail_secure"><i class="fa fa-caret-right text-primary-m2"><!----></i> '.T_('Préfixe').' :</label>
                                <select style="width:auto" class="form-control form-control-sm d-inline-block" id="mail_secure" name="mail_secure" >
                                    <option ';if($rparameters['mail_secure']==0) echo "selected "; echo' value="0">'.T_('Aucun').'</option>
                                    <option ';if($rparameters['mail_secure']=='SSL') echo "selected "; echo' value="SSL">ssl//</option>
                                    <option ';if($rparameters['mail_secure']=='TLS') echo "selected "; echo' value="TLS">tls//</option>
                                </select>
                                    ';
                                    if($rparameters['mail_secure']=='SSL' || $rparameters['mail_secure']=='TLS') {echo'<i>('.T_("l'extension php_openssl devra être activée").')<!----></i>';} else {echo'<i title="Si votre serveur de messagerie est sécurisé avec SSL ou TLS (Exemple: Gmail aucun TLS, 1&1 aucun, OVH aucun, Office 365 aucun)."  class="fa fa-question-circle text-primary-m2"><!----></i>';} 
                                    echo '
                                <div class="pt-1"></div>
                                <label class="lbl" for="mail_smtp_class"><i class="fa fa-caret-right text-primary-m2"><!----></i> '.T_('Classe').' :</label>
                                <select style="width:auto" class="form-control form-control-sm d-inline-block" id="mail_smtp_class" name="mail_smtp_class" >
                                    <option ';if($rparameters['mail_smtp_class']=='IsSMTP()') echo "selected "; echo' value="IsSMTP()">IsSMTP ('.T_('Défaut').')</option>
                                    <option ';if($rparameters['mail_smtp_class']=='IsSendMail()') echo "selected "; echo' value="IsSendMail()">IsSendMail</option>
                                </select>
                                <i title="'.T_("Classe PHPMailer, par défaut utiliser isSMTP(), certains hébergements n'autorisent que le isSendMail() (exemple: OVH et 1&1 utilise isSendMail() et Office 365 isSMTP)").'" class="fa fa-question-circle text-primary-m2"><!----></i>
                                <div class="pt-1"></div>
                                <label>
                                    <input type="checkbox"'; if($rparameters['mail_auth']==1) {echo "checked";}  echo ' name="mail_auth" value="1">
                                    <span class="lbl">&nbsp;'.T_('Serveur authentifié').'</span>
                                    <i title="'.T_('Cochez cette case si votre serveur de messagerie nécessite un identifiant et mot de passe pour envoyer des messages. (exemple: 1&1 exige un SMTP authentifié)').'" class="fa fa-question-circle text-primary-m2"><!----></i>
                                </label>
                                ';
                                if($rparameters['mail_auth']==1) 
                                {
                                    echo '
                                    <br /><label class="lbl ml-4" for="mail_username"> '.T_('Utilisateur').' :</label> <input style="width:auto" class="form-control form-control-sm d-inline-block" name="mail_username" type="text" value="'.$rparameters['mail_username'].'" size="30" />
                                    <br /><label class="lbl ml-4" for="mail_password"> '.T_('Mot de passe').' :</label> <input style="width:auto" class="form-control form-control-sm d-inline-block" name="mail_password" type="password" value="'.$rparameters['mail_password'].'" size="30" />
                                    ';
                                }

                                //SMTP TEST CONNECTOR
                                echo '
                                <div class="mt-3"></div>
                                <button name="test_smtp" value="1" type="submit" class="btn btn-xs btn-info">
                                    <i class="fa fa-exchange-alt"><!----></i>
                                    '.T_('Test du connecteur SMTP').'
                                </button>
                                ';
                                if(!empty($_POST['test_smtp']))
                                {
                                    require_once(__DIR__.'/../../components/PHPMailer/src/PHPMailer.php');
                                    require_once(__DIR__.'/../../components/PHPMailer/src/SMTP.php');
                                    require_once(__DIR__.'/../../components/PHPMailer/src/Exception.php');
                                    $smtp_test = new PHPMailer\PHPMailer\PHPMailer(true);
                                    try {
                                        $smtp_test->CharSet = 'UTF-8'; //ISO-8859-1 possible if string problems
                                        if($rparameters['mail_smtp_class']=='IsSendMail()') {$smtp_test->IsSendMail();} else {$smtp_test->IsSMTP();} 
                                        if($rparameters['mail_secure']=='SSL') 
                                        {$smtp_test->Host = "ssl://$rparameters[mail_smtp]";} 
                                        elseif($rparameters['mail_secure']=='TLS') 
                                        {$smtp_test->Host = "tls://$rparameters[mail_smtp]";} 
                                        else 
                                        {$smtp_test->Host = "$rparameters[mail_smtp]";}
                                        $smtp_test->SMTPAuth = $rparameters['mail_auth'];
                                        if($rparameters['debug']) $smtp_test->SMTPDebug = 4;
                                        if($rparameters['mail_secure']!=0) $smtp_test->SMTPSecure = $rparameters['mail_secure'];
                                        if($rparameters['mail_port']!=25) $smtp_test->Port = $rparameters['mail_port'];
                                        $smtp_test->Username = "$rparameters[mail_username]";
                                        if(preg_match('/gs_en/',$rparameters['mail_password'])) {$rparameters['mail_password']=gs_crypt($rparameters['mail_password'], 'd' , $rparameters['server_private_key']);}
                                        $smtp_test->Password = "$rparameters[mail_password]";
                                        $smtp_test->IsHTML(true); 
                                        $smtp_test->Timeout = 30;
                                        $smtp_test->From = "$rparameters[mail_from_adr]";
                                        $smtp_test->FromName = "$rparameters[mail_from_name]";
                                        $smtp_test->XMailer = '_';
                                        $smtp_test->Subject = "SMTP CONNECTOR TEST";
                                        if($rparameters['mail_ssl_check']==0)
                                        {
                                            //bug fix 3292 & 3427
                                            $smtp_test->smtpConnect([
                                            'ssl' => [
                                                'verify_peer' => false,
                                                'verify_peer_name' => false,
                                                'allow_self_signed' => true
                                                ]
                                            ]);
                                        }
                                        $smtp_test->Body = "SMTP CONNECTOR TEST";
                                        if($smtp_test->smtpConnect()){
                                            $smtp_test->smtpClose();
                                            echo DisplayMessage('success',T_('Connecteur opérationnel'));
                                        } else {
                                            echo DisplayMessage('error',T_("Le connecteur ne fonctionne pas vérifier vos paramètres, vous pouvez activer le mode debug pour plus d'informations"));
                                            if($rparameters['log']) {logit('error','SMTP test connector ko',$_SESSION['user_id']);}
                                        }
                                    } catch (Exception $e) {
                                        echo DisplayMessage('error',T_("Le connecteur ne fonctionne pas vérifier vos paramètres, vous pouvez activer le mode debug pour plus d'informations").' ('.$e->getMessage().')');
                                        //log
                                        if($rparameters['log']) {logit('error', $e->getMessage(),$_SESSION['user_id']);}
                                    }
                                }
                            }
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 150px;" class="text-95 text-default-d3 bgc-secondary-l4">
                            <i class="fa fa-book text-blue-m3 pr-1"><!----></i>
                            LDAP :
                        </td>
                        <td class="text-95 text-default-d3">
                            <label>
                                <input type="checkbox" <?php if($rparameters['ldap']==1) echo "checked"; ?> name="ldap" value="1">
                                <span class="lbl"><?php echo T_('Activer la liaison LDAP'); ?> </span>	
                                <i title="<?php echo T_("Connecteur permettant la synchronisation entre l'annuaire d'entreprise (Active Directory ou OpenLDAP) et GestSup"); ?>" class="fa fa-question-circle text-primary-m2"><!----></i>
                            </label>
                            <?php if($rparameters['ldap']=='1') 
                            {
                                echo '
                                <div class="pt-1"></div>
                                <label>
                                    <input type="checkbox"'; if($rparameters['ldap_auth']==1) echo "checked"; echo ' name="ldap_auth" value="1">
                                    <span class="lbl">&nbsp;'.T_("Activer l'authentification GestSup avec LDAP").'
                                    <i title="'.T_("Active l'authentification des utilisateurs dans Gestsup, avec les identifiants présents dans l'annuaire LDAP. Cela ne désactive pas l'authentification avec la base utilisateurs de GestSup").'." class="fa fa-question-circle text-primary-m2 "><!----></i>
                                </label>
                                <div class="pt-1"></div>
                                <label>
                                    <input type="checkbox"'; if($rparameters['ldap_sso']==1) echo "checked"; echo ' name="ldap_sso" value="1">
                                    <span class="lbl">&nbsp;'.T_("Activer le SSO").'
                                    <i title="'.T_("Permet la connexion d'un utilisateur sans la saisie de l'identifiant et du mot de passe, sur un poste Windows connecté à un domaine Active Directory, cf documentation").'." class="fa fa-question-circle text-primary-m2 "><!----></i>
                                </label>
                                <div class="pt-1"></div>
                                <label class="lbl">
                                    <input type="checkbox"'; if($rparameters['ldap_service']==1) echo "checked"; echo ' name="ldap_service" value="1">
                                    <span class="lbl">&nbsp;'.T_("Activer la synchronisation des groupes LDAP de services").'
                                    <i title="'.T_("Permet de synchroniser des groupes LDAP de service: création, renommage, désactivation de services GestSup, création utilisateurs GestSup membres du groupe LDAP et association entre les deux. (Tous les utilisateurs doivent appartenir à un groupe)").'." class="fa fa-question-circle text-primary-m2 "><!----></i>
                                </label>
                                <div class="pt-1"></div>
                                ';
                                if($rparameters['ldap_service']==1 )
                                {
                                    echo '
                                    <label class="lbl ml-4" for="ldap_service_url">'.T_("Emplacement des groupes de service").' :</label>
                                    <input style="width:auto" class="form-control form-control-sm d-inline-block" name="ldap_service_url" type="text" value="'.$rparameters['ldap_service_url'].'" size="50" />
                                    <i title="'.T_("Emplacement des groupes de service dans l'annuaire LDAP. (exemple: ou=service, ou=utilisateurs) Attention il ne doit pas être suffixé du domaine").'." class="fa fa-question-circle text-primary-m2"><!----></i> <br />
                                    <div class="pt-1"></div>
                                    ';
                                }
                                if($rparameters['user_agency']==1)
                                {
                                    echo '
                                    <label class="lbl">
                                        <input type="checkbox"'; if($rparameters['ldap_agency']==1) echo "checked"; echo ' name="ldap_agency" value="1">
                                        <span class="lbl">&nbsp;'.T_("Activer la synchronisation des groupes LDAP d'agences").'
                                        <i title="'.T_("Permet de synchroniser des groupes LDAP d'agence: création, renommage, désactivation d'agences GestSup, création utilisateurs GestSup membres du groupe LDAP et association entre les deux. (Tous les utilisateurs doivent appartenir à un groupe)").'." class="fa fa-question-circle text-primary-m2 "><!----></i>
                                    </label>
                                    <div class="pt-1"></div>
                                    ';
                                    if($rparameters['ldap_agency']==1)
                                    {
                                        echo '
                                        <label class="lbl ml-4" for="ldap_agency_url">'.T_("Emplacement des groupes d'agence").' :</label>
                                        <input style="width:auto" class="form-control form-control-sm d-inline-block"  name="ldap_agency_url" type="text" value="'.$rparameters['ldap_agency_url'].'" size="50" />
                                        <i title="'.T_("Emplacement des groupes d'agences dans l'annuaire LDAP. (exemple: ou=groupe_agence, ou=utilisateurs) Attention il ne doit pas être suffixé du domaine").'." class="fa fa-question-circle text-primary-m2"><!----></i> <br />
                                        <div class="pt-1"></div>
                                        <label class="lbl ml-4" for="ldap_agency_url">'.T_("Déplacer les tickets associés à l'agence").' :</label>
                                        <select style="width:auto" class="form-control form-control-sm d-inline-block"  id="from_agency" name="from_agency" />
                                            ';
                                            $qry = $db->prepare("SELECT `id`,`name` FROM `tagencies` ORDER BY name");
                                            $qry->execute();
                                            while ($row=$qry->fetch()){echo '<option value="'.$row['id'].'">'.$row['name'].'</option>';}
                                            $qry->closeCursor();
                                            echo'
                                        </select>
                                        <label class="lbl " for="dest_agency">'.T_("vers l'agence").' :</label>
                                        <select style="width:auto" class="form-control form-control-sm d-inline-block"  id="dest_agency" name="dest_agency" />
                                        ';
                                            $qry = $db->prepare("SELECT `id`,`name` FROM `tagencies` ORDER BY name");
                                            $qry->execute();
                                            while ($row=$qry->fetch())	
                                            {
                                                echo '<option value="'.$row['id'].'">'.$row['name'].'</option>';
                                            }
                                            $qry->closeCursor();
                                            echo'
                                        </select>
                                        <div class="pt-1"></div>
                                        ';
                                    }
                                }
                                echo '
                                <label class="lbl" for="ldap_type"><i class="fa fa-caret-right text-primary-m2"><!----></i> '.T_('Type de serveur').' : </label>
                                <select style="width:auto" class="form-control form-control-sm d-inline-block" id="ldap_type" name="ldap_type" >
                                    <option ';if($rparameters['ldap_type']==0) echo "selected "; echo ' value="0">Active Directory</option>
                                    <option ';if($rparameters['ldap_type']==1) echo "selected "; echo ' value="1">OpenLDAP</option>
                                    <option ';if($rparameters['ldap_type']==3) echo "selected "; echo ' value="3">Samba4</option>
                                </select>
                                <i title="'.T_("Sélectionner si votre serveur d'annuaire est Windows Active Directory ou OpenLDAP").'." class="fa fa-question-circle text-primary-m2"><!----></i><br />
                                <div class="pt-1"></div>
                                <label class="lbl" for="ldap_server"><i class="fa fa-caret-right text-primary-m2"><!----></i> '.T_('Serveur').' :</label>
                                <input style="width:auto" class="form-control form-control-sm d-inline-block" name="ldap_server" type="text" value="'.$rparameters['ldap_server'].'" size="20" />
                                <i title="'.T_("Adresse IP ou nom netbios du serveur d'annuaire, sans suffixe DNS (Exemple: 192.168.0.1 ou SRVDC1)").'. " class="fa fa-question-circle text-primary-m2"><!----></i><br />
                                <div class="pt-1"></div>
                                <label class="lbl" for="ldap_port"><i class="fa fa-caret-right text-primary-m2"><!----></i> '.T_('Port').' : </label>
                                <select style="width:auto" class="form-control form-control-sm d-inline-block" id="ldap_port" name="ldap_port" >
                                    <option ';if($rparameters['ldap_port']==389) echo "selected "; echo ' value="389">389</option>
                                    <option ';if($rparameters['ldap_port']==636) echo "selected "; echo ' value="636">636</option>
                                </select>
                                <i title="'.T_('Le port par défaut est 389 si vous utilisez un serveur LDAPS (sécurisé) le port est 636. Si vous rencontrez des difficultés avec le port 636 vous pouvez ajouter "TLS_REQCERT never" dans le fichier /etc/ldap/ldap.conf').'." class="fa fa-question-circle text-primary-m2"><!----></i> <br />
                                <div class="pt-1"></div>
                                <label class="lbl" for="ldap_domain"><i class="fa fa-caret-right text-primary-m2"><!----></i> '.T_('Domaine').' :</label>
                                <input style="width:auto" class="form-control form-control-sm d-inline-block" name="ldap_domain" type="text" value="'.$rparameters['ldap_domain'].'" size="20" />
                                <i title="'.T_('Nom du domaine FQDN (Exemple: exemple.local)').'." class="fa fa-question-circle text-primary-m2"><!----></i> <br />
                                <div class="pt-1"></div>
                                <label class="lbl" for="ldap_url"><i class="fa fa-caret-right text-primary-m2"><!----></i> '.T_('Emplacement des utilisateurs').' :</label>
                                <input style="width:auto" class="form-control form-control-sm d-inline-block" name="ldap_url" type="text" value="'.$rparameters['ldap_url'].'" size="80" />
                                <i title="'.T_("Emplacement dans l'annuaire des utilisateurs. Par défaut pour Active Directory cn=users, si vous utilisez plusieurs unités d'organisation séparer avec un point virgule (ou=France,ou=utilisateurs;ou=Belgique,ou=utilisateurs) Attention il ne doit pas être suffixé du domaine").'." class="fa fa-question-circle text-primary-m2"><!----></i> <br />
                                <div class="pt-1"></div>
                                ';
                                    if($rparameters['ldap_type']==0)
                                    {
                                        echo '
                                        <label class="lbl" for="ldap_login_field"><i class="fa fa-caret-right text-primary-m2"><!----></i> '.T_('Champ identifiant').' : </label>
                                        <select style="width:auto" class="form-control form-control-sm d-inline-block" id="ldap_login_field" name="ldap_login_field" >
                                            <option ';if($rparameters['ldap_login_field']=='SamAcountName') {echo "selected ";} echo ' value="SamAcountName">SamAcountName</option>
                                            <option ';if($rparameters['ldap_login_field']=='UserPrincipalName') {echo "selected ";} echo ' value="UserPrincipalName">UserPrincipalName</option>
                                        </select>
                                        <i title="'.T_("Permet de configurer le champ AD à utiliser pour le login GestSup").'." class="fa fa-question-circle text-primary-m2"><!----></i><br />
                                        <div class="pt-1"></div>
                                        ';
                                    }
                                    
                                echo '
                                <label class="lbl" for="ldap_user"><i class="fa fa-caret-right text-primary-m2"><!----></i> '.T_('Utilisateur').' : </label>
                                <input style="width:auto" class="form-control form-control-sm d-inline-block" name="ldap_user" type="text" value="'.$rparameters['ldap_user'].'" size="20" />
                                <i title="'.T_("Utilisateur présent dans l'annuaire LDAP, pour OpenLDAP l'utilisateur doit être à la racine et de type CN").'" class="fa fa-question-circle text-primary-m2"><!----></i> <br />
                                <div class="pt-1"></div>
                                <label class="lbl" for="ldap_password"><i class="fa fa-caret-right text-primary-m2"><!----></i> '.T_('Mot de passe').' :</label>
                                <input style="width:auto" class="form-control form-control-sm d-inline-block" name="ldap_password" type="password" value="'.$rparameters['ldap_password'].'" size="20" /><br />
                                ';
                                if($rparameters['ldap_agency']==0 && $rparameters['ldap_service']==0)
                                {
                                    echo '
                                    <i class="fa fa-caret-right text-primary-m2"><!----></i> '.T_('Désactiver les utilisateurs GestSup lors de la synchronisation').' : 
                                    <select style="width:auto" class="form-control form-control-sm d-inline-block" id="ldap_disable_user" name="ldap_disable_user" >
                                        <option ';if($rparameters['ldap_disable_user']==0) echo "selected "; echo ' value="0">Non</option>
                                        <option ';if($rparameters['ldap_disable_user']==1) echo "selected "; echo ' value="1">Oui</option>
                                    </select>
                                    <i title="'.T_("Désactive les utilisateurs présents dans GestSup, mais qui ne sont pas présent dans l'annuaire LDAP").'." class="fa fa-question-circle text-primary-m2"><!----></i><br />
                                    <div class="pt-1"></div>
                                    ';
                                }
                                echo '
                                <br />
                                <button name="test_ldap" value="1" type="submit" class="btn btn-xs btn-info">
                                    <i class="fa fa-exchange-alt"><!----></i>
                                    '.T_('Test du connecteur LDAP').'
                                </button>
                                ';
                                //check LDAP parameters
                                if($_GET['ldaptest']==1) {
                                    
                                    if($rparameters['ldap_sso']==1) {
                                        if(isset($_SERVER['REMOTE_USER']))
                                        {
                                            echo DisplayMessage('success',T_('Le SSO est opérationnel'));
                                        } else {
                                            echo DisplayMessage('error',T_('Le SSO ne fonctionne pas vérifier votre configuration serveur'));
                                        }
                                    }
                                    include('./core/ldap.php');
                                    echo $ldap_connection;
                                } 
                            }
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 150px;" class="text-95 text-default-d3 bgc-secondary-l4">
                            <i class="fa fa-download text-blue-m3 pr-1"><!----></i>
                            IMAP :
                        </td>
                        <td class="text-95 text-default-d3">
                            <input type="checkbox" <?php if($rparameters['imap']==1) {echo "checked";} ?> name="imap" value="1">
                            <label for="imap"><?php echo T_('Activer la liaison IMAP'); ?></label>
                            <i title="<?php echo T_("Connecteur permettant de créer des tickets automatiquement en interrogeant une boite mail. Une fois le mail converti en ticket le message passe en lu dans la boite de messagerie. Attention une tâche planifiée doit être crée afin d'interroger de manière régulière la boite mail (cf FAQ)"); ?>" class="fa fa-question-circle text-primary-m2"><!----></i>
                            <div class="pt-1"></div>
                            <?php
                            if($rparameters['imap']=='1') 
                            {
                                //generate stat token access
                                if($rright['admin'])
                                {
                                    
                                    //generate mail2ticket token access
                                    $qry=$db->prepare("DELETE FROM `ttoken` WHERE `action`='mail2ticket' AND `user_id`=:user_id");
                                    $qry->execute(array('user_id' => $_SESSION['user_id']));
                                    $token = bin2hex(random_bytes(32));
                                    $qry=$db->prepare("INSERT INTO `ttoken` (`date`,`token`,`action`,`user_id`) VALUES (NOW(),:token,'mail2ticket',:user_id)");
                                    $qry->execute(array('token' => $token,'user_id' => $_SESSION['user_id']));
                                }
                                echo '
                                    <label class="lbl" for="imap_server" ><i class="fa fa-caret-right text-primary-m2"><!----></i> '.T_('Serveur').' :</label>
                                    <input style="width:auto" class="form-control form-control-sm d-inline-block" name="imap_server" type="text" value="'.$rparameters['imap_server'].'" size="20" />
                                    <i title="'.T_('Adresse IP ou nom netbios ou nom FQDN du serveur IMAP de messagerie (ex: imap.free.fr, imap.gmail.com)').'" class="fa fa-question-circle text-primary-m2"><!----></i>
                                    <div class="pt-1"></div>
                                    <label class="lbl" for="imap_port"><i class="fa fa-caret-right text-primary-m2"><!----></i> '.T_('Port').' :</label>
                                    <select style="width:auto" class="form-control form-control-sm d-inline-block" id="imap_port" name="imap_port" >
                                        <option ';if($rparameters['imap_port']=='143') {echo "selected ";} echo ' value="143">143 (IMAP)</option>
                                        <option ';if($rparameters['imap_port']=="993/imap/ssl") {echo "selected ";} echo ' value="993/imap/ssl">993 (IMAP sécurisé)</option>
                                    </select>
                                    <i title="'.T_('Protocole utilisé sur le serveur POP ou IMAP sécurisé ou non (ex: pour free.fr sélectionner IMAP, pour gmail utiliser IMAP sécurisé)').'" class="fa fa-question-circle text-primary-m2"><!----></i>
                                    <div class="pt-1"></div>
                                    <label class="lbl" for="imap_ssl_check"><i class="fa fa-caret-right text-primary-m2"><!----></i> '.T_('Vérification SSL').' :</label>
                                    <select style="width:auto" class="form-control form-control-sm d-inline-block"  id="imap_ssl_check" name="imap_ssl_check" >
                                        <option ';if($rparameters['imap_ssl_check']==1) echo "selected "; echo' value="1">'.T_('Activée').'</option>
                                        <option ';if($rparameters['imap_ssl_check']==0) echo "selected "; echo' value="0">'.T_('Désactivée').'</option>
                                    </select>
                                    <i title="'.T_('Désactivation de la verification du certificat serveur et autorise les certificats auto-signés').'" class="fa fa-question-circle text-primary-m2"><!----></i>
                                    <div class="pt-1"></div>
                                    <label class="lbl" for="inbox"><i class="fa fa-caret-right text-primary-m2"><!----></i> '.T_('Dossier racine').' :</label>
                                    <select style="width:auto" class="form-control form-control-sm d-inline-block" id="imap_inbox" name="imap_inbox" >
                                        <option ';if($rparameters['imap_inbox']=='INBOX') {echo "selected ";} echo ' value="INBOX">INBOX</option>
                                        <option ';if($rparameters['imap_inbox']=='') {echo "selected ";} echo ' value="">'.T_('Aucun').'</option>
                                    </select>
                                    <i title="'.T_('Dossier racine ou se trouve les messages entrants (par défaut INBOX, pour gmail INBOX)').'" class="fa fa-question-circle text-primary-m2"><!----></i>
                                    <div class="pt-1"></div>
                                    <label class="lbl" for="imap_user"><i class="fa fa-caret-right text-primary-m2"><!----></i> '.T_('Adresse de messagerie').' :</label>
                                    <input style="width:auto" class="form-control form-control-sm d-inline-block" name="imap_user" type="text" value="'.$rparameters['imap_user'].'" size="25" />
                                    <i title="'.T_("Adresse de la boite de messagerie à relever, pour exchange mettre le login utilisateur de la boite aux lettres ou le nom FQDN de l'utilisateur exemple: user@domain.local").'." class="fa fa-question-circle text-primary-m2"><!----></i>
                                    <div class="pt-1"></div>
                                    <label class="lbl" for="imap_password"><i class="fa fa-caret-right text-primary-m2"><!----></i> '.T_('Mot de passe').' :</label>
                                    <input style="width:auto" class="form-control form-control-sm d-inline-block" name="imap_password" type="password" value="'.$rparameters['imap_password'].'" size="20" /><br /><div class="pt-1"></div>
                                    
                                    <input type="checkbox" '; if($rparameters['imap_reply']==1) {echo "checked";} echo ' name="imap_reply" value="1">
                                    <label class="lbl" for="imap_reply">'.T_('Gérer les réponses aux mails').'</label>
                                    <i title="'.T_("Ajoute des délimiteurs dans le mail, indiquant à l'utilisateur qu'il peut répondre au message envoyé").'" class="fa fa-question-circle text-primary-m2"><!----></i>
                                    
                                    <div class="pt-1"></div>
                                    <input type="checkbox" '; if($rparameters['imap_mailbox_service']==1) {echo "checked";} echo ' name="imap_mailbox_service" value="1">
                                    <label class="lbl" for="imap_mailbox_service">'.T_('Activer le multi boite aux lettres par service').' </label>
                                    <i title="'.T_("Permet de relever plusieurs boites aux lettres et d'associer les tickets crées à des services GestSup").'" class="fa fa-question-circle text-primary-m2"><!----></i>
                                
                                    <div class="pt-1"></div>
                                    ';
                                    //display parameters of imap_mailbox_service
                                    if($rparameters['imap_mailbox_service']==1)
                                    {
                                        echo "<ul>";
                                        //display all existing association
                                        $qry = $db->prepare("SELECT `id`,`mail`,`service_id` FROM `tparameters_imap_multi_mailbox`");
                                        $qry->execute();
                                        while ($row = $qry->fetch()) 
                                        {
                                            //get service name do display
                                            $qry2 = $db->prepare("SELECT `name` FROM `tservices` WHERE `id`=:id");
                                            $qry2->execute(array('id' => $row['service_id']));
                                            $row2=$qry2->fetch();
                                            $qry2->closeCursor();
                                            echo '<li>'.$row['mail'].' > '.$row2['name'].' <a onClick="javascript: return confirm(\''.T_('Êtes-vous sur de vouloir supprimer cette boite aux lettres ?').'\');" href="./index.php?page=admin&subpage=parameters&tab=connector&delete_imap_service='.$row['id'].'"><i title="'.T_("Supprimer l'association").'" class="fa fa-trash text-danger bigger-130"><!----></i></a></li>';
                                        }
                                        $qry->closeCursor();
                                        echo "</ul>";
                                        //inputs for new association
                                        echo '&nbsp;&nbsp;&nbsp;
                                        <label class="lbl" for="mailbox_service">'.T_('Adresse mail').' :</label> <input style="width:auto" class="form-control form-control-sm d-inline-block" name="mailbox_service" type="text" value="" size="20" />&nbsp
                                        <label class="lbl" for="mailbox_password">'.T_('Mot de passe').' :</label> <input style="width:auto" class="form-control form-control-sm d-inline-block" name="mailbox_password" type="password" value="" size="20" />&nbsp
                                        <label class="lbl" for="mailbox_service_id">'.T_('Service').' :</label> 
                                        <select style="width:auto" class="form-control form-control-sm d-inline-block" id="mailbox_service_id" name="mailbox_service_id" >
                                            ';
                                            $qry = $db->prepare("SELECT `id`,`name` FROM `tservices` WHERE `disable`='0'");
                                            $qry->execute();
                                            while ($row = $qry->fetch()) {echo'<option value="'.$row['id'].'">'.$row['name'].'</option>';}
                                            $qry->closeCursor();
                                            echo '
                                        </select>
                                        <div class="pt-1"></div>
                                        <label class="ml-3" >
                                            <input type="checkbox" '; if($rparameters['imap_from_adr_service']) {echo "checked";} echo ' name="imap_from_adr_service" value="1">
                                            <span class="lbl">&nbsp'.T_("Utiliser l'adresse mail du service pour l'émission des mails").'
                                            <i title="'.T_("Utilise l'adresse mail du service en tant qu'émetteur des mails, si un service un paramétré sur le ticket. A noter certains serveurs de messagerie n'accepte pas ce paramétrage").'" class="fa fa-question-circle text-primary-m2"><!----></i>
                                        </label>
                                        <div class="pt-1"></div>
                                        ';
                                    }
                                    echo '
                                    <label class="lbl" for="imap_blacklist"><i class="fa fa-caret-right text-primary-m2"><!----></i> '.T_('Adresses à exclure').' :</label>
                                    <input style="width:auto" class="form-control form-control-sm d-inline-block" name="imap_blacklist" type="text" value="'.$rparameters['imap_blacklist'].'" size="60" />
                                    <i title="'.T_("Permet d'ajouter des adresses mail et/ou des domaines à exclure de la récupération des messages. Le séparateur est le point virgule exemple: john.doe@example.com;example2.com;outlook").'." class="fa fa-question-circle text-primary-m2"><!----></i>
                                    <div class="pt-1"></div>
                                    <label class="lbl" for="imap_post_treatment"><i class="fa fa-caret-right text-primary-m2"><!----></i> '.T_('Action post-traitement').' :</label>
                                    <select style="width:auto" class="form-control form-control-sm d-inline-block" id="imap_post_treatment" name="imap_post_treatment" >
                                        <option ';if($rparameters['imap_post_treatment']=='move') {echo "selected ";} echo ' value="move">'.T_('Déplacer le mail dans un répertoire').'</option>
                                        <option ';if($rparameters['imap_post_treatment']=='delete') {echo "selected ";} echo ' value="delete">'.T_('Supprimer le mail').'</option>
                                        <option ';if($rparameters['imap_post_treatment']=='') {echo "selected ";} echo ' value="">'.T_('Passer en lu le mail').'</option>
                                    </select>
                                    <i title="'.T_('Permet de spécifier une action sur le mail de la boite aux lettre, une fois le mail convertit en ticket').'" class="fa fa-question-circle text-primary-m2"><!----></i>
                                    ';
                                    if($rparameters['imap_post_treatment']=='move') {echo '<br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.T_('Répertoire').': <input name="imap_post_treatment_folder" type="text" value="'.$rparameters['imap_post_treatment_folder'].'"  /> <i title="'.T_('Permet de spécifier un répertoire de la messagerie dans lequel le mail sera déplacé exemple: INBOX/vu').'" class="fa fa-question-circle text-primary-m2"><!----></i>';}
                                    echo'
                                    <div class="pt-1"></div>
                                    <button OnClick="window.open(\'./mail2ticket.php?token='.$token.'\')"  type="button" class="btn btn-xs btn-info">
                                        <i class="fa fa-download"><!----></i>
                                        '.T_("Lancer l'import des mails").'
                                    </button>
                                ';
                            }
                            ?>
                        </td>
                    </tr>
                    <?php
                    //include plugin
                    $section='connector';
                    include('./plugin.php');
                    ?>
                </tbody>
            </table>
        </div>
        <div class="border-t-1 brc-secondary-l1 bgc-secondary-l4 py-3 text-center">
                <button name="submit_connector" id="submit_connector" value="submit_connector" type="submit" class="btn btn-success">
                    <i class="fa fa-check"><!----></i>
                    <?php echo T_('Valider'); ?>
                </button>
        </div>
    </form>
</div>