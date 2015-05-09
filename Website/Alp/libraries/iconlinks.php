<?php
function MakeIconLink($icon, $link, $hint, $onclick='', $color='')
{
	echo "<a href=\"$link\" onclick=\"$onclick\" class=\"ActionIconLinks $color\"><span class=\"glyphicon glyphicon-$icon\"></span></a>";
}

function MakeJSIcon($icon, $js, $hint, $size)
{
	//echo "<img src=\"/image/$icon\" alt=\"$hint\" title=\"$hint\" class=\"icons\" border=\"0\" onClick=\"$js\">";
	echo "<a href=\"#hint\" onclick=\"$js\" alt=\"$hint\" class=\"ActionIconLinks\"><span class=\"glyphicon glyphicon-$icon\"></span></a>";
}
?>