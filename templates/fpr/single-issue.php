<?php
/**
 * Template Name: Single Issue Template (FPR)
 */

get_header();

the_post();

$meta = get_post_meta( $post->ID, 'issue-meta', true );

if( empty( $meta ) ) {
    $plugin_dir = ABSPATH . 'wp-content/plugins/common-issue-2';

    require_once "$plugin_dir/includes/cah-issue-editor.php";

    CAH_IssueEditor::update_meta_schema( $post->ID );

    $meta = get_post_meta( $post->ID, 'issue-meta', true );
}

$vol = numberToRomanRepresentation( intval( $meta['volume'] ) );

$pub = maybe_unserialize( $meta['pub-date'] );
?>

<div class="container mb-5 mt-3 mt-lg-5" style="min-height: 250px;">
    <div class="serif-div">
        <div class="row">
        <?php if( has_post_thumbnail() && !empty( $thumb_url = get_the_post_thumbnail_url() ) ) : ?>
            <div class="col-4 col-md-2">
                <img style="width: 100%;" src="<?= $thumb_url ?>">
            </div>
        <?php endif; ?>
            <div class="<?= has_post_thumbnail() ? 'col-8 col-md-10' : 'col-12' ?>">
                <h1 class="h1 font-condensed text-complementary">Volume <?= $vol ?>, Issue <?= $meta['issue'] ?></h1>
            <?php foreach( array( $meta['theme'], $meta['cover-date'] ) as $value ) : ?>
                <?php if( !empty( $value ) ) : ?>
                <h2 class="h2 mb-2"><?= $value ?></h2>
                <?php endif; ?>
            <?php endforeach; ?>
                <?php the_content(); ?>
                <h4 class="mb-4">Articles</h4>
                <?php
                $args = array(
                    'post_type' => 'article',
                    'orderby' => 'meta_value_num',
                    'meta_key' => 'start',
                    'order' => 'asc',
                    'meta_query' => array(
                        'relation' => 'AND',
                        array(
                            'key' => 'article-issue',
                            'value' => intval( $meta['issue'] ),
                            'compare' => '='
                        ),
                        array(
                            'key' => 'article-volume',
                            'value' => intval( $meta['volume'] ),
                            'compare' => '='
                        )
                    ),
                );

                $articles = new WP_Query( $args );

                if( $articles->have_posts() ) : ?>
                    <ul style="list-style-type: none; padding-left: 0;">
                    <?php while( $articles->have_posts() ) :
                        $articles->the_post();

                        $article_meta = maybe_unserialize( get_post_meta( get_the_ID(), 'article-meta', true ) );

                        $title = get_the_title();

                        $other_auth_str = '';
                        if( !empty( $article_meta['other-authors'] ) ) {
                            $other_arr = explode( ',', $other_authors );
                            
                            if( count( $other_arr ) > 1 ) {
                                $other_auth_str .= " <em>et al.</em>";
                            }
                            else {
                                $other_auth_str .= " and {$other_arr[0]}";
                            }
                        }

                        $authors = $article_meta['author1-last'] . ( !empty( $article_meta['author1-first'] ) ? ", {$article_meta['author1-first']}" : '' ) . $other_auth_str;

                        ?>
                        <li>
                            <a href="<?= the_permalink() ?>">
                                <strong><?= $article_meta['start'] ?></strong> | <?= $authors ?> &ndash; &ldquo;<?= $title ?>&rdquo;
                            </a>
                            <br />
                            <?php the_tags( '<span class="tags">' . __( 'Tags', 'bonestheme' ) . '</span> ', ', ', '' ); ?>
                        </li>
                    
                    <?php endwhile; ?>
                    <?php wp_reset_postdata(); ?>

                    </ul>
                    
                <?php else : ?>
                    <article id="post-not-found" class="hentry clearfix">
                        <header class="article-header">
                            <h1><?php _e( 'Oops, Articles Not Found!', 'bonestheme' ); ?></h1>
                        </header>
                        <section class="entry-content">
                            <p><?php _e( 'Uh-oh! Something is missing. Try double-checking things.', 'bonestheme' ); ?></p>
                        </section>
                        <footer class="article-footer">
                            <p><?php _e( 'This is the error message in the single-issue.php template.', 'bonestheme' ); ?></p>
                        </footer>
                    </article>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php

get_footer();

?>