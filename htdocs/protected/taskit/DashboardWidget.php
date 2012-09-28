<?php

class DashboardWidget extends CWidget
{
	public $title = '[Default Title]';
	public $viewId;
	public $widgetId;
	public $scenario = 'display';

	public function run()
	{
		switch( $this->scenario )
		{
		case 'display':
			return $this->runDisplay();

		case 'edit':
			return $this->runEdit();

		default:
			echo '--No such scenario--';
		}
	}

	public function runEdit()
	{
		echo 'There are no options for this widget.';
	}
}

