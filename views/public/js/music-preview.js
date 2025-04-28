jQuery(document).ready(function($) {
    const modal = $('#tnv-preview-modal');
    const modalTitle = modal.find('.tnv-preview-title');
    const spotifyEmbed = modal.find('.tnv-spotify-embed');
    const youtubeEmbed = modal.find('.tnv-youtube-embed');
    const loading = modal.find('.tnv-preview-loading');
    const error = modal.find('.tnv-preview-error');

    // Обработчик кнопки предпрослушивания
    $('.tnv-preview-track').on('click', function() {
        const artist = $(this).data('artist');
        const track = $(this).data('track');
        
        showPreviewModal(artist, track);
    });

    // Закрытие модального окна
    $('.tnv-modal-close').on('click', function() {
        hidePreviewModal();
    });

    // Закрытие по клику вне контента
    modal.on('click', function(e) {
        if (e.target === this) {
            hidePreviewModal();
        }
    });

    // Показ модального окна
    function showPreviewModal(artist, track) {
        modalTitle.text(`${artist} - ${track}`);
        spotifyEmbed.empty();
        youtubeEmbed.empty();
        loading.show();
        error.hide();
        modal.show();

        // Запрос к API для получения предпрослушивания
        $.ajax({
            url: tnv_preview.ajax_url,
            type: 'GET',
            data: {
                action: 'tnv_get_preview',
                artist: artist,
                track: track,
                nonce: tnv_preview.nonce
            },
            success: function(response) {
                loading.hide();
                
                if (response.success) {
                    if (response.data.spotify) {
                        spotifyEmbed.html(response.data.spotify);
                        $('#tnv-spotify-player').show();
                    } else {
                        $('#tnv-spotify-player').hide();
                    }

                    if (response.data.youtube) {
                        youtubeEmbed.html(response.data.youtube);
                        $('#tnv-youtube-player').show();
                    } else {
                        $('#tnv-youtube-player').hide();
                    }

                    if (!response.data.spotify && !response.data.youtube) {
                        error.show();
                    }
                } else {
                    error.show();
                }
            },
            error: function() {
                loading.hide();
                error.show();
            }
        });
    }

    // Скрытие модального окна
    function hidePreviewModal() {
        modal.hide();
        spotifyEmbed.empty();
        youtubeEmbed.empty();
    }
});