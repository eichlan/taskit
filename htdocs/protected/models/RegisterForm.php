<?php

/**
 * RegisterForm class.
 * RegisterForm is the data structure for keeping
 * user login form data. It is used by the 'login' action of 'SiteController'.
 */
class RegisterForm extends CFormModel
{
	public $username;
	public $password;
	public $password2;
	public $email;
	public $verifyCode;
	public $invite;

	private $_identity;

	/**
	 * Declares the validation rules.
	 * The rules state that username and password are required,
	 * and password needs to be authenticated.
	 */
	public function rules()
	{
		return array(
			// username and password are required
			array('username, password, password2, email', 'required'),
			// password needs to be authenticated
			array('email', 'email'),
			array('password2', 'confirm'),
			array('invite', 'inviteCheck'),
//			array('verifyCode', 'captcha', 'allowEmpty'=>!CCaptcha::checkRequirements()),
		);
	}

	public function inviteCheck($attribute, $params )
	{
		$code = InvitationCode::model()->findByAttributes(array(
			'code' => $this->invite,
			'enabled' => true,
			'recipientid' => null,
		));

		if( $code === null )
		{
			$this->adderror('invite', 'Invalid invitation code.');
		}
	}

	/**
	 * Authenticates the password.
	 * This is the 'authenticate' validator as declared in rules().
	 */
	public function confirm($attribute,$params)
	{
		if(!$this->hasErrors())
		{
			if( $this->password != $this->password2 )
				$this->addError('password2','The two passwords must match.');
		}
	}

	/**
	 * Logs in the user using the given username and password in the model.
	 * @return boolean whether register is successful
	 */
	public function register()
	{
		$model = User::register(
			$this->username,
			$this->email,
			$this->password
		);
		
		$code = InvitationCode::model()->findByAttributes(array(
			'code' => $this->invite,
			'enabled' => true,
			'recipientid' => null,
		));
		$code->recipientid = $model->userid;
		$code->enabled = false;
		$code->save();

		$identity = new UserIdentity( $model->name, $this->password );
		$identity->authenticate();
		Yii::app()->user->login($identity, 3600*24*30);		
		
		return True;
	}

	public function attributeLabels()
	{
		return array(
			'password2' => 'Password Verify',
			'invite' => 'Invitation Code',
		);
	}
}
