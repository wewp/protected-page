(function ($) {
    if ($) {
        $(function () {
            $('.reportrange').each(function (index, element) {
                // var start = moment().subtract(29, 'days');
                // var end = moment();
                var start = moment();
                var end = moment().add(1, 'M');
                var td = element.closest('td');
                var startTimeInput = td.querySelector('[name="start_time"]');
                var endTimeInput = td.querySelector('[name="end_time"]');

                function cb(start, end) {
                    if (start || end) {
                        startTimeInput.value = start.format('YYYY-MM-DD HH:mm:ss');
                        endTimeInput.value = end.format('YYYY-MM-DD HH:mm:ss');
                        //$(element).find('span').html(start.format('MMMM D, YYYY hh:mm A') + ' - ' + end.format('MMMM D, YYYY hh:mm A'));
                    }
                }

                $(element).find('[name="datetimes"]').daterangepicker({
                    timePicker: true,
                    startDate: start,
                    endDate: end,
                    ranges: {
                        'Tomorrow': [moment(), moment().add(1, 'd')],
                        'One Week': [moment(), moment().add(7, 'd')],
                        'One Month': [moment(), moment().add(1, 'M')],
                        'One Year': [moment(), moment().add(1, 'Y')],
                    },
                    locale: {
                        format: 'MM/DD/YYYY hh:mm A'
                    }
                }, cb);

                cb(start, end);
            });

            function initPasswordsTable() {
                var passwordsTable = document.getElementById('passwords-table');
                if (passwordsTable) {
                    passwordsTable.addEventListener('submit', function (event) {
                        var target = event.explicitOriginalTarget || event.relatedTarget ||
                            document.activeElement || {};

                        if(target.classList.contains('delete')){
                            deletePasswordRow(event);
                        }

                        if(target.classList.contains('update')){
                            updatePasswordRow(event);
                        }

                        event.preventDefault();
                        return false;
                    });
                }
            }

            function deletePasswordRow(event){
                var target = event.target;
                var parent = target.closest('tr');
                if (!parent) {
                    return;
                }

                var updateButton = parent.querySelector('button.update');
                var removeButton = parent.querySelector('button.delete');
                updateButton.disabled = true;
                removeButton.disabled = true;
                var spinner = parent.querySelector('.spinner');
                if (spinner) {
                    spinner.classList.add('is-active');
                }

                var theid = parent.querySelector('[name="theid"]').value;
                $.ajax({
                    method: 'POST',
                    url: ajaxurl,
                    data: {action:'delete_password',theid:theid},
                    dataType: "json",
                    success: function (response) {
                        if (response.success) {
                            const successMessage = (typeof is_rtl !=='undefined' && is_rtl )? 'הקוד נמחק בהצלחה' : 'Password deleted successfully';
                            var success = notifier.success(successMessage, {
                                labels: {
                                    success: (typeof is_rtl !=='undefined' && is_rtl )? 'בוצע בהצלחה' : 'Success'
                                }
                            });
                        }
                        if (spinner) {
                            spinner.classList.remove('is-active');
                        }

                        updateButton.disabled = false;
                        removeButton.disabled = false;

                        parent.remove();
                    },
                    error: function (errorThrown) {
                        if (spinner) {
                            spinner.classList.remove('is-active');
                        }

                        updateButton.disabled = false;
                        removeButton.disabled = false;
                    }
                });
            }

            function updatePasswordRow(event) {
                var target = event.target;
                var parent = target.closest('tr');
                if (!parent) {
                    return;
                }
                var data = new FormData(target);
                var updateButton = parent.querySelector('button.update');
                var removeButton = parent.querySelector('button.delete');

                updateButton.disabled = true;
                removeButton.disabled = true;
                var spinner = parent.querySelector('.spinner');
                if (spinner) {
                    spinner.classList.add('is-active');
                }

                $.ajax({
                    method: 'POST',
                    url: ajaxurl,
                    data: data,
                    dataType: "json",
                    processData: false,
                    contentType: false,
                    success: function (response) {
                        if (response.success) {
                            const successMessage = (typeof is_rtl !=='undefined' && is_rtl )? 'הקוד עודכן בהצלחה' : 'Password updated successfully';
                            var success = notifier.success(successMessage, {
                                labels: {
                                    success: (typeof is_rtl !=='undefined' && is_rtl )? 'בוצע בהצלחה' : 'Success'
                                }
                            });
                        }
                        if (spinner) {
                            spinner.classList.remove('is-active');
                        }

                        updateButton.disabled = false;
                        removeButton.disabled = false;
                    },
                    error: function (errorThrown) {
                        if (spinner) {
                            spinner.classList.remove('is-active');
                        }

                        updateButton.disabled = false;
                        removeButton.disabled = false;
                    }
                });
            }

            initPasswordsTable();

        });
    }
})(typeof jQuery !== 'undefined' ? jQuery : null);
