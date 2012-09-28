<?php

class TeamController extends Controller
{
	public $layout='//layouts/column1';

	/**
	 * @return array action filters
	 */
	public function filters()
	{
		return array(
			'accessControl', // perform access control for CRUD operations
		);
	}

	/**
	 * Specifies the access control rules.
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	 */
	public function accessRules()
	{
		return array(
			array('allow',  // allow all users to perform 'index' and 'view' actions
				'actions'=>array('index','view'),
				'users'=>array('@'),
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('create','update','listProjects','addUser',
					'userAutoComplete', 'quickCreate', 'delete'),
				'users'=>array('@'),
			),
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions'=>array('admin','delete'),
				'users'=>array('admin'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	public function actionUserAutoComplete( $term, $teamid=NULL )
	{
		if( $teamid === NULL )
		{
			$res = Yii::app()->db->createCommand(
				'SELECT name FROM "user" WHERE lower(name) LIKE lower(:term) '.
				'ORDER BY name DESC LIMIT 20'
			)->query(array('term'=>$term.'%'));
		}
		else
		{
			$res = Yii::app()->db->createCommand(
				'SELECT name FROM "user" LEFT JOIN team_user ON '.
				'team_user.userid="user".userid WHERE teamid=:teamid AND '.
				'lower(name) LIKE lower(:term) '.
				'ORDER BY name DESC LIMIT 20'
			)->query(array('teamid'=>$teamid,'term'=>$term.'%'));
		}

		$ret = array();
		foreach( $res as $row )
		{
			$ret[] = $row['name'];
		}

		echo json_encode( $ret );
		Yii::app()->end();
	}

	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionView($id)
	{
		$model = $this->loadModel( $id );

		if( !$model->hasUserId( Yii::app()->user->id ) )
			throw new CHttpException(404,'The requested page does not exist.');

		$this->activeTeamId = $model->teamid;
		$this->render('view',array(
			'model'=>$model,
		));
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$model=new Team;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Team']))
		{
			$model->attributes=$_POST['Team'];
			$model->ownerid = Yii::app()->user->id;
			if($model->save())
			{
				$model->setupTeamTemplate();
				$this->redirect(array('dashboard/settings#teams'));
			}
		}

		$this->render('create',array(
			'model'=>$model,
		));
	}
	
	public function actionQuickCreate()
	{
		$model=new Team;

		if(isset($_POST['Team']))
		{
			$model->attributes=$_POST['Team'];
			$model->ownerid = Yii::app()->user->id;
			if($model->save())
			{
				$model->setupTeamTemplate();
				$this->redirect(array('settings/structure', 'selid'=>'t'.$model->teamid));
			}
		}

		$this->renderPartial('quickCreate',array(
			'model'=>$model,
		));
	}

	public function actionDelete( $id )
	{
		Team::model()->deleteByPk( (int)$id );
		$this->redirect(array('settings/structure'));
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id)
	{
		$model=$this->loadModel($id);

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Team']))
		{
			$model->attributes=$_POST['Team'];
			if($model->save())
				$this->redirect(array('view','id'=>$model->teamid));
		}

		$this->render('update',array(
			'model'=>$model,
		));
	}

	public function actionListProjects()
	{
		$data = Team::model()->findByPk(
			(int)$_POST['Project']['teamid']
		)->getProjectTree();
		foreach( $data as $value=>$name )
		{
			echo CHtml::tag(
				'option',
				array('value' => $value),
				CHtml::encode($name),
				true
			);
		}
	}

	public function actionAddUser( $teamid )
	{
		$this->layout = '//layouts/ajax';
		$model = new PickUserForm;

		if(isset($_POST['ajax']) && $_POST['ajax']==='pick-user-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}

		if( isset($_POST['PickUserForm']) )
		{
			$model->attributes = $_POST['PickUserForm'];
			if( $model->validate() )
			{
				if( Team::model()->findByPk( (int)$teamid ) === NULL )
				{
					echo 'No such team: '.$teamid;
					Yii::app()->end();
				}
				$user = User::model()->find(array(
					'condition' => 'lower(name)=:name',
					'params' => array('name' => strtolower($model->name)),
				));
				$tu = new TeamUser;
				$tu->teamid = (int)$teamid;
				$tu->userid = $user->userid;
				$tu->save();
				echo 'TeamUser('.$tu->teamid.', '.$tu->userid.')';
				Yii::app()->end();
			}
		}
/*
		$this->render('addUser', array(
			'model'=>$model,
			'teamid'=>$teamid,
		));*/
	//	Yii::app()->clientScript->renderBodyEnd( $data );
		Yii::app()->end();
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id)
	{
		$model=Team::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param CModel the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='team-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
