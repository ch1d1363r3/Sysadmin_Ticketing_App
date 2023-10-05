<?php
################################################################################
# @Name : histo_load.php
# @Description : Display Statistics by categories
# @call : /stat.php
# @parameters : 
# @Author : Flox
# @Create : 15/02/2014
# @Update : 23/07/2021
# @Version : 3.2.15
################################################################################

//array declaration
$values = array();
$xnom = array();

//count
$qry=$db->prepare("SELECT COUNT(id) FROM `tincidents`");
$qry->execute();
$rtotal=$qry->fetch();
$qry->closeCursor();

$libchart=T_('Charge de travail actuelle par technicien');
$query = $db->query("
	SELECT CONCAT_WS('. ', left(tusers.firstname, 1),  tusers.lastname) as Technicien, ROUND((SUM(tincidents.time_hope-tincidents.time))/60) as Charge
	FROM tincidents 
	INNER JOIN tusers ON (tincidents.technician=tusers.id) 
	INNER JOIN tstates ON (tincidents.state=tstates.id) 
	WHERE 
	tusers.disable='0' AND
	tincidents.disable='0' AND
	tincidents.u_service LIKE '$_POST[service]' $where_service $where_agency AND
	$where_state AND
	tincidents.time_hope-tincidents.time>0 AND
	tstates.meta='1' 
	GROUP BY tusers.firstname ORDER BY Charge DESC
");
while ($row = $query->fetch()) 
{
	$r=$row[1];
	$name=addslashes(substr($row[0],0,42));
	array_push($values, $r);
	array_push($xnom, $name);
} 
$container="container5";
include('./stats/graph_histo.php');
echo "<div class=\"card-body bgc-dark-l4 p-0 border-1 brc-default-l2 radius-2 px-1 mx-n2 mx-md-0 h-100 d-flex align-items-center\" id=\"$container\"></div>";
?>