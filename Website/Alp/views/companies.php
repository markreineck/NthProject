<?php
$db = $this->Database();
$form = $this->Forms();

if ($db->IsGlobalSupervisor()) {
?>
<h1>Organizations</h1>
<table class="ListTable">
	<form method="post">
		<tr class="tableclass">
			<th>Company Name</th>
			<th>Status</th>
			<th align="center">Active Projects</th>
			<th align="center">Active Users</th>
			<th>Action</th>
			<th></th>
			<th></th>
		</tr>
<?php
$stripe = 1;
$data = $db->GetCompanyList();

foreach ($data as $row) {
	$orgid = $row->orgid;
	$countprj = $row->pcnt;
	$countuser = $row->ucnt;
	$name = $row->name;
	$statusx = $row->status;
?>
	<tr class="stripe<?php echo $stripe; ?>">
		<td><?php echo $name; ?></td>
		<td><?php echo $statusx; ?></td>
		<td align='center'><?php echo $countprj; ?></td>
		<td align='center'><?php echo $countuser; ?></td>
		<td><a href="orgedit?id=<?php echo $orgid; ?>"><img src="/image/pencil.png" alt="Edit Project" title="Edit Project"></td>
		<td><a href="useradd?o=<?php echo $orgid; ?>"><img src="/image/adduser.png" alt="Add a User" title="Add a User"></td>
		<td><a href="projectadd?o=<?php echo $orgid; ?>"><img src="/image/addproject.png" alt="Add a Project" title="Add a Project"></td>
	</tr>
<?php
	$stripe = ($stripe > 1) ? 1 : 2;
}
?>
	</form>
</table><br>
<?php
	$form->ShowRedirectButton ('orgedit', 'Add Organization');
}
?>
<br clear="all">