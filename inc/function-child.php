<?php

/**
 * Fuction yang digunakan di theme ini.
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

function velocity_categories()
{
    $args = array(
        'orderby' => 'name',
        'hide_empty' => false,
    );
    $cats = array(
        '' => 'Show All'
    );
    $categories = get_categories($args);
    foreach ($categories as $category) {
        $cats[$category->term_id] = $category->name;
    }
    return $cats;
}

add_action('after_setup_theme', 'velocitychild_theme_setup', 9);
function velocitychild_theme_setup()
{
    if (class_exists('Kirki')) :

        Kirki::add_panel('panel_toko21', [
            'priority'    => 10,
            'title'       => esc_html__('Setting Toko 21', 'justg'),
            'description' => esc_html__('', 'justg'),
        ]);

        // section title_tagline
        Kirki::add_section('title_tagline', [
            'panel'    => 'panel_toko21',
            'title'    => __('Site Identity', 'justg'),
            'priority' => 10,
        ]);

        ///Section Color
        Kirki::add_section('section_colorvelocity', [
            'panel'    => 'panel_toko21',
            'title'    => __('Background', 'justg'),
            'priority' => 10,
        ]);

        new \Kirki\Field\Color(
            [
                'settings'    => 'color_content',
                'label'       => __('Color Content', 'justg'),
                'description' => esc_html__('Control Content/Container Color.', 'justg'),
                'section'     => 'section_colorvelocity',
                'priority'    => 10,
                'default'     => '#343a40',
                'output'      => [
                    [
                        'element'   => '.bg-container',
                        'property'  => 'background-color',
                    ],
                    [
                        'element'   => '.bg-container',
                        'property'  => 'border-color',
                    ],
                    [
                        'element'   => ':root',
                        'property'  => '--content-color',
                    ]
                ],
            ]
        );

        Kirki::add_field('justg_config', [
            'type'        => 'background',
            'settings'    => 'background_themewebsite',
            'label'       => __('Website Background', 'justg'),
            'description' => esc_html__('', 'justg'),
            'section'     => 'section_colorvelocity',
            'default'     => [
                'background-color'      => 'rgba(255,255,255)',
                'background-image'      => '',
                'background-repeat'     => 'repeat',
                'background-position'   => 'center center',
                'background-size'       => 'cover',
                'background-attachment' => 'scroll',
            ],
            'transport'   => 'auto',
            'output'      => [
                [
                    'element'   => ':root[data-bs-theme=light] body',
                ],
                [
                    'element'   => 'body',
                ],
            ],
        ]);

        ///Section Typography
        Kirki::add_section('section_typography', [
            'panel'    => 'panel_toko21',
            'title'    => __('Typography', 'justg'),
            'priority' => 10,
        ]);
        new \Kirki\Field\Typography(
            [
                'settings'    => 'typography_setting',
                'label'       => esc_html__('Typography', 'velocity'),
                'description' => esc_html__('Just the font-family and font-weight.', 'velocity'),
                'section'     => 'section_typography',
                'priority'    => 10,
                'transport'   => 'auto',
                'default'     => [
                    'font-family'     => 'Roboto',
                    'variant'         => 'regular',
                    'font-style'      => 'normal',
                    'color'           => '#ffffff',
                    'font-size'       => '14px',
                    'line-height'     => '1.5',
                    'letter-spacing'  => '0',
                    'text-transform'  => 'none',
                    'text-decoration' => 'none',
                    'text-align'      => 'left',
                ],
                'output'      => [
                    [
                        'element' => ':root',
                        'property' => '--color-main',
                    ],
                    [
                        'element' => '.velocity-judul,#primary-menu>li>a, .nav-link, .widget-title, .text-colortheme, .text-colortheme i, .page-link',
                        'property' => 'color',
                    ],
                ],
            ]
        );

        ///Section Color
        Kirki::add_section('section_slider', [
            'panel'    => 'panel_toko21',
            'title'    => __('Slider Home', 'justg'),
            'priority' => 10,
        ]);
        new \Kirki\Field\Repeater(
            [
                'settings' => 'slider_repeat',
                'label'    => esc_html__('Slider Home', 'justg'),
                'section'  => 'section_slider',
                'priority' => 10,
                'row_label'    => [
                    'type'  => 'field',
                    'value' => esc_html__('Slider', 'justg'),
                ],
                'button_label' => esc_html__('"Add Slider" ', 'justg'),
                'fields'   => [
                    'imgslider'   => [
                        'type'        => 'image',
                        'label'       => esc_html__('Slider', 'justg'),
                        'description' => esc_html__('', 'justg'),
                        'default'     => '',
                    ],
                ],
            ]
        );

        // section velocity_news_section
        Kirki::add_section('velocity_news_section', [
            'panel'    => 'panel_toko21',
            'title'    => __('Velocity Home News', 'justg'),
            'priority' => 10,
        ]);
        new \Kirki\Field\Text(
            [
                'settings' => 'velocity_judul_news',
                'label'    => esc_html__('Judul', 'justg'),
                'section'  => 'velocity_news_section',
                'default'  => esc_html__('', 'justg'),
                'priority' => 10,
            ]
        );
        new \Kirki\Field\Select(
            [
                'settings'    => 'velocity_news',
                'label'       => esc_html__('Pilih Kategori:', 'justg'),
                'section'     => 'velocity_news_section',
                'default'     => '',
                'placeholder' => esc_html__('Pilih Kategori', 'justg'),
                'choices'   => velocity_categories(),
            ]
        );

        // remove panel in customizer 
        Kirki::remove_panel('global_panel');
        Kirki::remove_panel('panel_header');
        Kirki::remove_panel('panel_footer');
        Kirki::remove_panel('panel_antispam');
    // Kirki::remove_control('custom_logo');

    endif;

    //remove action from Parent Theme
    remove_action('justg_header', 'justg_header_menu');
    remove_action('justg_do_footer', 'justg_the_footer_open');
    remove_action('justg_do_footer', 'justg_the_footer_content');
    remove_action('justg_do_footer', 'justg_the_footer_close');
    remove_theme_support('widgets-block-editor');
}


if (!function_exists('justg_header_open')) {
    function justg_header_open()
    {
        echo '<header id="wrapper-header">';
        echo '<div id="wrapper-navbar" class="wrapper-fluid wrapper-navbar position-relative pb-0 p-md-0 p-2" itemscope itemtype="http://schema.org/WebSite">';
    }
}
if (!function_exists('justg_header_close')) {
    function justg_header_close()
    {
        echo '</div>';
        echo '</header>';
    }
}


///add action builder part
add_action('justg_header', 'justg_header_toko');
function justg_header_toko()
{
    require_once(get_stylesheet_directory() . '/inc/part-header.php');
}

add_action('justg_do_footer', 'justg_footer_toko');
function justg_footer_toko()
{
    do_action('justg_the_footer_content');
    require_once(get_stylesheet_directory() . '/inc/part-footer.php');
}
add_action('justg_before_wrapper_content', 'justg_before_wrapper_content');
function justg_before_wrapper_content()
{
    echo '<div class="px-2">';
    echo '<div class="card bg-container text-white rounded-0 border-0 shadow p-2 container">';
}
add_action('justg_after_wrapper_content', 'justg_after_wrapper_content');
function justg_after_wrapper_content()
{
    echo '</div>';
    echo '</div>';
}

if (!function_exists('justg_left_sidebar_check')) {
    function justg_left_sidebar_check()
    {
        echo '<div class="collapse collapse-horizontal overflow-hidden" id="sidebar">';
        echo do_shortcode('[vtoko-list-taxonomy]');
        echo '</div>';
    }
}


// excerpt more
add_filter('excerpt_more', 'velocity_custom_excerpt_more');
if (!function_exists('velocity_custom_excerpt_more')) {
    function velocity_custom_excerpt_more($more)
    {
        return '...';
    }
}

// excerpt length
add_filter('excerpt_length', 'velocity_excerpt_length');
function velocity_excerpt_length($length)
{
    return 20;
}

if (!function_exists('justg_right_sidebar_check')) {
    function justg_right_sidebar_check()
    {
        if (is_singular('fl-builder-template')) {
            return;
        }
        if (!is_active_sidebar('main-sidebar')) {
            return;
        }
        // if (is_singular('product')) {
        //     return;
        // }
        if (is_tax(array('merk', 'category-product'))) {
            echo '<div class="right-sidebar widget-area pe-md-2 col-sm-12 col-md-3 order-md-1 order-4" id="right-sidebar" role="complementary">';
            echo '<aside class="mb-3 d-none d-md-block">';
            echo get_velocitytoko_part('public/templates/filter');
            echo '</aside>';
            echo '</div>';
            return;
        }
        echo '<div class="right-sidebar velocity-widget widget-area px-md-0 col-sm-12 col-md-3 order-md-1 order-3" id="right-sidebar" role="complementary">';
        do_action('justg_before_main_sidebar');
        dynamic_sidebar('main-sidebar');
        do_action('justg_after_main_sidebar');
        echo '</div>';
    }
}

function velocity_title()
{
    if (is_single() || is_page()) {
        return the_title('<h1 class="h4 fw-bold text-uppercase velocity-postheader velocity-judul colortheme">', '</h1>');
    } elseif (is_archive()) {
        return the_archive_title('<h1 class="h4 fw-bold text-uppercase velocity-postheader velocity-judul colortheme">', '</h1>');
        return category_description();
    } elseif (is_tag()) {
        return '<h1 class="h4 fw-bold text-uppercase velocity-postheader velocity-judul colortheme">' . single_tag_title('', false) . '</h1>';
    } elseif (is_day()) {
        return '<h1 class="h4 fw-bold text-uppercase velocity-postheader velocity-judul colortheme">' . sprintf(__('Daily Archives: <span>%s</span>', THEME_NS), get_the_date()) . '</h1>';
    } elseif (is_month()) {
        return '<h1 class="h4 fw-bold text-uppercase velocity-postheader velocity-judul colortheme">' . sprintf(__('Monthly Archives: <span>%s</span>', THEME_NS), get_the_date('F Y')) . '</h1>';
    } elseif (is_year()) {
        return '<h1 class="h4 fw-bold text-uppercase velocity-postheader velocity-judul colortheme">' . sprintf(__('Yearly Archives: <span>%s</span>', THEME_NS), get_the_date('Y')) . '</h1>';
    } elseif (is_tax()) {
        $object = get_queried_object();
        return '<h1 class="h4 fw-bold text-uppercase velocity-postheader velocity-judul colortheme">' . $object->name . '</h1>';
    } elseif (is_post_type_archive()) {
        $object = get_queried_object();
        return '<h1 class="h4 fw-bold text-uppercase velocity-postheader velocity-judul colortheme">' . $object->label . '</h1>';
    } elseif (is_author()) {
        //the_post();
        return '<h1 class="h4 fw-bold text-uppercase velocity-postheader velocity-judul colortheme">' . get_the_author() . '</h1>';
        //rewind_posts();
    } elseif (isset($_GET['paged']) && !empty($_GET['paged'])) {
        return '<h1 class="h4 fw-bold text-uppercase velocity-postheader velocity-judul colortheme">Blog Archives</h1>';
    } elseif (is_search()) {
        return '<h1 class="h4 fw-bold text-uppercase velocity-postheader velocity-judul colortheme">Search Results for: "' . get_search_query() . '"</h1>';
    }
}

// archive product
remove_action('velocitytoko_product_loop', 'velocitytoko_content_product', 20);
add_action('velocitytoko_product_loop', 'velocitytoko_content_products', 30);
function velocitytoko_content_products($post)
{
    $title = wp_trim_words(get_the_title(), '5');
?>
    <article <?php post_class('col-md-3 col-6 p-2 mb-3'); ?> id="post-<?php the_ID(); ?>">
        <div class="card rounded-0 h-100 card-product">
            <?php echo do_shortcode("[thumbnail width='310' height='290' crop='false' upscale='true']");
            ?>
            <div class="p-3">
                <div class="my-2 text-center">
                    <a class="fw-bold text-white" href="<?php echo get_the_permalink(); ?>"><?php echo $title; ?></a>
                </div>
                <div class="my-2 text-center fw-bold"><?php echo do_shortcode("[harga]"); ?></div>
                <div class="row">
                    <div class="col-6 p-1 text-start"><a href="<?php the_permalink(); ?>" class="p-1 btn btn-sm bg-colortheme text-white w-100"><i class="fa fa-info-circle"></i></a></div>
                    <div class="col-6 p-1 text-end">
                        <span class="cart-arsip p-1 w-100 btn btn-sm bg-colortheme"><?php echo do_shortcode("[beli]"); ?></span>
                    </div>
                </div>
            </div>
        </div>
    </article>
<?php
}

// single product
remove_action('velocitytoko_content_single_product', 'velocitytoko_content_single_product', 20);
add_action('velocitytoko_content_single_product', 'velocitytoko_content_single_products', 30);
function velocitytoko_content_single_products($post)
{
    echo do_shortcode('[vd-breadcrumbs]'); ?>
    <article <?php post_class(); ?> id="post-<?php the_ID(); ?>">

        <div class="single-produk block-primary">
            <div class="row">
                <div class="col-md-6 col-xl-5">
                    <?php echo do_shortcode('[slider-produk width="350" height="350"]'); ?>
                </div>
                <div class="col-md">
                    <h1 class="fs-4 fw-bold mb-3">
                        <?php echo do_shortcode("[vtoko-title link='true' class='text-white ' node-cart='cartsingle']"); ?>
                    </h1>
                    <div class="mb-2">
                        <small>
                            Kategori: <?php echo velocitytoko_term_list('category-product', ",", get_the_ID()); ?>
                            | Dilihat: <?php echo do_shortcode('[view]'); ?>
                        </small>
                    </div>
                    <div class="text-white mb-3">Harga: <?php echo do_shortcode('[harga node-cart="cartsingle"]'); ?></div>
                    <div class="mb-3"><?php echo do_shortcode('[detail-produk]'); ?></div>
                    <div class="mb-3"><?php echo do_shortcode('[beli modal="false" node-cart="cartsingle" text="true"]'); ?></div>
                    <div class="mb-3"><?php echo do_shortcode('[love text="true"]'); ?></div>
                    <div class="mb-3"><?php echo do_shortcode('[beli-lain]'); ?></div>
                    <div class="text-white mb-3"><?php echo do_shortcode('[share]'); ?></div>
                </div>
            </div>
        </div>

        <div class="block-primary">
            <h3 class="title-single-part">Detail Produk</h3>
            <div><?php echo get_the_content(); ?></div>
        </div>

    </article><!-- #post-## -->
<?php
}
