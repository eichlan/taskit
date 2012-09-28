<?php

require_once(Yii::getPathOfAlias('application.taskit.DashboardWidget').'.php');

class ProjectList extends DashboardWidget
{
	public function runDisplay()
	{
		$dataProvider = new CActiveDataProvider('Project', array(
			'criteria' => array(
				'together'=> true,
				'with' => array('team.users'),
				'condition' => '"users_users".userid=:userid',
				'params' => array(
					'userid' => Yii::app()->user->id,
				),
//				'order' => '"t".priority DESC, "t".created ASC',
			)
		));

		$this->render('projectList', array(
			'dataProvider' => $dataProvider,
		));
	}
}

