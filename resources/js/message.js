$(function () {
    $("input[name=uploader]").on("change", function () {
        var fd = new FormData();
        fd.append("image", $(this).prop("files")[0]);


        $.ajax({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            url: "/message/upload",
            type: "POST",
            data: fd,
            processData: false,
            contentType: false,

            dataType: "text",
        })
            .done(function (result) {
                ret = JSON.parse(result);
                console.log(ret);
                if (ret["Ack"] !== "Failure") {
                    image = ret["SiteHostedPictureDetails"]["FullURL"];
                    html = `<input type="hidden" value="${image}" >`;
                    $('.imageUploads').append(html);
                }else{
                    alert("不明なエラーが発生しました。");
                }
            })

            .fail(function (data) {
                alert("アップロードに失敗しました");
            });
    });
});
