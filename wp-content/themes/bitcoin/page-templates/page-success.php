<?php
/*
    Template Name: Success
*/
get_header();
?>

<div class="login_page">
    <div class="form_wrapper">
        <div class="success_info text-center cl-black">
            <div class="mb-4">
                <img src="<?php echo get_template_directory_uri() ?>/assets/images/success.png" class="img-fluid" alt="">
            </div>
            Mật khẩu của bạn đã được đặt lại thành công. <a href="/login"><strong>Đăng nhập</strong></a>
        </div>
    </div>
</div>


<?php
get_footer();
