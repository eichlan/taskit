<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name="language" content="en" />

		<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/main.css" />
		<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/form.css" />

		<title><?php echo CHtml::encode($this->pageTitle); ?></title>
	</head>
	<body>
		<div class="top-menu">
			<?php if(isset($this->breadcrumbs)): if( count($this->breadcrumbs) == 0 ) echo 'Dashboard';?>
				<?php $this->widget('zii.widgets.CBreadcrumbs', array(
					'links'=>$this->breadcrumbs,
					'homeLink'=>Yii::app()->user->isGuest?false:'<a href="'.Yii::app()->homeUrl.'">Dashboard</a>',
				)); ?><!-- breadcrumbs -->
			<?php else: ?> &nbsp;
			<?php endif?>
			<div class="virt-line">&nbsp;</div>
			<ul>
			<?php if( Yii::app()->user->isGuest ) { ?>
			  <li><a href="<?php echo Yii::app()->createUrl('/site/register');
					?>">Register</a></li>
			  <li><a href="<?php echo Yii::app()->createUrl('/site/login');
					?>">Login</a></li>
			<?php } else { ?>
			  <li><a href="#" onclick="$('#project-tree-box').toggle('slide');"><img src="/images/proj-tree.png" alt="Projects" title="Projects" /></a></li>
			  <li><a href="<?php echo Yii::app()->createUrl('/dashboard/index'); ?>"><img src="/images/dashboard.png" alt="Dashboard" title="Dashboard" /></a></li>
			  <li><a href="<?php echo Yii::app()->createUrl('/settings/structure'); ?>"><img src="/images/settings.png" alt="Settings" title="Settings" /></a></li>
			  <li><a href="<?php echo Yii::app()->createUrl('/site/logout'); ?>"><img src="/images/logout.png" alt="Logout" title="Logout" /></a></li>
			<?php } ?>
			</ul>
		</div>
		<?php if( !Yii::app()->user->isGuest ) { ?>
		<div class="hello-box"><a href="<?php echo Yii::app()->createUrl('settings/profile'); ?>"><img src="<?php echo Yii::app()->createUrl('file/index', array('cat'=>'avatar','id'=>Yii::app()->user->id, 'size'=>'miniIcon')); ?>" /> <?php echo Yii::app()->user->name; ?></a></div>
		<?php } ?>
		<div class="clearbar">&nbsp;</div>

		<div id="content">
<?php
global $selId;
global $openId;
$selId = NULL;
$openId = NULL;
			if( !Yii::app()->user->isGuest ) {
Yii::app()->clientScript->registerCoreScript('jquery');
Yii::app()->clientScript->registerCoreScript('jquery.ui');
Yii::app()->clientScript->registerScriptFile( 
	"/js/jquery.jstree.js",
	CClientScript::POS_HEAD
); ?>

<div id="project-tree-box">
<strong>Select a Team or Project below</strong>
<div id="project-tree">
<ul>
<?php
if( $this->activeProjectId !== NULL )
{
	$selId = 'project-tree-p'.$this->activeProjectId;
}
else if( $this->activeTeamId !== NULL )
{
	$selId = 'project-tree-t'.$this->activeTeamId;
}
function listProjects( $teamid, $parentid=NULL )
{
	global $selId;
	global $openId;
	$projects = Project::model()->findAllByAttributes(array(
		'parentid' => $parentid,
		'teamid' => $teamid,
	));
	foreach( $projects as $project )
	{
		$id = 'project-tree-p'.$project->projectid;
		if( $selId == $id )
		{
			if( $project->parentid === NULL )
				$openId = 'project-tree-t'.$project->teamid;
			else
				$openId = 'project-tree-p'.$project->parentid;
			echo '<li class="active-leaf" ';
		}
		else
			echo '<li ';
		echo 'id="'.$id.'"><a href="'.
			Yii::app()->createUrl('/project/view', array(
			'id'=>$project->projectid)).'">' . $project->name . '</a><ul>';
		listProjects( $teamid, $project->projectid );
		echo '</ul></li>';
	}
}
foreach( Yii::app()->user->getModel()->teams as $team )
{
	$id = 'project-tree-t'.$team->teamid;
	if( $selId == $id )
		echo '<li class="active-leaf" ';
	else
		echo '<li ';
	echo 'rel="team" id="project-tree-t'.$team->teamid.'"><a href="'.
		Yii::app()->createUrl('/team/view', array('id'=>$team->teamid)).'">'.
		$team->name . '</a><ul>';
	listProjects( $team->teamid );
	echo '</ul></li>';
}
?>
</ul>
</div>
<script type="text/javascript">
function popupFragment( type )
{
	$.ajax('<?php echo Yii::app()->createUrl('site/fragment', array('id'=>'XXX')); ?>'.replace('XXX', type), {success: function( d ) { $('body').append( d ); }});
}
$('#project-tree').jstree({
	"plugins": ["themes", "html_data", "types"],
	"core": {
<?php
if( $openId !== NULL )
{
	echo "'initially_open': ['#".$openId."'],";
}

?>
	},
	"themes": {
		"url": "/jstree/style.css",
	},
	'types': {
		'types': {
			'team': {
				'icon': {
					'image': '/images/team.png'
				}
			}
		}
	}
});
</script>
</div>
<?php } ?>
		<?php echo $content; ?>
		</div>
		<div class="clearbar"></div>
		
		<div id="footer">
			Taskit <?php echo @file_get_contents('protected/version'); ?><br />
			Copyright &copy; <?php echo date('Y'); ?> by Xagasoft, LLC.<br/>
			All Rights Reserved.<br/>
		</div><!-- footer -->
	</body>
</html>
