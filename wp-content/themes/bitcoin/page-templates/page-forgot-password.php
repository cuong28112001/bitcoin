<?php
/*
    Template Name: Forgot Password
*/
if (is_user_logged_in()) {
    wp_redirect(home_url());
    exit;
}

get_header();
?>

<div class="login_page">
    <div class="form_wrapper">
        <form action="<?php echo esc_url(wp_lostpassword_url()); ?>" method="post">
            <div class="text-center mb-4">
                <a href="<?php echo home_url(); ?>" class="logo_site">
                    <img src="<?php echo get_template_directory_uri(); ?>/assets/images/logo.png" class="img-fluid" alt="">
                </a>
            </div>
            <h3 class="fs-24 ff-title cl-black text-uppercase text-center mb-4">QUÊN MẬT KHẨU</h3>
            
            <div class="confirm_info text-center fs-16 cl-black mb-3">
                Nhập địa chỉ Email của bạn và chúng tôi sẽ gửi cho bạn hướng dẫn đặt lại mật khẩu.
            </div>
            <div class="valid_group mb-4">
                <input type="email" name="user_login" class="form-control" required placeholder="Nhập email của bạn">
                <label for="user_login">Địa chỉ Email <span>*</span></label>
                <div class="invalid-feedback">
                    <i class="fa fa-exclamation-triangle"></i> Email không hợp lệ
                </div>
            </div>
            <div class="mb-2">
                <input type="submit" class="btn btn-yellow w-100" value="Tiếp tục">
            </div>
            <div class="text-center"><a href="<?php echo wp_login_url(); ?>"><strong>Quay lại đăng nhập</strong></a></div>
        </form>
    </div>
</div>

<?php
get_footer();
?>