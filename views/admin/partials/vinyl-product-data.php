<?php if (!defined('ABSPATH')) exit; ?>

<div id="vinyl_product_data" class="panel woocommerce_options_panel">
    <div class="options_group">
        <p class="form-field">
            <label for="tnv_artist"><?php _e('Artist', 'tonna-vinyla'); ?></label>
            <?php
            woocommerce_wp_text_input(array(
                'id' => '_tnv_artist',
                'label' => __('Artist', 'tonna-vinyla'),
                'desc_tip' => true,
                'description' => __('Enter the artist name', 'tonna-vinyla')
            ));
            ?>
        </p>

        <p class="form-field">
            <label for="tnv_label"><?php _e('Label', 'tonna-vinyla'); ?></label>
            <?php
            woocommerce_wp_text_input(array(
                'id' => '_tnv_label',
                'label' => __('Record Label', 'tonna-vinyla'),
                'desc_tip' => true,
                'description' => __('Enter the record label name', 'tonna-vinyla')
            ));
            ?>
        </p>

        <p class="form-field">
            <label for="tnv_year"><?php _e('Year', 'tonna-vinyla'); ?></label>
            <?php
            woocommerce_wp_text_input(array(
                'id' => '_tnv_year',
                'label' => __('Release Year', 'tonna-vinyla'),
                'desc_tip' => true,
                'description' => __('Enter the release year', 'tonna-vinyla'),
                'type' => 'number'
            ));
            ?>
        </p>
    </div>

    <div class="options_group">
        <h4><?php _e('Tracklist', 'tonna-vinyla'); ?></h4>
        <div id="tnv_tracklist_container">
            <?php
            $tracklist = get_post_meta($post->ID, '_tnv_tracklist', true);
            if (!empty($tracklist)): 
                foreach ($tracklist as $index => $track):
            ?>
                <div class="tnv-track-item">
                    <input type="text" 
                           name="tnv_tracklist[<?php echo $index; ?>][position]" 
                           value="<?php echo esc_attr($track['position']); ?>" 
                           placeholder="<?php _e('Position', 'tonna-vinyla'); ?>" 
                           class="tnv-track-position">
                    <input type="text" 
                           name="tnv_tracklist[<?php echo $index; ?>][title]" 
                           value="<?php echo esc_attr($track['title']); ?>" 
                           placeholder="<?php _e('Track Title', 'tonna-vinyla'); ?>" 
                           class="tnv-track-title">
                    <input type="text" 
                           name="tnv_tracklist[<?php echo $index; ?>][duration]" 
                           value="<?php echo esc_attr($track['duration']); ?>" 
                           placeholder="<?php _e('Duration', 'tonna-vinyla'); ?>" 
                           class="tnv-track-duration">
                    <button type="button" class="button tnv-remove-track"><?php _e('Remove', 'tonna-vinyla'); ?></button>
                </div>
            <?php
                endforeach;
            endif;
            ?>
        </div>
        <button type="button" class="button tnv-add-track"><?php _e('Add Track', 'tonna-vinyla'); ?></button>
    </div>

    <div class="options_group">
        <p class="form-field">
            <label for="tnv_scan_qr"><?php _e('QR Code', 'tonna-vinyla'); ?></label>
            <button type="button" class="button" id="tnv_scan_qr"><?php _e('Scan QR Code', 'tonna-vinyla'); ?></button>
            <span class="description"><?php _e('Scan QR code to import product data from Discogs', 'tonna-vinyla'); ?></span>
        </p>
        <div id="tnv_qr_scanner_container" style="display: none;"></div>
    </div>
</div>