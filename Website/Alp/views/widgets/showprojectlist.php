<?php
$db = $this->Model();

if ($db->IsSupervisor())
	$data = $db->ListProjects($Company, $ProjectStatus);
else if ($db->IsProjectSupervisor())
	$data = $db->ListMyProjects($Company, $ProjectStatus);

if ($data) {
?>
<table class="table table-striped">
    <tr class="ColumnTitles">
      <th>&nbsp;</th>
      <th>Project Name</th>
      <th>Priority</th>
      <th>Start Date</th>
      <th>Target Date</th>
      <th>Completion Date</th>
      <th>Company</th>
      <th></th>
      <th></th>
      <th></th>
    </tr>
<?php
	$stripe = 1;
	foreach ($data as $row) {
		$prjid = $row->prjid;
?>
    <tr class="stripe<?php echo $stripe; ?>">
      <td>&nbsp;</td>
      <td align="left" ><a href="projectinfo?id=<?php echo $prjid; ?>"><?php echo $row->name; ?></a>
        <input type="hidden" name="projectName" value="" /></td>
      <td><?php if ($row->status!='I') echo $row->priority; ?></td>
      <td><?php echo $row->started; ?></td>
      <td><?php echo $row->targetdate; ?></td>
      <td><?php echo $row->completed; ?></td>
      <td><?php echo $row->orgname; ?></td>
      <td>
<?php
		if ($row->completed) {
			$statcc = "Complete";
			$color = '#0080FF';
		} else if (($row->status=='A') || (empty($row->status))) {	
				$statcc = "Active";
				$color = '#00A000';
		} else if ($row->status=='I') {	
				$statcc = "Inactive";
				$color = '#E4201B';
		}
?>
        <div style="color:<?php echo $color; ?>"><?php echo $statcc; ?></div></td>
      <!--<td><input type=button name=pbtn54 value='Add Item <?php $ctr?>' onClick="redirect('ProjectsAddItem?id=',<?php echo $prjid; ?>)"></td>-->
      <td>
<?php
	MakeIconLink('pencil', 'projectedit?id='.$prjid, 'Edit');
/*
      	<a href='projectedit?id=<?php echo $prjid; ?>'><img src="/image/pencil.png" width="32" height="32" alt="Edit" title="Edit"></a>
*/
?>
      	</td>
<?php /*
      <td><a href='projectusers?id=<?php echo $prjid; ?>'><img src="/image/people.png" width="32" height="32" alt="Project Users" title="Project Users"></a></td>
*/ ?>
<td>
<?php
	MakeIconLink('link', 'projectlinks?id='.$prjid, 'Update Links');
/*
	<a href="projectlinks?id=<?php echo $prjid; ?>"><img src="/image/chainlink.png" width="32" alt="Update Links" title="Update Links" class="icons"></a>
*/ ?>
</td>
    </tr>
<?php
	$stripe = ($stripe > 1) ? 1 : 2;
	}
?>
  </table>
<?php
} else {
	echo 'You do not have rights to view this page.';
}
?>

