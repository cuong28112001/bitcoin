<?php
/*
    Template Name: Register
*/
if(is_user_logged_in()) {
    wp_redirect(home_url(''));
    exit;
}

get_header();
?>

<div class="login_page">
    <div class="form_wrapper">
        <form class="needs-validation" novalidate action="/register-confirm" method="POST" 	>
        	<?php wp_nonce_field('custom_register_action', 'custom_register_nonce'); ?>
            <div class="text-center mb-4">
                <a href="index.html" class="logo_site">
                    <img src="<?php echo get_template_directory_uri() ?>/assets/images/logo.png" class="img-fluid" alt="">
                </a>
            </div>
            <h3 class="fs-24 ff-title cl-black text-uppercase text-center mb-4">đăng ký</h3>
            <ul class="login_by fs-16">
                <li><a href=""><img src="<?php echo get_template_directory_uri() ?>/assets/images/google-login.png" alt=""> Đăng nhập với Google</a></li>
                <li><a href=""><img src="<?php echo get_template_directory_uri() ?>/assets/images/fb-login.png" alt=""> Đăng nhập với Facebook</a></li>
                <li><a href=""><img src="<?php echo get_template_directory_uri() ?>/assets/images/apple-login.png" alt=""> Đăng nhập với Apple</a></li>
            </ul>
            <div class="by_email">
                <span>Hoặc</span>
            </div>
            <div class="valid_group mb-4">
                <input type="email" class="form-control" required name="yourEmail">
                <label for="yourEmail">Địa chỉ email <span>*</span></label>
                <div class="invalid-feedback">
                    <i class="fa fa-exclamation-triangle"></i> Email không hợp lệ
                </div>
            </div>
            <div class="checkbox_group mb-4">
                <span class="wpcf7-form-control-wrap" data-name="acceptance">
                    <span class="wpcf7-form-control wpcf7-acceptance">
                        <span class="wpcf7-list-item">
                            <label>
                                <input type="checkbox" name="acceptance" required value="1" aria-invalid="false" data-gtm-form-interact-field-id="0">
                                <span class="wpcf7-list-item-label">Bằng cách tạo tài khoản, bạn đồng ý với <a href="">Điều khoản dịch vụ</a> và <a href="">Chính sách bảo mật</a> của Tạp Chí Bitcoin.</span>
                                <div class="invalid-feedback">
                                    <i class="fa fa-exclamation-triangle"></i> Vui lòng đồng ý với điều khoản
                                </div>
                            </label>
                        </span>
                    </span>
                </span>
            </div>
            <div class="mb-2">
                <input type="submit" class="btn btn-yellow w-100" value="Đăng ký">
            </div>
            <div class="">Bạn đã có tài khoản? <a href="login.html"><strong>Đăng nhập</strong></a></div>
        </form>
    </div>
</div>


<?php
get_footer();
