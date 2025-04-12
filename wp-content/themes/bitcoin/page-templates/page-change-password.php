<?php
/* Template Name: Change Password */
if (!is_user_logged_in()) {
    wp_redirect(home_url('/login/'));
    exit;
}

// Handle password change request
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['change_password_nonce']) && wp_verify_nonce($_POST['change_password_nonce'], 'change_password_action')) {
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Password validation
    if (strlen($password) < 8 || !preg_match('/[a-z]/', $password) || !preg_match('/[A-Z]/', $password) || !preg_match('/[0-9]/', $password) || !preg_match('/[!@#$%^&*]/', $password)) {
        $error =  "<p class='error-message'>Mật khẩu không đạt yêu cầu bảo mật.</p>";
    }

    if ($password !== $confirm_password) {
        $error =  "<p class='error-message'>Mật khẩu xác nhận không khớp.</p>";
    }
    if(!$error) {
        wp_set_password($password, $current_user->ID);
        wp_redirect(home_url('/success/'));
    }
    
    exit();
}
get_header();

$current_user = wp_get_current_user();
?>

<div class="login_page">
    <div class="form_wrapper">
        <form class="needs-validation" novalidate method="POST" action="">
            <?php wp_nonce_field('change_password_action', 'change_password_nonce'); ?>

            <div class="text-center mb-4">
                <a href="<?php echo esc_url(home_url('/')); ?>" class="logo_site">
                    <img src="<?php echo get_template_directory_uri(); ?>/assets/images/logo.png" class="img-fluid" alt="">
                </a>
            </div>
            <h3 class="fs-24 ff-title cl-black text-uppercase text-center mb-4">Thay đổi mật khẩu</h3>

            <div class="confirm_info text-center fs-16 cl-black mb-3">
                Nhập mật khẩu mới cho <br>
                <?php echo esc_html($current_user->user_email); ?>
            </div>

            <div class="valid_group mb-4">
                <input type="password" class="form-control" required placeholder="" id="password-input" name="password">
                <label for="password-input">Mật khẩu mới <span>*</span></label>
                <div class="showPass"><i class="fa fa-eye" aria-hidden="true"></i></div>
                <div class="invalid-feedback">
                    <i class="fa fa-exclamation-triangle"></i> Mật khẩu quá yếu
                </div>
            </div>

            <div class="valid_group mb-4">
                <input type="password" class="form-control" required placeholder="" name="confirm_password">
                <label for="confirm_password">Xác nhận mật khẩu <span>*</span></label>
                <div class="showPass"><i class="fa fa-eye" aria-hidden="true"></i></div>
                <div class="invalid-feedback">
                    <i class="fa fa-exclamation-triangle"></i> Mật khẩu không khớp
                </div>
            </div>

            <div class="password-container mb-4">
                Mật khẩu của bạn phải chứa:
                <ul class="conditions">
                    <li data-condition="length">Ít nhất 8 ký tự</li>
                    <li data-condition="threeOfFour">Ít nhất 3 trong số những điều sau đây:
                        <ul>
                            <li data-condition="lowercase">Chữ thường (a-z)</li>
                            <li data-condition="uppercase">Chữ in hoa (A-Z)</li>
                            <li data-condition="number">Số (0-9)</li>
                            <li data-condition="special">Ký tự đặc biệt (!@#$%^&*)</li>
                        </ul>
                    </li>
                </ul>
            </div>
            <?php if (($error)): ?>
                <div class="alert alert-danger">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>
            <div class="mb-2">
                <input type="submit" class="btn btn-yellow w-100" value="Tiếp tục">
            </div>

            <div class="text-center">
                <a href="<?php echo esc_url(home_url('/account/')); ?>"><strong>Quay lại</strong></a>
            </div>
        </form>
    </div>
</div>
<script>
$(document).ready(function() {
    const passwordInput = $("#password-input");
    const submitButton = $("input[type='submit']");
    
    function validatePassword(password) {
        let conditions = {
            length: password.length >= 8,
            lowercase: /[a-z]/.test(password),
            uppercase: /[A-Z]/.test(password),
            number: /[0-9]/.test(password),
            special: /[!@#$%^&*]/.test(password)
        };

        let validCount = Object.values(conditions).filter(Boolean).length;
        return validCount >= 3;
    }

    passwordInput.on("input", function() {
        let password = passwordInput.val();
        let isValid = validatePassword(password);

        // Cập nhật màu sắc điều kiện
        $("[data-condition='length']").css("color", password.length >= 8 ? "green" : "red");
        $("[data-condition='lowercase']").css("color", /[a-z]/.test(password) ? "green" : "red");
        $("[data-condition='uppercase']").css("color", /[A-Z]/.test(password) ? "green" : "red");
        $("[data-condition='number']").css("color", /[0-9]/.test(password) ? "green" : "red");
        $("[data-condition='special']").css("color", /[!@#$%^&*]/.test(password) ? "green" : "red");

        $("[data-condition='threeOfFour']").css("color", isValid ? "green" : "red");

        // Vô hiệu hóa nút submit nếu mật khẩu chưa đạt yêu cầu
        submitButton.prop("disabled", !isValid);
    });
});
</script>
<?php
get_footer();

?>