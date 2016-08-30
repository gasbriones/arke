<?php

function getAwebParams($module)
{
	$db = JFactory::getDBO();
	$q="select params from ".$db->getPrefix()."extensions where element = '$module'";	
	$db->setQuery($q);
	
	foreach ($db->loadObjectList() as $row)
	{				
		$jparams = $row->params; 
	}
	$params = json_decode($jparams);
	return $params;	
}	 



?>