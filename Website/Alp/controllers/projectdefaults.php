<?php
include 'ProjectController.php';

class projectdefaults extends ProjectController implements AlpController {

public function __construct($url)
{
	parent::ProjectController($url);
}

function Start()
{
	if (isset($this->PostData['PrjCnt'])) {
		$err = 0;
		$cnt = $this->PostData['PrjCnt'];

		for ($x=0; $x<$cnt; $x++) {
			$atidx = 'AssignTo'.$x;
			$abidx = 'ApproveBy'.$x;
			$pridx = 'TaskPriority'.$x;
			$ididx = 'PrjID'.$x;

			if ($this->DataChanged(array($atidx, $abidx, $pridx))) {
				$db = $this->Database();

				$assn = $this->PostData[$atidx];
				$appr = $this->PostData[$abidx];
				$pri = $this->PostData[$pridx];
				$prjid = $this->PostData[$ididx];

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