<?php
/**
 * Template Name: Template Videos 1
 *
 */

$videoCategory = get_category_by_slug( "video" );
$childrenVideoCategories = get_categories(array("parent" => $videoCategory->term_id));

//echo "<pre>";
//print_r($childrenVideoCategories);
//$x = get_post_meta( 5064, 'mfn-post-video', true );
//echo "</pre>";
//die();

get_header();

?>
<link rel="stylesheet" href="//cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.css" />
<script src="//cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.js"></script>
<link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css"/>
<script type="text/javascript" src="//cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.css"></script>
<script type="text/javascript">
    (function ($) {
        $(document).ready(function () {
            $("#Subheader").hide();
            var $videosWrapSlides = $('.wrap-videos-cat');
            $videosWrapSlides.each(function (i, elem) {
                var $wrap = $(elem);
                var $slideBox = $wrap.find('.card-videos-wrap');
                var $prev = $wrap.find('.prev');
                var $next = $wrap.find('.next');
                $slideBox.slick({
                    // dots: true,
                    infinite: true,
                    speed: 500,
                    slidesToShow: 1,
                    centerMode: true,
                    autoplay: false,
                    variableWidth: true,
                    centerPadding: "20px",
                    focusOnSelect: true,
                    mobileFirst: true,
                    prevArrow: $prev,
                    nextArrow: $next,
                });
            });
        })
    })(jQuery)
</script>


<div class="wm-page-wrapper wm-wrapper wm-video-wrapper">
    <div class="page-title">
        <img src="//beta.thammyvienngocdung.com/wp-content/uploads/2019/05/phan-hoi-desktop.jpg" alt="">
        <div class="wm-container">
            <h1><span><?php echo $videoCategory->name ?></span></h1>
        </div>
    </div>
    <div class="page-content">
        <div class="wm-container">
            <?php $videosFeature = new WP_Query(array(
                'cat' => $videoCategory->term_id,
                'post_status' => "publish",
                "posts_per_page" => 4,
                'order' => 'DESC',
                'orderby' => 'date'
            ));
            $k = 0;
            if ($videosFeature->have_posts()) : ?>
            <div class="wm-row wrap-box">
            <?php while ($videosFeature->have_posts()) : $videosFeature->the_post();
                $isBig = $k == 0;
                $classBoxItem = $isBig ? "box-big" : "box-small";
                $ytVideoId = get_post_meta( get_the_ID(), 'mfn-post-video', true );
                $videoFeatureURL = "//www.youtube.com/watch?v=$ytVideoId&autoplay=1&rel=0&controls=0&showinfo=0";
                if (has_post_thumbnail()) {
                    $imageFeatureVideo = the_post_thumbnail_url();
                } else {
                    $videoFeature = webManagerLib::ytVideoDetailById($ytVideoId)->items[0];
                    $videoFeatureStatistics = $videosFeature->statistics;
                    $imageFeatureVideo = $isBig ? $videoFeature->snippet->thumbnails->maxres->url : $videoFeature->snippet->thumbnails->high->url;
                    if (!$imageFeatureVideo) {
                        $imageFeatureVideo = $videoFeature->snippet->thumbnails->high->url;
                    }
                }
                ?>
                <?php if ($isBig) : ?>
                    <div class="wm-col-md-7 wm-col-left">
                <?php elseif ($k == 1) : ?>
                    <div class="wm-col wm-row wm-col-right">
                <?php endif; ?>
                    <div class="<?php echo $classBoxItem ?> box-item">
                        <div class="box-thumbnail">
                            <a class="" data-fancybox href="<?php echo $videoFeatureURL ?>">
                                <span style="background-image: url('<?php echo $imageFeatureVideo ?>');"></span>
                            </a>
                        </div>
                        <div class="box-content">
                            <h5 class="title">
                                <a class="" data-fancybox href="<?php echo $videoFeatureURL ?>">
                                    <?php the_title(); ?>
                                </a>
                            </h5>
                        </div>
                    </div>
                <?php if ($isBig || $k == 3) : ?>
                    </div>
                <?php endif; ?>
            <?php $k++;endwhile;?>
            </div>
            <?php endif; ?>

            <?php foreach ($childrenVideoCategories as $category) :
                $categoryName = $category->name;
                $categoryId = $category->cat_ID;
                $videosDieuTriDa = new WP_Query(array(
                    'cat' => $categoryId,
                    'post_status' => "publish",
                    "posts_per_page" => 10,
                    'order' => 'DESC',
                    'orderby' => 'date'
                ));
                if ($videosDieuTriDa->have_posts()) : ?>
                    <div class="wm-row wrap-box wrap-box-slide">
                        <div class="card cat-slide cat1 wrap-videos-cat">
                            <div class="title">
                                <h5 class=""><span><?php echo $categoryName ?></span></h5>
                            </div>
                            <div class="card-body card-videos-wrap">
                                <?php while($videosDieuTriDa->have_posts()) : $videosDieuTriDa->the_post();
                                    $ytIdVideoDieuTriDa = get_post_meta( get_the_ID(), 'mfn-post-video', true );
                                    $videoDieuTriDaURL = "//www.youtube.com/watch?v=$ytIdVideoDieuTriDa&autoplay=1&rel=0&controls=0&showinfo=0";
                                    if (has_post_thumbnail()) {
                                        $imageDieuTriDaURL = the_post_thumbnail_url();
                                    } else {
                                        $videoDieuTriDa = webManagerLib::ytVideoDetailById($ytIdVideoDieuTriDa)->items[0];
                                        $imageDieuTriDaURL = $videoDieuTriDa->snippet->thumbnails->standard->url;
                                        if (!$imageDieuTriDaURL) $imageDieuTriDaURL = $videoDieuTriDa->snippet->thumbnails->high->url;
                                    }
                                    ?>
                                    <div class="box-item">
                                        <a class="video-box-detail youtube-fancybox" data-fancybox href="<?php echo $videoDieuTriDaURL ?>"
                                           title="<?php the_title() ?>">
                                            <img class="" src="<?php echo $imageDieuTriDaURL ?>" alt="" />
                                            <p><?php the_title() ?></p>
                                        </a>
                                    </div>
                                <?php endwhile; ?>
                            </div>
                            <div class="wrap-pagination-slide">
                                <a href="#" class="prev"><span class="dashicons dashicons-arrow-left-alt"></span></a>
                                <a href="#" class="next"><span class="dashicons dashicons-arrow-right-alt"></span></a>
                            </div>
                        </div>
                    </div>
            <?php endif; endforeach; ?>
        </div>
    </div>
</div>

<?php get_footer(); ?>