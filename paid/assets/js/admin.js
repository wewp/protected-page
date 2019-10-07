(function ($) {
    if ($) {
        $(function () {

            if (typeof notifier === 'undefined' && AWN) {
                window.notifier = new AWN.default({

                });
            }

            // for copy password
            if (ClipboardJS) {
                const clipboard = new ClipboardJS('.clipboard-btn');
            }

            // for switcher (pages_table.php)
            document.addEventListener('change', function (event) {
                var target = event.target;
                var parent = target.closest('.is-page-protected');
                if (!parent) {
                    return;
                }
                var tr = target.closest('tr');
                var spinner = parent.querySelector('.spinner');

                var data = {
                    'action': 'toggle_protected_page',
                    'is_page_protected': target.checked,
                    'page_id': target.dataset.page_id
                };

                if (spinner) {
                    spinner.classList.add('is-active');
                }
                $.ajax({
                    method: 'POST',
                    url: ajaxurl,
                    data: data,
                    dataType: "json",
                    success: function (response) {
                        if (response.success) {
                            const _success = (typeof is_rtl !=='undefined' && is_rtl )? 'מאובטח' : 'protected';
                            const notSuccess = (typeof is_rtl !=='undefined' && is_rtl ) ? 'לא מאובטח' : 'Not protected';
                            var successMessage = target.dataset.page_title + ' <br/> ' + (data.is_page_protected ? ' <b>' + _success + '</b>' : '<b>' + notSuccess + '</b>');
                            var success = notifier.success(successMessage, {
                                labels: {
                                    success: (typeof is_rtl !=='undefined' && is_rtl )? 'עודכן בהצלחה' : 'Success'
                                }
                            });
                            if (spinner) {
                                spinner.classList.remove('is-active');
                            }
                            tr.classList.toggle('is-protected', target.checked);
                        }
                    },
                    error: function (errorThrown) {
                    }
                });
            });

            // for switcher (pages_table.php)
            document.addEventListener('change', function (event) {
                var target = event.target;
                var parent = target.closest('.is-protected-all-page');
                if (!parent) {
                    return;
                }
                var tr = target.closest('tr');
                var spinner = parent.querySelector('.spinner');

                var data = {
                    'action': 'toggle_protected_all_page',
                    'is_all_page_protected': target.checked,
                    'page_id': target.dataset.page_id
                };

                if (spinner) {
                    spinner.classList.add('is-active');
                }
                $.ajax({
                    method: 'POST',
                    url: ajaxurl,
                    data: data,
                    dataType: "json",
                    success: function (response) {
                        if (response.success) {
                            const _success = (typeof is_rtl !=='undefined' && is_rtl )? 'מאובטח' : 'protected';
                            const notSuccess = (typeof is_rtl !=='undefined' && is_rtl ) ? 'לא מאובטח' : 'Not protected';
                            var successMessage = target.dataset.page_title + ' <br/> ' + (data.is_page_protected ? ' <b>' + _success + '</b>' : '<b>' + notSuccess + '</b>');
                            var success = notifier.success(successMessage, {
                                labels: {
                                    success: (typeof is_rtl !=='undefined' && is_rtl )? 'עודכן בהצלחה' : 'Success'
                                }
                            });
                            if (spinner) {
                                spinner.classList.remove('is-active');
                            }
                            // tr.classList.toggle('is-protected', target.checked);
                        }
                    },
                    error: function (errorThrown) {
                    }
                });
            });
        });
    }
})(typeof jQuery !== 'undefined' ? jQuery : null);
