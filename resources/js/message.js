$(function () {
    $("input[name=uploader]").on("change", function () {
        $("#loader").show();
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
                $("#loader").hide();
                ret = JSON.parse(result);
                console.log(ret);
                if (ret["Ack"] !== "Failure") {
                    image = ret["SiteHostedPictureDetails"]["FullURL"];
                    html = "";
                    html += `<input type="hidden" name="images[]" value="${image}" >`;
                    html += `<div class="w-2/12 mr-2 mb-3"><img src="${image}"></div>`;
                    $('.imageUploads').append(html);
                }else{
                    alert("不明なエラーが発生しました。");
                }
            })

            .fail(function (data) {
                alert("アップロードに失敗しました");
                $("#loader").hide();
            });
    });
});
