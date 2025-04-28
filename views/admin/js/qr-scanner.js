jQuery(document).ready(function($) {
    const qrButton = $('#tnv_scan_qr');
    const container = $('#tnv_qr_scanner_container');
    let scanner = null;

    // Инициализация QR сканера
    qrButton.on('click', function(e) {
        e.preventDefault();
        
        if (!scanner) {
            startScanner();
        } else {
            stopScanner();
        }
    });

    // Запуск сканера
    async function startScanner() {
        try {
            const videoElem = $('<video/>')[0];
            container.html(videoElem);
            container.show();

            scanner = new QrScanner(
                videoElem,
                result => handleScanResult(result),
                {
                    highlightScanRegion: true,
                    highlightCodeOutline: true,
                }
            );

            await scanner.start();
            qrButton.text(tnv_admin.stop_scanning);
        } catch (error) {
            console.error('Ошибка запуска сканера:', error);
            alert(tnv_admin.camera_error);
        }
    }

    // Остановка сканера
    function stopScanner() {
        if (scanner) {
            scanner.stop();
            scanner.destroy();
            scanner = null;
            container.empty();
            container.hide();
            qrButton.text(tnv_admin.start_scanning);
        }
    }

    // Обработка результата сканирования
    function handleScanResult(result) {
        stopScanner();

        // Отправляем запрос к Discogs через наш API
        $.ajax({
            url: tnv_admin.ajax_url,
            type: 'POST',
            data: {
                action: 'tnv_scan_qr',
                nonce: tnv_admin.nonce,
                barcode: result
            },
            beforeSend: function() {
                container.html('<div class="tnv-spinner"></div>');
                container.show();
            },
            success: function(response) {
                container.hide();
                if (response.success) {
                    fillProductFields(response.data);
                } else {
                    alert(response.data);
                }
            },
            error: function() {
                container.hide();
                alert(tnv_admin.request_error);
            }
        });
    }

    // Заполнение полей продукта данными
    function fillProductFields(data) {
        // Основные поля WooCommerce
        $('#title').val(data.title);
        $('#_regular_price').val('');
        
        // Кастомные поля
        $('#_tnv_artist').val(data.artist);
        $('#_tnv_label').val(data.label);
        $('#_tnv_year').val(data.year);
        
        // Очистка и заполнение трек-листа
        const tracklistContainer = $('#tnv_tracklist_container');
        tracklistContainer.empty();
        
        data.tracklist.forEach((track, index) => {
            const trackHtml = `
                <div class="tnv-track-item">
                    <input type="text" 
                           name="tnv_tracklist[${index}][position]" 
                           value="${track.position}" 
                           class="tnv-track-position">
                    <input type="text" 
                           name="tnv_tracklist[${index}][title]" 
                           value="${track.title}" 
                           class="tnv-track-title">
                    <input type="text" 
                           name="tnv_tracklist[${index}][duration]" 
                           value="${track.duration}" 
                           class="tnv-track-duration">
                    <button type="button" class="button tnv-remove-track">
                        ${tnv_admin.remove_track}
                    </button>
                </div>
            `;
            tracklistContainer.append(trackHtml);
        });

        // Устанавливаем жанры и стили если они есть
        if (data.genre) {
            $('#tax-input-vinyl_genre').val(data.genre.join(', '));
        }
        if (data.style) {
            $('#tax-input-vinyl_style').val(data.style.join(', '));
        }
    }

    // Обработчики для трек-листа
    $('#tnv_tracklist_container').on('click', '.tnv-remove-track', function() {
        $(this).closest('.tnv-track-item').remove();
    });

    $('.tnv-add-track').on('click', function() {
        const index = $('.tnv-track-item').length;
        const trackHtml = `
            <div class="tnv-track-item">
                <input type="text" 
                       name="tnv_tracklist[${index}][position]" 
                       class="tnv-track-position"
                       placeholder="${tnv_admin.position_placeholder}">
                <input type="text" 
                       name="tnv_tracklist[${index}][title]" 
                       class="tnv-track-title"
                       placeholder="${tnv_admin.title_placeholder}">
                <input type="text" 
                       name="tnv_tracklist[${index}][duration]" 
                       class="tnv-track-duration"
                       placeholder="${tnv_admin.duration_placeholder}">
                <button type="button" class="button tnv-remove-track">
                    ${tnv_admin.remove_track}
                </button>
            </div>
        `;
        $('#tnv_tracklist_container').append(trackHtml);
    });
});