<?php

/**
 * A simple interface to a properties table in the database.  This class makes
 * the items in the database look like class variables.  If you want to save
 * a new property, just set a new variable on this class.
 */
class GlobalProperties implements IApplicationComponent
{
	private $fields = array();

	/**
	 * We don't really need to do any initialization, so these are empty.
	 */
	public function init()
	{
	}

	/**
	 * I figure we just always return true here, we're good to go.
	 */
	public function getIsInitialized()
	{
		return true;
	}

	public function __set( $name, $value )
	{
		$this->fields[$name] = $value;
		$command = Yii::app()->db->createCommand(
			"UPDATE global_property SET value=:value WHERE name=:name");
		$params = array('value'=>serialize($value), 'name'=>$name);
		if( $command->execute( $params ) == 0 )
		{
			$command = Yii::app()->db->createCommand(
				"INSERT INTO global_property (name, value) ".
				"VALUES (:name, :value)");
			$command->execute( $params );
		}
	}

	public function __get( $name )
	{
		if( isset( $this->fields[$name] ) )
			return $this->fields[$name];

		$command = Yii::app()->db->createCommand(
			"SELECT value FROM global_property WHERE name=:name");
		$res = $command->query(array('name'=>$name));
		if( $res->rowCount == 0 )
			return NULL;
		$row = $res->read();
		$this->fields[$name] = unserialize($row['value']);
		return $this->fields[$name];
	}

	public function __isset( $name )
	{
		if( isset($this->fields[$name]) )
			return true;
		$this->__get( $name );
		return isset($this->fields[$name]);
	}

	public function __unset( $name )
	{
		unset( $this->fields[$name] );
		$command = Yii::app()->db->createCommand(
			"DELETE FROM global_property WHERE name=:name");
		$command->execute(array('name'=>$name));
	}
};
