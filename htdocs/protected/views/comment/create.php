<?php
if( strlen($task->name) >= 15 ) {
	$name = substr($task->name, 0, 12 ).'...';
} else {
	$name = $task->name;
}
$this->breadcrumbs=array(
	$task->project->name=>array('project/view', 'id'=>$task->projectid),
	$name=>array('task/view', 'id'=>$task->taskid),
	'Comment',
);
?>

<h1>Create Task</h1>

<?php echo $this->renderPartial('_form', array(
	'model'=>$model,
)); ?>
