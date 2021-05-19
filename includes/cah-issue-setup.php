<?php

if( !class_exists( 'CAH_IssueSetup' ) ) {
    class CAH_IssueSetup
    {

    // Public Methods
        public static function setup() {
            add_action( 'init', array( __CLASS__, 'register_issue' ), 10, 0 );
            add_action( 'add_meta_boxes', array( 'CAH_IssueEditor', 'add_meta_boxes' ), 10, 0 );
            add_action( 'save_post_issue', array( 'CAH_IssueEditor', 'save' ), 10, 0 );
        }


        public static function register_issue() {
            register_post_type( 'issue', self::_args() );
        }


    // Private Methods
        private static function _args() {
            $args = array(
				'label'                 => __( 'Issue', 'cah-issue' ),
				'description'           => __( 'A post type that contains extra meta information for authors, publication details, etc.', 'cah-issue' ),
				'labels'                => self::_labels(),
				'supports'              => array( 
                                            'title',
                                            'editor',
                                            'excerpt',
                                            'thumbnail',
                                            'revisions',
                                            'custom-fields'
                ),
				'taxonomies'            => self::_taxonomies(),
				'hierarchical'          => false,
				'public'                => true,
				'show_ui'               => true,
				'show_in_menu'          => true,
				'menu_position'         => 5,
				'menu_icon'             => 'dashicons-media-document',
				'show_in_admin_bar'     => true,
				'show_in_nav_menus'     => true,
				'can_export'            => true,
				'has_archive'           => true,		
				'exclude_from_search'   => false,
				'publicly_queryable'    => true,
				'capability_type'       => 'post',
			);

			$args = apply_filters( 'cah_issue_post_type_args', $args );

			return $args;
        }


        private static function _labels() {
            return array(
				'name'                  => _x( 'Issues', 'Post Type General Name', 'cah-issue' ),
				'singular_name'         => _x( 'Issue', 'Post Type Singular Name', 'cah-issue' ),
				'menu_name'             => __( 'Issues', 'cah-issue' ),
				'name_admin_bar'        => __( 'Issue', 'cah-issue' ),
				'archives'              => __( 'Issue Archives', 'cah-issue' ),
				'parent_item_colon'     => __( 'Parent Issue:', 'cah-issue' ),
				'all_items'             => __( 'All Issues', 'cah-issue' ),
				'add_new_item'          => __( 'Add New Issue', 'cah-issue' ),
				'add_new'               => __( 'Add New', 'cah-issue' ),
				'new_item'              => __( 'New Issue', 'cah-issue' ),
				'edit_item'             => __( 'Edit Issue', 'cah-issue' ),
				'update_item'           => __( 'Update Issue', 'cah-issue' ),
				'view_item'             => __( 'View Issue', 'cah-issue' ),
				'search_items'          => __( 'Search Issues', 'cah-issue' ),
				'not_found'             => __( 'Not found', 'cah-issue' ),
				'not_found_in_trash'    => __( 'Not found in Trash', 'cah-issue' ),
				'featured_image'        => __( 'Featured Image', 'cah-issue' ),
				'set_featured_image'    => __( 'Set featured image', 'cah-issue' ),
				'remove_featured_image' => __( 'Remove featured image', 'cah-issue' ),
				'use_featured_image'    => __( 'Use as featured image', 'cah-issue' ),
				'insert_into_item'      => __( 'Insert into issue', 'cah-issue' ),
				'uploaded_to_this_item' => __( 'Uploaded to this issue', 'cah-issue' ),
				'items_list'            => __( 'Issues list', 'cah-issue' ),
				'items_list_navigation' => __( 'Issues list navigation', 'cah-issue' ),
                'filter_items_list'     => __( 'Filter issue list', 'cah-issue' ),
            );
        }


        private static function _taxonomies() {
            $retval = array(
                'category',
                'post_tag',
			);

			$retval = apply_filters( 'cah_issue_taxonomies', $retval );

			foreach( $retval as $taxonomy ) {
				if ( ! taxonomy_exists( $taxonomy ) ) {
					unset( $retval[$taxonomy] );
				}
			}

			return $retval;
        }
    }
}
?>