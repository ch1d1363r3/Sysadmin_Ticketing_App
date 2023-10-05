<?php
################################################################################
# @Name : ./admin/lists/display.php
# @Description : display tables
# @Call : /admin/list.php
# @Parameters : 
# @Author : Flox
# @Create : 10/08/2021
# @Update : 10/08/2021
# @Version : 3.2.15
################################################################################
?>
<ul class="nav nav-tabs align-self-start" role="tablist">
    <?php 
    if($rright['admin_lists_category'] || $rright['admin'])
    {
        echo '
        <li class="nav-item brc-primary shadow-sm" >
            <a href="./index.php?page=admin&amp;subpage=list&amp;table=tcategory" class="nav-link text-left py-3 '; if($_GET['table']=='tcategory') {echo 'active';} echo '">
                '.T_('Catégories').'
            </a>
        </li>
        ';
    }
    if($rright['admin_lists_subcat'] || $rright['admin'])
    {
        echo '
        <li class="nav-item brc-primary shadow-sm" >
            <a href="./index.php?page=admin&amp;subpage=list&amp;table=tsubcat" class="nav-link text-left py-3 '; if($_GET['table']=='tsubcat') {echo 'active';} echo'">
                '.T_('Sous-catégories').'
            </a>
        </li>
        ';
    }
    //for user agency parameter
    if($rparameters['user_agency'] && $rright['admin'])
    {
        echo '
        <li class="nav-item brc-primary shadow-sm" >
            <a href="./index.php?page=admin&amp;subpage=list&amp;table=tagencies" class="nav-link text-left py-3 '; if($_GET['table']=='tagencies') echo 'active'; echo '">
                '.T_('Agences').'
            </a>
        </li>
        ';
    }
    if($rright['admin'])
    {
        echo '
        <li class="nav-item brc-primary shadow-sm" >
            <a href="./index.php?page=admin&amp;subpage=list&amp;table=tservices" class="nav-link text-left py-3 '; if($_GET['table']=='tservices') {echo 'active';} echo'">
                '.T_('Services').'
            </a>
        </li>
        ';
    }
    if($rright['admin_lists_priority'] || $rright['admin'])
    {
        echo '
        <li class="nav-item brc-primary shadow-sm" >
            <a href="./index.php?page=admin&amp;subpage=list&amp;table=tpriority" class="nav-link text-left py-3 '; if($_GET['table']=='tpriority') {echo 'active';} echo'">
                '.T_('Priorités').'
            </a>
        </li>
        ';
    }
    if($rright['admin_lists_criticality'] || $rright['admin'])
    {
        echo '
        <li class="nav-item brc-primary shadow-sm" >
            <a href="./index.php?page=admin&amp;subpage=list&amp;table=tcriticality" class="nav-link text-left py-3 '; if($_GET['table']=='tcriticality') {echo 'active';} echo'">
                '.T_('Criticités').'
            </a>
        </li>
        ';
    }
    if(($rparameters['ticket_type'] && $rright['admin_lists_type']) || $rright['admin'])
    {
        echo '
        <li class="nav-item brc-primary shadow-sm" >
            <a href="./index.php?page=admin&amp;subpage=list&amp;table=ttypes" class="nav-link text-left py-3 '; if($_GET['table']=='ttypes') {echo 'active';} echo'">
                '.T_('Types tickets').'
            </a>
        </li>
        ';
    }
    //display ticket type answer table if one profil have right
    $qry=$db->prepare("SELECT COUNT(`id`) FROM `trights` WHERE `ticket_type_answer_disp`!=0;");
    $qry->execute();
    $row=$qry->fetch();
    $qry->closeCursor();
    if($row[0])
    {
        echo '
        <li class="nav-item brc-primary shadow-sm">
            <a href="./index.php?page=admin&amp;subpage=list&amp;table=ttypes_answer" class="nav-link text-left py-3 '; if($_GET['table']=='ttypes_answer') {echo 'active';} echo'">
                '.T_('Types réponse tickets').'
            </a>
        </li>
        ';
    }
    if($rright['admin'])
    {
        echo '
        <li class="nav-item brc-primary shadow-sm" >
            <a href="./index.php?page=admin&amp;subpage=list&amp;table=tstates" class="nav-link text-left py-3 ';if($_GET['table']=='tstates') {echo 'active';} echo '">
                '.T_('États tickets').'
            </a>
        </li>
        ';
    }
    if($rparameters['ticket_places'] && $rright['admin'])
    {
        echo '
        <li class="nav-item brc-primary shadow-sm" >
            <a href="./index.php?page=admin&amp;subpage=list&amp;table=tplaces" class="nav-link text-left py-3 '; if($_GET['table']=='tplaces') {echo 'active';} echo '">
                '.T_('Lieux').'
            </a>
        </li>
        ';
    }
    if($rright['admin'])
    {
        echo '
        <li class="nav-item brc-primary shadow-sm" >
            <a href="./index.php?page=admin&amp;subpage=list&amp;table=ttemplates" class="nav-link text-left py-3 '; if($_GET['table']=='ttemplates') {echo 'active';} echo '">
                '.T_('Modèles tickets').'
            </a>
        </li>
        ';
    }
    //for advanced user parameter
    if($rparameters['user_advanced'] && $rright['admin'])
    {
        echo '
        <li class="nav-item brc-primary shadow-sm" >
            <a href="./index.php?page=admin&amp;subpage=list&amp;table=tcompany" class="nav-link text-left py-3 '; if($_GET['table']=='tcompany') echo 'active'; echo '">
                '.T_('Sociétés').'
            </a>
        </li>
        ';
    }
    if($rright['admin'])
    {
        echo '
        <li class="nav-item brc-primary shadow-sm">
            <a href="./index.php?page=admin&amp;subpage=list&amp;table=ttime" class="nav-link text-left py-3 '; if($_GET['table']=='ttime') {echo 'active';} echo '">
                '.T_('Temps').'
            </a>
        </li>
        ';
    }

    if($rright['admin'])
    {
        if($rparameters['asset'])
        {
            echo '
            <li class="nav-item brc-primary shadow-sm">
                <a href="./index.php?page=admin&amp;subpage=list&amp;table=tassets_type" class="nav-link text-left py-3 '; if($_GET['table']=='tassets_type') {echo 'active';} echo'">
                    '.T_('Types équipements').'
                </a>
            </li>
            <li class="nav-item brc-primary shadow-sm">
                <a href="./index.php?page=admin&amp;subpage=list&amp;table=tassets_manufacturer" class="nav-link text-left py-3 '; if($_GET['table']=='tassets_manufacturer') {echo 'active';} echo'">
                    '.T_('Fabricants équipements').'
                </a>
            </li>
            <li class="nav-item brc-primary shadow-sm">
                <a href="./index.php?page=admin&amp;subpage=list&amp;table=tassets_model" class="nav-link text-left py-3 '; if($_GET['table']=='tassets_model') {echo 'active';} echo'">
                    '.T_('Modèles équipements').'
                </a>
            </li>
            <li class="nav-item brc-primary shadow-sm">
                <a href="./index.php?page=admin&amp;subpage=list&amp;table=tassets_state" class="nav-link text-left py-3 '; if($_GET['table']=='tassets_state') {echo 'active';} echo'">
                    '.T_('États équipements').'
                </a>
            </li>
            <li class="nav-item brc-primary shadow-sm">
                <a href="./index.php?page=admin&amp;subpage=list&amp;table=tassets_location" class="nav-link text-left py-3 '; if($_GET['table']=='tassets_location') {echo 'active';} echo'">
                    '.T_('Localisations équipements').'
                </a>
            </li>
            ';
            if($rparameters['asset_ip']==1)
            {
                echo '
                <li class="nav-item brc-primary shadow-sm">
                    <a href="./index.php?page=admin&amp;subpage=list&amp;table=tassets_iface_role" class="nav-link text-left py-3 '; if($_GET['table']=='tassets_iface_role') {echo 'active';} echo'">
                        '.T_('Rôles interfaces équipements').'
                    </a>
                </li>
                <li class="nav-item brc-primary shadow-sm">
                    <a href="./index.php?page=admin&amp;subpage=list&amp;table=tassets_network" class="nav-link text-left py-3 '; if($_GET['table']=='tassets_network') {echo 'active';} echo'">
                        '.T_('Réseaux équipements').'
                    </a>
                </li>
                ';
            }
        }
    }
    ?>								
</ul>