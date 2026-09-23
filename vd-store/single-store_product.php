<?php
/**
 * Detail produk VD Store bertampilan Toko 21 (susunan sama dengan
 * velocitytoko_content_single_products di inc/function-child.php).
 *
 * @package justg
 */

defined('ABSPATH') || exit;

get_header();
$container = velocitytheme_option('justg_container_type', 'container');
?>

<div class="wrapper" id="single-wrapper">
    <div class="<?php echo esc_attr($container); ?>" id="content" tabindex="-1">
        <div class="row">
            <?php do_action('justg_before_content'); ?>
            <main class="site-main text-white col order-2" id="main">
                <?php while (have_posts()) :
                    the_post();
                    echo do_shortcode('[vd-breadcrumbs]'); ?>
                    <article <?php post_class(); ?> id="post-<?php the_ID(); ?>">

                        <div class="single-produk block-primary">
                            <div class="row">
                                <div class="col-md-6 col-xl-5">
                                    <?php echo do_shortcode('[wp_store_gallery]'); ?>
                                </div>
                                <div class="col-md">
                                    <h1 class="fs-4 fw-bold mb-3"><?php the_title(); ?></h1>
                                    <?php $kategori = get_the_term_list(get_the_ID(), 'store_product_cat', '', ', ');
                                    if ($kategori && !is_wp_error($kategori)) : ?>
                                        <div class="mb-2 kategori-produk"><small>Kategori: <?php echo $kategori; ?></small></div>
                                    <?php endif; ?>
                                    <div class="text-white mb-3">Harga: <?php echo do_shortcode('[wp_store_price]'); ?></div>
                                    <div class="mb-3"><?php echo do_shortcode('[wp_store_product_info]'); ?></div>
                                    <div class="mb-3"><?php echo do_shortcode('[wp_store_add_to_cart qty="true" text="Beli" class="btn bg-colortheme text-white"]'); ?></div>
                                    <div class="mb-3"><?php echo do_shortcode('[wp_store_add_to_wishlist label_add="Tambah ke Wishlist"]'); ?></div>
                                    <div class="mb-3"><?php echo velocity_toko21_beli_lain(); ?></div>
                                    <?php if (shortcode_exists('velocity-sharepost')) : ?>
                                        <div class="text-white mb-3"><?php echo do_shortcode('[velocity-sharepost]'); ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="block-primary">
                            <h3 class="title-single-part">Detail Produk</h3>
                            <?php // the_content() di sini diganti VD Store dengan tata letak bawaannya, jadi isi dibaca langsung. ?>
                            <div><?php echo do_shortcode(wpautop(get_the_content())); ?></div>
                        </div>

                        <?php velocity_toko21_produk_terkait(); ?>

                    </article>
                <?php endwhile; ?>
            </main>
            <?php do_action('justg_after_content'); ?>
        </div>
    </div>
</div>

<?php
get_footer();
