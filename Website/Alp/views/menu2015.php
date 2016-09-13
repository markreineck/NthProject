<?php


function ShowMenu($alp, $menuname, $menuid, $menuitems)
{
	$db = $alp->Model();
	
	
	echo "<li><a id='".$menuid."' class='link'>$menuname  <div id='' class='arrow-left'></div></a>";

	echo "<div class='sub-menu'><ul class='child-menu' style='display:none' id='SubMenu_".$menuid."'>";
	ShowSubMenu($alp, $menuitems);
	echo '</ul></div>';

	echo "</li>";
	
}	
	
function ShowSubMenu($alp, $menuitems)
{
	$db = $alp->Model();	
	
	
	if($menuitems=='Time'){	
		$itemarray = array(
			array('priv'=>'',	'opt'=>'',				'link'=>'signin',			'name'=>'Project Sign In'),
			array('priv'=>'',	'opt'=>'',				'link'=>'signout',			'name'=>'Signout'),
			array('priv'=>'G',	'opt'=>'',				'link'=>'recordtime',		'name'=>'Record Time'),
			array('priv'=>'',	'opt'=>'',				'link'=>'mytime',			'name'=>'My Time'),
			array('priv'=>'',	'opt'=>'',				'link'=>'signedin',			'name'=>'Signed-in Staff')
		);
	}
	
	if($menuitems=='Financial'){
		$itemarray = array(
			array('priv'=>'G',	'opt'=>'Time',			'link'=>'timedetail',		'name'=>'Time Detail'),
			array('priv'=>'G',	'opt'=>'Time',			'link'=>'payroll',			'name'=>'Payroll'),
			array('priv'=>'G',	'opt'=>'Time',			'link'=>'timeexport',		'name'=>'Time Log'),
			array('priv'=>'G',	'opt'=>'TaskCost',		'link'=>'paytasks',			'name'=>'Pay For Tasks'),
			array('priv'=>'G',	'opt'=>'TaskCost',		'link'=>'payhistory',		'name'=>'Payment History'),
			array('priv'=>'',	'opt'=>'TaskCost',		'link'=>'mypayhistory',		'name'=>'My Payment History'),
			array('priv'=>'G',	'opt'=>'TaskCost',		'link'=>'billtask',			'name'=>'Bill Tasks'),
			array('priv'=>'G',	'opt'=>'TaskCost',		'link'=>'billhistory',		'name'=>'Billing History'),
			array('priv'=>'G',	'opt'=>'',				'link'=>'projectcost',		'name'=>'Project Costs')
		);
	}
	
	if($menuitems=='Tasks'){
	
		$itemarray = array(
			array('priv'=>'',	'opt'=>'',				'link'=>'taskbypriority',	'name'=>'Active Tasks'),
			array('priv'=>'',	'opt'=>'TaskCost',		'link'=>'taskcosts',		'name'=>'Fixed Price Tasks'),
			array('priv'=>'',	'opt'=>'Milestones',	'link'=>'taskbymilestone',	'name'=>'Tasks by Milestone'),
			array('priv'=>'',	'opt'=>'',				'link'=>'tasksremoved',		'name'=>'Cancelled Tasks')
		);
	}
/*
	if($menuitems=='Completed Tasks'){
	
		$itemarray = array(
			array('priv'=>'',	'opt'=>'',				'link'=>'taskcompleted',	'name'=>'Completed'),
			array('priv'=>'',	'opt'=>'',				'link'=>'taskrecentdone',	'name'=>'Recently Completed '),
			array('priv'=>'',	'opt'=>'ApproveTasks',	'link'=>'taskapproved',		'name'=>'Approved'),
			array('priv'=>'',	'opt'=>'ApproveTasks',	'link'=>'taskrecentappr',	'name'=>'Recently Approved '),
			array('priv'=>'',	'opt'=>'ReleaseTasks',	'link'=>'releasetasks',		'name'=>'Release Tasks'),
			array('priv'=>'',	'opt'=>'ReleaseTasks',	'link'=>'taskrecentrel',	'name'=>'Recently Released ')
		);
	}
*/
	if($menuitems=='Projects'){
	
		$itemarray = array(
			array('priv'=>'',	'opt'=>'',				'link'=>'projects',			'name'=>'Projects'),
			array('priv'=>'',	'opt'=>'',				'link'=>'projectareas',		'name'=>'Project Areas'),
			array('priv'=>'',	'opt'=>'Milestones',	'link'=>'milestones',		'name'=>'Milestones'),
			array('priv'=>'S',	'opt'=>'',				'link'=>'projectdefaults',	'name'=>'Project Defaults'),
			array('priv'=>'S',	'opt'=>'',				'link'=>'projectadd',		'name'=>'Add Project')
		);
	}
	
	if($menuitems=='Supervisor'){
	
		$itemarray = array(
			array('priv'=>'G',	'opt'=>'Organizations',	'link'=>'companies',		'name'=>'Organizations'),
			array('priv'=>'U',	'opt'=>'',				'link'=>'users',			'name'=>'Users'),
			array('priv'=>'U',	'opt'=>'',				'link'=>'useradd',			'name'=>'Create User'),
			array('priv'=>'G',	'opt'=>'Organizations',	'link'=>'orgtypes ',		'name'=>'Organizaton Types'),
			array('priv'=>'G',	'opt'=>'',				'link'=>'userstatuses ',	'name'=>'User Status'),
			array('priv'=>'G',	'opt'=>'',				'link'=>'taskstatuses ',	'name'=>'Task Status'),
			array('priv'=>'G',	'opt'=>'',				'link'=>'purgetime ',		'name'=>'Purge Time'),
			array('priv'=>'G',	'opt'=>'',				'link'=>'purgetask ',		'name'=>'Purge Task')
			
		/*
		,
			array('priv'=>'G',	'link'=>'settings',			'name'=>'Settings')
		*/
		);
	
	}

	foreach ($itemarray as $menu) {
		if ($menu['priv'] == 'G')
			$ok = $db->IsGlobalSupervisor();
		else if ($menu['priv'] == 'S')
			$ok = $db->IsSupervisor();
		else if ($menu['priv'] == 'U')
			$ok = $db->IsUserManager();
		else
			$ok = true;

		if ($ok && $menu['opt']) {
			if (!$alp->UserSetting($menu['opt']))
				$ok = false;
		}

		if ($ok)
			echo '<li><a href="'.$menu['link'].'">'.$menu['name'].'</a></li>';
	}
}
?>
<div class="menu">
    <ul class="parent-menu">
	<li><a href="home" class='link'>Messages</a></li>
<?php
if ($this->UserSetting('Time') && $db->IsOwnerOrg())
	ShowMenu($this, 'Time', 'Time', 'Time');

if ($db->IsGlobalSupervisor()) {
	if ($this->UserSetting('Time') || $this->UserSetting('TaskCost'))
		ShowMenu($this, 'Financial', 'Financial', 'Financial');
}

ShowMenu($this, 'Tasks', 'Tasks', 'Tasks');
//ShowMenu($this, 'Completed Tasks', 'CompleteTask', 'Completed Tasks');

if ($db->IsProjectSupervisor())
	ShowMenu($this, 'Projects', 'Projects', 'Projects');

if ($db->IsGlobalSupervisor() || $db->IsUserManager())
	ShowMenu($this, 'Supervisor', 'Supervisor', 'Supervisor');
?>
	<li><a href="newtask" class='link'>Add a Task</a></li>
	</ul>
    <br clear="all" />

</div><br clear="all" />
<div class="StaticSubMenu">
    <ul>
<?php
if (isset($MenuID)) {
	ShowSubMenu($this, $MenuID);
}
?>
    </ul>
</div>
