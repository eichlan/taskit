<?php

/**
 * RegisterForm class.
 * RegisterForm is the data structure for keeping
 * user login form data. It is used by the 'login' action of 'SiteController'.
 */
class ChangePasswordForm extends CFormModel
{
	public $password;
	public $password2;

	/**
	 * Declares the validation rules.
	 * The rules state that username and password are required,
	 * and password needs to be authenticated.
	 */
	public function rules()
	{
		return array(
			// username and password are required
			array('password, password2', 'required'),
			array('password2', 'confirm'),
		);
	}

	public function confirm($attribute,$params)
	{
		if(!$this->hasErrors())
		{
			if( $this->password != $this->password2 )
				$this->addError('password2','The two passwords must match.');
		}
	}

	public function changePassword()
	{
		$model = User::model()->findByPk( Yii::app()->user->id );
		$model->setPassword( $this->password );
		$model->save();
		
		return True;
	}

	public function attributeLabels()
	{
		return array(
			'password' => 'New Password',
			'password2' => 'Password Verify',
		);
	}
}
