<?php
/**
 * Integrasi plugin VD Store (post type store_product).
 *
 * Toko 21 aslinya dibuat untuk plugin Velocity Toko (post type `product`,
 * shortcode [harga] [beli] [cart] ...). Situs baru memakai VD Store, jadi
 * semua bagian toko punya jalur VD Store di sini. Selama VD Store tidak aktif,
 * tema tetap memakai Velocity Toko seperti sebelumnya.
 *
 * @package justg
 */

defined('ABSPATH') || exit;

/**
 * True kalau VD Store aktif.
 */
function velocity_toko21_vd_store()
{
    return defined('WP_STORE_VERSION');
}

/**
 * Post type produk yang dipakai situs ini.
 */
function velocity_toko21_post_type_produk()
{
    return velocity_toko21_vd_store() ? 'store_product' : 'product';
}

/**
 * CSS penyesuaian VD Store (warna tema, kartu & detail produk). Warna utama
 * toko ikut warna utama tema (Customizer > primary color).
 */
add_action('wp_enqueue_scripts', function () {
    if (!velocity_toko21_vd_store()) {
        return;
    }
    $berkas = get_stylesheet_directory() . '/css/vd-store.css';
    wp_enqueue_style('velocity-toko21-vd-store', get_stylesheet_directory_uri() . '/css/vd-store.css', ['custom-style'], wp_get_theme()->get('Version') . '.' . filemtime($berkas));
}, 30);

/**
 * Pencarian situs = pencarian produk: hasilnya tampil di arsip produk VD Store
 * (vd-store/archive-store_product.php, kartu produk Toko 21). Prioritas 5 supaya
 * jalan sebelum VD Store menyusun query arsipnya (pre_get_posts prioritas 10).
 */
add_action('pre_get_posts', function ($query) {
    if (is_admin() || !$query->is_main_query() || !$query->is_search() || !velocity_toko21_vd_store()) {
        return;
    }
    if (!$query->get('post_type')) {
        $query->set('post_type', 'store_product');
    }
}, 5);

/**
 * Kartu produk arsip & beranda (markup sama dengan velocitytoko_content_products).
 */
function velocity_toko21_kartu_produk()
{
    $title = wp_trim_words(get_the_title(), 5);
?>
    <article <?php post_class('col-md-3 col-6 p-2 mb-3'); ?> id="post-<?php the_ID(); ?>">
        <div class="card rounded-0 h-100 card-product">
            <a href="<?php the_permalink(); ?>">
                <?php echo do_shortcode('[wp_store_thumbnail width="310" height="290" crop="true"]'); ?>
            </a>
            <div class="p-3">
                <div class="my-2 text-center">
                    <a class="fw-bold text-white" href="<?php the_permalink(); ?>"><?php echo esc_html($title); ?></a>
                </div>
                <div class="my-2 text-center fw-bold"><?php echo do_shortcode('[wp_store_price]'); ?></div>
                <div class="row">
                    <div class="col-6 p-1 text-start">
                        <a href="<?php the_permalink(); ?>" class="p-1 btn btn-sm bg-colortheme text-white w-100" aria-label="Detail produk">
                            <?php echo velocity_toko21_ikon('info'); ?>
                        </a>
                    </div>
                    <div class="col-6 p-1 text-end">
                        <span class="cart-arsip p-1 w-100 btn btn-sm bg-colortheme">
                            <?php echo do_shortcode('[wp_store_add_to_cart text="" class="velocity-toko21-beli"]'); ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </article>
<?php
}

/**
 * Grid produk dari query yang sedang berjalan.
 */
function velocity_toko21_grid_produk($query = null)
{
    $query = $query ?: $GLOBALS['wp_query'];
    echo '<div class="row m-0">';
    while ($query->have_posts()) {
        $query->the_post();
        velocity_toko21_kartu_produk();
    }
    echo '</div>';
    wp_reset_postdata();
}

/**
 * Daftar kategori produk (pengganti [vtoko-list-taxonomy], markup & kelas sama).
 */
function velocity_toko21_list_kategori($taxonomy = 'store_product_cat')
{
    $induk = get_terms(['taxonomy' => $taxonomy, 'parent' => 0, 'hide_empty' => false]);
    if (is_wp_error($induk) || !$induk) {
        return '';
    }
    $node = uniqid();
    ob_start();
?>
    <div class="vtoko-list-taxonomy" data-taxonomy="<?php echo esc_attr($taxonomy); ?>">
        <div class="list-group list-group-flush">
            <?php foreach ($induk as $tax) :
                $anak = get_terms(['taxonomy' => $taxonomy, 'parent' => $tax->term_id, 'hide_empty' => false]);
                $anak = is_wp_error($anak) ? [] : $anak;
                $id = $node . '-' . $tax->term_id; ?>
                <div class="list-group-item list-group-item-action px-1">
                    <a href="<?php echo esc_url(get_term_link($tax)); ?>"><?php echo esc_html($tax->name); ?></a>
                    <?php if ($anak) : ?>
                        <span class="float-end collapse-icon-toggle" data-bs-toggle="collapse" data-bs-target="#child-list-<?php echo esc_attr($id); ?>" aria-expanded="false" aria-controls="child-list-<?php echo esc_attr($id); ?>"></span>
                    <?php endif; ?>
                </div>
                <?php if ($anak) : ?>
                    <div id="child-list-<?php echo esc_attr($id); ?>" class="list-group list-group-flush collapse">
                        <?php foreach ($anak as $sub) : ?>
                            <div class="list-group-item ps-4 pe-3">
                                <a href="<?php echo esc_url(get_term_link($sub)); ?>"><?php echo esc_html($sub->name); ?></a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </div>
<?php
    return ob_get_clean();
}

/**
 * Kontak toko di header (pengganti [kontak style="false"]): data dari
 * pengaturan VD Store, tampilan tombol link berikon seperti Velocity Toko.
 */
function velocity_toko21_kontak()
{
    $s = get_option('wp_store_settings', []);
    $nomor = function ($n, $awal) {
        $n = trim((string) $n);
        return ($n !== '' && $n[0] === '0') ? $awal . substr($n, 1) : $n;
    };
    $pesan = rawurlencode('Hallo ' . get_bloginfo('name'));
    $sms = $nomor($s['store_sms'] ?? '', '+62');
    $tlp = $nomor($s['store_phone'] ?? '', '+62');
    $wa = preg_replace('/\D/', '', $nomor($s['store_wa'] ?? '', '62'));
    $tg = trim((string) ($s['store_telegram'] ?? ''));
    $email = trim((string) ($s['store_email'] ?? ''));
    $tombol = [
        ['sms', $sms, 'sms:' . $sms . '?body=' . $pesan, 'chat'],
        ['tlp', $tlp, 'tel:' . $tlp, 'telepon'],
        ['wa', $wa, 'https://wa.me/' . $wa . '?text=' . $pesan, 'whatsapp'],
        ['telegram', $tg, 'https://telegram.me/' . $tg, 'telegram'],
        ['email', $email, 'mailto:' . $email, 'email'],
    ];
    $html = '';
    foreach ($tombol as [$kunci, $isi, $href, $ikon]) {
        if ($isi === '') {
            continue;
        }
        $html .= '<a href="' . esc_url($href) . '" target="_blank" rel="noopener" class="btn btn-sm btn-link">'
            . '<span>' . velocity_toko21_ikon($ikon) . '</span>'
            . '<span class="kontak-caption ms-1">' . esc_html($isi) . '</span></a>';
    }
    return $html;
}

/**
 * "Pemesanan juga dapat melalui" (pengganti [beli-lain]): WhatsApp, SMS, dan
 * telepon toko dari pengaturan VD Store, pesan berisi nama & tautan produk.
 */
function velocity_toko21_beli_lain()
{
    $s = get_option('wp_store_settings', []);
    $wa = preg_replace('/\D/', '', (string) ($s['store_wa'] ?? ''));
    if ($wa !== '' && $wa[0] === '0') {
        $wa = '62' . substr($wa, 1);
    }
    $tlp = trim((string) ($s['store_phone'] ?? ''));
    $sms = trim((string) ($s['store_sms'] ?? '')) ?: $tlp;
    $pesan = rawurlencode('Halo, saya ingin memesan ' . get_the_title() . ' ' . get_permalink());
    $tombol = [];
    if ($wa !== '') {
        $tombol[] = ['https://wa.me/' . $wa . '?text=' . $pesan, 'whatsapp', 'Whatsapp'];
    }
    if ($sms !== '') {
        $tombol[] = ['sms:' . $sms . '?body=' . $pesan, 'chat', 'SMS'];
    }
    if ($tlp !== '') {
        $tombol[] = ['tel:' . $tlp, 'telepon', 'Telp'];
    }
    if (!$tombol) {
        return '';
    }
    $html = '<div class="belilain"><div class="mb-1">Pemesanan Juga dapat melalui :</div>';
    foreach ($tombol as [$href, $ikon, $label]) {
        $html .= '<a href="' . esc_url($href) . '" target="_blank" rel="noopener" class="btn btn-sm me-1 mb-1">'
            . velocity_toko21_ikon($ikon) . ' ' . esc_html($label) . '</a>';
    }
    return $html . '</div>';
}

/**
 * Produk lain dari kategori yang sama, dengan kartu Toko 21.
 */
function velocity_toko21_produk_terkait($jumlah = 4)
{
    $kategori = wp_get_post_terms(get_the_ID(), 'store_product_cat', ['fields' => 'ids']);
    $args = [
        'post_type'      => 'store_product',
        'posts_per_page' => $jumlah,
        'post__not_in'   => [get_the_ID()],
        'orderby'        => 'rand',
    ];
    if ($kategori && !is_wp_error($kategori)) {
        $args['tax_query'] = [['taxonomy' => 'store_product_cat', 'terms' => $kategori]];
    }
    $query = new WP_Query($args);
    if (!$query->have_posts()) {
        return;
    }
    echo '<div class="block-primary produk-terkait"><h3 class="title-single-part">Produk Terkait</h3>';
    velocity_toko21_grid_produk($query);
    echo '</div>';
}

/**
 * Ikon profil pelanggan (pengganti [profile]) ke halaman Profil Saya VD Store.
 */
function velocity_toko21_profil()
{
    $s = get_option('wp_store_settings', []);
    $id = isset($s['page_profile']) ? absint($s['page_profile']) : 0;
    $url = $id ? get_permalink($id) : site_url('/profil-saya/');
    return '<span class="linkprofile"><a href="' . esc_url($url) . '" aria-label="Profil">' . velocity_toko21_ikon('profil') . '</a></span>';
}

/**
 * Ikon SVG Bootstrap Icons yang dipakai tema.
 */
function velocity_toko21_ikon($nama)
{
    $jalur = [
        'info'     => '<path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16m.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2"/>',
        'profil'   => '<path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0"/><path fill-rule="evenodd" d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8m8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1"/>',
        'chat'     => '<path d="M2 1a1 1 0 0 0-1 1v8a1 1 0 0 0 1 1h9.586a2 2 0 0 1 1.414.586l2 2V2a1 1 0 0 0-1-1zm12-1a2 2 0 0 1 2 2v12.793a.5.5 0 0 1-.854.353l-2.853-2.853a1 1 0 0 0-.707-.293H2a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2z"/>',
        'telepon'  => '<path d="M3.654 1.328a.678.678 0 0 0-1.015-.063L1.605 2.3c-.483.484-.661 1.169-.45 1.77a17.6 17.6 0 0 0 4.168 6.608 17.6 17.6 0 0 0 6.608 4.168c.601.211 1.286.033 1.77-.45l1.034-1.034a.678.678 0 0 0-.063-1.015l-2.307-1.794a.68.68 0 0 0-.58-.122l-2.19.547a1.75 1.75 0 0 1-1.657-.459L5.482 8.062a1.75 1.75 0 0 1-.46-1.657l.548-2.19a.68.68 0 0 0-.122-.58z"/>',
        'whatsapp' => '<path d="M13.601 2.326A7.85 7.85 0 0 0 7.994 0C3.627 0 .068 3.558.064 7.926c0 1.399.366 2.76 1.057 3.965L0 16l4.204-1.102a7.9 7.9 0 0 0 3.79.965h.004c4.368 0 7.926-3.558 7.93-7.93A7.9 7.9 0 0 0 13.6 2.326zM7.994 14.521a6.6 6.6 0 0 1-3.356-.92l-.24-.144-2.494.654.666-2.433-.156-.251a6.56 6.56 0 0 1-1.007-3.505c0-3.626 2.957-6.584 6.591-6.584a6.56 6.56 0 0 1 4.66 1.931 6.56 6.56 0 0 1 1.928 4.66c-.004 3.639-2.961 6.592-6.592 6.592m3.615-4.934c-.197-.099-1.17-.578-1.353-.646-.182-.065-.315-.099-.445.099-.133.197-.513.646-.627.775-.114.133-.232.148-.43.05-.197-.1-.836-.308-1.592-.985-.59-.525-.985-1.175-1.103-1.372-.114-.198-.011-.304.088-.403.087-.088.197-.232.296-.346.1-.114.133-.198.198-.33.065-.134.034-.248-.015-.347-.05-.099-.445-1.076-.612-1.47-.16-.389-.323-.335-.445-.34-.114-.007-.247-.007-.38-.007a.73.73 0 0 0-.529.247c-.182.198-.691.677-.691 1.654s.71 1.916.81 2.049c.098.133 1.394 2.132 3.383 2.992.47.205.84.326 1.129.418.475.152.904.129 1.246.08.38-.058 1.171-.48 1.338-.943.164-.464.164-.86.114-.943-.049-.084-.182-.133-.38-.232"/>',
        'telegram' => '<path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0M8.287 5.906q-1.168.486-4.666 2.01-.567.225-.595.442c-.03.243.275.339.69.47l.175.055c.408.133.958.288 1.243.294q.39.01.868-.32 3.269-2.206 3.374-2.23c.05-.012.12-.026.166.016s.042.12.037.141c-.03.129-1.227 1.241-1.846 1.817-.193.18-.33.307-.358.336a8 8 0 0 1-.188.186c-.38.366-.664.64.015 1.088.327.216.589.393.85.571.284.194.568.387.936.629q.14.092.27.187c.331.236.63.448.997.414.214-.02.435-.22.547-.82.265-1.417.786-4.486.906-5.751a1.4 1.4 0 0 0-.013-.315.34.34 0 0 0-.114-.217.53.53 0 0 0-.31-.093c-.3.005-.763.166-2.984 1.09"/>',
        'email'    => '<path d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2zm2-1a1 1 0 0 0-1 1v.217l7 4.2 7-4.2V4a1 1 0 0 0-1-1zm13 2.383-4.708 2.825L15 11.105zm-.034 6.876-5.64-3.471L8 9.583l-1.326-.795-5.64 3.47A1 1 0 0 0 2 13h12a1 1 0 0 0 .966-.741M1 11.105l4.708-2.897L1 5.383z"/>',
    ];
    if (!isset($jalur[$nama])) {
        return '';
    }
    return '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16" aria-hidden="true">' . $jalur[$nama] . '</svg>';
}

/**
 * Isi halaman arsip/kategori/merek produk VD Store dengan tampilan Toko 21.
 * Dipanggil dari vd-store/*.php (template override VD Store).
 */
function velocity_toko21_arsip_produk($judul, $kosong = 'Produk belum tersedia.')
{
    get_header();
    $container = velocitytheme_option('justg_container_type', 'container');
?>
    <div class="wrapper" id="archive-wrapper">
        <div class="<?php echo esc_attr($container); ?> p-0" id="content" tabindex="-1">
            <div class="row m-0">
                <?php do_action('justg_before_content'); ?>
                <main class="site-main col order-2" id="main">
                    <?php echo justg_breadcrumb(); ?>
                    <header class="page-header block-primary py-2">
                        <h1 class="page-title"><?php echo esc_html($judul); ?></h1>
                        <?php the_archive_description('<div class="taxonomy-description">', '</div>'); ?>
                    </header>
                    <?php if (have_posts()) :
                        velocity_toko21_grid_produk();
                        justg_pagination();
                    else : ?>
                        <div class="block-primary text-center py-4"><?php echo esc_html($kosong); ?></div>
                    <?php endif; ?>
                </main>
                <?php do_action('justg_after_content'); ?>
            </div>
        </div>
    </div>
<?php
    get_footer();
}
