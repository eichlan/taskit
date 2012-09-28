<?php
$this->breadcrumbs = array(
	'Invitations',
);

$this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'invite-grid',
	'dataProvider'=>$dataProvider,
	'columns' => array(
		'code',
		array(
			'name'=>'enabled',
			'header'=>'Available',
			'value'=>'$data->enabled?"Yes":"No"',
		),
		array(
			'name'=>'senderid',
			'value'=>'User::model()->findByPk($data->senderid)->dispName()',
			'type'=>'raw',
		),
		array(
			'name'=>'recipientid',
			'value'=>'$data->recipientid===null?"":User::model()->findByPk($data->recipientid)->dispName()',
			'type'=>'raw',
		),
		array(
			'name'=>'created',
			'value'=>'Utility::timeAgo($data->created)',
		),
	),
));
?>
<script type="text/javascript">
function newInv()
{
	$.ajax('<?php echo Yii::app()->createUrl('settings/newInvite'); ?>', {
		dataType: 'json',
		success: function( data ) {
			if( 'error' in data ) {
				alert( data['error'] );
				return;
			}
			$('#codes-left').text( data['count'] );
			$.fn.yiiGridView.update('invite-grid');
			alert('Your new invitation code is: ' + data['code'] +
				"\nSomeone else can use this to create an account.");
		}
	});
}
</script>

You have <span id="codes-left"><?php echo $codesLeft; ?></span> invitations left this week.  <a href="javascript:void(0)" onclick="newInv();">Create a new invitation</a>
