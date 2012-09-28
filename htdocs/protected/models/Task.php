<?php

/**
 * This is the model class for table "task".
 *
 * The followings are the available columns in table 'task':
 * @property integer $taskid
 * @property integer $authorid
 * @property integer $projectid
 * @property integer $teamid
 * @property integer $milestoneid
 * @property integer $priority
 * @property integer $statusid
 * @property integer $typeid
 * @property integer $complexity
 * @property string $name
 * @property string $description
 * @property string $created
 * @property string $updated
 *
 * The followings are the available model relations:
 * @property User[] $users
 * @property TaskComment[] $taskComments
 * @property SubTask[] $subTasks
 * @property FileAttachment[] $fileAttachments
 * @property CodeUnit[] $codeUnits
 * @property User $author
 * @property Project $project
 * @property TeamPriority $team
 * @property Milestone $milestone
 * @property TeamTaskStatus $status
 * @property TeamTaskType $type
 * @property TeamPriority $priority0
 */
class Task extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Task the static model class
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
		return 'task';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
//			array('created', 'required'),
			array('authorid, projectid, teamid, milestoneid, priority, statusid, typeid, complexity', 'numerical', 'integerOnly'=>true),
			array('name', 'length', 'max'=>80),
			array('name', 'required'),
			array('description', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('taskid, authorid, projectid, teamid, milestoneid, priority, statusid, typeid, complexity, name, description, created, updated', 'safe', 'on'=>'search'),
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
			'users' => array(self::MANY_MANY, 'User', 'task_assignment(taskid, userid)'),
			'taskComments' => array(self::HAS_MANY, 'TaskComment', 'taskid'),
			'subTasks' => array(self::HAS_MANY, 'SubTask', 'taskid'),
			'fileAttachments' => array(self::HAS_MANY, 'FileAttachment', 'taskid'),
			'codeUnits' => array(self::MANY_MANY, 'CodeUnit', 'code_unit_task(taskid, unitid)'),
			'author' => array(self::BELONGS_TO, 'User', 'authorid'),
			'project' => array(self::BELONGS_TO, 'Project', 'projectid'),
			'team' => array(self::BELONGS_TO, 'Team', 'teamid'),
			'milestone' => array(self::BELONGS_TO, 'Milestone', 'milestoneid'),
			'status' => array(self::BELONGS_TO, 'TeamTaskStatus', 'statusid'),
			'type' => array(self::BELONGS_TO, 'TeamTaskType', 'typeid'),
			'priorityName' => array(self::BELONGS_TO, 'TeamPriority', 'priority'),
			'assignedTo' => array(self::MANY_MANY, 'User', 'task_assignment(taskid,userid)'),
			'teamUser' => array(self::HAS_MANY, 'TeamUser', array('teamid'=>'teamid')),
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
			'taskid' => 'Task',
			'authorid' => 'Authorid',
			'projectid' => 'Project',
			'teamid' => 'Teamid',
			'milestoneid' => 'Milestone',
			'priority' => 'Priority',
			'statusid' => 'Status',
			'typeid' => 'Type',
			'complexity' => 'Complexity',
			'name' => 'Name',
			'description' => 'Description',
			'created' => 'Created',
			'updated' => 'Updated',
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

		$criteria->compare('taskid',$this->taskid);
		$criteria->compare('authorid',$this->authorid);
		$criteria->compare('projectid',$this->projectid);
		$criteria->compare('teamid',$this->teamid);
		$criteria->compare('milestoneid',$this->milestoneid);
		$criteria->compare('priority',$this->priority);
		$criteria->compare('statusid',$this->statusid);
		$criteria->compare('typeid',$this->typeid);
		$criteria->compare('complexity',$this->complexity);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('description',$this->description,true);
		$criteria->compare('created',$this->created,true);
		$criteria->compare('updated',$this->updated,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	public function log( $msg )
	{
		$cmnt = new TaskComment;
		$cmnt->authorid = Yii::app()->user->id;
		$cmnt->taskid = $this->taskid;
		$cmnt->comment = $msg;
		$cmnt->type = TCT_LOG;
		$cmnt->save();

		return $cmnt->commentid;
	}
}
