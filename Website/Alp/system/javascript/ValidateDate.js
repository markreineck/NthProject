// JavaScript Document

function IsDate(value)
{
	try {
		var bits = value.split('-');
		if (bits.length < 3)
			var bits = value.split('/');
		if (bits.length < 3)
			return false
		m1 = bits[0] - 1;
		d1 = bits[1];
		y1 = bits[2];
		var dt = new Date(y1,m1,d1,0,0,0,0);
		d2 = dt.getDate();
		m2 = dt.getMonth();
		y2 = dt.getFullYear();
		if (y1 != y2)
			return false;
		if (m1 != m2)
			return false;
		if (d1 != d2)
			return false;
		return true;
	} catch (e) {
		return false;
	}
}