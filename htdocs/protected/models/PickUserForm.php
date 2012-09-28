<?php

class PickUserForm extends CFormModel
{
	public $name;

	public function rules()
	{
		return array(
			array('name', 'exists'),
		);
	}

	public function exists( $attribute, $params )
	{
		if( User::model()->find(array(
			'condition' => 'lower(name)=:name',
			'params' => array(
				'name' => strtolower( $this->name ),
			),
		)) == NULL )
		{
			$this->addError('name', 'No such user');
		}
	}

	public function attributeLabels()
	{
		return array(
			'name' => 'User name',
		);
	}

}

