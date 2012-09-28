<div class="comment-body">
<?php if( $data->authorid == Yii::app()->user->id ) {
?><a href="<?php echo Yii::app()->createUrl('comment/update', array('id'=>$data->commentid)); ?>"><img src="/images/edit.png" style="float: right;" /></a>
<?php } ?>
	<?php global $parse; echo $parse->safeTransform( $data->comment ); ?>
<?php
$fas = FileAttachment::model()->findAllByAttributes(array('commentid'=>$data->commentid));
if( count($fas) > 0 )
{
	foreach( $fas as $fa )
	{
		$fa->renderMini();
	}
}
?>
</div>
<div class="comment-foot">
	Posted by <?php
	echo $data->author->dispName().' '.Utility::timeAgo($data->created); ?>
</div>
