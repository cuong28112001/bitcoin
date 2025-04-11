<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="format-detection" content="telephone=no">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tạp chí Bitcons</title>
    <?php wp_head(); ?>
    <script src="<?php echo get_template_directory_uri() ?>/assets/js/vendor/modernizr-2.8.3-respond-1.4.2.min.js"></script>
    <link rel="stylesheet" href="//cdn.datatables.net/2.2.2/css/dataTables.dataTables.min.css">
</head>

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
        <ul class="menu_site ff-title">
            <li class="menu-item-has-children">
                <a href="price.html">
                    Bảng giá
                </a>
                <ul class="sub-menu">
                    <li><a href="price-detail.html">Bảng giá tiền ảo</a></li>
                    <li><a href="price-detail.html">Gainers</a></li>
                    <li><a href="price-detail.html">Losers</a></li>
                    <li><a href="price-detail.html">Recently Added</a></li>
                </ul>
            </li>
            <li class="menu-item-has-children">
                <a href="news.html">
                    Tạp chí
                </a>
                <ul class="sub-menu">
                    <li><a href="news.html">Tin tức Bitcons (BTC)</a></li>
                    <li><a href="news.html">Tin tức Ethereum (ETH)</a></li>
                    <li><a href="news.html">Tin tức Solana (SOL)</a></li>
                    <li><a href="news.html">Tin tức Tron</a></li>
                </ul>
            </li>
            <li>
                <a href="technical.html">
                    Phân tích kỹ thuật
                </a>
            </li>
            <li class="menu-item-has-children">
                <a href="">
                    Tin tức Crypto
                </a>
                <ul class="sub-menu">
                    <li><a href="">Tin vắng Crypto</a></li>
                    <li><a href="">Giá coin hôm nay</a></li>
                    <li><a href="">Tin tức Memecoin</a></li>
                </ul>
            </li>
            <li class="menu-item-has-children">
                <a href="">
                    Kinh nghiệm trade coin
                </a>
                <ul class="sub-menu">
                    <li><a href="">Kiến thức crypto</a></li>
                    <li><a href="">Quy định pháp lý</a></li>
                    <li><a href="">Đăng ký sàn giao dịch</a></li>
                </ul>
            </li>
            <li class="menu-item-has-children">
                <a href="">
                    Blockchian & AI
                </a>
                <ul class="sub-menu">
                    <li><a href="">Công nghệ Blockchain</a></li>
                    <li><a href="">Tin tức AI</a></li>
                </ul>
            </li>
            <li class="menu-item-has-children">
                <a href="people.html">
                    Danh mục khác
                </a>
                <ul class="sub-menu">
                    <li><a href="">Phân tích kỹ thuật</a></li>
                    <li><a href="">Quảng cáo</a></li>
                </ul>
            </li>
        </ul>
        <div class="user_menu">
            <ul>
                <li><a href="login.html" class="login_btn">Đăng nhập</a></li>
                <li><a href="register.html" class="register_btn">Đăng ký</a></li>
            </ul>
        </div>
    </div>
    <div class="search_table">
        <div class="close_search"><img src="<?php echo get_template_directory_uri() ?>/assets/images/close2.png" class="img-fluid" alt=""></div>
        <form action="search.html">
            <input type="search" class="form-control mb-3"
                placeholder="Search for people, companies, products and news">
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
                    <a href="index.html">
                        <img src="<?php echo get_template_directory_uri() ?>/assets/images/logo.png" class="img-fluid" alt="">
                    </a>
                </div>
                <div class="search_btn">
                    <a href="" class="ff-title">
                        <img src="<?php echo get_template_directory_uri() ?>/assets/images/search-icon.svg" class="img-fluid" alt=""> Tìm kiếm
                    </a>
                </div>
                <ul class="menu_site ff-title">
                    <li class="menu-item-has-children">
                        <a href="price.html">
                            <img src="<?php echo get_template_directory_uri() ?>/assets/images/quote-icon.svg" class="img-fluid" alt=""> Bảng giá
                        </a>
                        <ul class="sub-menu">
                            <li><a href="">Bảng giá tiền ảo</a></li>
                            <li><a href="">Gainers</a></li>
                            <li><a href="">Losers</a></li>
                            <li><a href="">Recently Added</a></li>
                        </ul>
                    </li>
                    <li class="menu-item-has-children">
                        <a href="news.html">
                            <img src="<?php echo get_template_directory_uri() ?>/assets/images/magazine-icon.svg" class="img-fluid" alt=""> Tạp chí
                        </a>
                        <ul class="sub-menu">
                            <li><a href="">Tin tức Bitcons (BTC)</a></li>
                            <li><a href="">Tin tức Ethereum (ETH)</a></li>
                            <li><a href="">Tin tức Solana (SOL)</a></li>
                            <li><a href="">Tin tức Tron</a></li>
                        </ul>
                    </li>
                    <li>
                        <a href="technical.html">
                            <img src="<?php echo get_template_directory_uri() ?>/assets/images/chart-icon.svg" class="img-fluid" alt=""> Phân tích kỹ thuật
                        </a>
                    </li>
                    <li class="menu-item-has-children">
                        <a href="">
                            <img src="<?php echo get_template_directory_uri() ?>/assets/images/news-icon.svg" class="img-fluid" alt=""> Tin tức Crypto
                        </a>
                        <ul class="sub-menu">
                            <li><a href="">Tin vắng Crypto</a></li>
                            <li><a href="">Giá coin hôm nay</a></li>
                            <li><a href="">Tin tức Memecoin</a></li>
                        </ul>
                    </li>
                    <li class="menu-item-has-children">
                        <a href="">
                            <img src="<?php echo get_template_directory_uri() ?>/assets/images/exp-icon.svg" class="img-fluid" alt=""> Kinh nghiệm trade coin
                        </a>
                        <ul class="sub-menu">
                            <li><a href="">Kiến thức crypto</a></li>
                            <li><a href="">Quy định pháp lý</a></li>
                            <li><a href="">Đăng ký sàn giao dịch</a></li>
                        </ul>
                    </li>
                    <li class="menu-item-has-children">
                        <a href="">
                            <img src="<?php echo get_template_directory_uri() ?>/assets/images/block-chain-icon.svg" class="img-fluid" alt=""> Blockchian & AI
                        </a>
                        <ul class="sub-menu">
                            <li><a href="">Công nghệ Blockchain</a></li>
                            <li><a href="">Tin tức AI</a></li>
                        </ul>
                    </li>
                    <li class="menu-item-has-children">
                        <a href="people.html">
                            <img src="<?php echo get_template_directory_uri() ?>/assets/images/other-icon.svg" class="img-fluid" alt=""> Danh mục khác
                        </a>
                        <ul class="sub-menu">
                            <li><a href="">Phân tích kỹ thuật</a></li>
                            <li><a href="">Quảng cáo</a></li>
                        </ul>
                    </li>
                </ul>
                <div class="user_menu">
                    <ul>
                        <li><a href="login.html" class="login_btn">Đăng nhập</a></li>
                        <li><a href="register.html" class="register_btn">Đăng ký</a></li>
                    </ul>
                </div>
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
                            <a href="index.html" class="logo_site">
                                <img src="<?php echo get_template_directory_uri() ?>/assets/images/logo.png" class="img-fluid" alt="">
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