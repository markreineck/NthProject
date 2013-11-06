/*
Copyright (c) 2012, 2013, Nth Generation. All rights reserved.

Licensed under the Apache License, Version 2.0 (the "License");
you may not use this file except in compliance with the License.
You may obtain a copy of the License at

http://www.apache.org/licenses/LICENSE-2.0

Unless required by applicable law or agreed to in writing, software
distributed under the License is distributed on an "AS IS" BASIS,
WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
See the License for the specific language governing permissions and
limitations under the License.
*/

function DoCapitalization(str)
{
	toreturn='';
	firstchar = str.charAt(0);
	therest = str.substr(1);
	firstcharupper = firstchar.toUpperCase();

	haslowers = false;
	hasuppers = false;
	for (ctr2=0; ctr2<str.length; ctr2++) {
		thischar=str.charAt(ctr2);
		if (isNaN(thischar) && thischar != " ") {
			if (thischar == thischar.toLowerCase())	
				haslowers = true;
			if (thischar == thischar.toUpperCase())	
				hasuppers = true;
		}
		
		if (haslowers && hasuppers)
			break;
	}
	
	
	if (haslowers && hasuppers)
		toreturn += str;
	else
		toreturn += firstcharupper + therest.toLowerCase();

	return toreturn;
}
