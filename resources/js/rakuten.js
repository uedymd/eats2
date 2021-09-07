$(function () {

    const rakutenGenreApi = 'https://app.rakuten.co.jp/services/api/IchibaGenre/Search/20140222';
    const rakutenSearchApi = 'https://app.rakuten.co.jp/services/api/IchibaItem/Search/20170706';
    const rakutenAppID = '1000118304340903899';
    const sublist = $('.rakuten__genre--selector');
    const list = '.rakuten__genre--selector li span';
    const link = '.rakuten__genre--selector li>div.data';
    const genre = $('input#genre');
    const genre_id = $('#genre_id');
    const loader = $('#loader');
    const loaderCounter = $('#loader .count');
    const reserveBtn = $('#reserve-submit');
    const waitTime = 300;
    const pages = 100;

    const getGenre = (id) => {
        return new Promise((resolve, reject) => {
            $.ajax({
                dataType: 'json',
                type: 'GET',
                data: {
                    'applicationId': rakutenAppID,
                    'genreId': id,
                },
                url: rakutenGenreApi,
            }).then(
                function (result) {
                    // 正常終了
                    resolve(result.children);
                },
                function () {
                    // エラー
                    reject();
                }
            )
        })
    }

    const getRakutenSearchResult = (request) => {
        return new Promise((resolve, reject) => {

            $.ajax({
                dataType: 'json',
                type: 'GET',
                data: request,
                url: rakutenSearchApi,
            }).then(
                function (result) {
                    // 正常終了
                    if (result.hits > 0) {
                        resolve(result.Items);
                    } else {
                        reject();
                    }
                },
                function () {
                    // エラー
                    reject();
                }
            )
        })
    }

    const sleep = msec => new Promise(resolve => setTimeout(resolve, msec));

    const makeResultTable = (items) => {
        let html = '';
        html += '<p>' + items.length + '件見つかりました。</p>';
        html += '<table class="table">';
        html += '<thead class="thead-dark">';
        html += '<tr>';
        html += '<th scope="col" class="select">選択</th>';
        html += '<th scope="col" class="image">画像</th>';
        html += '<th scope="col" class="title">タイトル</th>';
        html += '<th scope="col" class="price">価格</th>';
        html += '</tr>';
        html += '</thead>';
        html += '</tbody>';
        for (let i = 0; i < items.length; i++) {
            let count = i + 1;
            html += '<tr>';
            html += '<td><label>No.' + count;
            html += '<input type="checkbox" class="target" name="item[]" val="' + items[i]['Item'].itemCode + '" checked></label>';
            html += '<input type="hidden" name="item_url[' + items[i]['Item'].itemCode + ']" val="' + items[i]['Item']['itemUrl'] + '">';
            html += '<input type="hidden" name="item_price[' + items[i]['Item'].itemCode + ']" val="' + items[i]['Item']['itemPrice'] + '">';
            html += '<input type="hidden" name="item_name[' + items[i]['Item'].itemCode + ']" val="' + items[i]['Item']['itemName'] + '">';
            if (items[i]['Item']['mediumImageUrls'] !== undefined) {
                for (let j = 0; j < items[i]['Item']['mediumImageUrls'].length; j++) {
                    html += '<input type="hidden" name="item_image[' + items[i]['Item'].itemCode + '][]" val="' + items[i]['Item']['mediumImageUrls'][j].imageUrl + '">';
                }
            }
            html += '</td>';
            if (items[i]['Item']['mediumImageUrls'][0] !== undefined) {
                html += '<td><img src="' + items[i]['Item']['mediumImageUrls'][0].imageUrl + '"></td>';
            } else {
                html += '<td>画像なし</td>';
            }
            html += '<td>' + items[i]['Item']['itemName'] + '<br><br><a href="' + items[i]['Item']['itemUrl'] + '" target="_blank" >' + items[i]['Item']['itemUrl'] + '</a></td>';
            html += '<td>' + items[i]['Item']['itemPrice'].toLocaleString() + '円</td>';
            html += '</tr>';
        }
        html += '</tbody>';
        html += '</table>';

        $('#search-result').empty().append(html);
    }

    const initResultTable = () => {
        $('#search-result').empty();
    }

    const setGenre = async (id) => {
        let children = await getGenre(id);
        return children;
    }

    const setRakutenSearchResult = async (request) => {
        let items = [];
        loader.fadeIn('fast');
        loaderCounter.text('');
        for (let page = 1; page <= pages; page++) {
            await sleep(waitTime);
            try {
                request.page = page;
                let data = await getRakutenSearchResult(request);
                if (data !== undefined && data !== '') {
                    items = items.concat(data);
                    loaderCounter.text((page - 1) * 30 + 'アイテム取得');
                }
            } catch (e) {
                console.log(e);
                break;
            }
        }
        loader.fadeOut('fast');
        return items;
    }

    $(document).on('click', list, async function () {

        let parent = $(this).parent('li');


        if (parent.hasClass('open')) {
            parent.removeClass('open').find('ul').remove();
        } else {
            parent.addClass('open');
            let id = parent.data('genre');
            if (id != void (0)) {
                parent.addClass('hasChild');
                let children = await setGenre(id);

                if (children.length > 0) {

                    let html = '<ul class="sublist">';

                    for (let i = 0; i < children.length; i++) {
                        html += '<li class="hasChild" data-genre="' + children[i]['child']['genreId'] + '" data-name="' + children[i]['child']['genreName'] + '"><span></span><div class="data">' + children[i]['child']['genreName'] + '</div></li>';
                    }

                    html += '</ul>';

                    parent.append(html);
                } else {
                    parent.removeClass('hasChild')
                }
            }
        }
    });

    $(document).on('click', link, function () {
        let name = $(this).parent('li').data('name');
        let id = $(this).parent('li').data('genre');
        genre.val(name);
        genre_id.val(id);
        sublist.find('ul').remove();
    });

    $('#rakutenSearch').on('submit', function (event) {

        initResultTable();

        reserveBtn.prop('disabled', true);

        let keyword = $('#keyword').val();
        let minPrice = $('#minPrice').val();
        let maxPrice = $('#maxPrice').val();
        let genreId = $('#genre_id').val();
        let NGKeyword = $('#NGKeyword').val();
        let NGurl = $('#NGurl').val();
        let request = {
            'applicationId': rakutenAppID
        };

        if (keyword !== '') {

            request.keyword = keyword;

            if (minPrice !== '') {
                request.minPrice = minPrice;
            }
            if (maxPrice !== '') {
                request.maxPrice = maxPrice;
            }
            if (genreId !== '') {
                request.genreId = genreId;
            }
            if (NGKeyword !== '') {
                request.NGKeyword = NGKeyword;
            }
            (async () => {
                let result = await setRakutenSearchResult(request);
                if (result.length > 0) {
                    makeResultTable(result);
                    reserveBtn.prop('disabled', false);
                }
            })()
        }
        return false;
    });
});