<?php
require_once( 'includes/class/widget.php' );
class WidgetTest extends WP_UnitTestCase {
	private $widget;
	function __construct() {
		$this->widget = new WordCamp_Scheduler_Widget();
	}

	function test_trust_class_call() {
		$this->assertInstanceOf( 'WordCamp_Scheduler_Widget', $this->widget );
	}

}
