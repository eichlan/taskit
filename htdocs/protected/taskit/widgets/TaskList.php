<?php

require_once(Yii::getPathOfAlias('application.taskit.DashboardWidget').'.php');

class TaskList extends DashboardWidget
{
	public $mode = 'assignedTo';
	public $open = true;
	public $user = null;
	public $projectid = null;
	public $cols = array('project','name','cplx','prio');
	public $itemsPerPage = 10;

	public function runDisplay()
	{
		$cond = array();
		$params = array();
		$with = array('status', 'assignedTo');

		if( $this->open === true || $this->open === false)
		{
			$cond[] = '"status".open=:open';
			$params['open'] = $this->open;
		}

		switch( $this->mode )
		{
		case 'assignedTo':
			$cond[] = '"assignedTo".userid=:userid';
			$params['userid'] = Yii::app()->user->id;
			break;

		case 'createdBy':
			$cond[] = '"t".authorid=:userid';
			$params['userid'] = Yii::app()->user->id;
			break;

		case 'unassigned':
			$cond[] = '"assignedTo".userid IS NULL';
			break;

		case 'all':
			break;
		}

		if( $this->projectid !== NULL )
		{
			$cond[] = '"t".projectid=:projectid';
			$params['projectid'] = $this->projectid;
		}
		else
		{
			$cond[] = '"teamUser".userid=:myuserid';
			$params['myuserid'] = Yii::app()->user->id;
			$with[] = 'teamUser';
		}

		$dataProvider = new CActiveDataProvider('Task', array(
			'criteria' => array(
				'together'=> true,
				'with' => $with,
				'condition' => implode(' AND ', $cond ),
				'params' => $params,
				'order' => '"t".priority DESC, "t".created ASC',
			),
			'pagination' => array(
				'pageSize' => $this->itemsPerPage,
			),
		));

		$this->render('taskList', array(
			'dataProvider' => $dataProvider,
		));
	}

	public function runEdit()
	{
		$this->render('taskList-edit');
	}
}

