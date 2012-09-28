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
		<label for="title">Max comment characters</label>
		<div class="row-right">
			<input type="text" id="maxComment" value="<?php echo $this->maxComment; ?>" />
			<p class="hint">For smaller displays, enter a lower number here.  If maximum is zero then the entire comment will be displayed.</p>
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
	'Team' => 'Team',
	'Project' => 'Project',
	'Task' => 'Task',
	'Author' => 'Author',
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
		widget: 'RecentComments',
		params: {
			title: $('#title').val(),
			maxComment: parseInt( $('#maxComment').val() ),
			itemsPerPage: parseInt( $('#itemsPerPage').val() ),
			cols: []
		}
	};
	if( g.params.maxComment < 0 )
		g.params.maxComment = 0;
	if( g.params.itemsPerPage < 1 )
		g.params.itemsPerPage = 1;
	$('#widgetColumns ul li.jstree-checked').each(function(idx, el){
		g['params']['cols'].push( $(el).attr('id') );
	});
	saveWidget( $.toJSON(g) );
}
</script>

	<div class="row buttons">
		<label>&nbsp;</label>
		<div class="row-right">
			<button onclick="go();">Save Widget</button>
		</div>
	</div>

	<div style="clear: both;"></div>
</div>
