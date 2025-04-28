<?php if (!defined('ABSPATH')) exit;

global $product;
$artist = get_post_meta($product->get_id(), '_tnv_artist', true);
$label = get_post_meta($product->get_id(), '_tnv_label', true);
$year = get_post_meta($product->get_id(), '_tnv_year', true);
$tracklist = get_post_meta($product->get_id(), '_tnv_tracklist', true);
?>

<div class="tnv-vinyl-details">
    <?php if ($artist || $label || $year): ?>
        <div class="tnv-vinyl-info">
            <?php if ($artist): ?>
                <div class="tnv-artist">
                    <strong><?php _e('Artist:', 'tonna-vinyla'); ?></strong>
                    <span><?php echo esc_html($artist); ?></span>
                </div>
            <?php endif; ?>

            <?php if ($label): ?>
                <div class="tnv-label">
                    <strong><?php _e('Label:', 'tonna-vinyla'); ?></strong>
                    <span><?php echo esc_html($label); ?></span>
                </div>
            <?php endif; ?>

            <?php if ($year): ?>
                <div class="tnv-year">
                    <strong><?php _e('Release Year:', 'tonna-vinyla'); ?></strong>
                    <span><?php echo esc_html($year); ?></span>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($tracklist)): ?>
        <div class="tnv-tracklist">
            <h3><?php _e('Tracklist', 'tonna-vinyla'); ?></h3>
            <table>
                <thead>
                    <tr>
                        <th><?php _e('Position', 'tonna-vinyla'); ?></th>
                        <th><?php _e('Title', 'tonna-vinyla'); ?></th>
                        <th><?php _e('Duration', 'tonna-vinyla'); ?></th>
                        <th><?php _e('Preview', 'tonna-vinyla'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($tracklist as $track): ?>
                        <tr>
                            <td><?php echo esc_html($track['position']); ?></td>
                            <td><?php echo esc_html($track['title']); ?></td>
                            <td><?php echo esc_html($track['duration']); ?></td>
                            <td>
                                <button type="button" 
                                        class="tnv-preview-track" 
                                        data-artist="<?php echo esc_attr($artist); ?>"
                                        data-track="<?php echo esc_attr($track['title']); ?>">
                                    <?php _e('Play', 'tonna-vinyla'); ?>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>