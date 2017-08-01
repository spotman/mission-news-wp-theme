<?php

//----------------------------------------------------------------------------------
// Add meta box that lets users choose a layout for any post that will override the global Customizer setting.
// Post templates (as added in 4.7) are not used instead b/c the post template doesn't change between layouts.
// TRT Note: no content is created by the meta box.
//----------------------------------------------------------------------------------
if ( ! function_exists( ( 'ct_mission_add_post_layout_meta_box' ) ) ) {
	function ct_mission_add_post_layout_meta_box() {

		$screens = array( 'post' );

		foreach ( $screens as $screen ) {

			add_meta_box(
				'ct_mission_post_layout',
				esc_html__( 'Layout', 'mission' ),
				'ct_mission_post_layout_callback',
				$screen,
				'side'
			);
		}
	}
}
add_action( 'add_meta_boxes', 'ct_mission_add_post_layout_meta_box' );

//----------------------------------------------------------------------------------
// Output the <select> element for users to select a layout
//----------------------------------------------------------------------------------
if ( ! function_exists( ( 'ct_mission_post_layout_callback' ) ) ) {
	function ct_mission_post_layout_callback( $post ) {

		wp_nonce_field( 'ct_mission_post_layout', 'ct_mission_post_layout_nonce' );

		$layout = get_post_meta( $post->ID, 'ct_mission_post_layout_key', true );
		?>
		<p>
			<select name="mission-post-layout" id="mission-post-layout" class="widefat">
				<option value="default"><?php esc_html_e( 'Use layout set in Customizer', 'mission' ); ?></option>
				<option value="double-sidebar" <?php if ( $layout == 'double-sidebar' ) {
					echo 'selected';
				} ?>><?php esc_html_e( 'Double sidebar', 'mission' ); ?>
				</option>
				<option value="left-sidebar" <?php if ( $layout == 'left-sidebar' ) {
					echo 'selected';
				} ?>><?php esc_html_e( 'Left sidebar', 'mission' ); ?>
				</option>
				<option value="right-sidebar" <?php if ( $layout == 'right-sidebar' ) {
					echo 'selected';
				} ?>><?php esc_html_e( 'Right sidebar', 'mission' ); ?>
				</option>
				<option value="no-sidebar" <?php if ( $layout == 'no-sidebar' ) {
					echo 'selected';
				} ?>><?php esc_html_e( 'No sidebar', 'mission' ); ?>
				</option>
			</select>
		</p> <?php
	}
}

//----------------------------------------------------------------------------------
// Save the meta box setting
//----------------------------------------------------------------------------------
if ( ! function_exists( ( 'ct_mission_post_layout_save_data' ) ) ) {
	function ct_mission_post_layout_save_data( $post_id ) {

		global $post;

		if ( ! isset( $_POST['ct_mission_post_layout_nonce'] ) ) {
			return;
		}
		if ( ! wp_verify_nonce( $_POST['ct_mission_post_layout_nonce'], 'ct_mission_post_layout' ) ) {
			return;
		}
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		if ( isset( $_POST['mission-post-layout'] ) ) {

			$layout            = $_POST['mission-post-layout'];
			$acceptable_values = array( 'default', 'double-sidebar', 'left-sidebar', 'right-sidebar', 'no-sidebar' );

			if ( in_array( $layout, $acceptable_values ) ) {
				update_post_meta( $post_id, 'ct_mission_post_layout_key', $layout );
			}
		}
	}
}
add_action( 'pre_post_update', 'ct_mission_post_layout_save_data' );