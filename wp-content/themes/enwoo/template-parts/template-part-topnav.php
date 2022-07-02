<?php do_action('enwoo_construct_top_bar'); ?>
<div class="site-header container-fluid">
    <div class="<?php echo esc_attr(get_theme_mod('header_content_width', 'container')); ?>" >
        <div class="heading-row row" >
            <?php do_action('enwoo_header'); ?>
        </div>
    </div>
</div>
<?php do_action('enwoo_before_second_menu'); ?>
    <div class="main-menu">
        <nav id="second-site-navigation" class="navbar navbar-default <?php enwoo_second_menu(); ?>">
            <div class="container">   
                <?php do_action('enwoo_header_bar'); ?>
            </div>
        </nav> 
    </div>
<?php 
do_action('enwoo_after_second_menu');