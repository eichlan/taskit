<?php

class DashboardController extends Controller
{
	public $layout='//layouts/column1';
	public function filters()
	{
		return array(
			'accessControl', // perform access control for CRUD operations
/*			array(
				'application.filters.GridViewHandler',
			), */
		);
	}

	public function accessRules()
	{
		return array(
			array('allow',  // allow all users to perform 'index' and 'view' actions
				'actions'=>array('index','settings'),
				'roles'=>array('user'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	public function actionIndex( $page=0 )
	{
		$vr = new ViewRenderer( $this, UserView::secDashboard, $page,
			'dashboard/index', array() );
		$vr->render();
	}

	public function actionSettings()
	{
		$teams = new CActiveDataProvider('Team', array(
			'criteria' => array(
				'condition' => 'ownerid=:userid',
				'params' => array(
					'userid' => Yii::app()->user->id,
				),
			),
		));
		$teamid = NULL;
		if( Yii::app()->user->getModel()->teams == NULL ||
			count(Yii::app()->user->getModel()->teams) == 0 )
		{
			$projects = NULL;
		}
		else
		{
			$T = array_values(Yii::app()->user->getModel()->teams);
			$projects = new CActiveDataProvider('Project', array(
				'criteria' => array(
					'condition' => 'teamid=:team AND parentid IS NULL',
					'params' => array(
						'team' => $T[0]->teamid,
					),
				),
			));
			$teamid = $T[0]->teamid;
		}
		$this->render('settings', array(
			'teamid' => $teamid,
			'teams' => $teams,
			'projects' => $projects,
			'parentProject' => NULL,
		));
	}

	public function _getGridViewProjects()
	{
		$parentProject = Project::model()->findByPk( (int)$_GET['parentid'] );
		$T = array_values(Yii::app()->user->getModel()->teams);
		if( $parentProject === NULL )
		{
			$projects = new CActiveDataProvider('Project', array(
				'criteria' => array(
					'condition' => 'teamid=:team AND parentid IS NULL',
					'params' => array(
						'team' => $T[0]->teamid,
					),
				),
			));
		}
		else
		{
			$projects = new CActiveDataProvider('Project', array(
				'criteria' => array(
					'condition' => 'parentid=:parentid',
					'params' => array(
						'parentid' => $parentProject->projectid,
					),
				),
			));
		}
		$this->renderPartial('_settingsProjects', array(
			'teamid' => $T[0]->teamid,
			'projects' => $projects,
			'parentProject' => $parentProject,
		));
	}

	public function _getGridViewTeams()
	{
		$teams = new CActiveDataProvider('Team', array(
			'criteria' => array(
				'condition' => 'ownerid=:userid',
				'params' => array(
					'userid' => Yii::app()->user->id,
				),
			),
		));
		$this->renderPartial('_settingsTeams', array(
			'teams' => $teams,
		));
	}

	// Uncomment the following methods and override them if needed
	/*
	public function filters()
	{
		// return the filter configuration for this controller, e.g.:
		return array(
			'inlineFilterName',
			array(
				'class'=>'path.to.FilterClass',
				'propertyName'=>'propertyValue',
			),
		);
	}

	public function actions()
	{
		// return external action classes, e.g.:
		return array(
			'action1'=>'path.to.ActionClass',
			'action2'=>array(
				'class'=>'path.to.AnotherActionClass',
				'propertyName'=>'propertyValue',
			),
		);
	}
	*/
}
