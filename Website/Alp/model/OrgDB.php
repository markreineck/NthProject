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
left outer join (select orgid, count(orgid) cnt from projects where completed is null and status!='I' group by orgid) p on o.orgid=p.orgid
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

}
?>