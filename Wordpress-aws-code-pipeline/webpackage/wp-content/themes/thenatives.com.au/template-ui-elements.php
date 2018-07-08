<?php
/*
 * Template Name: UI Elements
 */ ?>

<?php get_header(); ?>

    <section class="container UIElement">
        <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                <h5 class="titleText">Desktop Vertical Spacing</h5>
                <div class="verticalSpacing-lg"></div>
                <div class="desktopVerticalSpacing row">
                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                        <div class="boxExample1"></div>
                        <p class="titleText">90px</p>
                    </div>

                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                        <div class="boxExample2"></div>
                        <p class="titleText">45px</p>
                    </div>

                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                        <div class="boxExample3"></div>
                        <p class="titleText">30px</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                <h5 class="titleText">Mobile Vertical Spacing</h5>
                <div class="verticalSpacing-lg"></div>
                <div class="mobileVerticalSpacing row">
                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                        <div class="boxExample1"></div>
                        <p class="titleText">60px</p>
                    </div>

                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                        <div class="boxExample2"></div>
                        <p class="titleText">30px</p>
                    </div>

                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                        <div class="boxExample3"></div>
                        <p class="titleText">20px</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="verticalSpacing-lg"></div>
    <div class="verticalSpacing-lg"></div>
    <div class="verticalSpacing-lg"></div>

    <section class="container UIElement">
        <div class="row allImageThumbnails">
            <h5 class="titleText col-lg-12 col-md-12 col-sm-12 col-xs-12">All image thumbnails should receive the same white opacity hover treatment</h5>
            <div class="verticalSpacing-lg"></div>
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                <div class="hrefThumbnails margin-space">
                    <a class="imageThumbnails">
                        <img class="img-responsive image-lg" src="<?php echo THEME_IMAGES; ?>/campaign-summer-17-4.png" alt="image">
                        <div class="boxContain contentThumbnails-Top">
                            <div class="innerBoxContain">
                                <p>19 of Our Favourite Sustainably Sourced Local Labels</p>
                                <span class="tagName fashion">fashion</span>
                            </div>
                        </div>
                    </a>
                    <div class="clearfix"></div>
                </div>
                <p class="titleText">static</p>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                <div class="hrefThumbnails margin-space">
                    <a class="imageThumbnails">
                        <img class="img-responsive image-lg" src="<?php echo THEME_IMAGES; ?>/campaign-summer-17-4.png" alt="image">
                        <div class="boxContain contentThumbnails-Top">
                            <div class="innerBoxContain">
                                <p>19 of Our Favourite Sustainably Sourced Local Labels</p>
                                <span class="tagName fashion">fashion</span>
                            </div>
                        </div>
                    </a>
                    <div class="clearfix"></div>
                </div>
                <p class="titleText">hover: 20% white overlay</p>
            </div>
            <div class="verticalSpacing-md"></div>
            <div class="verticalSpacing-sm"></div>
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                <div class="row">
                    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                        <div class="hrefThumbnails">
                            <a class="imageThumbnails">
                                <img class="img-responsive image-sm" src="<?php echo THEME_IMAGES; ?>/image.png" alt="image">
                                <div class="boxContain contentThumbnails-Bottom">
                                    <div class="innerBoxContain beforeHover">
                                        <p>19-20 may</p>
                                    </div>
                                    <div class="innerBoxContain afterHover">
                                        <p>add to calendar</p>
                                        <span class="tagName sales">sales</span>
                                    </div>
                                </div>
                            </a>
                            <h6 class="thumbnailLocal">melbourne</h6>
                            <p class="thumbnailName">Ginger & Smart <br/> Warehouse Sale</p>
                            <div class="clearfix"></div>
                        </div>
                    </div>

                    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                        <div class="hrefThumbnails">
                            <a class="imageThumbnails">
                                <img class="img-responsive image-sm" src="<?php echo THEME_IMAGES; ?>/image.png" alt="image">
                                <div class="boxContain contentThumbnails-Bottom">
                                    <div class="innerBoxContain beforeHover">
                                        <p>19-20 may</p>
                                    </div>
                                    <div class="innerBoxContain afterHover">
                                        <p>add to calendar</p>
                                        <span class="tagName sales">sales</span>
                                    </div>
                                </div>
                            </a>
                            <h6 class="thumbnailLocal">melbourne</h6>
                            <p class="thumbnailName">Ginger & Smart <br/> Warehouse Sale</p>
                            <div class="clearfix"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                <div class="row">
                    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                        <div class="hrefThumbnails">
                            <a class="imageThumbnails">
                                <img class="img-responsive image-md" src="<?php echo THEME_IMAGES; ?>/imgpsh_fullsize.png" alt="image">
                                <div class="boxContain contentThumbnails-Bottom">
                                    <div class="innerBoxContain">
                                        <p><span>WIN</span> An internship with Bassike</p>
                                    </div>
                                </div>
                            </a>
                            <div class="clearfix"></div>
                        </div>
                    </div>

                    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                        <div class="hrefThumbnails">
                            <a class="imageThumbnails">
                                <img class="img-responsive image-md" src="<?php echo THEME_IMAGES; ?>/imgpsh_fullsize.png" alt="image">
                                <div class="boxContain contentThumbnails-Bottom">
                                    <div class="innerBoxContain">
                                        <p><span>WIN</span> An internship with Bassike</p>
                                    </div>
                                </div>
                            </a>
                            <div class="clearfix"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="verticalSpacing-lg"></div>
    <div class="verticalSpacing-lg"></div>

    <section class="container UIElement">
        <div class="row">
            <div class="col-lg-7 col-md-7 col-sm-6 col-xs-12">
                <h5 class="titleText">slider buttons</h5>
                <div class="verticalSpacing-md"></div>
                <div class="row">
                    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-6">
                        <button class="sliderButton onLandInactive">
                            <img class="buttonLeft" src="<?php echo THEME_IMAGES; ?>/dropdown-arrow-black.svg" alt="image">
                        </button>
                        <p class="titleText">on land left inactive</p>
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-6">
                        <button class="sliderButton onLandInactive">
                            <img class="buttonRight" src="<?php echo THEME_IMAGES; ?>/dropdown-arrow-black.svg" alt="image">
                        </button>
                        <p class="titleText">on land right inactive</p>
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-6">
                        <button class="sliderButton">
                            <img class="buttonRight" src="<?php echo THEME_IMAGES; ?>/dropdown-arrow-black.svg" alt="image">
                        </button>
                        <p class="titleText">button right</p>
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-6">
                        <button class="sliderButton">
                            <img class="buttonLeft" src="<?php echo THEME_IMAGES; ?>/dropdown-arrow-black.svg" alt="image">
                        </button>
                        <p class="titleText">button left</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-5 col-md-5 col-sm-6 col-xs-12">
                <h5 class="titleText">close buttons</h5>
                <div class="verticalSpacing-md"></div>
                <div class="row">
                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                        <a class="closeButton">
                            <img class="blackClose" src="<?php echo THEME_IMAGES; ?>/close-black.svg" alt="image">
                        </a>
                        <p class="titleText">static</p>
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                        <a class="closeButton">
                            <img class="redClose" src="<?php echo THEME_IMAGES; ?>/close-careers-red.svg" alt="image">
                        </a>
                        <p class="titleText">static</p>
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                        <a class="closeButton">
                            <img class="pinkClose" src="<?php echo THEME_IMAGES; ?>/close-sales-pink.svg" alt="image">
                        </a>
                        <p class="titleText">static</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="container">
        <div class="row">
            <div class="dropdownButtons">
                <select name="variantSize" class="countries-form_select" tabindex="-1">
                    <option value="">all cities</option>
                    <option value="extra-small">melbourne</option>
                    <option value="small">sydney</option>
                    <option value="medium">brisbane</option>
                    <option value="medium">adelaide</option>
                    <option value="medium">perth</option>
                </select>

            </div>
        </div>
    </section>

<?php
if(get_field('footer_') == 'random') {
    $field = get_field_object('footer_');
    $index = rand(0,2);
    $i = 0;
    foreach ($field['choices'] as $key=>$option) {
        if ($i == $index) {
            $footer_style = $key;
            break;
        }
        $i++;
    }
}
else {
    $footer_style = get_field('footer_');
}
get_footer($footer_style);
?>