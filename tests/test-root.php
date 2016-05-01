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

	function test_constructor() {
		$this->assertEquals( 'WordCamp Schedule List Widget', $this->widget->name );
		$this->assertEquals( 'WordCamp List', $this->widget->widget_options['description'] );
	}

	function test_get_wordcamp_list() {
		$reflection = new \ReflectionClass( $this->widget );
		$method = $reflection->getMethod( '_get_wordcamp_list' );
		$method->setAccessible( true );
		$res = $method->invoke( $this->widget );
	}

	function test_get_basic_post_meta() {
		$method = $this->create_reflection_mothod( '_get_dl_data_from_post_meta' );

		$dummy = array(
			'ID' => 12345,
			'key' => "Venue Name",
			'value' => "Sample Venue",
		);
		$expect = array(
			'key' => $dummy['key'],
			'value' => $dummy['value'],
		);
		$res = $method->invoke( $this->widget, $dummy );
		$this->assertEquals( $res, $expect );
	}

	function test_get_url_post_meta() {
		$method = $this->create_reflection_mothod( '_get_dl_data_from_post_meta' );

		$dummy = array(
			'ID' => 12345,
			'key' => 'URL',
			'value' => "https://example.com/",
		);
		$expect = array(
			'key' => 'WordCamp URL',
			'value' => "<a href='https://example.com/' target='_blank'>https://example.com/</a>",
		);
		$res = $method->invoke( $this->widget, $dummy );
		$this->assertEquals( $res, $expect );
	}

	function test_get_website_url_post_meta() {
		$method = $this->create_reflection_mothod( '_get_dl_data_from_post_meta' );

		$dummy = array(
			'ID' => 12345,
			'key' => "Website URL",
			'value' => "https://example.com/",
		);
		$expect = array(
			'key' => 'Venue Website URL',
			'value' => "<a href='https://example.com/' target='_blank'>https://example.com/</a>",
		);
		$res = $method->invoke( $this->widget, $dummy );
		$this->assertEquals( $res, $expect );
	}

	function test_get_start_date_post_meta() {
		$method = $this->create_reflection_mothod( '_get_dl_data_from_post_meta' );

		$dummy = array(
			'ID' => 12345,
			'key' => "Start Date (YYYY-mm-dd)",
			'value' => "1462579200",
		);
		$expect = array(
			'key' => 'Start Date ',
			'value' => '2016-05-07',
		);
		$res = $method->invoke( $this->widget, $dummy );
		$this->assertEquals( $res, $expect );
	}

	function test_get_end_date_post_meta() {
		$method = $this->create_reflection_mothod( '_get_dl_data_from_post_meta' );

		$dummy = array(
			'ID' => 12345,
			'key' => "End Date (YYYY-mm-dd)",
			'value' => "1462579200",
		);
		$expect = array(
			'key' => 'End Date ',
			'value' => '2016-05-07',
		);
		$res = $method->invoke( $this->widget, $dummy );
		$this->assertEquals( $res, $expect );
	}

	function create_reflection_mothod( $mothod_name ) {
		$reflection = new \ReflectionClass( $this->widget );
		$method = $reflection->getMethod( $mothod_name );
		$method->setAccessible( true );
		return $method;
	}
}
