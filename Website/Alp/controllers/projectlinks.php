<?php
include 'ProjectController.php';

class projectlinks extends ProjectController implements AlpController {

public function __construct($url)
{
	parent::ProjectController($url);
}

function Start()
{
	if ($this->IsPosted('LinkCnt')) {
		$db = $this->Model();
		$prjid = $this->PostedDigit('ProjectID');

		for ($x=$this->PostedDigit('LinkCnt'); $x>0; $x--) {
			$idodx = 'LinkID'.$x;
			$urlidx = 'LinkURL'.$x;
			$descridx = 'LinkName'.$x;
			if ($this->DataChanged(array($urlidx, $descridx))) {
				$id = $this->PostedDigit($idodx);
				$url = $this->PostedString($urlidx);
				$descr = $this->PostedString($descridx);
				if ($id) {
					// Update & delete
					$db->UpdateProjectLinks($id, $descr, $url);
				} else if ($url && $descr) {
					// Add
					$db->CreateProjectLinks($prjid, $descr, $url);
				}
			}
		}
	} else {
		$prjid = $this->GetNumber('id');
	}

	$this->PutData ('PageHeading', array('projectlinks'));
	$this->PutData ('PrjID', $prjid);
	$this->LoadView('home');
}
}
?>