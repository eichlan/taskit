<?php

/**
 * This is the model class for table "user_view".
 *
 * The followings are the available columns in table 'user_view':
 * @property integer $viewid
 * @property integer $userid
 * @property boolean $dashboard
 * @property integer $sequence
 * @property string $name
 * @property string $definition
 *
 * The followings are the available model relations:
 * @property User $user
 */
class UserView extends CActiveRecord
{
	const secDashboard = 1;
	const secTeam = 2;
	const secProject = 3;

	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return UserView the static model class
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
		return 'user_view';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('userid, sequence', 'numerical', 'integerOnly'=>true),
			array('name', 'length', 'max'=>64),
			array('dashboard, definition', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('viewid, userid, dashboard, sequence, name, definition', 'safe', 'on'=>'search'),
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
			'user' => array(self::BELONGS_TO, 'User', 'userid'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'viewid' => 'Viewid',
			'userid' => 'Userid',
			'dashboard' => 'Dashboard',
			'sequence' => 'Sequence',
			'name' => 'Name',
			'definition' => 'Definition',
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

		$criteria->compare('viewid',$this->viewid);
		$criteria->compare('userid',$this->userid);
		$criteria->compare('dashboard',$this->dashboard);
		$criteria->compare('sequence',$this->sequence);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('definition',$this->definition,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}
