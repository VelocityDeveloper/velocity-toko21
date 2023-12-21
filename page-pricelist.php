<?php

/**
 * Template Name: Velocity Toko Pricelist
 *
 * @package justg
 */

get_header();
$container        = get_theme_mod('justg_container_type', 'container');
$search_query     = new WP_Query(array(
    'post_type'         => 'product',
    'post_status'       => 'publish',
    'order'             => 'asc',
    'orderby'           => 'title',
    'posts_per_page'    => -1
));
?>

<div class="wrapper" id="page-wrapper">

    <div class="<?php echo esc_attr($container); ?>" id="content">

        <div class="row">

            <div class="content-area col order-2" id="primary">

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <?php the_title('<h1 class="entry-title fs-4 m-0">', '</h1>'); ?>
                    <span>
                        <?php echo do_shortcode('[print targetid="main"]'); ?>
                    </span>
                </div>

                <main class="site-main mt-3" id="main">
                    <?php if ($search_query->have_posts()) : ?>
                        <div class="table-responsive">
                            <table class="table">
                                <thead class="table-dark">
                                    <th>Kode</th>
                                    <th>Nama Barang</th>
                                    <th>Gambar</th>
                                    <th>Stok</th>
                                    <th>Berat</th>
                                    <th>Harga</th>
                                </thead>
                                <tbody>
                                    <?php while ($search_query->have_posts()) : $search_query->the_post(); ?>
                                        <tr>
                                            <td><?php echo get_post_meta(get_the_ID(), 'sku', true); ?></td>
                                            <td><?php echo get_the_title(); ?></td>
                                            <td>
                                                <div class="ratio ratio-1x1"><img src="<?php echo get_the_post_thumbnail_url(); ?>" /></div>
                                            </td>
                                            <td><?php echo get_post_meta(get_the_ID(), 'stok', true); ?></td>
                                            <td><?php echo get_post_meta(get_the_ID(), 'berat', true); ?></td>
                                            <td class="fw-bold"><?php echo do_shortcode('[harga]'); ?></td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else : ?>
                        <div class="container text-center">
                            <span>
                                <svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" fill="currentColor" class="bi bi-box-seam" viewBox="0 0 16 16">
                                    <path d="M8.186 1.113a.5.5 0 0 0-.372 0L1.846 3.5l2.404.961L10.404 2l-2.218-.887zm3.564 1.426L5.596 5 8 5.961 14.154 3.5l-2.404-.961zm3.25 1.7-6.5 2.6v7.922l6.5-2.6V4.24zM7.5 14.762V6.838L1 4.239v7.923l6.5 2.6zM7.443.184a1.5 1.5 0 0 1 1.114 0l7.129 2.852A.5.5 0 0 1 16 3.5v8.662a1 1 0 0 1-.629.928l-7.185 2.874a.5.5 0 0 1-.372 0L.63 13.09a1 1 0 0 1-.63-.928V3.5a.5.5 0 0 1 .314-.464L7.443.184z" />
                                </svg>
                            </span>
                            <h3 class="mt-2 mb-3"> Produk tidak ditemukan ! :(</h3>
                        </div>
                    <?php endif; ?>

                </main><!-- #main -->

            </div><!-- #primary -->

        </div>

    </div><!-- #content -->

</div><!-- #page-wrapper -->

<?php
get_footer();
