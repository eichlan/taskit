<?php
$this->breadcrumbs = array(
	'Teams & Projects',
);

$this->widget('zii.widgets.jui.CJuiDialog', array(
	'id'=>'general-dlg',
    'options'=>array(
        'title'=>'',
		'autoOpen'=>false,
		'modal'=>true,
    ),
));

Yii::app()->clientScript->registerScript('projectChildren', "
curTeamId = null;
curParentId = null;
function reloadPanel( id )
{
	var url = '". Yii::app()->createUrl('settings/tptreeDetails',
		array('id'=>'XXX'))."'.replace('XXX', id);
	$.ajax(url, {
		'success': function(data) {
			$('#stng-panel').html(data)
		},
		'error': function(data) {
			alert( 'error: '+data );
		}
	});
}
function reloadCurPanel()
{
	if( curParentId == null )
	{
		reloadPanel( 't' + curTeamId );
	}
	else
	{
		reloadPanel( 'p' + curParentId );
	}
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
function popupNewProject()
{
	if( curTeamId == null )
	{
		alert('You must select a team or project before you can create a project.');
		return;
	}

	popupDlg( 'Create new Project',
		'". Yii::app()->createUrl('project/quickCreate', array('teamid'=>'XXX', 'parentid'=>'YYY')). "'.replace('XXX',curTeamId).replace('YYY',curParentId)
	);
}

function popupNewTeam()
{
	popupDlg( 'Create new Team',
		'". Yii::app()->createUrl('team/quickCreate')."');
}

function delSel()
{
	if( curParentId != null )
	{
		if( confirm('You are going to delete the selected project and all projects and tasks within it.  Are you sure you want to do this?') )
		{
			$.ajax('". Yii::app()->createUrl('project/delete', array(
				'id'=>'XXX')). "'.
				replace('XXX',curParentId), {
					success: function() {
						$('#stng-panel').html('');
						$('#project-stng-tree').jstree('delete_node',
							'#stng-project-tree-p'+curParentId);
					}
			});
		}
	}
	else if( curTeamId != null )
	{
		if( confirm('You are going to delete the selected team and all projects and tasks within it.  Are you sure you want to do this?') )
		{
			$.ajax('". Yii::app()->createUrl('team/delete', array(
				'id'=>'XXX')). "'.
				replace('XXX',curTeamId), {
					success: function() {
						$('#stng-panel').html('');
						$('#project-stng-tree').jstree('delete_node',
							'#stng-project-tree-t'+curTeamId);
					}
			});
		}
	}
	else
	{
		alert('To delete a project or team please select one first.');
	}
}
", CClientScript::POS_END );

?>

<?php $this->beginWidget('zii.widgets.jui.CJuiDialog', array(
	'id'=>'user-add-dlg',
    'options'=>array(
        'title'=>'Add User to Team',
		'autoOpen'=>false,
		'modal'=>true,
    ),
)); ?>

<div class="form">

<?php 

$tmpModel = new PickUserForm;

$form=$this->beginWidget('CActiveForm', array(
	'id'=>'pick-user-form',
	'enableAjaxValidation'=>true,
	'action'=>'#',
	'htmlOptions'=>array(
		'onsubmit'=>"ajaxPostAddUser(); return false;",
	),
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($tmpModel); ?>

	<div class="row">
		<?php echo $form->labelEx($tmpModel,'name'); ?>
		<?php $this->widget('zii.widgets.jui.CJuiAutoComplete', array(
			'name'=>'PickUserForm[name]',
			'sourceUrl'=>Yii::app()->createUrl('team/userAutoComplete'),
			'options'=>array(
				'minLength' => 1,
			),
		)); ?>
		<?php echo $form->error($tmpModel,'name'); ?>
	</div>

<?php $this->endWidget(); ?>

	<div class="row buttons">
		<button id="TeamAddUserButton" onclick="ajaxPostAddUser(); return false;">Add</button>
	</div>
</div>
<?php $this->endWidget('zii.widgets.jui.CJuiDialog'); ?>



<button onclick="popupNewTeam();">Create a new team</button>
<button onclick="popupNewProject();">Create a new project</button>
<button onclick="delSel();">Delete</button>
<table width="100%">
<tr>
<td style="min-width: 30%; vertical-align: top;">
<?php
if( count(Yii::app()->user->getModel()->teams) == 0 )
{
	echo '<br /><br />';
	echo 'In order to start creating tasks and projects you must first be a member of a Team.  Either click the "Create a new team" link above or have someone else add you to one of their teams.';
}
?>
<div id="project-stng-tree">
<?php $openId = Utility::genTeamTree('stng-'); ?>
</div>
<script type="text/javascript">
$('#project-stng-tree').jstree({
	"plugins": ["themes", "html_data", "types", "ui", "dnd"],
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
		'valid_children': ['team'],
		'types': {
			'team': {
				'icon': {
					'image': '/images/team.png'
				},
				'valid_children': ['default']
			},
			'default': {
				'valid_children': ['default']
			}
		}
	},
	'ui': {
<?php if( $selid !== null ) { ?>
		'initially_select': '#stng-project-tree-<?php echo $selid; ?>',
<?php } ?>
		'select_limit': 1
	},
}).bind("select_node.jstree", function (event, data) {
	reloadPanel( data.rslt.obj.attr('id').substring( 18 ) );
}).bind('move_node.jstree', function( event, data ) {
	$.ajax('<?php echo Yii::app()->createUrl('project/reparent', array(
		'id'=>'XXX', 'newParent'=>'YYY')); ?>'.
		replace('XXX', data.rslt.o.attr('id').substring( 19 )).
		replace('YYY', data.rslt.r.attr('id').substring( 18 ) ));
});
</script>
</td>
<td style="width: 5px; background: grey;"></td>
<td id="stng-panel" style="vertical-align: top;">
</td>
</tr>
</table>
