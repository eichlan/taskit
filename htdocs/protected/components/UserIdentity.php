<?php

/**
 * UserIdentity represents the data needed to identify a user.
 * It contains the authentication method that checks if the provided
 * data can identity the user.
 */
class UserIdentity extends CUserIdentity
{
	public $model = NULL;

	public function setUserModel( $model )
	{
		$this->model = $model;
	}

	/**
	 * Authenticates a user.
	 * The example implementation makes sure if the username and password
	 * are both 'demo'.
	 * In practical applications, this should be changed to authenticate
	 * against some persistent user identity storage (e.g. database).
	 * @return boolean whether authentication succeeds.
	 */
	public function authenticate()
	{
		$this->model = User::model()->find(
			'LOWER(name)=LOWER(:name)', array(':name'=>$this->username)
		);

		if( $this->model == NULL )
			$this->errorCode = self::ERROR_USERNAME_INVALID;
		else if( !$this->model->checkPass( $this->password ) )
			$this->errorCode = self::ERROR_PASSWORD_INVALID;
		else
			$this->errorCode = self::ERROR_NONE;

		return !$this->errorCode;
	}

	public function getId()
	{
		return $this->model->userid;
	}

	public function getName()
	{
		return $this->model->name;
	}
}
