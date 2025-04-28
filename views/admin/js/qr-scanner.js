jQuery(document).ready(function($) {
    const qrButton = $('#tnv-scan-qr');
    const container = $('#tnv-qr-scanner-container');
    let scanner = null;

    // Устанавливаем путь к воркеру QR-сканера
    QrScanner.WORKER_PATH = tnvQR.plugin_url + 'assets/js/qr-scanner-worker.min.js';

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

            // Проверяем доступ к камере
            const stream = await navigator.mediaDevices.getUserMedia({ 
                video: { 
                    facingMode: "environment",
                    width: { ideal: 1280 },
                    height: { ideal: 720 }
                } 
            });
            
            videoElem.srcObject = stream;
            
            scanner = new QrScanner(
                videoElem,
                result => handleScanResult(result),
                {
                    highlightScanRegion: true,
                    highlightCodeOutline: true,
                    maxScansPerSecond: 5
                }
            );

            await scanner.start();
            qrButton.text(tnvQR.stop_scanning);
        } catch (error) {
            console.error('Ошибка запуска сканера:', error);
            alert(tnvQR.camera_error);
            stopScanner();
        }
    }

    // Остановка сканера
    function stopScanner() {
        if (scanner) {
            scanner.stop();
            scanner.destroy();
            scanner = null;
            
            // Останавливаем поток с камеры
            const videoElem = container.find('video')[0];
            if (videoElem && videoElem.srcObject) {
                const tracks = videoElem.srcObject.getTracks();
                tracks.forEach(track => track.stop());
            }
            
            container.empty();
            container.hide();
            qrButton.text(tnvQR.start_scanning);
        }
    }

    // Обработка результата сканирования
    function handleScanResult(result) {
        stopScanner();

        // Отправляем запрос к Discogs через наш API
        $.ajax({
            url: tnvQR.ajax_url,
            type: 'POST',
            data: {
                action: 'tnv_scan_qr',
                nonce: tnvQR.nonce,
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
                    alert(response.data || tnvQR.request_error);
                }
            },
            error: function() {
                container.hide();
                alert(tnvQR.request_error);
            }
        });
    }

    // Заполнение полей продукта данными
    function fillProductFields(data) {
        // Основные поля WooCommerce
        $('#title').val(data.title || '');
        
        // Кастомные поля
        $('#_tnv_artist').val(data.artist || '');
        $('#_tnv_label').val(data.label || '');
        $('#_tnv_year').val(data.year || '');
        
        // Очистка и заполнение трек-листа
        const tracklistContainer = $('#tnv_tracklist_container');
        tracklistContainer.empty();
        
        if (data.tracklist && data.tracklist.length > 0) {
            data.tracklist.forEach((track, index) => {
                const trackHtml = `
                    <div class="tnv-track-item">
                        <input type="text" 
                               name="tnv_tracklist[${index}][position]" 
                               value="${track.position || ''}" 
                               class="tnv-track-position">
                        <input type="text" 
                               name="tnv_tracklist[${index}][title]" 
                               value="${track.title || ''}" 
                               class="tnv-track-title">
                        <input type="text" 
                               name="tnv_tracklist[${index}][duration]" 
                               value="${track.duration || ''}" 
                               class="tnv-track-duration">
                        <button type="button" class="button tnv-remove-track">
                            ${tnvQR.remove_track}
                        </button>
                    </div>
                `;
                tracklistContainer.append(trackHtml);
            });
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
                       placeholder="${tnvQR.position_placeholder}">
                <input type="text" 
                       name="tnv_tracklist[${index}][title]" 
                       class="tnv-track-title"
                       placeholder="${tnvQR.title_placeholder}">
                <input type="text" 
                       name="tnv_tracklist[${index}][duration]" 
                       class="tnv-track-duration"
                       placeholder="${tnvQR.duration_placeholder}">
                <button type="button" class="button tnv-remove-track">
                    ${tnvQR.remove_track}
                </button>
            </div>
        `;
        $('#tnv_tracklist_container').append(trackHtml);
    });
});