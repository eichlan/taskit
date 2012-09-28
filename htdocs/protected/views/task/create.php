<?php
$this->breadcrumbs=array(
	$project->name=>array('project/view', 'id'=>$project->projectid),
	'Create Task',
);

$this->menu=array(
	array('label'=>'List Task', 'url'=>array('index')),
	array('label'=>'Manage Task', 'url'=>array('admin')),
);
?>

<h1>Create Task</h1>

<?php echo $this->renderPartial('_form', array(
	'model'=>$model,
	'project'=>$project,
)); ?>
