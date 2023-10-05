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
if(!isset($_POST['planning'])) $_POST['planning']= '';
if(!isset($_POST['procedure'])) $_POST['procedure']= '';
if(!isset($_POST['survey'])) $_POST['survey']= '';
if(!isset($_POST['survey_mail_text'])) $_POST['survey_mail_text']= '';
if(!isset($_POST['survey_ticket_state'])) $_POST['survey_ticket_state']= '';
if(!isset($_POST['survey_auto_close_ticket'])) $_POST['survey_auto_close_ticket']= '';
if(!isset($_POST['project'])) $_POST['project']= '';
if(!isset($_POST['asset'])) $_POST['asset']= '';
if(!isset($_POST['asset_ip'])) $_POST['asset_ip']= '';
if(!isset($_POST['asset_warranty'])) $_POST['asset_warranty']= '';
if(!isset($_POST['asset_vnc_link'])) $_POST['asset_vnc_link']= '';
if(!isset($_FILES['asset_import']['name'])) $_FILES['asset_import']['name'] = '';

//generate stat token access
if($rright['admin'])
{
	//generate ticket token access
	$qry=$db->prepare("DELETE FROM `ttoken` WHERE `action`='admin_function_access' AND `user_id`=:user_id");
	$qry->execute(array('user_id' => $_SESSION['user_id']));
	$token = bin2hex(random_bytes(32));
	$qry=$db->prepare("INSERT INTO `ttoken` (`date`,`token`,`action`,`user_id`) VALUES (NOW(),:token,'admin_function_access',:user_id)");
	$qry->execute(array('token' => $token,'user_id' => $_SESSION['user_id']));
}

//delete question from survey
if($rparameters['survey']==1 && $_GET['deletequestion'] && $rright['admin'])
{
	$qry=$db->prepare("DELETE FROM tsurvey_questions WHERE id=:id");
	$qry->execute(array('id' => $_GET['deletequestion']));
}

//post form
if($_POST['submit_function'])
{
	//upload assets file
	if($_FILES['asset_import']['name'])
	{
		//create asset folder if not exist
		if(!file_exists('./upload/asset')) {mkdir('./upload/asset', 0777, true);}
		
	    $filename = $_FILES['asset_import']['name'];
		//change special character in filename
		$a = array('à', 'á', 'â', 'ã', 'ä', 'å', 'æ', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ø', 'œ', 'ù', 'ú', 'û', 'ü', 'ý', 'ÿ', 'š', 'ž', "'", " ", "/", "%", "?", ":", "!", "’", ",",">","<");
		$b = array("a", "a", "a", "a", "a", "a", "ae", "c", "e", "e", "e", "e", "i", "i", "i", "i", "n", "o", "o", "o", "o", "o", "o", "oe", "u", "u", "u", "u", "y", "y", "s", "z", "-", "-", "-", "-", "", "-", "", "-", "-", "", "");
		$file_rename = str_replace($a,$b,$_FILES['asset_import']['name']);
	    //check file extension
	    $whitelist =  array('csv');
        $extension=new SplFileInfo($file_rename);
		$extension=$extension->getExtension();
        if(in_array(strtolower($extension),$whitelist)) {
            $dest_folder = "./upload/asset/";
    		if(move_uploaded_file($_FILES['asset_import']['tmp_name'], $dest_folder.$file_rename)) 
    		{
                $file_content = file_get_contents($dest_folder.$file_rename, true);
				if(preg_match('{\<\?php}',$file_content) || preg_match('/system\(/',$file_content)) {
					unlink($dest_folder.$file_rename); //remove file
					echo DisplayMessage('error',T_("Fichier interdit"));
					if($rparameters['log']) {logit('security','File upload blocked on import asset function ',$_SESSION['user_id']);}
				} else {
                    //launch import treatment
				    require('./core/import_assets.php');
                }
				
    		} else {
                 echo DisplayMessage('error',T_('Erreur de transfert vérifier le chemin').' ('.$dest_folder.')');
    		}
        } else {
            echo DisplayMessage('error',T_('Blocage de sécurité fichier interdit'));
            if($rparameters['log']) {logit('security','File upload blocked on import asset function',$_SESSION['user_id']);}
        }
	}
	
	//action on survey questions
	if($rparameters['survey']==1)
	{
		//update current question
		$qry = $db->prepare("SELECT `id` FROM `tsurvey_questions` WHERE `disable`='0'");
		$qry->execute();
		while ($row=$qry->fetch())
		{
			//define var
			if(!isset($_POST['survey_question_select_1_'.$row['id']])) $_POST['survey_question_select_1_'.$row['id']]= '';
			if(!isset($_POST['survey_question_select_2_'.$row['id']])) $_POST['survey_question_select_2_'.$row['id']]= '';
			if(!isset($_POST['survey_question_select_3_'.$row['id']])) $_POST['survey_question_select_3_'.$row['id']]= '';
			if(!isset($_POST['survey_question_select_4_'.$row['id']])) $_POST['survey_question_select_4_'.$row['id']]= '';
			if(!isset($_POST['survey_question_select_5_'.$row['id']])) $_POST['survey_question_select_5_'.$row['id']]= '';
			if(!isset($_POST['survey_question_scale_'.$row['id']])) $_POST['survey_question_scale_'.$row['id']]= '';
			
			//secure string
			$_POST['survey_question_number_'.$row['id']]=strip_tags($_POST['survey_question_number_'.$row['id']]);
			$_POST['survey_question_type_'.$row['id']]=strip_tags($_POST['survey_question_type_'.$row['id']]);
			$_POST['survey_question_text_'.$row['id']]=strip_tags($_POST['survey_question_text_'.$row['id']]);
			$_POST['survey_question_scale_'.$row['id']]=strip_tags($_POST['survey_question_scale_'.$row['id']]);
			$_POST['survey_question_select_1_'.$row['id']]=strip_tags($_POST['survey_question_select_1_'.$row['id']]);
			$_POST['survey_question_select_2_'.$row['id']]=strip_tags($_POST['survey_question_select_2_'.$row['id']]);
			$_POST['survey_question_select_3_'.$row['id']]=strip_tags($_POST['survey_question_select_3_'.$row['id']]);
			$_POST['survey_question_select_4_'.$row['id']]=strip_tags($_POST['survey_question_select_4_'.$row['id']]);
			$_POST['survey_question_select_5_'.$row['id']]=strip_tags($_POST['survey_question_select_5_'.$row['id']]);
			
			$qry2=$db->prepare("UPDATE tsurvey_questions SET `number`=:number, `type`=:type, `text`=:text, `scale`=:scale, `select_1`=:select_1, `select_2`=:select_2, `select_3`=:select_3, `select_4`=:select_4, `select_5`=:select_5 WHERE `id`=:id");
			$qry2->execute(array(
				'number' => $_POST['survey_question_number_'.$row['id']],
				'type' => $_POST['survey_question_type_'.$row['id']],
				'text' => $_POST['survey_question_text_'.$row['id']],
				'scale' => $_POST['survey_question_scale_'.$row['id']],
				'select_1' => $_POST['survey_question_select_1_'.$row['id']],
				'select_2' => $_POST['survey_question_select_2_'.$row['id']],
				'select_3' => $_POST['survey_question_select_3_'.$row['id']],
				'select_4' => $_POST['survey_question_select_4_'.$row['id']],
				'select_5' => $_POST['survey_question_select_5_'.$row['id']],
				'id' => $row['id']
				));
		}
		$qry->closeCursor();
		
		//insert new question
		if($_POST['survey_new_question_number'])
		{
			//secure string
			$_POST['survey_new_question_number']=strip_tags($_POST['survey_new_question_number']);
			$_POST['survey_new_question_text']=strip_tags($_POST['survey_new_question_text']);
			
			$qry=$db->prepare("INSERT INTO `tsurvey_questions` (`number`,`type`,`text`) VALUES (:number,:type,:text)");
			$qry->execute(array('number' => $_POST['survey_new_question_number'],'type' => $_POST['survey_new_question_type'],'text' => $_POST['survey_new_question_text']));
		}
	}
	
	//escape special char and secure string before database insert
	$_POST['survey_mail_text']=str_replace('<script>','',$_POST['survey_mail_text']);
	$_POST['survey_mail_text']=str_replace('</script>','',$_POST['survey_mail_text']);
	
	$qry=$db->prepare("
	UPDATE `tparameters` SET 
	`planning`=:planning,
	`procedure`=:procedure,
	`survey`=:survey,
	`survey_mail_text`=:survey_mail_text,
	`survey_ticket_state`=:survey_ticket_state,
	`survey_auto_close_ticket`=:survey_auto_close_ticket,
	`project`=:project,
	`asset`=:asset,
	`asset_ip`=:asset_ip,
	`asset_warranty`=:asset_warranty,
	`asset_vnc_link`=:asset_vnc_link
	WHERE `id`=:id");
	$qry->execute(array(
		'planning' => $_POST['planning'],
		'procedure' => $_POST['procedure'],
		'survey' => $_POST['survey'],
		'survey_mail_text' => $_POST['survey_mail_text'],
		'survey_ticket_state' => $_POST['survey_ticket_state'],
		'survey_auto_close_ticket' => $_POST['survey_auto_close_ticket'],
		'project' => $_POST['project'],
		'asset' => $_POST['asset'],
		'asset_ip' => $_POST['asset_ip'],
		'asset_warranty' => $_POST['asset_warranty'],
		'asset_vnc_link' => $_POST['asset_vnc_link'],
		'id' => '1'
        ));
        
	
	if(!$error)
	{
	//redirect
		$www = "./index.php?page=admin&subpage=parameters&tab=function";
		echo '<script language="Javascript">
		<!--
		document.location.replace("'.$www.'");
		// -->
		</script>'; 
	} else {echo $error;}
}

?>

<!-- /////////////////////////////////////////////////////////////// functions tab /////////////////////////////////////////////////////////////// -->
<div id="function" class="tab-pane <?php if($_GET['tab']=='function') echo 'active'; ?>">
    <form id="function_form" name="function_form" enctype="multipart/form-data" method="POST" action="">
        <div class="table-responsive">
            <table class="table table table-bordered">
                <tbody>
                    <tr>
                        <td style="width: 150px;" class="text-95 text-default-d3 bgc-secondary-l4">
                            <i class="fa fa-calendar text-blue-m3 pr-1"><!----></i>
                        <?php echo T_('Calendrier'); ?> :
                        </td>
                        <td class="text-95 text-default-d3">
                            <label>
                                <input type="checkbox" <?php if($rparameters['planning']==1) echo "checked"; ?> name="planning" value="1">
                                <span class="lbl"><?php echo T_('Activer la fonction Calendrier'); ?></span>
                                <i title="<?php echo T_('Active la gestion de planning, nouvel onglet et gestion dans les tickets'); ?>" class="fa fa-question-circle text-primary-m2"><!----></i>
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 150px;" class="text-95 text-default-d3 bgc-secondary-l4">
                            <i class="fa fa-desktop text-blue-m3 pr-1"><!----></i>
                            <?php echo T_('Équipement'); ?> :
                            </td>
                            <td class="text-95 text-default-d3">
                                <label>
                                <input type="checkbox" <?php if($rparameters['asset']==1) echo "checked"; ?> name="asset" value="1" />
                                <span class="lbl">&nbsp;<?php echo T_('Activer la fonction gestion des équipements'); ?></span>
                                <i title="<?php echo T_('Active la gestion des équipements, affiche un nouvel item dans le menu de gauche'); ?>." class="fa fa-question-circle text-primary-m2"><!----></i>
                            </label>	
                            <?php
                            if($rparameters['asset']==1)
                            {
                                echo'
                                <div class="pt-1"></div>
                                &nbsp; &nbsp; &nbsp;<i class="fa fa-circle text-success"><!----></i>
                                '.T_('Gestion des adresses IP').' :&nbsp;
                                <label for="asset_ip">
                                    <input type="radio" value="1" name="asset_ip"'; if($rparameters['asset_ip']==1) {{echo "checked";}} echo '> <span class="lbl"> '.T_('Oui').' </span>
                                    <input type="radio" value="0" name="asset_ip"'; if($rparameters['asset_ip']==0) echo "checked"; echo '  > <span class="lbl"> '.T_('Non').' </span>
                                </label>
                                <i title="'.T_("Permet d'afficher dans la liste des équipements une colonne adresse IP, active les également des champs additionnels sur les fiches des équipements").'" class="fa fa-question-circle text-primary-m2"><!----></i>
                                <div class="pt-1"></div>
                                &nbsp; &nbsp; &nbsp;<i class="fa fa-circle text-success"><!----></i>
                                '.T_('Gestion des garanties').' :&nbsp;
                                <label for="asset_warranty">
                                    <input type="radio" value="1" name="asset_warranty"'; if($rparameters['asset_warranty']==1) {{echo "checked";}} echo '> <span class="lbl"> '.T_('Oui').' </span>
                                    <input type="radio" value="0" name="asset_warranty"'; if($rparameters['asset_warranty']==0) echo "checked"; echo '  > <span class="lbl"> '.T_('Non').' </span>
                                </label>
                                <i title="'.T_('Affiche un nouvel item dans le menu des équipements, permettant de visualiser les équipements en fin de garantie').'" class="fa fa-question-circle text-primary-m2"><!----></i>
                                <div class="pt-1"></div>
                                ';
                                if($rparameters['asset_ip']==1)
                                {
                                    echo '
                                    &nbsp; &nbsp; &nbsp;<i class="fa fa-circle text-success"><!----></i>
                                    '.T_("Activer le lien VNC sur l'équipement").' :&nbsp;
                                    <label for="asset_vnc_link">
                                            <input type="radio" value="1" name="asset_vnc_link"'; if($rparameters['asset_vnc_link']==1) {{echo "checked";}} echo '> <span class="lbl"> '.T_('Oui').' </span>
                                            <input type="radio" value="0" name="asset_vnc_link"'; if($rparameters['asset_vnc_link']==0) echo "checked"; echo '  > <span class="lbl"> '.T_('Non').' </span>
                                    </label>
                                    <i title="'.T_("Affiche un nouveau bouton sur la fiche de l'équipement permettant de prendre la main si un serveur VNC web est installé sur le client").'" class="fa fa-question-circle text-primary-m2"><!----></i>
                                    <div class="pt-1"></div>';
                                }
                                echo '
                                &nbsp; &nbsp; &nbsp;<i class="fa fa-circle text-success"><!----></i>
                                '.T_('Importer des équipements').' :&nbsp;
                                <input style="display:inline" type="file" id="asset_import" name="asset_import" />
                                <a title="'.T_("Télécharger le modèle CSV pour ensuite pouvoir lancer l'import").'" href="./download/tassets_template.csv">'.T_('Modèle').'</a>
                                <i title="'.T_("Permet d'importer des équipements en lot depuis un fichier CSV").'" class="fa fa-question-circle text-primary-m2"><!----></i>
                                ';
                            }
                            ?>	
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 150px;" class="text-95 text-default-d3 bgc-secondary-l4">
                            <i class="fa fa-book text-blue-m3 pr-1"><!----></i>
                        <?php echo T_('Procédure'); ?> :
                        </td>
                        <td class="text-95 text-default-d3">
                            <label>
                                <input type="checkbox" <?php if($rparameters['procedure']==1) {echo "checked";} ?> name="procedure" value="1" /> 
                                <span class="lbl">&nbsp;<?php echo T_('Activer la fonction procédure'); ?></span>
                                <i title="<?php echo T_('Active la gestion des procédures'); ?>" class="fa fa-question-circle text-primary-m2"><!----></i>
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 150px;" class="text-95 text-default-d3 bgc-secondary-l4">
                            <i class="fa fa-tasks text-blue-m3 pr-1"><!----></i>
                        <?php echo T_('Projet'); ?> :
                        </td>
                        <td class="text-95 text-default-d3">
                            <label>
                            <input type="checkbox" <?php if($rparameters['project']==1) {echo "checked";} ?> name="project" value="1" /> 
                            <span class="lbl">&nbsp;<?php echo T_('Activer la fonction projet'); ?></span>
                            <i title="<?php echo T_('Active la gestion des projets, visualisation de jonction de tickets'); ?>" class="fa fa-question-circle text-primary-m2"><!----></i>
                        </label>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 150px;" class="text-95 text-default-d3 bgc-secondary-l4">
                            <i class="fa fa-check text-blue-m3 pr-1"><!----></i>
                        <?php echo T_('Sondage'); ?> :
                        </td>
                        <td class="text-95 text-default-d3">
                            <label>
                                <?php
                                    if($rparameters['mail'] && $rparameters['mail_smtp']) //check if SMTP connector is enabled, before display survey section
                                    {
                                        echo '
                                        <input type="checkbox"  ';if($rparameters['survey']==1) {echo "checked";} echo ' name="survey" value="1" />
                                        <span class="lbl">&nbsp;'.T_('Activer la fonction sondage').'</span>
                                        <i title="'.T_("Active la gestion d'un sondage utilisateur, permettant à un utilisateur de remplir un questionnaire de satisfaction sur un ticket ").'" class="fa fa-question-circle text-primary-m2"><!----></i>
                                        ';
                                    } else {
                                        echo '<i class="fas fa-exclamation-triangle text-warning-m1"><!----></i>&nbsp;<span class="text-warning-m1">'.T_('Le connecteur SMTP doit être configuré, pour activer cette fonction').'.</span>';
                                    }
                                ?>
                            </label>
                            <?php
                            if($rparameters['survey']==1)
                            {
                                echo'
                                <div class="pt-1"></div>
                                &nbsp; &nbsp; &nbsp;<i class="fa fa-circle text-success"><!----></i>
                                '.T_("Envoyer un mail avec un lien vers le sondage à l'utilisateur lorsque le ticket passe dans l'état").' :&nbsp;
                                <select style="width:auto" class="form-control form-control-sm d-inline-block" name="survey_ticket_state">
                                    ';
                                $qry = $db->prepare("SELECT `id`,`name` FROM `tstates` ORDER by number");
                                $qry->execute();
                                while ($row=$qry->fetch())
                                {
                                    if($rparameters['survey_ticket_state']==$row['id']) {$selected='selected';} else {$selected='';}
                                    echo '<option value="'.$row['id'].'" '.$selected.' >'.$row['name'].'</option>';
                                }
                                $qry->closecursor();
                                    echo '
                                </select>
                                <i title="'.T_("Envoi un mail en destination de l'utilisateur lorsque le ticket passe dans l'état sélectionné, exemple état attente retour client").'" class="fa fa-question-circle text-primary-m2"><!----></i>
                                <div class="pt-1"></div>
                                &nbsp; &nbsp; &nbsp;<i class="fa fa-circle text-success"><!----></i>
                                '.T_("Texte du mail envoyé à l'utilisateur pour le sondage, vous pouvez utiliser la balise [ticket_link] afin d'insérer un lien vers le ticket").' :&nbsp;<i title="'.T_("Texte du mail que l'utilisateur va recevoir afin de lui indiquer de remplir un sondage en cliquant sur un lien").'" class="fa fa-question-circle text-primary-m2"><!----></i><br />
                                &nbsp; &nbsp; &nbsp;&nbsp; &nbsp;&nbsp;&nbsp;<textarea style="width:auto" class="form-control form-control-sm d-inline-block" cols="60" rows="3" name="survey_mail_text">'.$rparameters['survey_mail_text'].'</textarea>
                                <div class="pt-1"></div>&nbsp; &nbsp; &nbsp;<i class="fa fa-circle text-success"><!----></i>
                                '.T_("Clôturer automatiquement le ticket lorsque le sondage a été validé par l'utilisateur").' :&nbsp;
                                <label for="survey_auto_close_ticket">
                                        <input type="radio" value="1" name="survey_auto_close_ticket"'; if($rparameters['survey_auto_close_ticket']==1) {{echo "checked";}} echo '> <span class="lbl"> '.T_('Oui').' </span>
                                        <input type="radio" value="0" name="survey_auto_close_ticket"'; if($rparameters['survey_auto_close_ticket']==0) echo "checked"; echo '  > <span class="lbl"> '.T_('Non').' </span>
                                </label>
                                <i title="'.T_("Modifie l'état du ticket en résolu si l'utilisateur à remplit et validé le sondage").'" class="fa fa-question-circle text-primary-m2"><!----></i>
                                <div class="pt-1"></div>
                                &nbsp; &nbsp; &nbsp;<i class="fa fa-circle text-success"><!----></i>
                                '.T_('Liste des questions du sondage').' :<br />';
                                $qry = $db->prepare("SELECT * FROM `tsurvey_questions` ORDER by number");
                                $qry->execute();
                                while ($row=$qry->fetch())
                                {
                                    echo '<div class="pt-1"></div>';
                                    echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
                                    echo '<input style="width:auto" class="form-control form-control-sm d-inline-block" type="text" name="survey_question_number_'.$row['id'].'" size="1" value="'.$row['number'].'"></input>&nbsp;&nbsp;';
                                    echo '<input style="width:auto" class="form-control form-control-sm d-inline-block" type="text" name="survey_question_text_'.$row['id'].'" size="50" value="'.$row['text'].'" ></input>&nbsp;&nbsp;';
                                    echo 'Type :&nbsp;';
                                    echo '
                                        <select style="width:auto" class="form-control form-control-sm d-inline-block" name="survey_question_type_'.$row['id'].'" >
                                            <option '; if($row['type']==1) {echo 'selected';} echo ' value="1">'.T_('Oui/Non').'</option>
                                            <option '; if($row['type']==2) {echo 'selected';} echo ' value="2">'.T_('Texte').'</option>
                                            <option '; if($row['type']==3) {echo 'selected';} echo ' value="3">'.T_('Liste déroulante').'</option>
                                            <option '; if($row['type']==4) {echo 'selected';} echo ' value="4">'.T_('Échelle').'</option>
                                        </select>
                                    ';
                                    //display scale size filed if selected
                                    if($row['type']==4) {echo '&nbsp;&nbsp;Valeur maximum: <input style="width:auto" class="form-control form-control-sm d-inline-block" type="text" size="2" name="survey_question_scale_'.$row['id'].'" value="'.$row['scale'].'" />';}
                                    if($row['type']==3) {
                                    echo '&nbsp;&nbsp;Choix :&nbsp;
                                        <input style="width:auto" class="form-control form-control-sm d-inline-block" type="text" size="5" name="survey_question_select_1_'.$row['id'].'" value="'.$row['select_1'].'" />
                                        <input style="width:auto" class="form-control form-control-sm d-inline-block" type="text" size="5" name="survey_question_select_2_'.$row['id'].'" value="'.$row['select_2'].'" />
                                        <input style="width:auto" class="form-control form-control-sm d-inline-block" type="text" size="5" name="survey_question_select_3_'.$row['id'].'" value="'.$row['select_3'].'" />
                                        <input style="width:auto" class="form-control form-control-sm d-inline-block" type="text" size="5" name="survey_question_select_4_'.$row['id'].'" value="'.$row['select_4'].'" />
                                        <input style="width:auto" class="form-control form-control-sm d-inline-block" type="text" size="5" name="survey_question_select_5_'.$row['id'].'" value="'.$row['select_5'].'" />
                                    ';
                                    }
                                    echo '&nbsp;&nbsp;<a href="./index.php?page=admin&subpage=parameters&tab=function&deletequestion='.$row['id'].'"><i class="fa fa-trash text-danger bigger-130" title="'.T_('Supprimer la question').'"><!----></i></a>';
                                    echo '<br />';
                                }
                                $qry->closecursor();
                                //display fields for new question
                                echo '
                                    <div class="pt-1"></div>
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    <input style="width:auto" class="form-control form-control-sm d-inline-block" placeholder="N°" type="text" name="survey_new_question_number" size="1" ></input>&nbsp;
                                    <input style="width:auto" class="form-control form-control-sm d-inline-block" placeholder="'.T_('Texte de la nouvelle question').'" type="text" name="survey_new_question_text" size="50" ></input>&nbsp;
                                    Type :&nbsp;
                                    <select style="width:auto" class="form-control form-control-sm d-inline-block"  name="survey_new_question_type">
                                        <option value="1">'.T_('Oui/Non').'</option>
                                        <option value="2">'.T_('Texte').'</option>
                                        <option value="3">'.T_('Liste déroulante').'</option>
                                        <option value="4">'.T_('Échelle').'</option>
                                    </select>
                                ';
                                //display export button
                                echo '
                                <br />
                                <br />
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                <button title="'.T_("Télécharger les résultats du sondage au format CSV").'" name="dump_survey" OnClick="window.open(\'./core/export_survey.php?token='.$token.'&user_id='.$_SESSION['user_id'].'&\')"  value="dump_survey" type="submit" class="btn btn-xs btn-info">
                                        <i class="fa fa-download"><!----></i>
                                        '.T_("Exporter les résultats").'
                                    </button>
                                <br />
                                <br />
                                ';
                            }
                            ?>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="border-t-1 brc-secondary-l1 bgc-secondary-l4 py-3 text-center">
            <button name="submit_function" id="submit_function" value="submit_function" type="submit" class="btn btn-success">
                <i class="fa fa-check"><!----></i>
                <?php echo T_('Valider'); ?>
            </button>
        </div>
        <div class="pt-1"></div>
    </form>				
</div>