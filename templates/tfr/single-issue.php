<?php
/**
 * Template Name: Single Issue Template (TFR)
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

$pub_date = maybe_unserialize( $meta['pub-date'] );

$cov_date = !empty( $meta['cover-date'] ) ? $meta['cover-date'] : date_format( $pub_date, 'F Y' );

if( !empty( $meta['editorial'] ) ) {
    $editorial = wpautop( convert_chars( convert_smilies( wptexturize ( $meta['editorial'] ) ) ) );
}
?>

<div id="primary" class="content-area border-top">
    <main id="main" class="site-main" role="main">

        <h1 class="entry-title issue-title"><?= "{$meta['volume']}.{$meta['issue']}, $cov_date" ?></h1>

        <?php if( !empty( $meta['theme'] ) ) : ?>
        <h3 class="issue-theme"><em><?= $meta['theme'] ?></em></h3>
        <?php endif; ?>

        <div class="issue-block">
            <div class="issue-metadata">
                <?php if( has_post_thumbnail() ) : ?>
                <div class="issue-img">
                    <?php the_post_thumbnail(); ?>
                    <p class="issue-img-caption"><em>Published: <?= date_format( $pub_date, 'd F, Y' ) ?></em></p>
                </div>
                <?php endif; ?>

                <div class="issue-purchase-info">
                    <p>
                        <a class="issue-purchase-button<?= empty( $meta['pur-url'] ) ? ' disabled' : '' ?>" 
                            target="_blank" 
                            href="<?= !empty( $meta['pur-url'] ) ? $meta['pur-url'] : '#' ?>"
                        >
                            PURCHASE
                        </a>
                    </p>

                <?php foreach( 
                        array( 'isbn' => $meta['isbn'], 'issn' => $meta['issn'] ) 
                        as $key => $value
                    ) : 
                        if( !empty( $value ) ) :
                ?>
                    <p><strong><?= strtoupper( $key ) ?>: </strong><?= $value ?></p>
                <?php   endif; 
                    endforeach;
                ?>
                </div>

                <div class="issue-editorial">
                <?php if( !empty( $editorial ) ) : ?>
                    <h4>Editorial Staff</h4>
                    <?= $editorial ?>
                <?php endif; ?>
                </div>

                <div class="issue-content">
                    <?= the_content(); ?>
                </div>
            </div>
        </div>
    </main>
</div>
<?php

get_footer();

?>