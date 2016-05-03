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

	function test_match_keys() {
		$method = $this->create_reflection_mothod( '_match_keys' );

		$res = $method->invoke( $this->widget, "%camp_title%" );
		$this->assertEquals( $res, [ 'title', false ] );
		$res = $method->invoke( $this->widget, "%start_date%" );
		$this->assertEquals( $res, [ 'post_meta', 'Start Date (YYYY-mm-dd)' ] );
		$res = $method->invoke( $this->widget, "%end_date%" );
		$this->assertEquals( $res, [ 'post_meta', 'End Date (YYYY-mm-dd)' ] );
		$res = $method->invoke( $this->widget, "%url%" );
		$this->assertEquals( $res, [ 'post_meta', 'URL' ] );
		$res = $method->invoke( $this->widget, "%venue_name%" );
		$this->assertEquals( $res, [ 'post_meta', 'Venue Name' ] );
		$res = $method->invoke( $this->widget, "%address%" );
		$this->assertEquals( $res, [ 'post_meta', 'Physical Address' ] );
		$res = $method->invoke( $this->widget, "%capacity%" );
		$this->assertEquals( $res, [ 'post_meta', 'Maximum Capacity' ] );
		$res = $method->invoke( $this->widget, "%venue_url%" );
		$this->assertEquals( $res, [ 'post_meta', 'Website URL' ] );
		$res = $method->invoke( $this->widget, "%exhibition_space_available%" );
		$this->assertEquals( $res, [ 'post_meta', 'Exhibition Space Available' ] );
		$res = $method->invoke( $this->widget, "%location%" );
		$this->assertEquals( $res, [ 'post_meta', 'Location' ] );
		$res = $method->invoke( $this->widget, "%central_url%" );
		$this->assertEquals( $res, [ 'link', false ] );
		$res = $method->invoke( $this->widget, "%twitter%" );
		$this->assertEquals( $res, [ 'post_meta', 'Twitter' ] );
		$res = $method->invoke( $this->widget, "%hashtag%" );
		$this->assertEquals( $res, [ 'post_meta', 'WordCamp Hashtag' ] );
		$res = $method->invoke( $this->widget, "%anticipated_attendees%" );
		$this->assertEquals( $res, [ 'post_meta', 'Number of Anticipated Attendees' ] );
		$res = $method->invoke( $this->widget, "%organizer_name%" );
		$this->assertEquals( $res, [ 'post_meta', 'Organizer Name' ] );
		$res = $method->invoke( $this->widget, "%organizer_username%" );
		$this->assertEquals( $res, [ 'post_meta', 'WordPress.org Username' ] );
		$res = $method->invoke( $this->widget, "%rooms%" );
		$this->assertEquals( $res, [ 'post_meta', 'Available Rooms' ] );
		$res = $method->invoke( $this->widget, "dummy" );
		$this->assertEquals( $res, [ false, false ] );
	}

	function test_get_matched_data() {
		$method = $this->create_reflection_mothod( '_get_matched_data' );
		$dummy = $this->create_dummy_return();

		$res = $method->invoke( $this->widget, "%camp_title%", $dummy );
		$this->assertEquals( $res, $dummy['title'] );
		$res = $method->invoke( $this->widget, "%start_date%", $dummy );
		$this->assertEquals( $res, date( 'Y-m-d', 1462579200 ) );
		$res = $method->invoke( $this->widget, "%end_date%", $dummy );
		$this->assertEquals( $res, date( 'Y-m-d', 1462665600 ) );
		$res = $method->invoke( $this->widget, "%url%", $dummy );
		$this->assertEquals( $res, 'https://2016.sunshinecoast.wordcamp.org' );
		$res = $method->invoke( $this->widget, "%venue_name%", $dummy );
		$this->assertEquals( $res, 'Innovation Centre' );
		$res = $method->invoke( $this->widget, "%address%", $dummy );
		$this->assertEquals( $res, 'Innovation Centre Sunshine Coast' );
		$res = $method->invoke( $this->widget, "%capacity%", $dummy );
		$this->assertEquals( $res, '500' );
		$res = $method->invoke( $this->widget, "%venue_url%", $dummy );
		$this->assertEquals( $res, 'https://innovationcentre.com.au/' );
		$res = $method->invoke( $this->widget, "%exhibition_space_available%", $dummy );
		$this->assertEquals( $res, '' );
		$res = $method->invoke( $this->widget, "%location%", $dummy );
		$this->assertEquals( $res, 'Sunshine Coast, QLD, Australia' );
		$res = $method->invoke( $this->widget, "%central_url%", $dummy );
		$this->assertEquals( $res, 'http://example.com' );
		$res = $method->invoke( $this->widget, "%twitter%", $dummy );
		$this->assertEquals( $res, '@WCSunshineCoast' );
		$res = $method->invoke( $this->widget, "%hashtag%", $dummy );
		$this->assertEquals( $res, '#WordCampSC' );
		$res = $method->invoke( $this->widget, "%anticipated_attendees%", $dummy );
		$this->assertEquals( $res, '360' );
		$res = $method->invoke( $this->widget, "%organizer_name%", $dummy );
		$this->assertEquals( $res, 'John doe' );
		$res = $method->invoke( $this->widget, "%organizer_username%", $dummy );
		$this->assertEquals( $res, 'johndoe' );
		$res = $method->invoke( $this->widget, "%rooms%", $dummy );
		$this->assertEquals( $res, '2' );
		$res = $method->invoke( $this->widget, false, $dummy );
		$this->assertEquals( $res, false );
	}

	function test_set_api_data_camp_title() {
		$dummy = $this->create_dummy_return();
		$method = $this->create_reflection_mothod( '_set_api_data' );
		$tpl = "<div>%camp_title%</div>";
		$res = $method->invoke( $this->widget, $tpl, $dummy );
		$this->assertEquals( $res, "<div>sample title</div>" );
	}

	function test_set_api_data_start_date() {
		$dummy = $this->create_dummy_return();
		$method = $this->create_reflection_mothod( '_set_api_data' );
		$tpl = "<div>%start_date%</div>";
		$res = $method->invoke( $this->widget, $tpl, $dummy );
		$this->assertEquals( $res, '<div>'. date( 'Y-m-d', 1462579200 ). '</div>' );
	}

	function test_set_api_data_end_date() {
		$dummy = $this->create_dummy_return();
		$method = $this->create_reflection_mothod( '_set_api_data' );
		$tpl = "<div>%end_date%</div>";
		$res = $method->invoke( $this->widget, $tpl, $dummy );
		$this->assertEquals( $res, '<div>'. date( 'Y-m-d', 1462665600 ). '</div>' );
	}

	function test_set_api_data_url() {
		$dummy = $this->create_dummy_return();
		$method = $this->create_reflection_mothod( '_set_api_data' );
		$tpl = "<div>%url%</div>";
		$res = $method->invoke( $this->widget, $tpl, $dummy );
		$this->assertEquals( $res, "<div>https://2016.sunshinecoast.wordcamp.org</div>" );
	}

	function test_set_api_data_venue_name() {
		$dummy = $this->create_dummy_return();
		$method = $this->create_reflection_mothod( '_set_api_data' );
		$tpl = "<div>%venue_name%</div>";
		$res = $method->invoke( $this->widget, $tpl, $dummy );
		$this->assertEquals( $res, "<div>Innovation Centre</div>" );
	}

	function test_set_api_data_address() {
		$dummy = $this->create_dummy_return();
		$method = $this->create_reflection_mothod( '_set_api_data' );
		$tpl = "<div>%address%</div>";
		$res = $method->invoke( $this->widget, $tpl, $dummy );
		$this->assertEquals( $res, "<div>Innovation Centre Sunshine Coast</div>" );
	}

	function test_set_api_data_capacity() {
		$dummy = $this->create_dummy_return();
		$method = $this->create_reflection_mothod( '_set_api_data' );
		$tpl = "<div>%capacity%</div>";
		$res = $method->invoke( $this->widget, $tpl, $dummy );
		$this->assertEquals( $res, "<div>500</div>" );
	}

	function test_set_api_data_venue_url() {
		$dummy = $this->create_dummy_return();
		$method = $this->create_reflection_mothod( '_set_api_data' );
		$tpl = "<div>%venue_url%</div>";
		$res = $method->invoke( $this->widget, $tpl, $dummy );
		$this->assertEquals( $res, "<div>https://innovationcentre.com.au/</div>" );
	}

	function test_set_api_data_exhibition_space_available() {
		$dummy = $this->create_dummy_return();
		$method = $this->create_reflection_mothod( '_set_api_data' );
		$tpl = "<div>%exhibition_space_available%</div>";
		$res = $method->invoke( $this->widget, $tpl, $dummy );
		$this->assertEquals( $res, "<div></div>" );
	}

	function test_set_api_data_location() {
		$dummy = $this->create_dummy_return();
		$method = $this->create_reflection_mothod( '_set_api_data' );
		$tpl = "<div>%location%</div>";
		$res = $method->invoke( $this->widget, $tpl, $dummy );
		$this->assertEquals( $res, "<div>Sunshine Coast, QLD, Australia</div>" );
	}

	function test_set_api_data_central_url() {
		$dummy = $this->create_dummy_return();
		$method = $this->create_reflection_mothod( '_set_api_data' );
		$tpl = "<div>%central_url%</div>";
		$res = $method->invoke( $this->widget, $tpl, $dummy );
		$this->assertEquals( $res, "<div>http://example.com</div>" );
	}

	function test_set_api_data_twitter() {
		$dummy = $this->create_dummy_return();
		$method = $this->create_reflection_mothod( '_set_api_data' );
		$tpl = "<div>%twitter%</div>";
		$res = $method->invoke( $this->widget, $tpl, $dummy );
		$this->assertEquals( $res, "<div>@WCSunshineCoast</div>" );
	}

	function test_set_api_data_hashtag() {
		$dummy = $this->create_dummy_return();
		$method = $this->create_reflection_mothod( '_set_api_data' );
		$tpl = "<div>%hashtag%</div>";
		$res = $method->invoke( $this->widget, $tpl, $dummy );
		$this->assertEquals( $res, "<div>#WordCampSC</div>" );
	}

	function test_set_api_data_anticipated_attendees() {
		$dummy = $this->create_dummy_return();
		$method = $this->create_reflection_mothod( '_set_api_data' );
		$tpl = "<div>%anticipated_attendees%</div>";
		$res = $method->invoke( $this->widget, $tpl, $dummy );
		$this->assertEquals( $res, "<div>360</div>" );
	}

	function test_set_api_data_organizer_name() {
		$dummy = $this->create_dummy_return();
		$method = $this->create_reflection_mothod( '_set_api_data' );
		$tpl = "<div>%organizer_name%</div>";
		$res = $method->invoke( $this->widget, $tpl, $dummy );
		$this->assertEquals( $res, "<div>John doe</div>" );
	}

	function test_set_api_data_organizer_username() {
		$dummy = $this->create_dummy_return();
		$method = $this->create_reflection_mothod( '_set_api_data' );
		$tpl = "<div>%organizer_username%</div>";
		$res = $method->invoke( $this->widget, $tpl, $dummy );
		$this->assertEquals( $res, "<div>johndoe</div>" );
	}

	function test_set_api_data_rooms() {
		$dummy = $this->create_dummy_return();
		$method = $this->create_reflection_mothod( '_set_api_data' );
		$tpl = "<div>%rooms%</div>";
		$res = $method->invoke( $this->widget, $tpl, $dummy );
		$this->assertEquals( $res, "<div>2</div>" );
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

	function create_dummy_return() {
		$array = array(
			'title' => 'sample title',
			'link' => 'http://example.com',
			'post_meta' => $this->create_dummy_post_meta(),
		);
		return $array;
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
			    'value' => "John doe",
			),
			array(
			    'ID' => 42508180,
			    'key' => "WordPress.org Username",
			    'value' => "johndoe",
		 	),
		];
		return $array;
	}

}
