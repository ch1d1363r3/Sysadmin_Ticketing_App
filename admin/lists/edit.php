<?php
################################################################################
# @Name : ./admin/lists/edit.php
# @Description : modify value in table
# @Call : /admin/list.php
# @Parameters : 
# @Author : Flox
# @Create : 10/08/2021
# @Update : 12/08/2021
# @Version : 3.2.15
################################################################################

if($_GET['action']=="update") 
{
	if($_GET['table']=='tcategory') //special case category
	{
		//default qry
		$qry=$db->prepare("UPDATE `tcategory` SET `number`=:number,`name`=:name WHERE `id`=:id");
		$qry->execute(array('number' => $_POST['number'],'name' => $_POST['category'],'id' => $_GET['id']));
		//case tech auto attribute
		if($rparameters['ticket_cat_auto_attribute'])
		{
			if($_POST['technician'] && $_POST['technician_group'])
			{
				//error technician OR technician group
			} else {
				$qry=$db->prepare("UPDATE `tcategory` SET `technician`=:technician,`technician_group`=:technician_group WHERE `id`=:id");
				$qry->execute(array('technician' => $_POST['technician'],'technician_group' => $_POST['technician_group'],'id' => $_GET['id']));
			}
		}
		//case service limit
		if($rparameters['user_limit_service'])
		{
			$qry=$db->prepare("UPDATE `tcategory` SET `service`=:service WHERE `id`=:id");
			$qry->execute(array('service' => $_POST['service'],'id' => $_GET['id']));
		}
	}elseif($_GET['table']=='tsubcat') //special case subcat
	{
		//secure string
		$_POST['subcat']=strip_tags($_POST['subcat']);
		
		if($_POST['technician'] && $_POST['technician_group']) {$_POST['technician']=0; $_POST['technician_group']=0;}
		
		$qry=$db->prepare("UPDATE `tsubcat` SET `cat`=:cat, `name`=:name,`technician`=:technician,`technician_group`=:technician_group WHERE id=:id");
		$qry->execute(array('cat' => $_POST['cat'],'name' => $_POST['subcat'],'technician' => $_POST['technician'],'technician_group' => $_POST['technician_group'],'id' => $_GET['id']));
	}
	elseif($_GET['table']=='tassets_model') //special case  asset model
	{ 
		//secure string
		$_POST['model']=strip_tags($_POST['model']);
		$_POST['warranty']=strip_tags($_POST['warranty']);
		
		///upload file
		if($_FILES['file1']['name'])
		{
			//white list exclusion for extension
			$whitelist =  array('png','jpg','jpeg' ,'gif' ,'bmp');
			$file_name = basename($_FILES['file1']['name']);
			$dest_dir='./upload/asset/model/';
			//create dir if not exist
			if(!file_exists('./upload/asset')) {
				mkdir('./upload/asset', 0777, true);
			}
			if(!file_exists('./upload/asset/model')) {
				mkdir('./upload/asset/model', 0777, true);
			}

			//secure check for extension
			$ext = pathinfo($file_name, PATHINFO_EXTENSION);
			if(in_array($ext,$whitelist) ) {
				$dest_filename = $dest_dir.$file_name;
				if(move_uploaded_file($_FILES['file1']['tmp_name'], $dest_filename))
				{
					//content check security
					$file_content = file_get_contents($dest_filename, true);
					if(preg_match('{\<\?php}',$file_content) || preg_match('/system\(/',$file_content)) 
					{
						echo DisplayMessage('error',T_("Fichier interdit"));
						unlink($dest_filename); //remove file
						if($rparameters['log']) {logit('security','File upload blocked on admin list asset model ',$_SESSION['user_id']);}
						exit;
					}
				} 
			
				$qry=$db->prepare("UPDATE `tassets_model` SET `type`=:type, `manufacturer`=:manufacturer, `name`=:name, `image`=:image,`ip`=:ip,`wifi`=:wifi,`warranty`=:warranty WHERE `id`=:id");
				$qry->execute(array(
					'type' => $_POST['type'],
					'manufacturer' => $_POST['manufacturer'],
					'name' => $_POST['model'],
					'image' => $file_name,
					'ip' => $_POST['ip'],
					'wifi' => $_POST['wifi'],
					'warranty' => $_POST['warranty'],
					'id' => $_GET['id']
					));
				
			} else {
				echo DisplayMessage('error',T_("Blocage de sécurité fichier interdit"));
				if($rparameters['log']) {logit('security','File upload blocked on admin list asset model ',$_SESSION['user_id']);}
			}
		} else {
			$qry=$db->prepare("UPDATE `tassets_model` SET `type`=:type, `manufacturer`=:manufacturer, `name`=:name, `ip`=:ip,`wifi`=:wifi,`warranty`=:warranty WHERE `id`=:id");
			$qry->execute(array(
				'type' => $_POST['type'],
				'manufacturer' => $_POST['manufacturer'],
				'name' => $_POST['model'],
				'ip' => $_POST['ip'],
				'wifi' => $_POST['wifi'],
				'warranty' => $_POST['warranty'],
				'id' => $_GET['id']
				));
		}
	}
	else
	{
		for ($i=0; $i <= $nbchamp; $i++)
		{
			$reqchamp="${'champ' . $i}";
			if(!isset($_POST[$reqchamp])) $_POST[$reqchamp] = '';
			$_POST[$reqchamp] = strip_tags($db->quote($_POST[$reqchamp])); 
			if($i=='1') $set="`$reqchamp`=$_POST[$reqchamp]"; else $set="$set, `$reqchamp`=$_POST[$reqchamp]";
		}
		$db->exec("UPDATE $db_table SET $set WHERE id=$db_id");
	}

	$www = "./index.php?page=admin&subpage=list&table=$_GET[table]&action=disp_list&hide_disabled_values=$_GET[hide_disabled_values]";
	echo '<script language="Javascript">
	<!--
	document.location.replace("'.$www.'");
	// -->
	</script>';
}

if($_GET['action']=="disp_edit")
{
    //check right before display list
    if(
        $rright['admin']!='0' ||
        ($_GET['table']=='tcategory' && $rright['admin_lists_category']!='0') ||
        ($_GET['table']=='tsubcat' && $rright['admin_lists_subcat']!='0') ||
        ($_GET['table']=='tcriticality' && $rright['admin_lists_criticality']!='0') ||
        ($_GET['table']=='tpriority' && $rright['admin_lists_priority']!='0') ||
        ($_GET['table']=='ttypes' && $rright['admin_lists_type']!='0')
    )
    {
        echo '
            <div class="pr-4 pl-4">
                <div class="widget-box">
                    <div class="pt-4 pb-2">
                        <h5 class="text-primary-m2"><i class="fa fa-pencil-alt"><!----></i> '.T_("Édition d'une entrée").' :</h5>
                        <hr class="mb-3 border-dotted">
                    </div>
                    <div class="widget-body">
                        <div class="widget-main no-padding">
                            <form method="post" enctype="multipart/form-data" action="./index.php?page=admin&amp;subpage=list&amp;table='.$_GET['table'].'&amp;action=update&amp;id='.$_GET['id'].'&amp;hide_disabled_values='.$_GET['hide_disabled_values'].'" >
                            ';
                                //specific views 
                                if($_GET['table']=='tcategory')
                                {
                                    //find value
                                    $qry=$db->prepare("SELECT `name`,`number`,`technician`,`technician_group` FROM `tcategory` WHERE id=:id");
                                    $qry->execute(array('id' => $_GET['id']));
                                    $row=$qry->fetch();
                                    $qry->closeCursor();
                                    
                                    echo'
                                    <fieldset>
                                        <label for="number">'.T_('Ordre').'</label>
                                        <input style="width:auto" class="form-control form-control-sm d-inline-block" name="number" type="text" value="'.$row['number'].'" />
                                        <br />
                                        <br />
                                        <label for="category">'.T_('Catégorie').'</label>
                                        <input style="width:auto" class="form-control form-control-sm d-inline-block" name="category" type="text" value="'.$row['name'].'" />
                                        ';
                                        //service limit case
                                        if($rparameters['user_limit_service'])
                                        {
                                            //find service value
                                            $qry=$db->prepare("SELECT `service` FROM `tcategory` WHERE id=:id");
                                            $qry->execute(array('id' => $_GET['id']));
                                            $row2=$qry->fetch();
                                            $qry->closeCursor();
                                            
                                            if($cnt_service==1) //not show select field, if there are only one service, send data in background
                                            {
                                                echo '<input type="hidden" name="service" value="'.$row2['service'].'" />'; 
                                            } else { //display select box for service
                                                echo '
                                                    <div class="space-10"></div>
                                                    <label for="service">'.T_('Service').'</label>
                                                    <select style="width:auto" class="form-control form-control-sm d-inline-block" name="service" id="form-field-select-1" >
                                                    ';
                                                        if($rright['dashboard_service_only']!=0 && $rparameters['user_limit_service']==1) {
                                                            //display only service associated with this user
                                                            $qry=$db->prepare("SELECT `tservices`.`id`,`tservices`.`name` FROM `tservices`,`tusers_services` WHERE `tservices`.`id`=`tusers_services`.`service_id` AND `tusers_services`.`user_id`=:user_id AND `tservices`.`disable`='0' ORDER BY `tservices`.`name`");
                                                            $qry->execute(array('user_id' => $_SESSION['user_id']));
                                                        } else {
                                                            //display all services
                                                            $qry=$db->prepare("SELECT `id`,`name` FROM `tservices` WHERE `disable`='0' ORDER BY `name`");
                                                            $qry->execute();
                                                        }
                                                        while ($row3=$qry->fetch()) 
                                                        {
                                                            echo '
                                                            <option '; if($row2['service']==$row3['id']) {echo 'selected';} echo ' value="'.$row3['id'].'">
                                                                '.$row3['name'].'
                                                            </option>';
                                                        }
                                                        $qry->closeCursor();
                                                    echo '
                                                    </select>
                                                ';
                                            }
                                        }
                                        //auto tech attribute case
                                        if($rparameters['ticket_cat_auto_attribute'])
                                        {
                                            //display technician list
                                            echo '
                                                <div class="space-10"></div>
                                                <label for="technician">'.T_('Attribution automatique ').'</label>
                                                <select style="width:auto" class="form-control form-control-sm d-inline-block" name="technician" id="form-field-select-1" >
                                                ';
                                                $qry2=$db->prepare("SELECT `id`,`firstname`,`lastname` FROM `tusers` WHERE (`profile`='0' OR `profile`='4') AND `disable`='0' OR `id`='0' ORDER BY `id`!=0,`lastname`");
                                                $qry2->execute();
                                                while ($row2=$qry2->fetch()) 
                                                {
                                                    echo '
                                                    <option '; if($row['technician']==$row2['id']) {echo 'selected';} echo ' value="'.$row2['id'].'">
                                                        '.$row2['firstname'].' '.$row2['lastname'].'
                                                    </option>';
                                                }
                                                $qry2->closeCursor();
                                                echo '
                                                </select>
                                                '.T_('ou').'
                                                <select style="width:auto" class="form-control form-control-sm d-inline-block" name="technician_group" id="form-field-select-1" >
                                                ';
                                                $qry2=$db->prepare("SELECT `id`,`name` FROM `tgroups` WHERE `type`='1' AND `disable`='0' OR `id`='0' ORDER BY `id`!=0,`name`");
                                                $qry2->execute();
                                                while ($row2=$qry2->fetch()) 
                                                {
                                                    echo '
                                                    <option '; if($row['technician_group']==$row2['id']) {echo 'selected';} echo ' value="'.$row2['id'].'">
                                                        '.$row2['name'].'
                                                    </option>';
                                                }
                                                $qry2->closeCursor();
                                                echo '
                                                </select>
                                            ';
                                        }
                                    echo '</fieldset>';
                                }elseif($_GET['table']=='tcriticality')
                                {
                                    //find value
                                    $qry=$db->prepare("SELECT `number`,`name`,`color` FROM `tcriticality` WHERE id=:id");
                                    $qry->execute(array('id' => $_GET['id']));
                                    $row=$qry->fetch();
                                    $qry->closeCursor();
                                        
                                    echo'
                                    <fieldset>
                                        <label for="number">'.T_('Numéro').'</label>
                                        <input style="width:auto" class="form-control form-control-sm d-inline-block" name="number" type="text" value="'.$row['number'].'" />
                                        <br /><br />
                                        <label for="name">'.T_('Nom').'</label>
                                        <input style="width:auto" class="form-control form-control-sm d-inline-block" name="name" type="text" value="'.$row['name'].'" />
                                        <br /><br />
                                        <label for="color">'.T_('Couleur').'</label>
                                        <input style="width:auto" class="form-control form-control-sm d-inline-block" name="color" type="text" value="'.$row['color'].'" />
                                        <br /><br />
                                    ';
                                    
                                    if($rparameters['user_limit_service'])
                                    {
                                        $qry=$db->prepare("SELECT `service` FROM `tcriticality` WHERE id=:id");
                                        $qry->execute(array('id' => $_GET['id']));
                                        $row=$qry->fetch();
                                        $qry->closeCursor();
                                        
                                        if($cnt_service==1) //not show select field, if there are only one service, send data in background
                                        {
                                            echo '<input type="hidden" name="service" value="'.$row['service'].'" />'; 
                                        } else {
                                            echo '
                                            <label for="service">'.T_('Service').'</label>
                                            <select style="width:auto" class="form-control form-control-sm d-inline-block" name="service" id="form-field-select-1" >
                                            ';
                                                if($rright['dashboard_service_only']!=0 && $rparameters['user_limit_service']==1 && $_SESSION['profile_id']!=4) {
                                                    //display only service associated with this user
                                                    $qry=$db->prepare("SELECT `tservices`.`id`,`tservices`.`name` FROM `tservices`,`tusers_services` WHERE `tservices`.`id`=`tusers_services`.`service_id` AND `tusers_services`.`user_id`=:user_id AND `tservices`.`disable`='0' ORDER BY `tservices`.`name`");
                                                    $qry->execute(array('user_id' => $_SESSION['user_id']));
                                                    
                                                } else {
                                                    //display all services
                                                    $qry=$db->prepare("SELECT `id`,`name` FROM `tservices` WHERE `disable`='0' ORDER BY `name`");
                                                    $qry->execute();
                                                }
                                                while ($row2=$qry->fetch()) 
                                                {
                                                    echo '
                                                    <option '; if($row['service']==$row2['id']) {echo 'selected';} echo ' value="'.$row2['id'].'">
                                                        '.$row2['name'].'
                                                    </option>';
                                                }
                                                $qry->closeCursor();
                                            echo '
                                            </select>
                                            ';
                                        }
                                    }
                                    echo '
                                    <fieldset>
                                    <div class=\"space-4\"></div>';
                                }elseif($_GET['table']=='tpriority')
                                {
                                    //find value
                                    $qry=$db->prepare("SELECT `number`,`name`,`color` FROM `tpriority` WHERE id=:id");
                                    $qry->execute(array('id' => $_GET['id']));
                                    $row=$qry->fetch();
                                    $qry->closeCursor();
                                    
                                    echo'
                                    <fieldset>
                                        <label for="number">'.T_('Numéro').'</label>
                                        <input style="width:auto" class="form-control form-control-sm d-inline-block" name="number" type="text" value="'.$row['number'].'" />
                                        <br /><br />
                                        <label for="name">'.T_('Nom').'</label>
                                        <input style="width:auto" class="form-control form-control-sm d-inline-block" name="name" type="text" value="'.$row['name'].'" />
                                        <br /><br />
                                        <label for="color">'.T_('Couleur').'</label>
                                        <input style="width:auto" class="form-control form-control-sm d-inline-block" name="color" type="text" value="'.$row['color'].'" />
                                        <br /><br />
                                    ';
                                    
                                    if($rparameters['user_limit_service'])
                                    {
                                        //find value
                                        $qry=$db->prepare("SELECT `service` FROM `tpriority` WHERE id=:id");
                                        $qry->execute(array('id' => $_GET['id']));
                                        $row=$qry->fetch();
                                        $qry->closeCursor();
                                        
                                        if($cnt_service==1) //not show select field, if there are only one service, send data in background
                                        {
                                            echo '<input type="hidden" name="service" value="'.$row['service'].'" />'; 
                                        } else {
                                            echo '
                                            <label for="service">'.T_('Service').'</label>
                                            <select style="width:auto" class="form-control form-control-sm d-inline-block" name="service" id="form-field-select-1" >
                                            ';
                                                if($rright['dashboard_service_only']!=0 && $rparameters['user_limit_service']==1 && $_SESSION['profile_id']!=4) {
                                                    //display only service associated with this user
                                                    $qry=$db->prepare("SELECT `tservices`.`id`,`tservices`.`name` FROM `tservices`,`tusers_services` WHERE `tservices`.`id`=`tusers_services`.`service_id` AND `tusers_services`.`user_id`=:user_id AND `tservices`.`disable`='0' ORDER BY `tservices`.`name`");
                                                    $qry->execute(array('user_id' => $_SESSION['user_id']));
                                                } else {
                                                    //display all services
                                                    $qry=$db->prepare("SELECT `id`,`name` FROM `tservices` WHERE `disable`='0' ORDER BY `name`");
                                                    $qry->execute();
                                                }
                                                while ($row2=$qry->fetch()) 
                                                {
                                                    echo '
                                                    <option '; if($row['service']==$row2['id']) {echo 'selected';} echo ' value="'.$row2['id'].'">
                                                        '.$row2['name'].'
                                                    </option>';
                                                }
                                                $qry->closeCursor();
                                            echo '
                                            </select>
                                            ';
                                        }
                                    }
                                    echo '
                                    <fieldset>
                                    <div class=\"space-4\"></div>';
                                }elseif($_GET['table']=='ttypes')
                                {
                                    //find value
                                    $qry=$db->prepare("SELECT `name`,`user_validation` FROM `ttypes` WHERE `id`=:id");
                                    $qry->execute(array('id' => $_GET['id']));
                                    $row=$qry->fetch();
                                    $qry->closeCursor();
                                    echo'
                                    <fieldset>
                                        <label for="name">'.T_('Nom').'</label>
                                        <input style="width:auto" class="form-control form-control-sm d-inline-block" name="name" type="text" value="'.$row['name'].'" />
                                        <div class="pt-2"></div>
                                    ';
                                    if($rparameters['user_validation'])
                                    {
                                        echo '
                                        <label for="user_validation">'.T_('Validation demandeur').'&nbsp;</label>
                                        <input type="radio" class="ace" value="1" name="user_validation"'; if($row['user_validation']==1) {echo "checked";} echo ' > <span class="lbl"> '.T_('Oui').' </span>
                                        <input type="radio" class="ace" value="0" name="user_validation"'; if($row['user_validation']==0) {echo "checked";} echo ' > <span class="lbl"> '.T_('Non').' </span>
                                        <div class="pt-2"></div>
                                        ';
                                    }
                                    if($rparameters['user_limit_service'])
                                    {
                                        //find value
                                        $qry=$db->prepare("SELECT `service` FROM `ttypes` WHERE `id`=:id");
                                        $qry->execute(array('id' => $_GET['id']));
                                        $row=$qry->fetch();
                                        $qry->closeCursor();
                                        
                                        if($cnt_service==1) //not show select field, if there are only one service, send data in background
                                        {
                                            echo '<input type="hidden" name="service" value="'.$row['service'].'" />'; 
                                        } else {
                                            echo '
                                            <label for="service">'.T_('Service').'</label>
                                            <select style="width:auto" class="form-control form-control-sm d-inline-block" name="service" id="form-field-select-1" >
                                            ';
                                                if($rright['dashboard_service_only']!=0 && $rparameters['user_limit_service']==1) {
                                                    //display only service associated with this user
                                                    $qry=$db->prepare("SELECT `tservices`.`id`,`tservices`.`name` FROM `tservices`,`tusers_services` WHERE `tservices`.`id`=`tusers_services`.`service_id` AND `tusers_services`.`user_id`=:user_id AND `tservices`.`disable`='0' ORDER BY `tservices`.`name`");
                                                    $qry->execute(array('user_id' => $_SESSION['user_id']));
                                                } else {
                                                    //display all services
                                                    $qry=$db->prepare("SELECT `id`,`name` FROM `tservices` WHERE `disable`='0' ORDER BY `name`");
                                                    $qry->execute();
                                                }
                                                while ($row2=$qry->fetch()) 
                                                {
                                                    echo '
                                                    <option '; if($row['service']==$row2['id']) {echo 'selected';} echo ' value="'.$row2['id'].'">
                                                        '.$row2['name'].'
                                                    </option>';
                                                }
                                                $qry->closeCursor();
                                            echo '
                                            </select>
                                            ';
                                        }
                                    }
                                    echo '
                                    <fieldset>
                                    <div class=\"space-4\"></div>';
                                }elseif($_GET['table']=='tsubcat')
                                {
                                        //find value
                                        $qry=$db->prepare("SELECT `id`,`name`,`cat`,`technician`,`technician_group` FROM `tsubcat` WHERE `id`=:id");
                                        $qry->execute(array('id' => $_GET['id']));
                                        $row=$qry->fetch();
                                        $qry->closeCursor();
                                        
                                        //find category name
                                        $qry=$db->prepare("SELECT `id` FROM `tcategory` WHERE `id`=:id");
                                        $qry->execute(array('id' => $row['cat']));
                                        $rowcatfind=$qry->fetch();
                                        $qry->closeCursor();
                                    
                                        echo '
                                            <fieldset>
                                                <label for="cat">'.T_('Catégorie').'</label>
                                                <select style="width:auto" class="form-control form-control-sm d-inline-block" name="cat" id="form-field-select-1">
                                                ';
                                                    if($rright['dashboard_service_only']!=0 && $rparameters['user_limit_service']==1) {
                                                        //display only category associated services of this current user
                                                        $qry=$db->prepare("SELECT `tcategory`.`id`,`tcategory`.`name` FROM `tcategory` WHERE `tcategory`.`service` IN (SELECT `service_id` FROM `tusers_services` WHERE `user_id`=:user_id) ORDER BY `tcategory`.`name`");
                                                        $qry->execute(array('user_id' => $_SESSION['user_id']));
                                                    } else {
                                                        //display all category
                                                        $qry=$db->prepare("SELECT `id`,`name` FROM `tcategory` ORDER BY `name`");
                                                        $qry->execute();
                                                    }
                                                
                                                    while ($row2=$qry->fetch()) 
                                                    {
                                                        echo '
                                                        <option '; if($rowcatfind['id']==$row2['id']) {echo 'selected';} echo ' value="'.$row2['id'].'">
                                                            '.$row2['name'].'
                                                        </option>';
                                                    }
                                                    $qry->closeCursor();
                                                echo '
                                                </select>
                                                <div class="space-4"></div>
                                                <label for="subcat">'.T_('Sous-catégorie').'</label>
                                                <input style="width:auto" class="form-control form-control-sm d-inline-block" name="subcat" type="text" value="'.$row['name'].'" />
                                                ';
                                                //auto tech attribute case
                                                if($rparameters['ticket_cat_auto_attribute'])
                                                {
                                                    //display technician list
                                                    echo '
                                                        <div class="space-4"></div>
                                                        <label for="technician">'.T_('Attribution automatique ').'</label>
                                                        <select style="width:auto" class="form-control form-control-sm d-inline-block" name="technician" id="form-field-select-1" >
                                                        ';
                                                        $qry2=$db->prepare("SELECT `id`,`firstname`,`lastname` FROM `tusers` WHERE (`profile`='0' OR `profile`='4') AND `disable`='0' OR `id`='0' ORDER BY `id`!=0,`lastname`");
                                                        $qry2->execute();
                                                        while ($row2=$qry2->fetch()) 
                                                        {
                                                            echo '<option '; if($row['technician']==$row2['id']) {echo 'selected';} echo ' value="'.$row2['id'].'">'.$row2['firstname'].' '.$row2['lastname'].'</option>';
                                                        }
                                                        $qry2->closeCursor();
                                                        echo '
                                                        </select>
                                                        '.T_('ou').'
                                                        <select style="width:auto" class="form-control form-control-sm d-inline-block" name="technician_group" id="form-field-select-1" >
                                                        ';
                                                        $qry2=$db->prepare("SELECT `id`,`name` FROM `tgroups` WHERE `type`='1' AND `disable`='0' OR `id`='0' ORDER BY `id`!=0,`name`");
                                                        $qry2->execute();
                                                        while ($row2=$qry2->fetch()) 
                                                        {
                                                            echo '<option '; if($row['technician_group']==$row2['id']) {echo 'selected';} echo ' value="'.$row2['id'].'">'.$row2['name'].'</option>';
                                                        }
                                                        $qry2->closeCursor();
                                                        echo '
                                                        </select>
                                                    ';
                                                }
                                                echo '
                                            </fieldset>';
                                } 
                                elseif($_GET['table']=='tassets_model')
                                {
                                        //find value
                                        $qry=$db->prepare("SELECT * FROM `tassets_model` WHERE `id`=:id");
                                        $qry->execute(array('id' => $_GET['id']));
                                        $req=$qry->fetch();
                                        $qry->closeCursor();
                                        
                                        //find type name
                                        $qry=$db->prepare("SELECT `id` FROM `tassets_type` WHERE `id`=:id");
                                        $qry->execute(array('id' => $req['type']));
                                        $row=$qry->fetch();
                                        $qry->closeCursor();
                                        
                                        //find manufacturer name
                                        $qry=$db->prepare("SELECT `id` FROM `tassets_manufacturer` WHERE `id`=:id");
                                        $qry->execute(array('id' => $req['manufacturer']));
                                        $rowmodelfind=$qry->fetch();
                                        $qry->closeCursor();
                                        
                                        echo '
                                            <fieldset>
                                                <label for="type">'.T_('Type').'</label>
                                                <select style="width:auto" class="form-control form-control-sm d-inline-block" name="type" id="form-field-select-1">
                                                ';
                                                    $qry=$db->prepare("SELECT `id`,`name` FROM `tassets_type` ORDER BY `name`");
                                                    $qry->execute();
                                                    while ($rtype=$qry->fetch()) 
                                                    {
                                                        echo '
                                                        <option '; if($row['id']==$rtype['id']) {echo 'selected';} echo ' value="'.$rtype['id'].'">
                                                            '.$rtype['name'].'
                                                        </option>';
                                                    }
                                                    $qry->closeCursor();
                                                echo '
                                                </select>
                                                <div class="space-4"></div>
                                                <label for="manufacturer">'.T_('Fabriquant').'</label>
                                                <select style="width:auto" class="form-control form-control-sm d-inline-block" name="manufacturer" id="form-field-select-1">
                                                ';
                                                    $qry=$db->prepare("SELECT `id`,`name` FROM `tassets_manufacturer` ORDER BY `name`");
                                                    $qry->execute();
                                                    while ($rman=$qry->fetch()) 
                                                    {
                                                        echo '
                                                        <option '; if($rowmodelfind['id']==$rman['id']) {echo 'selected';} echo ' value="'.$rman['id'].'">
                                                            '.$rman['name'].'
                                                        </option>';
                                                    }
                                                    $qry->closeCursor();
                                                echo '
                                                </select>
                                                <div class="space-4"></div>
                                                <label for="file1">'.T_('Image').': <span style="font-size: x-small;"><i>(250px x 250px max)</i></span></label>
                                                ';
                                                //display existing image
                                                if($req['image'] && file_exists('./images/model/'.$req['image'])) {echo '<br /><img src="./images/model/'.$req['image'].'" /> <br /><br />';}
                                                elseif($req['image'] && file_exists('./upload/asset/model/'.$req['image'])) {echo '<br /><img src="./upload/asset/model/'.$req['image'].'" /> <br /><br />';}
                                                echo '
                                                <input type="hidden" name="MAX_FILE_SIZE" value="100000000" />
                                                <input name="file1" type="file"  />
                                                <div class="space-4"></div>
                                                <label for="model">'.T_('Modèle').'</label>
                                                <input style="width:auto" class="form-control form-control-sm d-inline-block" name="model" type="text" value="'.$req['name'].'" />
                                                <div class="space-4"></div>
                                                ';
                                                if($rparameters['asset_ip']==1)
                                                {
                                                    echo '
                                                    <label for="ip">'.T_('Équipement IP').'&nbsp;</label>
                                                    <input type="radio" class="ace" value="1" name="ip"'; if($req['ip']==1) {echo "checked";} echo ' > <span class="lbl"> '.T_('Oui').' </span>
                                                    <input type="radio" class="ace" value="0" name="ip"'; if($req['ip']==0) {echo "checked";} echo ' > <span class="lbl"> '.T_('Non').' </span>
                                                    <div class="space-4"></div>
                                                    <label for="wifi">'.T_('Équipement Wifi').'&nbsp;</label>
                                                    <input type="radio" class="ace" value="1" name="wifi"'; if($req['wifi']==1) {echo "checked";} echo ' > <span class="lbl"> '.T_('Oui').' </span>
                                                    <input type="radio" class="ace" value="0" name="wifi"'; if($req['wifi']==0) {echo "checked";} echo ' > <span class="lbl"> '.T_('Non').' </span>
                                                    <div class="space-4"></div>
                                                    ';
                                                } else {echo '<input type="hidden" name="ip" value="0" /><input type="hidden" name="wifi" value="0" />';}
                                                echo '
                                                <label for="warranty">'.T_("Nombre d'années de garantie").'</label>
                                                <input style="width:auto" class="form-control form-control-sm d-inline-block" name="warranty" type="text" size="2" value="'.$req['warranty'].'" />
                                            </fieldset>
                                        ';
                                } 
                                else 
                                {
                                    for ($i=1; $i <= $nbchamp; $i++)
                                    {
                                        $query2 = $db->query("SELECT `${'champ' . $i}` FROM $db_table WHERE id=$db_id"); 
                                        $req = $query2->fetch();
                                        $query2->closeCursor();
                                        
                                        //translate label name
                                        $label_name=${'champ' . $i}; //default value
                                        if(${'champ' . $i}=='id') {$label_name=T_('Identifiant');}
                                        if(${'champ' . $i}=='name') {$label_name=T_('Libellé');}
                                        if(${'champ' . $i}=='name') {$label_name=T_('Libellé');}
                                        if(${'champ' . $i}=='cat') {$label_name=T_('Catégorie');}
                                        if(${'champ' . $i}=='disable') {$label_name=T_('Désactivé');}
                                        if(${'champ' . $i}=='number') {$label_name=T_('Ordre');}
                                        if(${'champ' . $i}=='color') {$label_name=T_('Couleur');}
                                        if(${'champ' . $i}=='description') {$label_name=T_('Description');}
                                        if(${'champ' . $i}=='mail_object') {$label_name=T_('Objet du mail');}
                                        if(${'champ' . $i}=='display') {$label_name=T_("Couleur d'affichage");}
                                        if(${'champ' . $i}=='incident') {$label_name=T_("Numéro ticket");}
                                        if(${'champ' . $i}=='address') {$label_name=T_("Adresse");}
                                        if(${'champ' . $i}=='zip') {$label_name=T_("Code postal");}
                                        if(${'champ' . $i}=='city') {$label_name=T_("Ville");}
                                        if(${'champ' . $i}=='country') {$label_name=T_("Pays");}
                                        if(${'champ' . $i}=='limit_ticket_number') {$label_name=T_("Nombre de limite de ticket");}
                                        if(${'champ' . $i}=='limit_ticket_days') {$label_name=T_("Nombre de limite de jours");}
                                        if(${'champ' . $i}=='limit_ticket_date_start') {$label_name=T_("Date de début de la limite de jours");}
                                        if(${'champ' . $i}=='limit_hour_number') {$label_name=T_("Nombre de limite d'heures");}
                                        if(${'champ' . $i}=='limit_hour_days') {$label_name=T_("Nombre de limite de jours");}
                                        if(${'champ' . $i}=='limit_hour_date_start') {$label_name=T_("Date de début de la limite de jours");}
                                        if(${'champ' . $i}=='min') {$label_name=T_("Minutes");}
                                        if(${'champ' . $i}=='virtualization') {$label_name=T_("Virtualisation");}
                                        if(${'champ' . $i}=='manufacturer') {$label_name=T_("Fabricant");}
                                        if(${'champ' . $i}=='image') {$label_name=T_("Image");}
                                        if(${'champ' . $i}=='ip') {$label_name=T_("Équipement IP");}
                                        if(${'champ' . $i}=='type') {$label_name=T_("Type");}
                                        if(${'champ' . $i}=='wifi') {$label_name=T_("Équipement WIFI");}
                                        if(${'champ' . $i}=='warranty') {$label_name=T_("Années de garantie");}
                                        if(${'champ' . $i}=='order') {$label_name=T_("Ordre");}
                                        if(${'champ' . $i}=='block_ip_search') {$label_name=T_("Blocage de recherche IP");}
                                        if(${'champ' . $i}=='mail') {$label_name=T_("Adresse mail");}
                                        if(${'champ' . $i}=='service') {$label_name=T_("Service");}
                                        if(${'champ' . $i}=='network') {$label_name=T_("Réseau");}
                                        if(${'champ' . $i}=='netmask') {$label_name=T_("Masque");}
                                        if(${'champ' . $i}=='scan') {$label_name=T_("Scan");}
                                        if(${'champ' . $i}=='meta') {$label_name=T_("État à traiter");}
                                        if(${'champ' . $i}=='user_validation') {$label_name=T_("Validation demandeur");}
                                        
                                        //hide specific field 
                                        if((${'champ' . $i}=='limit_ticket_number' || ${'champ' . $i}=='limit_ticket_days' || ${'champ' . $i}=='limit_ticket_date_start') && !$rparameters['company_limit_ticket'] && $_GET['table']=='tcompany')
                                        {
                                            //hide company limit ticket if parameter is off
                                        }elseif((${'champ' . $i}=='limit_hour_number' || ${'champ' . $i}=='limit_hour_days' || ${'champ' . $i}=='limit_hour_date_start') && !$rparameters['company_limit_hour'] && $_GET['table']=='tcompany')
                                        {
                                            //hide company limit hour if parameter is off
                                        } else {
                                            echo "
                                            <fieldset>
                                                <label for=\"${'champ' . $i}\">$label_name</label>
                                                    <input style=\"width:auto\" class=\"form-control form-control-sm d-inline-block\"  name=\"${'champ' . $i}\" type=\"text\" value=\"$req[0]\" />
                                            </fieldset>
                                            <div class=\"space-4\"></div>
                                            ";
                                        }
                                    }
                                }
                                
                                //display color informations and information on critical table
                                if(($_GET['table']=='tcriticality' || $_GET['table']=='tpriority') && ($_GET['action']=='disp_edit' || $_GET['action']=='disp_add'))
                                {
                                    echo T_('Liste des couleurs par défaut').' : ';
                                    echo '<b><span style="color:#82af6f">#82af6f</span></b>&nbsp;';
                                    echo '<b><span style="color:#f8c806">#f8c806</span></b>&nbsp;';
                                    echo '<b><span style="color:#f89406">#f89406</span></b>&nbsp;';
                                    echo '<b><span style="color:#d15b47">#d15b47</span></b>&nbsp;';
                                    echo '<br /><br /><i class="fa fa-question-circle text-primary-m2"></i> '.T_("Le numéro permet de sélectionner l'ordre de trie");
                                }
                                if(($_GET['table']=='tstates' || $_GET['table']=='tassets_state'))
                                {
                                    echo ''.T_('Liste des styles par défaut').' :<br />';
                                    echo '<span class="badge text-75 border-l-3 brc-black-tp8 bgc-success text-white">badge text-75 border-l-3 brc-black-tp8 bgc-success text-white</span><br />';
                                    echo '<span class="badge text-75 border-l-3 brc-black-tp8 bgc-danger text-white">badge text-75 border-l-3 brc-black-tp8 bgc-danger text-white</span><br />';
                                    echo '<span class="badge text-75 border-l-3 brc-black-tp8 bgc-warning text-white">badge text-75 border-l-3 brc-black-tp8 bgc-warning text-white</span><br />';
                                    echo '<span class="badge text-75 border-l-3 brc-black-tp8 bgc-primary text-white">badge text-75 border-l-3 brc-black-tp8 bgc-primary text-white</span><br />';
                                    echo '<span class="badge text-75 border-l-3 brc-black-tp8 bgc-secondary text-white">badge text-75 border-l-3 brc-black-tp8 bgc-secondary text-white</span><br />';
                                    echo '<span class="badge text-75 border-l-3 brc-black-tp8 bgc-info text-white">badge text-75 border-l-3 brc-black-tp8 bgc-info text-white</span><br />';
                                    echo '<span class="badge text-75 border-l-3 brc-black-tp8 bgc-dark text-white">badge text-75 border-l-3 brc-black-tp8 bgc-dark text-white</span><br />';
                                    echo '<span class="badge text-75 border-l-3 brc-black-tp8 bgc-pink text-white">badge text-75 border-l-3 brc-black-tp8 bgc-pink text-white</span><br />';
                                    echo '<span class="badge text-75 border-l-3 brc-black-tp8 bgc-purple text-white">badge text-75 border-l-3 brc-black-tp8 bgc-purple text-white</span><br />';
                                    echo '<span class="badge text-75 border-l-3 brc-black-tp8 bgc-yellow">badge text-75 border-l-3 brc-black-tp8 bgc-yellow</span><br />';
                                    echo '<span class="badge text-75 border-l-3 brc-black-tp8 bgc-grey text-white">badge text-75 border-l-3 brc-black-tp8 bgc-grey text-white</span><br />';
                                    echo '<span class="badge text-75 border-l-3 brc-black-tp8 bgc-light">badge text-75 border-l-3 brc-black-tp8 bgc-light</span><br />';
                                    echo '<span class="badge text-75 border-l-3 brc-black-tp8 bgc-default text-white">badge text-75 border-l-3 brc-black-tp8 bgc-default text-white</span><br />';
                                    echo '<span class="badge text-75 border-l-3 brc-black-tp8 bgc-brown text-white">badge text-75 border-l-3 brc-black-tp8 bgc-brown text-white</span><br />';
                                    echo '<br />';
                                    echo '<span class="badge text-75 badge-info arrowed-in arrowed-in-right">badge text-75 badge-info arrowed-in arrowed-in-right</span><br />';
                                    echo '<span class="badge bgc-secondary-l1 text-dark-tp4 border-1 brc-black-tp10">badge bgc-secondary-l1 text-dark-tp4 border-1 brc-black-tp10</span><br />';
                                    echo '<span class="badge badge-warning badge-pill px-25">badge badge-warning badge-pill px-25</span><br />';
                                    echo '<span class="badge badge-sm badge-light">badge badge-sm badge-light</span><br />';
                                    echo '<span class="badge badge-sm badge-dark">badge badge-sm badge-dark</span><br />';
                                }
                                
                                echo '
                                <div class="border-t-1 brc-secondary-l1 bgc-secondary-l3 py-3 text-center mt-5">
                                    <button type="submit" class="btn btn-success">
                                        <i class="fa fa-check"><!----></i>
                                        '.T_('Modifier').'
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                        
                </div>
            </div>
        ';
    } else {
        echo DisplayMessage('error',T_("Vous n'avez pas le droit de modifier une entrée sur cette liste, contacter votre administrateur"));
    }
}
?>