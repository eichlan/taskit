<?php

/**
 * This is the model class for table "file_attachment".
 *
 * The followings are the available columns in table 'file_attachment':
 * @property string $fileid
 * @property integer $authorid
 * @property integer $projectid
 * @property integer $taskid
 * @property string $filename
 * @property string $mimetype
 * @property integer $size
 * @property string $craeted
 *
 * The followings are the available model relations:
 * @property User $author
 * @property Project $project
 * @property Task $task
 */
class FileAttachment extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return FileAttachment the static model class
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
		return 'file_attachment';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('fileid', 'required'),
			array('authorid, projectid, taskid, size', 'numerical', 'integerOnly'=>true),
			array('filename, mimetype', 'length', 'max'=>1024),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('fileid, authorid, projectid, taskid, filename, mimetype, size, craeted', 'safe', 'on'=>'search'),
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
			'project' => array(self::BELONGS_TO, 'Project', 'projectid'),
			'task' => array(self::BELONGS_TO, 'Task', 'taskid'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'fileid' => 'Fileid',
			'authorid' => 'Authorid',
			'projectid' => 'Projectid',
			'taskid' => 'Taskid',
			'filename' => 'Filename',
			'mimetype' => 'Mimetype',
			'size' => 'Size',
			'craeted' => 'Craeted',
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

		$criteria->compare('fileid',$this->fileid,true);
		$criteria->compare('authorid',$this->authorid);
		$criteria->compare('projectid',$this->projectid);
		$criteria->compare('taskid',$this->taskid);
		$criteria->compare('filename',$this->filename,true);
		$criteria->compare('mimetype',$this->mimetype,true);
		$criteria->compare('size',$this->size);
		$criteria->compare('craeted',$this->craeted,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	public function renderMini()
	{
		$mime = $this->mimetype;
		$p = strpos($mime, ';');
		if( $p !== false )
			$mime = substr( $mime, 0, $p );
		$mimebits = explode('/', $mime );
		echo '<a href="'. Yii::app()->createUrl('file/index', array(
				'id' => $this->fileid, 'cat'=>'attachment' ))
			.'"><div class="file-mini-box">';
		switch( $mimebits[0] )
		{
		case 'image':
			echo '<img src="'. Yii::app()->createUrl('file/index', array(
				'id' => $this->fileid, 'cat'=>'attachment', 'size'=>'thumbnail'
			)).'" />';
			echo '<br /><span class="filename">'.$this->filename.
				'</span><br /><span style="font-size: 75%;">';
			echo Utility::bytesToStr($this->size);
			echo '</span>';
			break;

		default:
			echo '<span class="filename">'.$this->filename.'</span><br /><span style="font-size: 75%;">';
			echo Utility::bytesToStr($this->size);
			echo '<br />';
			echo $this->mimetype;
			echo '</span>';
			break;
		}
		echo '</div></a>';
	}
}
