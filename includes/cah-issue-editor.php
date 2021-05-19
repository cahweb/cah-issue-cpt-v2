<?php

require_once 'cah-issue-field.php';
use CAH_IssueMetaField as Field;

if( !class_exists( 'CAH_IssueEditor' ) ) {
    class CAH_IssueEditor
    {
    // Private Members
        private static $meta_values;
        private static $settings = array(
            'sm' => array( 'textarea_rows' => 3 ),
            'md' => array( 'textarea_rows' => 6 )
        );
        private static $meta_fields = array(
            'journal-title',
            'theme',
            'cover-date',
            'volume',
            'issue',
            'isbn',
            'issn',
            'pur-url',
            'pub-date',
            'editorial'
        );

        private static $post;

    // Public Methods
        public static function add_meta_boxes() {
            add_meta_box(
                'issue-info-meta', 
                'Issue Information', 
                array( __CLASS__, 'issue_info' ), 
                'issue', 
                'normal', 
                'high' 
            );
            add_meta_box(
                'issue-editorial-meta',
                'Editorial Information',
                array( __CLASS__, 'editorial_info' ),
                'issue',
                'normal',
                'high'
            );
        }


        public static function save() {
            $post = self::_get_post();

            if( !is_object( $post ) ) return;

            $meta = self::_get_meta( $post->ID );
            $vol = get_post_meta( $post->ID, 'vol-num', true );
            $issue = get_post_meta( $post->ID, 'issue-num', true );
            $pub_date = maybe_unserialize( get_post_meta( $post->ID, 'issue-pub-date', true ) );

            if( empty( $meta ) ) {
                $meta = array();

                foreach( self::$meta_fields as $key ) {
                    $meta[$key] = null;
                }
            }

            if( isset( $_POST['pub-date'] ) ) {
                $date = date_create_from_format( 'Y-m-d', $_POST['pub-date'] );

                if( $date != $pub_date ) {
                    update_post_meta( $post->ID, 'issue-pub-date', $date );
                    $meta['pub-date'] = $date;
                }
            }

            foreach( $meta as $key => $value ) {
                if( isset( $_POST[$key] ) 
                    && ( 'pub-date' != $key ) ) 
                {
                    $meta[$key] = $_POST[$key];
                }
            }

            if( !isset( $meta['cover-date'] ) && isset( $_POST['cover-date'] ) ) {
                $meta['cover-date'] = $_POST['cover-date'];
            }

            // Keeping these older values is less efficient, but we need them for some of the
            // older plugins to keep functioning properly. They also allow us to more easily
            // utilize the WP_Query object for filtering/sorting results, which will be a
            // net savings, in the long run (I think).
            if( isset( $_POST['volume'] ) && $_POST['volume'] != $vol ) {
                update_post_meta( $post->ID, 'vol-num', $_POST['volume'] );
            }
            if( isset( $_POST['issue'] ) && $_POST['issue'] != $issue ) {
                update_post_meta( $post->ID, 'issue-num', $_POST['issue'] );
            }

            update_post_meta( $post->ID, 'issue-meta', $meta );
        }


        public static function issue_info() {
            $meta = self::_get_meta();

            $pub_date = maybe_unserialize( $meta['pub-date'] );

            if( $pub_date instanceof DateTime ) {
                $meta['pub-date'] = date_format( $meta['pub-date'], 'Y-m-d' );
            }

            $fields = array(
                new Field( $meta, 'journal-title', 'Journal Title' ),
                new Field( $meta, 'theme', 'Theme' ),
                new Field( $meta, 'pub-date', 'Publication Date', '', 'date' ),
                new Field( $meta, 'cover-date', 'Cover Date' ),
                new Field( $meta, 'volume', 'Volume Number', '', 'number' ),
                new Field( $meta, 'issue', 'Issue Number', '', 'number' ),
                new Field( $meta, 'isbn', 'ISBN' ),
                new Field( $meta, 'issn', 'ISSN' ),
                new Field( $meta, 'pur-url', 'Purchase URL', '', 'url' )
            );

            ob_start();
            ?>
            <div class="inner-meta">
                <table>
                    <?php foreach( $fields as $field ) : ?>
                    <?= $field ?>
                    <?php endforeach; ?>
                </table>
            </div>
            <?php
            echo ob_get_clean();

            unset( $fields );
        }


        public static function editorial_info() {
            $meta = self::_get_meta();

            wp_editor( $meta['editorial'], 'editorial', self::$settings['md'] );
        }


        public static function update_meta_schema( $post_id, $return = false ) {
            $meta = self::_clean_meta( get_post_meta( $post_id ) );

            $new_meta = array();

            foreach( self::$meta_fields as $key ) {
                if( isset( $meta[$key] ) ) {
                    $new_meta[$key] = is_array( $meta[$key] ) ? $meta[$key][0] : $meta[$key];
                    delete_post_meta( $post_id, $key );
                }
            }

            // Keeping these fields is less efficient, but maintains compatibility with a few
            // plugins that rely on these legacy meta fields. Will also speed up searching and
            // organization in some other contexts that will result in a net efficiency increase.
            $vol = null;
            $issue = null;
            if( isset( $meta['issue-num'] ) && !empty( $meta['issue-num'] ) ) {
                $issue = is_array( $meta['issue-num'] ) ? $meta['issue-num'][0] : $meta['issue-num'];
                $new_meta['issue'] = $issue;
            }
            if( isset( $meta['vol-num'] ) && !empty( $meta['vol-num'] ) ) {
                $vol = is_array( $meta['vol-num'] ) ? $meta['vol-num'][0] : $meta['vol-num'];
                $new_meta['volume'] = $vol;
            }

            if( !isset( $meta['cover-date'] ) ) {
                $new_meta['cover-date'] = '';
            }

            update_post_meta( $post_id, 'issue-meta', $new_meta );
            update_post_meta( $post_id, 'issue-num', $issue );
            update_post_meta( $post_id, 'vol-num', $vol );

            if( $return ) {
                return $new_meta;
            }
        }


    // Private Methods
        private static function _get_post() {
            if( !isset( self::$post ) ) {
                global $post;
                self::$post = $post;
            }
            return self::$post;
        }


        private static function _get_meta() {
            $post = self::_get_post();

            if( !isset( self::$meta_values ) && get_post_meta( $post->ID, 'journal-title', true ) ) {
                self::update_meta_schema( $post->ID );
            }

            if( !isset( self::$meta_values ) ) {

                self::$meta_values = maybe_unserialize( get_post_meta( $post->ID, 'issue-meta', true ) );

                if( !is_array( self::$meta_values ) ) {
                    self::$meta_values = array();
                }

                if( isset( self::$meta_values['issue'] ) && is_array( self::$meta_values['issue'] ) ) {
                    self::$meta_values = self::_clean_meta( self::$meta_values );
                }
            }

            return self::$meta_values;
        }


        private static function _clean_meta( $meta ) {

            foreach( self::$meta_fields as $key ) {
                if( isset( $meta[$key] ) && is_array( $meta[$key] ) ) {
                    $arr = maybe_unserialize( $meta[$key][0] );
                    if( is_array( $arr ) ) {
                        $meta[$key] = $arr[0];
                    }
                    else {
                        $meta[$key] = $arr;
                    }
                }
            }

            return $meta;
        }
    }
}
?>