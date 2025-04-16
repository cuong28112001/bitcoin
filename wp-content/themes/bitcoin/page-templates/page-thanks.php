<?php
/*
    Template Name: Thank you
*/
get_header();
?>

<div class="seach_page">
    <div class="head_search">
        <h1 class="fs-48 ff-title cl-black mb-3"><?php the_title() ?></h1>
        <div class="fs-24 cl-black mb-3">
            <?php the_content() ?>
        </div>
        <div class="fs-14">
            Quay lại <a href="<?php echo home_url() ?>" class="fs-12 fw-bold btn btn-seemore">Trang chủ</a>
        </div>
    </div>
    <div class="search_result">
        <div class="title_group fw-bold fs-12">
            <span>Bài mới</span>
        </div>
        <div class="list_post">
            <div class="row">
                <?php
                $p = get_query_var('paged') ? get_query_var('paged') : 1;
                $query_args = array( 
                    'post_type'      => 'post',
                    'posts_per_page'  => '8',
                    'paged' => $p,
                    // 'post__not_in'  => $post__not_in,
                   
                 );
                $related_cats_post = new WP_Query( $query_args );
                if($related_cats_post->have_posts()):
                    while($related_cats_post->have_posts()): 
                        $related_cats_post->the_post();
                        $post__not_in[]=get_the_ID();
                        $time = get_the_time('U');
                        ?>
                        <div class="col-lg-3 col-md-4 col-6">
                            <div class="item">
                                <figure>
                                    <a href="<?php the_permalink(); ?>"><img src="<?php echo get_the_post_thumbnail_url()?>" class="img-fluid" alt=""></a>
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
    </div>
    <div class="ads_footer">
        <div class="ads_box">
            <a href="">
                <img src="<?php echo get_template_directory_uri() ?>/assets/images/ads-980x120.jpg" alt="">
            </a>
        </div>
    </div>
</div>


<?php
get_footer();
