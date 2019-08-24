(function(root, document) {
    "use strict";
    [].forEach.call(document.getElementsByClassName("iframe-lightbox-link"), function(el) {
        el.lightbox = new IframeLightbox(el, {
            onCreated: function() {
                /* show your preloader */
            },
            onLoaded: function() {
                document.getElementsByClassName("content-holder")[0].style.backgroundColor = "#fff";
            },
            onError: function() {
                /* hide your preloader */
            },
            onClosed: function() {
                document.getElementsByClassName("content-holder")[0].style.backgroundColor = "#4c4c4c";
                document.getElementsByClassName("content-holder")[0].style.opacity = "1";
            },
            scrolling: true,
            /* default: false */
            rate: 500 /* default: 500 */,
            touch: false /* default: false - use with care for responsive images in links on vertical mobile screens */
        });
    });

    document.getElementById("iframe-just-pay").click();

})("undefined" !== typeof window ? window : this, document);