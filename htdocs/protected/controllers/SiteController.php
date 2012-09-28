<?php

class SiteController extends Controller
{
	/**
	 * Declares class-based actions.
	 */
	public function actions()
	{
		return array(
			// captcha action renders the CAPTCHA image displayed on the contact page
			'captcha'=>array(
				'class'=>'CCaptchaAction',
				'backColor'=>0xFFFFFF,
			),
			// page action renders "static" pages stored under 'protected/views/site/pages'
			// They can be accessed via: index.php?r=site/page&view=FileName
			'page'=>array(
				'class'=>'CViewAction',
			),
		);
	}

	public function actionFragment( $id )
	{
		if( preg_match('/[^a-zA-Z0-9_]/', $id ) > 0 )
			throw new CHttpException(404, 'No such page');

		// This is taken directly from CController::renderPartial (and
		// then everything we don't need was trimmed), it may not
		// be 100% proper to hijack this code (I didn't check to see if it was
		// public (stable) or private (changable), but with any luck this API
		// will stay stable ;)
		if(($viewFile=$this->getViewFile('fragments/'.$id))!==false)
			echo $this->renderFile($viewFile,array(),true);
		else
			throw new CHttpException(404, 'No such page');
	}

	/**
	 * This is the default 'index' action that is invoked
	 * when an action is not explicitly requested by users.
	 */
	public function actionIndex()
	{
		// renders the view file 'protected/views/site/index.php'
		// using the default layout 'protected/views/layouts/main.php'
		$this->render('index');
	}

	/**
	 * This is the action to handle external exceptions.
	 */
	public function actionError()
	{
	    if($error=Yii::app()->errorHandler->error)
	    {
	    	if(Yii::app()->request->isAjaxRequest)
	    		echo $error['message'];
	    	else
	        	$this->render('error', $error);
	    }
	}

	/**
	 * Displays the contact page
	 */
	public function actionContact()
	{
		$model=new ContactForm;
		if(isset($_POST['ContactForm']))
		{
			$model->attributes=$_POST['ContactForm'];
			if($model->validate())
			{
				$headers="From: {$model->email}\r\nReply-To: {$model->email}";
				mail(Yii::app()->params['adminEmail'],$model->subject,$model->body,$headers);
				Yii::app()->user->setFlash('contact','Thank you for contacting us. We will respond to you as soon as possible.');
				$this->refresh();
			}
		}
		$this->render('contact',array('model'=>$model));
	}

	/**
	 * Displays the login page
	 */
	public function actionLogin()
	{
		$model=new LoginForm;

		// if it is ajax validation request
		if(isset($_POST['ajax']) && $_POST['ajax']==='login-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}

		// collect user input data
		if(isset($_POST['LoginForm']))
		{
			$model->attributes=$_POST['LoginForm'];
			// validate user input and redirect to the previous page if valid
			if($model->validate() && $model->login())
				$this->redirect(Yii::app()->user->returnUrl);
		}
		// display the login form
		$this->render('login',array('model'=>$model));
	}

	public function actionRegister()
	{
		$model=new RegisterForm;

		// uncomment the following code to enable ajax-based validation
		if(isset($_POST['ajax']) && $_POST['ajax']==='register-form-register-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}

		if(isset($_POST['RegisterForm']))
		{
			$model->attributes=$_POST['RegisterForm'];
			if($model->validate() && $model->register())
				$this->redirect(array('/dashboard/index'));
		}
		$this->render('register',array('model'=>$model));
	}

	/**
	 * Logs out the current user and redirect to homepage.
	 */
	public function actionLogout()
	{
		Yii::app()->user->logout();
		$this->redirect(Yii::app()->homeUrl);
	}

	/**
	 * Creates a new anonymous invitation, if applicable.
	 **/
	public function actionNewInvite()
	{
		if( !Yii::app()->params['anonInvites'] )
		{
			print json_encode(array(
				'error'=>'Anonymous invitations are not enabled.'
			));
			return;
		}

		$cnt = $this->getCodesLeft();
		if( $cnt <= 0 )
		{
			print json_encode(array(
				'error'=>'There are no anonymous invitations left, '.
					'wait a day and try again.'
			));
			return;
		}
		
		if( Yii::app()->db->createCommand('SELECT count(*) AS count FROM invitation_code WHERE senderid IS NULL AND created>=(now()-INTERVAL \'30 min\')')->query()->readAll()[0]['count'] > 0 )
		{
			print json_encode(array(
				'error'=>'There are no anonymous invitations left, '.
					'wait an hour and try again.'
			));
			return;
		}
		
		$invite = new InvitationCode;
		$invite->senderid = NULL;
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
		$result = Yii::app()->db->createCommand('SELECT count(*) AS count FROM invitation_code WHERE senderid IS NULL AND created>=(now()-INTERVAL \'1 week\')')->query()->readAll();

		$max = Yii::app()->params['anonInviteLimit'];
		return max( min( $max, $max-$result[0]['count']), 0 );
	}
}
