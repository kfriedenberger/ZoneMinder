<?php
	if ( !canView( 'Events' ) )
	{
		$view = "error";
		return;
	}
	$result = mysql_query( "select E.*,M.Name as MonitorName, M.Palette from Events as E, Monitors as M where E.Id = '$eid' and E.MonitorId = M.Id" );
	if ( !$result )
		die( mysql_error() );
	$event = mysql_fetch_assoc( $result );

	if ( !isset( $scale ) )
		$scale = 1;
	if ( !isset( $rate ) )
		$rate = 1;

	ob_start();
?>
<html>
<head>
<title>ZM - Video - <?= $event[Name] ?></title>
<link rel="stylesheet" href="zm_styles.css" type="text/css">
</head>
<body>
<form name="video_form" method="get" action="<?= $PHP_SELF ?>">
<input type="hidden" name="view" value="<?= $view ?>">
<input type="hidden" name="action" value="<?= $action ?>">
<input type="hidden" name="eid" value="<?= $eid ?>">
<input type="hidden" name="generate" value="1">
<table align="center" border="0" cellspacing="0" cellpadding="2" width="250">
<tr><td colspan="2" class="head" align="center">Video Generation Parameters</td></tr>
<tr><td colspan="2">&nbsp;</td></tr>
<tr><td class="text" align="right">Frame Rate</td><td><?= buildSelect( "rate", $rates ) ?></td></tr>
<tr><td class="text" align="right">Video Size</td><td><?= buildSelect( "scale", $scales ) ?></td></tr>
<tr><td class="text" align="right">Overwite Existing</td><td><input type="checkbox" class="form-noborder" name="overwrite" value="1"<?php if ( $overwrite ) { ?> checked<?php } ?>></td></tr>
<tr><td colspan="2">&nbsp;</td></tr>
<tr><td colspan="2" align="center"><input type="submit" class="form" value="Generate Video"></td></tr>
</table>
</form>
<?php
	if ( $generate )
	{
?>
<table border="0" cellspacing="0" cellpadding="4" width="100%">
<tr><td>&nbsp;</td></tr>
<tr><td>&nbsp;</td></tr>
<tr>
<td align="center" class="head">Generating Video</td>
</tr>
<tr><td>&nbsp;</td></tr>
<tr><td>&nbsp;</td></tr>
</table>
</body>
</html>
<?php
		$buffer_string = "<!-- This is some long buffer text to ensure that IE flushes correctly -->";
		for ( $i = 0; $i < 4096/strlen($buffer_string); $i++ )
		{
			echo $buffer_string."\n";
		}
?>
<?php
		ob_end_flush();
		if ( $video_file = createVideo( $event, $rate, $scale, $overwrite ) )
		{
			$event_dir = ZM_DIR_EVENTS."/$event[MonitorName]/".sprintf( "%d", $eid );
			$video_path = $event_dir.'/'.$video_file;
			//header("Location: $video_path" );
?>
<html>
<head>
<script language="JavaScript">
location.replace('<?= $video_path ?>');
</script>
</head>
</html>
<?php
		}
		else
		{
			ob_end_flush();
?>
<html>
<head>
<link rel="stylesheet" href="zm_styles.css" type="text/css">
</head>
<body>
<p class="head" align="center"><font color="red"><br><br>Video Generation Failed!<br><br></font></p>
<?php
		}
	}
	else
	{
		ob_end_flush();
	}
?>
</body>
</html>