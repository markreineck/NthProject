<?php
class OrgDB extends DatabaseDB {

function OrgDB($framework)
{
	$this->DatabaseDB($framework);
}

/**********************************************************************
 *	Query Functions
 **********************************************************************/
function GetCompanyList()
{
	$sql = "select o.orgid, o.name, s.name status, ifnull(u.cnt,0) ucnt, ifnull(p.cnt,0) pcnt
from organizations o
left outer join (select orgid, count(orgid) cnt from projects group by orgid) p on o.orgid=p.orgid
left outer join (select orgid, count(orgid) cnt from users group by orgid) u on o.orgid=u.orgid
left outer join orgstatus s on o.status=s.statusid
group by o.orgid order by o.name";
	return $this->SelectAll($sql);
}

function GetOrgTypeList()
{
	$sql = 'select statusid, name from orgstatus order by name';
	return $this->SelectAll($sql, 2);
}

function ReadOrg($orgid)
{
	$sql = 'select status, name from organizations where orgid='.$orgid;
	return $this->SelectRow($sql);
}

/**********************************************************************
 *	Update Functions
 **********************************************************************
function CreateCompany($status, $name)
{
	$sid = $this->GetSessionID();
	$name = $this->MakeStringValue($name);
	$status = $this->MakeNumericValue($status);
	$sql = "call CreateCompany($sid, $status, $name)";
	return $this->ExecuteProc ($sql);
}

function UpdateOrganization($orgid, $status, $name)
{
	$sid = $this->GetSessionID();
	$name = $this->MakeStringValue($name);
	$status = $this->MakeNumericValue($status);
	$sql = "call UpdateOrganization($sid, $orgid, $status, $name)";
	return $this->ExecuteProc ($sql);
}

function UpdateOrgField($orgid, $fld, $val)
{
	$sid = $this->GetSessionID();
	$val = $this->MakeStringValue($val);
	$sql = "call UpdateOrgField($sid, $orgid, $fld, $val)";
	return $this->ExecuteProc ($sql);
}

function DeleteOrganization($orgid)
{
	$sid = $this->GetSessionID();
	$sql = "call DeleteOrganization($sid, $orgid)";
	return $this->ExecuteProc ($sql);
}
*/
}
?>