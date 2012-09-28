<div class="widget-title"><?php echo $this->title; ?><a href="<?php
echo Yii::app()->createUrl('widget/edit', array('vid'=>$this->viewId, 'wid'=>$this->widgetId)); ?>"><img src="/images/widget-config.png" style="float: right;" /></a></div>
<div class="widget-container"><?php echo $content; ?></div>
