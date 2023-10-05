<?php
################################################################################
# @Name : system.php
# @Description :  admin system
# @Call : admin.php
# @Parameters : 
# @Author : Flox
# @Create : 12/01/2011
# @Update : 12/08/2021
# @Version : 3.2.15
################################################################################ 
?>
<div class="page-header position-relative">
	<h1 class="page-title text-primary-m2">
		<i class="fa fa-desktop text-primary-m"><!----></i>  <?php echo T_('Système'); ?>
	</h1>
</div>
<?php include('./system.php'); ?>
<div class="text-center mt-4">
	<button onclick='window.open("./admin/phpinfos.php?key=<?php echo $rparameters['server_private_key']; ?>")' class="btn btn-primary">
		<i class="fa fa-cogs"><!----></i>
		 <?php echo T_('Tous les paramètres PHP'); ?>
	</button>
</div>