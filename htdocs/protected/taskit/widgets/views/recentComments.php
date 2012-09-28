<?php

$this->widget('zii.widgets.CListView', array(
	'dataProvider' => $dataProvider,
	'itemView'=>'_recentComments_commentView',
	'itemsTagName'=>'table',
	'template'=>'{items}{pager}',
)); ?>
