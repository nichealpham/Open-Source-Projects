<?php get_header(); ?>
<?php
if(is_category()){
    $category = get_queried_object();
    $style =  get_field('style', $category)?get_field('style', $category):'';
}
$style = (isset($style) && $style)?$style:'vertical';
if(!file_exists(THEME_DIR.'/content-archive-'.$style.'.php')){
    $style = 'vertical';
}
?>
    <main class="vertical-post">
        <section class="selectAria">
            <div class="container">
                <div class="row margin-space">
                    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12 chooseCity chooseOption colImages">
                        <?php
                        $category = get_category(get_query_var('cat'),false);
                        if($category->parent){
                            $category = get_category($category->parent);
                            while ($category->parent) {
                                $parent_cat = get_category($parent_cat->parent);
                            }
                        }
                        $categories = get_categories(
                            array( 'parent' => $category->cat_ID )
                        );
                        ?>
                        <?php if(count($categories)): ?>
                            <select data-style="<?php echo $style; ?>" id="filter-post">
                                <option value="<?php echo $category->term_id; ?>">All categories</option>
                                <?php foreach($categories as $cat): ?>
                                    <option value="<?php echo $cat->term_id; ?>"<?php if(get_query_var('cat') == $cat->term_id) echo "selected"; ?>><?php echo $cat->name; ?></option>
                                <?php endforeach; ?>
                            </select>
                        <?php endif; ?>
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-6 hidden-xs chooseCity chooseOption colImages">
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-6 hidden-xs chooseCity chooseOption colImages">
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-6 hidden-xs chooseCity chooseOption colImages">
                    </div>
                </div>
            </div>
            <div class="verticalSpacing-md"></div>
        </section>
        <?php
        get_template_part('content','archive-'.$style);
        ?>
    </main>
<?php get_footer(); ?>