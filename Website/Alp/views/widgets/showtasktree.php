<?php
function ShowTaskColumnHeadings()
{
?>
<tr class="ColumnTitles">
	<th width="10%" align="center">Priority</th>
	<th width="10%" align="center">Due</th>
	<th width="10%" align="left">Assigned to</th>
	<th width="10%" align="center">Status</th>
	<th align="left" colspan="2">Task</th>
	<th width="24">&nbsp;</th>
	<th width="24">&nbsp;</th>
</tr>
<?php
}

function ShowTaskLine($c, $p, $userid, $superrights, $stripe)
{
?>
<tr class="stripe<?php echo $stripe; ?>">
	<td align="center"><?php echo $p->priority; ?></td>
	<td align="center"><?php echo $p->needed; ?></td>
	<td><?php echo $p->assigned; ?></td>
	<td align="center"><?php echo $p->status; ?> </td>
	<td width="1%"><?php $c->TaskInfoLink($p->taskid, $p->taskid); ?></a></td>
	<td width="60%"><?php $c->TaskInfoLink ($p->taskid, $p->name); ?></td>
<?php 
	ShowTaskIcons($userid, $superrights, $p);
	return ($stripe > 1) ? 1 : 2;
?>
</tr>
<?php
}

function ShowAreaLine($prjid, $areaid, $areaname, $icons=true)
{
?>
<tr class="tabletitle">
	<td width="15">
		<img src='image/openicon.png' onClick="PrjClick('ia<?php echo $areaid; ?>','a<?php echo $areaid; ?>')" id="ia<?php echo $areaid; ?>" height="15" width="15">
	</td>
	<td class="SubTitle" width='100%'><a class="child" onClick="PrjClick('ia<?php echo $areaid; ?>','a<?php echo $areaid; ?>')"><?php echo $areaname; ?></a></td>
	<td width="30">
<?php
if ($icons)
	MakeIconLink('pencil.png', 'taskedit?aid='.$areaid, 'Edit Tasks');
?>
	</td>
	<td width="30" class="SubTitle" align="right">
<?php
AddTaskToAreaLink($prjid, $areaid, $areaname)
?>
	</td>
</tr>
<?php
}

function ShowProjectLine($p, $superrights, $icons=true)
{
?>
	<tr class="tablesupertitle">
		<td width="15"><img src='image/openicon.png' onClick="PrjClick('ip<?php echo $p->prjid; ?>','p<?php echo $p->prjid; ?>')" id="ip<?php echo $p->prjid; ?>" height="15" width="15"></td>
		<td align="left" width='100%' class='SubTitle'>
<?php /*
		<a class="parent"  onClick="PrjClick('ip<?php echo $p->prjid; ?>','p<?php echo $p->prjid; ?>')"><?php echo $p->project; ?></a>
*/ ?>
		<a class="parent" href="projectinfo?id=<?php echo $p->prjid; ?>"><?php echo $p->project; ?></a>
		</td>
		<td width="30">
<?php
if ($icons)
	MakeIconLink('pencil.png', 'taskedit?pid='.$p->prjid, 'Edit Tasks');
?>
		</td>
		<td width="30" align="left" class='SubTitle'>
			<div style="float:right">
				<div style="float:right">
<?php
AddTaskToProjectLink($p->prjid);
?>
				</div>
			</div>
		</td>
	</tr>
<?php
}

function StartProjectTable($prjid)
{
?>
	<tr>
		<td></td>
		<td colspan="3">
			<table border="0" id="p<?php echo $prjid; ?>" width="100%" cellspacing="0" cellpadding="0">
<?php
}

function StartAreaTable($areaid)
{
?>
                <tr>
                  <td width="24"></td>
                  <td colspan="3">
                  <table width="100%" id="a<?php echo $areaid; ?>"  border="0" cellspacing="0" cellpadding="0" class="tabletasklist">
<?php
}

function CloseTable()
{
?>
		</table></td>
	</tr>
<?php
}
?>

<table width="100%" border='0' cellspacing="0" cellpadding="0">
<?php
$db = $this->Model();

$userid = $db->GetUserID();
$superrights = $db->IsSupervisor();
$countproj = 0;
$lastprj = 0;
$lastarea = 0;
$innercnt = 0;
$stripe = 1;
foreach ($data as $p) {
	if ($lastprj != $p->prjid) {
		$slst[$countproj]='p'.$p->prjid;
		$countproj += 1;
		$lastarea = 0;
//		Project heading
		if ($lastprj > 0) {
			CloseTable(); // Area
			CloseTable(); // Project
		}
		ShowProjectLine($p, $superrights, $icons);
		StartProjectTable($p->prjid);
	}
	if ($lastarea != $p->areaid) {
	$taskcnt = 1;
// Area heading
		if ($lastarea > 0) {
			CloseTable(); // Area
			$slst[$countproj]='a'.$p->areaid;
			$countproj += 1;
					
		}
		$lastarea = $p->areaid;
		$innercnt++;		
		$slstinner[$innercnt] = 'a'.$p->areaid;
		ShowAreaLine($p->prjid, $p->areaid, $p->areaname, $icons);
		StartAreaTable($p->areaid);		
		ShowTaskColumnHeadings();
		$stripe = 1;
	}
	
	$stripe = ShowTaskLine($this, $p, $userid, $superrights, $stripe);
	$lastprj = $p->prjid;
}

CloseTable(); // Area
CloseTable(); // Project
?>
</table>
