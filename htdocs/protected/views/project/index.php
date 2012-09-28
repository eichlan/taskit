<?php
Yii::app()->clientScript->registerCoreScript('jquery');
Yii::app()->clientScript->registerScriptFile( 
	"/js/jquery.jstree.js",
	CClientScript::POS_HEAD
); ?>

<div id="project-tree">
<ul>
<?php
function listProjects( $teamid, $parentid=NULL )
{
	$projects = Project::model()->findAllByAttributes(array(
		'parentid' => $parentid,
		'teamid' => $teamid,
	));
	foreach( $projects as $project )
	{
		echo '<li id="project-tree-p'.$project->projectid.
			'"><a href="'.Yii::app()->createUrl('/project/view', array(
			'id'=>$project->projectid)).'">' . $project->name . '</a><ul>';
		listProjects( $teamid, $project->projectid );
		echo '</ul></li>';
	}
}
foreach( Yii::app()->user->getModel()->teams as $team )
{
	echo '<li id="project-tree-t'.$team->teamid.'"><a href="#">'.
		$team->name . '</a><ul>';
	listProjects( $team->teamid );
	echo '</ul></li>';
}
?>
</ul>
</div>

<script type="text/javascript">
$('#project-tree').jstree({
	"plugins": ["themes", "html_data"],
	"core": {
		"initially_open": ['#project-tree-p1'],
	},
	"themes": {
		"url": "/jstree/style.css",
	}
});
</script>
