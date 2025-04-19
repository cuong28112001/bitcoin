<?php get_header(); ?>
<?php
$symbol=get_field('symbol');    
$post_category=get_field('post_category');    
$video=get_field('video');    
$post__not_in=[];
$page_for_posts = get_option('page_for_posts');
?>
<div class="price_detail">
    <form action="" class="search_people">
        <div class="input-group">
            <input type="text" class="form-control" placeholder="Tìm kiếm...">
            <button class="btn btn-outline-secondary" type="button" id="button-addon2">
                <img src="<?php echo get_template_directory_uri() ?>/assets/images/search-icon.svg" class="img-fluid" alt="">
            </button>
        </div>
    </form>
    <div id="coin-overview" class="daily_update">
        <div class="tradingview-widget-container">
            <div class="tradingview-widget-container__widget"></div>
            <script
                type="text/javascript"
                src="https://s3.tradingview.com/external-embedding/embed-widget-symbol-info.js"
                async
            >
                {
                "symbol": "<?php echo $symbol ?>",
                "width": "100%",
                "locale": "vi_VN",
                "colorTheme": "light",
                "isTransparent": false
                 }
            </script>
        </div>
       
    </div>
    <div class="price_primary_info">
        <div class="row">
            <div class="col-xxl col-lg order-lg-1">
                    <div class="tradingview-widget-container" style="height:100%;width:100%">
                        <div class="tradingview-widget-container__widget" style="height:calc(100% - 32px);width:100%"></div>
                        <script type="text/javascript" src="https://s3.tradingview.com/external-embedding/embed-widget-advanced-chart.js" async>
                        {
                        "autosize": true,
                        "symbol": "<?php echo $symbol ?>",
                        "interval": "30",
                        "timezone": "Etc/UTC",
                        "theme": "light",
                        "style": "1",
                        "locale": "vi_VN",
                        "allow_symbol_change": false,
                        "calendar": false,
                        "support_host": "https://www.tradingview.com"
                    }
                        </script>
                    </div>
            </div>
            <div class="col-xxl-2j col-lg-3 order-lg-2">
                <div class="date_tag jtag fs-12 fw-bold">
                    <span>THỐNG KÊ THỊ TRƯỜNG <?php echo $symbol ?></span>
                </div>
                <!-- TradingView Widget BEGIN -->
                <div class="tradingview-widget-container">
                  <div class="tradingview-widget-container__widget"></div>
                  <div class="tradingview-widget-copyright"><a href="https://vn.tradingview.com/" rel="noopener nofollow" target="_blank"><span class="blue-text">Theo dõi mọi thị trường trên TradingView</span></a></div>
                  <script type="text/javascript" src="https://s3.tradingview.com/external-embedding/embed-widget-financials.js" async>
                  {
                  "isTransparent": false,
                  "largeChartUrl": "",
                  "displayMode": "adaptive",
                  "width": "100%",
                  "height": "100%",
                  "colorTheme": "light",
                  "symbol": "<?php echo $symbol ?>",
                  "locale": "vi_VN"
                }
                  </script>
                </div>
                <!-- TradingView Widget END -->
                <div class="date_tag jtag fs-12 fw-bold mb-3">
                    <span>KHÁM PHÁ BTC</span>
                </div>
                <a href="" class="btn btn_explorer fs-14 fw-bold">
                    Khám phá Blockchain
                </a>
            </div>
            <div class="col-xxl-2j col-lg-3 order-lg-0">
                <div class="date_tag jtag fs-12 fw-bold">
                    <span>TIN TỨC <?php echo $symbol ?></span>
                </div>
                <div class="daily_post">
                    <?php
                    $query_args = array( 
                        'post_type'      => 'post',
                        'posts_per_page'  => '5',
                        'category__in'   => $post_category,
                       
                     );
                    $related_cats_post = new WP_Query( $query_args );
                    if($related_cats_post->have_posts()):
                        $fl=0;
                        while($related_cats_post->have_posts()): 
                            $related_cats_post->the_post();
                            $post__not_in[]=get_the_ID();
                            $time = get_the_time('U');
                            $fl++;
                            ?>
                            <div class="item">
                                <h3 class="fs-13 ff-title"><a href="<?php the_permalink(); ?>"><?php echo get_the_title() ?></a></h3>
                                <ul class="post_meta">
                                    <?php custom_breadcrumb() ?>
                                    <li><?php echo human_time_diff($time, current_time('timestamp')) . ' trước'; ?></li>
                                </ul>
                            </div>
                        S<?php endwhile; ?>
                    <?php endif;wp_reset_postdata(); ?>
                </div>
                <div class="mt-3">
                    <a class="btn btn-violet d-block" href="<?php echo $post_category ? get_term_link($post_category) : get_permalink($page_for_posts) ?>">
                        Show more <?php echo $symbol ?> news
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div id="coin-info" class="price_info">
        <div class="row">
            <div class="col-lg-9">
                <div class="date_tag jtag fs-12 fw-bold mb-3">
                    <span>THÔNG TIN <?php echo $symbol ?></span>
                </div>
                <div class="price_content">
                    <?php the_content() ?>
                </div>
                <?php if ($video): ?>
                    <div class="date_tag jtag fs-12 fw-bold mb-3">
                        <span><?php echo $symbol ?> video</span>
                    </div>
                    <?php echo $video ?>
                <?php endif ?>
            </div>
            <div class="col-lg-3">
                <!-- TradingView Widget BEGIN -->
                <div class="tradingview-widget-container">
                  <div class="tradingview-widget-container__widget"></div>
                  <div class="tradingview-widget-copyright"><a href="https://vn.tradingview.com/" rel="noopener nofollow" target="_blank"><span class="blue-text">Theo dõi mọi thị trường trên TradingView</span></a></div>
                  <script type="text/javascript" src="https://s3.tradingview.com/external-embedding/embed-widget-symbol-profile.js" async>
                  {
                  "width": 400,
                  "height": 550,
                  "isTransparent": false,
                  "colorTheme": "light",
                  "symbol": "<?php echo $symbol ?>",
                  "locale": "vi_VN"
                }
                  </script>
                </div>
                <!-- TradingView Widget END -->
            </div>
        </div>
    </div>
    <div id="coin-news" class="news_section">
        <div class="inner_content">
            <div class="date_tag jtag fs-12 fw-bold mb-3">
                <span>Tin <?php echo $symbol ?></span>
            </div>
            <div class="row">
                <?php
                $query_args = array( 
                    'post_type'      => 'post',
                    'posts_per_page'  => '7',
                    'category__in'   => $post_category,
                    'post__not_in'  => $post__not_in,
                   
                 );
                $related_cats_post = new WP_Query( $query_args );
                if($related_cats_post->have_posts()):
                    $fl=0;
                    while($related_cats_post->have_posts()): 
                        $related_cats_post->the_post();
                        $post__not_in[]=get_the_ID();
                        $time = get_the_time('U');
                        $fl++;
                        ?>
                        <?php if ($fl<=2): ?>
                            <div class="col-lg-6 col-6">
                                <div class="big_item">
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <figure>
                                                <a href="<?php the_permalink(); ?>"><img src="<?php echo get_the_post_thumbnail_url()?>"
                                                        class="img-fluid" alt=""></a>
                                            </figure>
                                        </div>
                                        <div class="col-lg-6">
                                            <h3 class="fs-16 ff-title"><a href="<?php the_permalink(); ?>"><?php echo get_the_title() ?></a></h3>
                                            <ul class="post_meta">
                                                <?php custom_breadcrumb() ?>
                                                 <li><?php echo human_time_diff($time, current_time('timestamp')) . ' trước'; ?></li>
                                            </ul>
                                            <div class="desc">
                                                <p><?php echo get_the_excerpt() ?></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="col-xxl-2j col-6 <?php if($fl==7) echo 'd-none d-lg-block'; ?>">
                                <div class="item">
                                    <figure>
                                        <a href="<?php the_permalink(); ?>"><img src="<?php echo get_the_post_thumbnail_url()?>"
                                                        class="img-fluid" alt=""></a>
                                    </figure>
                                    <h3 class="fs-16 ff-title"><a href="<?php the_permalink(); ?>"><?php echo get_the_title() ?></a></h3>
                                    <ul class="post_meta">
                                        <?php custom_breadcrumb() ?>
                                         <li><?php echo human_time_diff($time, current_time('timestamp')) . ' trước'; ?></li>
                                    </ul>
                                </div>
                            </div>
                        <?php endif ?>
                    <?php endwhile; ?>
                <?php endif;wp_reset_postdata(); ?>
            </div>
            <div class="text-center">
                <a href="<?php echo $post_category ? get_term_link($post_category) : get_permalink($page_for_posts) ?>" class="btn btn-blue">Show more <?php echo $symbol ?> news</a>
            </div>
        </div>
    </div>
    <div id="coin-intel" class="token_section">
        <!-- TradingView Widget BEGIN -->
        <div class="tradingview-widget-container" style="width: 100%;height: 520px;">
          <div class="tradingview-widget-container__widget"></div>
          <div class="tradingview-widget-copyright"><a href="https://www.tradingview.com/" rel="noopener nofollow" target="_blank"><span class="blue-text">Track all markets on TradingView</span></a></div>
          <script type="text/javascript" src="https://s3.tradingview.com/external-embedding/embed-widget-technical-analysis.js" async>
          {
          "interval": "1h",
          "width": "100%",
          "height = 'inherit'": "100%",
          "isTransparent": true,
          "symbol": "<?php echo $symbol ?>",
          "showIntervalTabs": true,
          "displayMode": "multiple",
          "locale": "vi_VN",
          "colorTheme": "light"
        }
          </script>
        </div>
    </div>
<!--     <div class="signal_section">
        <div class="inner_content">
            <div class="row">
                <div class="col-lg">
                    <div class="fs-14 cl-black ff-title fw-bold mb-3">
                        Signals
                    </div>
                </div>
                <div class="col-lg-auto">
                    <ul class="signal_filter">
                        <li class="active"><a href=""><i class="fa fa-align-justify"
                                    aria-hidden="true"></i></a></li>
                        <li><a href=""><i class="fa fa-tachometer" aria-hidden="true"></i></a></li>
                    </ul>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-3">
                    <div class="box_chart">
                        <div class="chart_inner">
                            <h4 class="fs-13 fw-bold cl-black">Summary</h4>
                            <div>
                                <img src="<?php echo get_template_directory_uri() ?>/assets/images/summary.png" class="img-fluid" alt="">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-9">
                    <div class="box_chart">
                        <div class="row">
                            <div class="col-lg-4">
                                <div class="chart_inner">
                                    <h4 class="fs-13 fw-bold cl-black">Onchain Signals</h4>
                                    <div>
                                        <img src="<?php echo get_template_directory_uri() ?>/assets/images/onchain.png" class="img-fluid" alt="">
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="chart_inner">
                                    <h4 class="fs-13 fw-bold cl-black">Exchange Signals</h4>
                                    <div class="no_content fst-italic fs-13 cl-black">
                                        There are no Exchange Signals for this token
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="chart_inner">
                                    <h4 class="fs-13 fw-bold cl-black">Derivatives</h4>
                                    <div>
                                        <img src="<?php echo get_template_directory_uri() ?>/assets/images/derivatives.png" class="img-fluid" alt="">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="box_chart">
                        <div class="chart_inner">
                            <h4 class="fs-13 fw-bold cl-black">Twitter Sentiment</h4>
                            <div>
                                <img src="<?php echo get_template_directory_uri() ?>/assets/images/twitter.png" class="img-fluid" alt="">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="coin-wallets" class="list_coin">
        <div class="inner_content">
            <div class="date_tag jtag fs-12 fw-bold mb-3">
                <span>Ví <?php echo $symbol ?></span>
            </div>
            <div class="row gx-3">
                <div class="col-lg-3 col-6">
                    <div class="item">
                        <figure>
                            <a href="price-detail.html">
                                <img src="<?php echo get_template_directory_uri() ?>/assets/images/wallet1.png" class="img-fluid" alt="">
                            </a>
                        </figure>
                        <div class="info">
                            <h4 class="fs-14"><a href="price-detail.html">AnkerPay</a></h4>
                            <div class="desc fs-12">
                                Wallet
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="item">
                        <figure>
                            <a href="price-detail.html">
                                <img src="<?php echo get_template_directory_uri() ?>/assets/images/wallet2.png" class="img-fluid" alt="">
                            </a>
                        </figure>
                        <div class="info">
                            <h4 class="fs-14"><a href="price-detail.html">Binance App</a></h4>
                            <div class="desc fs-12">
                                Trading App, Wallet
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="item">
                        <figure>
                            <a href="price-detail.html">
                                <img src="<?php echo get_template_directory_uri() ?>/assets/images/wallet3.png" class="img-fluid" alt="">
                            </a>
                        </figure>
                        <div class="info">
                            <h4 class="fs-14"><a href="price-detail.html">Bitamp</a></h4>
                            <div class="desc fs-12">
                                Wallet
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="item">
                        <figure>
                            <a href="price-detail.html">
                                <img src="<?php echo get_template_directory_uri() ?>/assets/images/wallet4.png" class="img-fluid" alt="">
                            </a>
                        </figure>
                        <div class="info">
                            <h4 class="fs-14"><a href="price-detail.html"><?php echo $symbol ?> Wallet</a></h4>
                            <div class="desc fs-12">
                                Wallet
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="item">
                        <figure>
                            <a href="price-detail.html">
                                <img src="<?php echo get_template_directory_uri() ?>/assets/images/wallet5.png" class="img-fluid" alt="">
                            </a>
                        </figure>
                        <div class="info">
                            <h4 class="fs-14"><a href="price-detail.html"><?php echo $symbol ?>.com Wallet</a></h4>
                            <div class="desc fs-12">
                                Wallet
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="item">
                        <figure>
                            <a href="price-detail.html">
                                <img src="<?php echo get_template_directory_uri() ?>/assets/images/wallet6.png" class="img-fluid" alt="">
                            </a>
                        </figure>
                        <div class="info">
                            <h4 class="fs-14"><a href="price-detail.html">Bitpay Wallet</a></h4>
                            <div class="desc fs-12">
                                Wallet
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="item">
                        <figure>
                            <a href="price-detail.html">
                                <img src="<?php echo get_template_directory_uri() ?>/assets/images/wallet7.png" class="img-fluid" alt="">
                            </a>
                        </figure>
                        <div class="info">
                            <h4 class="fs-14"><a href="price-detail.html">Bitpie</a></h4>
                            <div class="desc fs-12">
                                Wallet
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="item">
                        <figure>
                            <a href="price-detail.html">
                                <img src="<?php echo get_template_directory_uri() ?>/assets/images/wallet8.png" class="img-fluid" alt="">
                            </a>
                        </figure>
                        <div class="info">
                            <h4 class="fs-14"><a href="price-detail.html">Blockchain Wallet</a></h4>
                            <div class="desc fs-12">
                                Wallet
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="item">
                        <figure>
                            <a href="price-detail.html">
                                <img src="<?php echo get_template_directory_uri() ?>/assets/images/wallet9.png" class="img-fluid" alt="">
                            </a>
                        </figure>
                        <div class="info">
                            <h4 class="fs-14"><a href="price-detail.html">BRD</a></h4>
                            <div class="desc fs-12">
                                Wallet
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="item">
                        <figure>
                            <a href="price-detail.html">
                                <img src="<?php echo get_template_directory_uri() ?>/assets/images/wallet10.png" class="img-fluid" alt="">
                            </a>
                        </figure>
                        <div class="info">
                            <h4 class="fs-14"><a href="price-detail.html">BTC.com Wallet App</a></h4>
                            <div class="desc fs-12">
                                Wallet
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="text-center mt-3">
                <a href="" class="btn btn-blue">Show all wallets</a>
            </div>
        </div>
    </div>
    <div id="coin-exchanges" class="list_coin">
        <div class="inner_content">
            <div class="date_tag jtag fs-12 fw-bold mb-3">
                <span>Trao đổi <?php echo $symbol ?></span>
            </div>
            <div class="row gx-3">
                <div class="col-lg-3 col-6">
                    <div class="item">
                        <figure>
                            <a href="price-detail.html">
                                <img src="<?php echo get_template_directory_uri() ?>/assets/images/exc1.png" class="img-fluid" alt="">
                            </a>
                        </figure>
                        <div class="info">
                            <h4 class="fs-14"><a href="price-detail.html">Amplify Exchange</a></h4>
                            <div class="desc fs-12">
                                Exchange Company in Europe
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="item">
                        <figure>
                            <a href="price-detail.html">
                                <img src="<?php echo get_template_directory_uri() ?>/assets/images/exc2.png" class="img-fluid" alt="">
                            </a>
                        </figure>
                        <div class="info">
                            <h4 class="fs-14"><a href="price-detail.html">Anycoin Direct</a></h4>
                            <div class="desc fs-12">
                                Exchange Company in Europe
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="item">
                        <figure>
                            <a href="price-detail.html">
                                <img src="<?php echo get_template_directory_uri() ?>/assets/images/exc3.png" class="img-fluid" alt="">
                            </a>
                        </figure>
                        <div class="info">
                            <h4 class="fs-14"><a href="price-detail.html">Bakkt</a></h4>
                            <div class="desc fs-12">
                                Exchange Company in Europe
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="item">
                        <figure>
                            <a href="price-detail.html">
                                <img src="<?php echo get_template_directory_uri() ?>/assets/images/exc4.png" class="img-fluid" alt="">
                            </a>
                        </figure>
                        <div class="info">
                            <h4 class="fs-14"><a href="price-detail.html">BaseFEX</a></h4>
                            <div class="desc fs-12">
                                Exchange Company in Europe
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="item">
                        <figure>
                            <a href="price-detail.html">
                                <img src="<?php echo get_template_directory_uri() ?>/assets/images/exc5.png" class="img-fluid" alt="">
                            </a>
                        </figure>
                        <div class="info">
                            <h4 class="fs-14"><a href="price-detail.html">BigONE</a></h4>
                            <div class="desc fs-12">
                                Exchange Company in Europe
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="item">
                        <figure>
                            <a href="price-detail.html">
                                <img src="<?php echo get_template_directory_uri() ?>/assets/images/exc6.png" class="img-fluid" alt="">
                            </a>
                        </figure>
                        <div class="info">
                            <h4 class="fs-14"><a href="price-detail.html">Binance.US</a></h4>
                            <div class="desc fs-12">
                                Exchange Company in Europe
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="item">
                        <figure>
                            <a href="price-detail.html">
                                <img src="<?php echo get_template_directory_uri() ?>/assets/images/exc7.png" class="img-fluid" alt="">
                            </a>
                        </figure>
                        <div class="info">
                            <h4 class="fs-14"><a href="price-detail.html">BIT Crypto Exchange</a></h4>
                            <div class="desc fs-12">
                                Exchange Company in Europe
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="item">
                        <figure>
                            <a href="price-detail.html">
                                <img src="<?php echo get_template_directory_uri() ?>/assets/images/exc8.png" class="img-fluid" alt="">
                            </a>
                        </figure>
                        <div class="info">
                            <h4 class="fs-14"><a href="price-detail.html">Bitbuy</a></h4>
                            <div class="desc fs-12">
                                Exchange Company in Europe
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="item">
                        <figure>
                            <a href="price-detail.html">
                                <img src="<?php echo get_template_directory_uri() ?>/assets/images/exc9.png" class="img-fluid" alt="">
                            </a>
                        </figure>
                        <div class="info">
                            <h4 class="fs-14"><a href="price-detail.html"><?php echo $symbol ?> of America</a></h4>
                            <div class="desc fs-12">
                                Exchange Company in Europe
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="item">
                        <figure>
                            <a href="price-detail.html">
                                <img src="<?php echo get_template_directory_uri() ?>/assets/images/exc10.png" class="img-fluid" alt="">
                            </a>
                        </figure>
                        <div class="info">
                            <h4 class="fs-14"><a href="price-detail.html">BitCoke</a></h4>
                            <div class="desc fs-12">
                                Exchange Company in Europe
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="text-center mt-3">
                <a href="" class="btn btn-blue">Show all exchanges</a>
            </div>
        </div>
    </div> -->
    <div class="ads_footer">
        <div class="ads_box">
            <a href="">
                <img src="<?php echo get_template_directory_uri() ?>/assets/images/ads-980x120.jpg" alt="">
            </a>
        </div>
    </div>
</div>
<?php get_footer() ?>