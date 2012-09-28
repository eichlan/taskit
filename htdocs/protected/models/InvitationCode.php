<?php

/**
 * This is the model class for table "invitation_code".
 *
 * The followings are the available columns in table 'invitation_code':
 * @property string $code
 * @property boolean $enabled
 * @property integer $senderid
 * @property integer $recipientid
 * @property string $created
 *
 * The followings are the available model relations:
 * @property User $sender
 * @property User $recipient
 */
class InvitationCode extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return InvitationCode the static model class
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
		return 'invitation_code';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('code', 'required'),
//			array('senderid, recipientid', 'numerical', 'integerOnly'=>true),
			array('code', 'length', 'max'=>16),
			array('enabled', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('code, enabled, senderid, recipientid, created', 'safe', 'on'=>'search'),
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
			'sender' => array(self::BELONGS_TO, 'User', 'senderid'),
			'recipient' => array(self::BELONGS_TO, 'User', 'recipientid'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'code' => 'Code',
			'enabled' => 'Enabled',
			'senderid' => 'Sender',
			'recipientid' => 'Recipient',
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

		$criteria->compare('code',$this->code,true);
		$criteria->compare('enabled',$this->enabled);
		$criteria->compare('senderid',$this->senderid);
		$criteria->compare('recipientid',$this->recipientid);
		$criteria->compare('created',$this->created,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}
