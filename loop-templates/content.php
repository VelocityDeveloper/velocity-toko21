<?php

/**
 * Post rendering content according to caller of get_template_part
 *
 * @package justg
 */

// Exit if accessed directly.
defined('ABSPATH') || exit; ?>

<article <?php post_class('p-md-2 p-0 mb-3'); ?> id="post-<?php the_ID(); ?>">
    <div class="row mx-0 py-2">
        <div class="col-md-3 p-0">
            <?php echo do_shortcode('[ratio-thumbnail class="rounded" size="medium" ratio="1:1"]'); ?>
        </div>

        <div class="col-md-9 p-md-2 py-2 px-0">
            <div class="judul-posts">
                <a class="h6 colortheme fw-bold" href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>"><?php echo the_title(); ?></a>
            </div>
            <div class="post-info d-flex flex-wrap">
                <span class="p-1">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-person" viewBox="0 0 16 16">
                        <path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6m2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0m4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4m-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10c-2.29 0-3.516.68-4.168 1.332-.678.678-.83 1.418-.832 1.664z" />
                    </svg> <?php echo get_the_author(); ?>
                </span>
                <span class="p-1">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-calendar4" viewBox="0 0 16 16">
                        <path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5M2 2a1 1 0 0 0-1 1v1h14V3a1 1 0 0 0-1-1zm13 3H1v9a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1z" />
                    </svg> <?php echo get_the_date('M d, Y', get_the_ID()); ?>
                </span>
                <span class="p-1">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-folder" viewBox="0 0 16 16">
                        <path d="M.54 3.87.5 3a2 2 0 0 1 2-2h3.672a2 2 0 0 1 1.414.586l.828.828A2 2 0 0 0 9.828 3h3.982a2 2 0 0 1 1.992 2.181l-.637 7A2 2 0 0 1 13.174 14H2.826a2 2 0 0 1-1.991-1.819l-.637-7a1.99 1.99 0 0 1 .342-1.31zM2.19 4a1 1 0 0 0-.996 1.09l.637 7a1 1 0 0 0 .995.91h10.348a1 1 0 0 0 .995-.91l.637-7A1 1 0 0 0 13.81 4H2.19zm4.69-1.707A1 1 0 0 0 6.172 2H2.5a1 1 0 0 0-1 .981l.006.139C1.72 3.042 1.95 3 2.19 3h5.396l-.707-.707z" />
                    </svg>
                    <?php
                    foreach (get_the_terms(get_the_ID(), 'category') as $tax) {
                        echo '<a class="text-white" href="' . get_term_link($tax->slug, 'category') . '">' . $tax->name . '</a>, ';
                    } ?>
                </span>
            </div>
            <div class="entry-content">
                <?php $content = get_the_content();
                $trimmed_content = wp_trim_words($content, 20);
                echo $trimmed_content; ?>
            </div>
        </div>
    </div>
</article>