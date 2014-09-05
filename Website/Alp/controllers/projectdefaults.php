<?php
include 'ProjectController.php';

class projectdefaults extends ProjectController implements AlpController {

public function __construct($url)
{
	parent::ProjectController($url);
}

function Start()
{
	if ($this->IsPosted('PrjCnt')) {
		$err = 0;
		$cnt = $this->PostedDigit('PrjCnt');

		for ($x=0; $x<$cnt; $x++) {
			$atidx = 'AssignTo'.$x;
			$abidx = 'ApproveBy'.$x;
			$pridx = 'TaskPriority'.$x;
			$ididx = 'PrjID'.$x;

			if ($this->DataChanged(array($atidx, $abidx, $pridx))) {
				$db = $this->Model();

				$assn = $this->PostedDigit($atidx);
				$appr = $this->PostedDigit($abidx);
				$pri = $this->PostedDigit($pridx);
				$prjid = $this->PostedDigit($ididx);

				$e = $db->UpdateProjectDefaults($prjid, $pri, $assn, $appr);
				if ($e)
					$err = $e;
			}
		}

		if (!$err) {
			$this->RedirectTo('projects');
		}
	}

	$ajax = $this->Ajax();
	$ajax->SetSection('AjaxList');
	$ajax->AddArg('sid', $this->Cookie()->GetSessionID());
	$ajax->SetFunction('GetProjectDefaultsList');
	$ajax->SetFields(array("DefaultOrg"));

	$this->PutData ('PageHeading', array('orglist'));
	$this->LoadView('home');
}
}
?>