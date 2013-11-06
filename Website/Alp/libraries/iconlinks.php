<?php
function MakeIconLink($icon, $link, $hint, $onclick='')
{
	echo "<a href=\"$link\" onclick=\"$onclick\"><img src=\"/image/$icon\" alt=\"$hint\" title=\"$hint\" class=\"icons\" border=\"0\"></a>";
}

function MakeJSIcon($icon, $js, $hint)
{
	echo "<img src=\"/image/$icon\" alt=\"$hint\" title=\"$hint\" class=\"icons\" border=\"0\" onClick=\"$js\">";
}
?>