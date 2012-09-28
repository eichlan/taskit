<?php
$this->breadcrumbs = array(
	'Settings',
);
?>
<h1>Settings!</h1>

<?php $this->widget('CTabView', array(
	'tabs' => array(
		'profile' => array(
			'title' => 'Profile',
			'view' => '_settingsProfile',
			'data' => array(
			),
		),
		'teams' => array(
			'title' => 'Teams',
			'view' => '_settingsTeams',
			'data' => array(
				'teams' => $teams,
			),
		),
		'projects' => array(
			'title' => 'Projects',
			'view' => '_settingsProjects',
			'data' => array(
				'teamid' => $teamid,
				'projects' => $projects,
				'parentProject' => $parentProject,
			),
		),
		'views' => array(
			'title' => 'Views',
			'view' => '_settingsViews',
		),
	),
)); ?>

<?php $this->beginWidget('zii.widgets.jui.CJuiDialog', array(
	'id'=>'general-dlg',
    'options'=>array(
        'title'=>'',
		'autoOpen'=>false,
		'modal'=>true,
    ),
));

$this->endWidget('zii.widgets.jui.CJuiDialog'); ?>

