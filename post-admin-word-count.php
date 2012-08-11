<?php
/*
  Plugin Name: Post Admin Word Count
  Plugin URI: http://www.jonbishop.com/downloads/wordpress-plugins/post-word-count/
  Description: Adds a sortable column to the admin's post manager, displaying the word count for each post.
  Version: 1.0
  Author: Jon Bishop
  Author URI: http://www.jonbishop.com
  License: GPL2
 */
class PostAdminWordCount{
	function init() {
		if(is_admin()){
			add_filter('manage_edit-post_sortable_columns', array(&$this, 'pwc_column_register_sortable'));
			add_filter('posts_orderby', array(&$this, 'pwc_column_orderby'), 10, 2);
			add_filter("manage_posts_columns", array(&$this, "pwc_columns"));
			add_action("manage_posts_custom_column", array(&$this, "pwc_column"));
		}
	}

	//=============================================
	// Add new columns to action post type
	//=============================================
	function pwc_columns($columns) {
		$columns["post_word_count"] = "Word Count";
		return $columns;
	}
	
	//=============================================
	// Add data to new columns of action post type
	//=============================================
	function pwc_column($column){
		global $post;
		if ("post_word_count" == $column) {
			$saved_word_count = get_post_meta($post->ID, '_post_word_count', true);
			$word_count = str_word_count($post->post_content);
			if($saved_word_count != $word_count){
				update_post_meta($post->ID, '_post_word_count', $word_count, $saved_word_count);
			}
			echo $word_count;
		}
	}
	
	//=============================================
	// Queries to run when sorting
	// new columns of action post type
	//=============================================
	function pwc_column_orderby($orderby, $wp_query) {
		global $wpdb;
	 
		$wp_query->query = wp_parse_args($wp_query->query);

		if ( 'post_word_count' == @$wp_query->query['orderby'] )
			$orderby = "(SELECT CAST(meta_value as decimal) FROM $wpdb->postmeta WHERE post_id = $wpdb->posts.ID AND meta_key = 'post_word_count') " . $wp_query->get('order');

		return $orderby;
				
	}
	
	//=============================================
	// Make new columns to action post type sortable
	//=============================================
	function pwc_column_register_sortable($columns) {
		$columns['post_word_count'] = 'post_word_count';
		return $columns;
	}
}
$postAdminWordCount = new PostAdminWordCount();
$postAdminWordCount->init();
?>