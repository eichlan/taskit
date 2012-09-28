<?php
$this->breadcrumbs=Utility::projectMenu( $project, false ) + array(
	'Code Units',
);
?>
<h1><?php echo $project->name; ?></h1>

To populate or update this listing <a href="<?php echo Yii::app()->createUrl('project/uploadTags', array('id'=> $project->projectid)); ?>">upload a tags file</a>.

<?php
$this->widget('zii.widgets.grid.CGridView', array(
	'dataProvider' => $code,
	'columns' => array(
		'type',
		'name',
		'parent',
		'file',
		'language',
		'complete',
	),
)); ?>
