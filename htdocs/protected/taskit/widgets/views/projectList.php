<?php //$this->beginContent('frame');
$this->widget('zii.widgets.grid.CGridView', array(
	'dataProvider' => $dataProvider,
	'columns' => array(
		array(
			'header'=>'Parent',
			'value'=>'$data->parent!==NULL?$data->parent->name:"None"',
		),
		array(
			'class'=>'CLinkColumn',
			'header'=>'Project',
			'labelExpression'=>'$data->name',
			'urlExpression'=>'Yii::app()->createUrl("project/view",array("id"=>$data->projectid))',
		),
		'name'=>'description',
	),
));

//$this->endContent(); ?>
