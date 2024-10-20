<title>Σ(oﾟдﾟoﾉ) 无法找到该页面</title>
<?php 
get_header(); 
?>
<head>
</head>
<img src="https://cmxz.top/images/404/very_sorry.webp"><br></br>

<div class="err-button back" style="display: flex; flex-direction: row; flex-wrap: wrap; align-content: center; justify-content: center;">
    <a id="golast" href="javascript:history.go(-1);"><?php _e('Return to previous page', 'sakurairo'); ?></a>
    <a id="gohome" href="<?php echo esc_url(home_url('/')); ?>"><?php _e('Return to home page', 'sakurairo'); ?></a>
</div>
<br></br><br>

<figure class="wp-block-image size-large">
    <img decoding="async" src="https://cmxz.top/images/404/api.php" onerror="imgError(this)" alt="">
</figure>

<?php
get_footer();
?>
