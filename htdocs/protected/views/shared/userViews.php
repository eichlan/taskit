<?php
Yii::app()->clientScript->registerScriptFile( 
	"/js/jquery.json-2.3.min.js",
	CClientScript::POS_HEAD
);

if( isset($project) )
{
	$this->breadcrumbs=Utility::projectMenu( $project, true );
}
else
{
	$this->breadcrumbs=array();
}

?>
<span id="extraStyle" style="display: none;"></span>
<div class="yiiTab"><ul class="tabs" id="viewTabs">
<?php
foreach( $views as $k => $v )
{
	echo '<li id="tab-view-'.$k.'" ><a ';
	if( $k == $renderer->page )
		echo 'class="active" ';
	echo 'href="'.Yii::app()->createUrl($renderer->route,$renderer->params+array('page'=>$k)).'">'.$v['name'].'</a></li>';
}
?><li style="float: right;">
<a class="notatab" href="javascript:void(0);" onclick="addTab();" title="Add a tab"><img src="/images/new-view.png" /></a>
<a class="notatab" href="javascript:void(0);" onclick="addWid();" title="Add a widget"><img src="/images/new-widget.png" /></a>
<a class="notatab" href="javascript:void(0);" onclick="true;" title="Edit current tab settings"><img src="/images/widget-config.png" /></a>
</li>
</ul>
<div class="view">
<?php $renderer->renderTemplate(); ?>
</div></div>
<script type="text/javascript">
function delWid( url ) {
	if( confirm('Are you sure you want to delete this widget?\n'+
		'This action cannot be undone.') )
	{
		window.location = url;
	}
}
function addWid() {
	popupFragment('newwidget');
}
function addTab() {
	$.ajax('<?php echo Yii::app()->createUrl('widget/addTab', array(
		'sec' => $renderer->view['section'])); ?>', {
		success: function() { window.location.reload(); }
	});
}
var lastTempl = '';
var lastTabTempl = '';
$(function(){
	$("#viewTabs").sortable({
		axis: 'x',
		containment: 'parent',
		tolerance: 'pointer',
		placeholder: 'tab-drop-placeholder',
		forcePlaceholderSize: true,
		start: function(event, ui) {
			$('#viewTabs').addClass('sort-drop-target');
			$('#extraStyle').html('<style type="text/css">.tab-drop-placeholder { padding-left: '+ui.helper.width()+'px; }</style>');
		},
		stop: function(event, ui) {
			$('#viewTabs').removeClass('sort-drop-target');
		},
		update: function(event, ui) {
			var nss = [];
			$('#viewTabs').children('[id|="tab-view"]').each(function(index){
				nss.push( parseInt($(this).attr('id').substr(9)) );
			});
			var templ = $.toJSON(nss);
			if( templ != lastTabTempl )
			{
				lastTabTempl = templ;
				$.ajax('<?php echo Yii::app()->createUrl('widget/updateTabLayout', array('sid'=>$renderer->view['section'])); ?>', {
					type: 'POST',
					data: templ,
					success: function( d ) {
						var idx = 0;
						$('#viewTabs').children('[id|="tab-view"]').each(function(index){
							$(this).attr('id', 'tab-view-'+idx);
							$(this).children('a').attr('href', '<?php
			echo Yii::app()->createUrl(
				$renderer->route,$renderer->params+array('page'=>'XXX')
			); ?>'.replace('XXX', idx));
							idx++;
						});
					},
					error: function( a, b, c ) {
						alert('error: ' + a + ', ' + b + ', ' + c );
					}
				});
			}
			//Yii::app()->createUrl($renderer->route,$renderer->params+array('page'=>$k))
		}
	});
	$(".widget-order-box").sortable({
		connectWith: '.widget-order-box',
		handle: '.widget-title',
		tolerance: 'pointer',
		placeholder: 'sort-drop-placeholder',
		start: function(event, ui) {
			$('.widget-order-box').addClass('sort-drop-target');
		},
		stop: function(event, ui) {
			$('.widget-order-box').removeClass('sort-drop-target');
		},
		update: function(event, ui) {
			var ns = {};
			$('.widget-order-box').each(function(index){
				var nss = [];
				$(this).children('.widget-box').each(function(index){
					nss.push( parseInt($(this).attr('id').substr(10)) );
				});
				ns[$(this).attr('id').substr(11)] = nss;
			});
			var templ = $.toJSON(ns);
			if( templ != lastTempl )
			{
				lastTempl = templ;
				$.ajax('<?php echo Yii::app()->createUrl('widget/updateLayout', array('vid'=>$renderer->view['viewid'], 'oid'=>$renderer->page, 'eid'=>$renderer->eid)); ?>', {
					type: 'POST',
					data: templ,
					success: function() {
						$('.widget-box').each(function(index){
							$(this).attr('id', 'widget-id-' + index );
						});
					},
					error: function( a, b, c ) {
						alert('error: ' + a + ', ' + b + ', ' + c );
					}
				});
			}
		}
	});
	$(".widget-title").disableSelection();
});
viewid = <?php echo $renderer->view['viewid']; ?>;
</script>
