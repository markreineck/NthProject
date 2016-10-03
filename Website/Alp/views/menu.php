This appears to be unused
<?php
$timemenu = array(
	array('priv'=>'',	'opt'=>'',				'link'=>'signin',			'name'=>'Project Sign In'),
	array('priv'=>'',	'opt'=>'',				'link'=>'signout',			'name'=>'Signout'),
	array('priv'=>'G',	'opt'=>'',				'link'=>'recordtime',		'name'=>'Record Time'),
	array('priv'=>'',	'opt'=>'',				'link'=>'mytime',			'name'=>'My Time'),
	array('priv'=>'',	'opt'=>'',				'link'=>'signedin',			'name'=>'Signed-in Staff')
);

$timerptmenu = array(
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

$taskmenu = array(
	array('priv'=>'',	'opt'=>'',				'link'=>'taskbypriority',	'name'=>'Active Tasks'),
	array('priv'=>'',	'opt'=>'TaskCost',		'link'=>'taskcosts',		'name'=>'Fixed Price Tasks'),
	array('priv'=>'',	'opt'=>'Milestones',	'link'=>'taskbymilestone',	'name'=>'Tasks by Milestone'),
	array('priv'=>'',	'opt'=>'',				'link'=>'tasksremoved',		'name'=>'Deleted Tasks')
);
/*
$taskrptmenu = array(
	array('priv'=>'',	'opt'=>'',				'link'=>'taskcompleted',	'name'=>'Completed'),
	array('priv'=>'',	'opt'=>'',				'link'=>'taskrecentdone',	'name'=>'Recently Completed '),
	array('priv'=>'',	'opt'=>'ApproveTasks',	'link'=>'taskapproved',		'name'=>'Approved'),
	array('priv'=>'',	'opt'=>'ApproveTasks',	'link'=>'taskrecentappr',	'name'=>'Recently Approved '),
	array('priv'=>'',	'opt'=>'ReleaseTasks',	'link'=>'releasetasks',		'name'=>'Release Tasks'),
	array('priv'=>'',	'opt'=>'ReleaseTasks',	'link'=>'taskrecentrel',	'name'=>'Recently Released ')
);
*/
$projectmenu = array(
	array('priv'=>'',	'opt'=>'',				'link'=>'projects',			'name'=>'Projects'),
	array('priv'=>'',	'opt'=>'',				'link'=>'projectareas',		'name'=>'Project Areas'),
	array('priv'=>'',	'opt'=>'Milestones',	'link'=>'milestones',		'name'=>'Milestones'),
	array('priv'=>'S',	'opt'=>'',				'link'=>'projectdefaults',	'name'=>'Project Defaults'),
	array('priv'=>'S',	'opt'=>'',				'link'=>'projectadd',		'name'=>'Add Project')
);

$supermenu = array(
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

function ShowMenu($alp, $menuname, $timemenu)
{
	$db = $alp->Model();

	echo "
<li><a>$menuname</a><ul>";

	foreach ($timemenu as $menu) {
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
	echo '</ul></li>
';
}
?>
<div class="">
	<ul id="verticalmenu" class="MenuList">
	<li><a href="home">Messages</a></li>
<?php
if ($this->UserSetting('Time') && $db->IsOwnerOrg())
	ShowMenu($this, 'Time', $timemenu);

if ($db->IsGlobalSupervisor()) {
	if ($this->UserSetting('Time') || $this->UserSetting('TaskCost'))
		ShowMenu($this, 'Financial', $timerptmenu);
}

ShowMenu($this, 'Tasks', $taskmenu);
//ShowMenu($this, 'Completed Tasks', $taskrptmenu);

if ($db->IsProjectSupervisor())
	ShowMenu($this, 'Projects', $projectmenu);

if ($db->IsGlobalSupervisor() || $db->IsUserManager())
	ShowMenu($this, 'Supervisor', $supermenu);
?>
	<li><a href="newtask">Add a Task</a></li>
	</ul>
</div>
