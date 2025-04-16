<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="format-detection" content="telephone=no">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php wp_head(); ?>
    <script src="<?php echo get_template_directory_uri() ?>/assets/js/vendor/modernizr-2.8.3-respond-1.4.2.min.js"></script>
    <link rel="stylesheet" href="//cdn.datatables.net/2.2.2/css/dataTables.dataTables.min.css">
</head>
<?php $header = get_field('header_group', 'option'); ?>
<body>
    <div id="menu_mobile">
        <a href="" class="hamburger_btn d-lg-none">
            <div class="hamburger-icon">
                <span></span>
                <span></span>
                <span></span>
                <span></span>
                <span></span>
                <span></span>
            </div>
        </a>
        <?php
        wp_nav_menu(array(
            'menu_id'       => 'menu-top', 
            'theme_location'=> 'menu-header',
            'menu_class'    => 'menu_site ff-title',
            'container'     => 'ul',
            'link_class'    => '',
            'add_li_class'  => 'menu-item-has-children',
        ));
        ?>
        <?php if (is_user_logged_in()): ?>
            <div class="menu_profile">
                <ul>
                    <li class="menu-item-has-children">
                        <a href="">Tài khoản của bạn <span><?php echo esc_html(wp_get_current_user()->display_name); ?></span></a>
                        <ul class="sub-menu">
                            <li><a href="<?php echo esc_url(home_url('/change-password/')); ?>">Đổi mật khẩu</a></li>
                            <li><a href="<?php echo esc_url(wp_logout_url(home_url('/'))); ?>">Đăng xuất</a></li>
                            <li><a href="<?php //echo esc_url(home_url('/delete-account/')); ?>">Xoá tài khoản</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        <?php else: ?>
            <div class="user_menu">
                <ul>
                    <li><a href="<?php echo esc_url(home_url('/login/')); ?>" class="login_btn">Đăng nhập</a></li>
                    <li><a href="<?php echo esc_url(home_url('/register/')); ?>" class="register_btn">Đăng ký</a></li>
                </ul>
            </div>
        <?php endif; ?>
    </div>
    <div class="search_table">
        <div class="close_search"><img src="<?php echo get_template_directory_uri() ?>/assets/images/close2.png" class="img-fluid" alt=""></div>
        <form action="<?php echo home_url('') ?>">
            <input type="search" class="form-control mb-3"
                placeholder="Search for people, companies, products and news" name="s">
        </form>
        <div class="quick_search">
            <h4 class="fs-13 fw-bold">Crypto assets</h4>
            <ul>
                <li><a href=""><img src="<?php echo get_template_directory_uri() ?>/assets/images/coin-logo.png" class="img-fluid" alt=""> Bitcoin</a></li>
                <li><a href=""><img src="<?php echo get_template_directory_uri() ?>/assets/images/ethereum.png" class="img-fluid" alt=""> Ethereum</a></li>
                <li><a href=""><img src="<?php echo get_template_directory_uri() ?>/assets/images/xrp-logo.png" class="img-fluid" alt=""> XRP</a></li>
                <li><a href=""><img src="<?php echo get_template_directory_uri() ?>/assets/images/bnb-logo.png" class="img-fluid" alt=""> BNB</a></li>
                <li><a href=""><img src="<?php echo get_template_directory_uri() ?>/assets/images/usdc.png" class="img-fluid" alt=""> USDC</a></li>
                <li><a href=""><img src="<?php echo get_template_directory_uri() ?>/assets/images/dogecoin.png" class="img-fluid" alt=""> Dogecoin</a></li>
                <li><a href=""><img src="<?php echo get_template_directory_uri() ?>/assets/images/solana.png" class="img-fluid" alt=""> Solana</a></li>
                <li><a href=""><img src="<?php echo get_template_directory_uri() ?>/assets/images/lido.png" class="img-fluid" alt=""> Lido staked ether </a></li>
                <li><a href=""><img src="<?php echo get_template_directory_uri() ?>/assets/images/cardano.png" class="img-fluid" alt=""> Cardano </a></li>
            </ul>
            <h4 class="fs-13 fw-bold">People</h4>
            <ul>
                <li><a href=""><img src="<?php echo get_template_directory_uri() ?>/assets/images/hayden.png" class="img-fluid" alt=""> Hayden Adams</a></li>
                <li><a href=""><img src="<?php echo get_template_directory_uri() ?>/assets/images/elonmusk.png" class="img-fluid" alt=""> Elon Musk</a></li>
                <li><a href=""><img src="<?php echo get_template_directory_uri() ?>/assets/images/hayden.png" class="img-fluid" alt=""> Hayden Adams</a></li>
                <li><a href=""><img src="<?php echo get_template_directory_uri() ?>/assets/images/elonmusk.png" class="img-fluid" alt=""> Elon Musk</a></li>
                <li><a href=""><img src="<?php echo get_template_directory_uri() ?>/assets/images/markmoss.png" class="img-fluid" alt=""> Mark Moss</a></li>
                <li><a href=""><img src="<?php echo get_template_directory_uri() ?>/assets/images/hayden.png" class="img-fluid" alt=""> Hayden Adams</a></li>
                <li><a href=""><img src="<?php echo get_template_directory_uri() ?>/assets/images/hayden.png" class="img-fluid" alt=""> Hayden Adams</a></li>
                <li><a href=""><img src="<?php echo get_template_directory_uri() ?>/assets/images/trump.png" class="img-fluid" alt=""> Donal Trump</a></li>
                <li><a href=""><img src="<?php echo get_template_directory_uri() ?>/assets/images/hayden.png" class="img-fluid" alt=""> Hayden Adams </a></li>
            </ul>
            <h4 class="fs-13 fw-bold">Companies</h4>
            <ul>
                <li><a href=""><img src="<?php echo get_template_directory_uri() ?>/assets/images/x.png" class="img-fluid" alt=""> X</a></li>
                <li><a href=""><img src="<?php echo get_template_directory_uri() ?>/assets/images/bybit.png" class="img-fluid" alt=""> Bybit</a></li>
                <li><a href=""><img src="<?php echo get_template_directory_uri() ?>/assets/images/tetherlimited.png" class="img-fluid" alt=""> Tether Limited</a>
                </li>
                <li><a href=""><img src="<?php echo get_template_directory_uri() ?>/assets/images/a16z.png" class="img-fluid" alt=""> a16z</a></li>
                <li><a href=""><img src="<?php echo get_template_directory_uri() ?>/assets/images/bitget.png" class="img-fluid" alt=""> Bitget</a></li>
                <li><a href=""><img src="<?php echo get_template_directory_uri() ?>/assets/images/tetherlimited.png" class="img-fluid" alt=""> BlackRock</a></li>
                <li><a href=""><img src="<?php echo get_template_directory_uri() ?>/assets/images/a16z.png" class="img-fluid" alt=""> 21shares</a></li>
            </ul>
        </div>
    </div>
    <div class="overlay_menu"></div>
    <div class="body_wrapper">
        <header id="header_site" class="sticky-top">
            <div class="group_top">
                <div class="logo_box">
                    <a href="<?php echo home_url() ?>">
                        <img src="<?php echo $header['logo_header'] ?>" class="img-fluid" alt="">
                    </a>
                </div>
                <div class="search_btn">
                    <a href="" class="ff-title">
                        <img src="<?php echo get_template_directory_uri() ?>/assets/images/search-icon.svg" class="img-fluid" alt=""> Tìm kiếm
                    </a>
                </div>
                <?php
                wp_nav_menu(array(
                    'menu_id'       => 'menu-top', 
                    'theme_location'=> 'menu-header',
                    'menu_class'    => 'menu_site ff-title',
                    'container'     => 'ul',
                    'link_class'    => '',
                    'add_li_class'  => '',
                ));
                ?>
                <?php if (is_user_logged_in()): ?>
                    <div class="menu_profile">
                        <ul>
                            <li class="menu-item-has-children">
                                <a href="">Tài khoản của bạn <span><?php echo esc_html(wp_get_current_user()->display_name); ?></span></a>
                                <ul class="sub-menu">
                                    <li><a href="<?php echo esc_url(home_url('/change-password/')); ?>">Đổi mật khẩu</a></li>
                                    <li><a href="<?php echo esc_url(wp_logout_url(home_url('/'))); ?>">Đăng xuất</a></li>
                                    <li><a href="<?php //echo esc_url(home_url('/delete-account/')); ?>">Xoá tài khoản</a></li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                <?php else: ?>
                    <div class="user_menu">
                        <ul>
                            <li><a href="<?php echo esc_url(home_url('/login/')); ?>" class="login_btn">Đăng nhập</a></li>
                            <li><a href="<?php echo esc_url(home_url('/register/')); ?>" class="register_btn">Đăng ký</a></li>
                        </ul>
                    </div>
                <?php endif; ?>
            </div>
            <div class="group_bottom">
                <ul class="social">
                    <li><a href=""><img src="<?php echo get_template_directory_uri() ?>/assets/images/facebook.png" class="img-fluid" alt=""></a></li>
                    <li><a href=""><img src="<?php echo get_template_directory_uri() ?>/assets/images/telegram.png" class="img-fluid" alt=""></a></li>
                    <li><a href=""><img src="<?php echo get_template_directory_uri() ?>/assets/images/x.png" class="img-fluid" alt=""></a></li>
                </ul>
            </div>
        </header>
        <div class="primary_content">
            <div id="ads_head">
                <div class="ads_box">
                    <a href="">
                        <img src="<?php echo get_template_directory_uri() ?>/assets/images/ads-980x120.jpg" alt="">
                    </a>
                </div>
            </div>
            <div class="head_mobile d-block d-md-none">
                <div class="row align-items-center">
                    <div class="col-2">
                        <div class="d-flex align-items-center">
                            <a href="" class="hamburger_btn">
                                <div class="hamburger-icon">
                                    <span></span>
                                    <span></span>
                                    <span></span>
                                    <span></span>
                                    <span></span>
                                    <span></span>
                                </div>
                            </a>
                        </div>
                    </div>
                    <div class="col-8">
                        <div class="text-center">
                            <a href="<?php echo home_url() ?>" class="logo_site">
                                <img src="<?php echo $header['logo_header'] ?>" class="img-fluid" alt="">
                            </a>
                        </div>
                    </div>
                    <div class="col-2">
                        <div class="d-flex justify-content-end">
                            <div class="search_btn">
                                <a href=""><img src="<?php echo get_template_directory_uri() ?>/assets/images/search-icon.svg" class="img-fluid" alt=""></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Header tới đây -->