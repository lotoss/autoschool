$(document).on("click", ".view-img", function() {
    $("body").append(`
            <div class="w-100 h-100 position-fixed" style="top: 0; background-color: #00000086; z-index: 99999" id="img-expanded">
                <div class="container h-100 d-flex justify-content-center">
                    <img src="${$(this).attr("src")}" alt="${$(this).attr("alt")}" class="d-block my-auto shadow-lg" style="max-width:calc(100vw - 5%), max-height: calc(100vh - 5%); min-width: 30vw">
                </div>
            </div>
        `)
    $("html").addClass("html-no-scroll");
})

$(document).on("click", function(e) {
    if ($("#img-expanded").length > 0) {
        if (!$(e.target).is("img")) {
            $("#img-expanded").remove();
        }
    }
})

$(document).on("click", "[data-sort]", function() {
    var sortOrder;
    if (urlParams.get('sortOrder') == 'DESC' || urlParams.get('sortOrder') == '') {
        sortOrder = 'ASC';
    } else {
        sortOrder = 'DESC';
    }

    var qString = "";
    if (urlParams.get('q') != null) {
        qString = '&q=' + urlParams.get('q');
    }

    var pageString = "";
    if (urlParams.get('page') != null) {
        pageString = '&page=' + urlParams.get('page');
    }

    location.href = `?orderBy=${$(this).attr('data-sort')}&sortOrder=${sortOrder}${qString}${pageString}`
})

String.prototype.escape = function() {
    var tagsToReplace = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;'
    };
    return this.replace(/[&<>]/g, function(tag) {
        return tagsToReplace[tag] || tag;
    });
};

function genUUID() {
    var dt = new Date().getTime();
    var uuid = 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
        var r = (dt + Math.random() * 16) % 16 | 0;
        dt = Math.floor(dt / 16);
        return (c == 'x' ? r : (r & 0x3 | 0x8)).toString(16);
    });
    return uuid;
}

const urlParams = new URLSearchParams(window.location.search);
$(function() {
    $(".submenu-title").click(function() {
        $($(this).attr('href')).toggleClass('show');
        $($(this).attr('href')).attr('aria-expanded', true);
    })

    $(".sidebar-submenu .active").parents('.sidebar-submenu').addClass("show");
    $(".sidebar-submenu .active").parents('.sidebar-submenu').attr('aria-expanded', true);
})