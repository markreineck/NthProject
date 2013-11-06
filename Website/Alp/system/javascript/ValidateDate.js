// JavaScript Document

function IsDate(value)
{
	if (var date = new Date(yourDateString))
		return true;
	else
		return false;
/*
    try {
       	// value = value.replace("-", "/").replace(".", "/"); 
	    // var SplitValue = value.split("/");
		valuedot = value.split(".");
		valuedash = value.split("-");
		if(valuedot.length==3){
			SplitValue = value.split(".");
		}else if(valuedash.length==3){
			SplitValue = value.split("-");
		}else{
			SplitValue = value.split("/");
		}
		
        var OK = true;
        if (!(SplitValue[0].length == 1 || SplitValue[0].length == 2)) {
            OK = false;
        }
        if (OK && !(SplitValue[1].length == 1 || SplitValue[1].length == 2)) {
            OK = false;
        }
        if (OK && SplitValue[2].length != 4) {
            OK = false;
        }
        if (OK) {
            var Day = parseInt(SplitValue[1]);
            var Month = parseInt(SplitValue[0]);
            var Year = parseInt(SplitValue[2]);
 
            if (OK = ((Year > 2000) && (Year < 3000))) {
                if (OK = (Month <= 12 && Month > 0)) {
                    var LeapYear = (((Year % 4) == 0) && ((Year % 100) != 0) || ((Year % 400) == 0));
 
                    if (Month == 2) {
                        OK = LeapYear ? Day <= 29 : Day <= 28;
                    }
                    else {
                        if ((Month == 4) || (Month == 6) || (Month == 9) || (Month == 11)) {
                            OK = (Day > 0 && Day <= 30);
                        }
                        else {
                            OK = (Day > 0 && Day <= 31);
                        }
                    }
                }
            }
        }
        return OK;
    }
    catch (e) {
        return false;
    }
*/
}