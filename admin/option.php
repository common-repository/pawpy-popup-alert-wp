<?php
if (!defined('ABSPATH')) {
    exit();
}
if (!class_exists('PAWPY_Admin')) {
    class PAWPY_Admin
    {
        /**
         * This is Constructor to this Class in initializes 
         */
        public function __construct()
        {
            add_action('wp_head', array($this, 'PAWPY_PAWPY_popup_alert_add_css'));
            add_action('wp_enqueue_scripts', array($this, 'PAWPY_PAWPY_popup_alert_my_script'));
            add_action('admin_menu', array($this, 'add_custom_menu_page'));
            add_action('wp_footer', array($this, 'PAWPY_popup_alert_display_format'));
        }
        /**
         * 
         */
        public function PAWPY_PAWPY_popup_alert_add_css()
        {
            if (get_option('PAWPY_popup_alert_display_news') == 1) {
                wp_enqueue_style('marquee_style', plugin_dir_url(__FILE__) . './../css/marquee.css');
            } elseif (get_option('PAWPY_popup_alert_display_news') == 2) {
                wp_enqueue_style('display_modal_style', plugin_dir_url(__FILE__) . './../css/display_modal.css');
            }
        }
        public function PAWPY_PAWPY_popup_alert_my_script()
        {
            wp_enqueue_style('test_style', plugin_dir_url(__FILE__) . '../css/style.css');
            //        wp_enqueue_style('test_style', plugin_dir_url(__FILE__) . 'css/display_modal.css');
        }
        /**
         * Register a custom menu page.
         */
        public function add_custom_menu_page()
        {
            add_menu_page(
                __('Popup Alert WP', 'textdomain'),
                'Popup Alert WP',
                'administrator',
                'page_custom',
                'PAWPY_popup_alert_custom_menu_page',
                plugins_url('image/icon.png', __FILE__),
                'last',
            );
            /**
             * Display a custom menu page
             */
            function PAWPY_popup_alert_custom_menu_page()
            {
                if (isset($_POST) && !empty($_POST)) {
                    update_option('PAWPY_popup_alert_display_news', sanitize_key($_POST['display_news']));
                    update_option('PAWPY_popup_alert_display_color', sanitize_text_field($_POST['display_color']));
                    update_option('PAWPY_popup_alert_post_type', sanitize_text_field($_POST['post_type']));
                }
                $post_types = get_post_types('', 'names');
?>
                <form action="#" method="post">
                    <table>
                        <tr>
                            <th><label for="display_news"><?php _e('Display format') ?></label></th>
                            <th></th>
                            <th><select name="display_news" id="display_news">
                                    <option value="1" <?php if (get_option('PAWPY_popup_alert_display_news') == 1) {
                                                            esc_html_e('selected');
                                                        } ?>>
                                        <?php _e('Display 1') ?></option>
                                    <option value="2" <?php if (get_option('PAWPY_popup_alert_display_news') == 2) {
                                                            esc_html_e('selected');
                                                        } ?>>
                                        <?php _e('Display 2') ?></option>
                                </select></th>
                        </tr>
                        <tr>
                            <th><label for="post_type"><?php _e('Select posts type :') ?></label></th>
                            <th></th>
                            <th>
                                <select name="post_type" id="post_type">
                                    <?php
                                    foreach ($post_types as $post_type) {
                                    ?>
                                        <option value="<?php esc_html_e($post_type) ?>" <?php if ($post_type == get_option('PAWPY_popup_alert_post_type')) {
                                                                                            esc_html_e('selected');
                                                                                        } ?>>
                                            <?php esc_html_e($post_type) ?></option>
                                    <?php
                                    }
                                    ?>
                                </select>
                            </th>
                        </tr>
                        <tr>
                            <th><label for="display_color"><?php _e('Display Color :') ?></label></th>
                            <th></th>
                            <th> <input type="color" name="display_color" value="<?php esc_attr_e(get_option('PAWPY_popup_alert_display_color')) ?>">
                            </th>
                        </tr>
                        <th>
                        <th></th>
                        <th><button type="submit">Save</button></th>
                        <th></th>
                        </th>
                    </table>
                </form>
            <?php
            }
        }
        /**
         * Format Display in pages in website :) .
         */
        public function PAWPY_popup_alert_display_format()
        {
            if (get_option('PAWPY_popup_alert_display_news') == 1) {
            ?>
                <div class="display_color_css" style="color : <?php esc_attr_e(get_option('PAWPY_popup_alert_display_color')) ?> !important">
                    <?php

                    $args = array(
                        'post_type' => get_option('PAWPY_popup_alert_post_type'), // the post type
                    );

                    // The query
                    $the_query = new WP_Query($args);

                    // The Loop
                    if ($the_query->have_posts()) {
                    ?>
                        <marquee onmouseover="this.stop();" onmouseout="this.start();">
                            <?php
                            while ($the_query->have_posts()) {
                                $the_query->the_post();
                            ?>
                                <span> <span style="color:<?php echo get_option('PAWPY_popup_alert_display_color') ?>;">&nbsp | &nbsp &nbsp </sapn>
                                        <a href="<?php echo esc_url(get_permalink()) ?>" style="color:<?php echo get_option('PAWPY_popup_alert_display_color'); ?>">
                                            <?php echo get_the_title(); ?>
                                        </a>
                                    </span> <span style="color:black;"> </sapn>
                                    <?php
                                }
                                    ?>
                        </marquee>
                    <?php
                    } else {
                        // no posts found
                    }
                    wp_reset_postdata(); // reset global $post;
                    ?>
                </div>
                <?php
            } elseif (get_option('PAWPY_popup_alert_display_news') == 2) {
                $args = array(
                    'post_type' => get_option('PAWPY_popup_alert_post_type'), // the post type
                );
                $the_query = new WP_Query($args);

                if ($the_query->have_posts()) {
                ?>
                    <div id="myModal" class="modal" style="display: block">
                        <div class="modal-content vs-card-content type-5">
                            <span class="close">&times;</span>
                            <?php
                            while ($the_query->have_posts()) {

                                $the_query->the_post();
                                $title = get_the_title();
                            ?>
                                <a href="<?php esc_url(get_permalink()); ?>" class="vs-card a-vs">
                                    <div class="vs-card__img">
                                        <img src="<?php esc_attr_e(get_the_post_thumbnail_url($the_query->ID, 'thumbnail')); ?>" class="a-img-vs">
                                        <div class="vs-card__interactions"><button class="a-button-vs vs-button vs-button--danger">
                                                <div class="vs-button__content">
                                                    <img class="a-img-vs" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABgAAAAYCAYAAADgdz34AAAAAXNSR0IArs4c6QAAAOtJREFUSEvtlV0RwjAQhL8qAAcgAQlIQAIoAwdIoA4YHIADHMAs02Q6+evRtDyRx066393eJmmYeTUz6/NzwBY4AutEZyfg0H3fd/tyBvjCww7uwKpgmxWSBbw6cat1uU4mAyyBa8LSSQASvwAb4BFYWw3oi98AhWPXG3wVICX+DNI1GlASd+HT4JW2zwrTUkqRRTxKuBVgFVfl6sAvK0ADPANuoM7zsGI50D+MX1kkSAvkxAWLLLZ2YL10/4BBpwYt0gAXgzLlDbqX/HsSDll3imJWehNK8hLXOVDakie5svj4d+vDMho8O+ANoSU7GWmPbfwAAAAASUVORK5CYII=" />
                                                </div>
                                            </button>
                                            <button class="a-button-vs vs-button btn-chat vs-button--null vs-button--size-null vs-button--primary vs-button--shadow"></button>
                                        </div>
                                    </div>
                                    <div class="vs-card__text">
                                        <div class="vs-card__title">
                                            <h3 class="a-h3-vs"><?php esc_attr_e($title) ?></h3>
                                        </div>
                                        <p class="a-p-vs">
                                            <?php esc_attr_e(wp_trim_words(get_the_content(), 3));  ?>
                                        </p>
                                    </div>
                                </a>
                            <?php
                            }
                            ?>
                        </div>
                    </div>
                <?php
                } else {
                }
                wp_reset_postdata();
                ?>
                <script>
                    var modal = document.getElementById("myModal");
                    var span = document.getElementsByClassName("close")[0];
                    span.onclick = function() {
                        modal.style.display = "none";
                    }
                    window.onclick = function(event) {
                        if (event.target == modal) {
                            modal.style.display = "none";
                            modal.remove();
                        }
                    }
                </script>
<?php
            }
        }
    }
    new PAWPY_Admin;
}
