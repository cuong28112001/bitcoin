<?php
/*
    Template Name: Register confirm
*/
if(is_user_logged_in()) {
    wp_redirect(home_url(''));
    exit;
}
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    session_start();
    if (isset($_POST['custom_register_nonce']) && wp_verify_nonce($_POST['custom_register_nonce'], 'custom_register_action')) {
        $email = sanitize_email($_POST['yourEmail']);
        // Tạo mã xác thực ngẫu nhiên
        $verification_code = wp_generate_password(6, false, false);
        // $verification_code ='123456';
        // Gửi email
        $subject = "Mã xác thực đăng ký";
        $message = "Mã xác thực của bạn là: " . $verification_code;
        $headers = array('Content-Type: text/html; charset=UTF-8');

        if (wp_mail($email, $subject, $message, $headers)) {
            $error= '<p class="success-message">Mã xác thực đã được gửi đến email của bạn.</p>';
        } else {
            $error= '<p class="error-message">Không thể gửi email, vui lòng thử lại.</p>';
        }
        $verification_code=md5($verification_code.'2025');
        $_SESSION['verification_code'] = $verification_code;
        $verification_email=md5($email.'2025');
        $_SESSION['verification_email'] = $verification_email;
    } elseif (isset($_POST['custom_register_validation_nonce']) && wp_verify_nonce($_POST['custom_register_validation_nonce'], 'custom_register_validation_action')) {
        $validation = sanitize_text_field($_POST['validation']);
        $email = sanitize_email($_POST['yourEmail']);
        if(md5($validation.'2025')===$_SESSION['verification_code']){
            unset($_SESSION['verification_code']);
            // Hiển thị form tự động submit bằng phương thức POST
            echo '<form id="redirectForm" action="' . esc_url(home_url('/create-password')) . '" method="POST">';
            echo wp_nonce_field('create_password_action', 'create_password_nonce'); // Thêm nonce
            echo '<input type="email" hidden name="email" value="' . $email . '">';
            echo '<input type="hidden" name="verification_email" value="' . $_SESSION['verification_email'] . '">';
            echo '</form>';
            echo '<script>document.getElementById("redirectForm").submit();</script>';
            exit;
        } else {
            $error= 'Mã xác thực không đúng. Vui lòng thử lại.';
        }
    }
} else {
    wp_redirect(home_url(''));
    exit;
}
get_header();
?>

<div class="login_page">
    <div class="form_wrapper">
        <form class="needs-validation" novalidate action="" method="POST">
            <?php wp_nonce_field('custom_register_validation_action', 'custom_register_validation_nonce'); ?>
            <div class="text-center mb-4">
                <a href="index.html" class="logo_site">
                    <img src="<?php echo get_template_directory_uri() ?>/assets/images/logo.png" class="img-fluid" alt="">
                </a>
            </div>
            <h3 class="fs-24 ff-title cl-black text-uppercase text-center mb-4">Xác minh danh tính của bạn</h3>
            <div class="confirm_info text-center fs-16 cl-black mb-3">
                Mã xác thực đã được gửi tới: <br>
                <?php echo $email ?>
                <input type="text" hidden name="yourEmail" value="<?php echo $email ?>">
                <input type="text" hidden name="verification_email" value="<?php echo $_SESSION['verification_email'] ?>">
            </div>
            <div class="reconfirm text-center mb-4 cl-black">
                Không nhận được mã xác thực? <a href=""><strong>Gửi lại</strong></a>
            </div>
            <div class="valid_group mb-4">
                <input type="text" minlength="6" maxlength="6" class="form-control" required name="validation">
                <label>Nhập mã bao gồm 6 chữ số <span>*</span></label>
                <div class="invalid-feedback">
                    <i class="fa fa-exclamation-triangle"></i> Mã xác nhận không hợp lệ
                </div>
            </div>
            <?php if (($error)): ?>
                <div class="alert alert-danger">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>
            <div class="mb-2">
                <input type="submit" class="btn btn-yellow w-100" value="Tiếp tục">
            </div>
            <div class="text-center"><a href="/register/"><strong>Quay lại</strong></a></div>
        </form>
    </div>
</div>


<?php
get_footer();
