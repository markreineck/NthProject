<h2>
<?php
$form = $this->Forms();
$db = $this->Model();
$ajax = $this->Ajax();
$args['DefaultPrj'] = $prjid;

if ($prjid > 0) {
	echo "Milestones for ".$db->ReadProjectName($prjid);

	$canedit = $db->IsProjectSuperUser($prjid);
?>
</h2>
<table class="table table-striped">
<?php
	$data = $db->ListMilestones($prjid);
?>
	<tr align="left">
		<th>Area Name</th>
		<th>Target Date</th>
		<th>Completion Date</th>
		<th></th>
	</tr>
<?php
	if ($data) {
		$stripe = 1;
		foreach ($data as $row) {
			if (empty($row->completion)) {
				$style = '';
			} else {
				$style = 'style="color:#E4201B"';
			}
?>
    <tr class="stripe<?php echo $stripe; ?>">
		<td><a href="taskbymilestone?mid=<?php echo $row->milestoneid; ?>"><?php echo $row->name; ?></a></td>
		<td><?php echo $row->target; ?></td>
		<td><?php echo $row->completedon; ?></td>
		<td>
<?php
			if ($canedit) {
?>
		<a href="milestoneedit?mid=<?php echo $row->milestoneid; ?>&pid=<?php echo $prjid; ?>"  alt="Edit Milestone" class="ActionIconLinks" ><span class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;
<?php
				if (!$row->completedon) {
					$args['msid'] = $row->milestoneid;
?>
		<a href="#complete"  alt="Complete Milestone" class="ActionIconLinks" onClick="<?php echo $ajax->Query('CompleteMilestone', 'AjaxList', $args); ?>"><span class="glyphicon glyphicon-ok"></span></a>
<?php
			}
			}
?>
		</td>
	</tr>
<?php
			$stripe = ($stripe > 1) ? 1 : 2;
		}
	}
?>
</table>
<?php
	$form->ShowRedirectButton ('milestoneedit&pid='.$prjid, 'New Milestone');
	$form->ShowRedirectButton ('projects', 'Project List');
?>
<?php 
} else {
	echo '<p>Please select a project.</p>';
}
?>


