<?php
$this->breadcrumbs=array(
	'Project'=>array('/project'),
	'Create',
);?>
<h1><?php echo $this->id . '/' . $this->action->id; ?></h1>

<?php $this->renderPartial('_form', array(
	'model' => $model,
	'teams' => $teams,
	'projects' => $projects,
)); ?>
