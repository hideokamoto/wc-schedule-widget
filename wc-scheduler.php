<?php
/**
 * Plugin Name: Wc-scheduler
 * Version: 0.1-alpha
 * Description: PLUGIN DESCRIPTION HERE
 * Author: YOUR NAME HERE
 * Author URI: YOUR SITE HERE
 * Plugin URI: PLUGIN SITE HERE
 * Text Domain: wc-scheduler
 * Domain Path: /languages
 * @package Wc-scheduler
 */
 require_once( dirname( __FILE__ ).'/includes/class/widget.php' );

function register_wc_schedule_widget() {
  register_widget( 'WordCamp_Scheduler_Widget' );
}
add_action( 'widgets_init', 'register_wc_schedule_widget' );
