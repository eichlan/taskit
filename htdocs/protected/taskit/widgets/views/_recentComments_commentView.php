<?php if( $index == 0 ) { ?>
<thead>
<tr><?php foreach( $this->cols as $col ) {
	echo '<th>'.$col.'</th>';
} ?></tr>
<tr><th colspan="<?php echo count($this->cols); ?>">Comment text</th></tr>
</thead>
<?php } ?>
<tr class="<?php echo ($index%2==0)?'even':'odd'; ?>">
<?php foreach( $this->cols as $col ) {
	switch( $col ) {
	case 'Team': ?>
<td><a href="<?php
	echo Yii::app()->createUrl('team/view', array('id'=>$data->task->teamid ));
?>"><?php echo $data->task->team->name; ?></a></td>
<?php break;

	case 'Project': ?>
<td><a href="<?php
	echo Yii::app()->createUrl('project/view', array('id'=>$data->task->projectid ));
?>"><?php echo $data->task->project->name; ?></a></td>
<?php break;

	case 'Task': ?>
<td><a href="<?php
	echo Yii::app()->createUrl('task/view', array('id'=>$data->taskid ));
?>"><?php echo $data->task->name; ?></a></td>
<?php break;

	case 'Author': ?>
<td><?php echo $data->author->dispName(); ?></td>
<?php break;
	}
} ?>
</tr>
<tr class="<?php echo ($index%2==0)?'even':'odd'; ?>"><td colspan="<?php
	echo count($this->cols);
?>" title="<?php echo htmlspecialchars($data->comment); ?>"><?php
	echo '<i>'.Utility::timeAgo($data->created) . '</i> &mdash; ';
	echo htmlspecialchars(Utility::clipStr($data->comment, $this->maxComment));
?></td></tr>
