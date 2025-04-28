// Импортируем библиотеку QR Scanner
import QrScanner from 'qr-scanner';

jQuery(document).ready(function($) {
    const qrButton = $('#tnv-scan-qr');
    const qrContainer = $('#tnv-qr-scanner-container');
    let qrScanner = null;

    // Обработчик клика по кнопке сканирования
    qrButton.on('click', function(e) {
        e.preventDefault();
        
        if (!qrScanner) {
            initQRScanner();
        } else {
            stopScanner();
        }
    });

    // Инициализация сканера
    function initQRScanner() {
        // Создаем элемент video для сканера
        const videoElem = $('<video></video>')[0];
        qrContainer.html(videoElem);
        qrContainer.show();

        // Инициализируем сканер
        qrScanner = new QrScanner(
            videoElem,
            result => handleScan(result),
            {
                highlightScanRegion: true,
                highlightCodeOutline: true,
            }
        );

        qrScanner.start().catch(error => {
            console.error('Ошибка запуска сканера:', error);
            alert('Не удалось получить доступ к камере. Проверьте разрешения.');
        });

        // Меняем текст кнопки
        qrButton.text('Остановить сканирование');
    }

    // Остановка сканера
    function stopScanner() {
        if (qrScanner) {
            qrScanner.stop();
            qrScanner.destroy();
            qrScanner = null;
            qrContainer.empty();
            qrContainer.hide();
            qrButton.text('Сканировать QR код');
        }
    }

    // Обработка результатов сканирования
    function handleScan(result) {
        // Останавливаем сканер после успешного сканирования
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
            success: function(response) {
                if (response.success) {
                    // Заполняем поля продукта полученными данными
                    fillProductFields(response.data);
                } else {
                    alert('Не удалось найти информацию о пластинке: ' + response.data);
                }
            },
            error: function() {
                alert('Произошла ошибка при обработке запроса');
            }
        });
    }

    // Заполнение полей продукта данными
    function fillProductFields(data) {
        // Заполняем основные поля WooCommerce
        $('#title').val(data.title);
        $('#_regular_price').val(''); // Цену нужно установить вручную
        
        // Заполняем кастомные поля
        $('#_tnv_artist').val(data.artist);
        $('#_tnv_label').val(data.label);
        $('#_tnv_year').val(data.year);
        
        // Заполняем описание трек-листом
        let description = '<h3>Трек-лист:</h3><ul>';
        data.tracklist.forEach(track => {
            description += `<li>${track.position}. ${track.title} (${track.duration})</li>`;
        });
        description += '</ul>';
        
        if (window.tinyMCE && window.tinyMCE.get('content')) {
            window.tinyMCE.get('content').setContent(description);
        } else {
            $('#content').val(description);
        }
    }
});