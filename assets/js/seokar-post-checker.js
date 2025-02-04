jQuery(document).ready(function($) {
    function checkConditions() {
        var title = $('#title').val();
        var content = tinymce.get('content').getContent({format: 'text'});
        var wordCount = content.split(/\s+/).length;
        var h1Count = (content.match(/<h1[^>]*>/g) || []).length;
        var h2Count = (content.match(/<h2[^>]*>/g) || []).length;
        var h3Count = (content.match(/<h3[^>]*>/g) || []).length;
        var h4Count = (content.match(/<h4[^>]*>/g) || []).length;
        var imgCount = (content.match(/<img[^>]*>/g) || []).length;
        var altCount = (content.match(/<img[^>]*alt=["']([^"']+)["']/g) || []).length;
        var extLinkCount = (content.match(/<a[^>]*href=["'](http|https)[^"']*["']/g) || []).length;
        var intLinkCount = (content.match(/<a[^>]*href=["']\/[^"']*["']/g) || []).length;
        var tableCount = (content.match(/<table[^>]*>/g) || []).length;
        var boldCount = (content.match(/<strong[^>]*>/g) || []).length;
        var videoCount = (content.match(/<video[^>]*>/g) || []).length;

        var conditionsMet = true;

        if (h1Count !== 1) {
            $('#seokar-h1-status').text('✖').css('color', 'red');
            conditionsMet = false;
        } else {
            $('#seokar-h1-status').text('✔').css('color', 'green');
        }

        if (h2Count < 5) {
            $('#seokar-h2-status').text('✖').css('color', 'red');
            conditionsMet = false;
        } else {
            $('#seokar-h2-status').text('✔').css('color', 'green');
        }

        if (h3Count < 7) {
            $('#seokar-h3-status').text('✖').css('color', 'red');
            conditionsMet = false;
        } else {
            $('#seokar-h3-status').text('✔').css('color', 'green');
        }

        if (h4Count < 5) {
            $('#seokar-h4-status').text('✖').css('color', 'red');
            conditionsMet = false;
        } else {
            $('#seokar-h4-status').text('✔').css('color', 'green');
        }

        if (imgCount < 4) {
            $('#seokar-img-status').text('✖').css('color', 'red');
            conditionsMet = false;
        } else {
            $('#seokar-img-status').text('✔').css('color', 'green');
        }

        if (altCount < imgCount) {
            $('#seokar-alt-status').text('✖').css('color', 'red');
            conditionsMet = false;
        } else {
            $('#seokar-alt-status').text('✔').css('color', 'green');
        }

        if (extLinkCount < 2) {
            $('#seokar-ext-link-status').text('✖').css('color', 'red');
            conditionsMet = false;
        } else {
            $('#seokar-ext-link-status').text('✔').css('color', 'green');
        }

        if (intLinkCount < 3) {
            $('#seokar-int-link-status').text('✖').css('color', 'red');
            conditionsMet = false;
        } else {
            $('#seokar-int-link-status').text('✔').css('color', 'green');
        }

        if (tableCount < 1) {
            $('#seokar-table-status').text('✖').css('color', 'red');
            conditionsMet = false;
        } else {
            $('#seokar-table-status').text('✔').css('color', 'green');
        }

        if (boldCount < 1) {
            $('#seokar-bold-status').text('✖').css('color', 'red');
            conditionsMet = false;
        } else {
            $('#seokar-bold-status').text('✔').css('color', 'green');
        }

        if (videoCount < 1) {
            $('#seokar-video-status').text('✖').css('color', 'red');
            conditionsMet = false;
        } else {
            $('#seokar-video-status').text('✔').css('color', 'green');
        }

        if (wordCount < 800) {
            $('#seokar-word-count-status').text('✖').css('color', 'red');
            conditionsMet = false;
        } else {
            $('#seokar-word-count-status').text('✔').css('color', 'green');
        }

        if (!conditionsMet) {
            $('.seokar-validation-message').show();
        } else {
            $('.seokar-validation-message').hide();
        }

        return conditionsMet;
    }

    // Check conditions on post save
    $('#publish').on('click', function(event) {
        if (!checkConditions()) {
            event.preventDefault();
            if (confirm('این پست هنوز واجد الشرایط منتشر شدن نیست. آیا مطمئن هستید که می‌خواهید آن را منتشر کنید؟')) {
                $('#publish').off('click');
                $('#publish').click();
            } else {
                $('.seokar-validation-message').show();
            }
        }
    });

    // Check conditions on content change
    $('#title, #content').on('input', checkConditions);
    $('#postimagediv').on('DOMSubtreeModified', checkConditions);
});
