$(function () {

    const deleter = $('a[href*="/destroy"]');

    deleter.on('click', function () {
        if (!confirm('削除しますか？')) {
            return false;
        } else {
            location.href = $(this).attr('href');
        }
    })

});