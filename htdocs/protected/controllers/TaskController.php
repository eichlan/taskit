<?php

class TaskController extends Controller
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
				'actions'=>array('create','update','setStatus','setPriority',
					'setName','setType','assign', 'setComplexity',
					'quickCreate', 'getDesc', 'setDesc', 'attachFile',
					'setLogFilter'),
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

	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionView($id)
	{
		$model = $this->loadModel( $id );
		if( !$model->hasUserid( Yii::app()->user->id ) )
			throw new CHttpException(404,'The requested page does not exist.');
		$this->activeProjectId = $model->projectid;
		$this->activeTeamId = $model->project->teamid;

		$cond = array('taskid=:id');
		$params = array('id' => $model->taskid);
		if( !isset(Yii::app()->session['logFilter']) )
			Yii::app()->session['logFilter'] = 0;
		switch( Yii::app()->session['logFilter'] )
		{
		case 1:
		case 2:
			$cond[] = 'type=:type';
			$params['type'] = Yii::app()->session['logFilter'];
			break;
		default:
			Yii::app()->session['logFilter'] = 0;
		case 0:
			break;
		}

		$comments = new CActiveDataProvider('TaskComment', array(
			'criteria' => array(
				'condition' => implode(' AND ', $cond),
				'params' => $params,
				'order' => 'created DESC',
			),
		));
		$this->render('view',array(
			'model'=>$this->loadModel($id),
			'comments' => $comments,
		));
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate( $projectid )
	{
		$project = Project::model()->with(array(
			'team',
			'team.teamPriorities',
			'team.teamTaskTypes',
			'team.teamTaskStatuses',
		))->findByPk( $projectid );
		if( !$project->hasUserid( Yii::app()->user->id ) )
			throw new CHttpException(404,'The requested page does not exist.');

		$model=new Task;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Task']))
		{
			$model->attributes=$_POST['Task'];
			$model->authorid = Yii::app()->user->id;
			$model->projectid = $project->projectid;
			$model->teamid = $project->teamid;
			if($model->save())
				$this->redirect(array('view','id'=>$model->taskid));
		}

		$this->render('create',array(
			'model'=>$model,
			'project'=>$project,
		));
	}

	protected static function error( $txt )
	{
		print json_encode( array('result' => 'error', 'error' => $txt ) );
		Yii::app()->end();
	}
	
	public function actionQuickCreate()
	{
		if( !isset($_POST['projectid']) )
			TaskController::error('Bad Request');

		$projectid = (int)$_POST['projectid'];
		// We should check the project here for security (TODO)
		$project = Project::model()->findByPk( (int)$projectid );
		if( $project === null )
			TaskController::error('Bad Request');
		if( !$project->hasUserid( Yii::app()->user->id ) )
			throw new CHttpException(404,'The requested page does not exist.');

		$model=new Task;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		$model->authorid = Yii::app()->user->id;
		$model->projectid = $project->projectid;
		$model->teamid = $project->teamid;
		$model->statusid = (int)$_POST['statusid'];
		$model->typeid = (int)$_POST['typeid'];
		$model->name = $_POST['name'];
		if($model->save())
			print json_encode( array('result'=>'ok',
				'url'=>Yii::app()->createUrl('task/view',
					array('id'=>$model->taskid )
				), 'name' => $model->name ) );
		else
			TaskController::error( $model->getErrors() );
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

		if(isset($_POST['Task']))
		{
			$model->attributes=$_POST['Task'];
			if($model->save())
				$this->redirect(array('view','id'=>$model->taskid));
		}

		$this->render('update',array(
			'model'=>$model,
			'project'=>$model->project,
		));
	}
	
	public function actionAssign($id)
	{
		$model=$this->loadModel($id);
		if( !$model->hasUserid( Yii::app()->user->id ) )
			throw new CHttpException(404,'The requested page does not exist.');

		if(isset($_POST['PickUserForm']))
		{
			$form = new PickUserForm;
			$form->attributes=$_POST['PickUserForm'];
			$user = User::model()->find('lower(name)=lower(:name)',
				array('name' => $form->name )
			);
			if( $user === NULL )
			{
				print '!No such user.';
				return;
			}
			$ut = TeamUser::model()->findByPk(array(
				'teamid'=>$model->project->teamid,
				'userid'=>$user->userid,
			));
			if( $ut === NULL )
			{
				print '!No such user.';
				return;
			}

			$ta = new TaskAssignment();
			$ta->userid = $user->userid;
			$ta->taskid = $model->taskid;
			$ta->save();

			$model->log('Assigned to ' . $user->name );	
		}
		echo 'Assigned to ';
		echo Utility::userList( $model->assignedTo );

		return;
	}

	public function actionSetStatus( $id, $status )
	{
		$task = Task::model()->findByPk( (int)$id );
		if( !$task->hasUserid( Yii::app()->user->id ) )
			throw new CHttpException(404,'The requested page does not exist.');
		if( $task->statusid != (int)$status )
		{
			$oldstatus = TeamTaskStatus::model()->findByPk( $task->statusid );
			$task->statusid = (int)$status;
			$task->save();
			
			$task->log('Status changed from ' . $oldstatus->name . ' to ' .
				$task->status->name . '.' );
		}
		echo $task->status->name;
	}

	public function actionSetPriority( $id, $priority )
	{
		$task = Task::model()->findByPk( (int)$id );
		if( !$task->hasUserid( Yii::app()->user->id ) )
			throw new CHttpException(404,'The requested page does not exist.');
		if( $task->priority != (int)$priority )
		{
			$oldPrio = TeamPriority::model()->findByPk(array(
				'teamid' => $task->project->teamid,
				'priority' => $task->priority,
			));
			$task->priority = (int)$priority;
			$task->save();
			$newPrio = TeamPriority::model()->findByPk(array(
				'teamid' => $task->project->teamid,
				'priority' => $task->priority,
			));

			$task->log('Priority changed from '.$oldPrio->name.' to '.
				$newPrio->name.'.');
			echo $newPrio->name . ' priority';
			return;
		}
		
		echo TeamPriority::model()->findByPk(array(
			'teamid' => $task->project->teamid,
			'priority' => $task->priority,
		))->name . ' priority';
	}
	
	public function actionSetComplexity( $id, $complexity )
	{
		$task = Task::model()->findByPk( (int)$id );
		if( !$task->hasUserid( Yii::app()->user->id ) )
			throw new CHttpException(404,'The requested page does not exist.');
		if( $task->complexity != (int)$complexity )
		{
			$task->log('Complexity changed from ' . $task->complexity . ' to '.
				(int)$complexity .'.' );
			$task->complexity = (int)$complexity;
			$task->save();
		}

		echo $task->complexity;
	}

	public function actionSetName( $id, $name )
	{
		$task = Task::model()->findByPk( (int)$id );
		if( !$task->hasUserid( Yii::app()->user->id ) )
			throw new CHttpException(404,'The requested page does not exist.');
		if( $task->name != $name )
		{
			$oldname = $task->name;
			$task->name = $name;
			if( $task->save() )
			{
				$task->log('Name changed from "' . $oldname . '" to "'.
					$name . '".');
			}
			else
			{
				$task->name = $oldname;
			}
		}
		echo htmlspecialchars($task->name);
	}

	public function actionSetType( $id, $type )
	{
		$task = Task::model()->findByPk( (int)$id );
		if( !$task->hasUserid( Yii::app()->user->id ) )
			throw new CHttpException(404,'The requested page does not exist.');
		if( $task->typeid != (int)$type )
		{
			$oldType = TeamTaskType::model()->findByPk($task->typeid);
			$task->typeid = (int)$type;
			$task->save();

			$task->log('Type changed from ' . $oldType->name . ' to ' .
				$task->type->name . '.' );
		}
		echo $task->type->name;
	}

	public function actionGetDesc( $id, $raw=true )
	{
		$task = Task::model()->findByPk( (int)$id );
		if( !$task->hasUserid( Yii::app()->user->id ) )
			throw new CHttpException(404,'The requested page does not exist.');
		if( $raw === true )
		{
			echo $task->description;
		}
		else
		{
			$parse = new CMarkdownParser;
			echo $parse->safeTransform($task->description);
		}
	}

	public function actionSetDesc( $id )
	{
		$task = Task::model()->findByPk( (int)$id );
		if( !$task->hasUserid( Yii::app()->user->id ) )
			throw new CHttpException(404,'The requested page does not exist.');
		$desc = file_get_contents('php://input');
		if( $task->description != $desc )
		{
			$task->description = $desc;
			$task->save();
			
			$task->log('Description changed.');
		}
		$parse = new CMarkdownParser;
		echo $parse->safeTransform($task->description);
	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete($id)
	{
		if(Yii::app()->request->isPostRequest)
		{
			// we only allow deletion via POST request
			$task = $this->loadModel($id);
			if( !$task->hasUserid( Yii::app()->user->id ) )
				throw new CHttpException(404,'The requested page does not exist.');
			$task->delete();

			// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
			if(!isset($_GET['ajax']))
				$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
		}
		else
			throw new CHttpException(400,'Invalid request. Please do not repeat this request again.');
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		$dataProvider=new CActiveDataProvider('Task');
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
		));
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new Task('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Task']))
			$model->attributes=$_GET['Task'];

		$this->render('admin',array(
			'model'=>$model,
		));
	}

	public function actionAttachFile()
	{
		if( $_FILES['upload-box']['error'] == 0 )
		{
			$fa = new FileAttachment;
			$fh = fopen('/proc/sys/kernel/random/uuid', 'rb');
			$fa->fileid = fread($fh, 36 );
			fclose( $fh );

			$task = $this->loadModel( $_POST['taskid'] );
			if( !$task->hasUserid( Yii::app()->user->id ) )
				throw new CHttpException(404,'The requested page does not exist.');

			$note = $_POST['note'];
			if( $note == '' )
				$note = 'Attachment uploaded.';
			$cid = $task->log( $note );

			$fa->authorid = Yii::app()->user->id;
			$fa->projectid = $task->projectid;
			$fa->taskid = $task->taskid;
			$fa->commentid = $cid;
			$fa->filename = $_FILES['upload-box']['name'];
			$fi = finfo_open(FILEINFO_MIME);
			$fa->mimetype = finfo_file(
				$fi, $_FILES['upload-box']['tmp_name']
				);
			$fa->size = $_FILES['upload-box']['size'];
			$fa->sha1 = sha1_file( $_FILES['upload-box']['tmp_name'] );
			$fa->save();

			if( !file_exists('protected/files/attachment') )
			{
				mkdir('protected/files/attachment');
			}
			move_uploaded_file( $_FILES['upload-box']['tmp_name'],
				'protected/files/attachment/'.$fa->fileid
			);
		}
		$this->redirect(array('task/view', 'id'=>$_POST['taskid']));
	}

	public function actionSetLogFilter( $flt )
	{
		Yii::app()->session['logFilter'] = $flt;
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id)
	{
		$model=Task::model()->findByPk($id);
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
		if(isset($_POST['ajax']) && $_POST['ajax']==='task-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
