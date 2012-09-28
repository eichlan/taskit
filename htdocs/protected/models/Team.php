<?php

/**
 * This is the model class for table "team".
 *
 * The followings are the available columns in table 'team':
 * @property integer $teamid
 * @property string $name
 * @property string $description
 * @property integer $ownerid
 *
 * The followings are the available model relations:
 * @property User $owner
 * @property TeamPriority[] $teamPriorities
 * @property TeamTaskType[] $teamTaskTypes
 * @property User[] $users
 * @property Project[] $projects
 */
class Team extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return Team the static model class
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
		return 'team';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('ownerid', 'numerical', 'integerOnly'=>true),
			array('name', 'length', 'max'=>80),
			array('description', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('teamid, name, description, ownerid', 'safe', 'on'=>'search'),
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
			'owner' => array(self::BELONGS_TO, 'User', 'ownerid'),
			'teamPriorities' => array(self::HAS_MANY, 'TeamPriority', 'teamid'),
			'teamTaskTypes' => array(self::HAS_MANY, 'TeamTaskType', 'teamid'),
			'teamTaskStatuses' => array(self::HAS_MANY, 'TeamTaskStatus', 'teamid'),
			'users' => array(self::MANY_MANY, 'User', 'team_user(teamid, userid)'),
			'projects' => array(self::HAS_MANY, 'Project', 'teamid'),
		);
	}

	public function hasUserId( $id )
	{
		foreach( $this->users as $u )
		{
			if( $u->userid == $id )
				return true;
		}
		return false;
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'teamid' => 'Teamid',
			'name' => 'Name',
			'description' => 'Description',
			'ownerid' => 'Owner',
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

		$criteria->compare('teamid',$this->teamid);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('description',$this->description,true);
		$criteria->compare('ownerid',$this->ownerid);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Creates the basic things that all teams need.  They may be edited later.
	 */
	public function setupTeamTemplate()
	{
		$pri = new TeamPriority;
		$pri->teamid = $this->teamid;
		$pri->priority = 100;
		$pri->name = 'High';
		$pri->save();
		
		$pri = new TeamPriority;
		$pri->teamid = $this->teamid;
		$pri->priority = 0;
		$pri->name = 'Normal';
		$pri->save();
		
		$pri = new TeamPriority;
		$pri->teamid = $this->teamid;
		$pri->priority = -100;
		$pri->name = 'Low';
		$pri->save();

		$type = new TeamTaskType;
		$type->teamid = $this->teamid;
		$type->name = 'To Do';
		$type->save();

		$type = new TeamTaskType;
		$type->teamid = $this->teamid;
		$type->name = 'Feature Request';
		$type->save();

		$type = new TeamTaskType;
		$type->teamid = $this->teamid;
		$type->name = 'Bug Report';
		$type->save();

		$status = new TeamTaskStatus;
		$status->teamid = $this->teamid;
		$status->name = "Open";
		$status->open = true;
		$status->save();

		$status = new TeamTaskStatus;
		$status->teamid = $this->teamid;
		$status->name = "Completed";
		$status->open = false;
		$status->save();

		$status = new TeamTaskStatus;
		$status->teamid = $this->teamid;
		$status->name = "Rejected";
		$status->open = false;
		$status->save();

		$status = new TeamTaskStatus;
		$status->teamid = $this->teamid;
		$status->name = "By Design";
		$status->open = false;
		$status->save();

		$usr = new TeamUser;
		$usr->teamid = $this->teamid;
		$usr->userid = $this->ownerid;
		$usr->save();
	}

	private $priorities = NULL;
	public function getPriorities()
	{
		if( $this->priorities === NULL )
		{
			$raw = TeamPriority::model()->findAllByAttributes(array(
				'teamid' => $this->teamid,
			));

			foreach( $raw as $pri )
			{
				$this->priorities[$pri->priority] = $pri;
			}
		}

		return $this->priorities;
	}

	public function getProjectTree( $prefix='', $parent=NULL )
	{
		$projs = Project::model()->findAllByAttributes(array(
			'parentid' => $parent,
			'teamid' => $this->teamid,
		));

		$ret = array();
		foreach( $projs as $proj )
		{
			$name = $prefix . ' ' . $proj->name;
			$ret[$proj->projectid] = $name;
			$ret += $this->getProjectTree( $prefix.'--', $proj->projectid );
		}
		return $ret;
	}
}
