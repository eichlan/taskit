<?php Yii::app()->clientScript->registerScriptFile( 
	"/js/jquery.json-2.3.min.js",
	CClientScript::POS_HEAD
); ?>
<div class="form">
	<div class="row">
		<label for="title">Title</label>
		<div class="row-right">
			<input type="text" id="title" value="<?php echo htmlspecialchars($this->title); ?>" />
		</div>
	</div>
	
	<div class="row">
		<label for="mode">Filter</label>
		<div class="row-right">
			<select id="mode">
				<option value="assignedTo">Assigned To Me</option>
				<option value="createdBy">Created By Me</option>
				<option value="unassigned">Unassigned</option>
				<option value="all">All</option>
			</select>
		</div>
	</div>
	
	<div class="row">
		<label for="open">Status</label>
		<div class="row-right">
			<select id="open">
				<option value="true">Open</option>
				<option value="false">Closed</option>
				<option value="null">All</option>
			</select>
		</div>
	</div>
	
	<div class="row">
		<label for="title">Items per page</label>
		<div class="row-right">
			<input type="text" id="itemsPerPage" value="<?php echo $this->itemsPerPage; ?>" />
		</div>
	</div>

	<div class="row">
		<label for="columns">Columns</label>
		<div class="row-right">
<div id="widgetColumns">
<ul>
<?php
$cols = array(
	'team' => 'Team',
	'project' => 'Project',
	'name' => 'Name',
	'author' => 'Author',
	'assign' => 'Assigned To',
	'cplx' => 'Complexity',
	'prio' => 'Priority',
	'status' => 'Status',
	'type' => 'Type',
	'created' => 'Created Time',
);

foreach( $this->cols as $id )
{
	echo '<li id="'.$id.'" class="jstree-checked"><a href="#">'.$cols[$id].'</a></li>';
	unset( $cols[$id] );
}
foreach( $cols as $id => $name )
{
	echo '<li id="'.$id.'"><a href="#">'.$name.'</a></li>';
}
//	<li id="project" class="jstree-checked"><a href="#">Project</a></li>
?>
</ul>
</div>
			<p class="hint">Only checked columns will be displayed.  You can drag and drop the column items to re-order them.</p>
		</div>
	</div>

<script type="text/javascript">
$('#widgetColumns').jstree({
	plugins: ['themes', 'html_data', 'checkbox', 'ui', 'dnd', 'types'],
	themes: {
		url: '/jstree/style.css',
		dots: false
	},
	dnd: {
	},
	types: {
		valid_children: ['default'],
		types: {
			default: {
				valid_children: []
			}
		}
	}
});

function go()
{
	var g = {
		widget: 'TaskList',
		params: {
			title: $('#title').val(),
			mode: $('#mode').val(),
			open: eval($('#open').val()),
			itemsPerPage: parseInt( $('#itemsPerPage').val() ),
			cols: []
		}
	};
	if( g.params.itemsPerPage < 1 )
		g.params.itemsPerPage = 1;
	$('#widgetColumns ul li.jstree-checked').each(function(idx, el){
		g['params']['cols'].push( $(el).attr('id') );
	});
	saveWidget( $.toJSON(g) );
}

$('#mode').val('<?php echo $this->mode; ?>');
$('#open').val('<?php strtolower(var_export($this->open)); ?>');
</script>

	<div class="row buttons">
		<label>&nbsp;</label>
		<div class="row-right">
			<button onclick="go();">Save Widget</button>
		</div>
	</div>

	<div style="clear: both;"></div>
</div>
