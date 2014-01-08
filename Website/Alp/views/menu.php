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
	array('priv'=>'G',	'opt'=>'TaskCost',		'link'=>'billhistory',		'name'=>'Billing History')
);

$taskmenu = array(
	array('priv'=>'',	'opt'=>'',				'link'=>'taskbyproject',	'name'=>'By Project'),
	array('priv'=>'',	'opt'=>'',				'link'=>'taskbypriority',	'name'=>'By Priority'),
	array('priv'=>'',	'opt'=>'',				'link'=>'taskbytarget',		'name'=>'By Due Date'),
	array('priv'=>'',	'opt'=>'',				'link'=>'taskbyperson',		'name'=>'By Person'),
	array('priv'=>'',	'opt'=>'',				'link'=>'taskbycreated',	'name'=>'New Tasks')
);

$alltaskmenu = array(
	array('priv'=>'',	'opt'=>'TaskCost',		'link'=>'taskcosts',		'name'=>'Fixed Price Tasks'),
	array('priv'=>'',	'opt'=>'Milestones',	'link'=>'taskbymilestone',	'name'=>'Tasks by Milestone'),
	array('priv'=>'',	'opt'=>'',				'link'=>'tasksremoved',		'name'=>'Deleted Tasks'),
	array('priv'=>'',	'opt'=>'',				'link'=>'newtask',			'name'=>'Add a Task')
);

$taskrptmenu = array(
	array('priv'=>'',	'opt'=>'',				'link'=>'taskcompleted',	'name'=>'Completed'),
	array('priv'=>'',	'opt'=>'',				'link'=>'taskrecentdone',	'name'=>'Recently Completed '),
	array('priv'=>'',	'opt'=>'ApproveTasks',	'link'=>'taskapproved',		'name'=>'Approved'),
	array('priv'=>'',	'opt'=>'ApproveTasks',	'link'=>'taskrecentappr',	'name'=>'Recently Approved '),
	array('priv'=>'',	'opt'=>'ReleaseTasks',	'link'=>'releasetasks',		'name'=>'Release Tasks'),
	array('priv'=>'',	'opt'=>'ReleaseTasks',	'link'=>'taskrecentrel',	'name'=>'Recently Released ')
);

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
	array('priv'=>'G',	'opt'=>'Organizations',	'link'=>'orgtypes ',	'name'=>'Organizaton Types'),
	array('priv'=>'G',	'opt'=>'',				'link'=>'userstatuses ',	'name'=>'User Status'),
	array('priv'=>'G',	'opt'=>'',				'link'=>'taskstatuses ',	'name'=>'Task Status')
/*
,
	array('priv'=>'G',	'link'=>'settings',			'name'=>'Settings')
*/
);

function ShowMenu($alp, $menuname, $timemenu)
{
	$db = $alp->Database();

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
if ($this->UserSetting('Time'))
	ShowMenu($this, 'Time', $timemenu);

if ($db->IsSupervisor()) {
	if ($this->UserSetting('Time') || $this->UserSetting('TaskCost'))
		ShowMenu($this, 'Financial', $timerptmenu);
}

ShowMenu($this, 'Active Tasks', $taskmenu);
ShowMenu($this, 'Completed Tasks', $taskrptmenu);
ShowMenu($this, 'All Tasks', $alltaskmenu);

if ($db->IsProjectSupervisor())
	ShowMenu($this, 'Projects', $projectmenu);

if ($db->IsSupervisor())
	ShowMenu($this, 'Supervisor', $supermenu);
?>
	</ul>
</div>
