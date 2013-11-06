<?php
// 12-28-12 Not used
if (isset($_POST['complist']))
	$pdb->SetDefaultCompany($_POST['complist']);
?>
<div class="top">
<table width="100%">
	<tr>
		<td width="150"><div style="width:150px;height:1px"></div></td>
		<td>
			<form method="post">
			Show Company &nbsp;
     			<select name="complist" id="complist" onChange="form.submit()">
	  				<option value="-1">All Companies</option>
<?php $pdb->FillCompanyList($pdb->GetDefaultCompany()); ?>
        		</select>
			</form>
		</td>
	</tr>
</table>
</div>