<?php
include 'UserController.php';

class userrights extends UserController implements AlpController {

function Start()
{
	$db = $this->Model();
	$msg = '';

	if (isset($_POST['UserID'])) {
		$userid = $_POST['UserID'];

		if ($this->DataChanged('NewProject')) {

			$prj = $_POST['NewProject'];

			$db->AddUserToProject($userid, $prj);
			if (!$db->HasError())
				$msg = 'User added to project';
		} else {
			$super = $_POST['Supervisor'];
			if ($this->DataChanged(array('Supervisor','UserMaint'))) {
				$maint = $_POST['UserMaint'];
				$db->SetGlobalRights($userid, $super, $maint);
				if (!$db->HasError())
					$msg = 'User privileges changed';
			}
			if (!$super) {
				$rowcnt = $_POST['maxproject'];

				for ($x=0; $x<$rowcnt; $x++) {
					$prj = $_POST['PrjID'.$x];
					$superidx = 'Super'.$prj;
					$super = $_POST[$superidx];
					if ($this->DataChanged($superidx)) {
						if ($super)
							$db->ProjectSuperUser($userid, $prj);
						else
							$db->UpdateProjectUser($userid, $prj, 0, 0, 0, 0, 0);
					}
					if (!$super) {
						$subidx = 'Submit'.$prj;
						$appidx = 'Apprv'.$prj;
						$assidx = 'Assign'.$prj;
						$edidx = 'Edit'.$prj;
						$baidx = 'BeAssign'.$prj;
						$relidx = 'Release'.$prj;
						$appr = (isset($_POST[$appidx])) ? $_POST[$appidx] : 0;
						$rel = (isset($_POST[$relidx])) ? $_POST[$relidx] : 0;

						if ($this->DataChanged(array($subidx, $appidx, $assidx, $edidx, $baidx, $relidx))) {
							$db->UpdateProjectUser($userid, $prj, 
								$_POST[$subidx],
								$appr,
								$_POST[$assidx],
								$_POST[$baidx],
								$_POST[$edidx],
								$rel
							);
						if (!$db->HasError())
							$msg = 'User privileges changed';
						}
					}
				}
			}
		}
	} else {
		$userid = $this->GetNumber('userid');
		$prj = $this->GetNumber('delid');
		if ($prj > 0)
			$db->RemoveUserFromProject($userid, $prj);
	}

	$this->PutData ('msg', $msg);
	$this->PutData ('UserID', $userid);
	$this->LoadView('template2015');
}
}
?>
