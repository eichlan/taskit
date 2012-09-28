<?php

class ViewRenderer
{
	public $ctrl;
	public $sec;
	public $page;
	public $route;
	public $params;
	public $data;

	public $project = NULL;
	public $team = NULL;
	public $extSpec = array();
	public $viewDesc;
	public $view;
	public $eid = '';

	public static function getTemplate( $name )
	{
		Yii::autoload(Yii::getPathOfAlias(
			'application.taskit.templates.userview.' . $name
		));
		return new $name;
	}

	public function __construct( $ctrl, $sec, $page, $route, $params,
		$data=array() )
	{
		$this->ctrl = $ctrl;
		$this->sec = $sec;
		$this->page = $page;
		$this->route = $route;
		$this->params = $params;
		$this->data = $data;

		if( isset($data['project']) )
		{
			$this->extSpec['projectid'] = $data['project']->projectid;
			$this->eid = $data['project']->projectid;
		}
	}

	public function render()
	{
		unset( Yii::app()->session['views'] );
		if( !isset( Yii::app()->session['views'] ) )
		{
			// TODO: Move this into a helper function
			$vRaw = UserView::model()->findAll(array(
				'condition'=>'userid=:userid',
				'order'=>'sequence ASC',
				'params'=>array(
					'userid'=>Yii::app()->user->id,
				),
			));

			$views = array();
			foreach( $vRaw as $v )
			{
				$views[$v->section][] = $v->attributes;
			}
			Yii::app()->session['views'] = $views;
		}
		else
		{
			$views = Yii::app()->session['views'];
		}

		$views = $views[$this->sec];

		if( isset( $views[$this->page] ) )
		{
			$this->view = $views[$this->page];
		}
		else
		{
			$this->view = current( $views );
		}

		$this->viewDesc = json_decode( $this->view['definition'], true );

		$this->ctrl->render('//shared/userViews', $this->data + array(
			'views'=>$views,
			'renderer' => $this,
		));		
	}

	public function renderTemplate()
	{
		$templ = ViewRenderer::getTemplate( $this->viewDesc['layout'] );
		$templ->render( $this );
	}

	private $widgetId = 0;

	function doSection( $secId )
	{
		foreach( $this->viewDesc['template'][$secId] as $widSpec )
		{ ?>
<div class="widget-box" id="widget-id-<?php echo $this->widgetId; ?>">
<div class="widget-title"><?php echo $widSpec['params']['title'];
?><a href="<?php
echo Yii::app()->createUrl('widget/edit', array(
	'vid'=>$this->view['viewid'], 'wid'=>$this->widgetId, 'oid'=>$this->page, 'eid'=>$this->eid));
?>"><img src="/images/widget-config.png" style="float: right;" title="Edit widget settings" /></a><a href="javascript:void(0)" onclick="delWid('<?php
echo Yii::app()->createUrl('widget/delete', array(
	'vid'=>$this->view['viewid'], 'wid'=>$this->widgetId, 'oid'=>$this->page, 'eid'=>$this->eid));
?>');"><img src="/images/delete.png" style="float: right;" title="Delete widget" /></a></div>
<div class="widget-container"><?php
			$this->ctrl->widget('application.taskit.widgets.'.$widSpec['widget'],
				$widSpec['params'] + $this->extSpec );
			echo '</div></div>';
			$this->widgetId++;
		}
	}
}
