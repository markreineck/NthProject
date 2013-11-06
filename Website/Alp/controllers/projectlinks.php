<?php
include 'ProjectController.php';

class projectlinks extends ProjectController implements AlpController {

public function __construct($url)
{
	parent::ProjectController($url);
}

function Start()
{
	if (isset($this->PostData['LinkCnt'])) {
		$db = $this->Database();
		$prjid = $this->PostData['ProjectID'];

		for ($x=$this->PostData['LinkCnt']; $x>0; $x--) {
			$idodx = 'LinkID'.$x;
			$urlidx = 'LinkURL'.$x;
			$descridx = 'LinkName'.$x;
			if ($this->DataChanged(array($urlidx, $descridx))) {
				$id = $this->PostData[$idodx];
				$url = $this->PostData[$urlidx];
				$descr = $this->PostData[$descridx];
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
		$prjid = $this->GetData['id'];
	}

	$this->PutData ('PageHeading', array('projectlinks'));
	$this->PutData ('PrjID', $prjid);
	$this->LoadView('home');
}
}
?>