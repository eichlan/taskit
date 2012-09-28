<script type="text/javascript">
function quickCreate()
{
	var payload = 'projectid=' + $('#project').val() + 
		'&name='+encodeURI($('#taskname').val()) +
		'&typeid=' + $('#tasktype').val() +
		'&statusid=' + $('#taskstatus').val();
	$.ajax({
		type: 'POST',
		url: '<?php echo Yii::app()->createUrl('task/quickCreate'); ?>',
		data: payload,
		dataType: 'json',
		success: function(data,a,b){
			if( data.result == 'ok' )
			{
				if( $('#editnowcb:checked').length == 1 )
				{
					window.location = data.url;
				}
				else
				{
					$('#taskname').val('');
					$('#last-created-task').html('Created Task: <a href="' +
						data.url +'">' + data.name + '</a>');
				}
			}
			else
			{
				if( typeof(data.error) == 'string' )
					alert( 'error: ' + data.error );
				else
				{
					var err = '';
					for( var i in data.error )
					{
						err += '  ' + data.error[i] + '\n';
					}
					alert( 'Errors:\n' + err );
				}
			}
		}
	});
}
statuses = {<?php
$first = true;
foreach( $teams as $t )
{
	if( $first )
		$first = false;
	else
		echo ', ';
	echo $t->teamid.': \'';
	foreach( $t->teamTaskStatuses as $s )
	{
		echo '<option value="'.$s->statusid.'">'.
			str_replace("'", "\\'", $s->name).'</option>';
	}
	echo "'";
}
?>};

types = {<?php
$first = true;
foreach( $teams as $t )
{
	if( $first )
		$first = false;
	else
		echo ', ';
	echo $t->teamid.': \'';
	foreach( $t->teamTaskTypes as $ty )
	{
		echo '<option value="'.$ty->typeid.'">'.
			str_replace("'", "\\'", $ty->name).'</option>';
	}
	echo "'";
}
?>};

projs = {<?php
$first = true;
foreach( $teams as $t )
{
	if( $first )
		$first = false;
	else
		echo ', ';
	echo $t->teamid.': \'';
	foreach( $t->getProjectTree() as $k => $n )
	{
		echo '<option value="'.$k.'">'.
			str_replace("'", "\\'", $n).'</option>';
	}
	echo "'";
}
?>};

function pickTeam()
{
	$('#taskstatus').html( statuses[$('#quickTask_team').val()] );
	$('#tasktype').html( types[$('#quickTask_team').val()] );
	$('#project').html( projs[$('#quickTask_team').val()] );
}
</script>
<form action="javascript:void(0)" onsubmit="quickCreate();">
<select id="quickTask_team" name="quickTask_team" onchange="pickTeam()"><?php
	foreach( $teams as $t )
	{
		echo '<option value="'.$t->teamid.'">'.$t->name.'</option>';
	}
?></select>
<select id="project" name="status"></select>
<br />
<select id="taskstatus" name="status"></select>
<select id="tasktype" name="type"></select>
<input id="taskname" type="text" name="name" />
<input type="submit" value="Create Task" />
<input type="checkbox" id="editnowcb" <?php
	if( $this->editNowDef ) echo 'checked="checked" ';
	if( !$this->editNowShow ) echo 'style="display: none;" ';
?>/><?php if( $this->editNowShow )
	echo '<label for="editnowcb">&nbsp;Edit&nbsp;now</label>';
?>
</form>
<script type="text/javascript">pickTeam();</script>
<div id="last-created-task"></div>
