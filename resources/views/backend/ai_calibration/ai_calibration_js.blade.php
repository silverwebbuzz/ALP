<script>
$( document ).ready(function() {
    /**
     * USE : Set multiselect dropdown for select schools
     */
    $("#select-ai-calibration-schools").multiselect({
        enableHTML: true,
        templates: {
            filter: '<li class="multiselect-item multiselect-filter"><div class="input-group mb-3"><div class="input-group-prepend"><span class="input-group-text"><i class="fa fa-search"></i></span></div><input class="form-control multiselect-search" type="text" /></div></li>',
            filterClearBtn:'<span class="input-group-btn"><button class="btn btn-default multiselect-clear-filter" type="button"><i class="fa fa-times"></i></button></span>',
        },
        columns: 1,
        placeholder: SELECT_SCHOOL,        
        includeSelectAllOption: true,
        enableFiltering: true,
    });

    /**
     * USE : Set multiselect dropdown for select students
     */
    $("#select-ai-calibration-students").multiselect({
        enableHTML: true,
        templates: {
            filter: '<li class="multiselect-item multiselect-filter"><div class="input-group mb-3"><div class="input-group-prepend"><span class="input-group-text"><i class="fa fa-search"></i></span></div><input class="form-control multiselect-search" type="text" /></div></li>',
            filterClearBtn:'<span class="input-group-btn"><button class="btn btn-default multiselect-clear-filter" type="button"><i class="fa fa-times"></i></button></span>',
        },
        columns: 1,
        placeholder: SELECT_STUDENT,
        includeSelectAllOption: true,
        enableFiltering: true,
    });

    /**
     * USE Set Date Formate ai-calibration start date and end date picker
     */
    $("#ai-calibration-start-date, #ai-calibration-end-date").datepicker({
        dateFormat: "dd/mm/yy",
        maxDate:0,
        changeMonth: true,
        changeYear: true,
        yearRange: "1950:" + new Date().getFullYear(),
    });
        
    // $(document).on('click', '.step-headings', function() {
    //     var currentStep = parseInt($(this).attr('data-tabid'))-1;
    //     if(currentStep!=0){
    //         $(".form-steps.step"+currentStep+" .next-button").click();
    //     }
    // });

    // // event fire on click step button
    // $(document).on('click', '.step-headings', function() {
    //     var currentStep = parseInt($(this).attr('data-tabid'))-1;
    //     if(currentStep!=0)
    //     {
    //         $(".form-steps.step"+currentStep+" .next-button").click();
    //     }
    // });

    // event fire on click next button
    $(document).on('click', '.next-button', function() {
        var currentStep = $(this).attr('data-stepid');
        var nextStep = (parseInt(currentStep) + 1);
        if(checkValidation(currentStep)){
            if(currentStep == 1){
                $("#cover-spin").show();
                GenerateAICalibration();
                $('.form-steps').hide();
                $('.step-headings').removeClass('tab_active');
                $('.section-step'+nextStep).addClass('tab_active');
                $('.step'+nextStep).show();
            }

            if(currentStep == 2){
                // Check update confirmation
                //if($('.isUpdateNonIncludedQuestions').prop('checked')){
                console.log($('.isUpdateNonIncludedQuestions:checked').val());
                if($('.isUpdateNonIncludedQuestions:checked').val()){
                    $('#isUpdateNonIncludedQuestionsError').hide();
                }else{
                    $('#isUpdateNonIncludedQuestionsError').show();
                    return false;
                }
                var CalibrationReportId = $('#calibration_report_id').val();
                ExecuteCalibrationAdjustment(CalibrationReportId);
                $("#cover-spin").show();
                $('.form-steps').hide();
                $('.step-headings').removeClass('tab_active');
                $('.section-step'+nextStep).addClass('tab_active');
                $('.step'+nextStep).show();

            }
        }else{
            setTimeout(function () {
                $('.step-headings').removeClass('tab_active');
                $('.section-step'+currentStep).addClass('tab_active');
                $('.section-step'+currentStep).click();
            },200)
        }
    });
    
    $(document).on('click', '.admin-tab', function() {
        var TabId = $(this).attr('data-tabid');
        $('.form-steps').hide();
        $('.step-headings').removeClass('tab_active');
        $('.section-step'+TabId).addClass('tab_active');
        $('.step'+TabId).show();
    });

    // Event fire on click previous button
    $(document).on('click', '.previous-button', function() {
        var currentStep = $(this).attr('data-stepid');
        var previousStep = (parseInt(currentStep) - 1);
        $('.form-steps').hide();
        $('.step-headings').removeClass('tab_active');
        $('.section-step'+previousStep).addClass('tab_active');
        $('.step'+previousStep).show();
    });

    /**
     * Use : Get Student list based on selected schools
     */
    $(document).on('change', '#select-ai-calibration-schools', function() {
        // Get Selected school ids
        var SchoolIds = $("select[name='schoolIds[]']").val();
        if(SchoolIds!=""){
            $("#cover-spin").show();
            $.ajax({
                url: BASE_URL + "/ai-calibration/student-list",
                method: "GET",
                data: {
                    school_id: SchoolIds
                },
                success: function (response) {
                    if(response.data){
                        $('#select-ai-calibration-students').html(response.data);
                        //$("#select-ai-calibration-students").find('option').attr('selected','selected');
                        $("#select-ai-calibration-students").multiselect("rebuild");
                    }
                    $("#cover-spin").hide();
                },
                error: function (response) {
                    $("#cover-spin").hide();
                    ErrorHandlingMessage(response);
                }
            });
        }
    });

    /**
     * Trigger : On change update difficulty excluded question
     */
    $(document).on('change', '.isUpdateNonIncludedQuestions', function() {
        if($('.isUpdateNonIncludedQuestions:checked').val()){
            $('#isUpdateNonIncludedQuestionsError').hide();
        }
    });
});

function checkValidation(currentStep) {
    var formIsValid = 0;
    $('.form-steps.step'+currentStep+' label.error').remove();
    switch('step_'+currentStep){
        case 'step_1':
            $('.form-steps.step'+currentStep).find('[name=start_date],[name=end_date],[name="schoolIds[]"],[name="studentIds[]"],[name=result_type]').each(function(){
                var element = $(this).closest('.form-group').css('display');
                if($.trim($(this).val()) == '' && element != 'none' ){
                    var label = $(this).closest('.form-group').find('label:eq(0)').text();
                    $(this).closest('.form-group').append('<label class="error">'+PLEASE_ENTER+' '+label+'</label>');
                    formIsValid++;
                }
            });
            break;
        case 'step_2':
            $('.form-steps.step'+currentStep).find('[name="schoolIds[]"]').each(function(){
                var element = $(this).closest('.form-group').css('display');
                if($.trim($(this).val()) == '' && element != 'none'){
                    var label = $(this).closest('.form-group').find('label:eq(0)').text();
                    $(this).closest('.form-group').append('<label class="error">'+VALIDATIONS.PLEASE_SELECT_SCHOOL+'</label>');
                    formIsValid++;
                }
            });
            break;
        case 'step_3':
            break;
        case 'step_4':
            break;
        default:
    }
    // Check the steps all field is completed then next step open
    if(formIsValid == 0){
        return true;
    }else{
        return false;
    }
}

/**
 * USE : Generate AI-Calibration
 */
function GenerateAICalibration(){
    $("#cover-spin").show();
    $.ajax({
        url: BASE_URL + "/ai-calibration",
        method: "POST",
        async: true,
        data: {
            _token: $('meta[name="csrf-token"]').attr("content"),
            formData: $("#ai-calibration").serialize(),
        },
        success: function (response) {
            var data = JSON.parse(JSON.stringify(response));
            if(data){
                if(data.data.reportHtml){
                    $('#calibration-report-preview').html(data.data.reportHtml);
                    $("#cover-spin").hide();
                }
            }
        },
        error: function (response) {
            ErrorHandlingMessage(response);
        }
    });
}

/**
 * USE : ExecuteCalibrationAdjustment
 */
function ExecuteCalibrationAdjustment(CalibrationReportId){
    if(CalibrationReportId){
        $("#cover-spin").show();
        $.ajax({
            url: BASE_URL + "/ai-calibration/execute-calibration-adjustment/"+CalibrationReportId,
            method: "GET",
            async: true,
            data:{
                isUpdateNonIncludedQuestions : $('.isUpdateNonIncludedQuestions:checked').val(),
            },
            success: function (response) {
                var data = JSON.parse(JSON.stringify(response));
                if(data){
                    if(data.data.CalibrationLogReportHtml){
                        $('#calibration-log-report-preview').html(data.data.CalibrationLogReportHtml);
                    }
                }
                $("#cover-spin").hide();
            },
            error: function (response) {
                ErrorHandlingMessage(response);
            }
        });
    }
}
</script>