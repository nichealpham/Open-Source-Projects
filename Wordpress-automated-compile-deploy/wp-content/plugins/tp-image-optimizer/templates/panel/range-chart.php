<div class="data-chart">
    <label class='title tp-tooltip tp-tooltip-right' >
        <?php echo esc_html__("Stastics of your library", 'tp-image-optimizer'); ?>
        <span class="faq-local-statistics"></span>
    </label>
    <div class='filter-chart'>
        <?php
        foreach ($data_range as $key => $range):
            $checked = '';
            if ($key == $option_range) {
                $checked = 'checked';
            }
            ?>
            <input type='radio' name='select-range' class="select-range" id='<?php echo $key; ?>' <?php echo $checked; ?> value="<?php echo $key; ?>" data-chart="<?php echo $key; ?>" data-title="<?php echo $range; ?>">
            <label for='<?php echo $key; ?>' data-chart='<?php echo $key; ?>' class="select-range">
                <?php echo $range; ?>
            </label>
        <?php endforeach; ?>
    </div>
    <div class="tp-element ">
        <div class='images-chart'>
            <div class="chartPreload" >
                <div class="loader">
                    <div class="cssload-loader">
                        <div></div>
                        <div></div>
                        <div></div>
                        <div></div>
                        <div></div>
                    </div>
                </div>
            </div>
            <div class="chart-container" style="position: relative; width:100%">
            <canvas id="dataChart" width="800" height="600"></canvas>
            </div>
        </div>
    </div>
</div>