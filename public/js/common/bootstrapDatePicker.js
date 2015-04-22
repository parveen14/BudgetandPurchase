/**
 * @author Vipul Sharma
 * @since 11 Nov 2014
 */
$(document).ready(function() {
    $(".start_date").datetimepicker({
        format: "dd MM yyyy",
        weekStart: 1,
        todayBtn: 1,
        autoclose: 1,
        todayHighlight: 1,
        startView: 2,
        minView: 2,
        forceParse: 0,
        startDate: new Date()
    });

    $('.start_date').datetimepicker().on('changeDate', function(ev) {
        $(".end_date input").val('');
        $(".end_date").datetimepicker("remove");
        var startDate = $(this).datetimepicker('getDate');
        $(".end_date").datetimepicker({
            format: "dd MM yyyy",
            weekStart: 1,
            todayBtn: 1,
            autoclose: 1,
            todayHighlight: 1,
            startView: 2,
            minView: 2,
            forceParse: 0,
            startDate: startDate
        });
    });
    
    $(".date_of_birth").datetimepicker({
        format: "yyyy-mm-dd",
        weekStart: 1,
        todayBtn: false,
        autoclose: 1,
        todayHighlight: 1,
        startView: 2,
        minView: 2,
        forceParse: 0,
    });
    
    $(".start_date_pie").datetimepicker({
        format: "dd MM yyyy",
        weekStart: 1,
        todayBtn: 1,
        autoclose: 1,
        todayHighlight: 1,
        startView: 2,
        minView: 2,
        forceParse: 0
    });

    $('.start_date_pie').datetimepicker().on('changeDate', function(ev) {
        $(".end_date_pie input").val('');
        $(".end_date_pie").datetimepicker("remove");
        var startDate = $(this).datetimepicker('getDate');
        $(".end_date_pie").datetimepicker({
            format: "dd MM yyyy",
            weekStart: 1,
            todayBtn: 1,
            autoclose: 1,
            todayHighlight: 1,
            startView: 2,
            minView: 2,
            forceParse: 0,
            startDate: startDate
        });
    });
});