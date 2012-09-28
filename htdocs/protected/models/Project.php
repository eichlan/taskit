<?php

/**
 * This is the model class for table "project".
 *
 * The followings are the available columns in table 'project':
 * @property integer $projectid
 * @property integer $teamid
 * @property integer $parentid
 * @property string $name
 * @property string $description
 * @property string $created
 *
 * The followings are the available model relations:
 * @property Team $team
 * @property Project $parent
 * @property Project[] $projects
 * @property FileAttachment[] $fileAttachments
 * @property Task[] $tasks
 * @property Milestone[] $milestones
 */
class Project extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return Project the static model class
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
		return 'project';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('teamid', 'required'),
			array('teamid, parentid', 'numerical', 'integerOnly'=>true),
			array('name', 'length', 'max'=>80),
			array('description', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('projectid, teamid, parentid, name, description, created', 'safe', 'on'=>'search'),
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
			'parent' => array(self::BELONGS_TO, 'Project', 'parentid'),
			'children' => array(self::HAS_MANY, 'Project', 'parentid'),
			'fileAttachments' => array(self::HAS_MANY, 'FileAttachment', 'projectid'),
			'tasks' => array(self::HAS_MANY, 'Task', 'projectid'),
			'milestones' => array(self::HAS_MANY, 'Milestone', 'projectid'),
		);
	}

	public function hasUserId( $id )
	{
		return $this->team->hasUserId( $id );
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'projectid' => 'Project',
			'teamid' => 'Team',
			'parentid' => 'Parent Project',
			'name' => 'Name',
			'description' => 'Description',
			'created' => 'Created',
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

		$criteria->compare('projectid',$this->projectid);
		$criteria->compare('teamid',$this->teamid);
		$criteria->compare('parentid',$this->parentid);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('description',$this->description,true);
		$criteria->compare('created',$this->created,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}
