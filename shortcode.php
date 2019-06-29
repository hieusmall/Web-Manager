<?php

function blogList($atts) {
    // Defaults
    extract(shortcode_atts(array(
        "chuyenmuc" => false,
        "so_luong" => 3,
        "chuyenmuc_lienquan" => false,
        "kieu_block" => "slide"
    ), $atts));

    // de-funkify query
    $so_luong = $so_luong ? $so_luong : 10;
    $kieu_block = $kieu_block && in_array($kieu_block, ["slide","feature"]) ? $kieu_block : "slide";

    $query = array(
        "order" => "DESC",
        "orderby" => "date",
        "post_status" => "publish",
        "posts_per_page" => $so_luong
    );

    $videoCategory = false;
    if (!$chuyenmuc) {
        return "Không tìm thấy tên chuyên mục";
    } elseif ((int)$chuyenmuc) {
        if ($chuyenmuc) $query["cat"] = $chuyenmuc;
        $videoCategory = get_category( $chuyenmuc );
    } else if (gettype($chuyenmuc) == "string") {
        if ($chuyenmuc) $query["category_name"] = $chuyenmuc;
        $videoCategory = get_category_by_slug("video");
    } else {
        return "Không tìm thấy chuyên mục này";
    }

    $childrenVideoCategories = get_categories(array("parent" => $videoCategory->term_id));
    $html = "";

    // query is made
    $videosFeature = new WP_Query($query);
    // the loop

    switch ($kieu_block) {
        case "slide" :
            $categoryName = $videoCategory->name;
            $categoryId = $videoCategory->cat_ID;
            if ($videosFeature->have_posts()) :
                $html .= '<div class="wm-row wrap-box wrap-box-slide">
            <div class="card cat-slide cat1 wrap-videos-cat">
                <div class="title">
                    <h5 class=""><span>'.$categoryName.'</span></h5>
                </div>
                <div class="card-body card-videos-wrap">';

                while($videosFeature->have_posts()) : $videosFeature->the_post();
                    $ytIdVideoDieuTriDa = get_post_meta( get_the_ID(), 'mfn-post-video', true );
                    $videoDieuTriDaURL = "//www.youtube.com/watch?v=$ytIdVideoDieuTriDa&autoplay=1&rel=0&controls=0&showinfo=0";
                    $imageDieuTriDaURL = "";
                    if (has_post_thumbnail()) {
                        $imageDieuTriDaURL = get_the_post_thumbnail_url();
                    } else {
                        $youtubeVideo = webManagerLib::wmReadYoutubeVideo($ytIdVideoDieuTriDa);
                        $youtubeVideo = gettype($youtubeVideo) == "object" && $youtubeVideo ? $youtubeVideo : false;
                        if ($youtubeVideo) {
                            $youtubeVideo = $youtubeVideo->details->items[0];
                            $imageDieuTriDaURL = $youtubeVideo->snippet->thumbnails->standard->url;
                            if (!$imageDieuTriDaURL) $imageDieuTriDaURL = $youtubeVideo->snippet->thumbnails->high->url;
                            if (!$imageDieuTriDaURL) $imageDieuTriDaURL = $youtubeVideo->snippet->thumbnails->medium->url;
                        }
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
            break;
        case "feature" :
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
                        $youtubeVideo = webManagerLib::wmReadYoutubeVideo($ytVideoId);
                        if ($youtubeVideo) {
                            $youtubeVideo = $youtubeVideo->details->items[0];
//                            $imageFeatureVideo = $isBig ? $youtubeVideo->snippet->thumbnails->maxres->url : $youtubeVideo->snippet->thumbnails->high->url;
                            $imageFeatureVideo = $youtubeVideo->snippet->thumbnails->maxres->url;
                            if ($imageFeatureVideo) $imageFeatureVideo = $youtubeVideo->snippet->thumbnails->standard->url;
                            if (!$imageFeatureVideo) $imageFeatureVideo = $youtubeVideo->snippet->thumbnails->high->url;
                            if (!$imageFeatureVideo) $imageFeatureVideo = $youtubeVideo->snippet->thumbnails->medium->url;
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
                                <span style="background-image: url('.$imageFeatureVideo.');"></span>
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
            break;
    }

    // the loop
    if ($chuyenmuc_lienquan) {
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
                        $youtubeVideo = webManagerLib::wmReadYoutubeVideo($ytIdVideoDieuTriDa);
                        $youtubeVideo = gettype($youtubeVideo) == "object" ? $youtubeVideo : false;
                        if ($youtubeVideo) {
                            $youtubeVideo = $youtubeVideo->details->items[0];
                            $imageDieuTriDaURL = $youtubeVideo->snippet->thumbnails->standard->url;
                            if (!$imageDieuTriDaURL) $youtubeVideo->snippet->thumbnails->high->url;
                            if (!$imageDieuTriDaURL) $youtubeVideo->snippet->thumbnails->medium->url;
                        }
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
    }

    return $html;
}
add_shortcode("blogList", "blogList");

