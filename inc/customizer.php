<?php
/**
 * Pengaturan Toko 21 di Customizer bawaan WordPress (tanpa Kirki).
 *
 * Nama theme mod sama dengan versi Kirki (color_content, typography_setting,
 * velocity_judul_news, velocity_news) supaya nilai yang sudah tersimpan tetap
 * terbaca. Slider Kirki (repeater slider_repeat) diganti slot gambar
 * slider_image_1..N; data slider_repeat lama tetap dipakai selama slot kosong.
 *
 * @package justg
 */

defined('ABSPATH') || exit;

const VELOCITY_TOKO21_SLIDER_SLOT = 5;

add_action('customize_register', function ($wp_customize) {
    $wp_customize->add_panel('panel_toko21', [
        'priority' => 10,
        'title'    => __('Setting Toko 21', 'justg'),
    ]);

    // Warna
    $wp_customize->add_section('section_colorvelocity', [
        'panel'    => 'panel_toko21',
        'title'    => __('Warna', 'justg'),
        'priority' => 10,
    ]);
    $wp_customize->add_setting('color_content', [
        'default'           => '#343a40',
        'sanitize_callback' => 'sanitize_hex_color',
    ]);
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'color_content', [
        'label'       => __('Warna Konten', 'justg'),
        'description' => __('Warna latar area konten/container.', 'justg'),
        'section'     => 'section_colorvelocity',
    ]));
    $wp_customize->add_setting('velocity_toko21_warna_menu', [
        'default'           => velocity_toko21_warna_menu_lama(),
        'sanitize_callback' => 'sanitize_hex_color',
    ]);
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'velocity_toko21_warna_menu', [
        'label'       => __('Warna Teks Menu & Judul Widget', 'justg'),
        'section'     => 'section_colorvelocity',
    ]));

    $wp_customize->add_setting('velocity_toko21_menu_aktif', [
        'default'           => '',
        'sanitize_callback' => 'sanitize_hex_color',
    ]);
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'velocity_toko21_menu_aktif', [
        'label'       => __('Warna Latar Menu Hover/Aktif', 'justg'),
        'description' => __('Latar item menu atas saat disorot dan pada halaman yang sedang dibuka. Kosong = tanpa latar.', 'justg'),
        'section'     => 'section_colorvelocity',
    ]));

    // Slider beranda
    $wp_customize->add_section('section_slider', [
        'panel'       => 'panel_toko21',
        'title'       => __('Slider Home', 'justg'),
        'description' => __('Gambar slider di halaman ber-template Home. Slot kosong dilewati.', 'justg'),
        'priority'    => 20,
    ]);
    for ($i = 1; $i <= VELOCITY_TOKO21_SLIDER_SLOT; $i++) {
        $wp_customize->add_setting("slider_image_$i", [
            'default'           => '',
            'sanitize_callback' => 'esc_url_raw',
        ]);
        $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, "slider_image_$i", [
            'label'   => sprintf(__('Slider %d', 'justg'), $i),
            'section' => 'section_slider',
        ]));
    }

    // Berita beranda
    $wp_customize->add_section('velocity_news_section', [
        'panel'    => 'panel_toko21',
        'title'    => __('Velocity Home News', 'justg'),
        'priority' => 30,
    ]);
    $wp_customize->add_setting('velocity_judul_news', [
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
    ]);
    $wp_customize->add_control('velocity_judul_news', [
        'label'   => __('Judul', 'justg'),
        'section' => 'velocity_news_section',
        'type'    => 'text',
    ]);
    $wp_customize->add_setting('velocity_news', [
        'default'           => '',
        'sanitize_callback' => 'absint',
    ]);
    $wp_customize->add_control('velocity_news', [
        'label'   => __('Pilih Kategori:', 'justg'),
        'section' => 'velocity_news_section',
        'type'    => 'select',
        'choices' => velocity_categories(),
    ]);
});

/**
 * Warna teks menu dari pengaturan Typography versi Kirki (bawaan #ffffff).
 */
function velocity_toko21_warna_menu_lama()
{
    $tipografi = get_theme_mod('typography_setting');
    $warna = is_array($tipografi) ? sanitize_hex_color($tipografi['color'] ?? '') : '';
    return $warna ?: '#ffffff';
}

/**
 * Komponen RGB dari warna hex (#rgb atau #rrggbb).
 */
function velocity_toko21_rgb($hex)
{
    $hex = ltrim($hex, '#');
    if (strlen($hex) === 3) {
        $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
    }
    return array_map('hexdec', str_split($hex, 2));
}

/**
 * Campur warna hex dengan putih sebanyak $porsi (0..1).
 */
function velocity_toko21_campur_putih($hex, $porsi)
{
    return vsprintf('#%02x%02x%02x', array_map(function ($v) use ($porsi) {
        return (int) round($v + (255 - $v) * $porsi);
    }, velocity_toko21_rgb($hex)));
}

function velocity_toko21_kecerahan($hex)
{
    $l = array_map(function ($v) {
        $v /= 255;
        return $v <= .03928 ? $v / 12.92 : pow(($v + .055) / 1.055, 2.4);
    }, velocity_toko21_rgb($hex));
    return .2126 * $l[0] + .7152 * $l[1] + .0722 * $l[2];
}

/**
 * Warna utama untuk TEKS di latar konten gelap: warna utama (misal hijau logo
 * #2a9630) sering tak terbaca di latar #343a40, jadi diterangkan bertahap sampai
 * kontrasnya minimal 4.5:1 (WCAG AA). Latar/tombol tetap memakai warna utama asli.
 */
function velocity_toko21_warna_terbaca($warna, $latar)
{
    $l_latar = velocity_toko21_kecerahan($latar);
    for ($i = 0; $i <= 20; $i++) {
        $hasil = velocity_toko21_campur_putih($warna, $i / 20);
        $l = velocity_toko21_kecerahan($hasil);
        if ((max($l, $l_latar) + .05) / (min($l, $l_latar) + .05) >= 4.5) {
            return $hasil;
        }
    }
    return '#ffffff';
}

/**
 * URL gambar slider beranda: slot Customizer, atau data slider Kirki lama.
 */
function velocity_toko21_slider()
{
    $gambar = [];
    for ($i = 1; $i <= VELOCITY_TOKO21_SLIDER_SLOT; $i++) {
        $url = get_theme_mod("slider_image_$i", '');
        if ($url) {
            $gambar[] = $url;
        }
    }
    if (!$gambar) {
        foreach ((array) get_theme_mod('slider_repeat', []) as $baris) {
            $url = is_array($baris) ? ($baris['imgslider'] ?? '') : '';
            // Kirki bisa menyimpan id lampiran, bukan URL.
            if (is_numeric($url)) {
                $url = wp_get_attachment_url((int) $url);
            }
            if ($url) {
                $gambar[] = $url;
            }
        }
    }
    return $gambar;
}

/**
 * CSS dari pengaturan di atas. Dicetak di akhir <head> seperti Kirki dulu, supaya
 * menang atas CSS Bootstrap tema induk (.card memberi latar putih pada .bg-container).
 */
add_action('wp_head', function () {
    $konten = sanitize_hex_color(get_theme_mod('color_content', '#343a40')) ?: '#343a40';
    $menu = sanitize_hex_color(get_theme_mod('velocity_toko21_warna_menu', velocity_toko21_warna_menu_lama())) ?: '#ffffff';
    $utama = sanitize_hex_color(get_theme_mod('primary_color', '#1e73be')) ?: '#1e73be';
    // Kartu produk memakai latar putih 10% di atas warna konten: uji kontras di latar itu.
    $aksen = velocity_toko21_warna_terbaca($utama, velocity_toko21_campur_putih($konten, .1));
    $css = ':root{--content-color:' . $konten . ';--color-main:' . $menu . ';--toko21-aksen:' . $aksen . ';}'
        . '.bg-container{background-color:' . $konten . ';border-color:' . $konten . ';}'
        . '.velocity-judul,#primary-menu>li>a,.nav-link,.widget-title,.text-colortheme,.text-colortheme i,.page-link{color:' . $menu . ';}';

    $menu_aktif = sanitize_hex_color(get_theme_mod('velocity_toko21_menu_aktif', ''));
    if ($menu_aktif) {
        $css .= '#primary-menu>li>a:hover,#primary-menu>li>a:focus-visible,#primary-menu>li.current-menu-item>a,'
            . '#primary-menu>li.current-menu-ancestor>a{background-color:' . $menu_aktif . ';color:#fff;}';
    }

    // Latar website versi Kirki (background_themewebsite) di situs lama. Situs baru
    // memakai pengaturan latar bawaan tema induk (Customizer > background website).
    $latar = get_theme_mod('background_themewebsite');
    if (is_array($latar)) {
        $aturan = [];
        foreach (['background-color', 'background-image', 'background-repeat', 'background-position', 'background-size', 'background-attachment'] as $prop) {
            $nilai = trim((string) ($latar[$prop] ?? ''));
            if ($nilai === '') {
                continue;
            }
            $aturan[] = $prop . ':' . ($prop === 'background-image' ? 'url(' . esc_url($nilai) . ')' : esc_attr($nilai));
        }
        if ($aturan) {
            $css .= 'body{' . implode(';', $aturan) . ';}';
        }
    }
    echo '<style id="velocity-toko21-customizer">' . wp_strip_all_tags($css) . '</style>' . "\n";
}, 100);
