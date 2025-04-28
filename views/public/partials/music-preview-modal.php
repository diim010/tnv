<?php if (!defined('ABSPATH')) exit; ?>

<div id="tnv-preview-modal" class="tnv-modal" style="display: none;">
    <div class="tnv-modal-content">
        <span class="tnv-modal-close">&times;</span>
        <h3 class="tnv-preview-title"></h3>
        
        <div class="tnv-preview-players">
            <div id="tnv-spotify-player" class="tnv-player">
                <h4><?php _e('Listen on Spotify', 'tonna-vinyla'); ?></h4>
                <div class="tnv-spotify-embed"></div>
            </div>
            
            <div id="tnv-youtube-player" class="tnv-player">
                <h4><?php _e('Watch on YouTube', 'tonna-vinyla'); ?></h4>
                <div class="tnv-youtube-embed"></div>
            </div>
        </div>
        
        <div class="tnv-preview-loading">
            <div class="tnv-spinner"></div>
            <p><?php _e('Loading preview...', 'tonna-vinyla'); ?></p>
        </div>
        
        <div class="tnv-preview-error" style="display: none;">
            <p><?php _e('Sorry, preview is not available for this track.', 'tonna-vinyla'); ?></p>
        </div>
    </div>
</div>