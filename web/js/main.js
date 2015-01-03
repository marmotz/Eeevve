$(
    function() {
        $('#dateSelector').glDatePicker(
            {
                selectableDateRange: [
                    {
                        from: new Date($('#dateSelector').data('min') * 1000),
                        to  : new Date($('#dateSelector').data('max') * 1000)
                    }
                ],
                onClick: function(el, cell, date) {
                    var strpad = function(n) {
                        return ('0' + n).substr(-2);
                    };

                    window.location = $('#dateSelector').data('baseurl') + date.getFullYear() + '-' + strpad(date.getMonth() + 1) + '-' + strpad(date.getDate());
                }
            }
        );

        $('#channelSelector').change(
            function() {
                window.location = $(this).val();
            }
        );
    }
);
