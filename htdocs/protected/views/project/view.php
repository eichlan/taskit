<?php
$this->breadcrumbs=Utility::projectMenu( $project, true );
?>
<h1><?php echo $project->name; ?></h1>

<script type="text/javascript">
function quickCreate()
{
	var payload = 'projectid=<?php echo $project->projectid; 
		?>&name='+encodeURI($('#taskname').val()) +
		'&typeid=' + $('#tasktype').val() +
		'&statusid=' + $('#taskstatus').val();
	$.ajax({
		type: 'POST',
		url: '<?php echo Yii::app()->createUrl('task/quickCreate'); ?>',
		data: payload,
		dataType: 'text',
		success: function(data,a,b){
			window.location = '<?php echo Yii::app()->createUrl('task/view',
				array('id'=>'XXX')); ?>'.replace('XXX', data );
		}
	});
}
</script>
<form action="javascript:void(0)" onsubmit="quickCreate();">
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
</form>
<a href="<?php echo Yii::app()->createUrl('project/code', array(
	'id' => $project->projectid,
)); ?>">View Code Units</a>
<?php
global $priorities;
$priorities = $project->team->getPriorities();
function getPriorityName( $priority )
{
	global $priorities;
	return $priorities[$priority]->name;
}
$this->widget('zii.widgets.grid.CGridView', array(
	'dataProvider' => $tasks,
	'columns' => array(
		array(
			'name'=>'priority',
			'value'=>'getPriorityName($data->priority)',
		),
		array(
			'name'=>'typeid',
			'value'=>'$data->type->name',
		),
		array(
			'name'=>'statusid',
			'value'=>'$data->status->name',
		),
		array(
			'class'=>'CLinkColumn',
			'header'=>'Name',
			'labelExpression'=>'$data->name',
			'urlExpression'=>'Yii::app()->createUrl("task/view", array("id"=>$data->taskid))',
		),
		array(
			'name' => 'created',
			'value' => 'Utility::timeAgo( $data->created )',
		),
	),
)); ?>
