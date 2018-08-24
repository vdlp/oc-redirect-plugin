var testerShouldStop = false;

function testerExecute(offset, total, button) {
    if (testerShouldStop) {
        testerDone();
        testerShouldStop = false;
        return;
    }

    $.request('onTest', {
        data: {
            offset: offset
        },
        success: function (data) {
            if (data.result === '' || typeof data.result === 'undefined') {
                testerDone();
                updateStatusBar(total, total);
                return;
            }

            $('#testerResults').prepend(data.result);

            updateStatusBar(total, offset);

            if (offset + 1 !== total) {
                testerExecute(offset + 1, total, button);
            }
        },
        error: function() {
            if (offset + 1 !== total) {
                testerExecute(offset + 1, total, button);
            }
        }
    });
}

function testerDone() {
    $('#testButton').prop('disabled', false);

    var loader = $('#loader');
        loader.removeClass('loading');

    setTimeout(function () {
        loader.addClass('hidden');
    }, 500);
}

function testerStart(button) {
    updateStatusBar(0);

    $('#testerResults').html('');

    button.prop('disabled', true);

    var loader = $('#loader');
        loader.removeClass('hidden');
        loader.addClass('loading');

    testerExecute(0, $('#redirectCount').val(), button);
}

function testerStop() {
    testerShouldStop = true;
}

function updateStatusBar(total, offset) {
    var width = 0;

    if (total > 0) {
        width = Math.ceil(100 / total * offset);
    }

    var progress = $('#progress');
    progress.html(width + '% complete (' + offset + ' of ' + total + ')');

    var progressBar = $('#progressBar');
    progressBar.attr('aria-valuenow', width);
    progressBar.css('width', width + '%');

    if (width === 0) {
        progress.html(progress.data('initial'));
    }
}
