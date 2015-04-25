<?php
/*
Plugin Name: Wp Users With Specific roles Widget
Plugin URI: https://github.com/Faisalawanisee/Wp-Users-With-Specific-roles
Description: This plugin show all users with specific roles in widget 
Author: Faisal Awan
Version: 1.0
Author URI: https://facebook.com/Faisalawanisee
*/

/**
 * Adds User Get widget.
 */
class users_get_widget extends WP_Widget {

	function __construct() {
		parent::__construct(
			'users_get_widget',
			__( 'Users', 'wpuwsrw' ),
			array( 'description' => __( 'Wp Users Get With Specific roles', 'wpuwsrww' ), )
		);
	}

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

	public function form( $instance ) {
		$title = ! empty( $instance['title'] ) ? $instance['title'] : __( 'Users', 'wpuwsrw' );
		$users_instance = $instance['users']; ?>
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>

		<?php global $wp_roles; ?>
		<?php foreach ( $wp_roles->roles as $key=>$value ): ?>
		<p>
			<input <?php if($users_instance && in_array($key, $users_instance)){ echo 'checked="checked" '; } ?> type="checkbox" id="user-<?php echo $key; ?>-<?php echo $this->get_field_id( 'users' ); ?>" value="<?php echo $key; ?>" name="<?php echo $this->get_field_name( 'users' ); ?>[]" />
			<label for="user-<?php echo $key; ?>-<?php echo $this->get_field_id( 'users' ); ?>"><?php echo $value['name']; ?></label>
		</p>
		<?php endforeach; ?>
		<?php 
	}

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





