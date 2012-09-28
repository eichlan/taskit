<?php

include_once('FileController.php');

class SettingsController extends Controller
{
	public $layout='//layouts/settings';
	public function filters()
	{
		return array(
			'accessControl', // perform access control for CRUD operations
		);
	}

	public function accessRules()
	{
		return array(
			array('allow',  // allow all users to perform 'index' and 'view' actions
				'actions'=>array('profile','getNewAvatar', 'structure',
					'tptreeDetails', 'invitations', 'newInvite'),
				'roles'=>array('user'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	public function actionProfile()
	{
		$model=new ProfileForm;
		$chgpw=new ChangePasswordForm;
		$user = Yii::app()->user->getModel();

		if(isset($_POST['ProfileForm']))
		{
			$model->attributes=$_POST['ProfileForm'];
			if($model->validate())
			{
				if( $_FILES['ProfileForm']['error']['avatar'] == 0 )
				{
					if( !file_exists('protected/files/avatar') )
						mkdir('protected/files/avatar');
					$dst = 'protected/files/avatar/'.Yii::app()->user->id;
					unlink( $dst );
					move_uploaded_file( $_FILES['ProfileForm']['tmp_name']['avatar'], $dst );
					FileController::resetCache('avatar', Yii::app()->user->id );
				}

				$user->email = $model->email;
				$user->timezone = $model->timezone;
				$user->realname = $model->realName;
				$user->save();
			}
		}
		else
		{
			$model->email = $user->email;
			$model->timezone = $user->timezone;
			$model->realName = $user->realname;
		}
		
		if(isset($_POST['ChangePasswordForm']))
		{
			$chgpw->attributes=$_POST['ChangePasswordForm'];
			if( $chgpw->validate() && $chgpw->changePassword() )
			{
			}
		}

		$tzList = array();
		foreach( timezone_abbreviations_list() as $tz )
		{
			foreach( $tz as $t )
			{
				if( $t['timezone_id'] != '' )
				{
					$tzList[$t['timezone_id']] = $t['timezone_id'];
				}
			}
		}
		asort( $tzList );

		$this->render('profile',array(
			'model'=>$model,
			'tzList' => $tzList,
			'chgpw' => $chgpw,
		));
	}

	public function actionGetNewAvatar()
	{
		$ap = new AvatarPicker();
		if( !file_exists('protected/files/avatar') )
			mkdir('protected/files/avatar');
		$ap->selectRandom( 'protected/files/avatar/' . Yii::app()->user->id );
		FileController::resetCache('avatar', Yii::app()->user->id );
	}

	public function actionTptreeDetails( $id )
	{
		if( $id[0] == 't' )
		{
			$model = Team::model()->findByPk( (int)substr($id,1) );
			if(isset($_POST['Team']))
			{
				$model->attributes=$_POST['Team'];
				if($model->validate())
				{
					$model->save();
					$this->redirect(array('settings/structure', 'selid'=>$id));
					return;
				}
			}

			$this->renderPartial('teamPanel', array('model'=>$model) );
		}
		elseif( $id[0] == 'p' )
		{
			$model = Project::model()->findByPk( (int)substr($id,1) );
			if(isset($_POST['Project']))
			{
				$model->attributes=$_POST['Project'];
				if($model->validate())
				{
					$model->save();
					$this->redirect(array('settings/structure', 'selid'=>$id));
					return;
				}
			}
			$this->renderPartial('projectPanel', array('model'=>$model) );
		}
	}

	public function actionStructure( $selid=null )
	{
		$this->render('structure', array(
			'selid' => $selid,
		));
	}

	public function actionInvitations()
	{
		$dataProvider = new CActiveDataProvider('InvitationCode',array(
			'criteria'=>array(
				'condition' => 'senderid=:id',
				'params' => array(
					'id' => Yii::app()->user->id,
				),
			),
			'sort'=>array(
				'defaultOrder'=>array(
					'enabled'=>true,
				),
			),
		));
		$this->render('invitations', array(
			'dataProvider' => $dataProvider,
			'codesLeft' => $this->getCodesLeft(),
		));
	}

	public function actionNewInvite()
	{
		$cnt = $this->getCodesLeft();
		if( $cnt <= 0 )
		{
			print json_encode(array('error'=>'You have no more invitations left, wait a few days and try again.'));
			return;
		}
		$invite = new InvitationCode;
		$invite->senderid = Yii::app()->user->id;
		$src = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
		do
		{
			$invite->code = '';
			for( $j = 0; $j < 16; $j++ )
			{
				$invite->code .= $src[mt_rand(0,61)];
			}
		} while( !$invite->save() );
		print json_encode(array('code'=>$invite->code, 'count'=>max(0,$cnt-1)));
	}

	private function getCodesLeft()
	{
		$result = Yii::app()->db->createCommand('SELECT count(*) AS count FROM invitation_code WHERE senderid=:uid AND created>=(now()-INTERVAL \'1 week\')')->query(array(
			'uid' => Yii::app()->user->id,
		))->readAll();

		$max = Yii::app()->params['userInviteLimit'];
		return max( min( $max, $max-$result[0]['count']), 0 );
	}
}
