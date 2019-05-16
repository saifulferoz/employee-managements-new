import 'bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js';

export default (function ($) {
    if ($().datepicker) {
        $('.date-picker, .input-daterange').datepicker({
            autoclose: true,
            format: "yyyy-mm-dd",
            todayBtn: "linked"
        });
    }
}(jQuery))