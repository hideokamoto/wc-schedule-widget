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

	function test_basic_get_dl_data_from_post_meta() {
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

	function test_url_get_dl_data_from_post_meta() {
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

	function test_website_get_dl_data_from_post_meta() {
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

	function test_start_get_dl_data_from_post_meta() {
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

	function test_end_date_get_dl_data_from_post_meta() {
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

	function test_sort_by_date() {
		$method = $this->create_reflection_mothod( '_sort_by_date' );

		$dummy = [
			array(
				'id' => 2,
				'start_datetime' => "1462579500",
			),
			array(
				'id' => 1,
				'start_datetime' => "1462579200",
			),
		];
		$this->assertEquals( $dummy[0]['id'], 2 );
		$res = $method->invoke( $this->widget, $dummy );
		$this->assertEquals( $res[0]['id'], 1 );
	}

	function test_add_param_to_pick_start_date() {
		$method = $this->create_reflection_mothod( '_pick_start_date' );
		$dummy = [
			array(
				'id' => 2,
				'post_meta' => [
					array(
						'key' => 'Start Date (YYYY-mm-dd)',
						'value' => 9999999999,
					),
				],
			),
		];
		$res = $method->invoke( $this->widget, $dummy );
		$this->assertEquals( $res[0]['start_datetime'], 9999999999 );
	}

	function test_remove_ended_camp_to_pick_start_date() {
		$method = $this->create_reflection_mothod( '_pick_start_date' );
		$dummy = [
			array(
				'id' => 2,
				'post_meta' => [
					array(
						'key' => 'Start Date (YYYY-mm-dd)',
						'value' => 0000000000,
					),
				],
			),
		];
		$res = $method->invoke( $this->widget, $dummy );
		$this->assertEmpty( $res );
	}

	function create_reflection_mothod( $mothod_name ) {
		$reflection = new \ReflectionClass( $this->widget );
		$method = $reflection->getMethod( $mothod_name );
		$method->setAccessible( true );
		return $method;
	}

		function create_dummy_post_meta() {
			$array = [
				array(
					'ID' => 42508216,
					'key' => "Venue Name",
					'value' => "Innovation Centre",
				),
				array(
					'ID' => 42508217,
					'key' => "Physical Address",
					'value' => "Innovation Centre Sunshine Coast",
				),
				array(
				    'ID' => 42508218,
				    'key' => "Maximum Capacity",
				    'value' => "500",
				),
				array(
				    'ID' =>42508219,
				    'key' => "Available Rooms",
				    'value' => "2",
				),
				array(
				    'ID' => 42508220,
				    'key' => "Website URL",
				    'value' => "https://innovationcentre.com.au/",
				),
				array(
				    'ID' => 42508222,
				    'key' => "Exhibition Space Available",
				    'value' => "",
				),
				array(
				    'ID' => 42508170,
				    'key' => "Start Date (YYYY-mm-dd)",
				    'value' => "1462579200",
				),
				array(
				    'ID' => 42508171,
				    'key' => "End Date (YYYY-mm-dd)",
				    'value' => "1462665600",
				),
				array(
				    'ID' => 42508172,
				    'key' => "Location",
				    'value' => "Sunshine Coast, QLD, Australia",
				),
				array(
				    'ID' => 42508173,
				    'key' => "URL",
				    'value' => "https://2016.sunshinecoast.wordcamp.org",
				),
				array(
				    'ID' => 42508175,
				    'key' => "Twitter",
				    'value' => "@WCSunshineCoast",
				),
				array(
				    'ID' => 42508176,
				    'key' =>"WordCamp Hashtag",
				    'value' =>"#WordCampSC",
				),
				array(
				    'ID' => 42508177,
				    'key' => "Number of Anticipated Attendees",
				    'value' => "360",
				),
				array(
				    'ID' => 42508179,
				    'key' => "Organizer Name",
				    'value' => "Luke Carbis",
				),
				array(
				    'ID' => 42508180,
				    'key' => "WordPress.org Username",
				    'value' => "lukecarbis",
			 	),
			];
			return $array;
		}

}
