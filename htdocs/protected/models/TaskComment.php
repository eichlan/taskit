<?php

define('TCT_COMMENT',	1 );
define('TCT_LOG',		2 );

/**
 * This is the model class for table "task_comment".
 *
 * The followings are the available columns in table 'task_comment':
 * @property integer $commentid
 * @property integer $authorid
 * @property integer $taskid
 * @property string $comment
 * @property string $created
 *
 * The followings are the available model relations:
 * @property User $author
 * @property Task $task
 */
class TaskComment extends CActiveRecord
{
	public static $types = array(
		TCT_COMMENT => 'Comment',
		TCT_LOG => 'Log',
	);
	/**
	 * Returns the static model of the specified AR class.
	 * @return TaskComment the static model class
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
		return 'task_comment';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('comment', 'required'),
			array('authorid, taskid', 'numerical', 'integerOnly'=>true),
			array('comment', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('commentid, authorid, taskid, comment, created', 'safe', 'on'=>'search'),
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
			'author' => array(self::BELONGS_TO, 'User', 'authorid'),
			'task' => array(self::BELONGS_TO, 'Task', 'taskid'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'commentid' => 'Commentid',
			'authorid' => 'Authorid',
			'taskid' => 'Taskid',
			'comment' => 'Comment',
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

		$criteria->compare('commentid',$this->commentid);
		$criteria->compare('authorid',$this->authorid);
		$criteria->compare('taskid',$this->taskid);
		$criteria->compare('comment',$this->comment,true);
		$criteria->compare('created',$this->created,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}
