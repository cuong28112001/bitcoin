<?php

get_header();
$s = sanitize_text_field($_GET['s']);
?>

<div class="seach_page">
    <div class="row">
        <div class="col-lg-9">
            <div class="head_search">
                <div class="jbreadcrumb fs-14">
                    <ul>
                        <li><a href="<?php echo home_url() ?>">Trang chủ</a></li>
                        <li>Tìm kiếm</li>
                    </ul>
                </div>
                <h1 class="fs-24 ff-title cl-black mb-3">Tìm kiếm “<?php echo $s ?>”</h1>
                <form action="" class="search_people">
                    <div class="input-group mb-3">
                        <input type="text" class="form-control" placeholder="Tìm kiếm..." value="Bitcoin">
                        <button class="btn btn-outline-secondary" type="button" id="button-addon2">
                            <img src="<?php echo get_template_directory_uri() ?>/assets/images/search-icon.svg" class="img-fluid" alt="">
                        </button>
                    </div>
                </form>
                <div class="fs-14 cl-black">
                    Vui lòng tìm lại nếu chưa hài lòng
                </div>
            </div>
            <?php
            $p = get_query_var('paged') ? get_query_var('paged') : 1;
            $query_args = array( 
                'post_type'      => 'post',
                'posts_per_page'  => '8',
                'paged' => $p,
                's' => $s
                // 'post__not_in'  => $post__not_in,
               
             );
            $related_cats_post = new WP_Query( $query_args );
            ?>
            <div class="search_result">
                <div class="fs-13 fw-bold total_result">
                    <?php echo $related_cats_post->found_posts ?> bài viết phù hợp
                </div>
                <?php if($related_cats_post->have_posts()): ?>
	                <div class="list_post">
	                    <div class="row">
	                    	<?php
	                    		while($related_cats_post->have_posts()): 
		                            $related_cats_post->the_post();
		                            $post__not_in[]=get_the_ID();
		                            $time = get_the_time('U');
			                    	?>
			                        <div class="col-lg-3 col-md-4 col-6">
			                            <div class="item">
			                                <figure>
			                                    <a href="<?php the_permalink(); ?>"><img src="<?php echo get_the_post_thumbnail_url()?>"
			                                            class="img-fluid" alt=""></a>
			                                </figure>
			                                <h3 class="fs-13 ff-title"><a href="<?php the_permalink(); ?>"><?php echo get_the_title() ?></a>
			                                </h3>
			                                <ul class="post_meta">
			                                    <li><?php echo get_the_date('d/m/Y'); ?></li>
                                                <li><?php echo get_the_time('H:i'); ?></li>
			                                </ul>
			                            </div>
			                        </div>
			                    <?php endwhile ?>
	                    </div>
	                    <div class="j_paging mt-3">
	                        <?php wp_pagenavi(array('query' => $related_cats_post) ); wp_reset_postdata(); ?>
	                    </div>
	                </div>
                <?php endif ?>
            </div>
            <div class="ads_footer">
                <div class="ads_box">
                    <a href="">
                        <img src="<?php echo get_template_directory_uri() ?>/assets/images/ads-980x120.jpg" alt="">
                    </a>
                </div>
            </div>
        </div>
        <div class="col-lg-3">
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
    </div>
</div>


<?php
get_footer();
