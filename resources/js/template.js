$(function () {

    const preview = $('#preview')
    const input = $('#source')


    const show_previre = () => {
        let soruce = input.val();
        preview.html(soruce);
    }

    const init = () => {
        show_previre();
    }

    init();

    input.keyup(function () {
        show_previre();
    });

});