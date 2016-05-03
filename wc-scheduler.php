<?php
/**
 * Plugin Name: WC Schedule Widget
 * Version: 1.0.0
 * Description: Show WordCamp Event List Widget
 * Author: hideokamoto
 * Author URI: https://profiles.wordpress.org/hideokamoto
 * Plugin URI: https://github.com/hideokamoto/wc-schedule-widget
 * Text Domain: wc-scheduler
 * Domain Path: /languages
 * @package Wc-scheduler
 */
require_once( dirname( __FILE__ ). '/includes/class/widget.php' );
require_once( dirname( __FILE__ ). '/includes/lib/array_column.php');

function register_wc_schedule_widget() {
  register_widget( 'WordCamp_Scheduler_Widget' );
}
add_action( 'widgets_init', 'register_wc_schedule_widget' );
