<?php

class WidgetController extends Controller
{
	public $layout='//layouts/column1';

	/**
	 * @return array action filters
	 */
	public function filters()
	{
		return array(
			'accessControl', // perform access control for CRUD operations
		);
	}

	/**
	 * Specifies the access control rules.
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	 */
	public function accessRules()
	{
		return array(
			array('allow',
				'actions'=>array('edit','save','delete','updateLayout','add',
					'addTab', 'updateTabLayout'),
				'users'=>array('@'),
			),
			array('deny', 
				'users'=>array('*'),
			),
		);
	}
	
	private function findWidget( $dat, $wid )
	{
		$secOrd = ViewRenderer::getTemplate( $dat['layout'] )->getOrder();

		$widgetId = 0;
		foreach( $secOrd as $sec )
		{
			foreach( $dat['template'][$sec] as $k => $wdg )
			{
				if( $widgetId == $wid )
				{
					return array( $sec, $k );
				}
				$widgetId++;
			}
		}

		return NULL;
	}

	private function getWidget( $dat, $wid )
	{
		list($sec, $idx) = $this->findWidget( $dat, $wid );
		return $dat['template'][$sec][$idx];
	}
	
	private function replaceWidget( $dat, $wid, $new )
	{
		list($sec, $idx) = $this->findWidget( $dat, $wid );
		$dat['template'][$sec][$idx] = $new;
		return $dat;
	}
	
	private function deleteWidget( $dat, $wid )
	{
		list($sec, $idx) = $this->findWidget( $dat, $wid );
		unset( $dat['template'][$sec][$idx] );
		return $dat;
	}

	private function updateLayout( $dat, $newOrd )
	{
		$secOrd = ViewRenderer::getTemplate( $dat['layout'] )->getOrder();

		$widById = array();
		$widgetId = 0;
		foreach( $secOrd as $sec )
		{
			foreach( $dat['template'][$sec] as $wdg )
			{
				$widById[$widgetId] = $wdg;
				$widgetId++;
			}
		}

		$newLayout = array();
		foreach( $newOrd as $sec => $ord )
		{
			$newLayout[$sec] = array();
			foreach( $ord as $id )
			{
				$newLayout[$sec][] = $widById[$id];
			}
		}

		$dat['template'] = $newLayout;

		return $dat;
	}

	public function actionEdit( $vid, $wid, $oid, $eid=null )
	{
		$model = UserView::model()->findByPk( (int)$vid );

		switch( $model->section )
		{
			case UserView::secDashboard:
				$area = 'dashboard';
				break;

			case UserView::secTeam:
				$area = 'team';
				break;

			case UserView::secProject:
				$area = 'project';
				break;
		}

		$dat = json_decode( $model->definition, true );

		$this->render('edit', array(
			'desc' => $this->getWidget( $dat, (int)$wid ),
			'vid' => $vid,
			'wid' => $wid,
			'oid' => $oid,
			'eid' => $eid,
			'viewTitle' => $model->name,
			'viewArea' => $area,
		));
	}

	public function actionSave( $vid, $wid, $oid, $eid=null )
	{
		$model = UserView::model()->findByPk( (int)$vid );

		$dat = json_decode( $model->definition, true );

		$json = json_decode( file_get_contents("php://input"), true );
		$newtxt = json_encode($this->replaceWidget( $dat, (int)$wid, $json ) );
		$model->definition = $newtxt;
		$model->save();

		switch( $model->section )
		{
		case UserView::secDashboard:
			echo Yii::app()->createUrl('dashboard/index', array('page'=>$oid));
			break;

		case UserView::secProject:
			echo Yii::app()->createUrl('project/view', array('id'=>(int)$eid,
				'page'=>$oid));
			break;

		case UserView::secTeam:
			break;
		}
	}
	
	public function actionDelete( $vid, $wid, $oid, $eid=null )
	{
		$model = UserView::model()->findByPk( (int)$vid );

		$dat = json_decode( $model->definition, true );

		$newtxt = json_encode($this->deleteWidget( $dat, (int)$wid ) );
		$model->definition = $newtxt;
		$model->save();

		switch( $model->section )
		{
		case UserView::secDashboard:
			$this->redirect(array('dashboard/index', 'page'=>$oid));
			break;

		case UserView::secProject:
			$this->redirect(array('project/view', 'id'=>(int)$eid,
				'page'=>$oid));
			break;

		case UserView::secTeam:
			break;
		}
	}

	public function actionUpdateLayout( $vid, $oid, $eid=null )
	{
		$model = UserView::model()->findByPk( (int)$vid );

		$dat = json_decode( $model->definition, true );

		$newOrd = json_decode( file_get_contents("php://input"), true );
		$newtxt = json_encode($this->updateLayout( $dat, $newOrd ) );
		$model->definition = $newtxt;
		$model->save();

		switch( $model->section )
		{
		case UserView::secDashboard:
			echo Yii::app()->createUrl('dashboard/index', array('page'=>$oid));
			break;

		case UserView::secProject:
			echo Yii::app()->createUrl('project/view', array('id'=>(int)$eid,
				'page'=>$oid));
			break;

		case UserView::secTeam:
			break;
		}
	}
	
	public function actionUpdateTabLayout( $sid )
	{
		$newOrd = json_decode( file_get_contents("php://input"), true );

		$model = UserView::model()->findAll(array(
			'condition' => 'userid=:uid AND section=:sid',
			'params' => array(
				'uid' => Yii::app()->user->id,
				'sid' => (int)$sid,
			),
			'order' => 'sequence ASC',
		));

		foreach( $newOrd as $k => $id )
		{
			if( $id < 0 || $id >= count($model) )
				continue;
			$model[$id]->sequence = $k;
			$model[$id]->save();
		}
	}
	
	public function actionAdd( $vid, $class )
	{
		$model = UserView::model()->findByPk( (int)$vid );

		$dat = json_decode( $model->definition, true );
		
		$secOrd = ViewRenderer::getTemplate( $dat['layout'] )->getOrder();
		array_unshift( $dat['template'][$secOrd[0]], array(
			'widget' => $class,
			'params' => array(
				'title' => 'Unnamed ' . $class,
			),
		));

		$newtxt = json_encode( $dat );
		$model->definition = $newtxt;
		$model->save();
	}
	
	public function actionAddTab( $sec )
	{
		$r = Yii::app()->db->createCommand('SELECT max(sequence)+1 AS seq FROM user_view WHERE userid=:uid AND section=:sec')->query(array('uid'=>Yii::app()->user->id,'sec'=>(int)$sec))->readAll();
		$model = new UserView;
		$model->userid = Yii::app()->user->id;
		$model->section = (int)$sec;
		$model->sequence = $r[0]['seq'];
		$model->name = 'New Tab';
		$model->definition = '{"layout":"TwoColumn","template":{"head":[],"col0":[],"col1":[],"foot":[]}}';
		$model->save();
	}
}
