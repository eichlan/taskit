<?php

/**
 * This is the model class for table "team_task_status".
 *
 * The followings are the available columns in table 'team_task_status':
 * @property integer $statusid
 * @property integer $teamid
 * @property string $name
 * @property boolean $open
 *
 * The followings are the available model relations:
 * @property Team $team
 * @property Task[] $tasks
 */
class TeamTaskStatus extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return TeamTaskStatus the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'team_task_status';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name', 'required'),
			array('teamid', 'numerical', 'integerOnly'=>true),
			array('name', 'length', 'max'=>64),
			array('open', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('statusid, teamid, name, open', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'team' => array(self::BELONGS_TO, 'Team', 'teamid'),
			'tasks' => array(self::HAS_MANY, 'Task', 'statusid'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'statusid' => 'Statusid',
			'teamid' => 'Teamid',
			'name' => 'Name',
			'open' => 'Open',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('statusid',$this->statusid);
		$criteria->compare('teamid',$this->teamid);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('open',$this->open);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}