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
    
    const get_item_data = async(ids) => {
        const url = `/api/message/side_items/`;
        const method = 'post';
        const headers = {
            'Accept': 'application/json',
            'Content-Type': 'application/json'
          };
        const body = JSON.stringify(ids);
        const response = await fetch(url,{method, headers, body});
        if(response.ok){
            const data = await response.json();
            return data;
        }else{
            return;
        }
    }

    const insert_image = (data) => {
        
        if(data.length > 0){
            data.forEach((data,key)=>{
                console.log(data);
                const target = $(`.block__mail > a[data-item=${data.id}]`);
                let html = `
                <div class="w-3/12 shrink-0 mr-5">
                    <img src="${data.image}" alt="">
                </div>
                `;
                target.find('.flex').prepend(html);
            })
        }
    }
    const insert_title = (data) => {
        data.forEach((data,key)=>{
            console.log(data);
            const target = $(`.block__mail > a[data-item=${data.id}]`);
            let html = `
            <div class="block__title">
                ${data.title}
            </div>
            `;
            target.find('.block__data').append(html);
        })
    }
    
    if(list.length>0){
        let ids = [];
        list.each(async function(){
            let itemID = $(this).find('a').data('item');
            if(itemID !== void 0){
                ids.push(itemID);
            }
        });
        get_item_data(JSON.stringify(ids))
            .then(data => {
                insert_image(data);
                insert_title(data);
            })
            .catch(error=>{
                // console.log(error);
            })
    }


});