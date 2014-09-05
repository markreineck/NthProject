<?php
$db = $this->Model();

header('Content-Type: image/png');
$sql = "select contents from taskfiles where fileid=$FileID and taskid=$TaskID";
$sql = "select contents from taskfiles limit 1";
$imgdata = $db->Select($sql);
imagepng(imagecreatefromstring(base64_decode($imgdata)));
?> 