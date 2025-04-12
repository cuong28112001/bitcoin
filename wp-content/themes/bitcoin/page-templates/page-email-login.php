<?php
/*
    Template Name: Email login
*/

if(is_user_logged_in()) {
    wp_redirect(home_url(''));
    exit;
}
$user = isset($_GET['user']) ? $_GET['user'] : (isset($_POST['yourEmail']) ? $_POST['yourEmail'] : '');
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Verify nonce
    if (!isset($_POST['custom_login_nonce']) || !wp_verify_nonce($_POST['custom_login_nonce'], 'custom_login_action')) {
        echo '<p class="error-message">Invalid request.</p>';
        return;
    }

    $creds = array(
        'user_login'    => sanitize_email($_POST['yourEmail']),
        'user_password' => $_POST['yourPass'],
        'remember'      => true
    );
    $user = wp_signon($creds, false);

    if (is_wp_error($user)) {
        $error = '<p class="error-message">' . esc_html($user->get_error_message()) . '</p>';
    } else {
        wp_redirect(home_url('')); // Redirect after successful login
        exit;
    }
}

get_header();
?>

<div class="login_page">
    <div class="form_wrapper">
        <form class="needs-validation" novalidate action="" method="POST">
            <?php wp_nonce_field('custom_login_action', 'custom_login_nonce'); ?>
            <div class="text-center mb-4">
                <a href="index.html" class="logo_site">
                    <img src="<?php echo get_template_directory_uri() ?>/assets/images/logo.png" class="img-fluid" alt="">
                </a>
            </div>
            <h3 class="fs-24 ff-title cl-black text-uppercase text-center mb-4">Nhập mật khẩu của bạn</h3>
            
            <div class="valid_group mb-4">
                <input type="email" class="form-control" required placeholder="" value="<?php echo $user ?>" id="yourEmail" name="yourEmail">
                <label for="yourEmail">Địa chỉ email <span>*</span></label>
                <div class="invalid-feedback">
                    <i class="fa fa-exclamation-triangle"></i> Email không hợp lệ
                </div>
            </div>
            <div class="valid_group mb-4">
                <input type="password" class="form-control" required placeholder="" id="yourPass" name="yourPass">
                <label for="yourPass">Mật khẩu <span>*</span></label>
                <div class="invalid-feedback">
                    <i class="fa fa-exclamation-triangle"></i> Vui lòng nhập mật khẩu
                </div>
            </div>
            <div class="captcha mb-4">
                <a href="forgot-password.html"><strong>Quên mật khẩu?</strong></a>
            </div>
            <?php if (($error)): ?>
                <div class="alert alert-danger">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>
            <div class="mb-2">
                <input type="submit" class="btn btn-yellow w-100" value="Tiếp tục">
            </div>
            <div class="">Bạn chưa có tài khoản? <a href="register.html"><strong>Đăng ký</strong></a></div>
        </form>
    </div>
</div>

<?php
get_footer();
