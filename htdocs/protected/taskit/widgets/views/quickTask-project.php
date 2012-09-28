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
					alert( 'Error: ' + data.error );
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
</script>
<form action="javascript:void(0)" onsubmit="quickCreate();">
<input id="project" name="projectid" type="hidden" value="<?php echo $this->projectid; ?>" />
<select id="taskstatus" name="status"><?php
	foreach( $project->team->teamTaskStatuses as $s )
	{
		echo '<option value="'.$s->statusid.'">'.$s->name.'</option>';
	}
?></select>
<select id="tasktype" name="type"><?php
	foreach( $project->team->teamTaskTypes as $t )
	{
		echo '<option value="'.$t->typeid.'">'.$t->name.'</option>';
	}
?></select>
<input id="taskname" type="text" name="name" />
<input type="submit" value="Create Task" />
<input type="checkbox" id="editnowcb" <?php
	if( $this->editNowDef ) echo 'checked="checked" ';
	if( !$this->editNowShow ) echo 'style="display: none;" ';
?>/><?php if( $this->editNowShow )
	echo '<label for="editnowcb">&nbsp;Edit&nbsp;now</label>';
?>
</form>
<div id="last-created-task"></div>
