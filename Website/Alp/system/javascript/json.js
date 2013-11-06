function ValidationRule (FieldName, Validation)
{
	this.FieldName = FieldName;
	this.Validation = Validation;
}

function TrimWhiteSpace (str)
{
	return str.replace(/^\s\s*/, '').replace(/\s\s*$/, '');
}

function AlpJson (ErrorFld, MsgFld) 
{
	this.ServerAddress = jsonurl;
	this.Async = jsonasync;
	this.ErrorMsgField = document.getElementById(ErrorFld);
	this.MsgField = document.getElementById(MsgFld);
	this.ValidationList = Array();
	this.SetValidation = function(FieldName, Validation) {
		ValidationList.push(new ValidationRule (FieldName, Validation));
	};

	this.ExtractJsonData = function()
	{
		if (xmlhttp.readyState==4) {
			var response = xmlhttp.responseText;
			if (response) {
				data = JSON.parse(TrimWhiteSpace(response));
				if (data.Result.ErrCode != 0) {
					ShowError(data.Result.ErrMsg);
				} else {
					return data.Result.Data;
				}
			}
		}
		return null;
	}

	this.GetXmlHttpObject = function()
	{
		if (window.XMLHttpRequest) {
			// code for IE7+, Firefox, Chrome, Opera, Safari
			return new XMLHttpRequest();
		}
		if (window.ActiveXObject) {
			// code for IE6, IE5
			return new ActiveXObject("Microsoft.XMLHTTP");
		}	  
		return null;
	}

	this.DoJson = function(service, handler, args)
	{
		xmlhttp = this.GetXmlHttpObject();
		if (xmlhttp == null){
			alert ("Browser does not support HTTP Request");
			return;
		}
		var url = this.ServerAddress + service;
		if (args !== "undefined")
			url += "&" + args;

		if (jsondbg) {
			r = document.getElementById("jsondebug");
			if (r) {
				r.innerHTML += "<br>" + url;
			}
		}

		xmlhttp.json = this;
		xmlhttp.onreadystatechange=handler;
		xmlhttp.open("GET",url,this.Async);
		xmlhttp.send(null);
	}

	this.FillList = function(listid, data, emptysel, sel)
	{
		list = document.getElementById(listid);
		if (list) {
			list.options.length=0;
			if (emptysel !== "undefined" && emptysel) {
				var opt = document.createElement('option');
				list.appendChild(opt);
			}
			if (data) {
				for (x=0; x<data.length; x++) {
					var opt = document.createElement('option');
					opt.innerHTML = data[x][1];
					opt.value = data[x][0];
					if (sel !== "undefined" && sel == data[x][0])
						opt.selected = true;
					list.appendChild(opt);
				}
			}
		}
	}

	this.FillListHandler = function(listid, emptysel)
	{
		data = this.ExtractJsonData();
		if (data) {
			this.FillList(listid, data, emptysel);
		}
	}

	this.FillHtml = function(tagid)
	{
		list = document.getElementById(tagid);
		data = this.ExtractJsonData();
		if (list && data) {
			list.innerHTML = data.tabledata;
		}
	}

	this.UpdateFields = function(data)
	{
		for (x=0; x<data.length; x++) {
			fld = document.getElementById(ErrorFld);
			if (fld) {
				val = data[x].value;
				if (fld.nodeName == 'input') {
					fld.value = val;
				} else if (fld.nodeName == 'select') {
				} else {
					this.MsgField.innerHTML = val;
				}
			}
		}
	}

	this.ExtractFormData = function()
	{
	}

	this.ShowError = function(msg)
	{
		if (this.ErrorMsgField)
			this.ErrorMsgField.innerHTML = msg;
	}

	this.ShowMessage = function()
	{
		if (this.MsgField)
			this.MsgField.innerHTML = msg;
	}

	this.ValidateForm = function()
	{
	}

	this.JSONUpdate = function()
	{
	}

	this.JSONQuery = function()
	{
	}

	this.JSONFillList = function(service, listid, args, emptysel)
	{
		handler = function()
		{
			this.json.FillListHandler(this.json.targetID, this.json.showEmptySel);
		}
		this.targetID = listid;
		this.showEmptySel = emptysel;
		this.DoJson(service, handler, args);
	}

	this.JSONFillHtml = function(service, divid, args)
	{
		handler = function()
		{
			this.json.FillHtml(this.json.targetID);
		}
		this.targetID = divid;
		this.DoJson(service, handler, args);
	}

	this.JSONFormSubmit = function()
	{
	}
}
