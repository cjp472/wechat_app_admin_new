<script>
    var htmlDom = document.documentElement;
    var windowWidth = htmlDom.clientWidth;
    var evt = "onorientationchange" in window ? "orientationchange" : "resize";
    htmlDom.style.fontSize = windowWidth / 7.5 + 'px';
    (function () {
        document.addEventListener('DOMContentLoaded', function () {
            if(!htmlDom.style.fontSize){
                var htmlDomProtect = document.documentElement;
                var windowWidth = htmlDomProtect.clientWidth;
                htmlDomProtect.style.fontSize = windowWidth / 7.5+ 'px';
            }
        }, false);
    })();

    window.addEventListener(evt, function() {
        setTimeout(function () {
            var htmlDomProtect = document.documentElement;
            var windowWidth = htmlDomProtect.clientWidth;
            htmlDomProtect.style.fontSize = windowWidth / 7.5 + 'px';
        },200);
    }, false);
</script>