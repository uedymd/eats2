$(function () {

    const deleter = $('a[href*="/destroy"]');


    deleter.on('click', function () {
        if (!confirm('削除しますか？')) {
            return false;
        } else {
            let nextPage = function () {
                location.href = $(this).attr('href');
            }
            setTimeout(nextPage, 10000);
        }
    })

});