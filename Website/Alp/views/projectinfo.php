<?php
$db = $this->Model();
$c = $this->Cookie();
$form = $this->Forms();
$errmsg = $db->ErrorMsg();

$PrjID = $c->GetDefaultProject();

$super = ($db->IsGlobalSupervisor() || $db->IsSupervisor() == $data->orgid);

if (!$super) {
	$privs = $db->ReadMyProjectRights($data->prjid);
	if ($privs->superuser)
		$super = true;
}

$data = $db->ReadProject($PrjID);	
$org = $data->orgid;
$name = $data->name;
$priority = $data->priority;
if ($data->completed)
	$status = 'Complete';
else if ($data->status == 'A')
	$status = 'Active';
else if ($data->status == 'I')
	$status = 'Inactive';

$started = $data->started;
$targetdate = $data->targetdate;
$completed = $data->completed;
$timerpt = $data->timerpt;
$notes = $data->notes;

if ($this->UserSetting('Organizations'))
	$orgname = $db->ReadOrgName($org);

$orglist = $db->ReadCompanyList();
$statuslist = $db->GetProjectStatusList();

?>
<style type="text/css">
.portfolio {
	display:table;
	width:350px;
}
.memoarea {
	width:723px;
}
.pagesection {
	float:left;
	padding:5px;
	background-color:#FFF;
	border:1px solid #999999;
	margin:5px;
	border-radius:3px;
}
h1 > span {
	float:left;
	margin-right:5px;
}
</style>
<div id="ViewTask">
<h1><span><?php echo $data->name; ?></span>
<span>
<?php
MakeIconLink('back.png', $c->GetLastTaskPage(), 'Back');
if ($db->IsSupervisor())
	MakeIconLink('pencil.png', 'projectedit?id='.$PrjID, 'Edit');
?>
</span>
</h1><br clear="all" />
<div class="portfolio pagesection">
    <table border="0" cellpadding="0" cellspacing="5" class="tabledata">
        <tr>
            <td colspan="2" class="SectionTitle"><h2>Project Info:</h2></td>
        </tr>
<?php
if ($this->UserSetting('Organizations')) {
?>
        <tr>
            <td width="100" valign="middle" class="LabelClass">Organization:</td>
            <td><?php echo $orgname; ?></td>
        </tr>
<?php
}
?>
        <tr>
            <td valign="middle" class="LabelClass">Priority:</td>
            <td><?php echo $data->priority; ?></td>
        </tr>
        <tr>
            <td valign="middle" class="LabelClass">Status:</td>
            <td><?php echo $status; ?></td>
        </tr>
        <tr>
            <td valign="middle" class="LabelClass">Start Date:</td>
            <td><?php echo $data->started; ?></td>
        </tr>
        <tr>
            <td valign="middle" class="LabelClass">Target Date:</td>
            <td><?php echo $data->targetdate; ?></td>
        </tr>
    </table>
</div>

<div class="portfolio pagesection">
<h2>Links&nbsp;&nbsp;&nbsp;
<?php
if ($super) {
//	MakeJSIcon('plus.png', "document.getElementById('AttachImage').style.display='block'", 'Attach Image');

?>
<a href="projectlinks?id=<?php echo $PrjID; ?>" target="_blank"><img src="/image/plus.png" alt="Update Links" title="Update Links" class="icons"></a>
<?php

}
?>
</h2>
<table border="0" cellpadding="5" cellspacing="0" class="tabledata">
<?php
$ols = $db->ReadProjectLinks($PrjID);
if ($ols) {
	foreach ($ols as $o) {
?>
	<tr>
		<td><?php echo $o->linkname; ?></td>		
		<td><a href="<?php echo $o->url; ?>" target=_new><?php echo $o->url; ?></a></td>
	</tr>
<?php
	}
}
?>
</table>
</div>

<div class="portfolio pagesection">
<h2>Attachments&nbsp;&nbsp;&nbsp;
<?php
if ($super) {
	MakeJSIcon('plus.png', "document.getElementById('AttachImage').style.display='block'", 'Attach Image');

/*?>
<img src="/image/plus.png" alt="Attach Image" title="Attach Image" onClick="document.getElementById('AttachImage').style.display='block'" class="icons">
<?php*/

}
?>
</h2>
<table border="0" cellpadding="5" cellspacing="0" class="tabledata">
<?php
//$files = $db->ReadTaskFiles($TaskID);
//if ($files) {
//	foreach ($files as $f) {
?>
	<?php /*?><tr>
		<td><?php //echo $f->uploadedon; ?></td>
		<td><?php // echo $f->loadedby; ?></td>
		<td><a href="http://projectserver.nth-generation.com/showimg.php?id=<?php //echo $f->fileid; ?>" target=_new><?php //echo $f->descr; ?></a></td>
		<td></td>
		<td><img src="/image/x.png" alt="Delete" title="Delete" onClick="window.location='<?php //echo $this->Controller()."?tid=$TaskID&df=$f->fileid"; ?>'" class="icons"></td>
	</tr><?php */?>
<?php
	//}
//}
?>
</table>
</div>
</div>

<div name="AttachImage" id="AttachImage" class="memoarea pagesection" <?php echo 'style="display:none"'; ?>>
<h2>Attach File</h2>
<form enctype="multipart/form-data" method="post" action="<?php echo $this->Controller(); ?>" <?php $form->ShowOnSubmit(); ?>>
	<input type="hidden" name="MAX_FILE_SIZE" value="200000000">
	<table border="0" cellpadding="3" cellspacing="5" class="tabledata">
		<tr>
			<td><label for="FileDescr" class="LabelClass">File:</label></td>
			<td><input name="Attachment" type="file" id="Attachment"></td>
	<?php
		$form->ShowTextField ('Description', 'FileDescr', 80, 98);
	?>
		<tr>
			<td>&nbsp;</td>
			<td>
	<?php
		$form->ShowHiddenField ('TaskID', $TaskID);
		$form->ShowSubmitButton();
		$form->ShowJavaScriptButton("ToggleViews('AttachImage','ViewTask')", 'Cancel');
	?>
			</td>
		</tr>
	</table>
</form></div>


<br clear="all" />

<div class="memoarea pagesection">
<h2>Notes</h2>
    <div class="memoarea">
        <?php echo $data->notes; ?>
    </div>
</div>

<br clear="all" />
<?php
$this->LoadView('contactform');
?>
<script type="text/javascript">
function ShowEditNote(id)
{
	v = document.getElementById('ViewNote'+id);
	e = document.getElementById('EditNote'+id);
	if (v && e) {
		v.style.display = 'none';
		e.style.display = 'block';
	}
}

function HideEditNote(id)
{
	v = document.getElementById('ViewNote'+id);
	e = document.getElementById('EditNote'+id);
	if (v && e) {
		e.style.display = 'none';
		v.style.display = 'block';
	}
}
</script>
