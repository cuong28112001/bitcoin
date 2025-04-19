<?php get_header(); ?>
<?php
$current_post_id = get_the_ID();
$categories = wp_get_post_categories($current_post_id);
$current_author_id = get_the_author_meta('ID');
$page_for_posts = get_option('page_for_posts');
?>
<div class="body_content news_page">
                <div class="row">
                    <div class="col-lg-auto order-lg-2">
                        <div class="vline"></div>
                    </div>
                    <div class="col-lg order-lg-0">
                        <div class="single_content">
                            <div class="jbreadcrumb fs-14">
                                <ul>
                                    <?php custom_breadcrumb_single() ?>
                                </ul>
                            </div>
                            <h1 class="fs-32 ff-title cl-black mb-3"><?php the_title() ?></h1>
                            <div class="fs-18 mb-4">
                                <p><?php echo get_the_excerpt(); ?></p>
                            </div>
                            <div class="single_meta">
                                <div class="row justify-content-between">
                                    <div class="col-lg col-auto order-lg-3">
                                        <div class="post_social">
                                            <ul>
                                                <li><a href=""><img src="<?php echo get_template_directory_uri() ?>/assets/images/facebook.png" alt=""></a></li>
                                                <li><a href=""><img src="<?php echo get_template_directory_uri() ?>/assets/images/x.png" alt=""></a></li>
                                                <li><a href=""><img src="<?php echo get_template_directory_uri() ?>/assets/images/telegram.png" alt=""></a></li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="col-lg order-lg-1">
                                        <div class="post_update">
                                            <div class="fs-14 cl-black"><?php echo get_reading_time($current_post_id) ?></div>
                                            <div class="cl-grey fs-11">
                                                <?php
                                                    echo 'Updated: ' . get_the_modified_time('M. j, Y') . ' at ' . get_the_modified_time('g:i a') . ' UTC';
                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-auto order-lg-2">
                                        <div class="fake_line"></div>
                                    </div>
                                    
                                </div>
                            </div>
                            <div class="ads_top">
                                <div class="ads_box">
                                    <?php echo get_field('ads_top', 'option'); ?>
                                </div>
                            </div>
                            <div class="post_content">
                                <?php the_content() ?>
                                
                                <ul class="post_cate fs-11">
                                    <li>DANH MỤC:</li>
                                    <li><a href="">BITCOIN</a></li>
                                    <li><a href="">CRYPTO</a></li>
                                </ul>
                                <div class="author_detail">
                                    <div class="owner_title fs-10 fw-bold">
                                        <span>EDITOR</span>
                                    </div>
                                    <div class="owner">
                                        <figure>
                                            <img src="<?php echo get_template_directory_uri() ?>/assets/images/adejumo.jpg" class="img-fluid" alt="">
                                        </figure>
                                        <div class="j_title mb-2">
                                            <div class="name fs-14 cl-black">Oluwapelumi Adejumo</div>
                                            <div class="cl-grey fs-11">Jan. 2, 2025 at 10:13 am UTC</div>
                                        </div>
                                        <div class="desc fs-12 cl-grey">
                                            <p>Also known as "Akiba," Liam Wright is the Editor-in-Chief at CryptoSlate and host of the SlateCast. He believes that decentralized technology has the potential to make widespread...</p>
                                        </div>
                                        <div class="owner_social fs-8">
                                            <ul>
                                                <li><a href=""><img src="<?php echo get_template_directory_uri() ?>/assets/images/x.png" class="img-fluid" alt=""> @hardeyjumoh</a></li>
                                                <li><a href=""><img src="<?php echo get_template_directory_uri() ?>/assets/images/linkedin.png" class="img-fluid" alt="">LinkedIn</a></li>
                                                <li><a href=""><img src="<?php echo get_template_directory_uri() ?>/assets/images/email.png" class="img-fluid" alt=""> Email Oluwapelumi</a></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="post_on_social">
                                    <div class="mb-4">
                                        <div class="sharing">
                                            <div class="fb-like"
                                                data-href="https://developers.facebook.com/docs/plugins/"
                                                data-width="300" data-layout="" data-action="" data-size=""
                                                data-share="false"></div>
                                        </div>
                                    </div>
                                    <div class="row justify-content-between">
                                        <div class="col">
                                            <ul>
                                                <li><a href=""><img src="<?php echo get_template_directory_uri() ?>/assets/images/facebook.png" class="img-fluid"
                                                            alt=""></a></li>
                                                <li><a href=""><img src="<?php echo get_template_directory_uri() ?>/assets/images/x.png" class="img-fluid"
                                                            alt=""></a></li>
                                                <li><a href=""><img src="<?php echo get_template_directory_uri() ?>/assets/images/linkedin.png" class="img-fluid"
                                                            alt=""></a></li>
                                            </ul>
                                        </div>
                                        <div class="col-auto">
                                            <a href="" class="ggnews fs-16 fw-bold">Tạp chí Bitcoin trên <img
                                                    src="<?php echo get_template_directory_uri() ?>/assets/images/ggnews.png" class="img-fluid" alt=""></a>
                                        </div>
                                    </div>
                                </div>
                                <div class="comment_post">
                                    <div class="fs-24 fw-bold cl-black mb-3">
                                        Bình luận
                                    </div>

                                    <?php if (comments_open() || get_comments_number()) : ?>
                                        <div class="comment-list">
                                            <?php comments_template(); ?>
                                        </div>
                                    <?php endif; ?>

                                    <?php if (is_user_logged_in()) : ?>
                                        <form action="<?php echo site_url('/wp-comments-post.php'); ?>" method="post">
                                            <div class="input-group mb-3">
                                                <input type="text" name="comment" class="form-control" placeholder="Chia sẻ ý kiến của bạn" required>
                                                <button class="btn btn-outline-secondary" type="submit"><i class="fa fa-angle-right"></i></button>
                                            </div>

                                            <?php comment_id_fields(); ?>
                                            <?php do_action('comment_form', get_the_ID()); ?>
                                        </form>
                                    <?php else : ?>
                                        <p>Bạn cần <a href="<?php echo home_url('/login/'); ?>">đăng nhập</a> để bình luận.</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="near_post">
                                <div class="row gx-0">
                                    <div class="col-6">
                                        <div class="prev_post">
                                            <div class="j_title fs-10 fw-bold">
                                                <span>BÀI TRƯỚC</span>
                                            </div>
                                            <h3 class="fs-13 ff-title"><a href="">Tin vắn Crypto 04/01: Bitcoin đang
                                                    chuẩn bị cho những mức đỉnh kỷ lục mới cùng tin tức ETF, DOGE, Base,
                                                    AERO, Solv Protocol</a></h3>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="next_post">
                                            <div class="j_title fs-10 fw-bold text-end">
                                                <span>BÀI SAU</span>
                                            </div>
                                            <h3 class="fs-13 ff-title"><a href="">15 ví nội gián kiếm được 20 triệu đô
                                                    la từ việc ra mắt token FOCAI trên Solana</a></h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="related_post">
                                <div class="row">
                                    <div class="col">
                                        <ul class="nav nav-tabs" id="relateTab" role="tablist">
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link fs-12 fw-bold cl-black active" id="samecate-tab"
                                                    data-arrow="arrows_1" data-bs-toggle="tab"
                                                    data-bs-target="#samecate-tab-pane" type="button" role="tab"
                                                    aria-controls="samecate-tab-pane" aria-selected="true">Cùng chuyên
                                                    mục</button>
                                            </li>
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link fs-12 fw-bold cl-black" id="sameauthor-tab"
                                                    data-arrow="arrows_2" data-bs-toggle="tab"
                                                    data-bs-target="#sameauthor-tab-pane" type="button" role="tab"
                                                    aria-controls="sameauthor-tab-pane" aria-selected="false">Cùng tác
                                                    giả</button>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="col-auto">
                                        <div class="arrow_slider arrows_1 active">
                                            <div class="prev_slider">
                                                <a href=""><i class="fa fa-angle-left"></i></a>
                                            </div>
                                            <div class="next_slider">
                                                <a href=""><i class="fa fa-angle-right"></i></a>
                                            </div>
                                        </div>
                                        <div class="arrow_slider arrows_2">
                                            <div class="prev_slider">
                                                <a href=""><i class="fa fa-angle-left"></i></a>
                                            </div>
                                            <div class="next_slider">
                                                <a href=""><i class="fa fa-angle-right"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-content" id="relateTabContent">
                                    <div class="tab-pane fade show active" id="samecate-tab-pane" role="tabpanel"
                                        aria-labelledby="samecate-tab" tabindex="0">
                                        <div class="related_slider_wrapper">
                                            <div class="slider_samecate related_slider" data-arrow="arrows_1">
                                                <?php
                                                $args = [
                                                    'post_type'      => 'post',
                                                    'posts_per_page' => 12, // Số bài viết muốn hiển thị
                                                    'category__in'   => $categories, // Lọc theo danh mục của bài viết hiện tại
                                                    'post__not_in'   => [$current_post_id], // Loại bỏ bài viết hiện tại
                                                ];

                                                $query = new WP_Query($args);

                                                if($query->have_posts()):
                                                    $fl=0;
                                                    while($query->have_posts()): 
                                                        $query->the_post();
                                                        $post__not_in[]=get_the_ID();
                                                        $time = get_the_time('U');
                                                        $fl++;
                                                        ?>
                                                        <div class="item">
                                                            <figure>
                                                                <a href="<?php the_permalink(); ?>"><img
                                                                        src="<?php echo get_the_post_thumbnail_url()?>" class="img-fluid"
                                                                        alt=""></a>
                                                            </figure>
                                                            <h3 class="fs-13 ff-title"><a href="<?php the_permalink(); ?>"><?php echo get_the_title()?></a></h3>
                                                            <ul class="post_meta">
                                                                <li><?php echo get_the_date('d/m/Y'); ?></li>
                                                                <li><?php echo get_the_time('H:i'); ?></li>
                                                            </ul>
                                                        </div>
                                                    <?php endwhile; ?>
                                                <?php endif;wp_reset_postdata(); ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="sameauthor-tab-pane" role="tabpanel"
                                        aria-labelledby="sameauthor-tab" tabindex="0">
                                        <div class="related_slider_wrapper">
                                            <div class="slider_samecate related_slider" data-arrow="arrows_2">
                                                <?php
                                                

                                                $args = [
                                                    'post_type'      => 'post',
                                                    'posts_per_page' => 12, // Số bài viết muốn hiển thị
                                                    'author'         => $current_author_id, // Lọc bài viết theo tác giả hiện tại
                                                    'post__not_in'   => [$current_post_id], // Loại bỏ bài viết hiện tại
                                                ];

                                                $query = new WP_Query($args);

                                                if($query->have_posts()):
                                                    $fl=0;
                                                    while($query->have_posts()): 
                                                        $query->the_post();
                                                        $post__not_in[]=get_the_ID();
                                                        $time = get_the_time('U');
                                                        $fl++;
                                                        ?>
                                                        <div class="item">
                                                            <figure>
                                                                <a href="<?php the_permalink(); ?>"><img
                                                                        src="<?php echo get_the_post_thumbnail_url()?>" class="img-fluid"
                                                                        alt=""></a>
                                                            </figure>
                                                            <h3 class="fs-13 ff-title"><a href="<?php the_permalink(); ?>"><?php the_title() ?></a></h3>
                                                            <ul class="post_meta">
                                                                <li><?php echo get_the_date('d/m/Y'); ?></li>
                                                                <li><?php echo get_the_time('H:i'); ?></li>
                                                            </ul>
                                                        </div>
                                                    <?php endwhile; ?>
                                                <?php endif;wp_reset_postdata(); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="ads_top">
                                <div class="ads_box">
                                    <?php echo get_field('ads_top', 'option'); ?>
                                </div>
                            </div>
                            <div class="lasted_post">
                                <div class="title_group fw-bold fs-12">
                                    <span>Mới cập nhật</span>
                                </div>
                                <div class="list_post">
                                    <?php
                                    $query_args = array( 
                                        'post_type'      => 'post',
                                        'posts_per_page'  => '10',
                                        'post__not_in'   => [$current_post_id],
                                       
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
                                                        <h3 class="fs-18 ff-title"><a href="<?php the_permalink(); ?>"><?php the_title() ?></a></h3>
                                                        <ul class="post_meta">
                                                            <?php custom_breadcrumb() ?>
                                                            <li><?php echo human_time_diff($time, current_time('timestamp')) . ' trước'; ?></li>
                                                        </ul>
                                                    </div>
                                                    <div class="col-lg-3 col-md-3 col-5 order-md-2">
                                                        <figure>
                                                            <a href="<?php the_permalink(); ?>">
                                                                <img src="<?php echo get_the_post_thumbnail_url()?>" class="img-fluid"
                                                                    alt="">
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
                    <div class="col-lg-3 order-lg-3">
                        <div class="sticky_sidebar no_sticky">
                            <div class="row">
                                <div class="col-lg-12 col-md-6">
                                    <div class="sidebar_ads">
                                        <div class="ads_box">
                                            <?php echo get_field('ads_sidebar', 'option'); ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-12 col-md-6">
                                    <?php get_template_part( 'template-parts/content-sidebar'); ?>
                                    <div class="jtag line_bottom fs-12 fw-bold mt-4">
                                        <span>Được đề cập trong bài viết</span>
                                    </div>
                                    <div class="coin_inpost">
                                        <div class="head">
                                            <div class="jlogo">
                                                <a href="price-detail.html">
                                                    <img src="<?php echo get_template_directory_uri() ?>/assets/images/bitget.png" class="img-fluid" alt="">
                                                </a>
                                            </div>
                                            <div class="jtitle">
                                                <a href="price-detail.html">
                                                    <div class="fs-13">THORChain</div>
                                                    <div class="fs-10 fw-bold cl-grey">
                                                        RUNE (24h)
                                                    </div>
                                                </a>
                                            </div>
                                            <div class="jnumber">
                                                <div class="fs-13">$1.22345</div>
                                                <div class="fs-10 fw-bold cl-grey">
                                                    -2.19%
                                                </div>
                                            </div>
                                        </div>
                                        <figure>
                                            <img src="<?php echo get_template_directory_uri() ?>/assets/images/thor.jpg" class="img-fluid" alt="">
                                        </figure>
                                        <div class="fs-12">
                                            <p>THORChain được xây dựng để thanh khoản tài sản kỹ thuật số không cần xin phép trên nhiều chuỗi.</p>
                                        </div>
                                        <div class="gopost">
                                            <a href="">Tìm hiểu thêm về THORChain</a>
                                        </div>
                                    </div>
                                    <div class="coin_inpost">
                                        <div class="head">
                                            <div class="jlogo">
                                                <a href="price-detail.html">
                                                    <img src="<?php echo get_template_directory_uri() ?>/assets/images/ethereum.png" class="img-fluid" alt="">
                                                </a>
                                            </div>
                                            <div class="jtitle">
                                                <a href="price-detail.html">
                                                    <div class="fs-13">Ethereum</div>
                                                    <div class="fs-10 fw-bold cl-grey">
                                                        BTC (24h)
                                                    </div>
                                                </a>
                                            </div>
                                            <div class="jnumber">
                                                <div class="fs-13">$2,212.43</div>
                                                <div class="fs-10 fw-bold cl-grey">
                                                    +1.08%
                                                </div>
                                            </div>
                                        </div>
                                        <div class="fs-12">
                                            <p>Ethereum là một nền tảng blockchain phi tập trung, mã nguồn mở cho phép tạo ra các hợp đồng thông minh và ứng dụng phi tập trung (DApp).</p>
                                        </div>
                                        <div class="gopost">
                                            <a href="">Tìm hiểu thêm về THORChain</a>
                                        </div>
                                    </div>
                                    <div class="coin_inpost">
                                        <div class="head">
                                            <div class="jlogo">
                                                <a href="price-detail.html">
                                                    <img src="<?php echo get_template_directory_uri() ?>/assets/images/coin-logo.png" class="img-fluid" alt="">
                                                </a>
                                            </div>
                                            <div class="jtitle">
                                                <a href="price-detail.html">
                                                    <div class="fs-13">Bitcoin</div>
                                                    <div class="fs-10 fw-bold cl-grey">
                                                        BTC (24h)
                                                    </div>
                                                </a>
                                            </div>
                                            <div class="jnumber">
                                                <div class="fs-13">$99,999.89</div>
                                                <div class="fs-10 fw-bold cl-grey">
                                                    +0.48%
                                                </div>
                                            </div>
                                        </div>
                                        <div class="fs-12">
                                            <p>Bitcoin, một loại tiền tệ phi tập trung không chịu sự chi phối của các ngân hàng trung ương hay quản trị viên, được giao dịch điện tử, không qua trung gian thông qua mạng ngang hàng.</p>
                                        </div>
                                        <div class="gopost">
                                            <a href="">Tìm hiểu thêm về THORChain</a>
                                        </div>
                                    </div>
                                    <div class="coin_inpost">
                                        <div class="head">
                                            <div class="jlogo">
                                                <a href="price-detail.html">
                                                    <img src="<?php echo get_template_directory_uri() ?>/assets/images/bybit.png" class="img-fluid" alt="">
                                                </a>
                                            </div>
                                            <div class="jtitle">
                                                <a href="price-detail.html">
                                                    <div class="fs-13">Bybit</div>
                                                    <div class="fs-10 fw-bold cl-grey">
                                                        Exchange Company in Asia
                                                    </div>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="fs-12">
                                            <p>Bybit là một nền tảng giao dịch phái sinh tiền điện tử được thành lập vào tháng 3 năm 2018 và được đăng ký tại BVI.</p>
                                        </div>
                                        <div class="gopost">
                                            <a href="">Tìm hiểu thêm về THORChain</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
<?php get_footer() ?>