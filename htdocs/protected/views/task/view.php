<?php
if( strlen($model->name) >= 15 ) {
	$name = substr($model->name, 0, 12 ).'...';
} else {
	$name = $model->name;
}
$this->breadcrumbs= Utility::projectMenu( $model->project, false ) + array(
	$name,
);

global $priorities;
$priorities = $model->project->team->getPriorities();
?>
<script type="text/javascript">
function toggle( id )
{
	var el = $(id);
	if( el.css('display') == 'none' )
		el.css('display', 'inline');
	else
		el.css('display', 'none');
}
function editname()
{
	var el = $('#task_name');
	el.replaceWith('<form action="javascript:void(0)" onsubmit="savename();" id="task_name_wrap"><input maxlength="80" type="text" name="name" id="task_name" value="" onchange="savename();" onblur="resetname();" /></form>');
	$('#task_name').val(el.text());
	$('#task_name').focus();
}
function savename()
{
	var trg = '<?php echo Yii::app()->createUrl('task/setName', array(
		'id'=>$model->taskid, 'name'=>'XXX'
	)); ?>';
	$.ajax( trg.replace('XXX', encodeURIComponent($('#task_name').val())), {
		success: function(data,t,j){
			$('#task_name_wrap').replaceWith('<span id="task_name" onclick="editname();">'+data+'</span>');
			$.fn.yiiListView.update("Comments");
		}
	});
}
function resetname()
{
	$('#task_name_wrap').replaceWith('<span id="task_name" onclick="editname();">'+
		$('#task_name').val()+'</span>');
}
function editdesc()
{
	$.ajax('<?php echo Yii::app()->createUrl('task/getDesc', array(
		'id'=>$model->taskid)); ?>', {
		'success': function(data) {
			var el = $('#task_desc');
			el.replaceWith('<div class="task-body" id="task_desc"><div class="ta-hack2"><textarea class="task-body" id="task_desc_edit">'+data+'</textarea></div><div style="text-align: center;"><button onclick="resetdesc();" style="float: left;">Discard</button><a href="javascript:void(0);" onclick="popupFragment(\'markdown\');">Formatting Help</a><button onclick="savedesc();" style="float: right;">Save</button></div></div>');
			$('#task_desc_edit').focus();
		}
	});
}
function savedesc()
{
	$.ajax('<?php echo Yii::app()->createUrl('task/setDesc', array(
		'id'=>$model->taskid )); ?>', {
		type: 'POST',
		data: $('#task_desc_edit').val(),
		success: function(data,t,j){
			$('#task_desc').replaceWith('<div class="task-body" id="task_desc"><a class="linklike" onclick="editdesc();" style="float: right;"><img src="/images/edit.png" /></a>' + data + '</div>');
			$.fn.yiiListView.update("Comments");
		}
	});
}
function resetdesc()
{
	$.ajax('<?php echo Yii::app()->createUrl('task/getDesc', array(
		'id'=>$model->taskid, 'raw'=>'false' )); ?>', {
		success: function(data,t,j){
			$('#task_desc').replaceWith('<div class="task-body" id="task_desc"><a class="linklike" onclick="editdesc();" style="float: right;"><img src="/images/edit.png" /></a>' + data + '</div>');
		}
	});
}
function popUserPicker( teamid )
{
	$('#TeamAddUserButton').attr('teamid', teamid);
	$('#PickUserForm_name').val('');
	$("#user-add-dlg").dialog("open");
}
function ajaxPostAddUser()
{
	$.ajax({
		type: 'POST',
		url: '<?php
			echo Yii::app()->createUrl('task/assign', array(
				'id'=>$model->taskid,
			)); ?>',
		data: 'PickUserForm[name]='+
		$('input[name="PickUserForm[name]"]').val(),
		dataType: 'text',
		success: function(data,a,b){
			$("#user-add-dlg").dialog("close");
			if( data[0] == '!' )
				alert(data.replace("!",""));
			else
			{
				$('#assigntxt').html(data);
				$.fn.yiiListView.update("Comments");
			}
			return false;
		},
		
	});
	return false;
}
function commitMenu( nm )
{
	return function( data, t, j )
	{
		toggle("#"+nm+"_menu");
		$("#"+nm+"_text").html(data);
		$.fn.yiiListView.update("Comments");
	}
}
function setLogFilter( flt )
{
	$.ajax('<?php echo Yii::app()->createUrl('task/setLogFilter', array(
		'flt'=>'XXX' )); ?>'.replace('XXX', flt), {
		success: function(data,t,j){
			window.location.reload();
		}
	});
}
</script>
<div class="task-head">
<table>
	<tr>
		<td><ul class="popup-menu" id="complexity_menu"><?php
for( $j = 1; $j <= 4; $j++ )
{
	echo '<li>'.CHtml::ajaxLink($j,array('task/setComplexity', 'id'=>$model->taskid,'complexity'=>$j), array('success'=>'commitMenu("complexity")') ).'</li>';
}
?></ul>[<span id="complexity_text" onclick="toggle('#complexity_menu');"><?php echo $model->complexity===NULL?'?':$model->complexity; ?></span>] <ul class="popup-menu" id="type_menu"><?php
foreach( $model->project->team->teamTaskTypes as $type )
{
	echo '<li>'.CHtml::ajaxLink($type->name,array('task/setType','id'=>$model->taskid,'type'=>$type->typeid), array('success'=>'commitMenu("type")') ).'</li>';
}
?></ul><a id="type_text" onclick="toggle('#type_menu');"><?php echo $model->type->name; ?></a>: <span id="task_name" onclick="editname();"><?php echo htmlspecialchars($model->name); ?></span></td>
		<td align="right" style="vertical-align: top;"><a href="<?php echo Yii::app()->createUrl('project/view', array('id'=>$model->projectid)); ?>"><?php echo $model->project->name; ?> <img src="/images/return.png" /></a></td>
	</tr>
</table>
<table>
	<tr class="sub-heading">
		<td>Status: <ul class="popup-menu" id="status_menu"><?php
foreach( $model->project->team->teamTaskStatuses as $stat )
{
	echo '<li>'.CHtml::ajaxLink($stat->name,array('task/setStatus','id'=>$model->taskid,'status'=>$stat->statusid), array('success'=>'commitMenu("status")') ).'</li>';
}
?></ul><a id="status_text" onclick="toggle('#status_menu');"><?php echo $model->status->name; ?></a></td>
		<td align="right"><ul class="popup-menu" id="prio_menu"><?php
foreach( $model->project->team->getPriorities() as $p )
{
	echo '<li>'.CHtml::ajaxLink($p->name,array('task/setPriority','id'=>$model->taskid,'priority'=>$p->priority), array('success'=>'commitMenu("prio")') ).'</li>';
}
?></ul><a id="prio_text" onclick="toggle('#prio_menu');"><?php echo $priorities[$model->priority]->name; ?> priority</a></td>
	</tr>
</table>
</div>
<div class="task-body" id="task_desc"><a class="linklike" onclick="editdesc();" style="float: right;"><?php if( $model->description == '' ) echo 'There is no description, click here to set one ->'; ?><img src="/images/edit.png" /></a><?php
global $parse;
$parse = new CMarkdownParser;
echo $parse->safeTransform($model->description);
?></div>
<div class="task-attachments"><?php 
foreach( FileAttachment::model()->findAllByAttributes(array('taskid'=>$model->taskid)) as $fa )
{
	$fa->renderMini();
}
?></div>
<div class="task-foot">
<table>
	<tr class="sub-heading">
		<td><a class="linklike" onclick="$('#attach-file-dlg').dialog('open');"><img src="/images/attach.png" /></a><span title="<?php echo $model->created; ?>">Created by <?php
		echo $model->author->dispName();
		echo ' ';
		echo Utility::timeAgo($model->created); ?></span></td>
		<td align="right"><span id="assigntxt"><?php
if( count($model->assignedTo) == 0 )
{
	echo 'Not assigned to anyone.';
}
else
{
	echo 'Assigned to ';
	echo Utility::userList( $model->assignedTo );
}
?></span> <a id="add_user_assignment" onclick="popUserPicker();"><img src="/images/assign-user.png" /></a></td>
	</tr>
</table>
</div>


<div style="text-align: right; margin-bottom: 5px; margin-top: 8px;">
<a style="float: left;" href="<?php echo Yii::app()->createUrl('comment/create',array(
	'taskid' => $model->taskid,
));?>"><img src="/images/newcomment.png" /> Add comment</a>
Show: 
<?php $lfm = Yii::app()->session['logFilter']; ?>
<a class="cst-radio cst-radio-left<?php
	if( $lfm == 1 ) echo ' cst-radio-selected';
?>" href="javascript:void(0);" onclick="setLogFilter(1);">Discussion</a><a class="cst-radio<?php
	if( $lfm == 2 ) echo ' cst-radio-selected';
?>" href="javascript:void(0);" onclick="setLogFilter(2);">Activity Log</a><a class="cst-radio cst-radio-right<?php
	if( $lfm == 0 ) echo ' cst-radio-selected';
?>" href="javascript:void(0);" onclick="setLogFilter(0);">Everything</a>
</div>
<?php $this->widget('zii.widgets.CListView', array(
	'id' => 'Comments',
	'dataProvider' => $comments,
	'itemView'=>'_comment',
	'separator' => '<br />',
	'template'=>'{items}{pager}',
	'emptyText' => 'No comments yet.',
)); ?>

<?php

//
//  This is the popup dialog to add users
//

$this->beginWidget('zii.widgets.jui.CJuiDialog', array(
	'id'=>'user-add-dlg',
    'options'=>array(
        'title'=>'Assign Task to User',
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

	<?php echo $form->errorSummary($tmpModel); ?>

	<div class="row">
		<?php echo $form->labelEx($tmpModel,'name'); ?>
		<?php $this->widget('zii.widgets.jui.CJuiAutoComplete', array(
			'name'=>'PickUserForm[name]',
			'sourceUrl'=>Yii::app()->createUrl('team/userAutoComplete',
				array('teamid'=>$model->project->teamid )),
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

<?php

//
//  This is the popup dialog to upload files
//

$this->beginWidget('zii.widgets.jui.CJuiDialog', array(
	'id'=>'attach-file-dlg',
    'options'=>array(
        'title'=>'Upload a file to attach',
		'autoOpen'=>false,
		'modal'=>true,
    ),
)); ?>

<form method="post" enctype="multipart/form-data" action="<?php echo Yii::app()->createUrl('task/attachFile'); ?>">
<label for="upload-box">Upload file</label>
<?php echo CHtml::fileField('upload-box'); ?>
<br />
<label for="file-note">Description</label>
<textarea id="file-note" name="note"></textarea>
<input type="submit" value="Upload" />
<input type="hidden" name="taskid" value="<?php echo $model->taskid; ?>" />
</form>
<?php $this->endWidget('zii.widgets.jui.CJuiDialog'); ?>
