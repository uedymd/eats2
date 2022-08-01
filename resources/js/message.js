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
                console.log(result);
                ret = JSON.parse(result);
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
        const response = await fetch(url);
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
            const target = $(`.block__mail > a[data-item=${data.id}]`);
            let html = `
            <div class="block__title">
                ${data.title}
            </div>
            `;
            target.find('.block__data').append(html);
        })
    }

    const scroll_current = () => {
        const sidebar = $('.side__mails');
        const current = $('.side__mails .current__item');
        if(sidebar.length > 0){
            let currentPos = current.position().top;
            sidebar.animate({scrollTop:currentPos},'slow');
        }
    }
    
    if(list.length>0){
        let ids = [];
        list.each(async function(){
            let itemID = $(this).find('a').data('item');
            if(itemID !== void 0){
                ids.push(itemID);
            }
        });
        get_item_data()
            .then(data => {
                insert_image(data);
                insert_title(data);
                scroll_current();
            })
            .catch(error=>{
                // console.log(error);
            })
    }
});


$(function(){
    const blockDetail = $('.block__item--detail');
    const currentID = blockDetail.data('current');
    const get_item_detail = async(id) => {
        const url = `/api/message/item_detail/${id}`;
        const response = await fetch(url);
        if(response.ok){
            const data = await response.json();
            return data;
        }else{
            return;
        }
    }

    const insert_item_detail = (data) =>{
        let html = '';
        html += '<div class="flex">';
            html += '<div class="w-3/12 shurink-0 mr-5">';
                if(data.ebay.image !== void 0){
                    html += `<img src="${data.ebay.image}" alt="" class="block" style="max-width:100%;height:auto;">`;
                }
                if(data.ebay.view_url !== void 0){
                    html += `<a href="${data.ebay.view_url}" target="_blank" class="block rounded bg-gray-500 p-2 text-white text-center mt-2">View</a>`;
                }else{
                    html += '<small>詳細取得中</small>';
                }
            html += '</div>';
            html += '<div class="w-9/12">';
                html += `${data.ebay.title}`;
                if(data.ebay.ebay_id > 0){
                    html += `<br>【${data.ebay.ebay_id}】`;
                }
                if(data.suppliers !== void 0){
                    html += `
                    <div class="w-8/12">
                        <a href="${data.suppliers}" target="_blank" class="block rounded bg-gray-500 p-2 text-white text-center mt-5">${data.ebay.site}</a>
                    </div>
                    `;
                }
        html += '</div>';
        html += '</div>';
        html += '<div class="flex mt-5">';
            html += `<div class="w-8/12 mr-5 mt-2">${data.ebay.tracking_at}</div>`;
            html += '<div class="w-4/12 text-center">';
                html += `<a href="/ebay/trading/delete/${data.ebay.id}" class="block rounded bg-red-600 p-2 text-white text-center">出品取消</a>`;
            html += '</div>';
        html += '</div>';
        html += '<div class="block__translate mt-10">';
            let jp_content = data.target.jp_content.replace(/\n/g, '<br>');
            let en_content = data.target.en_content.replace(/\n/g, '<br>');
            html += `<div class="border-t-2 px-4 py-4">${jp_content}</div>`;
            html += `<div class="border-t-2 px-4 py-4">${en_content}</div>`;
        html += '</div>';
        blockDetail.html(html);
    }


    if(currentID  !== void 0){
        get_item_detail(currentID)
            .then(data =>{
                insert_item_detail(data);
            })
            .catch(error =>{
                console.log(error);
            })
        
    }
})