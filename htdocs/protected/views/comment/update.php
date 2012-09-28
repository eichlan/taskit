<?php
$this->breadcrumbs=array(
	'Tasks'=>array('index'),
	'Comment'=>array('view','id'=>$model->taskid),
	'Update',
);

$this->menu=array(
	array('label'=>'List Task', 'url'=>array('index')),
	array('label'=>'Create Task', 'url'=>array('create')),
	array('label'=>'View Task', 'url'=>array('view', 'id'=>$model->taskid)),
	array('label'=>'Manage Task', 'url'=>array('admin')),
);
?>

<h1>Update Task <?php echo $model->taskid; ?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>
