<div class="instashow-demo">
    <form class="instashow-demo-form">
        <input type="hidden" name="api" value="<?php echo $preferences_custom_api_url; ?>">
        <input class="instashow-demo-result" type="hidden" name="options" value="">
        
        <div class="instashow-demo-accordion">
            <div class="instashow-demo-accordion-item instashow-demo-accordion-item-active">
                <div class="instashow-demo-accordion-item-trigger"><?php _e('Source', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></div>

                <div class="instashow-demo-accordion-item-content">
                    <div class="instashow-demo-source instashow-demo-field-group">
                        <div class="instashow-demo-field-group-name"><?php _e('Instagram sources', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></div>

                        <div class="instashow-demo-field-group-description">
                            <?php _e('Set any combination of @username, #hashtag, location or post URL.', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?>
                        </div>

                        <div class="instashow-demo-field">
                            <input type="hidden" class="instashow-demo-tags" name="source[]" placeholder="<?php _e('add a source', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?>">
                        </div>
                    </div>

                    <div class="instashow-demo-filter-only instashow-demo-field-group">
                        <div class="instashow-demo-field-group-name"><?php _e('Filter only', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></div>

                        <div class="instashow-demo-field-group-description">
                            <?php _e('It allows to filter posts by @username, #hashtag, location or post URL.', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?>
                        </div>

                        <div class="instashow-demo-field">
                            <input type="hidden" class="instashow-demo-tags" name="filterOnly[]" placeholder="<?php _e('add a source', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?>">
                        </div>
                    </div>

                    <div class="instashow-demo-filter-except instashow-demo-field-group">
                        <div class="instashow-demo-field-group-name"><?php _e('Filter except', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></div>

                        <div class="instashow-demo-field-group-description">
                            <?php _e('It allows to exclude specific posts by URL or posts which contain the specified hashtags or which refers to the certain authors or locations.', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?>
                        </div>

                        <div class="instashow-demo-field">
                            <input type="hidden" class="instashow-demo-tags" name="filterExcept[]" placeholder="<?php _e('add a source', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?>">
                        </div>
                    </div>

                    <div class="instashow-demo-field-group">
                        <div class="instashow-demo-limit instashow-demo-field">
                            <div class="instashow-demo-field-name-inline instashow-demo-field-name"><?php _e('Limit posts', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></div>

                            <div class="instashow-demo-numeric" data-min="0">
                                <div class="instashow-demo-numeric-decrease"></div>
                                <input type="text" name="limit" autocomplete="off">
                                <div class="instashow-demo-numeric-increase"></div>
                            </div>

                            <span class="instashow-demo-field-hint"><?php _e('set "0" to show all posts', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></span>
                        </div>
                    </div>

                    <div class="instashow-demo-field-group">
                        <div class="instashow-demo-cache instashow-demo-field">
                            <div class="instashow-demo-field-name-inline instashow-demo-field-name"><?php _e('Cache media time', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></div>

                            <span class="instashow-demo-range-container">
                                <input class="instashow-demo-range-input" type="text" name="cacheMediaTime">
                                <span class="instashow-demo-range" data-min="0" data-step="100" data-max="86400"></span>
                            </span>

                            <span class="instashow-demo-field-hint"><?php _e('s', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="instashow-demo-accordion-item">
                <div class="instashow-demo-accordion-item-trigger"><?php _e('Sizes', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></div>

                <div class="instashow-demo-accordion-item-content">
                    <div class="instashow-demo-field-group">
                        <div class="instashow-demo-field-group-name">
                            <?php _e('Widget size', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?>
                        </div>

                        <div class="instashow-demo-field">
                            <label class="instashow-demo-width">
                                <span class="instashow-demo-field-name-inline instashow-demo-field-name"><?php _e('Width', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></span>

                                <span class="instashow-demo-range-container">
                                    <input type="hidden" name="width" value="auto">
                                    <input class="instashow-demo-range-input" type="text" name="width">
                                    <span class="instashow-demo-range" data-min="100" data-step="10" data-max="2580"></span>
                                </span>
                            </label>

                            <label class="instashow-demo-width-auto">
                                <input class="instashow-demo-checkbox" type="checkbox" name="width_auto" value="true">
                                <span class="instashow-demo-checkbox-label"><?php _e('Responsive', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></span>
                            </label>
                        </div>

                        <div class="instashow-demo-field">
                            <label class="instashow-demo-height">
                                <span class="instashow-demo-field-name-inline instashow-demo-field-name"><?php _e('Height', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></span>

                                <span class="instashow-demo-range-container">
                                    <input type="hidden" name="height" value="auto">
                                    <input class="instashow-demo-range-input" type="text" name="height">
                                    <span class="instashow-demo-range" data-min="100" data-step="10" data-max="2000"></span>
                                </span>
                            </label>

                            <label class="instashow-demo-height-auto">
                                <input class="instashow-demo-checkbox" type="checkbox" name="height_auto" value="true">
                                <span class="instashow-demo-checkbox-label"><?php _e('By content', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></span>
                            </label>
                        </div>
                    </div>

                    <div class="instashow-demo-field-group">
                        <div class="instashow-demo-field-group-name">
                            <?php _e('Grid', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?>
                        </div>

                        <div class="instashow-demo-columns instashow-demo-field">
                            <label>
                                <span class="instashow-demo-field-name-inline instashow-demo-field-name"><?php _e('Columns', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></span>

                                <div class="instashow-demo-numeric" data-min="1">
                                    <div class="instashow-demo-numeric-decrease"></div>
                                    <input type="text" name="columns" autocomplete="off">
                                    <div class="instashow-demo-numeric-increase"></div>
                                </div>
                            </label>
                        </div>

                        <div class="instashow-demo-rows instashow-demo-field">
                            <label>
                                <span class="instashow-demo-field-name-inline instashow-demo-field-name"><?php _e('Rows', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></span>

                                <div class="instashow-demo-numeric" data-min="1">
                                    <div class="instashow-demo-numeric-decrease"></div>
                                    <input type="text" name="rows" autocomplete="off">
                                    <div class="instashow-demo-numeric-increase"></div>
                                </div>
                            </label>
                        </div>

                        <div class="instashow-demo-gutter instashow-demo-field">
                            <label>
                                <span class="instashow-demo-field-name-inline instashow-demo-field-name"><?php _e('Gutter', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></span>

                                <span class="instashow-demo-range-container">
                                    <input class="instashow-demo-range-input" type="text" name="gutter">
                                    <span class="instashow-demo-range" data-min="0" data-max="200"></span>
                                </span>

                                <span class="instashow-demo-field-hint"><?php _e('px', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></span>
                            </label>
                        </div>
                    </div>

                    <div class="instashow-demo-responsive instashow-demo-field-group">
                        <input type="hidden" name="responsive" value="">

                        <div class="instashow-demo-field-group-name">
                            <?php _e('Responsive breakpoints', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?>
                        </div>

                        <div class="instashow-demo-field-group-description">
                            <?php _e('Specify the breakpoints to set the columns, rows and gutter in the grid depending on a window width.', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?>
                        </div>

                        <div class="instashow-demo-responsive-items">
                            <template class="instashow-demo-template-responsive-item instashow-demo-template">
                                <div class="instashow-demo-responsive-item">
                                    <div class="instashow-demo-responsive-item-remove"></div>

                                    <div class="instashow-demo-field">
                                        <label>
                                            <span class="instashow-demo-field-name-inline instashow-demo-field-name"><?php _e('Window width', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></span>

                                            <span class="instashow-demo-range-container">
                                                <input class="instashow-demo-range-input" type="text" name="responsiveWindowWidth">
                                                <span class="instashow-demo-range" data-min="100" data-step="10" data-max="2580"></span>
                                            </span>

                                            <span class="instashow-demo-field-hint"><?php _e('px', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></span>
                                        </label>
                                    </div>

                                    <div class="instashow-demo-responsive-item-columns instashow-demo-field">
                                        <label>
                                            <span class="instashow-demo-field-name-inline instashow-demo-field-name"><?php _e('Columns', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></span>

                                            <div class="instashow-demo-numeric" data-min="1">
                                                <div class="instashow-demo-numeric-decrease"></div>
                                                <input type="text" name="responsiveColumns" autocomplete="off">
                                                <div class="instashow-demo-numeric-increase"></div>
                                            </div>
                                        </label>
                                    </div>

                                    <div class="instashow-demo-responsive-item-rows instashow-demo-field">
                                        <label>
                                            <span class="instashow-demo-field-name-inline instashow-demo-field-name"><?php _e('Rows', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></span>

                                            <div class="instashow-demo-numeric" data-min="1">
                                                <div class="instashow-demo-numeric-decrease"></div>
                                                <input type="text" name="responsiveRows" autocomplete="off">
                                                <div class="instashow-demo-numeric-increase"></div>
                                            </div>
                                        </label>
                                    </div>

                                    <div class="instashow-demo-responsive-item-gutter instashow-demo-field">
                                        <label>
                                            <span class="instashow-demo-field-name-inline instashow-demo-field-name"><?php _e('Gutter', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></span>

                                            <span class="instashow-demo-range-container">
                                                <input class="instashow-demo-range-input" type="text" name="responsiveGutter">
                                                <span class="instashow-demo-range" data-min="0" data-max="200"></span>
                                            </span>

                                            <span class="instashow-demo-field-hint"><?php _e('px', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></span>
                                        </label>
                                    </div>
                                </div>
                            </template>
                        </div>

                        <button class="instashow-demo-responsive-add-item">
                            <span class="instashow-demo-icon-plus instashow-demo-icon"></span>
                            <span class="instashow-demo-responsive-add-item-label"><?php _e('Add breakpoint', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></span>
                        </button>
                    </div>
                </div>
            </div>

            <div class="instashow-demo-accordion-item">
                <div class="instashow-demo-accordion-item-trigger"><?php _e('UI', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></div>

                <div class="instashow-demo-accordion-item-content">
                    <div class="instashow-demo-field-group">
                        <div class="instashow-demo-controls instashow-demo-field-col-3-4">
                            <div class="instashow-demo-field">
                                <div class="instashow-demo-field-name">
                                    <?php _e('Controls', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?>
                                </div>

                                <label class="instashow-demo-controls-item">
                                    <input type="hidden" name="arrowsControl" value="false">
                                    <input class="instashow-demo-checkbox" type="checkbox" name="arrowsControl" value="true">
                                    <span class="instashow-demo-icon-control-arrows-white instashow-demo-icon-active instashow-demo-icon"></span>
                                    <span class="instashow-demo-icon-control-arrows-blue instashow-demo-icon"></span>
                                    <span class="instashow-demo-controls-item-label"><?php _e('Arrows', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></span>
                                </label>

                                <label class="instashow-demo-controls-item">
                                    <input type="hidden" name="scrollControl" value="false">
                                    <input class="instashow-demo-checkbox" type="checkbox" name="scrollControl" value="true">
                                    <span class="instashow-demo-icon-control-scroll-blue instashow-demo-icon"></span>
                                    <span class="instashow-demo-icon-control-scroll-white instashow-demo-icon-active instashow-demo-icon"></span>
                                    <span class="instashow-demo-controls-item-label"><?php _e('Scroll', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></span>
                                </label>

                                <label class="instashow-demo-controls-item">
                                    <input type="hidden" name="dragControl" value="false">
                                    <input class="instashow-demo-checkbox" type="checkbox" name="dragControl" value="true">
                                    <span class="instashow-demo-icon-control-drag-white instashow-demo-icon-active instashow-demo-icon"></span>
                                    <span class="instashow-demo-icon-control-drag-blue instashow-demo-icon"></span>
                                    <span class="instashow-demo-controls-item-label"><?php _e('Drag', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></span>
                                </label>
                            </div>
                        </div>

                        <div class="instashow-demo-field-col-1-4">
                            <div class="instashow-demo-free-mode instashow-demo-field">
                                <label>
                                    <input type="hidden" name="freeMode" value="false">
                                    <input class="instashow-demo-checkbox" type="checkbox" name="freeMode" value="true">
                                    <span class="instashow-demo-checkbox-label"><?php _e('Free mode', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></span>
                                </label>
                            </div>

                            <div class="instashow-demo-loop instashow-demo-field">
                                <label>
                                    <input type="hidden" name="loop" value="false">
                                    <input class="instashow-demo-checkbox" type="checkbox" name="loop" value="true">
                                    <span class="instashow-demo-checkbox-label"><?php _e('Loop', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></span>
                                </label>
                            </div>

                            <div class="instashow-demo-field">
                                <label>
                                    <input type="hidden" name="scrollbar" value="false">
                                    <input class="instashow-demo-checkbox" type="checkbox" name="scrollbar" value="true">
                                    <span class="instashow-demo-checkbox-label"><?php _e('Scrollbar', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="instashow-demo-field-group">
                        <div class="instashow-demo-field-col-1-2">
                            <div class="instashow-demo-field">
                                <div class="instashow-demo-field-name"><?php _e('Direction', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></div>

                                <div class="instashow-demo-multiswitch">
                                    <label class="instashow-demo-multiswitch-item">
                                        <input type="radio" name="direction" value="horizontal" checked>
                                        <span class="instashow-demo-icon-direction-horizontal-blue instashow-demo-icon"></span>
                                        <span class="instashow-demo-icon-direction-horizontal-white instashow-demo-icon-active instashow-demo-icon"></span>
                                        <span class="instashow-demo-multiswitch-item-label"><?php _e('Horizontal', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></span>
                                    </label>

                                    <label class="instashow-demo-multiswitch-item">
                                        <input type="radio" name="direction" value="vertical">
                                        <span class="instashow-demo-icon-direction-vertical-blue instashow-demo-icon"></span>
                                        <span class="instashow-demo-icon-direction-vertical-white instashow-demo-icon-active instashow-demo-icon"></span>
                                        <span class="instashow-demo-multiswitch-item-label"><?php _e('Vertical', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="instashow-demo-field-col-1-2">
                            <div class="instashow-demo-auto instashow-demo-field">
                                <div class="instashow-demo-field-name"><?php _e('Autorotation', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></div>

                                <span class="instashow-demo-range-container">
                                    <input class="instashow-demo-range-input" type="text" name="auto">
                                    <span class="instashow-demo-range" data-min="0" data-step="100" data-max="10000"></span>
                                </span>

                                <span class="instashow-demo-field-hint"><?php _e('ms', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></span>
                            </div>

                            <div class="instashow-demo-auto-hover-pause instashow-demo-field">
                                <label>
                                    <input type="hidden" name="autoHoverPause" value="false">
                                    <input class="instashow-demo-checkbox" type="checkbox" name="autoHoverPause" value="true">
                                    <span class="instashow-demo-checkbox-label"><?php _e('Pause on hover', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="instashow-demo-field-group">
                        <div class="instashow-demo-field-col-1-2">
                            <div class="instashow-demo-field">
                                <div class="instashow-demo-field-name"><?php _e('Animation effect', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></div>

                                <div class="instashow-demo-multiswitch">
                                    <label class="instashow-demo-multiswitch-item">
                                        <input type="radio" name="effect" value="slide" checked>
                                        <span class="instashow-demo-multiswitch-item-label"><?php _e('Slide', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></span>
                                    </label>

                                    <label class="instashow-demo-multiswitch-item">
                                        <input type="radio" name="effect" value="fade">
                                        <span class="instashow-demo-multiswitch-item-label"><?php _e('Fade', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="instashow-demo-field-col-1-2">
                            <div class="instashow-demo-speed instashow-demo-field">
                                <div class="instashow-demo-field-name"><?php _e('Animation speed', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></div>

                                <span class="instashow-demo-range-container">
                                    <input class="instashow-demo-range-input" type="text" name="speed">
                                    <span class="instashow-demo-range" data-min="0" data-step="100" data-max="3000"></span>
                                </span>

                                <span class="instashow-demo-field-hint"><?php _e('ms', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></span>
                            </div>

                            <div class="instashow-demo-easing instashow-demo-field">
                                <div class="instashow-demo-field-name-inline instashow-demo-field-name"><?php _e('Easing', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></div>

                                <select class="instashow-demo-select" name="easing">
                                    <option value="linear"><?php _e('linear', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></option>
                                    <option value="ease" selected><?php _e('ease', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></option>
                                    <option value="ease-in"><?php _e('ease-in', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></option>
                                    <option value="ease-out"><?php _e('ease-out', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></option>
                                    <option value="ease-in-out"><?php _e('ease-in-out', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="instashow-demo-field-group">
                        <div class="instashow-demo-popup-deep-linking instashow-demo-field">
                            <label>
                                <input type="hidden" name="popupDeepLinking" value="false">
                                <input class="instashow-demo-checkbox" type="checkbox" name="popupDeepLinking" value="true">
                                <span class="instashow-demo-checkbox-label"><?php _e('Popup deep linking', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></span>
                            </label>
                        </div>

                        <div class="instashow-demo-field">
                            <span class="instashow-demo-field-name-inline instashow-demo-field-name"><?php _e('Popup image switch speed', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></span>

                            <span class="instashow-demo-range-container">
                                <input class="instashow-demo-range-input" type="text" name="popupSpeed">
                                <span class="instashow-demo-range" data-min="0" data-step="100" data-max="3000"></span>
                            </span>

                            <span class="instashow-demo-field-hint"><?php _e('ms', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></span>
                        </div>

                        <div class="instashow-demo-popup-easing instashow-demo-field">
                            <span class="instashow-demo-field-name-inline instashow-demo-field-name"><?php _e('Popup image switch easing', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></span>

                            <select class="instashow-demo-select" name="popupEasing">
                                <option value="linear"><?php _e('linear', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></option>
                                <option value="ease" selected><?php _e('ease', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></option>
                                <option value="ease-in"><?php _e('ease-in', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></option>
                                <option value="ease-out"><?php _e('ease-out', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></option>
                                <option value="ease-in-out"><?php _e('ease-in-out', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></option>
                            </select>
                        </div>
                    </div>

                    <div class="instashow-demo-field-group">
                        <div class="instashow-demo-field instashow-demo-lang">
                            <span class="instashow-demo-field-name-inline instashow-demo-field-name"><?php _e('Language', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></span>

                            <select class="instashow-demo-select-language instashow-demo-select" name="lang">
                                <option value="de"><?php _e('Deutsch', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></option>
                                <option value="en" selected><?php _e('English', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></option>
                                <option value="es"><?php _e('Espa&ntilde;ol', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></option>
                                <option value="fr"><?php _e('Fran&ccedil;ais', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></option>
                                <option value="it"><?php _e('Italiano', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></option>
                                <option value="nl"><?php _e('Nederlands', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></option>
                                <option value="no"><?php _e('Norsk', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></option>
                                <option value="pl"><?php _e('Polski', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></option>
                                <option value="pt-BR"><?php _e('Portugu&ecirc;s', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></option>
                                <option value="sv"><?php _e('Svenska', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></option>
                                <option value="tr"><?php _e('T&uuml;rk&ccedil;e', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></option>
                                <option value="ru"><?php _e('&#x0420;&#x0443;&#x0441;&#x0441;&#x043a;&#x0438;&#x0439;', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></option>
                                <option value="hi"><?php _e('&#x939;&#x93F;&#x928;&#x94D;&#x926;&#x940;', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></option>
                                <option value="ko"><?php _e('&#xd55c;&#xad6d;&#xc758;', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></option>
                                <option value="zh-HK"><?php _e('&#x4e2d;&#x6587;', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></option>
                                <option value="ja"><?php _e('&#x65e5;&#x672c;&#x8a9e;', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></option>
                            </select>
                        </div>
                    </div>

                    <div class="instashow-demo-field-group">
                        <div class="instashow-demo-field instashow-demo-mode">
                            <span class="instashow-demo-field-name-inline instashow-demo-field-name"><?php _e('Mode', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></span>

                            <select class="instashow-demo-select-mode instashow-demo-select" name="mode">
                                <option value="popup" selected><?php _e('Popup', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></option>
                                <option value="instagram"><?php _e('Instagram', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="instashow-demo-accordion-item">
                <div class="instashow-demo-accordion-item-trigger"><?php _e('Info', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></div>

                <div class="instashow-demo-accordion-item-content">
                    <div class="instashow-demo-field-group">
                        <div class="instashow-demo-field-group-name"><?php _e('Gallery Info', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></div>
                        <input type="hidden" name="info" value="">

                        <div class="instashow-demo-field">
                            <label>
                                <input class="instashow-demo-checkbox" type="checkbox" name="info[]" value="likesCounter">
                                <span class="instashow-demo-checkbox-label"><?php _e('Likes counter', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></span>
                            </label>
                        </div>

                        <div class="instashow-demo-field">
                            <label>
                                <input class="instashow-demo-checkbox" type="checkbox" name="info[]" value="commentsCounter">
                                <span class="instashow-demo-checkbox-label"><?php _e('Comments counter', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></span>
                            </label>
                        </div>

                        <div class="instashow-demo-field">
                            <label>
                                <input class="instashow-demo-checkbox" type="checkbox" name="info[]" value="description">
                                <span class="instashow-demo-checkbox-label"><?php _e('Description', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></span>
                            </label>
                        </div>
                    </div>

                    <div class="instashow-demo-field-group">
                        <div class="instashow-demo-field-group-name"><?php _e('Popup Info', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></div>
                        <input type="hidden" name="popupInfo" value="">

                        <div class="instashow-demo-field">
                            <label>
                                <input class="instashow-demo-checkbox" type="checkbox" name="popupInfo[]" value="username">
                                <span class="instashow-demo-checkbox-label"><?php _e('Username', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></span>
                            </label>
                        </div>

                        <div class="instashow-demo-field">
                            <label>
                                <input class="instashow-demo-checkbox" type="checkbox" name="popupInfo[]" value="instagramLink">
                                <span class="instashow-demo-checkbox-label"><?php _e('Instagram Link', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></span>
                            </label>
                        </div>

                        <div class="instashow-demo-field">
                            <label>
                                <input class="instashow-demo-checkbox" type="checkbox" name="popupInfo[]" value="likesCounter">
                                <span class="instashow-demo-checkbox-label"><?php _e('Likes counter', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></span>
                            </label>
                        </div>

                        <div class="instashow-demo-field">
                            <label>
                                <input class="instashow-demo-checkbox" type="checkbox" name="popupInfo[]" value="commentsCounter">
                                <span class="instashow-demo-checkbox-label"><?php _e('Comments counter', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></span>
                            </label>
                        </div>

                        <div class="instashow-demo-field">
                            <label>
                                <input class="instashow-demo-checkbox" type="checkbox" name="popupInfo[]" value="location">
                                <span class="instashow-demo-checkbox-label"><?php _e('Location', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></span>
                            </label>
                        </div>

                        <div class="instashow-demo-field">
                            <label>
                                <input class="instashow-demo-checkbox" type="checkbox" name="popupInfo[]" value="passedTime">
                                <span class="instashow-demo-checkbox-label"><?php _e('Passed time', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></span>
                            </label>
                        </div>

                        <div class="instashow-demo-field">
                            <label>
                                <input class="instashow-demo-checkbox" type="checkbox" name="popupInfo[]" value="description">
                                <span class="instashow-demo-checkbox-label"><?php _e('Description', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></span>
                            </label>
                        </div>

                        <div class="instashow-demo-field">
                            <label>
                                <input class="instashow-demo-checkbox" type="checkbox" name="popupInfo[]" value="comments">
                                <span class="instashow-demo-checkbox-label"><?php _e('Comments', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="instashow-demo-accordion-item">
                <div class="instashow-demo-accordion-item-trigger"><?php _e('Style', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></div>

                <div class="instashow-demo-accordion-item-content">
                    <div class="instashow-demo-field-group">
                        <div class="instashow-demo-color-scheme instashow-demo-field">
                            <span class="instashow-demo-field-name-inline instashow-demo-field-name"><?php _e('Color scheme', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></span>

                            <select class="instashow-demo-select"></select>
                        </div>
                    </div>
                    
                    <div class="instashow-demo-colors">
                        <div class="instashow-demo-field-group">
                            <div class="instashow-demo-field-group-name"><?php _e('Gallery Colors', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></div>

                            <label class="instashow-demo-field">
                                <input class="instashow-demo-colorpicker" type="text" name="colorGalleryBg">
                                <span class="instashow-demo-colorpicker-label instashow-demo-field-name-inline instashow-demo-field-name"><?php _e('Background', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></span>
                            </label>

                            <label class="instashow-demo-field">
                                <input class="instashow-demo-colorpicker" type="text" name="colorGalleryCounters">
                                <span class="instashow-demo-colorpicker-label instashow-demo-field-name-inline instashow-demo-field-name"><?php _e('Counters', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></span>
                            </label>

                            <label class="instashow-demo-field">
                                <input class="instashow-demo-colorpicker" type="text" name="colorGalleryDescription">
                                <span class="instashow-demo-colorpicker-label instashow-demo-field-name-inline instashow-demo-field-name"><?php _e('Description', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></span>
                            </label>

                            <label class="instashow-demo-field">
                                <input class="instashow-demo-colorpicker" type="text" name="colorGalleryOverlay">
                                <span class="instashow-demo-colorpicker-label instashow-demo-field-name-inline instashow-demo-field-name"><?php _e('Overlay', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></span>
                            </label>

                            <label class="instashow-demo-field">
                                <input class="instashow-demo-colorpicker" type="text" name="colorGalleryArrows">
                                <span class="instashow-demo-colorpicker-label instashow-demo-field-name-inline instashow-demo-field-name"><?php _e('Arrows', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></span>
                            </label>

                            <label class="instashow-demo-field">
                                <input class="instashow-demo-colorpicker" type="text" name="colorGalleryArrowsHover">
                                <span class="instashow-demo-colorpicker-label instashow-demo-field-name-inline instashow-demo-field-name"><?php _e('Arrows on hover', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></span>
                            </label>

                            <label class="instashow-demo-field">
                                <input class="instashow-demo-colorpicker" type="text" name="colorGalleryArrowsBg">
                                <span class="instashow-demo-colorpicker-label instashow-demo-field-name-inline instashow-demo-field-name"><?php _e('Arrows background', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></span>
                            </label>

                            <label class="instashow-demo-field">
                                <input class="instashow-demo-colorpicker" type="text" name="colorGalleryArrowsBgHover">
                                <span class="instashow-demo-colorpicker-label instashow-demo-field-name-inline instashow-demo-field-name"><?php _e('Arrows bg on hover', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></span>
                            </label>

                            <label class="instashow-demo-field">
                                <input class="instashow-demo-colorpicker" type="text" name="colorGalleryScrollbar">
                                <span class="instashow-demo-colorpicker-label instashow-demo-field-name-inline instashow-demo-field-name"><?php _e('Scrollbar', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></span>
                            </label>

                            <label class="instashow-demo-field">
                                <input class="instashow-demo-colorpicker" type="text" name="colorGalleryScrollbarSlider">
                                <span class="instashow-demo-colorpicker-label instashow-demo-field-name-inline instashow-demo-field-name"><?php _e('Scrollbar slider', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></span>
                            </label>
                        </div>

                        <div class="instashow-demo-field-group">
                            <div class="instashow-demo-field-group-name"><?php _e('Popup Colors', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></div>

                            <label class="instashow-demo-field">
                                <input class="instashow-demo-colorpicker" type="text" name="colorPopupOverlay">
                                <span class="instashow-demo-colorpicker-label instashow-demo-field-name-inline instashow-demo-field-name"><?php _e('Overlay', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></span>
                            </label>

                            <label class="instashow-demo-field">
                                <input class="instashow-demo-colorpicker" type="text" name="colorPopupBg">
                                <span class="instashow-demo-colorpicker-label instashow-demo-field-name-inline instashow-demo-field-name"><?php _e('Background', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></span>
                            </label>

                            <label class="instashow-demo-field">
                                <input class="instashow-demo-colorpicker" type="text" name="colorPopupUsername">
                                <span class="instashow-demo-colorpicker-label instashow-demo-field-name-inline instashow-demo-field-name"><?php _e('Username', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></span>
                            </label>

                            <label class="instashow-demo-field">
                                <input class="instashow-demo-colorpicker" type="text" name="colorPopupUsernameHover">
                                <span class="instashow-demo-colorpicker-label instashow-demo-field-name-inline instashow-demo-field-name"><?php _e('Username on hover', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></span>
                            </label>

                            <label class="instashow-demo-field">
                                <input class="instashow-demo-colorpicker" type="text" name="colorPopupInstagramLink">
                                <span class="instashow-demo-colorpicker-label instashow-demo-field-name-inline instashow-demo-field-name"><?php _e('Instagram link', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></span>
                            </label>

                            <label class="instashow-demo-field">
                                <input class="instashow-demo-colorpicker" type="text" name="colorPopupInstagramLinkHover">
                                <span class="instashow-demo-colorpicker-label instashow-demo-field-name-inline instashow-demo-field-name"><?php _e('Instagram link on hover', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></span>
                            </label>

                            <label class="instashow-demo-field">
                                <input class="instashow-demo-colorpicker" type="text" name="colorPopupCounters">
                                <span class="instashow-demo-colorpicker-label instashow-demo-field-name-inline instashow-demo-field-name"><?php _e('Counters', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></span>
                            </label>

                            <label class="instashow-demo-field">
                                <input class="instashow-demo-colorpicker" type="text" name="colorPopupPassedTime">
                                <span class="instashow-demo-colorpicker-label instashow-demo-field-name-inline instashow-demo-field-name"><?php _e('Passed time', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></span>
                            </label>

                            <label class="instashow-demo-field">
                                <input class="instashow-demo-colorpicker" type="text" name="colorPopupAnchor">
                                <span class="instashow-demo-colorpicker-label instashow-demo-field-name-inline instashow-demo-field-name"><?php _e('Anchor', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></span>
                            </label>

                            <label class="instashow-demo-field">
                                <input class="instashow-demo-colorpicker" type="text" name="colorPopupAnchorHover">
                                <span class="instashow-demo-colorpicker-label instashow-demo-field-name-inline instashow-demo-field-name"><?php _e('Anchor on hover', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></span>
                            </label>

                            <label class="instashow-demo-field">
                                <input class="instashow-demo-colorpicker" type="text" name="colorPopupText">
                                <span class="instashow-demo-colorpicker-label instashow-demo-field-name-inline instashow-demo-field-name"><?php _e('Text', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></span>
                            </label>

                            <label class="instashow-demo-field">
                                <input class="instashow-demo-colorpicker" type="text" name="colorPopupControls">
                                <span class="instashow-demo-colorpicker-label instashow-demo-field-name-inline instashow-demo-field-name"><?php _e('Controls', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></span>
                            </label>

                            <label class="instashow-demo-field">
                                <input class="instashow-demo-colorpicker" type="text" name="colorPopupControlsHover">
                                <span class="instashow-demo-colorpicker-label instashow-demo-field-name-inline instashow-demo-field-name"><?php _e('Controls on hover', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></span>
                            </label>

                            <label class="instashow-demo-field">
                                <input class="instashow-demo-colorpicker" type="text" name="colorPopupMobileControls">
                                <span class="instashow-demo-colorpicker-label instashow-demo-field-name-inline instashow-demo-field-name"><?php _e('Mobile controls', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></span>
                            </label>

                            <label class="instashow-demo-field">
                                <input class="instashow-demo-colorpicker" type="text" name="colorPopupMobileControlsBg">
                                <span class="instashow-demo-colorpicker-label instashow-demo-field-name-inline instashow-demo-field-name"><?php _e('Mobile controls bg', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <div class="instashow-demo-preview-container">
        <div class="instashow-demo-preview"></div>
        <div class="instashow-demo-preview-clone"></div>
    </div>
</div>