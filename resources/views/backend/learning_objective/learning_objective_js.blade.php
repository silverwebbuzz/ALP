<script>
    $(function(){
        $(document).on("click", 'input[name="is_extra_skills"]', function (e) {
            if ($(this).prop("checked") == true) {
                $(".extra-objectives-skill-portion").show();
            } else {
                $(".extra-objectives-skill-portion").hide();
            }
        });

        /**
         * USE : Trigger on click add more skill button
         */
        $(document).on("click", "#addMoreLearningObjectivesSkill", function (e) {
            $MoreAdminHtml = "";
            $MoreAdminHtml = '<div class="form-group col-md-3 d-flex">\
                                <input type="text" class="form-control" name="LearningObjectivesExtraSkills[]" placeholder="Ener skills">\
                                <span class="error-msg subadminname_err"></span>\
                                <a class="removeMoreExtraSkill btn btn-sm">X</a>\
                            </div>';
            $(".add-more-skills").append($MoreAdminHtml);
        });

        $(document).on("click", ".removeMoreExtraSkill", function (e) {
            var DataId = $(this).data("id");
            var SkillDiv = $(this).parent("div");
            if(DataId){
                $.confirm({
                    title: DELETE_SKILL,
                    content: CONFIRMATION,
                    autoClose: "Cancellation|8000",
                    buttons: {
                        deleteUser: {
                            text: DELETE_SKILL,
                            action: function () {
                                $("#cover-spin").show();
                                $.ajax({
                                    url: BASE_URL + "/learning-objective/skill/delete/"+DataId,
                                    type: "GET",
                                    success: function (response) {
                                        $("#cover-spin").hide();
                                        var data = JSON.parse(JSON.stringify(response));
                                        if(data.status === "success"){
                                            toastr.success(data.message);
                                            SkillDiv.remove();
                                        }else{
                                            toastr.error(data.message);
                                        }
                                    },
                                    error: function (response) {
                                        ErrorHandlingMessage(response);
                                    },
                                });
                            },
                        },
                        Cancellation: function () {},
                    },
                });
            }else{
                $(this).parent("div").remove();
            }
        });
    });
</script>