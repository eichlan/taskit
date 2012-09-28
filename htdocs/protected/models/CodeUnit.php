<?php

/**
 * This is the model class for table "code_unit".
 *
 * The followings are the available columns in table 'code_unit':
 * @property integer $unitid
 * @property integer $projectid
 * @property string $name
 * @property string $type
 * @property string $file
 * @property string $parent
 * @property double $complete
 *
 * The followings are the available model relations:
 * @property Task[] $tasks
 * @property Project $project
 */
class CodeUnit extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return CodeUnit the static model class
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
		return 'code_unit';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('projectid', 'numerical', 'integerOnly'=>true),
			array('complete', 'numerical'),
			array('name, file, parent', 'length', 'max'=>128),
			array('type', 'length', 'max'=>32),
			array('ignore', 'boolean'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('unitid, projectid, name, type, file, parent, complete', 'safe', 'on'=>'search'),
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
			'tasks' => array(self::MANY_MANY, 'Task', 'code_unit_task(unitid, taskid)'),
			'project' => array(self::BELONGS_TO, 'Project', 'projectid'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'unitid' => 'Unitid',
			'projectid' => 'Projectid',
			'name' => 'Name',
			'type' => 'Type',
			'file' => 'File',
			'parent' => 'Parent',
			'complete' => 'Complete',
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

		$criteria->compare('unitid',$this->unitid);
		$criteria->compare('projectid',$this->projectid);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('type',$this->type,true);
		$criteria->compare('file',$this->file,true);
		$criteria->compare('parent',$this->parent,true);
		$criteria->compare('complete',$this->complete);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}
