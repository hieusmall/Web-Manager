<?php
/**
 * Template Name: Thanks You Page
 *
 * @since    1.0.0
 * @version    1.0.0
 */

// Define some library
$webManager = new webManagerLib;
$staticURL = $webManager::PLUGIN_NAME . '/' . $webManager::VENDOR_ASSET;


get_header() ?>

<?php if (have_posts()) :
        while (have_posts()) :
    the_post(); ?>
        <?php the_content() ?>
<?php endwhile; endif; ?>
<?php get_footer() ?>

<!--<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Chúc mừng quý khách đã đăng kí thành công</title>

    <link rel="stylesheet" href="<?php /*echo plugins_url($staticURL . 'bootstrap/css/bootstrap.min.css') */ ?>">
    <script src="<?php /*echo $staticURL . '' */ ?>"></script>
</head>
<body>

</body>
</html>-->