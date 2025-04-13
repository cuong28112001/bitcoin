<?php



get_header();
$category = get_queried_object();
?>
<div class="news_heading">
    <div class="row">
        <div class="col-lg">
            <h1 class="fs-30 cl-black ff-title"><?php echo $category->name ?></h1>
        </div>
        <div class="col-auto">
            <ul class="post_rss fs-11 fw-bold mb-lg-0 mb-3">
                <li class="telegram"><a href=""><i class="fa fa-paper-plane"></i> Telegram</a></li>
                <li class="rss"><a href=""><i class="fa fa-rss"></i> RSS Feed</a></li>
            </ul>
        </div>
    </div>
    <div class="fs-16">
        <?php echo $category->description ?>
    </div>
</div>
<div class="ads_top">
    <div class="ads_box">
        <a href="">
            <img src="<?php echo get_template_directory_uri() ?>/assets/images/ads-720x90.png" alt="">
        </a>
    </div>
</div>
<div class="body_content news_page">
    <div class="row">
        <div class="col-lg-3 order-lg-3">
            <div class="sticky_sidebar">
                <div class="row">
                    <div class="col-lg-12 col-md-6">
                        <div class="sidebar_ads">
                            <div class="ads_box">
                                <a href="">
                                    <img src="<?php echo get_template_directory_uri() ?>/assets/images/ads-304x250.jpg" alt="">
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-12 col-md-6">
                        <div class="sidebar_chart">
                            <ul class="nav nav-tabs" id="myTab" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="home-tab" data-bs-toggle="tab"
                                        data-bs-target="#home-tab-pane" type="button" role="tab"
                                        aria-controls="home-tab-pane" aria-selected="true">BTC</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="profile-tab" data-bs-toggle="tab"
                                        data-bs-target="#profile-tab-pane" type="button" role="tab"
                                        aria-controls="profile-tab-pane" aria-selected="false">ETH</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="contact-tab" data-bs-toggle="tab"
                                        data-bs-target="#contact-tab-pane" type="button" role="tab"
                                        aria-controls="contact-tab-pane" aria-selected="false">XRP</button>
                                </li>
                            </ul>
                            <div class="tab-content" id="myTabContent">
                                <div class="tab-pane fade show active" id="home-tab-pane" role="tabpanel"
                                    aria-labelledby="home-tab" tabindex="0">
                                    <div class="head_chart">
                                        <div class="fs-12 fw-bold cl-black d-flex align-items-center gap-2">
                                            <img src="<?php echo get_template_directory_uri() ?>/assets/images/bnb-logo.png" class="brand-logo" alt="">
                                            Bitcoin / Đô la <span class="status up"></span>
                                        </div>
                                        <div class="fs-18 fw-bold cl-black">93,480.43 USD</div>
                                        <div class="fs-14 cl-green"><strong>+2,480.43 - 2.95%</strong> tháng
                                            trước</div>
                                    </div>
                                    <img src="<?php echo get_template_directory_uri() ?>/assets/images/chart.png" class="img-fluid" alt="">
                                </div>
                                <div class="tab-pane fade" id="profile-tab-pane" role="tabpanel"
                                    aria-labelledby="profile-tab" tabindex="0">
                                    <div class="head_chart">
                                        <div class="fs-12 fw-bold cl-black d-flex align-items-center gap-2">
                                            <img src="<?php echo get_template_directory_uri() ?>/assets/images/tether-logo.png" class="brand-logo"
                                                alt=""> Ethereum / Đô la <span class="status down"></span>
                                        </div>
                                        <div class="fs-18 fw-bold cl-black">93,480.43 USD</div>
                                        <div class="fs-14 cl-red"><strong>-1,480.43 - 2.95%</strong> tháng
                                            trước</div>
                                    </div>
                                    <img src="<?php echo get_template_directory_uri() ?>/assets/images/chart.png" class="img-fluid" alt="">
                                </div>
                                <div class="tab-pane fade" id="contact-tab-pane" role="tabpanel"
                                    aria-labelledby="contact-tab" tabindex="0">
                                    <div class="head_chart">
                                        <div class="fs-12 fw-bold cl-black d-flex align-items-center gap-2">
                                            <img src="<?php echo get_template_directory_uri() ?>/assets/images/bnb-logo.png" class="brand-logo" alt="">
                                            Bitcoin / Đô la <span class="status up"></span>
                                        </div>
                                        <div class="fs-18 fw-bold cl-black">93,480.43 USD</div>
                                        <div class="fs-14 cl-green"><strong>+2,480.43 - 2.95%</strong> tháng
                                            trước</div>
                                    </div>
                                    <img src="<?php echo get_template_directory_uri() ?>/assets/images/chart.png" class="img-fluid" alt="">
                                </div>
                            </div>
                        </div>
                        <div class="fs-16 text-center mt-3">
                            <a href="">BTC</a> bởi Trading View
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-auto order-lg-2">
            <div class="vline"></div>
        </div>
        <div class="col-lg order-lg-0">
            <div class="lasted_post">
                <div class="list_post">
                    <?php
                    $p = get_query_var('paged') ? get_query_var('paged') : 1;
                    $query_args = array( 
                        'post_type'      => 'post',
                        'posts_per_page'  => '15',
                        'paged' => $p,
                        'category__in'   => $category,
                        // 'post__not_in'  => $post__not_in,
                       
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
                                <div class="row">
                                    <div class="col-lg col-md col-7 order-md-0">
                                        <h3 class="fs-18 ff-title"><a href="<?php the_permalink(); ?>"><?php echo get_the_title() ?></a>
                                        </h3>
                                        <ul class="post_meta">
                                            <?php custom_breadcrumb() ?>
                                                        <li><?php echo human_time_diff($time, current_time('timestamp')) . ' trước'; ?></li>
                                        </ul>
                                    </div>
                                    <div class="col-lg-3 col-md-3 col-5 order-md-2">
                                        <figure>
                                            <a href="<?php the_permalink(); ?>">
                                                <img src="<?php echo get_the_post_thumbnail_url()?>" class="img-fluid" alt="">
                                            </a>
                                        </figure>
                                    </div>
                                    <div class="col-lg col-md order-md-1">
                                        <div class="post_excerpt">
                                            <p><?php echo get_the_excerpt() ?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php endif;wp_reset_postdata(); ?>
                </div>
                <div class="j_paging my-3">
                    <?php wp_pagenavi(array('query' => $related_cats_post) ); wp_reset_postdata(); ?>
                </div>
            </div>
            <?php
            $categories = get_categories();
            foreach ($categories as $list_category) :?>
                <?php if($category->term_id==$list_category->term_id) continue; ?>
                <div class="press_post">
                    <div class="title_group fw-bold fs-12">
                        <span><?php echo $list_category->name ?></span>
                    </div>
                    <div class="row">
                        <?php
                        $query_args = array( 
                            'post_type'      => 'post',
                            'posts_per_page'  => '4',
                            'category__in'   => $list_category,
                            // 'post__not_in'  => $post__not_in,
                           
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
                                <div class="col-lg col-md col-6">
                                    <div class="item">
                                        <figure>
                                            <a href="<?php the_permalink(); ?>"><img src="<?php echo get_the_post_thumbnail_url()?>"
                                                    class="img-fluid" alt=""></a>
                                        </figure>
                                        <h3 class="fs-13 ff-title"><a href="<?php the_permalink(); ?>"><?php echo get_the_title() ?></a></h3>
                                        <ul class="post_meta">
                                            <li><?php echo get_the_date('d/m/Y'); ?></li>
                                            <li><?php echo get_the_time('H:i'); ?></li>
                                        </ul>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        <?php endif;wp_reset_postdata(); ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<?php
get_footer();
