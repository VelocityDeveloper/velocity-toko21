<div class="card bg-theme rounded-0 p-0 border-0 container">
    <nav id="main-navi" class="m-md-0 my-2 p-0 navbar navbar-expand-md d-block navbar-light" aria-labelledby="main-nav-label">

        <div class="menu-header text-start d-md-none position-relative" data-bs-theme="dark">

            <button class="navbar-toggler p-0 m-2 rounded-0" type="button" data-bs-toggle="offcanvas" data-bs-target="#navbarNavOffcanvas" aria-controls="navbarNavOffcanvas" aria-expanded="false" aria-label="<?php esc_attr_e('Toggle navigation', 'justg'); ?>">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="text-dark bi bi-list" viewBox="0 0 16 16">
                    <path fill-rule="evenodd" d="M2.5 12a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5m0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5m0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5" />
                </svg>
            </button>

        </div>

        <div class="menu-styles">
            <div class="pb-0">

                <div class="bg-theme offcanvas offcanvas-start" tabindex="-1" id="navbarNavOffcanvas">

                    <div class="offcanvas-header justify-content-end">
                        <button type="button" class="btn-close btn-close-dark text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                    </div><!-- .offcancas-header -->

                    <!-- The WordPress Menu goes here -->
                    <?php
                    wp_nav_menu(
                        array(
                            'theme_location'  => 'primary',
                            'container_class' => 'offcanvas-body',
                            'container_id'    => '',
                            'menu_class'      => 'navbar-nav navbar-light justify-content-md-start justify-content-start flex-md-wrap flex-grow-1',
                            'fallback_cb'     => '',
                            'menu_id'         => 'primary-menu',
                            'depth'           => 4,
                            'walker'          => new justg_WP_Bootstrap_Navwalker(),
                        )
                    ); ?>

                </div><!-- .offcanvas -->
            </div>

    </nav><!-- .site-navigation -->
</div>

<div class="card container p-0 rounded-0 border-0">
    <div class="haeder-images">
        <?php if (has_header_image()) {
            echo '<a href="' . get_home_url() . '">';
            echo '<img class="w-100" src="' . esc_url(get_header_image()) . '" />';
            echo '</a>';
        } ?>
    </div>
</div>

<div class="container bg-theme p-2">
    <div class="row m-0 align-items-center text-white">
        <div class="col-md-9 col-12 pe-md-0">
            <div class="kontak-header text-md-end text-center align-items-center">
                <?php echo do_shortcode('[kontak style="false"]'); ?>
            </div>
        </div>
        <div class="col-md-1 col-6 p-md-0 px-2 profile-icons d-flex justify-content-md-center justify-content-end">
            <div class="px-1"><?php echo do_shortcode('[profile]'); ?></div>
            <div class="px-1"><?php echo do_shortcode('[cart]'); ?></div>
        </div>
        <div class="col-md-2 col-6 p-md-0 px-2"><?php echo get_search_form(); ?></div>
    </div>
</div>