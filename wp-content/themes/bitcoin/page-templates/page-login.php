<?php
/*
    Template Name: Login
*/

if(is_user_logged_in()) {
    wp_redirect(home_url(''));
    exit;
}
require_once get_template_directory() . '/config.php';
$siteKey = RECAPTCHA_SITE_KEY;
$secretKey = RECAPTCHA_SECRET_KEY;
// Xử lý đăng nhập khi biểu mẫu được gửi
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login_nonce']) && wp_verify_nonce($_POST['login_nonce'], 'login_action')) {
    $email = sanitize_email($_POST['yourEmail']);
    $captcha = $_POST['g-recaptcha-response'];
    

    // Verify reCAPTCHA
    
    $response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=$secretKey&response=$captcha");
    $responseKeys = json_decode($response, true);

    if (!$responseKeys["success"]) {
        $error = "<p class='error-message'>Captcha verification failed. Please try again.</p>";
    } else {
        // Check if email exists
        $user = get_user_by('email', $email);
        if ($user) {
            wp_redirect(home_url('/email-login?user=' . $email));
            exit();
        } else {
            $error = "<p class='error-message'>Email hoặc mật khẩu không chính xác!</p>";
        }
    }
}

get_header();
?>

<div class="login_page">
    <div class="form_wrapper">
        <form class="needs-validation" novalidate action="" method="POST">
             <?php wp_nonce_field('login_action', 'login_nonce'); ?>
            <div class="text-center mb-4">
                <a href="/" class="logo_site">a
                    <img src="<?php echo get_template_directory_uri() ?>/assets/images/logo.png" class="img-fluid" alt="">
                </a>
            </div>
            <h3 class="fs-24 ff-title cl-black text-uppercase text-center mb-4">đăng nhập</h3>
            <ul class="login_by fs-16">
                <li><a href=""><img src="<?php echo get_template_directory_uri() ?>/assets/images/google-login.png" alt=""> Đăng nhập với Google</a></li>
                <li><a href=""><img src="<?php echo get_template_directory_uri() ?>/assets/images/fb-login.png" alt=""> Đăng nhập với Facebook</a></li>
                <li><a href=""><img src="<?php echo get_template_directory_uri() ?>/assets/images/apple-login.png" alt=""> Đăng nhập với Apple</a></li>
            </ul>
            <div class="by_email">
                <span>Hoặc</span>
            </div>
            <div class="valid_group mb-4">
                <input type="email" class="form-control" required placeholder="" id="yourEmail" name="yourEmail" >
                <label for="yourEmail">Địa chỉ email <span>*</span></label>
                <div class="invalid-feedback">
                    <i class="fa fa-exclamation-triangle"></i> Email không hợp lệ
                </div>
            </div>
            <div class="captcha mb-4">
                <div class="g-recaptcha" id="rcaptcha" data-sitekey="<?php echo $siteKey ?>"></div>
                <span id="captcha" style="color:red"></span>
            </div>
            <?php if (($error)): ?>
                <div class="alert alert-danger">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>
            <div class="mb-2">
                <input type="submit" class="btn btn-yellow w-100" value="Đăng nhập">
            </div>
            <div class="">Bạn chưa có tài khoản? <a href="register"><strong>Đăng ký</strong></a></div>
        </form>
    </div>
</div>
<script src='https://www.google.com/recaptcha/api.js'></script>

<?php
get_footer();
