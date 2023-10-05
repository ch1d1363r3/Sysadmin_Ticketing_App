<?php
################################################################################
# @Name : /admin/plugin.php
# @Description : list / update / install / uninstall, available plugins
# @Call : /admin/parameters.php
# @Parameters : 
# @Author : Flox
# @Create : 12/01/2021
# @Update : 29/02/2021
# @Version : 3.2.15
################################################################################

//initialize variables 
if(!isset($_POST['submit_plugin'])) $_POST['submit_plugin']= '';
if(!isset($_POST['uninstall_plugin'])) $_POST['uninstall_plugin']= '';

//enable or disable plugin
if($_POST['submit_plugin'] && $rright['admin'])
{
    $plugins = array_diff(scandir('plugins'), array('..', '.'));
    foreach ($plugins as $plugin_scan)
    {
        $qry=$db->prepare("UPDATE `tplugins` SET `enable`=:enable WHERE `name`=:name");
        $qry->execute(array('enable' => isset($_POST[$plugin_scan]),'name' => $plugin_scan));
    }
}

if($_POST['uninstall_plugin'] && $rright['admin'])
{
    //check writeable file
    if(is_writable('./plugins/'.$_POST['uninstall_plugin'].'/_SQL/uninstall.sql')) 
    {   
        //delete SQL modifications
        $sql_file=file_get_contents('./plugins/'.$_POST['uninstall_plugin'].'/_SQL/uninstall.sql');
        $sql_file=explode(";", $sql_file);
        foreach ($sql_file as $value) {if($value!='') {$db->query($value);}} 

        //remove plugin directory
        function rrmdir($dir) {
            if(is_dir($dir)) {
                $objects = scandir($dir);
                foreach ($objects as $object) {
                    if($object != "." && $object != "..") {
                    if(filetype($dir."/".$object) == "dir") rrmdir($dir."/".$object); else unlink($dir."/".$object);
                    }
                }
                reset($objects);
                rmdir($dir);
            }
        }
        $dir='./plugins/'.$_POST['uninstall_plugin'].'/';
	    rrmdir($dir);
    } else {
        echo DisplayMessage('error',T_("Les droits d'écritures sont nécessaires sur le repertoire /plugins, pour supprimer ce plugin")); 
        exit;
    }
}

?>
<!-- /////////////////////////////////////////////////////////////// plugins part /////////////////////////////////////////////////////////////// -->
<div id="plugin" class="tab-pane <?php if($_GET['tab']=='plugin') echo 'active'; ?>">
    <form id="plugin_form" name="plugin_form" enctype="multipart/form-data" method="POST" action="">
        <div class="table-responsive">
            <table class="table table table-bordered">
                <tbody>
                    <?php
                        if(!$rright['admin']) {echo DisplayMessage('error',T_("Vous n'avez pas les droits nécessaire, contacter votre administrateur")); exit;}
                        //scan available plugins
                        $plugins = array_diff(scandir('plugins'), array('..', '.'));
                        foreach ($plugins as $plugin_scan)
                        {
                            //check if plugin is installed
                            $qry=$db->prepare("SELECT * FROM `tplugins` WHERE name=:name");
                            $qry->execute(array('name' => $plugin_scan));
                            $plugin=$qry->fetch();
                            $qry->closeCursor();
                            
                            if(empty($plugin['id'])) //install plugin
                            {
                                //sql insert
                                if(file_exists('./plugins/'.$plugin_scan.'/_SQL/install.sql'))
                                {
                                    $sql_file_install=file_get_contents('./plugins/'.$plugin_scan.'/_SQL/install.sql');
                                    $sql_file_install=explode(";", $sql_file_install);
                                    foreach ($sql_file_install as $query) {
                                        if($query!='') {$db->query($query);}
                                    } 
                                    echo DisplayMessage('success',T_('Installation du plugin').' '.$plugin_scan.' '.T_('réalisée avec succès'));
                                }
                            } else {  //existing plugin
                                //update plugin db
                                $sql_files = array_diff(scandir('plugins/'.$plugin['name'].'/_SQL'), array('..', '.','install.sql','uninstall.sql'));
                                foreach ($sql_files as $sql_file)
                                {
                                    $update_version=explode('_',$sql_file);
                                    $update_version=explode('.sql',$update_version[1]);
                                    $update_version=$update_version[0];
                                    if($update_version>$plugin['version'])
                                    {
                                        $sql_file_install=file_get_contents('plugins/'.$plugin['name'].'/_SQL/'.$sql_file);
                                        $sql_file_install=explode(";", $sql_file_install);
                                        foreach ($sql_file_install as $query) {
                                            if($query!='') {$db->query($query);}
                                        } 
                                        echo DisplayMessage('success',T_('Version').' '.$update_version.' '.T_('du plugin').' '.mb_strtolower($plugin['label']).' '.T_('installé avec succès').' ('.$sql_file.')');
                                    }
                                }
                                
                                //display plugin
                                echo '
                                <tr>
                                    <td style="width: 150px;" class="text-95 text-default-d3 bgc-secondary-l4">
                                        <i class="fa fa-'.$plugin['icon'].' text-blue-m3 pr-1"><!----></i>'.$plugin['label'].' :
                                    </td>
                                    <td class="text-95 text-default-d3">
                                        <input type="checkbox" name="'.$plugin['name'].'" '; if ($plugin['enable']) {echo "checked";} echo ' value="1" />
                                        <input type="hidden" name="plugin" value="'.$plugin['name'].'" />
                                        <span class="lbl">'.T_('Activer le plugin').' '.mb_strtolower($plugin['label']).' v'.$plugin['version'].'</span>
                                        <i title="'.T_($plugin['description']).'" class="fa fa-question-circle text-primary-m2"><!----></i>
                                        ';
                                        //uninstall plugin
                                        if(file_exists('plugins/'.$plugin['name'].'/_SQL/uninstall.sql') && $plugin['name']!='availability')
                                        {
                                            echo '
                                                <button onclick="javascript: return confirm(\''.T_('Êtes-vous sur de vouloir désinstaller ce plugin, les fichiers et les champs en base de données seront supprimés').'\');""  title="'.T_('Supprime tous les fichiers et les champs en base de données du plugin').'" name="uninstall_plugin" value="'.$plugin['name'].'" type="submit" class="ml-2 btn btn-xs btn-danger">
                                                    <i class="fa fa-trash"><!----></i>
                                                    '.T_('Désinstaller').'
                                                </button>
                                            ';
                                        }
                                        //if plugin is enabled add specific parameters
                                        if($plugin['enable'])
                                        {
                                            //display parameters
                                            if(file_exists('plugins/'.$plugin['name'].'/admin/parameters.php'))
                                            {
                                                echo '<div class="mt-2"></div>';
                                                include('plugins/'.$plugin['name'].'/admin/parameters.php');
                                            }
                                        }
                                        echo '
                                    </td>
                                </tr>
                                ';
                            }
                        }
                    ?>
                </tbody>
            </table>
        </div>
        <div class="border-t-1 brc-secondary-l1 bgc-secondary-l4 py-3 text-center">
            <button name="submit_plugin" id="submit_plugin" value="submit_plugin" type="submit" class="btn btn-success">
                <i class="fa fa-check"><!----></i>
                <?php echo T_('Valider'); ?>
            </button>
        </div>
    </form>
</div>