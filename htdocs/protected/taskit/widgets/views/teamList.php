<?php //$this->beginContent('frame');
$this->widget('zii.widgets.grid.CGridView', array(
	'dataProvider' => $dataProvider,
	'columns' => array(
		array(
			'class'=>'CLinkColumn',
			'header'=>'Team',
			'labelExpression'=>'$data->name',
			'urlExpression'=>'Yii::app()->createUrl("team/view",array("id"=>$data->teamid))',
		),
		'description',
	),
));

//$this->endContent(); ?>
