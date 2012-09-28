<?php

require_once(Yii::getPathOfAlias('application.taskit.DashboardWidget').'.php');

class QuickTask extends DashboardWidget
{
	public $projectid = null;
	public $editNowDef = true;
	public $editNowShow = true;

	public function runDisplay()
	{
		if( $this->projectid === null )
		{
			$teams = Team::model()->findAll(array(
				'together'=> true,
				'with' => array('users'),
				'condition' => '"users".userid=:userid',
				'params' => array(
					'userid' => Yii::app()->user->id,
				),
			));
			$this->render('quickTask', array(
				'teams' => $teams,
			));
		}
		else
		{
			$project = Project::model()->findByPk((int)$this->projectid);
			$this->render('quickTask-project', array(
				'project' => $project,
			));
		}
	}

	public function runEdit()
	{
		$this->render('quickTask-edit');
	}
}

