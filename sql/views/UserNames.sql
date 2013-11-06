create or replace view usernames as 
select userid, orgid, initials, firstname, lastname, status, superuser, usermaint, email,
length(password) hasaccount,
concat(firstname,' ',lastname) name
from users;