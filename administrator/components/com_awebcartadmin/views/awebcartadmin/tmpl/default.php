<?php
// No direct access to this file 
defined('_JEXEC') or die('Restricted access');
$option = JRequest::getCmd('option');
?>
<h1 class='leftsidetitle'>Admin database</h1>
<br/>
<div class="lefttop">
<table cellpadding="0" cellspacing="0">
<tr><th>Database Size in KB</th></tr>
<?php

foreach ($this->dbsize as $row)
{
	$size = $row->dsize + 50 * $row->n;
	$size = $size / 1024 ;
	$size = round(100*$size)/100;
	echo "<tr><td align='center' class='sizenumber'><b>".$size."</b></td></tr>";	
}

?>
</table>
<br/><br/>
<p class="functionslabel">Useful database functions:</p>
<?php
echo"<form name='cartadminform' class='deleteempty' style='float:left' method='POST' aciton='index.php'>";
echo"<input type='hidden' name='option' value='".$option."' />";
echo"<input type='hidden' name='controller' value='delete' />";
echo"<input type='hidden' name='task' value='emptys' />";
echo"<input type='submit' name='delempty' value='Delete Empty Carts'>";
echo"</form>";
echo"<form name='cartadminform' class='deleteempty' style='float:left' method='POST' aciton='index.php'>";
echo"<input type='hidden' name='option' value='".$option."' />";
echo"<input type='hidden' name='controller' value='delete' />";
echo"<input type='hidden' name='task' value='ordered' />";
echo"<input type='submit' name='delempty' value='Delete Ordered Carts'>";
echo"</form>";
echo"<form name='cartadminform1m' class='deleteempty' style='float:right' method='POST' aciton='index.php'>";
echo"<input type='hidden' name='option' value='".$option."' />";
echo"<input type='hidden' name='controller' value='delete' />";
echo"<input type='hidden' name='task' value='cronenable' />";
echo"<input type='hidden' name='mode' value=".$this->cronmode." />";
if ($this->cronmode==0) echo"<input type='submit' name='delempty' value='Enable CronMode'>";
else echo"<input type='submit' name='delempty' value='Disable CronMode'>";
echo"</form>";
echo"<form name='cartadminform1w' class='deleteempty' style='float:left' method='POST' aciton='index.php'>";
echo"<input type='hidden' name='option' value='".$option."' />";
echo"<input type='hidden' name='controller' value='delete' />";
echo"<input type='hidden' name='task' value='older' />";
echo"<input type='hidden' name='date' value='1week' />";
echo"<input type='submit' name='delempty' value='Delete Older than 1 week'>";
echo"</form>";
echo"<form name='cartadminform1m' class='deleteempty' style='float:right' method='POST' aciton='index.php'>";
echo"<input type='hidden' name='option' value='".$option."' />";
echo"<input type='hidden' name='controller' value='delete' />";
echo"<input type='hidden' name='task' value='older' />";
echo"<input type='hidden' name='date' value='1month' />";
echo"<input type='submit' name='delempty' value='Delete Older than 1 month'>";
echo"</form>";

echo"<form name='cartadminform1m' class='deleteempty' style='float:right' method='POST' aciton='index.php'>";
echo"<input type='hidden' name='option' value='".$option."' />";
echo"<input type='hidden' name='controller' value='delete' />";
echo"<input type='hidden' name='task' value='selected' />";
echo"<input type='submit' name='delempty' value='Delete Selected'>";


?>
</div>
<div class="righttop">
<table cellpadding="3" cellspacing="2">
<tr><th>User</th><th>Cart</th><th>Date</th><th></th></tr>
<?php 
foreach ($this->carts as $k => $v) {
	 echo "<tr><td> User id: ".$k." ".$v['name']."</td><td>".$v['prod']."</td><td>".$v['date']."</td><td><input type='checkbox' value='".$k."' name='del_".$k."'></td></tr>";	
}


?>

</table>
</form>
</div>