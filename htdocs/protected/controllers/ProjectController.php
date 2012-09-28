<?php

function endsWith($haystack, $needle)
{
    $length = strlen($needle);
    $start  = $length * -1; //negative
    return (substr($haystack, $start) === $needle);
}

class ProjectController extends Controller
{
	public $layout='//layouts/column1';

	public function filters()
	{
		return array(
			'accessControl', // perform access control for CRUD operations
		);
	}

	public function accessRules()
	{
		return array(
			array('allow',
				'actions'=>array('create','view','quickCreate','uploadTags','code', 'index', 'reparent', 'delete'),
				'roles'=>array('user'),
			),
			array('deny',
				'users'=>array('*'),
			),
		);
	}

	public function actionCreate()
	{
		$model=new Project;

		// uncomment the following code to enable ajax-based validation
		if(isset($_POST['ajax']) && $_POST['ajax']==='project-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}

		if(isset($_POST['Project']))
		{
			$model->attributes=$_POST['Project'];
			if($model->save())
			{

				return;
			}
		}

		$user = Yii::app()->user->getModel();

		$teams = array_values( $user->teams );

		$this->render('create', array(
			'model' => $model,
			'teams' => $user->teams,
			'projects' => $teams[0]->getProjectTree()
		));
	}
	
	public function actionQuickCreate( $teamid, $parentid=NULL )
	{
		$model=new Project;

		if( $parentid == 'null' )
			$parentid = null;

		// uncomment the following code to enable ajax-based validation
		if(isset($_POST['ajax']) && $_POST['ajax']==='project-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}

		if(isset($_POST['Project']))
		{
			$model->attributes=$_POST['Project'];
			$model->teamid = (int)$teamid;
			$team = Team::model()->findByPk( $model->teamid );
			if( !$team->hasUserId( Yii::app()->user->id ) )
				throw new CHttpException(404, 'No such document found.');
			if( $parentid === NULL || $parentid==='')
				$model->parentid = NULL;
			else
				$model->parentid = (int)$parentid;
			if($model->save())
			{
				$this->redirect(array('settings/structure', 'selid'=>'p'.$model->projectid));
				return;
			}
		}

		$this->renderPartial('quickCreate', array(
			'model' => $model,
			'parentid' => $parentid,
			'teamid' => $teamid,
		));
	}

	public function actionDelete( $id )
	{
		$project = Project::model()->findByPk( (int)$id );
		if( $project === NULL )
			throw new CHttpException(404, 'No such document found.');
		if( !$project->hasUserId( Yii::app()->user->id ) )
			throw new CHttpException(404, 'No such document found.');
		$project->delete();
		
//		Project::model()->deleteByPk( (int)$id );
		$this->redirect(array('settings/structure'));
	}

	public function actionReparent( $id, $newParent )
	{
		$project = Project::model()->findByPk( (int)$id );
		if( $project === NULL )
			throw new CHttpException(404, 'No such document found.');
		if( !$project->hasUserId( Yii::app()->user->id ) )
			throw new CHttpException(404, 'No such document found.');
		$teamid = null;
		if( $newParent[0] == 'p' )
		{
			$newParent = Project::model()->findByPk(
				(int)substr( $newParent, 1 )
			);
			if( $newParent === NULL )
				throw new CHttpException(404, 'No such document found.');
			$project->parentid = $newParent->projectid;
			$teamid = $newParent->teamid;
		}
		else if( $newParent[0] == 't' )
		{
			$team = Team::model()->findByPk( (int)substr( $newParent, 1 ) );
			if( $team === NULL )
				throw new CHttpException(404, 'No such document found.');
			
			$project->parentid = null;
			$teamid = $team->teamid;
		}
		else
		{
			throw new CHttpException(404, 'No such document found.');
		}
		$project->save();

		if( $teamid != $project->teamid )
		{
			// The team changed, now we have to recursively change the team of
			// all child projects and tasks
			$project->teamid = $teamid;
			$this->updateTeam( $project, $teamid );
		}
	}

	private function updateTeam( $project, $teamid )
	{
		$cmd = Yii::app()->db->createCommand(
			'UPDATE task SET teamid=:teamid WHERE projectid=:pid');

		$this->_updateTeam( $project, $teamid, $cmd );
	}

	private function _updateTeam( $project, $teamid, $cmd )
	{
		$project->teamid = $teamid;
		$project->save();
		$cmd->execute(array(
			'teamid' => $teamid,
			'pid' => $project->projectid,
		));
		foreach( $project->children as $p )
		{
			$this->_updateTeam( $p, $teamid, $cmd );
		}
	}

	public function actionIndex()
	{
		$this->render('index');
	}

	public function actionUpdate()
	{
		$this->render('update');
	}

	public function actionView( $id, $page=NULL )
	{
		$project = Project::model()->findByPk( (int)$id );
		if( $project === NULL )
			throw new CHttpException(404, 'No such document found.');
		if( !$project->hasUserId( Yii::app()->user->id ) )
			throw new CHttpException(404, 'No such document found.');

		$this->activeProjectId = $project->projectid;

		$vr = new ViewRenderer( $this, UserView::secProject, $page,
			'project/view', array('id'=>$id), array(
			'project' => $project,
		));
		$vr->render();
	}

	public function actionUploadTags( $id )
	{
		$model=new UploadTagsForm;

		$project = Project::model()->findByPk( (int)$id );
		if( $project === NULL )
			throw new CHttpException(404, 'No such project found.');
		if( !$project->hasUserId( Yii::app()->user->id ) )
			throw new CHttpException(404, 'No such document found.');

		if(isset($_POST['UploadTagsForm']))
		{
			$model->attributes=$_POST['UploadTagsForm'];
			if($model->validate())
			{
				// gzdeflate / gzinflate && store in session
				print '<pre>';
//				print_r( $_POST['UploadTagsForm'] );
//				print_r( $_FILES );

				$name = $_FILES['UploadTagsForm']['name']['file'];
				$tmpname = $_FILES['UploadTagsForm']['tmp_name']['file'];
				if( endsWith( $name, '.xz') )
				{
					$fin = popen("xzcat " . $tmpname, 'rb');
					$p = true;
				}
				else if( endsWith( $name, '.bz2') )
				{
					$fin = popen("bzcat " . $tmpname, 'rb');
					$p = true;
				}
				else if( endsWith( $name, '.gz') )
				{
					$fin = popen("zcat " . $tmpname, 'rb');
					$p = true;
				}
				else
				{
					$fin = fopen( $tmpname, 'rb');
					$p = false;
				}
				$types = array();
				while( !feof($fin) )
				{
					$l = fgets( $fin );
					if( $l[0] == '!' )
						continue;

					if( preg_match('/^([^\t]*)\t([^\t]*)\t(.*;")\t([^\t]*)\t?(.*)/', $l, $r ) == 0 )
						continue;

					$bits = explode("\t", $r[5]);
					if( count($bits) < 2 )
					{
						$parent = null;
					}
					else
						$parent = $bits[1];
					$lang = explode(':', $bits[0]);

					if( $r[4] == 'class' || $r[4] == 'struct' )
					{
						$cu = CodeUnit::model()->findByAttributes(array(
							'projectid' => $project->projectid,
							'name' => $r[1],
							'parent' => $parent
						));
						if( $cu === NULL )
						{
							$cu = new CodeUnit;
							$cu->projectid = $project->projectid;
							$cu->name = $r[1];
							$cu->parent = $parent;
							$cu->complete = 0.0;
							$cu->ignore = false;
						}
						$cu->language = $lang[1];
						$cu->type = $r[4];
						$cu->file = $r[2];
						if( !$cu->save() )
						{
							print_r($cu->getErrors() );
						}
					}
//					print $r[4] . " " .$r[1] . ", " . $r[5] . ", " . $r[2] . "\n";

					$il = $lang[1];
					$it = $r[4];
					if( !isset($types[$il][$it]) )
					{
						$types[$il][$it]['count'] = 0;
						$types[$il][$it]['ex'] = array();
					}
					$types[$il][$it]['count']++;
					if( count($types[$il][$it]['ex']) < 4 )
						$types[$il][$it]['ex'][$r[1]] = true;
//					print_r($bits);
				}

				// form inputs are valid, do something here
				if( $p )
				{
					pclose( $fin );
				}
				else
				{
					fclose( $fin );
				}
				print_r( $types );
				return;
			}
		}
		$this->render('uploadTags',array(
			'project' => $project,
			'model'=>$model
		));
	}

	public function actionCode( $id )
	{
		$project = Project::model()->findByPk( (int)$id );
		if( $project === NULL )
			throw new CHttpException(404, 'No such project found.');
		if( !$project->hasUserId( Yii::app()->user->id ) )
			throw new CHttpException(404, 'No such document found.');

		$code = new CActiveDataProvider('CodeUnit', array(
			'criteria' => array(
				'condition' => 'projectid=:proj AND ignore=false',
				'params' => array(
					'proj' => $project->projectid
				),
				'order' => 'complete ASC, name ASC',
			),
		));

		$this->render('code', array(
			'project'=>$project,
			'code'=>$code
		));
	}
}
