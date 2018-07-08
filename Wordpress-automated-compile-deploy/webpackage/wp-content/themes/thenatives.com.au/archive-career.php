<?php global $thenatives; ?>
<?php get_header ();?>
    <main id="template-careers" class="careers-page">
        <div class="main-header">
            <section class="selectAria">
                <div class="container">
                    <div class="row margin-space">
                        <?php
                        $cities = get_terms( array(
                            'taxonomy' => 'career-cities',
                            'hide_empty' => true,
                        ) );
                        ?>
                        <div class="col-lg-3 col-md-3 col-sm-6 hidden-xs chooseCity chooseOption colImages">
                            <select name="city">
                                <option value="">all cities</option>
                                <?php foreach($cities as $city): ?>
                                    <option value="<?php echo $city->term_id; ?>"><?php echo $city->name; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <?php
                        $levels = get_terms( array(
                            'taxonomy' => 'career-levels',
                            'hide_empty' => true,
                        ) );
                        ?>
                        <div class="col-lg-3 col-md-3 col-sm-6 hidden-xs chooseSeniority chooseOption colImages">
                            <select name="level">
                                <option value="">All Levels</option>
                                <?php foreach($levels as $level): ?>
                                    <option value="<?php echo $level->term_id; ?>"><?php echo $level->name; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <?php
                        $types = get_terms( array(
                            'taxonomy' => 'career-types',
                            'hide_empty' => true,
                        ) );
                        ?>
                        <div class="col-lg-3 col-md-3 col-sm-6 hidden-xs chooseEmployment chooseOption colImages">
                            <select name="type">
                                <option value="">All Types</option>
                                <?php foreach($types as $type): ?>
                                    <option value="<?php echo $type->term_id; ?>"><?php echo $type->name; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="hidden-lg hidden-md hidden-sm col-xs-6 filter colImages">
                            <button class="filterButton">
                                Filters
                            </button>
                            <div class="wrapperFilterItem">
                                <div class="headerFilter">
                                    <h3>filters</h3>
                                    <a class="closeButton">
                                        <img class="redClose" src="<?php echo THEME_IMAGES; ?>/close-careers-red.svg" alt="image">
                                    </a>
                                </div>
                                <div class="bodyFilter">
                                    <div class="itemFilter">
                                        <label class="labelSelect">location</label>
                                        <select name="city">
                                            <option value="">all cities</option>
                                            <?php foreach($cities as $city): ?>
                                                <option value="<?php echo $city->term_id; ?>"><?php echo $city->name; ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="itemFilter">
                                        <label class="labelSelect">seniority</label>
                                        <select name="level">
                                            <option value="">All Levels</option>
                                            <?php foreach($levels as $level): ?>
                                                <option value="<?php echo $level->term_id; ?>"><?php echo $level->name; ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="itemFilter">
                                        <label class="labelSelect">employment type</label>
                                        <select name="type">
                                            <option value="">All Types</option>
                                            <?php foreach($types as $type): ?>
                                                <option value="<?php echo $type->term_id; ?>"><?php echo $type->name; ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-6 postAJob colImages">
                            <a class="postAJobButton" href="<?php the_post_job_link(); ?>">
                                post a JOB
                                <span>+</span>
                                <span class="bgColor"></span>
                            </a>
                        </div>
                    </div>
                </div>
            </section>

            <section class="loginName">
                <div class="container">
                    <div class="row">
                        <div class="loginUser col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <?php if(!is_user_logged_in()): ?>
                            <a class="linkLoginUser" href="<?php the_login_link(); ?>">
                                <h4 class="titleGrey">EMPLOYER LOGIN</h4>

                                <div class="iconUser">
                                    <div class="head-icon"></div>
                                    <div class="body-icon"></div>
                                </div>
                            </a>
                            <?php else: ?>
                            <!--<a class="linkLoginUser" href="<?php /*the_user_dashboard_link(); */?>">
                                <div class="iconUser">
                                    <div class="head-icon"></div>
                                    <div class="body-icon"></div>
                                </div>
                            </a>-->
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </section>
        </div>
        <div class="main-body">
            <?php get_template_part('body-archive-career'); ?>
        </div>
    </main>
<?php get_footer ();?>