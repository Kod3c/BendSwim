/*======================================================================*\
 || #################################################################### ||
 || # CBN CMS SaaS                                                     # ||
 || # ---------------------------------------------------------------- # ||
 || # Copyright 2016 PT. Comestoarra Bentarra Noesantarra.             # ||
 || # This file may not be redistributed in whole or significant part. # ||
 || #   ---------------- THIS IS NOT FREE SOFTWARE ----------------    # ||
 || #                 https://www.comestoarra.com                       # ||
 || #################################################################### ||
 \*======================================================================*/

$(document).ready(function() {

    $('#panel').addClass('closed');

    $('#panel').click(function() {

        if ($(this).hasClass('closed')) {
            $(this).parent().animate({ right: "+=200" }, 500);
            $(this).removeClass('closed');
        } else {
            $(this).parent().animate({ right: "-=200" }, 500);
            $(this).addClass('closed');
        }

    });

});

function generateCBNSlug(input_form, output_form, space_character) {

    var slug, value;
    var chars = [{ "search": "\/\u00e4|\u00e6|\u01fd\/", "replace": "ae" }, { "search": "\/\u00f6|\u0153\/", "replace": "oe" }, { "search": "\/\u00fc\/", "replace": "ue" }, { "search": "\/\u00c4\/", "replace": "Ae" }, { "search": "\/\u00dc\/", "replace": "Ue" }, { "search": "\/\u00d6\/", "replace": "Oe" }, { "search": "\/\u00c0|\u00c1|\u00c2|\u00c3|\u00c4|\u00c5|\u01fa|\u0100|\u0102|\u0104|\u01cd|\u0391|\u0386\/", "replace": "A" }, { "search": "\/\u00e0|\u00e1|\u00e2|\u00e3|\u00e5|\u01fb|\u0101|\u0103|\u0105|\u01ce|\u00aa|\u03b1|\u03ac\/", "replace": "a" }, { "search": "\/\u00c7|\u0106|\u0108|\u010a|\u010c\/", "replace": "C" }, { "search": "\/\u00e7|\u0107|\u0109|\u010b|\u010d\/", "replace": "c" }, { "search": "\/\u00d0|\u010e|\u0110|\u0394\/", "replace": "D" }, { "search": "\/\u00f0|\u010f|\u0111|\u03b4\/", "replace": "d" }, { "search": "\/\u00c8|\u00c9|\u00ca|\u00cb|\u0112|\u0114|\u0116|\u0118|\u011a|\u0395|\u0388\/", "replace": "E" }, { "search": "\/\u00e8|\u00e9|\u00ea|\u00eb|\u0113|\u0115|\u0117|\u0119|\u011b|\u03ad|\u03b5\/", "replace": "e" }, { "search": "\/\u011c|\u011e|\u0120|\u0122|\u0393\/", "replace": "G" }, { "search": "\/\u011d|\u011f|\u0121|\u0123|\u03b3\/", "replace": "g" }, { "search": "\/\u0124|\u0126\/", "replace": "H" }, { "search": "\/\u0125|\u0127\/", "replace": "h" }, { "search": "\/\u00cc|\u00cd|\u00ce|\u00cf|\u0128|\u012a|\u012c|\u01cf|\u012e|\u0130|\u0397|\u0389|\u038a|\u0399|\u03aa\/", "replace": "I" }, { "search": "\/\u00ec|\u00ed|\u00ee|\u00ef|\u0129|\u012b|\u012d|\u01d0|\u012f|\u0131|\u03b7|\u03ae|\u03af|\u03b9|\u03ca\/", "replace": "i" }, { "search": "\/\u0134\/", "replace": "J" }, { "search": "\/\u0135\/", "replace": "j" }, { "search": "\/\u0136|\u039a\/", "replace": "K" }, { "search": "\/\u0137|\u03ba\/", "replace": "k" }, { "search": "\/\u0139|\u013b|\u013d|\u013f|\u0141|\u039b\/", "replace": "L" }, { "search": "\/\u013a|\u013c|\u013e|\u0140|\u0142|\u03bb\/", "replace": "l" }, { "search": "\/\u00d1|\u0143|\u0145|\u0147|\u039d\/", "replace": "N" }, { "search": "\/\u00f1|\u0144|\u0146|\u0148|\u0149|\u03bd\/", "replace": "n" }, { "search": "\/\u00d2|\u00d3|\u00d4|\u00d5|\u014c|\u014e|\u01d1|\u0150|\u01a0|\u00d8|\u01fe|\u039f|\u038c|\u03a9|\u038f\/", "replace": "O" }, { "search": "\/\u00f2|\u00f3|\u00f4|\u00f5|\u014d|\u014f|\u01d2|\u0151|\u01a1|\u00f8|\u01ff|\u00ba|\u03bf|\u03cc|\u03c9|\u03ce\/", "replace": "o" }, { "search": "\/\u0154|\u0156|\u0158|\u03a1\/", "replace": "R" }, { "search": "\/\u0155|\u0157|\u0159|\u03c1\/", "replace": "r" }, { "search": "\/\u015a|\u015c|\u015e|\u0160|\u03a3\/", "replace": "S" }, { "search": "\/\u015b|\u015d|\u015f|\u0161|\u017f|\u03c3|\u03c2\/", "replace": "s" }, { "search": "\/\u0162|\u0164|\u0166\u03a4\/", "replace": "T" }, { "search": "\/\u0163|\u0165|\u0167|\u03c4\/", "replace": "t" }, { "search": "\/\u00d9|\u00da|\u00db|\u0168|\u016a|\u016c|\u016e|\u0170|\u0172|\u01af|\u01d3|\u01d5|\u01d7|\u01d9|\u01db\/", "replace": "U" }, { "search": "\/\u00f9|\u00fa|\u00fb|\u0169|\u016b|\u016d|\u016f|\u0171|\u0173|\u01b0|\u01d4|\u01d6|\u01d8|\u01da|\u01dc|\u03c5|\u03cd|\u03cb\/", "replace": "u" }, { "search": "\/\u00dd|\u0178|\u0176|\u03a5|\u038e|\u03ab\/", "replace": "Y" }, { "search": "\/\u00fd|\u00ff|\u0177\/", "replace": "y" }, { "search": "\/\u0174\/", "replace": "W" }, { "search": "\/\u0175\/", "replace": "w" }, { "search": "\/\u0179|\u017b|\u017d|\u0396\/", "replace": "Z" }, { "search": "\/\u017a|\u017c|\u017e|\u03b6\/", "replace": "z" }, { "search": "\/\u00c6|\u01fc\/", "replace": "AE" }, { "search": "\/\u00df\/", "replace": "ss" }, { "search": "\/\u0132\/", "replace": "IJ" }, { "search": "\/\u0133\/", "replace": "ij" }, { "search": "\/\u0152\/", "replace": "OE" }, { "search": "\/\u0192\/", "replace": "f" }, { "search": "\/\u03b8\/", "replace": "th" }, { "search": "\/\u03c7\/", "replace": "x" }, { "search": "\/\u03c6\/", "replace": "f" }, { "search": "\/\u03be\/", "replace": "ks" }, { "search": "\/\u03c0\/", "replace": "p" }, { "search": "\/\u03b2\/", "replace": "v" }, { "search": "\/\u03bc\/", "replace": "m" }, { "search": "\/\u03c8\/", "replace": "ps" }]

    $(input_form).on('keyup', function() {
        value = $(input_form).val();

        if (!value.length) return;

        space_character = space_character || '-';
        var rx = /[a-z]|[A-Z]|[0-9]|[áàâąбćčцдđďéèêëęěфгѓíîïийкłлмñńňóôóпúùûůřšśťтвýыžżźзäæœчöøüшщßåяюжαβγδεέζηήθιίϊκλμνξοόπρστυύϋφχψωώ]/,
            value = value.toLowerCase(),
            /*chars = foreign_characters,*/
            space_regex = new RegExp('[' + space_character + ']+', 'g'),
            space_regex_trim = new RegExp('^[' + space_character + ']+|[' + space_character + ']+$', 'g'),
            search, replace;


        // If already a slug then no need to process any further
        if ($('.lock-slug').is(':checked') === false) {
            if (!rx.test(value)) {
                slug = value;
            } else {
                value = $.trim(value);

                for (var i = chars.length - 1; i >= 0; i--) {
                    // Remove backslash from string
                    search = chars[i].search.replace(new RegExp('/', 'g'), '');
                    replace = chars[i].replace;

                    // create regex from string and replace with normal string
                    value = value.replace(new RegExp(search, 'g'), replace);
                }

                slug = value.replace(/[^-a-z0-9~\s\.:;+=_]/g, '')
                    .replace(/[\s\.:;=+]+/g, space_character)
                    .replace(space_regex, space_character)
                    .replace(space_regex_trim, '');
            }

            $(output_form).val(slug);
        }
    });
}

jQuery(document).ready(function($) {
    $('.assets-manager-iframe-btn').fancybox({
        'width': 800,
        'height': 600,
        'type': 'iframe',
        'autoScale': false
    });

    //
    // Handles message from ResponsiveFilemanager
    //
    function OnMessage(e) {
        var event = e.originalEvent;
        // Make sure the sender of the event is trusted
        if (event.data.sender === 'responsivefilemanager') {
            if (event.data.field_id) {
                var fieldID = event.data.field_id;
                var url = event.data.url;
                $('#' + fieldID).val(url).trigger('change');
                $.fancybox.close();

                // Delete handler of the message from ResponsiveFilemanager
                $(window).off('message', OnMessage);
            }
        }
    }

});

function is_valid_video(url) {
    if (url.match(/^http:\/\/(?:(www|[a-z0-9]{1,3})\.)?youtube.com\/watch\?(?=.*v=\w+)(?:\S+)?$/))
        return true;

    if (url.match(/^http:\/\/(www\.)?vimeo\.com\/(\d+)$/))
        return true;

    return false;
}