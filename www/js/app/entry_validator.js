/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

ChurchApp.onDynamicPageLoaded(function (selector) {

    //we will have to remove all inline pages markup from the selector
    //and then append them to the body. this is because the content of this
    //selector is in another page and so that will render the inline pages
    //useless. we need to relocate them.
    var d = $(selector).find("[data-role='page']");//find the inline pages

    $(d).each(function () {
        var p = $(this);
        $(p).remove();//remove the intended inline page from this current page.
        $("body").append($(p));//append this intended inline page to the body so will can access it.
    });

    /*$("#test_table_click").on("click", function () {
     
     alert(selector.html());
     alert($("body").html());
     alert("test_table_click");
     
     });*/

});


$(document).ready(function () {

    $("#btn_next_parish_question").on("click", function () {

        if (document.getElementById('radio-choice-v-2a').checked
                || document.getElementById('radio-choice-v-2b').checked
                || document.getElementById('radio-choice-v-2c').checked
                || document.getElementById('radio-choice-v-2d').checked
                || document.getElementById('radio-choice-v-2e').checked
                || document.getElementById('radio-choice-v-2f').checked) {

            document.getElementById("next_parish_question_error").style.display = "none";

            document.getElementById("assign-to-parish-note").innerHTML = "If your selections above are correct your parish will be enlisted in the dropdown list below. Otherwise your parish is not yet registered with our service, so please check back later.";

            $("#assign-to-parish-explain").show();

            $("#assign-to-parish-under-national-cont").show();
            $("#assign-to-parish-under-region-cont").show();
            $("#assign-to-parish-under-province-cont").show();
            $("#assign-to-parish-under-zone-cont").show();
            $("#assign-to-parish-under-area-cont").show();

            ChurchApp.enableFindParishes = false;//disable for now - we don't want unnecessary requests sent to the server. 

            ChurchApp.comboBoxClear("assign-to-parish-under-national");
            ChurchApp.comboBoxClear("assign-to-parish-under-region");
            ChurchApp.comboBoxClear("assign-to-parish-under-province");
            ChurchApp.comboBoxClear("assign-to-parish-under-zone");
            ChurchApp.comboBoxClear("assign-to-parish-under-area");
            ChurchApp.comboBoxClear("assign-to-parish-name");

            if (document.getElementById('radio-choice-v-2a').checked) {

                document.getElementById("assign-to-parish-note").innerHTML = "If your selection above is correct your parish will be enlisted in the drop-down list below. Otherwise your parish is not yet registered with our service, so please check back later.";

                $("#assign-to-parish-under-region-cont").hide();
                $("#assign-to-parish-under-province-cont").hide();
                $("#assign-to-parish-under-zone-cont").hide();
                $("#assign-to-parish-under-area-cont").hide();

            } else if (document.getElementById('radio-choice-v-2b').checked) {

                $("#assign-to-parish-under-province-cont").hide();
                $("#assign-to-parish-under-zone-cont").hide();
                $("#assign-to-parish-under-area-cont").hide();

            } else if (document.getElementById('radio-choice-v-2c').checked) {

                $("#assign-to-parish-under-zone-cont").hide();
                $("#assign-to-parish-under-area-cont").hide();

            } else if (document.getElementById('radio-choice-v-2d').checked) {
                $("#assign-to-parish-under-area-cont").hide();
            } else if (document.getElementById('radio-choice-v-2e').checked) {
                //do nothing.
            } else if (document.getElementById('radio-choice-v-2f').checked) {
                //do nothing also.
            }

            $(":mobile-pagecontainer").pagecontainer("change", "#assign-to-parish-question-page", {
                transition: 'slide',
                //changeHash: false,
                // reverse: true,
                showLoadMsg: true
            });

            ChurchApp.post("php/Divisions.php", "",
                    function (data) {
                        alert(data);
                        var json = JSON.parse(data);

                        if (json.status === "success") {
                            ChurchApp.enableFindParishes = true; //enable
                            ChurchApp.comboBoxPopulate("assign-to-parish-under-national", json.data.national);
                            ChurchApp.comboBoxPopulate("assign-to-parish-under-region", json.data.region);
                            ChurchApp.comboBoxPopulate("assign-to-parish-under-province", json.data.province);
                            ChurchApp.comboBoxPopulate("assign-to-parish-under-zone", json.data.zone);
                            ChurchApp.comboBoxPopulate("assign-to-parish-under-area", json.data.area);
                        } else {
                            ChurchApp.alertResponse(json);
                        }
                    },
                    function (data, r, error) {

                    });

        } else {
            document.getElementById("next_parish_question_error").style.display = "block";
        }
    });


    $("#assign-to-parish-under-national").on("change", function () {
        ChurchApp.parishesOnDivisionChange();
    });

    $("#assign-to-parish-under-region").on("change", function () {
        ChurchApp.parishesOnDivisionChange();
    });

    $("#assign-to-parish-under-province").on("change", function () {
        ChurchApp.parishesOnDivisionChange();
    });

    $("#assign-to-parish-under-zone").on("change", function () {
        ChurchApp.parishesOnDivisionChange();
    });

    $("#assign-to-parish-under-area").on("change", function () {
        ChurchApp.parishesOnDivisionChange();
    });


    $(document).on("pagecontainerbeforeshow", function (event, ui) {

        if (ChurchApp.Util.endsWithStr(ui.toPage[0].baseURI, "/authorization.html")) {

            if (ChurchApp.userPivilegeFeatures === null) {

                $.mobile.loading("show", {
                    text: "Retrieving users privileges. Please wait... ",
                    textVisible: true,
                    theme: "a",
                    textonly: false,
                    html: ""
                });

                ChurchApp.post("php/GetPrivilegeHomePages.php", {},
                        function (data) {

                            var json = JSON.parse(data);
                            if (json.status === "success") {
                                ChurchApp.createUserPrivilegeFeatures(json.data);
                                $.mobile.loading("hide");
                            } else if (json.status === "error") {
                                $.mobile.loading("hide");
                                ChurchApp.alertResponse(json, true);
                            } else {
                                $.mobile.loading("hide");
                                ChurchApp.alertResponse(json);
                            }
                        },
                        function (data, r, error) {
                            $.mobile.loading("hide");
                        })
            }
        }
    });

    /**
     * Use this method to process events for the case of pages
     *  that are not dynamically created.
     *  
     *  WARNING!!! DO NOT USE THIS METHOD TO PROCESS EVENTS IF THE
     *  PAGE CONTENT WAS CREATED DYNAMICALLY. USE onDynamicPageLoaded(callbackFunc) 
     *  FUNCTION OF THE ChurchApp INSTEAD.
     *  
     */
    $(document).bind("pagecreate", function (e, data) {

        // $("#home-page-admin-content").html("see the news content");


        //custom validation to enter fields that are visible and require entry.
        jQuery.validator.addMethod("enterFieldShowing", function (value, element) {
            return !($(element).is(":visible") && value === "");
        }, 'Please enter this field.');

        jQuery.validator.addMethod("checkVerificationCode", function (value, element) {
            return ChurchApp.adminVerifyCode.code === value && value !== "";
        }, 'Invalid Verification Code.');

        jQuery.validator.addMethod("checkUsernameVerificationCodePair", function (value, element) {
            return (ChurchApp.adminVerifyCode.username === value
                    && value !== "")
                    || ChurchApp.adminVerifyCode.username === "";
        }, 'Invalid Username/Verificaton-Code pair');

        jQuery.validator.addMethod("checkEmailVerificationCodePair", function (value, element) {
            return (ChurchApp.adminVerifyCode.email === value
                    && value !== "")
                    || ChurchApp.adminVerifyCode.email === "";
        }, 'Invalid Email/Verificaton-Code pair');

        jQuery.validator.addMethod("workaroundComboboxValidateEmpty", function (value, element) {
            //alert($("#"+$(element).attr("id")).val());
            return $("#" + $(element).attr("id")).val() !== "";
        }, 'Please select an item.');


        //sign up form validation
        $("#sign_up_form").validate({
            //validation rules
            rules: {
                txt_sign_up_first_name: "required",
                txt_sign_up_last_name: "required",
                txt_sign_up_username: {
                    required: true,
                    minlength: 3,
                    remote: {
                        url: "php/check-username.php",
                        type: "post",
                        data: {
                            txt_sign_up_username: function () {
                                return $("#txt_sign_up_username").val();
                            }
                        }
                    }
                },
                txt_sign_up_password: {
                    required: true,
                    minlength: 6
                },
                txt_sign_up_email: {
                    required: true,
                    email: true
                },
                txt_sign_up_password_again: {
                    required: true,
                    equalTo: "#txt_sign_up_password"
                }

            },
            //custom validation message
            messages: {
                txt_sign_up_password_again: "Passwords did not match!",
                txt_sign_up_username: {
                    remote: "Username already taken!"
                },
                txt_sign_up_email: {
                    email: "Invalid email address - e.g name@domain.com"
                }
            },
            //submit sign up form
            submitHandler: function (form) {

                ChurchApp.postForm(form,
                        function (data) {//done
                            var json = JSON.parse(data);
                            if (json.status === "success") {
                                ChurchApp.assignParishToUser({
                                    username: json.data.username,
                                    firstName: json.data.firstName,
                                    lastName: json.data.lastName,
                                    email: json.data.email,
                                    stepsAwayFromCompleteSignup: "Thank you! You are few steps alway from completing your signup process.",
                                });
                            } else {
                                ChurchApp.alertResponse(json, true);
                            }
                        },
                        function (data, r, error) {//fail

                        });
            }
        });


        //login validation
        $("#login_form").validate({
            //validation rules
            rules: {
                txt_login_username: "required",
                txt_login_password: "required"
            },
            //custom validation message
            messages: {
                txt_login_username: "Your username is required!",
                txt_login_password: "Your password is required!"
            },
            //submit login form
            submitHandler: function (form) {
                //alert($(form).serialize());
                ChurchApp.postForm(form,
                        function (data) {//done
                            alert(data);
                            var json = JSON.parse(data);

                            if (json.status === "success") {
                                ChurchApp.loginByPriviledge(json.data);
                            } else {
                                ChurchApp.alertResponse(json, true);

                                ChurchApp.loginByPriviledge(//TESTING!!!
                                        {
                                            user: {group: "Admin", role: "super"}, //TESTING!!!
                                            carouselImages: ["img/images0.jpg", "img/images1.jpg", "img/images2.jpg", "img/images3.jpg", "img/images4.jpg", "img/images5.jpg"], //images                                          
                                            newsContent: newsContentTest()//TESTING!!!
                                        });
                            }

                        },
                        function (data, r, error) {//fail

                        });
            }

        });


    });
});
function newsContentTest() {
    return{
        newsGroupList: [[newsItemTest(1), newsItemTest(2), newsItemTest(3)],
            [newsItemTest(1), newsItemTest(2)]]
    };
}

function newsItemTest(n) {
    return{
        newsTitle: "The News Title " + n,
        newsImages: [""], //list of new images
        newsDate: "July 5, 2016",
        newsTime: "2:33",
        newsTimeAMPM: "PM",
        newsIntro: "The sun shall soon shine!",
        newsBody: "This is news content body - It says glorious days are ahead of us.This is news content body - It says glorious days are ahead of us.This is news content body - It says glorious days are ahead of us.",
    };
}
