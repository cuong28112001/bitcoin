<?php
    /*
    Template Name: Homepage
    */
    get_header()
?>
<?php //$section_banner=get_field('section_banner') ?>
<?php $page_for_posts = get_option('page_for_posts'); ?>
<!-- <div class="splide coin_news">
    <?php // get_template_part( 'template-parts/content-coin_news'); ?>
</div> -->
<div class="trade_live">
    <?php get_template_part( 'template-parts/content-trade_live'); ?>
</div>
<div class="ads_top"> 
    <div class="ads_box">
        <?php echo get_field('ads_top', 'option'); ?>
    </div>
</div>
<div class="body_content">
    <div class="row">
        <?php
        $query_args = array( 
            'post_type'      => 'post',
            'posts_per_page'  => '9',
           
         );
        $post__not_in=[];
        $related_cats_post = new WP_Query( $query_args );
        if($related_cats_post->have_posts()):
            $fl=0;
            ?>
            <div class="col-lg-6 order-lg-1">
                <div class="featured_tag jtag line_bottom fs-12 fw-bold">
                    <span>Tin nổi bật</span>
                </div>
                <?php while($related_cats_post->have_posts()): 
                    $related_cats_post->the_post();
                    $post__not_in[]=get_the_ID();
                    $time = get_the_time('U');
                    ?>
                    <div class="featured_post">
                        <div class="item">
                            <div class="text_box">
                                <h3 class="fs-28 ff-title"><a href="<?php the_permalink(); ?>"><?php the_title() ?></a></h3>
                                <div class="post_excerpt fs-15">
                                    <p><?php echo get_the_excerpt() ?></p>
                                </div>
                                <ul class="post_meta">
                                    <?php custom_breadcrumb() ?>
                                    <li><?php echo human_time_diff($time, current_time('timestamp')) . ' trước'; ?></li>
                                </ul>
                            </div>
                            <figure>
                                <a href="<?php the_permalink(); ?>">
                                    <img src="<?php echo get_the_post_thumbnail_url()?>" class="img-fluid" alt="">
                                </a>
                            </figure>
                        </div>
                    </div>
                <?php break;endwhile; ?>
            </div>
            <div class="col-lg-3 order-lg-2">
                <div class="featured_second">
                    <div class="row">
                        <?php $fl=0;while($related_cats_post->have_posts()): 
                            $related_cats_post->the_post();
                            $post__not_in[]=get_the_ID();
                            $time = get_the_time('U');
                            $fl++;
                            ?>
                            <div class="col-md-12 col-6">
                                <div class="item">
                                    <div class="row gx-lg-2">
                                        <div class="col-lg-4 col-md-5">
                                            <figure>
                                                <a href="<?php the_permalink(); ?>">
                                                    <img src="<?php echo get_template_directory_uri() ?>/assets/images/post-thumb3.jpg" class="img-fluid"
                                                        alt="">
                                                </a>
                                            </figure>
                                        </div>
                                        <div class="col-lg-8 col-md-7">
                                            <div class="text_box">
                                                <h3 class="fs-13 ff-title"><a href="<?php the_permalink(); ?>"><?php echo get_the_excerpt() ?></a></h3>
                                                <ul class="post_meta d-lg-none">
                                                    <?php custom_breadcrumb() ?>
                                                    <li><?php echo human_time_diff($time, current_time('timestamp')) . ' trước'; ?></li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    <ul class="post_meta d-none d-lg-flex">
                                        <?php custom_breadcrumb() ?>
                                        <li><?php echo human_time_diff($time, current_time('timestamp')) . ' trước'; ?></li>
                                    </ul>
                                    <div class="post_excerpt fs-12">
                                        <p><?php echo get_the_excerpt() ?></p>
                                    </div>
                                </div>
                            </div>
                        <?php if($fl==4) break;endwhile; ?>
                        
                    </div>
                </div>
            </div>
            <div class="col-lg-3 order-lg-0">
                <div class="date_tag jtag line_bottom fs-12 fw-bold">
                    <span>
                        <?php
                        $formatter = new IntlDateFormatter('vi_VN', IntlDateFormatter::FULL, IntlDateFormatter::NONE);
                        echo $formatter->format(time());
                        ?>
                    </span>
                </div>
                <div class="featured_second">
                    <div class="row">
                        <?php while($related_cats_post->have_posts()): 
                            $related_cats_post->the_post();
                            $post__not_in[]=get_the_ID();
                            $time = get_the_time('U');
                            $fl++;
                            ?>
                            <div class="col-md-12 col-6">
                                <div class="item">
                                    <div class="row gx-lg-2">
                                        <div class="col-lg-4 col-md-5">
                                            <figure>
                                                <a href="<?php the_permalink(); ?>">
                                                    <img src="<?php echo get_template_directory_uri() ?>/assets/images/post-thumb3.jpg" class="img-fluid"
                                                        alt="">
                                                </a>
                                            </figure>
                                        </div>
                                        <div class="col-lg-8 col-md-7">
                                            <div class="text_box">
                                                <h3 class="fs-13 ff-title"><a href="<?php the_permalink(); ?>"><?php echo get_the_excerpt() ?></a></h3>
                                                <ul class="post_meta d-lg-none">
                                                    <?php custom_breadcrumb() ?>
                                        <li><?php echo human_time_diff($time, current_time('timestamp')) . ' trước'; ?></li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    <ul class="post_meta d-none d-lg-flex">
                                        <?php custom_breadcrumb() ?>
                                        <li><?php echo human_time_diff($time, current_time('timestamp')) . ' trước'; ?></li>
                                    </ul>
                                    <div class="post_excerpt fs-12">
                                        <p><?php echo get_the_excerpt() ?></p>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                </div>
            </div>
        <?php endif;wp_reset_postdata(); ?>
    </div>
    <hr class="breakline">
    <div class="row">
        <div class="col-lg-3 order-lg-3">
            <div class="sticky_sidebar">
                <?php get_template_part( 'template-parts/content-sidebar'); ?>
            </div>
        </div>
        <div class="col-lg-auto order-lg-2">
            <div class="vline"></div>
        </div>
        <div class="col-lg order-lg-0">
            <div class="press_post">
                <div class="title_group fw-bold fs-12">
                    <span>Press Releases</span>
                </div>
                <div class="row">
                    <?php
                    $query_args = array( 
                        'post_type'      => 'post',
                        'posts_per_page'  => '5',
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
                            <div class="col-lg col-md col-6 <?php if($fl==5) echo "d-none d-md-block";?>">
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
                    <!-- Query 5 bài. Bài cuối thêm class d-none.d-md-block -->
                </div>
                
                <div class="text-center">
                    <a href="<?php echo get_permalink($page_for_posts) ?>" class="btn btn-rounded">Xem thêm bài viết</a>
                </div>
            </div>
            <hr class="breakline">
            <div class="lasted_post">
                <div class="title_group fw-bold fs-12">
                    <span>Bài viết mới nhất</span>
                </div>
                <div class="list_post">
                    <?php
                    $query_args = array( 
                        'post_type'      => 'post',
                        'posts_per_page'  => '15',
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
                <div class="text-center load_post">
                    <a href="<?php echo get_permalink($page_for_posts) ?>" class="btn btn-large">Xem thêm bài viết</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
    get_footer();

