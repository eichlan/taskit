<?php
$this->breadcrumbs=array(
//	'Teams'=>array('index'),
	'Settings' => array('dashboard/settings'),
	'Create Team',
);

$this->menu=array(
	array('label'=>'List Team', 'url'=>array('index')),
	array('label'=>'Manage Team', 'url'=>array('admin')),
);
?>

<h1>Create Team</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>
