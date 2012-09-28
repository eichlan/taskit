<?php

/**
 * This is the model class for table "user".
 *
 * The followings are the available columns in table 'user':
 * @property integer $userid
 * @property string $name
 * @property string $password
 * @property string $created
 * @property string $lastseen
 *
 * The followings are the available model relations:
 * @property Client[] $clients
 */
class User extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return User the static model class
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
		return 'user';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name', 'length', 'max'=>32),
			array('password', 'length', 'max'=>64),
			array('created, lastseen', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('userid, name, password, created, lastseen', 'safe', 'on'=>'search'),
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
			'teams' => array(self::MANY_MANY, 'Team', 'team_user(userid, teamid)', 'index'=>'teamid'),
			'ownedTeams' => array(self::HAS_MANY, 'Team', 'ownerid'),
			'taskComments' => array(self::HAS_MANY, 'TaskComment', 'authorid'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'userid' => 'Userid',
			'name' => 'Name',
			'password' => 'Password',
			'created' => 'Created',
			'lastseen' => 'Lastseen',
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

		$criteria->compare('userid',$this->userid);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('password',$this->password,true);
		$criteria->compare('created',$this->created,true);
		$criteria->compare('lastseen',$this->lastseen,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	public function checkPass( $pass )
	{
		return $this->password === User::crypt( $pass, $this->password );
	}

	public static function crypt( $pass, $salt=NULL )
	{
		if( $salt === NULL )
		{
			$from = './0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
			$salt = '$2a$08$';
			for( $j = 0; $j < 22; $j++ )
				$salt .= $from[rand(0,63)];

		}
		return crypt($pass, $salt );
	}

	public static function register( $username, $email, $password )
	{
		$model = new User;
		$model->name = $username;
		$model->setPassword( $password );
		$model->email = $email;
		$model->timezone = 'UTC';
		$model->save();
		
		if( !Yii::app()->authManager->isAssigned('user', $model->userid) )
			Yii::app()->authManager->assign('user', $model->userid);

		foreach( DefaultView::model()->findAll() as $dv )
		{
			$uv = new UserView;
			$uv->userid = $model->userid;
			$uv->section = $dv->section;
			$uv->sequence = $dv->sequence;
			$uv->name = $dv->name;
			$uv->definition = $dv->definition;
			$uv->save();
		}

		return $model;
	}

	public function setPassword( $pass )
	{
		$this->password = User::crypt( $pass );
	}

	public function dispName( $icon=true )
	{
		$ret = '';

		if( $icon )
			$ret .= Utility::avatarImg( $this->userid ).' ';

		if( $this->userid == Yii::app()->user->id )
			$ret .= 'you';
		else
			$ret .= $this->name;

		return $ret;
	}
}
