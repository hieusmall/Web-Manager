<?php

function blogVideoList($atts) {
	print_r($childrenVideoCategories);

	// Defaults
   	extract(shortcode_atts(array(
      	"the_query" => '',
      	"has_library" => false,
      	"chuyenmuc" => false
   	), $atts));
   	$query = array(
   		"posts_per_page" => 4,
   		"order" => "DESC",
   		"orderby" => "date",
   		"post_status" => "publish"
   	);
   	// de-funkify query
   	parse_str($the_query, $the_query);
   	$query = array_merge($query, $the_query);
   	$catId = $chuyenmuc ? $chuyenmuc : false;
   	if (!$catId) {
   		return "Không tìm thấy post";
   	}

   	$videoCategory = get_category( $catId );
	$childrenVideoCategories = get_categories(array("parent" => $videoCategory->term_id));
	$plugins_url = "/wp-content/plugins/Web-Manager/assets/";
	$html = "";

	// Style and javascript
   	if ($has_library == true) {
   		$dependencies = '<link rel="stylesheet" href="'. $plugins_url . "vendor/fancybox/dist/jquery.fancybox.min.css".'" />
			<script src="'.$plugins_url . "vendor/fancybox/dist/jquery.fancybox.min.js".'"></script>
			<link rel="stylesheet" type="text/css" href="'.$plugins_url . "vendor/slick/slick.css" . '"/>
			<script type="text/javascript" src="'.$plugins_url . "vendor/slick/slick.js".'"></script>
			<script type="text/javascript">
			    (function ($) {
			        $(document).ready(function () {
			            $("#Subheader").hide();
			            var $videosWrapSlides = $(".wrap-videos-cat");
			            $videosWrapSlides.each(function (i, elem) {
			                var $wrap = $(elem);
			                var $slideBox = $wrap.find(".card-videos-wrap");
			                var $prev = $wrap.find(".prev");
			                var $next = $wrap.find(".next");
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
			</script>';
		$html .= $dependencies;
   	}

	// query is made               
    $videosFeature = new WP_Query(array(
        'cat' => $videoCategory->term_id,
        'post_status' => "publish",
        "posts_per_page" => 4,
        'order' => 'DESC',
        'orderby' => 'date'
    ));
   	// the loop
    $k = 0;
    if ($videosFeature->have_posts()) :

    	$html .= '<div class="wm-row wrap-box">';

    	while ($videosFeature->have_posts()) : $videosFeature->the_post();
    		$isBig = $k == 0;
            $classBoxItem = $isBig ? "box-big" : "box-small";
            $ytVideoId = get_post_meta( get_the_ID(), 'mfn-post-video', true );
            $videoFeatureURL = "//www.youtube.com/watch?v=$ytVideoId&autoplay=1&rel=0&controls=0&showinfo=0";
            $imageFeatureVideo = "";
            if (has_post_thumbnail()) {
                $imageFeatureVideo = get_the_post_thumbnail_url();
            } else {
                $videoFeature = webManagerLib::ytVideoDetailById($ytVideoId)->items[0];
                $videoFeatureStatistics = $videosFeature->statistics;
                $imageFeatureVideo = $isBig ? $videoFeature->snippet->thumbnails->maxres->url : $videoFeature->snippet->thumbnails->high->url;
                if (!$imageFeatureVideo) {
                    $imageFeatureVideo = $videoFeature->snippet->thumbnails->high->url;
                }
            }
            if ($isBig) {
            	$html .= '<div class="wm-col-md-7 wm-col-left">';
            } elseif ($k == 1) {
            	$html .= '<div class="wm-col wm-col-right">';
            }

            $html .= '<div class="'.$classBoxItem.' box-item">
                        <div class="box-thumbnail">
                            <a class="" data-fancybox href="'.$videoFeatureURL.'">
                                <span style="background-image: url(\''.$imageFeatureVideo.'\');"></span>
                            </a>
                        </div>
                        <div class="box-content">
                            <h5 class="title">
                                <a class="" data-fancybox href="'.$videoFeatureURL.'">
                                    '.get_the_title() .'
                                </a>
                            </h5>
                        </div>
                    </div>';

            if ($isBig || $k == 3) {
            	$html .= '</div>';
            }

            $k++;
        endwhile;
        $html .= '</div>';
        wp_reset_query();
    endif;

   	// the loop
    foreach ($childrenVideoCategories as $category) :
        $categoryName = $category->name;
        $categoryId = $category->cat_ID;
        $videosDieuTriDa = new WP_Query(array(
            'cat' => $categoryId,
            'post_status' => "publish",
            "posts_per_page" => 10,
            'order' => 'DESC',
            'orderby' => 'date'
        ));
        if ($videosDieuTriDa->have_posts()) :
        	$html .= '<div class="wm-row wrap-box wrap-box-slide">
            <div class="card cat-slide cat1 wrap-videos-cat">
                <div class="title">
                    <h5 class=""><span>'.$categoryName.'</span></h5>
                </div>
                <div class="card-body card-videos-wrap">';

            while($videosDieuTriDa->have_posts()) : $videosDieuTriDa->the_post();
                $ytIdVideoDieuTriDa = get_post_meta( get_the_ID(), 'mfn-post-video', true );
                $videoDieuTriDaURL = "//www.youtube.com/watch?v=$ytIdVideoDieuTriDa&autoplay=1&rel=0&controls=0&showinfo=0";
                $imageDieuTriDaURL = "";
                if (has_post_thumbnail()) {
                    $imageDieuTriDaURL = get_the_post_thumbnail_url();
                } else {
                    $videoDieuTriDa = webManagerLib::ytVideoDetailById($ytIdVideoDieuTriDa)->items[0];
                    $imageDieuTriDaURL = $videoDieuTriDa->snippet->thumbnails->standard->url;
                    if (!$imageDieuTriDaURL) $imageDieuTriDaURL = $videoDieuTriDa->snippet->thumbnails->high->url;
                }

                $html .= '<div class="box-item">
                    <a class="video-box-detail youtube-fancybox" data-fancybox href="'.$videoDieuTriDaURL.'"
                       title="'.get_the_title().'">
                        <img class="" src="'.$imageDieuTriDaURL.'" alt="" />
                        <p>'.get_the_title().'</p>
                    </a>
                </div>';
            endwhile;
            $html .= '</div>
                    <div class="wrap-pagination-slide">
                        <a href="#" class="prev"><span class="dashicons dashicons-arrow-left-alt"></span></a>
                        <a href="#" class="next"><span class="dashicons dashicons-arrow-right-alt"></span></a>
                    </div>
                </div>
            </div>';
            wp_reset_query();
        endif;
    endforeach;

    /*$html .= '</div>
	    </div>
	</div>';*/

   	// echo "<pre>";print_r($videosFeature);echo "<pre>";die();

	return $html;
}
add_shortcode("blogVideoList", "blogVideoList");

