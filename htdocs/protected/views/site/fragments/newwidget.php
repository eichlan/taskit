<div id="fragment-newwidget-help-bg" style="position: fixed; z-index: 999; left: 0; right: 0; top: 0; bottom: 0; opacity: .6; filter: alpha(opacity=60); background: black;" ></div><div id="fragment-newwidget-help" style="position: absolute; z-index: 1000; left: 10%; right: 10%; top: 10%; background: #E2FFD2; border: 1px black solid; border-radius: 5px; padding: 8px;">
<h3>Add a widget</h3>
<ul><?php
foreach( glob(Yii::getPathOfAlias('application.taskit.widgets').'/*.php') as $path )
{
	$class = preg_replace('@.*/([A-Za-z]*)\.php@', '$1', $path );
	echo '<li><a href="javascript:void(0);" onclick="addWidget(\''.$class.
		'\')">'. $class . '</a></li>';
}
?></ul>
<p>The selected widget will be added to the top of the current page.</p>
<div style="text-align: right;"><button id="fragment-newwidget-close">Cancel</button></div>
<script type="text/javascript">
var htRoot = $('#fragment-newwidget-help-bg, #fragment-newwidget-close');
htRoot.click(function(e){
	$('#fragment-newwidget-help').remove();
	$('#fragment-newwidget-help-bg').remove();
	htRoot.unbind('click');
});
var fh = $('#fragment-newwidget-help');
var of = fh.offset();
of.top += $('html').scrollTop();
fh.offset( of );
function addWidget( c ) {
	$.ajax('<?php echo Yii::app()->createUrl('widget/add', array(
		'class'=>'XXX', 'vid'=>'YYY'
	)); ?>'.replace('XXX', c).replace('YYY', viewid), {
		success: function() {
			window.location.reload();
		}
	});
}
</script>
</div>
