<?php

require_once(Yii::getPathOfAlias('application.taskit.DashboardWidget').'.php');

class RecentComments extends DashboardWidget
{
	public $cols = array('Team', 'Project', 'Task', 'Author');
	public $maxComment = 50;
	public $itemsPerPage = 10;
	public function runDisplay()
	{
		$dataProvider = new CActiveDataProvider('TaskComment', array(
			'criteria' => array(
				'together'=> true,
				'with' => array('task','task.project', 'task.project.team.users'),
				'condition' => '"users_users".userid=:userid',
				'params' => array(
					'userid'=>Yii::app()->user->id,
				),
				'order' => '"t".created DESC',
			),
			'pagination' => array(
				'pageSize' => $this->itemsPerPage,
			),
		));

		$this->render('recentComments', array(
			'dataProvider' => $dataProvider,
		));
	}

	public function runEdit()
	{
		$this->render('recentComments-edit');
	}
}

