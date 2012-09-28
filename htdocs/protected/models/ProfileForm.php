<?php

class ProfileForm extends CFormModel
{
	public $realName;
	public $email;
	public $avatar;
	public $timezone;

	public function rules()
	{
		return array(
			array('email, timezone', 'required'),
			array('realName, avatar', 'safe'),
			array('email', 'email'),
		);
	}

	public function attributeLabels()
	{
		return array(
			'avatar' => 'Upload an Avatar',
		);
	}
}

