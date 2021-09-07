$(function () {

    const add = '.rate_add'
    const remove = '.rate_remove'
    const origin = $('.rate_set_model')
    const set = '.input_set'

    $(document).on('click', add, function (e) {
        let parent = $(this).parents(set)
        origin.clone(true)
            .removeClass('rate_set_model hidden')
            .addClass('flex')
            .insertAfter(parent)
    });
    $(document).on('click', remove, function (e) {
        $(this).parents(set).remove()
    });

});