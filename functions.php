<?php

/**
 * iro functions and definitions.
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package iro
 */

include_once('inc/classes/IpLocation.php');


define('IRO_VERSION', wp_get_theme()->get('Version'));
define('INT_VERSION', '19.0.0');
define('BUILD_VERSION', '2');

function check_php_version($preset_version)
{
    $current_version = phpversion();
    return version_compare($current_version, $preset_version, '>=') ? true : false;
}

//Option-Framework

require get_template_directory() . '/opt/option-framework.php';

if (!function_exists('iro_opt')) {
    $GLOBALS['iro_options'] = get_option('iro_options');
    function iro_opt($option = '', $default = null)
    {
        return $GLOBALS['iro_options'][$option] ?? $default;
    }
}
if (!function_exists('iro_opt_update')) {
    function iro_opt_update($option = '', $value = null)
    {
        $options = get_option('iro_options'); // 当数据库没有指定项时，WordPress会返回false
        if ($options) {
            $options[$option] = $value;
        } else {
            $options = array($option => $value);
        }
        update_option('iro_options', $options);
    }
}
$shared_lib_basepath = iro_opt('shared_library_basepath') ? get_template_directory_uri() : (iro_opt('lib_cdn_path', 'https://fastly.jsdelivr.net/gh/mirai-mamori/Sakurairo@') . IRO_VERSION);
$core_lib_basepath = iro_opt('core_library_basepath') ? get_template_directory_uri() : (iro_opt('lib_cdn_path', 'https://fastly.jsdelivr.net/gh/mirai-mamori/Sakurairo@') . IRO_VERSION);

/**
 * composer autoload
 */
if ((check_php_version('7.4.0')) && iro_opt('composer_load')) {
    require_once 'vendor/autoload.php';
}

//Update-Checker

require 'update-checker/update-checker.php';
use YahnisElsts\PluginUpdateChecker\v5\PucFactory;

function UpdateCheck($url, $flag = 'Sakurairo')
{
    return PucFactory::buildUpdateChecker(
        $url,
        __FILE__,
        $flag
    );
}
switch (iro_opt('iro_update_source')) {
    case 'github':
        $iroThemeUpdateChecker = UpdateCheck('https://github.com/mirai-mamori/Sakurairo', 'Sakurairo');
        break;
    case 'upyun':
        $iroThemeUpdateChecker = UpdateCheck('https://update.maho.cc/jsdelivr.json');
        break;
    case 'official_building':
        $iroThemeUpdateChecker = UpdateCheck('https://update.maho.cc/' . iro_opt('iro_update_channel') . '/check.json');
}
//ini_set('display_errors', true);
//error_reporting(E_ALL);
error_reporting(E_ALL & ~E_NOTICE);

if (!function_exists('akina_setup')) {
    function akina_setup()
    {
        /*
         * Make theme available for translation.
         * Translations can be filed in the /languages/ directory.
         * If you're building a theme based on Akina, use a find and replace
         * to change 'akina' to the name of your theme in all the template files.
         */
        load_theme_textdomain('sakurairo', get_template_directory() . '/languages');

        /*
         * Enable support for Post Thumbnails on posts and pages.
         *
         * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
         */
        add_theme_support('post-thumbnails');
        set_post_thumbnail_size(150, 150, true);

        // This theme uses wp_nav_menu() in one location.
        register_nav_menus(
            array(
                'primary' => __('Nav Menus', 'sakurairo'), //导航菜单
            )
        );

        /*
         * Switch default core markup for search form, comment form, and comments
         * to output valid HTML5.
         */
        add_theme_support(
            'html5',
            array(
                'search-form',
                'comment-form',
                'comment-list',
                'gallery',
                'caption',
            )
        );

        /*
         * Enable support for Post Formats.
         * See https://developer.wordpress.org/themes/functionality/post-formats/
         */
        add_theme_support(
            'post-formats',
            array(
                'aside',
                'image',
                'status',
            )
        );

        // Set up the WordPress core custom background feature.
        add_theme_support(
            'custom-background',
            apply_filters(
                'akina_custom_background_args',
                array(
                    'default-color' => 'ffffff',
                    'default-image' => '',
                )
            )
        );
        /**
         * 废弃过时的wp_title
         * @seealso https://make.wordpress.org/core/2015/10/20/document-title-in-4-4/
         */
        add_theme_support('title-tag');

        add_filter('pre_option_link_manager_enabled', '__return_true');

        // 优化代码
        //去除头部冗余代码
        remove_action('wp_head', 'feed_links_extra', 3);
        remove_action('wp_head', 'rsd_link');
        remove_action('wp_head', 'wlwmanifest_link');
        remove_action('wp_head', 'index_rel_link');
        remove_action('wp_head', 'start_post_rel_link', 10);
        remove_action('wp_head', 'wp_generator');
        remove_action('wp_head', 'wp_generator'); //隐藏wordpress版本
        remove_filter('the_content', 'wptexturize'); //取消标点符号转义

        //remove_action('rest_api_init', 'wp_oembed_register_route');
        //remove_filter('rest_pre_serve_request', '_oembed_rest_pre_serve_request', 10, 4);
        //remove_filter('oembed_dataparse', 'wp_filter_oembed_result', 10);
        //remove_filter('oembed_response_data', 'get_oembed_response_data_rich', 10, 4);
        //remove_action('wp_head', 'wp_oembed_add_discovery_links');
        //remove_action('wp_head', 'wp_oembed_add_host_js');
        remove_action('template_redirect', 'rest_output_link_header', 11);

        function coolwp_remove_open_sans_from_wp_core()
        {
            wp_deregister_style('open-sans');
            wp_register_style('open-sans', false);
            wp_enqueue_style('open-sans', '');
        }
        add_action('init', 'coolwp_remove_open_sans_from_wp_core');

        if (!function_exists('disable_emojis')) {
            /**
             * Disable the emoji's
             * @see https://wordpress.org/plugins/disable-emojis/
             */
            function disable_emojis()
            {
                remove_action('wp_head', 'print_emoji_detection_script', 7);
                remove_action('admin_print_scripts', 'print_emoji_detection_script');
                remove_action('wp_print_styles', 'print_emoji_styles');
                remove_action('admin_print_styles', 'print_emoji_styles');
                remove_filter('the_content_feed', 'wp_staticize_emoji');
                remove_filter('comment_text_rss', 'wp_staticize_emoji');
                remove_filter('wp_mail', 'wp_staticize_emoji_for_email');
                add_filter('tiny_mce_plugins', 'disable_emojis_tinymce');
            }
            add_action('init', 'disable_emojis');
        }
        if (!function_exists('disable_emojis_tinymce')) {
            /**
             * Filter function used to remove the tinymce emoji plugin.
             *
             * @param    array  $plugins
             * @return   array             Difference betwen the two arrays
             */
            function disable_emojis_tinymce($plugins)
            {
                if (is_array($plugins)) {
                    return array_diff($plugins, array('wpemoji'));
                } else {
                    return array();
                }
            }
        }
        // 移除菜单冗余代码
        add_filter('nav_menu_css_class', 'my_css_attributes_filter', 100, 1);
        add_filter('nav_menu_item_id', 'my_css_attributes_filter', 100, 1);
        add_filter('page_css_class', 'my_css_attributes_filter', 100, 1);
        function my_css_attributes_filter($var)
        {
            return is_array($var) ? array_intersect($var, array('current-menu-item', 'current-post-ancestor', 'current-menu-ancestor', 'current-menu-parent')) : '';
        }
    }
}
;
add_action('after_setup_theme', 'akina_setup');

function register_shuoshuo_post_type() {
    $labels = array(
        'name'               => _x('Shuoshuo', 'post type general name', 'sakurairo'),
        'singular_name'      => _x('Shuoshuo', 'post type singular name', 'sakurairo'),
        'menu_name'          => _x('Shuoshuo', 'admin menu', 'sakurairo'),
        'name_admin_bar'     => _x('Shuoshuo', 'add new on admin bar', 'sakurairo'),
        'add_new'            => _x('Add New', 'shuoshuo', 'sakurairo'),
        'add_new_item'       => __('Add New Shuoshuo', 'sakurairo'),
        'new_item'           => __('New Shuoshuo', 'sakurairo'),
        'edit_item'          => __('Edit Shuoshuo', 'sakurairo'),
        'view_item'          => __('View Shuoshuo', 'sakurairo'),
        'all_items'          => __('All Shuoshuo', 'sakurairo'),
        'search_items'       => __('Search Shuoshuo', 'sakurairo'),
        'parent_item_colon'  => __('Parent Shuoshuo:', 'sakurairo'),
        'not_found'          => __('No shuoshuo found.', 'sakurairo'),
        'not_found_in_trash' => __('No shuoshuo found in Trash.', 'sakurairo')
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'show_in_rest'       => true,
        'query_var'          => true,
        'rewrite'            => array('slug' => 'shuoshuo'),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => null,
        'supports'           => array('title', 'editor', 'author', 'thumbnail', 'custom-fields', 'comments'),
        'taxonomies'         => array('category') 
    );

    register_post_type('shuoshuo', $args);
}
add_action('init', 'register_shuoshuo_post_type');

function register_emotion_meta_boxes() {
    register_meta('post', 'emotion', array(
        'show_in_rest' => true,
        'single' => true,
        'type' => 'string',
        'auth_callback' => function() {
            return current_user_can('edit_posts');
        }
    ));
    register_meta('post', 'emotion_color', array(
        'show_in_rest' => true,
        'single' => true,
        'type' => 'string',
        'auth_callback' => function() {
            return current_user_can('edit_posts');
        }
    ));
}
add_action('init', 'register_emotion_meta_boxes');

function add_emotion_meta_box() {
    add_meta_box(
        'emotion_meta_box_id',
        __('Emotion Meta Box', 'sakurairo'),
        'render_emotion_meta_box',
        'shuoshuo', // 仅在shuoshuo内容类型中显示
        'side',
        'default'
    );
}
add_action('add_meta_boxes', 'add_emotion_meta_box');

function render_emotion_meta_box($post) {
    $emotion_value = get_post_meta($post->ID, 'emotion', true);
    $emotion_color_value = get_post_meta($post->ID, 'emotion_color', true);
    wp_nonce_field('emotion_meta_box_nonce', 'emotion_meta_box_nonce_field');
    echo '<label for="emotion">' . __('Emotion', 'sakurairo') . '</label>';
    echo '<input type="text" id="emotion" name="emotion" value="' . esc_attr($emotion_value) . '" />';
    echo '<br><br>';
    echo '<label for="emotion_color">' . __('Emotion Color', 'sakurairo') . '</label>';
    echo '<input type="text" id="emotion_color" name="emotion_color" value="' . esc_attr($emotion_color_value) . '" />';
    echo '<br><br>';
    echo '<p>' . __('For the Emotion, please fill in the Unicode value of the Fontawesome icon, and for the Emotion Color, please fill in the RGBA or hexadecimal color.', 'sakurairo') . '</p>';
}

function save_emotion_meta_box($post_id) {
    if (!isset($_POST['emotion_meta_box_nonce_field']) || !wp_verify_nonce($_POST['emotion_meta_box_nonce_field'], 'emotion_meta_box_nonce')) {
        return;
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    if (isset($_POST['emotion'])) {
        update_post_meta($post_id, 'emotion', sanitize_text_field($_POST['emotion']));
    }
    if (isset($_POST['emotion_color'])) {
        update_post_meta($post_id, 'emotion_color', sanitize_text_field($_POST['emotion_color']));
    }
}
add_action('save_post', 'save_emotion_meta_box');

function include_shuoshuo_in_main_query($query) {
    if ($query->is_main_query() && !is_admin() && (is_home() || is_archive())) {
        $query->set('post_type', array('post', 'shuoshuo'));
    }
}
add_action('pre_get_posts', 'include_shuoshuo_in_main_query');

function admin_lettering()
{
    echo '<style>body{font-family: Microsoft YaHei;}</style>';
}
add_action('admin_head', 'admin_lettering');

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function akina_content_width()
{
    $GLOBALS['content_width'] = apply_filters('akina_content_width', 640);
}
add_action('after_setup_theme', 'akina_content_width', 0);
/**
 * Enqueue scripts and styles.
 */

function sakura_scripts()
{
    global $core_lib_basepath;
    global $shared_lib_basepath;

    wp_enqueue_style('saukra-css', $core_lib_basepath . '/style.css', array(), IRO_VERSION);

    if(!is_404()){
    wp_enqueue_script('app', $core_lib_basepath . '/js/app.js', array('polyfills'), IRO_VERSION, true);
    if (!is_home()) {
        //非主页的资源
        wp_enqueue_style(
            'entry-content',
            $core_lib_basepath . '/css/theme/' . (iro_opt('entry_content_style') == 'sakurairo' ? 'sakura' : 'github') . '.css',
            array(),
            IRO_VERSION
        );
        wp_enqueue_script('app-page', $core_lib_basepath . '/js/page.js', array('app', 'polyfills'), IRO_VERSION, true);
    }
    }
    wp_enqueue_script('polyfills', $core_lib_basepath . '/js/polyfill.js', array(), IRO_VERSION, true);
    if (is_singular() && comments_open() && get_option('thread_comments')) {
        wp_enqueue_script('comment-reply');
    }
    //前端脚本本地化
    if (get_locale() != 'zh_CN') {
        wp_localize_script(
            'app',
            '_sakurairoi18n',
            array(
                '复制成功！' => __("Copied!", 'sakurairo'),
                '拷贝代码' => __("Copy Code", 'sakurairo'),
                '你的封面API好像不支持跨域调用,这种情况下缓存是不会生效的哦' => __("Your cover API seems to not support Cross Origin Access. In this case, Cover Cache won't take effect.", 'sakurairo'),
                '提交中....' => __('Commiting....', 'sakurairo'),
                '提交成功' => __('Succeed', 'sakurairo'),
                '每次上传上限为10张' => __('10 files max per request', 'sakurairo'),
                "图片上传大小限制为5 MB\n\n「{0}」\n\n这张图太大啦~请重新上传噢！" => __("5 MB max per file.\n\n「{0}」\n\nThis image is too large~Please reupload!", 'sakurairo'),
                '上传中...' => __('Uploading...', 'sakurairo'),
                '图片上传成功~' => __('Uploaded successfully~', 'sakurairo'),
                "上传失败！\n文件名=> {0}\ncode=> {1}\n{2}" => __("Upload failed!\nFile Name=> {0}\ncode=> {1}\n{2}", 'sakurairo'),
                '上传失败，请重试.' => __('Upload failed, please retry.', 'sakurairo'),
                '页面加载出错了 HTTP {0}' => __("Page Load failed. HTTP {0}", 'sakurairo'),
                '很高兴你翻到这里，但是真的没有了...' => __("Glad you come, but we've got nothing left.", 'sakurairo'),
                "文章" => __("Post", 'sakurairo'),
                "标签" => __("Tag", 'sakurairo'),
                "分类" => __("Category", 'sakurairo'),
                "页面" => __("Page", 'sakurairo'),
                "评论" => __("Comment", 'sakurairo'),
                "已暂停..." => __("Paused...", 'sakurairo'),
                "正在载入视频 ..." => __("Loading Video...", 'sakurairo'),
                "将从网络加载字体，流量请注意" => __("Downloading fonts, be aware of your data usage.", 'sakurairo'),
                "您真的要设为私密吗？" => __("Are you sure you want set it private?", 'sakurairo'),
                "您之前已设过私密评论" => __("You had set private comment before", 'sakurairo')
            )
        );
    }
    if (iro_opt('smoothscroll_option')) {
        wp_enqueue_script('SmoothScroll', $shared_lib_basepath . '/js/smoothscroll.js', array(), IRO_VERSION . iro_opt('cookie_version', ''), true);
    }
}
add_action('wp_enqueue_scripts', 'sakura_scripts');

/**
 * load .php.
 */
require get_template_directory() . '/inc/decorate.php';
require get_template_directory() . '/inc/swicher.php';
require get_template_directory() . '/inc/api.php';

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * function update
 */
require get_template_directory() . '/inc/theme_plus.php';
require get_template_directory() . '/inc/categories-images.php';

if (!function_exists('akina_comment_format')) {
    function akina_comment_format($comment, $args, $depth)
    {
        $GLOBALS['comment'] = $comment;
        ?>
        <li <?php comment_class(); ?> id="comment-<?php echo esc_attr(comment_ID()); ?>">
            <div class="contents">
                <div class="comment-arrow">
                    <div class="main shadow">
                        <div class="profile">
                            <a href="<?php comment_author_url(); ?>" target="_blank" rel="nofollow"><?php echo str_replace('src=', 'src="' . iro_opt('load_in_svg') . '" onerror="imgError(this,1)" data-src=', get_avatar($comment->comment_author_email, 80, '', get_comment_author(), array('class' => array('lazyload')))); ?></a>
                        </div>
                        <div class="commentinfo">
                            <section class="commeta">
                                <div class="left">
                                    <h4 class="author">
                                        <a href="<?php comment_author_url(); ?>" target="_blank" rel="nofollow"><?php echo get_avatar($comment->comment_author_email, 24, '', get_comment_author()); ?>
                                            <span class="bb-comment isauthor" title="<?php _e('Author', 'sakurairo'); ?>"><?php _e('Blogger', 'sakurairo'); /*博主*/?></span>
                                            <?php comment_author(); ?><?php echo get_author_class($comment->comment_author_email, $comment->user_id); ?>
                                        </a>
                                    </h4>
                                </div>
                                <?php comment_reply_link(array_merge($args, array('depth' => $depth, 'max_depth' => $args['max_depth']))); ?>
                                <div class="right">
                                    <div class="info"><time datetime="<?php comment_date('Y-m-d'); ?>"><?php echo poi_time_since(strtotime($comment->comment_date), true); //comment_date(get_option('date_format'));  
                                                ?></time><?= siren_get_useragent($comment->comment_agent); ?><?php echo mobile_get_useragent_icon($comment->comment_agent); ?>&nbsp;<?php if (iro_opt('comment_location')) {
                                                        _e('Location', 'sakurairo'); /*来自*/?>: <?php echo \Sakura\API\IpLocationParse::getIpLocationByCommentId($comment->comment_ID);
                                                    } ?>
                                    <?php if (current_user_can('manage_options') and (wp_is_mobile() == false)) {
                                        $comment_ID = $comment->comment_ID;
                                        $i_private = get_comment_meta($comment_ID, '_private', true);
                                        $flag = null;
                                        $flag .= ' <i class="fa-regular fa-snowflake"></i> <a href="javascript:;" data-actionp="set_private" data-idp="' . get_comment_id() . '" id="sp" class="sm">' . __("Private", "sakurairo") . ': <span class="has_set_private">';
                                        if (!empty($i_private)) {
                                            $flag .= __("Yes", "sakurairo") . ' <i class="fa-solid fa-lock"></i>';
                                        } else {
                                            $flag .= __("No", "sakurairo") . ' <i class="fa-solid fa-lock-open"></i>';
                                        }
                                        $flag .= '</span></a>';
                                        $flag .= edit_comment_link('<i class="fa-solid fa-pen-to-square"></i> ' . __("Edit", "mashiro"), ' <span style="color:rgba(0,0,0,.35)">', '</span>');
                                        echo $flag;
                                    } ?></div>
                                </div>
                            </section>
                        </div>
                        <div class="body">
                            <?php comment_text(); ?>
                        </div>
                    </div>
                    <div class="arrow-left"></div>
                </div>
            </div>
            <hr>
        <?php
    }
}

/**
 * 获取访客VIP样式
 */
function get_author_class($comment_author_email, $user_id)
{
    global $wpdb;
    $author_count = count(
        $wpdb->get_results(
            "SELECT comment_ID as author_count FROM $wpdb->comments WHERE comment_author_email = '$comment_author_email' "
        )
    );
    # 等级梯度
    $lv_array = [0, 5, 10, 20, 40, 80, 160];
    $Lv = 0;
    foreach ($lv_array as $key => $value) {
        if ($value >= $author_count)
            break;
        $Lv = $key;
    }

    // $Lv = $author_count < 5 ? 0 : ($author_count < 10 ? 1 : ($author_count < 20 ? 2 : ($author_count < 40 ? 3 : ($author_count < 80 ? 4 : ($author_count < 160 ? 5 : 6)))));
    echo "<span class=\"showGrade{$Lv}\" title=\"Lv{$Lv}\"><img alt=\"level_img\" src=\"" . iro_opt('vision_resource_basepath', 'https://s.nmxc.ltd/sakurairo_vision/@2.7/') . "comment_level/level_{$Lv}.svg\" style=\"height: 1.5em; max-height: 1.5em; display: inline-block;\"></span>";
}

/**
 * post views
 */
function restyle_text($number)
{
    switch (iro_opt('statistics_format')) {
        case "type_2": //23,333 次访问
            return number_format($number);
        case "type_3": //23 333 次访问
            return number_format($number, 0, '.', ' ');
        case "type_4": //23k 次访问
            if ($number >= 1000) {
                return round($number / 1000, 2) . 'k';
            }
            return $number;
        default:
            return $number;
    }
}

function set_post_views()
{
    if (!is_singular())
        return;

    global $post;
    $post_id = intval($post->ID);
    if (!$post_id)
        return;
    $views = (int) get_post_meta($post_id, 'views', true);
    if (!update_post_meta($post_id, 'views', ($views + 1))) {
        add_post_meta($post_id, 'views', 1, true);
    }
}

add_action('get_header', 'set_post_views');

function get_post_views($post_id)
{
    // 检查传入的参数是否有效
    if (empty($post_id) || !is_numeric($post_id)) {
        return 'Error: Invalid post ID.';
    }
    // 检查 WP-Statistics 插件是否安装
    if ((function_exists('wp_statistics_pages')) && (iro_opt('statistics_api') == 'wp_statistics')) {
        // 使用 WP-Statistics 插件获取浏览量
        $views = wp_statistics_pages('total', 'uri', $post_id);
        return empty($views) ? 0 : $views;
    } else {
        // 使用文章自定义字段获取浏览量
        $views = get_post_meta($post_id, 'views', true);
        // 格式化浏览量
        $views = restyle_text($views);
        return empty($views) ? 0 : $views;
    }
}

function is_webp(): bool
{
    return (isset($_COOKIE['su_webp']) || (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'image/webp')));
}

/**
 * 获取友情链接列表
 * @Param: string $sorting_mode 友情链接列表排序模式，name、updated、rating、rand四种模式
 * @Param: string $link_order 友情链接列表排序方法，ASC、DESC（升序或降序）
 * @Param: mixed $id 友情链接ID
 * @Param: string $output HTML格式化输出
 */
function get_the_link_items($id = null)
{
    $sorting_mode = iro_opt('friend_link_sorting_mode');
    $link_order = iro_opt('friend_link_order');
    $bookmarks = get_bookmarks(
        array(
            'orderby' => $sorting_mode,
            'order' => $link_order,
            'category' => $id
        )
    );
    $output = '';
    if (!empty($bookmarks)) {
        $output .= '<ul class="link-items fontSmooth">';
        foreach ($bookmarks as $bookmark) {
            if (empty($bookmark->link_description)) {
                $bookmark->link_description = __('This guy is so lazy ╮(╯▽╰)╭', 'sakurairo');
            }

            if (empty($bookmark->link_image)) {
                $bookmark->link_image = 'https://s.nmxc.ltd/sakurairo_vision/@2.7/basic/friendlink.jpg';
            }

            $output .= '<li class="link-item"><a class="link-item-inner effect-apollo" href="' . $bookmark->link_url . '" title="' . $bookmark->link_description . '" target="_blank" rel="friend"><img alt="friend_avator" class="lazyload" onerror="imgError(this,1)" data-src="' . $bookmark->link_image . '" src="' . iro_opt('load_in_svg') . '"></br><span class="sitename" style="' . $bookmark->link_notes . '">' . $bookmark->link_name . '</span><div class="linkdes">' . $bookmark->link_description . '</div></a></li>';
        }
        $output .= '</ul>';
    }
    return $output;
}

function get_link_items()
{
    $linkcats = get_terms('link_category');
    $result = null;
    if (empty($linkcats))
        return get_the_link_items();  // 友链无分类，直接返回全部列表  
    $link_category_need_display = get_post_meta(get_queried_object_id(), 'link_category_need_display', false);
    foreach ($linkcats as $linkcat) {
        if (!empty($link_category_need_display) && !in_array($linkcat->name, $link_category_need_display, true)) {
            continue;
        }
        $result .= '<h3 class="link-title"><span class="link-fix">' . $linkcat->name . '</span></h3>';
        if ($linkcat->description) {
            $result .= '<div class="link-description">' . $linkcat->description . '</div>';
        }

        $result .= get_the_link_items($linkcat->term_id);
    }
    return $result;
}

/*
 * Gravatar头像使用中国服务器
 */
function gravatar_cn(string $url): string
{
    $gravatar_url = array('0.gravatar.com/avatar', '1.gravatar.com/avatar', '2.gravatar.com/avatar', 'secure.gravatar.com/avatar');
    if (iro_opt('gravatar_proxy') == 'custom_proxy_address_of_gravatar') {
        return str_replace($gravatar_url, iro_opt('custom_proxy_address_of_gravatar'), $url);
    } else {
        return str_replace($gravatar_url, iro_opt('gravatar_proxy'), $url);
    }
}
if (iro_opt('gravatar_proxy')) {
    add_filter('get_avatar_url', 'gravatar_cn', 4);
}

/*
 * 检查主题版本号，并在更新主题后执行设置选项值的更新
 */
function visual_resource_updates($specified_version, $option_name, $new_value)
{
    $theme = wp_get_theme();
    $current_version = $theme->get('Version');

    // Check if the function has already been triggered
    $function_triggered = get_transient('visual_resource_updates_triggered19');
    if ($function_triggered) {
        return; // Function has already been triggered, do nothing
    }

    if (version_compare($current_version, $specified_version, '>')) {
        $option_value = iro_opt($option_name);
        if (empty($option_value)) {
            $option_value = "https://s.nmxc.ltd/sakurairo_vision/@2.7/";
        } else if (strpos($option_value, '@') === false || substr($option_value, strpos($option_value, '@') + 1) !== $new_value) {
            $option_value = preg_replace('/@.*/', '@' . $new_value, $option_value);
        }
        iro_opt_update($option_name, $option_value);

        // Set transient to indicate that the function has been triggered
        set_transient('visual_resource_updates_triggered19', true);
    }
}

visual_resource_updates('2.5.6', 'vision_resource_basepath', '2.7/');

function gfonts_updates($specified_version, $option_name)
{
    $theme = wp_get_theme();
    $current_version = $theme->get('Version');

    // Check if the function has already been triggered
    $function_triggered = get_transient('gfonts_updates_triggered19');
    if ($function_triggered) {
        return; // Function has already been triggered, do nothing
    }

    if (version_compare($current_version, $specified_version, '>')) {
        $option_value = iro_opt($option_name);
        if (empty($option_value) || $option_value !== 'fonts.googleapis.com') {
            $option_value = 'fonts.googleapis.com';
            iro_opt_update($option_name, $option_value);
        }

        // Set transient to indicate that the function has been triggered
        set_transient('gfonts_updates_triggered19', true);
    }
}

gfonts_updates('2.5.6', 'gfonts_api');

function gravater_updates($specified_version, $option_name)
{
    $theme = wp_get_theme();
    $current_version = $theme->get('Version');

    // Check if the function has already been triggered
    $function_triggered = get_transient('gravater_updates_triggered19');
    if ($function_triggered) {
        return; // Function has already been triggered, do nothing
    }

    if (version_compare($current_version, $specified_version, '>')) {
        $option_value = iro_opt($option_name);
        if (empty($option_value) || $option_value !== 'weavatar.com/avatar') {
            $option_value = 'weavatar.com/avatar';
            iro_opt_update($option_name, $option_value);
        }

        // Set transient to indicate that the function has been triggered
        set_transient('gravater_updates_triggered19', true);
    }
}

gravater_updates('2.5.6', 'gravatar_proxy');

/*
 * 阻止站内文章互相Pingback
 */
function theme_noself_ping(&$links)
{
    $home = get_option('home');
    foreach ($links as $l => $link) {
        if (0 === strpos($link, $home)) {
            unset($links[$l]);
        }
    }
}
add_action('pre_ping', 'theme_noself_ping');

/*
 * 订制body类
 */
function akina_body_classes($classes)
{
    // Adds a class of group-blog to blogs with more than 1 published author.
    if (is_multi_author()) {
        $classes[] = 'group-blog';
    }
    // Adds a class of hfeed to non-singular pages.
    if (!is_singular()) {
        $classes[] = 'hfeed';
    }
    // 定制中文字体class
    $classes[] = 'chinese-font';
    /*if(!wp_is_mobile()) {
    $classes[] = 'serif';
    }*/
    if (isset($_COOKIE['dark' . iro_opt('cookie_version', '')])) {
        $classes[] = $_COOKIE['dark' . iro_opt('cookie_version', '')] == '1' ? 'dark' : ' ';
    } else {
        $classes[] = ' ';
    }
    return $classes;
}
add_filter('body_class', 'akina_body_classes');

/*
 * 图片CDN
 */
add_filter('upload_dir', 'wpjam_custom_upload_dir');
function wpjam_custom_upload_dir($uploads)
{
    /*     $upload_path = '';
     */$upload_url_path = iro_opt('image_cdn');

    /*     if (empty($upload_path) || 'wp-content/uploads' == $upload_path) {
            $uploads['basedir'] = WP_CONTENT_DIR . '/uploads';
        } elseif (0 !== strpos($upload_path, ABSPATH)) {
            $uploads['basedir'] = path_join(ABSPATH, $upload_path);
        } else {
            $uploads['basedir'] = $upload_path;
        } */

    $uploads['path'] = $uploads['basedir'] . $uploads['subdir'];

    if ($upload_url_path) {
        $uploads['baseurl'] = $upload_url_path;
        $uploads['url'] = $uploads['baseurl'] . $uploads['subdir'];
    }
    return $uploads;
}

/*
 * 删除自带小工具
 */
function unregister_default_widgets()
{
    unregister_widget('WP_Widget_Pages');
    unregister_widget('WP_Widget_Calendar');
    unregister_widget('WP_Widget_Archives');
    unregister_widget('WP_Widget_Links');
    unregister_widget('WP_Widget_Meta');
    unregister_widget('WP_Widget_Search');
    //unregister_widget('WP_Widget_Text');
    unregister_widget('WP_Widget_Categories');
    unregister_widget('WP_Widget_Recent_Posts');
    //unregister_widget('WP_Widget_Recent_Comments');
    //unregister_widget('WP_Widget_RSS');
    //unregister_widget('WP_Widget_Tag_Cloud');
    unregister_widget('WP_Nav_Menu_Widget');
}
add_action("widgets_init", "unregister_default_widgets", 11);

/**
 * Jetpack setup function.
 *
 * See: https://jetpack.com/support/infinite-scroll/
 * See: https://jetpack.com/support/responsive-videos/
 */
function akina_jetpack_setup()
{
    // Add theme support for Infinite Scroll.
    add_theme_support(
        'infinite-scroll',
        array(
            'container' => 'main',
            'render' => 'akina_infinite_scroll_render',
            'footer' => 'page',
        )
    );

    // Add theme support for Responsive Videos.
    add_theme_support('jetpack-responsive-videos');
}
add_action('after_setup_theme', 'akina_jetpack_setup');

/**
 * Custom render function for Infinite Scroll.
 */
function akina_infinite_scroll_render()
{
    while (have_posts()) {
        the_post();
        get_template_part('tpl/content', is_search() ? 'search' : get_post_format());
    }
}

/*
 * 编辑器增强
 */
function enable_more_buttons($buttons)
{
    $buttons[] = 'hr';
    $buttons[] = 'del';
    $buttons[] = 'sub';
    $buttons[] = 'sup';
    $buttons[] = 'fontselect';
    $buttons[] = 'fontsizeselect';
    $buttons[] = 'cleanup';
    $buttons[] = 'styleselect';
    $buttons[] = 'wp_page';
    $buttons[] = 'anchor';
    $buttons[] = 'backcolor';
    return $buttons;
}
add_filter("mce_buttons_3", "enable_more_buttons");
// 下载按钮
function download($atts, $content = null)
{
    return '<a class="download" href="' . $content . '" rel="external"
target="_blank" title="' . __("Download Link", "sakurairo") . '">
<span><i class="fa-solid fa-download"></i>Download</span></a>';
}
add_shortcode("download", "download");

add_action('after_wp_tiny_mce', 'bolo_after_wp_tiny_mce');
function bolo_after_wp_tiny_mce($mce_settings)
{
    ?>
                                    <script type="text/javascript">
                                        QTags.addButton('download', '下载按钮', "[download]下载地址[/download]");

                                        function bolo_QTnextpage_arg1() {}
                                    </script>
    <?php }

/*
 * 后台登录页
 * @M.J
 */
//Login Page style
$custom_login_switch = iro_opt('custom_login_switch');

if ($custom_login_switch) {
    function custom_login()
    {
        require get_template_directory() . '/inc/login_addcss.php';
        //echo '<link rel="stylesheet" type="text/css" href="' . get_bloginfo('template_directory') . '/inc/login.css" />'."\n";
        echo '<link rel="stylesheet" type="text/css" href="' . get_template_directory_uri() . '/inc/login.css?' . IRO_VERSION . '" />' . "\n";
        //echo '<script type="text/javascript" src="'.get_bloginfo('template_directory').'/js/jquery.min.js"></script>'."\n";
    }

    add_action('login_head', 'custom_login');

    //Login Page Title
    function custom_headertitle($title)
    {
        return get_bloginfo('name');
    }
    add_filter('login_headertext', 'custom_headertitle');

    //Login Page Link
    function custom_loginlogo_url($url)
    {
        return esc_url(home_url('/'));
    }
    add_filter('login_headerurl', 'custom_loginlogo_url');

    //Login Page Footer
    function custom_html()
    {
        $loginbg = iro_opt('login_background') ?: iro_opt('vision_resource_basepath', 'https://s.nmxc.ltd/sakurairo_vision/@2.7/') . 'series/login_background.webp';
        ?>
                                                                    <script type="text/javascript">
                                                                        document.body.insertAdjacentHTML("afterbegin", "<div class=\"loading\"><img src=\"<?= iro_opt('vision_resource_basepath', 'https://s.nmxc.ltd/sakurairo_vision/@2.7/')
                                                                            ?>basic/login_loading.gif\" width=\"58\" height=\"10\"></div>");
                                                                        document.head.insertAdjacentHTML("afterbegin", "<style>.show{opacity:1;}.hide{opacity:0;transition: opacity 400ms;}</style>");
                                                                        const loading = document.querySelector(".loading"),
                                                                         src = "<?= $loginbg ?>",
                                                                            afterLoaded = () => {
                                                                                document.body.style.backgroundImage = `url(${src})`
                                                                                loading.classList.add("hide");
                                                                                loading.classList.remove("show");
                                                                                setTimeout(function() {
                                                                                    loading.remove()
                                                                                }, 400);
                                                                            },
                                                                            img = document.createElement('img')
                                                                        img.src = src
                                                                        img.addEventListener('load',afterLoaded,{once:true})
                                                                        <?php //3秒钟内加载不到图片也移除加载中提示
                                                                                ?>
                                                                        setTimeout(afterLoaded, 3000)
                                                                        document.addEventListener("DOMContentLoaded", ()=>{
                                                                        document.querySelector("h1 a").style.backgroundImage = "url('<?= iro_opt('login_logo_img') ?>')";
                                                                        forgetmenot = document.querySelector(".forgetmenot");
                                                                        if (forgetmenot){
                                                                            forgetmenot.outerHTML = '<p class="forgetmenot"><?= __("Remember me", "sakurairo") ?><input name="rememberme" id="rememberme" value="forever" type="checkbox"><label for="rememberme" style="float: right;margin-top: 5px;transform: scale(2);margin-right: -10px;"></label></p>';
                                                                        }
                                                                        const captchaimg = document.getElementById("captchaimg");
                                                                        captchaimg && captchaimg.addEventListener("click",(e)=>{
                                                                            fetch("<?= rest_url('sakura/v1/captcha/create') ?>")
                                                                            .then(resp=>resp.json())
                                                                            .then(json=>{
                                                                                e.target.src = json["data"];
                                                                                document.querySelector("input[name=\'timestamp\']").value = json["time"];
                                                                                document.querySelector("input[name=\'id\']").value = json["id"];
                                                                            });
                                                                        })
                                                                    }, false);
                                                                    </script>
                                                                <?php
    }

    add_action('login_footer', 'custom_html');
}

//Login message
//* Add custom message to WordPress login page
function smallenvelop_login_message($message)
{
    return empty($message) ? '<p class="message"><strong>You may try 3 times for every 5 minutes!</strong></p>' : $message;
}
//add_filter( 'login_message', 'smallenvelop_login_message' );

//Fix password reset bug </>
function resetpassword_message_fix($message)
{
    return str_replace(['>', '<'], '', $message);
    // $message = str_replace('<', '', $message);
    // $message = str_replace('>', '', $message);
    // return $message;
}
add_filter('retrieve_password_message', 'resetpassword_message_fix');

//Fix register email bug </>
function new_user_message_fix($message)
{
    $show_register_ip = '注册IP | Registration IP: ' . get_the_user_ip() . ' (' . \Sakura\API\IpLocationParse::getIpLocationByIp(get_the_user_ip()) . ")\r\n\r\n如非本人操作请忽略此邮件 | Please ignore this email if this was not your operation.\r\n\r\n";
    $message = str_replace('To set your password, visit the following address:', $show_register_ip . '在此设置密码 | To set your password, visit the following address:', $message);
    $message = str_replace('<', '', $message);
    $message = str_replace('>', "\r\n\r\n设置密码后在此登录 | Login here after setting password: ", $message);
    return $message;
}
add_filter('wp_new_user_notification_email', 'new_user_message_fix');

/*
 * 评论邮件回复
 */
function comment_mail_notify($comment_id)
{
    $mail_user_name = iro_opt('mail_user_name') ? iro_opt('mail_user_name') : 'poi';
    $comment = get_comment($comment_id);
    $parent_id = $comment->comment_parent ?: '';
    $spam_confirmed = $comment->comment_approved;
    $mail_notify = iro_opt('mail_notify') ? get_comment_meta($parent_id, 'mail_notify', false) : false;
    $admin_notify = iro_opt('admin_notify') ? '1' : ((isset(get_comment($parent_id)->comment_author_email) && get_comment($parent_id)->comment_author_email) != get_bloginfo('admin_email') ? '1' : '0');
    if (($parent_id != '') && ($spam_confirmed != 'spam') && ($admin_notify != '0') && (!$mail_notify)) {
        $wp_email = $mail_user_name . '@' . preg_replace('#^www\.#', '', strtolower($_SERVER['SERVER_NAME']));
        $to = trim(get_comment($parent_id)->comment_author_email);
        $subject = '你在 [' . get_option("blogname") . '] 的留言有了回应';
        $message = '
      <div style="background: white;
      width: 95%;
      max-width: 800px;
      margin: auto auto;
      border-radius: 5px;
      border: ' . iro_opt('theme_skin') . ' 1px solid;
      overflow: hidden;
      -webkit-box-shadow: 0px 0px 20px 0px rgba(0, 0, 0, 0.12);
      box-shadow: 0px 0px 20px 0px rgba(0, 0, 0, 0.18);">
        <header style="overflow: hidden;">
            <img style="width:100%;z-index: 666;" src="' . iro_opt('mail_img') . '">
        </header>
        <div style="padding: 5px 20px;">
        <p style="position: relative;
        color: white;
        float: left;
        z-index: 999;
        background: ' . iro_opt('theme_skin') . ';
        padding: 5px 30px;
        margin: -25px auto 0 ;
        box-shadow: 5px 5px 5px rgba(0, 0, 0, 0.30)">Dear&nbsp;' . trim(get_comment($parent_id)->comment_author) . '</p>
        <br>
        <h3>您有一条来自<a style="text-decoration: none;color: ' . iro_opt('theme_skin') . ' " target="_blank" href="' . home_url() . '/">' . get_option("blogname") . '</a>的回复</h3>
        <br>
        <p style="font-size: 14px;">您在文章《' . get_the_title($comment->comment_post_ID) . '》上发表的评论：</p>
        <div style="border-bottom:#ddd 1px solid;border-left:#ddd 1px solid;padding-bottom:20px;background-color:#eee;margin:15px 0px;padding-left:20px;padding-right:20px;border-top:#ddd 1px solid;border-right:#ddd 1px solid;padding-top:20px">'
            . trim(get_comment($parent_id)->comment_content) . '</div>
        <p style="font-size: 14px;">' . trim($comment->comment_author) . ' 给您的回复如下：</p>
        <div style="border-bottom:#ddd 1px solid;border-left:#ddd 1px solid;padding-bottom:20px;background-color:#eee;margin:15px 0px;padding-left:20px;padding-right:20px;border-top:#ddd 1px solid;border-right:#ddd 1px solid;padding-top:20px">'
            . trim($comment->comment_content) . '</div>

      <div style="text-align: center;">
          <img src="' . iro_opt('vision_resource_basepath', 'https://s.nmxc.ltd/sakurairo_vision/@2.7/') . 'basic/comment-mail.png" alt="hr" style="width:100%;
                                                                                                  margin:5px auto 5px auto;
                                                                                                  display: block;">
          <a style="text-transform: uppercase;
                      text-decoration: none;
                      font-size: 14px;
                      border: 2px solid #6c7575;
                      color: #2f3333;
                      padding: 10px;
                      display: inline-block;
                      margin: 10px auto 0; " target="_blank" href="' . htmlspecialchars(get_comment_link($parent_id)) . '">点击查看回复的完整內容</a>
      </div>
        <p style="font-size: 12px;text-align: center;color: #999;">本邮件为系统自动发出，请勿直接回复<br>
        &copy; ' . date('Y') . ' ' . get_option("blogname") . '</p>
      </div>
    </div>
';
        $message = convert_smilies($message);
        $message = str_replace('{{', '<img src="' . iro_opt('vision_resource_basepath', 'https://s.nmxc.ltd/sakurairo_vision/@2.7/') . '/smilies/bilipng/emoji_', $message);
        $message = str_replace('}}', '.png" alt="emoji" style="height: 2em; max-height: 2em;">', $message);

        $message = str_replace('{UPLOAD}', 'https://i.loli.net/', $message);
        $message = str_replace('[/img][img]', '[/img^img]', $message);

        $message = str_replace('[img]', '<img src="', $message);
        $message = str_replace('[/img]', '" style="width:80%;display: block;margin-left: auto;margin-right: auto;">', $message);

        $message = str_replace('[/img^img]', '" style="width:80%;display: block;margin-left: auto;margin-right: auto;"><img src="', $message);
        $from = 'From: "' . get_option('blogname') . "\" <$wp_email>";
        $headers = "$from\nContent-Type: text/html; charset=" . get_option('blog_charset') . "\n";
        wp_mail($to, $subject, $message, $headers);
    }
}
add_action('comment_post', 'comment_mail_notify');

/*
 * 链接新窗口打开
 */
function rt_add_link_target($content)
{
    $content = str_replace('<a', '<a rel="nofollow"', $content);
    // use the <a> tag to split into segments
    $bits = explode('<a ', $content);
    // loop though the segments
    foreach ($bits as $key => $bit) {
        // fix the target="_blank" bug after the link
        if (strpos($bit, 'href') === false) {
            continue;
        }

        // fix the target="_blank" bug in the codeblock
        if (strpos(preg_replace('/code([\s\S]*?)\/code[\s]*/m', 'temp', $content), $bit) === false) {
            continue;
        }

        // find the end of each link
        $pos = strpos($bit, '>');
        // check if there is an end (only fails with malformed markup)
        if ($pos !== false) {
            // get a string with just the link's attibutes
            $part = substr($bit, 0, $pos);
            // for comparison, get the current site/network url
            $siteurl = network_site_url();
            // if the site url is in the attributes, assume it's in the href and skip, also if a target is present
            if (strpos($part, $siteurl) === false && strpos($part, 'target=') === false) {
                // add the target attribute
                $bits[$key] = 'target="_blank" ' . $bits[$key];
            }
        }
    }
    // re-assemble the content, and return it
    return implode('<a ', $bits);
}
add_filter('comment_text', 'rt_add_link_target');

// 评论通过BBCode插入图片
function comment_picture_support($content)
{
    $content = str_replace('http://', 'https://', $content); // 干掉任何可能的 http
    $content = str_replace('{UPLOAD}', 'https://i.loli.net/', $content);
    $content = str_replace('[/img][img]', '[/img^img]', $content);
    $content = str_replace('[img]', '<br><img src="' . iro_opt('load_in_svg') . '" data-src="', $content);
    $content = str_replace('[/img]', '" class="lazyload comment_inline_img" onerror="imgError(this)"><br>', $content);
    $content = str_replace('[/img^img]', '" class="lazyload comment_inline_img" onerror="imgError(this)"><img src="' . iro_opt('load_in_svg') . '" data-src="', $content);
    return $content;
}
add_filter('comment_text', 'comment_picture_support');

/*
 * 修改评论表情调用路径
 */

// 简单遍历系统表情库，今后应考虑标识表情包名——使用增加的扩展名，同时保留原有拓展名
// 还有一个思路是根据表情调用路径来判定<-- 此法最好！
// 贴吧

function make_onclick_grin($name,$type,$before='',$after=''){
    $extra_params = "";
    if($before || $after){
        $extra_params = ",'$before','$after'";
    }
    return "onclick=\"grin('$name','$type'$extra_params)\"";
}
/**
 * 通过文件夹获取自定义表情列表，使用Transients来存储获得的列表，除非手动清除，数据永不过期。
 * 数据格式如下：
 * Array
 * (
 *     [0] => Array
 *         (
 *             [path] => C:\xampp\htdocs\wordpress/wp-content/uploads/sakurairo_vision/@2.4/smilies\bilipng\emoji_2233_chijing.png
 *             [little_path] => /sakurairo_vision/@2.4/smilies\bilipng\emoji_2233_chijing.png
 *             [file_url] => http://192.168.233.174/wordpress/wp-content/uploads/sakurairo_vision/@2.4/smilies\bilipng\emoji_2233_chijing.png
 *             [name] => emoji_2233_chijing.png
 *             [base_name] => emoji_2233_chijing
 *             [extension] => png
 *         )
 *     ...
 * ）    
 *
 * @return array
 */
function get_custom_smilies_list()
{

    $custom_smilies_list = get_transient("custom_smilies_list");

    if ($custom_smilies_list !== false) {
        return $custom_smilies_list;
    }

    $custom_smilies_list = array();
    $custom_smilies_dir = iro_opt('smilies_dir');

    if (!$custom_smilies_dir) {
        return $custom_smilies_list;
    }

    $custom_smilies_extension = ['jpg', 'jpeg', 'png', 'gif', 'svg', 'avif', 'webp'];
    $custom_smilies_path = wp_get_upload_dir()['basedir'] . $custom_smilies_dir;

    if (!is_dir($custom_smilies_path)) {
        return $custom_smilies_list;
    }

    $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($custom_smilies_path), RecursiveIteratorIterator::LEAVES_ONLY);
    foreach ($files as $file) {
        if ($file->isFile()) {
            $file_name = $file->getFilename();
            $file_base_name = pathinfo($file_name, PATHINFO_FILENAME);
            $file_extension = pathinfo($file_name, PATHINFO_EXTENSION);
            $file_path = $file->getPathname();
            $file_little_path = str_replace(wp_get_upload_dir()['basedir'], '', $file_path);
            $file_url = wp_get_upload_dir()['baseurl'] . $file_little_path;
            if (in_array($file_extension, $custom_smilies_extension)) {
                $custom_smilies_list[] = array(
                    'path' => $file_path,
                    'little_path' => $file_little_path,
                    'file_url' => $file_url,
                    'name' => $file_name,
                    'base_name' => $file_base_name,
                    'extension' => $file_extension
                );
            }
        }
    }
    set_transient("custom_smilies_list", $custom_smilies_list);

    return $custom_smilies_list;
}

/**
 * 通过 GET 方法更新自定义表情包列表
 */
function update_custom_smilies_list()
{

    if (!is_admin() || !current_user_can('manage_options')) {
        return;
    }

    if (!isset($_GET['update_custom_smilies'])) {
        return;
    }

    $transient_name = sanitize_key($_GET['update_custom_smilies']);

    if ($transient_name === 'true') {
        delete_transient("custom_smilies_list");
        $custom_smilies_list = get_custom_smilies_list();
        $much = count($custom_smilies_list);
        echo '自定义表情列表更新完成！总共有' . $much . '个表情。';
    }
}
update_custom_smilies_list();


$custom_smiliestrans = array();
/**
 * 输出表情列表
 *
 */
function push_custom_smilies()
{

    global $custom_smiliestrans;
    $custom_smilies_panel = '';
    $custom_smilies_list = get_custom_smilies_list();

    if (!$custom_smilies_list) {
        $custom_smilies_panel = '<div style="font-size: 20px;text-align: center;width: 300px;height: 100px;line-height: 100px;">File does not exist!</div>';
        return $custom_smilies_panel;
    }

    $custom_smilies_cdn = iro_opt('smilies_proxy');
    foreach ($custom_smilies_list as $smiley) {

        if ($custom_smilies_cdn) {
            $smiley_url = $custom_smilies_cdn . $smiley['little_path'];
        } else {
            $smiley_url = $smiley['file_url'];
        }
        $custom_smilies_panel = $custom_smilies_panel . '<span title="' . $smiley['base_name'].'" ' . make_onclick_grin($smiley['base_name'],'Math').'><img alt="custom_smilies" loading="lazy" style="height: 60px;" src="' . $smiley_url . '" /></span>';
        $custom_smiliestrans['{{' . $smiley['base_name'] . '}}'] = '<span title="' . $smiley['base_name'] . '" ><img alt="custom_smilies" loading="lazy" style="height: 60px;" src="' . $smiley_url . '" /></span>';
    }

    return $custom_smilies_panel;
}

/**
 * 替换评论、文章中的表情符号
 *
 */
function custom_smilies_filter($content)
{
    push_custom_smilies();
    global $custom_smiliestrans;
    $content = str_replace(array_keys($custom_smiliestrans), $custom_smiliestrans, $content);
    return $content;
}
add_filter('the_content', 'custom_smilies_filter');
add_filter('comment_text', 'custom_smilies_filter');


$wpsmiliestrans = array();
function push_tieba_smilies()
{
    global $wpsmiliestrans;
    // don't bother setting up smilies if they are disabled
    if (!get_option('use_smilies'))
        return;
    $tiebaname = array('good', 'han', 'spray', 'Grievance', 'shui', 'reluctantly', 'anger', 'tongue', 'se', 'haha', 'rmb', 'doubt', 'tear', 'surprised2', 'Happy', 'ku', 'surprised', 'theblackline', 'smilingeyes', 'spit', 'huaji', 'bbd', 'hu', 'shame', 'naive', 'rbq', 'britan', 'aa', 'niconiconi', 'niconiconi_t', 'niconiconit', 'awesome');
    $return_smiles = '';
    $type = is_webp() ? 'webp' : 'png';
    $tiebaimgdir = 'tieba' . $type . '/';
    $smiliesgs = '.' . $type;
    foreach ($tiebaname as $tieba_Name) {
        $grin = make_onclick_grin($tieba_Name,'tieba');
        // 选择面版
        $return_smiles = $return_smiles . '<span title="' . $tieba_Name . '" '.$grin.'><img alt="tieba_smilie" loading="lazy" src="' . iro_opt('vision_resource_basepath', 'https://s.nmxc.ltd/sakurairo_vision/@2.7/') . 'smilies/' . $tiebaimgdir . 'icon_' . $tieba_Name . $smiliesgs . '" /></span>';
        // 正文转换
        $wpsmiliestrans['::' . $tieba_Name . '::'] = '<span title="' . $tieba_Name . '" '.$grin.'><img alt="tieba_smilie" loading="lazy" src="' . iro_opt('vision_resource_basepath', 'https://s.nmxc.ltd/sakurairo_vision/@2.7/') . 'smilies/' . $tiebaimgdir . 'icon_' . $tieba_Name . $smiliesgs . '" /></span>';
    }
    return $return_smiles;
}
push_tieba_smilies();

function tieba_smile_filter($content)
{
    global $wpsmiliestrans;
    $content = str_replace(array_keys($wpsmiliestrans), $wpsmiliestrans, $content);
    return $content;
}
add_filter('the_content', 'tieba_smile_filter'); //替换文章关键词
add_filter('comment_text', 'tieba_smile_filter'); //替换评论关键词

function push_emoji_panel()
{
    $emojis = ['(⌒▽⌒)', '（￣▽￣）', '(=・ω・=)', '(｀・ω・´)', '(〜￣△￣)〜', '(･∀･)', '(°∀°)ﾉ', '(￣3￣)', '╮(￣▽￣)╭', '(´_ゝ｀)', '←_←', '→_→', '(&lt;_&lt;)', '(&gt;_&gt;)', '(;¬_¬)', '("▔□▔)/', '(ﾟДﾟ≡ﾟдﾟ)!?', 'Σ(ﾟдﾟ;)', 'Σ(￣□￣||)', '(’；ω；‘)', '（/TДT)/', '(^・ω・^ )', '(｡･ω･｡)', '(●￣(ｴ)￣●)', 'ε=ε=(ノ≧∇≦)ノ', '(’･_･‘)', '(-_-#)', '（￣へ￣）', '(￣ε(#￣)Σ', 'ヽ(‘Д’)ﾉ', '（#-_-)┯━┯', '(╯°口°)╯(┴—┴', '←◡←', '( ♥д♥)', '_(:3」∠)_', 'Σ&gt;―(〃°ω°〃)♡→', '⁄(⁄ ⁄•⁄ω⁄•⁄ ⁄)⁄', '(╬ﾟдﾟ)▄︻┻┳═一', '･*･:≡(　ε:)', '(笑)', '(汗)', '(泣)', '(苦笑)'];
    return join('', array_map(function ($emoji) {
        return '<a class="emoji-item">' . $emoji . '</a>';
    }, $emojis));
}

// bilibili smiles
$bilismiliestrans = array();
function push_bili_smilies()
{
    global $bilismiliestrans;
    $name = array('baiyan', 'bishi', 'bizui', 'chan', 'dai', 'daku', 'dalao', 'dalian', 'dianzan', 'doge', 'facai', 'fanu', 'ganga', 'guilian', 'guzhang', 'haixiu', 'heirenwenhao', 'huaixiao', 'jingxia', 'keai', 'koubizi', 'kun', 'lengmo', 'liubixue', 'liuhan', 'liulei', 'miantian', 'mudengkoudai', 'nanguo', 'outu', 'qinqin', 'se', 'shengbing', 'shengqi', 'shuizhao', 'sikao', 'tiaokan', 'tiaopi', 'touxiao', 'tuxue', 'weiqu', 'weixiao', 'wunai', 'xiaoku', 'xieyanxiao', 'yiwen', 'yun', 'zaijian', 'zhoumei', 'zhuakuang');
    $return_smiles = '';
    $type = is_webp() ? 'webp' : 'png';
    $biliimgdir = 'bili' . $type . '/';
    $smiliesgs = '.' . $type;
    foreach ($name as $smilies_Name) {
        $grin = make_onclick_grin($smilies_Name,'Math');
        // 选择面版
        $return_smiles = $return_smiles . '<span title="' . $smilies_Name . '" '.$grin.'><img alt="bili_smilies" loading="lazy" src="' . iro_opt('vision_resource_basepath', 'https://s.nmxc.ltd/sakurairo_vision/@2.7/') . 'smilies/' . $biliimgdir . 'emoji_' . $smilies_Name . $smiliesgs . '" /></span>';
        // 正文转换
        $bilismiliestrans['{{' . $smilies_Name . '}}'] = '<span title="' . $smilies_Name . '" '.$grin.'><img alt="bili_smilies" loading="lazy" src="' . iro_opt('vision_resource_basepath', 'https://s.nmxc.ltd/sakurairo_vision/@2.7/') . 'smilies/' . $biliimgdir . 'emoji_' . $smilies_Name . $smiliesgs . '" /></span>';
    }
    return $return_smiles;
}
push_bili_smilies();

function bili_smile_filter($content)
{
    global $bilismiliestrans;
    $content = str_replace(array_keys($bilismiliestrans), $bilismiliestrans, $content);
    return $content;
}
add_filter('the_content', 'bili_smile_filter'); //替换文章关键词
add_filter('comment_text', 'bili_smile_filter'); //替换评论关键词

function featuredtoRSS($content)
{
    global $post;
    if (has_post_thumbnail($post->ID)) {
        $content = '<div>' . get_the_post_thumbnail($post->ID, 'medium', array('style' => 'margin-bottom: 15px;')) . '</div>' . $content;
    }
    return $content;
}
add_filter('the_excerpt_rss', 'featuredtoRSS');
add_filter('the_content_feed', 'featuredtoRSS');

//
function bili_smile_filter_rss($content)
{
    $type = is_webp() ? 'webp' : 'png';
    $biliimgdir = 'bili' . $type . '/';
    $smiliesgs = '.' . $type;
    $content = str_replace('{{', '<img src="' . iro_opt('vision_resource_basepath', 'https://s.nmxc.ltd/sakurairo_vision/@2.7/') . 'smilies/' . $biliimgdir, $content);
    $content = str_replace('}}', $smiliesgs . '" alt="emoji" style="height: 2em; max-height: 2em;">', $content);
    $content = str_replace('[img]', '<img src="', $content);
    $content = str_replace('[/img]', '" style="display: block;margin-left: auto;margin-right: auto;">', $content);
    return $content;
}
add_filter('comment_text_rss', 'bili_smile_filter_rss'); //替换评论rss关键词

function toc_support($content)
{
    $content = str_replace('[toc]', '<div class="has-toc have-toc"></div>', $content); // TOC 支持
    $content = str_replace('[begin]', '<span class="begin">', $content); // 首字格式支持
    $content = str_replace('[/begin]', '</span>', $content); // 首字格式支持
    return $content;
}
add_filter('the_content', 'toc_support');
add_filter('the_excerpt_rss', 'toc_support');
add_filter('the_content_feed', 'toc_support');

function check_title_tags($content)
{
    if (!empty($content)) {
        $dom = new DOMDocument();
        @$dom->loadHTML($content);
        $headings = $dom->getElementsByTagName('h1');
        for ($i = 1; $i <= 6; $i++) {
            $headings = $dom->getElementsByTagName('h' . $i);
            foreach ($headings as $heading) {
                if (trim($heading->nodeValue) != '') {
                    return true;
                }
            }
        }
    }
    return false;
}

// 显示访客当前 IP
function get_the_user_ip()
{
    // if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
    //     //check ip from share internet
    //     $ip = $_SERVER['HTTP_CLIENT_IP'];
    // } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
    //     //to check ip is pass from proxy
    //     $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    // } else {
    //     $ip = $_SERVER['REMOTE_ADDR'];
    // }
    // 简略版
    // $ip = $_SERVER['HTTP_CLIENT_IP'] ?: ($_SERVER['HTTP_X_FORWARDED_FOR'] ?: $_SERVER['REMOTE_ADDR']);
    $ip = $_SERVER['HTTP_CLIENT_IP'] ?? $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'];
    return apply_filters('wpb_get_ip', $ip);
}

add_shortcode('show_ip', 'get_the_user_ip');

/*歌词*/
function hero_get_lyric()
{
    /** These are the lyrics to Hero */
    $lyrics = "";

    // Here we split it into lines
    $lyrics = explode("\n", $lyrics);

    // And then randomly choose a line
    return wptexturize($lyrics[mt_rand(0, count($lyrics) - 1)]);
}

// This just echoes the chosen line, we'll position it later
function hello_hero()
{
    $chosen = hero_get_lyric();
    echo $chosen;
}

/*私密评论*/
add_action('wp_ajax_nopriv_siren_private', 'siren_private');
add_action('wp_ajax_siren_private', 'siren_private');
function siren_private()
{
    $comment_id = $_POST["p_id"];
    $action = $_POST["p_action"];
    if ($action == 'set_private') {
        update_comment_meta($comment_id, '_private', 'true');
        $i_private = get_comment_meta($comment_id, '_private', true);
        echo empty($i_private) ? '是' : '否';
    }
    die;
}

//时间序列
function memory_archives_list()
{
    // 尝试从缓存中获取结果
    $cache_key = 'memory_archives_list_' . get_locale();
    $output = get_transient($cache_key);

    if ($output !== false) {
        echo $output;
        return;
    }
    $output = '<div id="archives"><p style="text-align:right;">[<span id="al_expand_collapse">' . __("All expand/collapse", "sakurairo") /*全部展开/收缩*/. '</span>]<!-- (注: 点击月份可以展开)--></p>';
    $posts = get_posts(
        array(
            'posts_per_page' => -1,
            'ignore_sticky_posts' => true,
            'post_type' => 'post'
        )
    );

    $posts_sorted_by_time = [];
    foreach ($posts as $post) {
        $post_id = $post->ID;
        $post_time = get_post_time('U', false, $post_id);
        $posts_sorted_by_time[$post_time] = $post;
    }
    krsort($posts_sorted_by_time); // 按时间倒序排列

    $year = 0;
    $mon = 0;
    foreach ($posts_sorted_by_time as $post) {
        setup_postdata($post);
        $year_tmp = get_post_time('Y', false, $post);
        $mon_tmp = get_post_time('m', false, $post);
        $post_id = $post->ID;
        $post_views = get_post_views($post_id);
        if ($mon != $mon_tmp && $mon > 0) {
            $output .= '</ul></li>';
        }

        if ($year != $year_tmp && $year > 0) {
            $output .= '</ul>';
        }

        if ($year != $year_tmp) {
            $year = $year_tmp;
            $output .= '<h3 class="al_year">' . $year . __(" year", "sakurairo") . /*年*/' </h3><ul class="al_mon_list">'; //输出年份
        }
        if ($mon != $mon_tmp) {
            $mon = $mon_tmp;
            $output .= '<li class="al_li"><span class="al_mon"><span style="color:' . iro_opt('theme_skin') . ';">' . get_post_time('M', false, $post) . '</span> (<span id="post-num"></span>' . __(" post(s)", "sakurairo") /*篇文章*/. ')</span><ul class="al_post_list">'; //输出月份
        }
        $output .= '<li>' . '<a href="' . get_permalink($post) . '"><span style="color:' . iro_opt('theme_skin') . ';">' /*get_post_time('d'.__(" ","sakurairo"), false, $post) 日*/. '</span>' . get_the_title($post) . ' <span>(' . $post_views . ' <span class="fa-solid fa-chess-queen" aria-hidden="true"></span> / ' . get_comments_number($post) . ' <span class="fa-regular fa-comment-dots" aria-hidden="true"></span>)</span></a></li>';
    }
    wp_reset_postdata();
    $output .= '</ul></li></ul> <!--<ul class="al_mon_list"><li><ul class="al_post_list" style="display: block;"><li>博客已经萌萌哒运行了<span id="monitorday"></span>天</li></ul></li></ul>--></div>';
    set_transient($cache_key, $output, 3600);
    echo $output;
}

// 在保存文章后清空缓存
function clear_memory_archives_list_cache($post_id)
{
    delete_transient('memory_archives_list_' . get_locale());
}
add_action('save_post', 'clear_memory_archives_list_cache');

require_once __DIR__ . '/inc/word-stat.php';
/**
 * 字数、词数统计
 */
function count_post_words($post_ID)
{
    $post = get_post($post_ID);
    if ($post->post_type !== "post") {
        return;
    }
    $content = $post->post_content;
    $content = strip_tags($content);
    $count = word_stat($content);
    update_post_meta($post_ID, 'post_words_count', $count);
    return $count;
}

add_action('save_post', 'count_post_words');

/*
 * 隐藏 Dashboard
 */
/* Remove the "Dashboard" from the admin menu for non-admin users */
function remove_dashboard()
{
    global $current_user, $menu, $submenu;
    wp_get_current_user();

    if (!in_array('administrator', $current_user->roles)) {
        reset($menu);
        $page = key($menu);
        while ((__('Dashboard') != $menu[$page][0]) && next($menu)) {
            $page = key($menu);
        }
        if (__('Dashboard') == $menu[$page][0]) {
            unset($menu[$page]);
        }
        reset($menu);
        $page = key($menu);
        while (!$current_user->has_cap($menu[$page][1]) && next($menu)) {
            $page = key($menu);
        }
        if (
            preg_match('#wp-admin/?(index.php)?$#', $_SERVER['REQUEST_URI']) &&
            ('index.php' != $menu[$page][2])
        ) {
            wp_redirect(get_option('siteurl') . '/wp-admin/profile.php');
        }
    }
}
add_action('admin_menu', 'remove_dashboard');

/**
 * Filter the except length to 20 words. 限制摘要长度
 *
 * @param int $length Excerpt length.
 * @return int (Maybe) modified excerpt length.
 */

function GBsubstr($string, $start, $length)
{
    if (strlen($string) > $length) {
        $str = null;
        $len = 0;
        $i = $start;
        while ($len < $length) {
            if (ord(substr($string, $i, 1)) > 0xc0) {
                $str .= substr($string, $i, 3);
                $i += 3;
            } elseif (ord(substr($string, $i, 1)) > 0xa0) {
                $str .= substr($string, $i, 2);
                $i += 2;
            } else {
                $str .= substr($string, $i, 1);
                $i++;
            }
            $len++;
        }
        return $str;
    } else {
        return $string;
    }
}

/**
 * chatgpt excerpt
 */
require_once __DIR__ . '/inc/chatgpt/hooks.php';
IROChatGPT\apply_chatgpt_hook();

function excerpt_length($exp)
{
    if (!function_exists('mb_substr')) {
        $exp = GBsubstr($exp, 0, 110);
    } else {
        /*
         * To use mb_substr() function, you should uncomment "extension=php_mbstring.dll" in php.ini
         */
        $exp = mb_substr($exp, 0, 110);
    }
    return $exp;
}
add_filter('the_excerpt', 'excerpt_length', 11);

/*
 * 后台路径
 */
/*
add_filter('site_url',  'wpadmin_filter', 10, 3);
function wpadmin_filter( $url, $path, $orig_scheme ) {
$old  = array( "/(wp-admin)/");
$admin_dir = WP_ADMIN_DIR;
$new  = array($admin_dir);
return preg_replace( $old, $new, $url, 1);
}
 */

function admin_ini()
{
    wp_enqueue_style('admin-styles-fix-icon', get_site_url() . '/wp-includes/css/dashicons.css');
    wp_enqueue_style('cus-styles-fit', get_template_directory_uri() . '/css/dashboard-fix.css');
}
add_action('admin_enqueue_scripts', 'admin_ini');

/*
 * 后台通知
 */

/**
 * 在提供权限的情况下，为管理员用户显示通知并更新 meta 值
 */
function theme_admin_notice_callback()
{
    // 判断当前用户是否为管理员
    if (!current_user_can('manage_options')) {
        return;
    }

    // 读取 meta 值
    $meta_value = get_user_meta(get_current_user_id(), 'theme_admin_notice', true);

    // 判断 meta 值是否存在
    if ($meta_value) {
        return; // 如果存在，退出函数，避免重复加载通知
    }

    // 显示通知
    $theme_name = 'Sakurairo';
    switch (get_locale()) {
        case 'zh_CN':
            $thankyou = '感谢你使用 ' . $theme_name . ' 主题！这里有一些需要你的许可的东西(*/ω＼*)';
            $dislike = '不，谢谢';
            $allow_send = '允许发送你的主题版本数据以便官方统计';
            break;

        case 'zh_TW':
            $thankyou = '感謝你使用 ' . $theme_name . ' 主題！以下是一些需要你許可的內容。';
            $dislike = '謝謝，不用了';
            $allow_send = '允許出於統計目的發送主題版本数据';
            break;

        case 'ja':
            $thankyou = 'ご使用いただきありがとうございます！以下は、あなたの許可が必要なコンテンツです。';
            $dislike = 'いいえ、結構です';
            $allow_send = '統計目的のためにあなたのテーマバージョンを送信することを許可する';
            break;

        default:
            $thankyou = 'Thank you for using the ' . $theme_name . ' theme! There is something that needs your approval.';
            $dislike = 'No, thanks';
            $allow_send = 'Allow sending your theme version for statistical purposes';
            break;
    }
    ?>
                                <div class="notice notice-success" id="send-ver-tip">
                                    <p><?php echo $thankyou; ?></p>
                                    <button class="button" onclick="dismiss_notice()"><?php echo $dislike; ?></button>
                                    <button class="button" onclick="update_option()"><?php echo $allow_send; ?></button>
                                </div>
                                <script>
                                    function dismiss_notice() {
                                        // 隐藏通知
                                        document.getElementById( "send-ver-tip" ).style.display = "none";
                                        // 写入 1 到 meta
                                        var data = new FormData();
                                        data.append( 'action', 'update_theme_admin_notice_meta' );
                                        data.append( 'user_id', '<?php echo get_current_user_id(); ?>' );
                                        data.append( 'meta_key', 'theme_admin_notice' );
                                        data.append( 'meta_value', '1' );
                                        fetch( '<?php echo admin_url('admin-ajax.php'); ?>', {
                                            method: 'POST',
                                            body: data
                                        } );
                                    }

                                    function update_option() {
                                        // 隐藏通知
                                        document.getElementById( "send-ver-tip" ).style.display = "none";
                                        // 发送 AJAX 请求
                                        var xhr = new XMLHttpRequest();
                                        xhr.open( "POST", "<?php echo admin_url('admin-ajax.php'); ?>", true );
                                        xhr.setRequestHeader( "Content-Type", "application/x-www-form-urlencoded" );
                                        xhr.send( "action=update_theme_option&option=send_theme_version&value=true" );

                                        // 写入 1 到 meta
                                        var data = new FormData();
                                        data.append( 'action', 'update_theme_admin_notice_meta' );
                                        data.append( 'user_id', '<?php echo get_current_user_id(); ?>' );
                                        data.append( 'meta_key', 'theme_admin_notice' );
                                        data.append( 'meta_value', '1' );
                                        fetch( '<?php echo admin_url('admin-ajax.php'); ?>', {
                                            method: 'POST',
                                            body: data
                                        } );
                                    }
                                </script>
                                <?php
}
add_action('admin_notices', 'theme_admin_notice_callback');

// AJAX 处理函数 - 更新主题选项
add_action('wp_ajax_update_theme_option', 'update_theme_option');
function update_theme_option()
{
    $option = $_POST['option'];
    $value = sanitize_text_field($_POST['value']);
    iro_opt_update($option, $value);
    wp_die();
}

// AJAX 处理函数 - 写入 theme_admin_notice 元值
add_action('wp_ajax_update_theme_admin_notice_meta', 'update_theme_admin_notice_meta');
function update_theme_admin_notice_meta()
{
    $user_id = $_POST['user_id'];
    $meta_key = $_POST['meta_key'];
    $meta_value = sanitize_text_field($_POST['meta_value']);
    update_user_meta($user_id, $meta_key, $meta_value);
    wp_die();
}

//dashboard scheme
function dash_scheme($key, $name, $col1, $col2, $col3, $col4, $base, $focus, $current, $rules = "")
{
    $hash = 'rules=' . urlencode($rules);
    if ($col1) {
        $hash .= '&color_1=' . str_replace("#", "", $col1);
    }
    if ($col2) {
        $hash .= '&color_2=' . str_replace("#", "", $col2);
    }
    if ($col3) {
        $hash .= '&color_3=' . str_replace("#", "", $col3);
    }
    if ($col4) {
        $hash .= '&color_4=' . str_replace("#", "", $col4);
    }

    wp_admin_css_color(
        $key,
        $name,
        get_template_directory_uri() . "/inc/dash-scheme.php?" . $hash,
        array($col1, $col2, $col3, $col4),
        array('base' => $base, 'focus' => $focus, 'current' => $current)
    );
}

//Sakurairo
dash_scheme(
    $key = "sakurairo",
    $name = "Sakurairo🌸",
    $col1 = iro_opt('admin_second_class_color'),
    $col2 = iro_opt('admin_first_class_color'),
    $col3 = iro_opt('admin_emphasize_color'),
    $col4 = iro_opt('admin_emphasize_color'),
    $base = "#FFF",
    $focus = "#FFF",
    $current = "#FFF",
    $rules = '#adminmenu .wp-has-current-submenu .wp-submenu a,#adminmenu .wp-has-current-submenu.opensub .wp-submenu a,#adminmenu .wp-submenu a,#adminmenu a.wp-has-current-submenu:focus+.wp-submenu a,#wpadminbar .ab-submenu .ab-item,#wpadminbar .quicklinks .menupop ul li a,#wpadminbar .quicklinks .menupop.hover ul li a,#wpadminbar.nojs .quicklinks .menupop:hover ul li a, .csf-field-button_set .csf--active, .csf-field-button_set .csf--active:hover, .folded #adminmenu .wp-has-current-submenu .wp-submenu a{color:' . iro_opt('admin_text_color') . '}body{background-image:url(' . iro_opt('admin_background') . ');background-attachment:fixed;background-size:cover;}#wpcontent{background:rgba(255,255,255,.0)}.wp-core-ui .button-primary{background:' . iro_opt('admin_button_color') . '!important;border-color:' . iro_opt('admin_button_color') . '!important;color:' . iro_opt('admin_text_color') . '!important;box-shadow:0 1px 0 ' . iro_opt('admin_button_color') . '!important;text-shadow:0 -1px 1px ' . iro_opt('admin_button_color') . ',1px 0 1px ' . iro_opt('admin_button_color') . ',0 1px 1px ' . iro_opt('admin_button_color') . ',-1px 0 1px ' . iro_opt('admin_button_color') . '!important}'
);

//Set Default Admin Color Scheme for New Users
function set_default_admin_color($user_id)
{
    $args = array(
        'ID' => $user_id,
        'admin_color' => 'sunrise',
    );
    wp_update_user($args);
}
//add_action('user_register', 'set_default_admin_color');

//Stop Users From Switching Admin Color Schemes
//if ( !current_user_can('manage_options') ) remove_action( 'admin_color_scheme_picker', 'admin_color_scheme_picker' );

// WordPress Custom style @ Admin
function custom_admin_open_sans_style()
{
    require get_template_directory() . '/inc/admin_addcss.php';
}
add_action('admin_head', 'custom_admin_open_sans_style');

// WordPress Custom Font @ Admin
function custom_admin_open_sans_font()
{
    echo '<link href="https://' . iro_opt('gfonts_api', 'fonts.googleapis.com') . '/css?family=Noto+Serif+SC&display=swap" rel="stylesheet">' . PHP_EOL;
    echo '<style>body, #wpadminbar *:not([class="ab-icon"]), .wp-core-ui, .media-menu, .media-frame *, .media-modal *{font-family: "Noto Serif SC", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif !important;}</style>' . PHP_EOL;
}
add_action('admin_head', 'custom_admin_open_sans_font');

// WordPress Custom Font @ Admin Frontend Toolbar
function custom_admin_open_sans_font_frontend_toolbar()
{
    if (current_user_can('administrator') && is_admin_bar_showing()) {
        echo '<link href="https://' . iro_opt('gfonts_api', 'fonts.googleapis.com') . '/css?family=Noto+Serif+SC&display=swap" rel="stylesheet">' . PHP_EOL;
        echo '<style>#wpadminbar *:not([class="ab-icon"]){font-family: "Noto Serif SC", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif !important;}</style>' . PHP_EOL;
    }
}
add_action('wp_head', 'custom_admin_open_sans_font_frontend_toolbar');

// WordPress Custom Font @ Admin Login
function custom_admin_open_sans_font_login_page()
{
    if (stripos($_SERVER["SCRIPT_NAME"], strrchr(wp_login_url(), '/')) !== false) {
        echo '<link href="https://' . iro_opt('gfonts_api', 'fonts.googleapis.com') . '/css?family=Noto+Serif+SC&display=swap" rel="stylesheet">' . PHP_EOL;
        echo '<style>body{font-family: "Noto Serif SC", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif !important;}</style>' . PHP_EOL;
    }
}
add_action('login_head', 'custom_admin_open_sans_font_login_page');

// 阻止垃圾注册
add_action('register_post', 'codecheese_register_post', 10, 3);

function codecheese_register_post($sanitized_user_login, $user_email, $errors)
{

    // Blocked domains
    $domains = array(
        'net.buzzcluby.com',
        'buzzcluby.com',
        'mail.ru',
        'h.captchaeu.info',
        'edge.codyting.com'
    );

    // Get visitor email domain
    $email = explode('@', $user_email);

    // Check and display error message for the registration form if exists
    if (in_array($email[1], $domains)) {
        $errors->add('invalid_email', __('<b>ERROR</b>: This email domain (<b>@' . $email[1] . '</b>) has been blocked. Please use another email.'));
    }
}
function array_html_props(array $props)
{
    $props_string = '';
    foreach ($props as $key => $value) {
        $props_string .= ' ' . $key . '="' . $value . '"';
    }
    return $props_string;
}
/**
 * 渲染一个懒加载的<img>
 * @author KotoriK
 */
function lazyload_img(string $src, string $class = '', array $otherParam = array())
{
    $noscriptParam = $otherParam;
    if ($class)
        $noscriptParam['class'] = $class;
    $noscriptParam['src'] = $src;
    $otherParam['class'] = 'lazyload' . ($class ? ' ' . $class : '');
    $otherParam['data-src'] = $src;
    $otherParam['onerror'] = 'imgError(this)';
    $otherParam['src'] = iro_opt('page_lazyload_spinner');
    $noscriptProps = '';
    $props = array_html_props($otherParam);
    $noscriptProps = array_html_props($noscriptParam);
    return "<img$props/><noscript><img$noscriptProps/></noscript>";
}

// html 标签处理器
function html_tag_parser($content)
{
    if (!is_feed()) {
        //图片懒加载标签替换
        if (iro_opt('page_lazyload') && iro_opt('page_lazyload_spinner')) {
            $img_elements = array();
            $is_matched = preg_match_all('/<img[^<]*>/i', $content, $img_elements);
            if ($is_matched) {
                array_walk($img_elements[0], function ($img) use (&$content) {
                    $class_found = 0;
                    $new_img = preg_replace('/class=[\'"]([^\'"]+)[\'"]/i', 'class="$1 lazyload"', $img, -1, $class_found);
                    if ($class_found == 0) {
                        $new_img = str_replace('<img ', '<img class="lazyload"', $new_img);
                    }
                    $new_img = preg_replace('/srcset=[\'"]([^\'"]+)[\'"]/i', 'data-srcset="$1"', $new_img);
                    $new_img = preg_replace('/src=[\'"]([^\'"]+)[\'"]/i', 'data-src="$1" src="' . iro_opt('page_lazyload_spinner') . '" onerror="imgError(this)"', $new_img);
                    $content = str_replace($img, $new_img . '<noscript>' . $img . '</noscript>', $content);
                });
            }
        }

        //Fancybox
        /* Markdown Regex Pattern for Matching URLs:
         * https://daringfireball.net/2010/07/improved_regex_for_matching_urls
         */
        $url_regex = '((?:https?:\/\/|www\d{0,3}[.]|[a-z0-9.\-]+[.][a-z]{2,4}\/)(?:[^\s()<>]+|\(([^\s()<>]+|(\([^\s()<>]+\)))*\))+(?:\(([^\s()<>]+|(\([^\s()<>]+\)))*\)|[^\s`!()\[\]{};:\'".,<>?«»“”‘’]))';

        //With Thumbnail: !{alt}(url)[th_url]
        if (preg_match_all('/\!\{.*?\)\[.*?\]/i', $content, $matches)) {
            foreach ($matches as $result) {
                $content = str_replace(
                    $result,
                    preg_replace(
                        '/!\{([^\{\}]+)*\}\(' . $url_regex . '\)\[' . $url_regex . '\]/i',
                        '<a data-fancybox="gallery"
                        data-caption="$1"
                        class="fancybox"
                        href="$2"
                        alt="$1"
                        title="$1"><img src="$7" target="_blank" rel="nofollow" class="fancybox"></a>',
                        $result
                    ),
                    $content
                );
            }
        }

        //Without Thumbnail :!{alt}(url)
        $content = preg_replace(
            '/!\{([^\{\}]+)*\}\(' . $url_regex . '\)/i',
            '<a data-fancybox="gallery"
                data-caption="$1"
                class="fancybox"
                href="$2"
                alt="$1"
                title="$1"><img src="$2" target="_blank" rel="nofollow" class="fancybox"></a>',
            $content
        );
    }
    //html tag parser for rss
    if (is_feed()) {
        //Fancybox
        $url_regex = '((?:https?:\/\/|www\d{0,3}[.]|[a-z0-9.\-]+[.][a-z]{2,4}\/)(?:[^\s()<>]+|\(([^\s()<>]+|(\([^\s()<>]+\)))*\))+(?:\(([^\s()<>]+|(\([^\s()<>]+\)))*\)|[^\s`!()\[\]{};:\'".,<>?«»“”‘’]))';
        if (preg_match_all('/\!\{.*?\)\[.*?\]/i', $content, $matches)) {
            foreach ($matches as $result) {
                $content = str_replace(
                    $result,
                    preg_replace('/!\{([^\{\}]+)*\}\(' . $url_regex . '\)\[' . $url_regex . '\]/i', '<a href="$2"><img src="$7" alt="$1" title="$1"></a>', $result),
                    $content
                );
            }
        }
        $content = preg_replace('/!\{([^\{\}]+)*\}\(' . $url_regex . '\)/i', '<a href="$2"><img src="$2" alt="$1" title="$1"></a>', $content);
    }
    return $content;
}
add_filter('the_content', 'html_tag_parser'); //替换文章关键词
//add_filter( 'comment_text', 'html_tag_parser' );//替换评论关键词

/*
 * QQ 评论
 */
// 数据库插入评论表单的qq字段
add_action('wp_insert_comment', 'sql_insert_qq_field', 10, 2);
function sql_insert_qq_field($comment_ID, $commmentdata)
{
    $qq = isset($_POST['new_field_qq']) ? $_POST['new_field_qq'] : false;
    update_comment_meta($comment_ID, 'new_field_qq', $qq); // new_field_qq 是表单name值，也是存储在数据库里的字段名字
}
// 后台评论中显示qq字段
add_filter('manage_edit-comments_columns', 'add_comments_columns');
add_action('manage_comments_custom_column', 'output_comments_qq_columns', 10, 2);
function add_comments_columns($columns)
{
    $columns['new_field_qq'] = __('QQ'); // 新增列名称
    return $columns;
}
function output_comments_qq_columns($column_name, $comment_id)
{
    switch ($column_name) {
        case "new_field_qq":
            // 这是输出值，可以拿来在前端输出，这里已经在钩子manage_comments_custom_column上输出了
            echo get_comment_meta($comment_id, 'new_field_qq', true);
            break;
    }
}
/**
 * 头像调用路径
 */
add_filter('get_avatar', 'change_avatar', 10, 3);
function change_avatar($avatar)
{
    global $comment, $sakura_privkey;
    if ($comment && get_comment_meta($comment->comment_ID, 'new_field_qq', true)) {
        $qq_number = get_comment_meta($comment->comment_ID, 'new_field_qq', true);
        if (iro_opt('qq_avatar_link') == 'off') {
            return '<img src="https://q2.qlogo.cn/headimg_dl?dst_uin=' . $qq_number . '&spec=100" class="lazyload avatar avatar-24 photo" alt="😀" width="24" height="24" onerror="imgError(this,1)">';
        }
        if (iro_opt('qq_avatar_link') == 'type_3') {
            $qqavatar = file_get_contents('http://ptlogin2.qq.com/getface?appid=1006102&imgtype=3&uin=' . $qq_number);
            preg_match('/:\"([^\"]*)\"/i', $qqavatar, $matches);
            return '<img src="' . $matches[1] . '" class="lazyload avatar avatar-24 photo" alt="😀" width="24" height="24" onerror="imgError(this,1)">';
        }
        
        // Ensure $sakura_privkey is defined and not null
        if (isset($sakura_privkey) && !is_null($sakura_privkey)) {
            // 生成一个合适长度的初始化向量
            $iv_length = openssl_cipher_iv_length('aes-128-cbc');
            $iv = openssl_random_pseudo_bytes($iv_length);
            
            // 加密数据
            $encrypted = openssl_encrypt($qq_number, 'aes-128-cbc', $sakura_privkey, 0, $iv);
            
            // 将初始化向量和加密数据一起编码
            $encrypted = urlencode(base64_encode($iv . $encrypted));
            
            return '<img src="' . rest_url("sakura/v1/qqinfo/avatar") . '?qq=' . $encrypted . '" class="lazyload avatar avatar-24 photo" alt="😀" width="24" height="24" onerror="imgError(this,1)">';
        } else {
            // Handle the case where $sakura_privkey is not set or is null
            return '<img src="default_avatar_url" class="lazyload avatar avatar-24 photo" alt="😀" width="24" height="24" onerror="imgError(this,1)">';
        }
    }
    return $avatar;
}


function get_random_url(string $url): string
{
    $array = parse_url($url);
    if (!isset($array['query'])) {
        // 无参数
        $url .= '?';
    } else {
        // 有参数
        $url .= '&';
    }
    return $url . random_int(1, 100);
}


// default feature image
function DEFAULT_FEATURE_IMAGE(string $size = 'source'): string
{
    if (iro_opt('post_cover_options') == 'type_2') {
        return get_random_url(iro_opt('post_cover'));
    }
    if (iro_opt('random_graphs_options') == 'external_api') {
        return get_random_url(iro_opt('random_graphs_link'));
    }
    $_api_url = rest_url('sakura/v1/image/feature');
    $rand = rand(1, 100);
    # 拼接符
    $splice = strpos($_api_url, 'index.php?') !== false ? '&' : '?';
    $_api_url = "{$_api_url}{$splice}size={$size}&$rand";
    return $_api_url;
}

//评论回复
function sakura_comment_notify($comment_id)
{
    if (!isset($_POST['mail-notify'])) {
        update_comment_meta($comment_id, 'mail_notify', 'false');
    }
}
add_action('comment_post', 'sakura_comment_notify');

//侧栏小工具
if (iro_opt('sakura_widget')) {
    if (function_exists('register_sidebar')) {
        register_sidebar(
            array(
                'name' => __('Sidebar'), //侧栏
                'id' => 'sakura_widget',
                'before_widget' => '<div class="widget %2$s">',
                'after_widget' => '</div>',
                'before_title' => '<div class="title"><h2>',
                'after_title' => '</h2></div>',
            )
        );
    }
}

// 评论Markdown解析
function markdown_parser($incoming_comment)
{
    global $wpdb, $comment_markdown_content;
    $re = '/```([\s\S]*?)```[\s]*|`{1,2}[^`](.*?)`{1,2}|\[.*?\]\([\s\S]*?\)/m';
    if (preg_replace($re, 'temp', $incoming_comment['comment_content']) != strip_tags(preg_replace($re, 'temp', $incoming_comment['comment_content']))) {
        siren_ajax_comment_err('评论只支持Markdown啦，见谅╮(￣▽￣)╭<br>Markdown Supported while <i class="fa-solid fa-code"></i> Forbidden');
        return ($incoming_comment);
    }
    $column_names = $wpdb->get_row("SELECT * FROM information_schema.columns where 
    table_name='$wpdb->comments' and column_name = 'comment_markdown' LIMIT 1");
    //Add column if not present.
    if (!isset($column_names)) {
        $wpdb->query("ALTER TABLE $wpdb->comments ADD comment_markdown text");
    }
    $comment_markdown_content = $incoming_comment['comment_content'];
    include 'inc/Parsedown.php';
    $Parsedown = new Parsedown();
    $incoming_comment['comment_content'] = $Parsedown->setUrlsLinked(false)->text($incoming_comment['comment_content']);
    return $incoming_comment;
}
add_filter('preprocess_comment', 'markdown_parser');
remove_filter('comment_text', 'make_clickable', 9);

//保存Markdown评论
function save_markdown_comment($comment_ID, $comment_approved)
{
    global $wpdb, $comment_markdown_content;
    $comment = get_comment($comment_ID);
    $comment_content = $comment_markdown_content;
    //store markdow content
    $wpdb->query("UPDATE $wpdb->comments SET comment_markdown='" . $comment_content . "' WHERE comment_ID='" . $comment_ID . "';");
}
add_action('comment_post', 'save_markdown_comment', 10, 2);

//打开评论HTML标签限制
function allow_more_tag_in_comment()
{
    global $allowedtags;
    $allowedtags['pre'] = array('class' => array());
    $allowedtags['code'] = array('class' => array());
    $allowedtags['h1'] = array('class' => array());
    $allowedtags['h2'] = array('class' => array());
    $allowedtags['h3'] = array('class' => array());
    $allowedtags['h4'] = array('class' => array());
    $allowedtags['h5'] = array('class' => array());
    $allowedtags['ul'] = array('class' => array());
    $allowedtags['ol'] = array('class' => array());
    $allowedtags['li'] = array('class' => array());
    $allowedtags['td'] = array('class' => array());
    $allowedtags['th'] = array('class' => array());
    $allowedtags['tr'] = array('class' => array());
    $allowedtags['table'] = array('class' => array());
    $allowedtags['thead'] = array('class' => array());
    $allowedtags['tbody'] = array('class' => array());
    $allowedtags['span'] = array('class' => array());
}
add_action('pre_comment_on_post', 'allow_more_tag_in_comment');

/**
 * 检查数据库是否支持MyISAM引擎
 */
function check_myisam_support()
{
    global $wpdb;
    $results = $wpdb->get_results("SHOW ENGINES");
    if (!$results)
        return false;
    foreach ($results as $result) {
        if ($result->Engine == "MyISAM") {
            return $result->Support == "YES";
        }
    }
    return false;
}

/*
 * 随机图
 * 暂移除, 在20个月前功能已被移除，该表应该不存在了。
 */
// function create_sakura_table()
// {
//     if (iro_opt('random_graphs_mts')) {
//         global $wpdb, $sakura_image_array, $sakura_mobile_image_array, $sakura_privkey;
//     } else {
//         global $wpdb, $sakura_image_array, $sakura_privkey;
//     }
//     $sakura_table_name = $wpdb->base_prefix . 'sakurairo';
//     require_once ABSPATH . "wp-admin/includes/upgrade.php";
//     /// TODO: 移除?
//     dbDelta("CREATE TABLE IF NOT EXISTS `" . $sakura_table_name . "` (
//         `mate_key` varchar(50) COLLATE utf8_bin NOT NULL,
//         `mate_value` text COLLATE utf8_bin NOT NULL,
//         PRIMARY KEY (`mate_key`)
//         ) " . (check_myisam_support() ? "ENGINE=MyISAM " : "") . "DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;");
//     //default data
//     if (!$wpdb->get_var("SELECT COUNT(*) FROM $sakura_table_name WHERE mate_key = 'manifest_json'")) {
//         $manifest = array(
//             "mate_key" => "manifest_json",
//             "mate_value" => file_get_contents(get_template_directory() . "/manifest/manifest.json"),
//         );
//         $wpdb->insert($sakura_table_name, $manifest);
//     }
//     if (iro_opt('random_graphs_mts') && !$wpdb->get_var("SELECT COUNT(*) FROM $sakura_table_name WHERE mate_key = 'mobile_manifest_json'")) {
//         $mobile_manifest = array(
//             "mate_key" => "mobile_manifest_json",
//             "mate_value" => file_get_contents(get_template_directory() . "/manifest/manifest_mobile.json"),
//         );
//         $wpdb->insert($sakura_table_name, $mobile_manifest);

//     }
//     if (!$wpdb->get_var("SELECT COUNT(*) FROM $sakura_table_name WHERE mate_key = 'json_time'")) {
//         $time = array(
//             "mate_key" => "json_time",
//             "mate_value" => date("Y-m-d H:i:s", time()),
//         );
//         $wpdb->insert($sakura_table_name, $time);
//     }
//     if (!$wpdb->get_var("SELECT COUNT(*) FROM $sakura_table_name WHERE mate_key = 'privkey'")) {
//         $privkey = array(
//             "mate_key" => "privkey",
//             "mate_value" => wp_generate_password(8),
//         );
//         $wpdb->insert($sakura_table_name, $privkey);
//     }
//     //reduce sql query
//     $sakura_image_array = $wpdb->get_var("SELECT `mate_value` FROM  $sakura_table_name WHERE `mate_key`='manifest_json'");
//     if (iro_opt('random_graphs_mts')) {
//         $sakura_mobile_image_array = $wpdb->get_var("SELECT `mate_value` FROM  $sakura_table_name WHERE `mate_key`='mobile_manifest_json'");
//     }
//     $sakura_privkey = $wpdb->get_var("SELECT `mate_value` FROM  $sakura_table_name WHERE `mate_key`='privkey'");
// }
// add_action('after_setup_theme', 'create_sakura_table');

//rest api支持
function permalink_tip()
{
    if (!get_option('permalink_structure')) {
        $msg = __('<b> For a better experience, please do not set <a href="/wp-admin/options-permalink.php"> permalink </a> as plain. To do this, you may need to configure <a href="https://www.wpdaxue.com/wordpress-rewriterule.html" target="_blank"> pseudo-static </a>. </ b>', 'sakurairo'); /*<b>为了更好的使用体验，请不要将<a href="/wp-admin/options-permalink.php">固定链接</a>设置为朴素。为此，您可能需要配置<a href="https://www.wpdaxue.com/wordpress-rewriterule.html" target="_blank">伪静态</a>。</b>*/
        echo '<div class="notice notice-success is-dismissible" id="scheme-tip"><p><b>' . $msg . '</b></p></div>';
    }
}
add_action('admin_notices', 'permalink_tip');
//code end

//发送主题版本号 
function send_theme_version()
{
    $theme = wp_get_theme();
    $version = $theme->get('Version');
    $data = array(
        'date' => date('Y-m-d H:i:s'),
        'version' => $version
    );
    $args = array(
        'body' => $data,
        'timeout' => '5',
        'redirection' => '5',
        'httpversion' => '1.0',
        'blocking' => true,
        'headers' => array(),
        'cookies' => array()
    );
    wp_remote_post('https://api.maho.cc/ver-stat/index.php', $args);
}

if (iro_opt('send_theme_version') == '1') {
    if (!wp_next_scheduled('daily_event')) {
        wp_schedule_event(time(), 'daily', 'daily_event');
    }
    add_action('daily_event', 'send_theme_version');
}

//解析短代码  
add_shortcode('task', 'task_shortcode');
function task_shortcode($attr, $content = '')
{
    $out = '<div class="task shortcodestyle"><i class="fa-solid fa-bars"></i>' . $content . '</div>';
    return $out;
}
add_shortcode('warning', 'warning_shortcode');
function warning_shortcode($attr, $content = '')
{
    $out = '<div class="warning shortcodestyle"><i class="fa-solid fa-triangle-exclamation"></i>' . $content . '</div>';
    return $out;
}
add_shortcode('noway', 'noway_shortcode');
function noway_shortcode($attr, $content = '')
{
    $out = '<div class="noway shortcodestyle"><i class="fa-solid fa-rectangle-xmark"></i>' . $content . '</div>';
    return $out;
}
add_shortcode('buy', 'buy_shortcode');
function buy_shortcode($attr, $content = '')
{
    $out = '<div class="buy shortcodestyle"><i class="fa-solid fa-check-to-slot"></i>' . $content . '</div>';
    return $out;
}
add_shortcode('ghcard', 'gh_card');
function gh_card($attr, $content = '')
{
    extract(shortcode_atts(array("path" => ""), $attr));
    return '<div class="ghcard"><a href="https://github.com/' . $path . '"><img src="https://github-readme-stats.vercel.app/api' . $content . '" alt="Github-Card"></a></div>';
}

add_shortcode('showcard', 'show_card');
function show_card($attr, $content = '')
{
    extract(shortcode_atts(array("icon" => "", "title" => "", "img" => "", "color" => ""), $attr));
    return '<section class="showcard">
    <div class="img" alt="Show-Card" style="background:url(' . $img . ');background-size:cover;background-position: center;">
    <a href="' . $content . '"><button class="showcard-button" style="color:' . $color . ' !important;"><i class="fa-solid fa-angle-right"></i></button> </a>
    </div>
    <br>
    <div class="icon">
    <i class="' . $icon . '"></i>
    </div>
    <div class="title">
    ' . $title . '		
    </div>
</section>';
}

add_shortcode('conversations', 'conversations');
function conversations($attr, $content = '')
{
    extract(shortcode_atts(array("avatar" => "", "direction" => "", "username" => ""), $attr));
    if ($avatar == "" && $username != "") {
        $user_id = get_user_by('login', $username)->ID;
        if ($user_id) {
            $avatar = get_avatar_url($user_id, 40);
        }
    }
    $speaker_alt = $username?'<span class="screen-reader-text">'.sprintf(__("%s says: ","sakurairo"),$username).'</span>':"";
    $output = '<div class="conversations-code" style="flex-direction: ' . $direction . ';">';
    $output .= "<img src=\"$avatar\">";
    $output .= '<div class="conversations-code-text">'. $speaker_alt . $content . '</div>';
    $output .= '</div>';

    return $output;
}

//code end

//WEBP支持
function mimvp_filter_mime_types($array)
{
    $array['webp'] = 'image/webp';
    return $array;
}
add_filter('mime_types', 'mimvp_filter_mime_types', 10, 1);
function mimvp_file_is_displayable_image($result, $path)
{
    $info = @getimagesize($path);
    // if ($info['mime'] == 'image/webp') {
    //     $result = true;
    // }
    // return $result;
    return (bool) ($info); // 根据文档这里需要返回一个bool
}
add_filter('file_is_displayable_image', 'mimvp_file_is_displayable_image', 10, 2);

//code end

//展开收缩功能
function xcollapse($atts, $content = null)
{
    $atts = shortcode_atts(array("title" => ""), $atts);

    ob_start(); // 开启输出缓存

    // HTML 结构
    ?>
                                <div style="margin: 0.5em 0;">
                                    <div class="xControl">
                                        <i class="fa-solid fa-angle-down" style="color: #16AF90;"></i> &nbsp;&nbsp;
                                        <span class="xTitle"><?= $atts['title'] ?></span>&nbsp;&nbsp;==>&nbsp;&nbsp;<a href="javascript:void(0)" class="collapseButton xButton"><span class="xbtn02"><?php _e('Expand / Collapse', 'sakurairo'); ?></span></a>
                                        <div style="clear: both;"></div>
                                    </div>
                                    <div class="xContent" style="display: none;"><?= do_shortcode($content) ?></div>
                                </div>
                                <?php

                                $output = ob_get_contents(); // 获取输出缓存
                                ob_end_clean(); // 清空输出缓存
                            
                                return $output; // 返回 HTML 结构
}
add_shortcode('collapse', 'xcollapse');

//code end


// add_action("wp_ajax_nopriv_getPhoto", "get_photo");
// add_action("wp_ajax_getPhoto", "get_photo");
// /**
//  * 相册模板
//  * @author siroi <mrgaopw@hotmail.com>
//  * @return Json
//  */
// function get_photo()
// {
//     $postId = $_GET['post'];
//     $page = get_post($postId);
//     if ($page->post_type != "page") {
//         $back['code'] = 201;
//     } else {
//         $back['code'] = 200;
//         $back['imgs'] = array();
//         $dom = new DOMDocument('1.0', 'utf-8');
//         $meta = '<meta http-equiv="Content-Type" content="text/html; charset=utf-8">';
//         $dom->loadHTML($meta . $page->post_content);
//         $imgS = $dom->getElementsByTagName('img');
//         //<img src="..." data-header="标题" data-info="信息" vertical=false>
//         foreach ($imgS as $key => $value) {
//             $attr = $value->attributes;
//             $header = $attr->getNamedItem('header');
//             $info = $attr->getNamedItem('data-info');
//             $vertical = $attr->getNamedItem('vertical');

//             //图片资源地址
//             $temp['img'] = $value->attributes->getNamedItem('src')->nodeValue;
//             //图片上的标题
//             $temp['header'] = $header->nodeValue ?? null;
//             //图片上的信息
//             $temp['info'] = $info->nodeValue ?? null;
//             //是否竖向展示 默认false
//             $temp['vertical'] = $vertical->nodeValue ?? null;
//             array_push($back['imgs'], $temp);
//         }
//     }
//     header('Content-Type:application/json;charset=utf-8');
//     echo json_encode($back);
//     exit();
// }

if (!iro_opt('login_language_opt') == '1') {
    add_filter('login_display_language_dropdown', '__return_false');
}

if (iro_opt('captcha_select') === 'iro_captcha') {
    function login_CAPTCHA()
    {
        include_once('inc/classes/Captcha.php');
        $img = new Sakura\API\Captcha;
        $test = $img->create_captcha_img();
        echo '<p><label for="captcha" class="captcha">验证码<br><img id="captchaimg" width="120" height="40" src="', $test['data'], '"><input type="text" name="yzm" id="yzm" class="input" value="" size="20" tabindex="4" placeholder="请输入验证码"><input type="hidden" name="timestamp" value="', $test['time'], '"><input type="hidden" name="id" value="', $test['id'], '">'
            . "</label></p>";
    }
    add_action('login_form', 'login_CAPTCHA');
    add_action('register_form', 'login_CAPTCHA');
    add_action('lostpassword_form', 'login_CAPTCHA');

    /**
     * 登录界面验证码验证
     */
    function CAPTCHA_CHECK($user, $username, $password)
    {
        if (empty($_POST)) {
            return new WP_Error();
        }
        if (!(isset($_POST['yzm']) && !empty(trim($_POST['yzm'])))) {
            return new WP_Error('prooffail', '<strong>错误</strong>：验证码为空！');
        }
        if (!isset($_POST['timestamp']) || !isset($_POST['id']) || !preg_match('/^[\w$.\/]+$/', $_POST['id']) || !ctype_digit($_POST['timestamp'])) {
            return new WP_Error('prooffail', '<strong>错误</strong>：非法数据');
        }
        include_once('inc/classes/Captcha.php');
        $img = new Sakura\API\Captcha;
        $check = $img->check_captcha($_POST['yzm'], $_POST['timestamp'], $_POST['id']);
        if ($check['code'] == 5) {
            return $user;
        }
        return new WP_Error('prooffail', '<strong>错误</strong>：' . $check['msg']);
        //return home_url('/wp-admin/');


    }
    add_filter('authenticate', 'CAPTCHA_CHECK', 20, 3);
    /**
     * 忘记密码界面验证码验证
     */
    function lostpassword_CHECK($errors)
    {
        if (empty($_POST)) {
            return false;
        }
        if (isset($_POST['yzm']) && !empty(trim($_POST['yzm']))) {
            if (!isset($_POST['timestamp']) || !isset($_POST['id']) || !preg_match('/^[\w$.\/]+$/', $_POST['id']) || !ctype_digit($_POST['timestamp'])) {
                return new WP_Error('prooffail', '<strong>错误</strong>：非法数据');
            }
            include_once('inc/classes/Captcha.php');
            $img = new Sakura\API\Captcha;
            $check = $img->check_captcha($_POST['yzm'], $_POST['timestamp'], $_POST['id']);
            if ($check['code'] != 5) {
                return $errors->add('invalid_department ', '<strong>错误</strong>：' . $check['msg']);
            }
        } else {
            return $errors->add('invalid_department', '<strong>错误</strong>：验证码为空！');
        }
    }

    add_action('lostpassword_post', 'lostpassword_CHECK');
    /** 
     *   注册界面验证码验证
     */
    function registration_CAPTCHA_CHECK($errors, $sanitized_user_login, $user_email)
    {
        if (empty($_POST)) {
            return new WP_Error();
        }
        if (!(isset($_POST['yzm']) && !empty(trim($_POST['yzm'])))) {
            return new WP_Error('prooffail', '<strong>错误</strong>：验证码为空！');
        }
        if (!isset($_POST['timestamp']) || !isset($_POST['id']) || !preg_match('/^[\w$.\/]+$/', $_POST['id']) || !ctype_digit($_POST['timestamp'])) {
            return new WP_Error('prooffail', '<strong>错误</strong>：非法数据');
        }
        include_once('inc/classes/Captcha.php');
        $img = new Sakura\API\Captcha;
        $check = $img->check_captcha($_POST['yzm'], $_POST['timestamp'], $_POST['id']);
        if ($check['code'] == 5)
            return $errors;

        return new WP_Error('prooffail', '<strong>错误</strong>：' . $check['msg']);

    }
    add_filter('registration_errors', 'registration_CAPTCHA_CHECK', 2, 3);
} elseif ((iro_opt('captcha_select') === 'vaptcha') && (!empty(iro_opt("vaptcha_vid")) && !empty(iro_opt("vaptcha_key")))) {
    function vaptchaInit()
    {
        include_once('inc/classes/Vaptcha.php');
        $vaptcha = new Sakura\API\Vaptcha;
        echo $vaptcha->html();
        echo $vaptcha->script();
    }
    add_action('login_form', 'vaptchaInit');

    function checkVaptchaAction($user)
    {
        if (empty($_POST)) {
            return new WP_Error();
        }
        if (!(isset($_POST['vaptcha_server']) && isset($_POST['vaptcha_token']))) {
            return new WP_Error('prooffail', '<strong>错误</strong>：请先进行人机验证');

        }
        if (!preg_match('/^https:\/\/([\w-]+\.)+[\w-]*([^<>=?\"\'])*$/', $_POST['vaptcha_server']) || !preg_match('/^[\w\-\$]+$/', $_POST['vaptcha_token'])) {
            return new WP_Error('prooffail', '<strong>错误</strong>：非法数据');
        }
        include_once('inc/classes/Vaptcha.php');
        $url = $_POST['vaptcha_server'];
        $token = $_POST['vaptcha_token'];
        $ip = get_the_user_ip();
        $vaptcha = new Sakura\API\Vaptcha;
        $response = $vaptcha->checkVaptcha($url, $token, $ip);
        if ($response->msg && $response->success && $response->score) {
            if ($response->success === 1 && $response->score >= 70) {
                return $user;
            }
            if ($response->success === 0) {
                $errorcode = $response->msg;
                return new WP_Error('prooffail', '<strong>错误</strong>：' . $errorcode);
            }
            return new WP_Error('prooffail', '<strong>错误</strong>：人机验证失败');

        } else if (is_string($response)) {
            return new WP_Error('prooffail', '<strong>错误</strong>：' . $response);
        }
        return new WP_Error('prooffail', '<strong>错误</strong>：未知错误');


    }
    add_filter('authenticate', 'checkVaptchaAction', 20, 3);
}
/**
 * 返回是否应当显示文章标题。
 * 
 */
function should_show_title(): bool
{
    $id = get_the_ID();
    $use_as_thumb = get_post_meta($id, 'use_as_thumb', true); //'true','only',(default)
    return !iro_opt('patternimg')
        || !get_post_thumbnail_id($id)
        && $use_as_thumb != 'true' && !get_post_meta($id, 'video_cover', true);
}

/**
 * 修复 WordPress 搜索结果为空，返回为 200 的问题。
 * @author ivampiresp <im@ivampiresp.com>
 */
function search_404_fix_template_redirect()
{
    if (is_search()) {
        global $wp_query;

        if ($wp_query->found_posts == 0) {
            status_header(404);
        }
    }
}

add_action('template_redirect', 'search_404_fix_template_redirect');

// 给上传图片增加时间戳
add_filter('wp_handle_upload_prefilter', function ($file) {
    $file['name'] = time() . '-' . $file['name'];
    return $file;
});

/**
 * 在后台评论列表中添加IP地理位置信息列
 *
 * @param string[] $columns 列表标题的标签
 * @return void
 */
function iro_add_location_to_comments_columns($columns)
{
    $columns['iro_ip_location'] = __('Location', 'sakurairo');
    return $columns;
}

/**
 * 将IP地理位置信息输出到评论列表中
 *
 * @param string $column_name 列表标题的标签
 * @param int $comment_id 评论ID
 * @return void
 */
function iro_output_ip_location_columns($column_name, $comment_id)
{
    switch ($column_name) {
        case "iro_ip_location":
            echo \Sakura\API\IpLocationParse::getIpLocationByCommentId($comment_id);
            break;
    }
}
if (iro_opt('show_location_in_manage')) {
    add_filter('manage_edit-comments_columns', 'iro_add_location_to_comments_columns');
    add_action('manage_comments_custom_column', 'iro_output_ip_location_columns', 10, 2);
}

// Modify search query to exclude pages and categories(修改搜索查询以排除'页面'和'类别')
function exclude_pages_and_categories_from_search($query) {
    if (!is_admin() && $query->is_search) {
        // Exclude pages
        $query->set('post_type', array('post', 'shuoshuo', 'link')); // Include other post types but exclude 'page'

        // Exclude categories
        $tax_query = array(
            array(
                'taxonomy' => 'category',
                'field' => 'name',
                'terms' => get_search_query(),
                'operator' => 'NOT IN'
            )
        );
        $query->set('tax_query', $tax_query);
    }
    return $query;
}
add_filter('pre_get_posts', 'exclude_pages_and_categories_from_search');

function iterator_to_string(Iterator $iterator): string
{
    $content = '';
    foreach ($iterator as $item) {
        $content .= $item;
    }
    return $content;
}
//保护后台登录
add_action('login_enqueue_scripts','login_protection');  
function login_protection(){  
    if($_GET['cmxz'] != 'yish')header('Location: https://cmxz.top');  
}