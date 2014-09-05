var htmlsection;

function GetXmlHttpObject(){
	if (window.XMLHttpRequest) {
		// code for IE7+, Firefox, Chrome, Opera, Safari
		return new XMLHttpRequest();
	}
	if (window.ActiveXObject){
		// code for IE6, IE5
		return new ActiveXObject("Microsoft.XMLHTTP");
	}	  
		return null;
}

function showLoader(){
}

function hideLoader(){
};

function AjaxSink(){
	if (xmlhttp.readyState==4){	
		var response = xmlhttp.responseText;
		if (response) {
			document.getElementById(htmlsection).innerHTML = response;
		}
	}
}

function AppendArg (args, input)
{
	if (input.name.length > 0 && input.value.length > 0) {
		if (args.length > 0)
			args += '&';
		args += input.name + '=' + input.value;
	}
	return args;
}

function FindArgs (args, form, type)
{
	inputs = form.getElementsByTagName(type);
	for (x = 0; x < inputs.length; ++x) {
		args = AppendArg (args, inputs[x]);
	}
	return args;
}

function DoAjaxFromForm(form, func, sect, args, async, debugdiv)
{
	if (form.nodeName != "FORM")
		form = form.form;

	args = FindArgs (args, form, 'input');
	args = FindArgs (args, form, 'textarea');
	args = FindArgs (args, form, 'select');
/*
	inputs = form.getElementsByTagName('input');
	for (x = 0; x < inputs.length; ++x) {
		args = AppendArg (args, inputs[x]);
	}
	inputs = form.getElementsByTagName('textarea');
	for (x = 0; x < inputs.length; ++x) {
		args = AppendArg (args, inputs[x]);
	}
	inputs = form.getElementsByTagName('select');
	for (x = 0; x < inputs.length; ++x) {
		args = AppendArg (args, inputs[x]);
	}
*/
	DoAjaxFill(func, sect, args, async, debugdiv);
}

function DoAjaxFromFields(func, sect, args, fields, async, debugdiv)
{
	for (var x=0; x<fields.length; x++) {
		r = document.getElementById(fields[x]);
		if (r) {
			if (args.length > 0)
				args = args + "&";
			args = args + fields[x] + "=" + r.value;
		}
	}
	DoAjaxFill(func, sect, args, async, debugdiv);
}

function DoAjaxFill(func, sect, args, async, debugdiv)
{
	showLoader();
	htmlsection = sect
	var url = ajaxurl + func;
	if (args.length > 0)
		url = url + "?" + args;
	if (debugdiv != '') {
		r = document.getElementById(debugdiv);
		if (r) {
			r.innerHTML += "<br>" + url;
		}
	}

	xmlhttp=GetXmlHttpObject();
	if (xmlhttp==null){
		alert ("Browser does not support HTTP Request");
		return;
	}
	xmlhttp.onreadystatechange=AjaxSink;
	xmlhttp.open("GET",url,async);
	xmlhttp.send(null);
}