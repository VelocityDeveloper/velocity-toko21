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
function velocity_toko21_kontak($kelas = 'btn btn-sm btn-link')
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
        $html .= '<a href="' . esc_url($href) . '" target="_blank" rel="noopener" class="' . esc_attr($kelas) . '">'
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


/* ---------- Widget sidebar ala toko21.velocitydeveloper.com ---------- */

/**
 * Susunan widget sidebar acuan Toko 21 (demo toko21.velocitydeveloper.com): judul + isi.
 * Dibaca installer (scripts/theme-paket-biasa) lewat velocity_tema_widget_sidebar() saat
 * tema ini aktif, jadi situs baru bertema Toko 21 langsung bersidebar seperti demo.
 */
function velocity_tema_widget_sidebar()
{
    if (!velocity_toko21_vd_store()) {
        return [];
    }
    return [
        ['Kategori', '[toko21_kategori]'],
        ['Cari Produk', '[toko21_cari_produk]'],
        ['Bank Pembayaran', '[toko21_bank]'],
        ['Ekspedisi', '[toko21_ekspedisi]'],
        ['Sosial Media', '[toko21_sosmed]'],
        ['Hubungi Kami', '[toko21_kontak]'],
    ];
}

/**
 * Widget footer acuan Toko 21: KOSONG, persis demo toko21.velocitydeveloper.com — footer hanya
 * baris hak cipta + "Design by Velocity Developer" dari inc/part-footer.php (keputusan user
 * 2026-09-24). Installer mengosongkan footer-widget-1..4 untuk tema ini.
 */
function velocity_tema_widget_footer()
{
    return [];
}

add_action('init', function () {
    if (!velocity_toko21_vd_store()) {
        return;
    }
    add_shortcode('toko21_kategori', function () {
        return velocity_toko21_list_kategori();
    });

    // Cari produk: kata kunci + kategori, hasil di arsip produk (kartu produk).
    add_shortcode('toko21_cari_produk', function () {
        $kat = get_terms(['taxonomy' => 'store_product_cat', 'hide_empty' => false, 'parent' => 0]);
        $pilih = isset($_GET['cat']) ? absint($_GET['cat']) : 0;
        $html = '<div class="vtoko-search-product" data-layout="stacked"><form action="' . esc_url(get_post_type_archive_link('store_product')) . '" method="get">'
            . '<div class="form-stacked"><div class="form-group mb-2"><input type="text" name="s" class="form-control" placeholder="Cari Produk.." value="' . esc_attr(get_search_query()) . '"></div>';
        if ($kat && !is_wp_error($kat)) {
            $html .= '<div class="form-group mb-2"><select name="cat" class="form-control"><option value="">Semua Kategori</option>';
            foreach ($kat as $k) {
                $html .= '<option value="' . (int) $k->term_id . '"' . selected($pilih, $k->term_id, false) . '>' . esc_html($k->name) . '</option>';
            }
            $html .= '</select></div>';
        }
        return $html . '<div class="form-group mb-2 text-center"><button type="submit" class="btn btn-primary bg-colortheme">'
            . '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16" aria-hidden="true"><path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0"/></svg> Cari'
            . '</button></div></div></form></div>';
    });

    // Bank pembayaran dari pengaturan VD Store: logo, nomor, atas nama.
    add_shortcode('toko21_bank', function () {
        $s = get_option('wp_store_settings', []);
        $html = '';
        foreach ((array) ($s['store_bank_accounts'] ?? []) as $acc) {
            if (empty($acc['bank_name'])) {
                continue;
            }
            $nama = function_exists('wp_store_bank_account_name') ? wp_store_bank_account_name($acc) : (string) $acc['bank_name'];
            $logo = function_exists('wp_store_bank_account_logo') ? wp_store_bank_account_logo($acc) : '';
            $html .= '<span class="d-inline-block mb-2" data-bank="' . esc_attr(sanitize_title($nama)) . '">';
            $html .= $logo ? '<img style="display:block;margin:0 auto;background:#fff;padding:4px;border-radius:3px" width="100" alt="' . esc_attr($nama) . '" src="' . esc_url($logo) . '">'
                           : '<strong class="d-block">' . esc_html($nama) . '</strong>';
            if (!empty($acc['bank_account'])) {
                $html .= '<span>' . esc_html($acc['bank_account']) . (empty($acc['bank_holder']) ? '' : '<br><small>a/n ' . esc_html($acc['bank_holder']) . '</small>') . '</span>';
            }
            $html .= '</span>';
        }
        return $html ? '<div class="frame-bank" style="text-align:center">' . $html . '</div>' : '';
    });

    // Logo kurir aktif VD Store (tanpa pengaturan: bawaan VD Store jne, sicepat, ide).
    add_shortcode('toko21_ekspedisi', function () {
        $s = get_option('wp_store_settings', []);
        $kurir = !empty($s['shipping_couriers']) ? (array) $s['shipping_couriers'] : ['jne', 'sicepat', 'ide'];
        $label = function_exists('wp_store_courier_labels') ? wp_store_courier_labels() : [];
        $html = '';
        foreach ($kurir as $kode) {
            $kode = sanitize_key($kode);
            if (!defined('WP_STORE_PATH') || !file_exists(WP_STORE_PATH . 'assets/frontend/img/ekspedisi/' . $kode . '.webp')) {
                continue;
            }
            $nama = $label[$kode] ?? strtoupper($kode);
            $html .= '<span class="col-6 logo-ekspedisi text-center mb-2"><img src="' . esc_url(WP_STORE_URL . 'assets/frontend/img/ekspedisi/' . $kode . '.webp')
                . '" alt="' . esc_attr($nama) . '" title="' . esc_attr($nama) . '" class="img-fluid" loading="lazy"></span>';
        }
        return $html ? '<div class="vtoko-ekspedisi"><div class="row">' . $html . '</div></div>' : '';
    });

    // Kartu media sosial berwarna. Atribut facebook/twitter/instagram/youtube = URL akun.
    add_shortcode('toko21_sosmed', function ($atts) {
        $atts = shortcode_atts(['facebook' => 'https://www.facebook.com/', 'twitter' => 'https://x.com/',
                                'instagram' => 'https://www.instagram.com/', 'youtube' => 'https://www.youtube.com/'], $atts);
        $gaya = [
            'facebook'  => ['#475A95', 'Find us on', 'Facebook', '<path d="M16 8.049c0-4.446-3.582-8.05-8-8.05C3.58 0-.002 3.603-.002 8.05c0 4.017 2.926 7.347 6.75 7.951v-5.625h-2.03V8.05H6.75V6.275c0-2.017 1.195-3.131 3.022-3.131.876 0 1.791.157 1.791.157v1.98h-1.009c-.993 0-1.303.621-1.303 1.258v1.51h2.218l-.354 2.326H9.25V16c3.824-.604 6.75-3.934 6.75-7.951"/>'],
            'twitter'   => ['#000000', 'Follow us on', 'Twitter', '<path d="M12.6.75h2.454l-5.36 6.142L16 15.25h-4.937l-3.867-5.07-4.425 5.07H.316l5.733-6.57L0 .75h5.063l3.495 4.633L12.601.75Zm-.86 13.028h1.36L4.323 2.145H2.865z"/>'],
            'instagram' => ['#C43FBD', 'Follow us on', 'Instagram', '<path d="M8 0C5.829 0 5.556.01 4.703.048 3.85.088 3.269.222 2.76.42a3.9 3.9 0 0 0-1.417.923A3.9 3.9 0 0 0 .42 2.76C.222 3.268.087 3.85.048 4.7.01 5.555 0 5.827 0 8.001c0 2.172.01 2.444.048 3.297.04.852.174 1.433.372 1.942.205.526.478.972.923 1.417.444.445.89.719 1.416.923.51.198 1.09.333 1.942.372C5.555 15.99 5.827 16 8 16s2.444-.01 3.298-.048c.851-.04 1.434-.174 1.943-.372a3.9 3.9 0 0 0 1.416-.923c.445-.445.718-.891.923-1.417.197-.509.332-1.09.372-1.942C15.99 10.445 16 10.173 16 8s-.01-2.445-.048-3.299c-.04-.851-.175-1.433-.372-1.941a3.9 3.9 0 0 0-.923-1.417A3.9 3.9 0 0 0 13.24.42c-.51-.198-1.092-.333-1.943-.372C10.443.01 10.172 0 7.998 0zm-.717 1.442h.718c2.136 0 2.389.007 3.232.046.78.035 1.204.166 1.486.275.373.145.64.319.92.599s.453.546.598.92c.11.281.24.705.275 1.485.039.843.047 1.096.047 3.231s-.008 2.389-.047 3.232c-.035.78-.166 1.203-.275 1.485a2.5 2.5 0 0 1-.599.919c-.28.28-.546.453-.92.598-.28.11-.704.24-1.485.276-.843.038-1.096.047-3.232.047s-2.39-.009-3.233-.047c-.78-.036-1.203-.166-1.485-.276a2.5 2.5 0 0 1-.92-.598 2.5 2.5 0 0 1-.6-.92c-.109-.281-.24-.705-.275-1.485-.038-.843-.046-1.096-.046-3.233s.008-2.388.046-3.231c.036-.78.166-1.204.276-1.486.145-.373.319-.64.599-.92s.546-.453.92-.598c.282-.11.705-.24 1.485-.276.738-.034 1.024-.044 2.515-.045zm4.988 1.328a.96.96 0 1 0 0 1.92.96.96 0 0 0 0-1.92m-4.27 1.122a4.109 4.109 0 1 0 0 8.217 4.109 4.109 0 0 0 0-8.217m0 1.441a2.667 2.667 0 1 1 0 5.334 2.667 2.667 0 0 1 0-5.334"/>'],
            'youtube'   => ['#E93E3C', 'Subscribe us on', 'Youtube', '<path d="M8.051 1.999h.089c.822.003 4.987.033 6.11.335a2.01 2.01 0 0 1 1.415 1.42c.101.38.172.883.22 1.402l.01.104.022.26.008.104c.065.914.073 1.77.074 1.957v.075c-.001.194-.01 1.108-.082 2.06l-.008.105-.009.104c-.05.572-.124 1.14-.235 1.558a2.01 2.01 0 0 1-1.415 1.42c-1.16.312-5.569.334-6.18.335h-.142c-.309 0-1.587-.006-2.927-.052l-.17-.006-.087-.004-.171-.007-.171-.007c-1.11-.049-2.167-.128-2.654-.26a2.01 2.01 0 0 1-1.415-1.419c-.111-.417-.185-.986-.235-1.558L.09 9.82l-.008-.104A31 31 0 0 1 0 7.68v-.123c.002-.215.01-.958.064-1.778l.007-.103.003-.052.008-.104.022-.26.01-.104c.048-.519.119-1.023.22-1.402a2.01 2.01 0 0 1 1.415-1.42c.487-.13 1.544-.21 2.654-.26l.17-.007.172-.006.086-.003.171-.007A100 100 0 0 1 7.858 2zM6.4 5.209v4.818l4.157-2.408z"/>'],
        ];
        $html = '';
        foreach ($gaya as $kunci => [$warna, $ajakan, $nama, $ikon]) {
            if (empty($atts[$kunci])) {
                continue;
            }
            $html .= '<a href="' . esc_url($atts[$kunci]) . '" target="_blank" rel="noopener" class="vtoko-sosmed-item text-white d-block mb-2 px-3 py-2 ' . $kunci
                . '" style="background-color:' . $warna . ' !important;text-decoration:none"><div class="row align-items-center"><div class="col-3 text-center">'
                . '<svg xmlns="http://www.w3.org/2000/svg" width="35" height="35" fill="currentColor" viewBox="0 0 16 16" aria-hidden="true">' . $ikon . '</svg></div>'
                . '<div class="col"><div>' . esc_html($ajakan) . '</div><div class="h4 m-0 text-white">' . esc_html($nama) . '</div></div></div></a>';
        }
        return '<div class="vtoko-sosmed">' . $html . '</div>';
    });

    add_shortcode('toko21_kontak', function () {
        return '<div class="kontak-widget">' . velocity_toko21_kontak('btn-sm d-block mb-1 btn btn-outline-dark') . '</div>';
    });
});
