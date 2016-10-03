<?php
$db = $this->Model();
$form = $this->Forms();
$c = $this->Cookie();
?>
<div class="field-container inline">
Submitted by &nbsp;<br />
<?php
$userlist = $db->ReadUserList('Act');
array_unshift($userlist, array(-1,'All'));
$form->ShowList ('Submitter', $userlist, 0, $c->GetSubmitter(), 'ChangeSubmittedBy(this)');
?>
<script language="javascript">

function ChangeSubmittedBy(field) {

mycookie = new Cookie();
mycookie.SetValue("Submitter", field.value, 1);
<?php
echo $this->Ajax()->DefaultFieldQuery();
?>
}
</script> 
</div>