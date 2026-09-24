Velocity Child Theme Paket Toko Online Toko 21
=================
[toko21.velocitydeveloper.com](https://www.toko21.velocitydeveloper.com/)

Child Theme for the Velocity System WordPress theme.

### Required
Theme Velocity versi 2.4.0 keatas , [Download](https://github.com/VelocityDeveloper/velocity/releases)

### Required Plugins
Salah satu plugin toko berikut:

- **VD Store** (disarankan untuk situs baru) — produk `store_product`, kategori `store_product_cat`,
  merek `brand`. Tema memakai shortcode VD Store (`[wp_store_price]`, `[wp_store_add_to_cart]`,
  `[wp_store_cart]`, `[wp_store_gallery]`, dst.) dan template override di folder `vd-store/`.
- **Velocity Toko** (situs lama) , [Download](https://github.com/VelocityDeveloper/velocity-toko/releases)

Jalur VD Store aktif otomatis bila plugin VD Store aktif (`WP_STORE_VERSION`); tanpa VD Store tema
tetap memakai Velocity Toko seperti versi 1.0.x. Integrasinya ada di `inc/vd-store.php` dan
`css/vd-store.css`.

| Velocity Toko | VD Store |
|---|---|
| `[harga]` | `[wp_store_price]` |
| `[beli]` | `[wp_store_add_to_cart]` |
| `[cart]` | `[wp_store_cart]` |
| `[profile]` | ikon ke halaman Profil Saya VD Store |
| `[kontak]` | kontak dari pengaturan VD Store (`velocity_toko21_kontak()`) |
| `[thumbnail]` | `[wp_store_thumbnail]` |
| `[slider-produk]` | `[wp_store_gallery]` |
| `[detail-produk]` | `[wp_store_product_info]` |
| `[love]` | `[wp_store_add_to_wishlist]` |
| `[beli-lain]` | `velocity_toko21_beli_lain()` |
| `[share]` | `[velocity-sharepost]` (Velocity Addons) |
| `[vtoko-list-taxonomy]` | `velocity_toko21_list_kategori()` |
| filter kategori | `[wp_store_filters]` |
| testimoni | `[toko21_testimoni jumlah="5"]` — ulasan produk VD Store terbaru |

### Usage
Simply download the zip and upload the zip (velocity-toko21.zip) under your WordPress dashboard at Appearance > Themes. Or extract and upload via FTP at wp-content/themes/.

