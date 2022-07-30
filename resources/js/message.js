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

$(function(){
    const list = $('.block__mail');
    
    const get_item_data = async(id) => {
        const url = `/api/message/side_items/${id}`;
        const response = await fetch(url);
        if(response.ok){
            const data = response.json();
            return data;
        }else{
            return;
        }
    }

    const insert_image = (data,target) => {
        if(data.image !== ''){
            let html = `
            <div class="w-3/12 shrink-0 mr-5">
                <img src="${data.image}" alt="">
            </div>
            `;
            target.find('.flex').prepend(html);
        }
    }
    const insert_title = (data,target) => {
        console.log(data.title);
        if(data.title !== ''){
            let html = `
            <div class="block__title">
                ${data.title}
            </div>
            `;
            target.find('.block__data').append(html);
        }
    }
    
    if(list.length>0){
        list.each(async function(){
            let itemID = $(this).find('a').data('item');
            if(itemID !== void 0){
                get_item_data(itemID)
                    .then(data => {
                        insert_image(data,$(this));
                        insert_title(data,$(this));
                    })
                    .catch(error=>{
                        // console.log(error);
                    })
            }
        });
    }


});