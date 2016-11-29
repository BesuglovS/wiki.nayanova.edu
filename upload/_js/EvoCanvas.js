$(function() {
    var INITIAL_COUNT = 100;
    var MAX_INITIAL_HEALTH = 20;
    var MAX_HEALTH = 300;
    var AREA_RADIUS = 100;
    var CANVAS_HEIGHT = $("#evoCanvas").height();
    var CANVAS_WIDTH = $("#evoCanvas").width();

    var canvas = document.getElementById("evoCanvas");
    var ctx = canvas.getContext("2d");

    function changeHue(rgb, degree) {
        var hsl = rgbToHSL(rgb);
        hsl.h += degree;
        if (hsl.h > 360) {
            hsl.h -= 360;
        }
        else if (hsl.h < 0) {
            hsl.h += 360;
        }
        return hslToRGB(hsl);
    }

    function changeSaturation(rgb, degree) {
        var hsl = rgbToHSL(rgb);
        hsl.s += degree;
        if (hsl.s > 360) {
            hsl.s -= 360;
        }
        else if (hsl.s < 0) {
            hsl.s += 360;
        }
        return hslToRGB(hsl);
    }

// exepcts a string and returns an object
    function rgbToHSL(rgb) {
        // strip the leading # if it's there
        rgb = rgb.replace(/^\s*#|\s*$/g, '');

        // convert 3 char codes --> 6, e.g. `E0F` --> `EE00FF`
        if(rgb.length == 3){
            rgb = rgb.replace(/(.)/g, '$1$1');
        }

        var r = parseInt(rgb.substr(0, 2), 16) / 255,
            g = parseInt(rgb.substr(2, 2), 16) / 255,
            b = parseInt(rgb.substr(4, 2), 16) / 255,
            cMax = Math.max(r, g, b),
            cMin = Math.min(r, g, b),
            delta = cMax - cMin,
            l = (cMax + cMin) / 2,
            h = 0,
            s = 0;

        if (delta == 0) {
            h = 0;
        }
        else if (cMax == r) {
            h = 60 * (((g - b) / delta) % 6);
        }
        else if (cMax == g) {
            h = 60 * (((b - r) / delta) + 2);
        }
        else {
            h = 60 * (((r - g) / delta) + 4);
        }

        if (delta == 0) {
            s = 0;
        }
        else {
            s = (delta/(1-Math.abs(2*l - 1)))
        }

        return {
            h: h,
            s: s,
            l: l
        }
    }

// expects an object and returns a string
    function hslToRGB(hsl) {
        var h = hsl.h,
            s = hsl.s,
            l = hsl.l,
            c = (1 - Math.abs(2*l - 1)) * s,
            x = c * ( 1 - Math.abs((h / 60 ) % 2 - 1 )),
            m = l - c/ 2,
            r, g, b;

        if (h < 60) {
            r = c;
            g = x;
            b = 0;
        }
        else if (h < 120) {
            r = x;
            g = c;
            b = 0;
        }
        else if (h < 180) {
            r = 0;
            g = c;
            b = x;
        }
        else if (h < 240) {
            r = 0;
            g = x;
            b = c;
        }
        else if (h < 300) {
            r = x;
            g = 0;
            b = c;
        }
        else {
            r = c;
            g = 0;
            b = x;
        }

        r = normalize_rgb_value(r, m);
        g = normalize_rgb_value(g, m);
        b = normalize_rgb_value(b, m);

        return rgbToHex(r,g,b);
    }

    function normalize_rgb_value(color, m) {
        color = Math.floor((color + m) * 255);
        if (color < 0) {
            color = 0;
        }
        return color;
    }

    function rgbToHex(r, g, b) {
        return "#" + ((1 << 24) + (r << 16) + (g << 8) + b).toString(16).slice(1);
    }

    function GenerateDots() {
        var result = [];

        for (var i = 0; i < INITIAL_COUNT; i++) {
            var angle = Math.random()*Math.PI*2;
            var distance = Math.random() * AREA_RADIUS;
            result[i] = {
                id: i,
                health: Math.floor((Math.random() * (MAX_INITIAL_HEALTH - 10)) + 10),
                x: Math.round(Math.cos(angle) * distance + 110),
                y: Math.round(Math.sin(angle) * distance + 110)
            };
        }

        return result;
    }

    function Fit(number, low, high) {
        if (number < low)
        {
            return low;
        }

        if (number > high)
        {
            return high;
        }

        return number;
    }

    function CalculateDots() {
        for (var i = 0; i < dots.length; i++) {
            dots[i].x = dots[i].x + Math.floor((Math.random() * ((dots[i].health/20)*2+1)) - (dots[i].health/20));
            dots[i].y = dots[i].y + Math.floor((Math.random() * ((dots[i].health/20)*2+1)) - (dots[i].health/20));

            var x = dots[i].x - 110;
            var y = dots[i].y - 110;

            var r = Math.sqrt(x*x + y*y);
            var t = Math.atan2(y, x);

            if (r > AREA_RADIUS)
            {
                r = AREA_RADIUS;

                x = Math.round(r * Math.cos(t));
                y = Math.round(r * Math.sin(t));

                dots[i].x = x + 110;
                dots[i].y = y + 110;
            }



            for (var j = 0; j < dots.length; j++) {
                if (dots[i] == undefined || dots[j] == undefined)
                {
                    var eprst = 999;
                }
                if (dots[i].id != dots[j].id) {
                    if (
                        Math.sqrt(
                            (dots[i].x - dots[j].x)*(dots[i].x - dots[j].x) +
                            (dots[i].y - dots[j].y)*(dots[i].y - dots[j].y)) <
                        (Math.log10(dots[i].health) + Math.log10(dots[j].health))
                    ) {
                        if (dots[i].health > dots[j].health)
                        {
                            dots[i].health = Fit(dots[i].health + dots[j].health, 0, MAX_HEALTH);
                            dots.splice(j, 1);
                        } else {

                            if (dots[i].health < dots[j].health) {
                                dots[j].health = Fit(dots[j].health + dots[i].health, 0, MAX_HEALTH);
                                dots.splice(i, 1);
                            } else {

                                if (dots[i].health = dots[j].health) {
                                    var r = Math.random();
                                    if (r > 0.5) {
                                        dots[i].health = Fit(dots[i].health + dots[j].health, 0, MAX_HEALTH);
                                        dots.splice(j, 1);
                                    } else {
                                        dots[j].health = Fit(dots[j].health + dots[i].health, 0, MAX_HEALTH);
                                        dots.splice(i, 1);
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    function RenderDots() {
        for (var i = 0; i < dots.length; i++) {
            ctx.beginPath();
            ctx.arc(dots[i].x, dots[i].y, Math.log10(dots[i].health), 0, 2 * Math.PI, true);
            ctx.fillStyle = changeHue("#FF0000", 360 - dots[i].health * 360 / MAX_HEALTH);
            ctx.fill();
        }
    }

    function RenderScreen() {
        ctx.beginPath();
        ctx.arc(110, 110, AREA_RADIUS, 0, 2*Math.PI);
        ctx.fillStyle="#000000";
        ctx.fill();
    }

    var dots = GenerateDots();

    RenderScreen();

    $( "#evoCanvas" ).one( "click", function() {
        setInterval(function(){
            CalculateDots();
            RenderScreen();
            RenderDots();
        }, 100);
    });
});
