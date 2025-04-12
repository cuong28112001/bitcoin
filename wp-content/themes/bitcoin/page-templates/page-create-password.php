<?php
/*
    Template Name: Create password
*/
if(is_user_logged_in()) {
    wp_redirect(home_url(''));
    exit;
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
	session_start();

	if (isset($_POST['create_password_nonce']) && wp_verify_nonce($_POST['create_password_nonce'], 'create_password_action')) {
		$email = sanitize_email($_POST['email']);
		$verification_email = sanitize_text_field($_POST['verification_email']);
		$_SESSION['verified_email'] = ($verification_email);
		var_dump($_SESSION['verified_email']);
	}elseif (isset($_POST['create_user_nonce']) && wp_verify_nonce($_POST['create_user_nonce'], 'create_user_action')){
	    global $wpdb;
	    $email = sanitize_email($_POST['email']);
	    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
	    if(md5($email.'2025')!=$_SESSION['verified_email']) {
    		// wp_redirect(home_url(''));
    		// exit;
    	} else {
    		unset($_SESSION['verified_email']);
    	}
	    // Kiểm tra xem email đã tồn tại hay chưa
	    $user_id = email_exists($email);
	    if (!$user_id) {
	        // Tạo mới user
	        $user_id = wp_create_user($email, $_POST['password'], $email);
	        if (is_wp_error($user_id)) {
	            $error= "<p class='error-message'>Đã có lỗi xảy ra khi tạo tài khoản!</p>";
	            exit();
	        }
	        wp_redirect(home_url('/success'));
	    } else {
	        $error= "<p class='error-message'>Email này đã tồn tại, vui lòng đăng nhập!</p>";
	    }
    }
}

get_header();
?>

<div class="login_page">
                <div class="form_wrapper">
                    <form class="needs-validation" novalidate method="POST" action="">
    					<?php wp_nonce_field('create_user_action', 'create_user_nonce'); ?>

                        <div class="text-center mb-4">
                            <a href="index.html" class="logo_site">
                                <img src="<?php echo get_template_directory_uri() ?>/assets/images/logo.png" class="img-fluid" alt="">
                            </a>
                        </div>
                        <h3 class="fs-24 ff-title cl-black text-uppercase text-center mb-4">tạo mật khẩu</h3>
                        
                        <div class="valid_group mb-4">
                            <input type="email" class="form-control" required value="<?php echo $email ?>" name="email" readonly>
                            <div class="invalid-feedback">
                                <i class="fa fa-exclamation-triangle"></i> Email không hợp lệ
                            </div>
                        </div>
                        <div class="valid_group mb-4">
                            <input type="password" class="form-control" required placeholder="" id="password-input" name="password">
                            <label for="yourPass">Mật khẩu <span>*</span></label>
                            <div class="showPass"><i class="fa fa-eye" aria-hidden="true"></i></div>
                            <div class="invalid-feedback">
                                <i class="fa fa-exclamation-triangle"></i> Mật khẩu quá yếu
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
                        <div class="text-center"><a href="/register/"><strong>Quay lại</strong></a></div>
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
