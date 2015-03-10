<?php
/*
Plugin Name: Wp Users With Specific roles
*/

/**
 * Adds User Get widget.
 */
class users_get_widget extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	function __construct() {
		parent::__construct(
			'users_get_widget', // Base ID
			__( 'Users', 'text_domain' ), // Name
			array( 'description' => __( 'Wp Users Get With Specific roles', 'text_domain' ), ) // Args
		);
	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
		echo $args['before_widget'];
		if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ). $args['after_title'];
		}
		$users = array();
	    $roles = $instance['users'];;

	    foreach ($roles as $role) :
	        $users_query = new WP_User_Query( array( 
	            'fields' => 'all_with_meta', 
	            'role' => $role, 
	            'orderby' => 'display_name'
	            ) );
	        $results = $users_query->get_results();
	        if ($results) $users = array_merge($users, $results);
	    endforeach;

	    foreach($users as $user) : ?>
		    <p><a href="<?php echo get_author_posts_url($user->ID); ?>"><?php echo $user->data->user_nicename; ?></a></p>
	    <?php endforeach;
		echo $args['after_widget'];
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {
		$title = ! empty( $instance['title'] ) ? $instance['title'] : __( 'New title', 'text_domain' );
		$users_instance = $instance['users']; ?>
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>

		<?php global $wp_roles; ?>
		<?php foreach ( $wp_roles->roles as $key=>$value ): ?>
		<p>
			<input <?php if(in_array($key, $users_instance)){ echo 'checked '; } ?> type="checkbox" id="user-<?php echo $key; ?>-<?php echo $this->get_field_id( 'users' ); ?>" value="<?php echo $key; ?>" name="<?php echo $this->get_field_name( 'users' ); ?>[]" />
			<label for="user-<?php echo $key; ?>-<?php echo $this->get_field_id( 'users' ); ?>"><?php echo $value['name']; ?></label>
		</p>	 
		<?php endforeach; ?>
		<?php 
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['users'] = $new_instance['users'];
		return $instance;
	}

} // class users_get_widget

// register users_get_widget widget
function register_users_get_widget() {
    register_widget( 'users_get_widget' );
}
add_action( 'widgets_init', 'register_users_get_widget' );
