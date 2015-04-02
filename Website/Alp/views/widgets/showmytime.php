<?php
$db = $this->Model();
$c = $this->Cookie();
//$form = $this->Forms();
$this->LoadLibrary('timeicons');
?>
<h1>Hours for <?php echo "$StartDate - $EndDate"; ?></h1>
<table class="table table-striped">
    <tr class="ColumnTitles">
        <th>Project</th>
        <th>Date</th>
        <th>Time</th>
        <th align="center">Elapse Time</th>
        <th>Comment</th>
        <th></th>
    </tr>
<?php
$stripe = 1;
$totlapsetime = 0;
$totadjustment = 0;
$totadjustedtime = 0;

foreach ($data as $vals) {
    $elapsetime = $vals->elapsetime;
    $adjustment = $vals->adjustment;
    $adjustedtime = $elapsetime - $adjustment;
    $reason = $vals->reason;
?>
    <tr class="stripe<?php echo $stripe; ?>">
        <td><?php echo $vals->project; ?></td>
        <td><?php echo $vals->startdate; ?></td>
        <td>
            <?php
            echo $vals->starttime . ' - ';
            if (!empty($vals->endtime))
                echo $vals->endtime;

            ?>
        </td>
        <td align="center"><?php echo $adjustedtime;?></td>
        <td><?php echo $vals->comment;?></td>
        <?php
        if ($db->IsGlobalSupervisor()) {
            ?>
            <td width="122">
                <?php
                TimeProjectIcon($vals->timeid, $vals->name, $vals->startdate, $vals->elapsetime);
                ?>
            </td>
        <?php
        }
?>
    </tr>
<?php
    $totlapsetime += $elapsetime;
    $totadjustment += $adjustment;
    $totadjustedtime += $adjustedtime;
    $stripe = ($stripe > 1) ? 1 : 2;
}
?>
    <tr>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td align="right">Total Hours:</td>
        <td align="center"><?php echo $totadjustedtime; ?></td>
    </tr>
</table>

