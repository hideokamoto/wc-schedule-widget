<?php
class WordCamp_Scheduler_Widget extends WP_Widget {

	function __construct() {
		parent::__construct(
			'WordCamp_Scheduler_Widget',
			__( 'WordCamp Schedule List Widget', 'wc-scheduler' ),
			array( 'description' => __( 'WordCamp List', 'wc-scheduler' ), )
		);
	}

	public function widget( $args, $instance ) {
		$html = $args['before_widget'];
		if ( ! empty( $instance['title'] ) ) {
			$html .= $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ). $args['after_title'];
		}
		if ( empty( $instance['count'] ) ) {
			$instance['count'] = '-1';
		}
		if ( empty( $instance['tpl'] ) ) {
			$instance['tpl'] = $this->_get_default_tpl();
		}
		$camp_list = $this->_get_wordcamp_list();

		$html .= "<div id='wc-scheduler-postlist'>";
		if ( ! is_array( $camp_list ) ) {
			$html .= "<p>{$camp_list}</p>";
		} else {
			$i = 1;
			foreach ( $camp_list as $camp ) {
				$link = $camp['link'];
				$html .= $this->_set_api_data( $instance['tpl'], $camp );
				if ( $instance['count'] !== '-1' && (int) $instance['count'] == $i ) {
					break;
				}
				$i++;
			}
		}
		$html .= '</div>';
		echo $html. $args['after_widget'];
	}

	private function _set_api_data( $tpl, $camp_data ) {
		$tag_list = $this->_get_tags();
		foreach ( $tag_list as $tag ) {
			$value = $this->_get_matched_data( $tag, $camp_data );
			if ( false === $value ) {
				continue;
			}
			$tpl = str_replace( $tag , $value, $tpl );
		}
		return $tpl;
	}

	private function _match_keys( $tag ) {
		$key = array();
		switch ( $tag ) {
			case "%camp_title%":
				$key = [ 'title', false ];
				break;

			case "%start_date%":
				$key = [ 'post_meta', 'Start Date (YYYY-mm-dd)' ];
				break;

			case "%end_date%":
				$key = [ 'post_meta', 'End Date (YYYY-mm-dd)' ];
				break;

			case "%url%":
				$key = [ 'post_meta', 'URL' ];
				break;

			case "%venue_name%":
				$key = [ 'post_meta', 'Venue Name' ];
				break;

			case "%address%":
				$key = [ 'post_meta', 'Physical Address' ];
				break;

			case "%capacity%":
				$key = [ 'post_meta', 'Maximum Capacity' ];
				break;

			case "%venue_url%":
				$key = [ 'post_meta', 'Website URL' ];
				break;

			case "%exhibition_space_available%":
				$key = [ 'post_meta', 'Exhibition Space Available' ];
				break;

			case "%location%":
				$key = [ 'post_meta', 'Location' ];
				break;

			case "%central_url%":
				$key = [ 'link', false ];
				break;

			case "%twitter%":
				$key = [ 'post_meta', 'Twitter' ];
				break;

			case "%rooms%":
				$key = [ 'post_meta', 'Available Rooms' ];
				break;

			case "%hashtag%":
				$key = [ 'post_meta', 'WordCamp Hashtag' ];
				break;

			case "%anticipated_attendees%":
				$key = [ 'post_meta', 'Number of Anticipated Attendees' ];
				break;

			case "%organizer_name%":
				$key = [ 'post_meta', 'Organizer Name' ];
				break;

			case "%organizer_username%":
				$key = [ 'post_meta', 'WordPress.org Username' ];
				break;

			default:
				$key = [ false, false ];
				break;
		}
		return $key;
	}

	private function _get_matched_data( $tag, $camp_data ) {
		$key = $this->_match_keys( $tag );
		if ( ! $key[0] ) {
			return false;
		} elseif ( ! $key[1] ) {
			if ( 'link' == $key[0] ) {
				$camp_data[ $key[0] ] = esc_url( $camp_data[ $key[0] ] );
			} else {
				$camp_data[ $key[0] ] = esc_attr( $camp_data[ $key[0] ] );
			}
			return $camp_data[ $key[0] ];
		} else {
			foreach ( $camp_data[ $key[0] ] as $value ) {
				if ( $key[1] == $value['key'] ) {
					if ( 'Start Date (YYYY-mm-dd)' == $value['key'] || 'End Date (YYYY-mm-dd)' == $value['key'] ) {
						$value['value'] = esc_attr( date('Y-m-d', (int) $value['value'] ) );
					} elseif ( 'URL' == $value['key'] || 'Website URL' == $value['key'] ) {
						 $value['value'] = esc_url( $value['value'] );
					} else {
						 $value['value'] = esc_attr( $value['value'] );
					}
					return $value['value'];
				}
			}
		}
	}

	private function _get_wordcamp_list() {
		$url = 'https://central.wordcamp.org/wp-json/posts?type=wordcamp';
		$url = $url. '&filter[posts_per_page]=100';
		$result = wp_remote_get( $url );
		if ( is_wp_error( $result ) ) {
			return 'Fail to Load List.';
		}
		$camp_list = $this->_parse_wc_list( $result['body'] );
		return $camp_list;
	}

	private function _parse_wc_list( $body ) {
		$camp_list = json_decode( $body , true );
		$camp_list = $this->_pick_start_date( $camp_list );
		$camp_list = $this->_sort_by_date( $camp_list );
		return $camp_list;
	}

	private function _pick_start_date( $camp_list ) {
		foreach ( $camp_list as $key => $camp ) {
			foreach ( $camp['post_meta'] as $meta ) {
				if ( 'Start Date (YYYY-mm-dd)' === $meta['key'] ) {
					if ( time() <=  $meta['value'] ) {
						$camp_list[ $key ]['start_datetime'] = $meta['value'];
					} else {
						unset( $camp_list[ $key ] );
					}
					break;
				}
			}
		}
		return $camp_list;
	}

	private function _sort_by_date( $camp_list ) {
		$date_list = array_column( $camp_list, 'start_datetime' );
		array_multisort( $date_list, SORT_ASC, $camp_list );
		return $camp_list;
	}

	private function _get_default_tpl() {
		$tpl  = '<dl>';
		$tpl .= '<dt>'. __( 'Event Name', 'wc-scheduler' ) . "</dt>\n";
		$tpl .= "<dd>%camp_title%</dd>\n";
		$tpl .= '<dt>'. __( 'Start Date', 'wc-scheduler' ) . "</dt>\n";
		$tpl .= "<dd>%start_date%</dd>\n";
		$tpl .= '<dt>'. __( 'End Date', 'wc-scheduler' ) . "</dt>\n";
		$tpl .= "<dd>%end_date%</dd>\n";
		$tpl .= '<dt>'. __( 'URL', 'wc-scheduler' ) . "</dt>\n";
		$tpl .= "<dd>%url%</dd>\n";
		$tpl .= '<dt>'. __( 'Venue Name', 'wc-scheduler' ) . "</dt>\n";
		$tpl .= "<dd>%venue_name%</dd>\n";
		$tpl .= '<dt>'. __( 'Available Rooms', 'wc-scheduler' ) . "</dt>\n";
		$tpl .= "<dd>%rooms%</dd>";
		$tpl .= '<dt>'. __( 'Address', 'wc-scheduler' ) . "</dt>\n";
		$tpl .= "<dd>%address%</dd>\n";
		$tpl .= '<dt>'. __( 'Capacity', 'wc-scheduler' ) . "</dt>\n";
		$tpl .= "<dd>%capacity%</dd>\n";
		$tpl .= '<dt>'. __( 'Venue URL', 'wc-scheduler' ) . "</dt>\n";
		$tpl .= "<dd>%venue_url%</dd>\n";
		$tpl .= '<dt>'. __( 'Exhibition Space Available', 'wc-scheduler' ) . "</dt>\n";
		$tpl .= "<dd>%exhibition_space_available%</dd>\n";
		$tpl .= '<dt>'. __( 'Location', 'wc-scheduler' ) . "</dt>\n";
		$tpl .= "<dd>%location%</dd>\n";
		$tpl .= '<dt>'. __( 'Central URL', 'wc-scheduler' ) . "</dt>\n";
		$tpl .= "<dd>%central_url%</dd>\n";
		$tpl .= '<dt>'. __( 'Twitter', 'wc-scheduler' ) . "</dt>\n";
		$tpl .= "<dd>%twitter%</dd>\n";
		$tpl .= '<dt>'. __( 'Hashtag', 'wc-scheduler' ) . "</dt>\n";
		$tpl .= "<dd>%hashtag%</dd>\n";
		$tpl .= '<dt>'. __( 'Anticipated Attendees', 'wc-scheduler' ) . "</dt>\n";
		$tpl .= "<dd>%anticipated_attendees%</dd>\n";
		$tpl .= '<dt>'. __( 'Organizer Name', 'wc-scheduler' ) . "</dt>\n";
		$tpl .= "<dd>%organizer_name%</dd>\n";
		$tpl .= '<dt>'. __( 'Organizer Username', 'wc-scheduler' ) . "</dt>\n";
		$tpl .= "<dd>%organizer_username%</dd>\n";
		$tpl .= '</dl>';
		return $tpl;
	}

	private function _get_tags() {
		$tags = array(
	        "%camp_title%",
	        "%start_date%",
	        "%end_date%",
	        "%url%",
			"%venue_name%",
			"%address%",
			"%capacity%",
			"%venue_url%",
			"%exhibition_space_available%",
			"%location%",
			"%central_url%",
			"%twitter%",
			"%hashtag%",
			"%anticipated_attendees%",
			"%organizer_name%",
			"%organizer_username%",
			"%rooms%",
	    );
		return $tags;
	}

	public function form( $instance ) {
		$title = ! empty( $instance['title'] ) ? $instance['title'] : __( 'WordCamp Schedule List Widget', 'wc-scheduler' );
		$count = ! empty( $instance['count'] ) ? $instance['count'] : -1;
		$tpl = ! empty( $instance['tpl'] ) ? $instance['tpl'] : $this->_get_default_tpl();
		$tags = $this->_get_tags();
		$html  = '';
	    $html .= '<div style="margin:5px 0;">';
	    $html .= '<code>'.join("</code>, <code>", $tags).'</code>';
	    $html .= '</div>';
		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Widget Title:', 'wc-scheduler' ); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		<label for="<?php echo $this->get_field_id( 'count' ); ?>"><?php _e( 'Show Count', 'wc-scheduler', 'wc-scheduler' ); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'count' ); ?>" name="<?php echo $this->get_field_name( 'count' ); ?>" type="text" value="<?php echo esc_attr( $count ); ?>">
		<label for="<?php echo $this->get_field_id( 'tpl' ); ?>"><?php _e( 'Template', 'wc-scheduler', 'wc-scheduler' ); ?></label>
		<textarea class="widefat" rows="16" cols="20" id="<?php echo $this->get_field_id( 'tpl' ); ?>" name="<?php echo $this->get_field_name( 'tpl' ); ?>"><?php echo esc_html( $tpl ); ?></textarea>
		<?php echo $html; ?>
		</p>
		<?php
	}

	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['count'] = ( ! empty( $new_instance['count'] ) ) ? strip_tags( $new_instance['count'] ) : -1;
		$instance['tpl'] = ( ! empty( $new_instance['tpl'] ) ) ? str_replace( ']]>', ']]&gt;', $new_instance['tpl'] ) : $this->_get_default_tpl();

		return $instance;
	}

}
