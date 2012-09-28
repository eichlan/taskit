<?php //$this->beginContent('frame');
$columns = array();
foreach( $this->cols as $c )
{
	switch( $c )
	{
	case 'project':
		$columns[] = array(
			'class'=>'CLinkColumn',
			'header'=>'Project',
			'labelExpression'=>'htmlspecialchars($data->project->name)',
			'urlExpression'=>'Yii::app()->createUrl("project/view",array("id"=>$data->projectid))',
		);
		break;

	case 'name':
		$columns[] = array(
			'class'=>'CLinkColumn',
			'header'=>'Task',
			'labelExpression'=>'htmlspecialchars($data->name)',
			'urlExpression'=>'Yii::app()->createUrl("task/view",array("id"=>$data->taskid))',
		);
		break;

	case 'cplx':
		$columns[] = array(
			'name'=>'complexity',
			'header'=>'C',
		);
		break;

	case 'prio':
		$columns[] = array(
			'header'=>'Prio',
			'value'=>'$data->priorityName->name',
		);
		break;

	case 'team':
		$columns[] = array(
			'class'=>'CLinkColumn',
			'header'=>'Team',
			'labelExpression'=>'htmlspecialchars($data->team->name)',
			'urlExpression'=>'Yii::app()->createUrl("team/view",array("id"=>$data->teamid))',
		);
		break;

	case 'author':
		$columns[] = array(
			'header'=>'Author',
			'value'=>'$data->author->dispName()',
			'type'=>'raw',
		);
		break;

	case 'assign':
		$columns[] = array(
			'header'=>'Assigned',
			'value'=>'Utility::userList($data->assignedTo)',
			'type'=>'raw',
		);
		break;

	case 'status':
		$columns[] = array(
			'name'=>'statusid',
			'value'=>'$data->status->name',
		);
		break;

	case 'type':
		$columns[] = array(
			'name'=>'typeid',
			'value'=>'$data->type->name',
		);
		break;

	case 'created':
		$columns[] = array(
			'name'=>'created',
			'value'=>'Utility::timeAgo($data->created)',
		);
		break;

	default:
		print '<p>' . $c . '</p>';
	}
}


$this->widget('zii.widgets.grid.CGridView', array(
	'dataProvider' => $dataProvider,
	'columns' => $columns,
));

//$this->endContent(); ?>
