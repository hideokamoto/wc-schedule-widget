<?php
class WordCamp_Scheduler_Widget extends WP_Widget {

	function __construct() {
		parent::__construct(
			'WordCamp_Scheduler_Widget',
			__( 'WordCamp Schedule List Widget', 'wc-schedule-widget' ),
			array( 'description' => __( 'WordCamp List', 'wc-schedule-widget' ), )
		);
	}

	public function widget( $args, $instance ) {
		$html = $args['before_widget'];
		if ( ! empty( $instance['title'] ) ) {
			$html .= $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ). $args['after_title'];
		}

		$html .= "<div id='wc-schedule-widget-postlist' >aaa</div>";
		echo $html. $args['after_widget'];
	}

	public function form( $instance ) {
		$title = ! empty( $instance['title'] ) ? $instance['title'] : __( 'REST API Comment Widget', 'wc-schedule-widget' );
		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Widget Title:' ); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
		<?php
	}

	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';

		return $instance;
	}

}
