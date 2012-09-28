<?php

/**
 * Replaces the user object in the yii application.  This gives access to the
 * current user's model as well as their actual user id and username.  This
 * class does it's own caching of the user's model, evn though Yii says it does
 * some intellegent model caching.  I figure it's safer this way.
 */
class WebUser extends CWebUser
{
	/**
	 *  This holds the model for the active user, all the user's data.
	 *  It's loaded dynamically when getModel() is called, if it needs to be.
	 */
	private $model = NULL;

	/**
	 * Since we know that our UserIdentity class loads the model, why not
	 * snag it from there instead of potentially doing another query, that's
	 * pretty much why this function exists.
	 */
	public function login($identity,$duration=0)
	{
		$this->model = $identity->model;

		return parent::login( $identity, $duration );
	}

	/**
	 * Get the model for the user, load it up if necesarry.
	 *@returns The currently logged in user's model.
	 */
	public function getModel()
	{
		if( $this->model == NULL )
		{
			$this->model = User::model()->findByPk( $this->getId() );
		}
		return $this->model;
	}
}
