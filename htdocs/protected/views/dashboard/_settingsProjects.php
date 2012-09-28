<?php
Yii::app()->clientScript->registerScript('projectChildren', "
function listChildren( id )
{
	$.fn.yiiGridView.update( 'Projects', {
		'url': '". Yii::app()->createUrl('dashboard/settings', array('parentid'=>'XXX')). "'.replace('XXX',id),
	});
}
function popupDlg( title, url )
{
	$.ajax( url, {
		success: function(data, a, b){ $('#general-dlg').html( data ); },
		dataType: 'html'
	});
	$('#ui-dialog-title-general-dlg').text( title );
	$('#general-dlg').dialog('open');
}
function popupNewProject( teamid, parentid )
{
	popupDlg( 'Create new Project',
		'". Yii::app()->createUrl('project/quickCreate', array('teamid'=>'XXX', 'parentid'=>'YYY')). "'.replace('XXX',teamid).replace('YYY',parentid)
	);
}
", CClientScript::POS_END );
if( $projects !== NULL )
{
	$template = '{summary}{items}{pager}';

	$pname = '[Root]';
	if( $parentProject !== NULL )
	{
		$template = 'Viewing '.$parentProject->name .
			' -- <a href="javascript:void(0);" onclick="listChildren('.
			$parentProject->parentid.');">[up one level]</a> ' . $template;
		$template .= '<a href="javascript:void(0);" onclick="popupNewProject('.
			$parentProject->teamid.', '.$parentProject->projectid.
			');">Create new project</a>';
	}
	else
	{
		$team = Team::model()->findByPk( $teamid );
		$template = 'Viewing root of team ' . $team->name . $template;
		$template .= '<a href="javascript:void(0);" onclick="popupNewProject('.
			$teamid.', \'\');">Create new project</a>';
	}
	$x = $this->widget('zii.widgets.grid.CGridView', array(
		'dataProvider' => $projects,
		'id' => 'Projects',
		'template' => $template,
		'columns'=>array(
			array(
				'header'=>'[X]',
				'value'=>'\'<a href="javascript:void(0);" onclick="listChildren(\'.$data->projectid.\');">open</a>\'',
				'type'=>'raw',
			),
			'name',
			'description',
		),
	));
}
?>
