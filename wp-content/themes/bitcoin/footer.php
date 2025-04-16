<?php $footer = get_field('footer_group', 'option'); ?>
<!-- Footer bắt đầu từ đây -->
            <footer id="footer_site">
                <div class="row">
                    <div class="col-lg-2">
                        <a href="<?php echo home_url() ?>" class="logo_ft">
                            <img src="<?php echo $footer['logo_footer'] ?>" class="img-fluid" alt="">
                        </a>
                    </div>
                    <div class="col-lg-2 col-md-3 col-6">
                        <?php foreach ($footer['menu_column_1'] as $key => $menu_column): ?>
                            <h4><?php echo $menu_column['menu_title'] ?></h4>
                            <ul>
                                <?php foreach ($menu_column['menu_item'] as $key => $menu_item): ?>
                                    <?php if ($menu_item['link']): ?>
                                        <?php if ($menu_item['link']['url']&&$menu_item['link']['url']!='#'): ?>
                                            <li><a href="<?php echo $menu_item['link']['url'] ?>"><?php echo $menu_item['link']['title'] ?></a></li>
                                        <?php else: ?>
                                            <li><?php echo $menu_item['link']['title'] ?></li>
                                        <?php endif ?>
                                    <?php endif ?>
                                <?php endforeach ?>
                            </ul>
                        <?php endforeach ?>
                    </div>
                    <div class="col-lg-2 col-md-3 col-6">
                        <?php foreach ($footer['menu_column_2'] as $key => $menu_column): ?>
                            <h4><?php echo $menu_column['menu_title'] ?></h4>
                            <ul>
                                <?php foreach ($menu_column['menu_item'] as $key => $menu_item): ?>
                                    <?php if ($menu_item['link']): ?>
                                        <?php if ($menu_item['link']['url']&&$menu_item['link']['url']!='#'): ?>
                                            <li><a href="<?php echo $menu_item['link']['url'] ?>"><?php echo $menu_item['link']['title'] ?></a></li>
                                        <?php else: ?>
                                            <li><?php echo $menu_item['link']['title'] ?></li>
                                        <?php endif ?>
                                    <?php endif ?>
                                <?php endforeach ?>
                            </ul>
                        <?php endforeach ?>
                    </div>
                    <div class="col-lg-2 col-md-3 col-6">
                        <?php foreach ($footer['menu_column_3'] as $key => $menu_column): ?>
                            <h4><?php echo $menu_column['menu_title'] ?></h4>
                            <ul>
                                <?php foreach ($menu_column['menu_item'] as $key => $menu_item): ?>
                                    <?php if ($menu_item['link']): ?>
                                        <?php if ($menu_item['link']['url']&&$menu_item['link']['url']!='#'): ?>
                                            <li><a href="<?php echo $menu_item['link']['url'] ?>"><?php echo $menu_item['link']['title'] ?></a></li>
                                        <?php else: ?>
                                            <li><?php echo $menu_item['link']['title'] ?></li>
                                        <?php endif ?>
                                    <?php endif ?>
                                <?php endforeach ?>
                            </ul>
                        <?php endforeach ?>
                    </div>
                    <div class="col-lg-4 col-md-3 col-6">
                        <h4><?php echo $footer['title_social'] ?></h4>
                        <ul class="social">
                            <?php foreach ($footer['list_social'] as $key => $list_social): ?>
                                <li><a href="<?php echo $list_social['link'] ?>"><img src="<?php echo $list_social['icon'] ?>" class="img-fluid" alt=""></a></li>
                            <?php endforeach ?>
                        </ul>
                    </div>
                </div>
                <div class="policy fs-8"><?php echo $footer['content_policy'] ?></div>
                <div class="copyright">
                    <ul class="fs-8 fw-bold">
                        <?php foreach ($footer['list_link_bottom'] as $key => $list_link_bottom): ?>
                            <?php if ($list_link_bottom['link']): ?>
                                <?php if ($list_link_bottom['link']['url']&&$list_link_bottom['link']['url']!='#'): ?>
                                    <li><a href="<?php echo $list_link_bottom['link']['url'] ?>"><?php echo $list_link_bottom['link']['title'] ?></a></li>
                                <?php else: ?>
                                    <li><?php echo $list_link_bottom['link']['title'] ?></li>
                                <?php endif ?>
                            <?php endif ?>
                        <?php endforeach ?>
                    </ul>
                </div>
            </footer>
        </div>
    </div>
    <div class="modal fade" id="adsModal" tabindex="-1" aria-labelledby="adsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body">
                    <a href="#" data-bs-dismiss="modal" aria-label="Close" class="close_ads"><img src="<?php echo get_template_directory_uri() ?>/assets/images/close.png" alt=""></a>
                    <a href="">
                        <img src="<?php echo get_template_directory_uri() ?>/assets/images/ads-304x250.jpg" alt="">
                    </a>
                </div>
            </div>
        </div>
    </div>
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <?php wp_footer(); ?>
    <!--Custom js-->
    <script type="text/javascript" src="//cdn.datatables.net/2.2.2/js/dataTables.min.js"></script> <!--Custom js-->

</body>

</html>