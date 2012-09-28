<?php

/**
 * This is the model class for table "milestone".
 *
 * The followings are the available columns in table 'milestone':
 * @property integer $milestoneid
 * @property integer $projectid
 * @property string $name
 * @property string $description
 * @property string $created
 * @property string $goal
 *
 * The followings are the available model relations:
 * @property Task[] $tasks
 * @property Project $project
 */
class Milestone extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return Milestone the static model class
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
		return 'milestone';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('created', 'required'),
			array('projectid', 'numerical', 'integerOnly'=>true),
			array('name', 'length', 'max'=>80),
			array('description, goal', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('milestoneid, projectid, name, description, created, goal', 'safe', 'on'=>'search'),
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
			'tasks' => array(self::HAS_MANY, 'Task', 'milestoneid'),
			'project' => array(self::BELONGS_TO, 'Project', 'projectid'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'milestoneid' => 'Milestoneid',
			'projectid' => 'Projectid',
			'name' => 'Name',
			'description' => 'Description',
			'created' => 'Created',
			'goal' => 'Goal',
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

		$criteria->compare('milestoneid',$this->milestoneid);
		$criteria->compare('projectid',$this->projectid);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('description',$this->description,true);
		$criteria->compare('created',$this->created,true);
		$criteria->compare('goal',$this->goal,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}