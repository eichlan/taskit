<?php

require_once(Yii::getPathOfAlias('application.taskit.DashboardWidget').'.php');

class TeamList extends DashboardWidget
{
	public function runDisplay()
	{
		$dataProvider = new CActiveDataProvider('Team', array(
			'criteria' => array(
				'together'=> true,
				'with' => array('users'),
				'condition' => '"users".userid=:userid',
				'params' => array(
					'userid' => Yii::app()->user->id,
				),
//				'order' => '"t".priority DESC, "t".created ASC',
			)
		));

		$this->render('teamList', array(
			'dataProvider' => $dataProvider,
		));
	}
}

