<?php

class TwoColumn
{
	public function render( $renderer )
	{?>
<table class="userview">
<tr><td colspan="2" class="widget-order-box" id="widget-box-head">
<?php $renderer->doSection('head'); ?>
</td></tr>
<tr>
<td class="widget-order-box" style="width: 50%;" id="widget-box-col0">
<?php $renderer->doSection('col0'); ?>
</td>
<td class="widget-order-box" style="width: 50%;" id="widget-box-col1">
<?php $renderer->doSection('col1'); ?>
</td>
</tr>
<tr><td colspan="2" class="widget-order-box" id="widget-box-foot">
<?php $renderer->doSection('foot'); ?>
</td></tr>
</table>

<?php
	}

	public function getOrder()
	{
		return array('head', 'col0', 'col1', 'foot');
	}
};

