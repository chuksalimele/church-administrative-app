/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

var ChurchApp = new function () {

    this.user = {};

    this.createSwipeEvents = function (related_div_ids) {
        for (var i = 0; i < related_div_ids.length; i++) {
            var tab_id = "";
            if (typeof related_div_ids[i] === "object") {
                tab_id = related_div_ids[i].id;
            } else {
                tab_id = related_div_ids[i];
            }
            $("#" + tab_id).off("swipeleft swiperight");
            $("#" + tab_id).on("swipeleft swiperight", function (event) {
                showStackTab($(this).attr("id"), event.type, related_div_ids);
            });
        }
    };


    function showStackTab(id, direction, related_div_ids) {
        for (var i = 0; i < related_div_ids.length; i++) {

            var tab_id = "";
            if (typeof related_div_ids[i] === "object") {
                tab_id = related_div_ids[i].id;
            } else {
                tab_id = related_div_ids[i];
            }

            if (tab_id === id) {
                
                if(typeof related_div_ids[i].onBeforeTabChange === "function"
                        && related_div_ids[i].onBeforeTabChange() === false){
                    return; //leave! the tab says it does not want to be changed.
                }
                
                if (direction === "swipeleft") {
                    if (i < related_div_ids.length - 1) {
                        ChurchApp.showTab(false, id);
                        ChurchApp.showTab(true, related_div_ids[i + 1], related_div_ids);
                    }
                } else if (direction === "swiperight") {
                    if (i > 0) {
                        ChurchApp.showTab(false, id);
                        ChurchApp.showTab(true, related_div_ids[i - 1], related_div_ids);
                    }
                }
                break;
            }
        }
    }
    ;

    this.showTab = function (show, idTab, related_div_ids) {
        var id = idTab;
        if (typeof idTab === "object") {
            id = idTab.id;
        }

        if (show) {
            active_tab = $('[data-tab-content-id="' + id + '"]').find("a");
            active_tab.addClass('ui-btn-active');
            $("#" + id).show();
            if(typeof idTab.onTabShow === "function"){
                idTab.onTabShow(idTab.id);
            }
            //make sure the others are hidden
            for (var i = 0; i < related_div_ids.length; i++) {
                var tab_id = "";
                if (typeof related_div_ids[i] === "object") {
                    tab_id = related_div_ids[i].id;
                } else {
                    tab_id = related_div_ids[i];
                }
                if (tab_id !== id) {
                    var active_tab = $('[data-tab-content-id="' + tab_id + '"]').find("a");
                    active_tab.removeClass('ui-btn-active');
                    $("#" + tab_id).hide();
                }
            }
        } else {
            var active_tab = $('[data-tab-content-id="' + id + '"]').find("a");
            active_tab.removeClass('ui-btn-active');
            $("#" + id).hide();
        }
    }
    ;

    this.SpinOpts = {
        lines: 13 // The number of lines to draw
        , length: 20 // The length of each line
        , width: 8 // The line thickness
        , radius: 30 // The radius of the inner circle
        , scale: 0.75 // Scales overall size of the spinner
        , corners: 0.9 // Corner roundness (0..1)
        , color: '#000' // #rgb or #rrggbb or array of colors
        , opacity: 0.15 // Opacity of the lines
        , rotate: 51 // The rotation offset
        , direction: 1 // 1: clockwise, -1: counterclockwise
        , speed: 1.5 // Rounds per second
        , trail: 31 // Afterglow percentage
        , fps: 20 // Frames per second when using setTimeout() as a fallback for CSS
        , zIndex: 2e9 // The z-index (defaults to 2000000000)
        , className: 'spinner' // The CSS class to assign to the spinner
        , top: '50%' // Top position relative to parent
        , left: '0' // Left position relative to parent
        , shadow: true // Whether to render a shadow
        , hwaccel: true // Whether to use hardware acceleration
        , position: 'relative' // Element positioning
    };

    this.spinner = new Spinner(this.SpinOpts);

    this.spinRun = function (el, speed) {
        if (typeof el === "string") {
            el = document.getElementById(el);
        }
        if (typeof speed === "undefined" || speed === null) {
            speed = 1.5;
        }

        ChurchApp.spinner.opts.speed = speed;
        ChurchApp.spinner.spin(el);
    };

    this.spinPause = function (el) {
        if (typeof el === "string") {
            el = document.getElementById(el);
        }
        ChurchApp.spinner.opts.speed = 0;
        ChurchApp.spinner.spin(el);
    };

    this.spinHide = function (el) {
        if (typeof el === "string") {
            el = document.getElementById(el);
        }
        ChurchApp.spinner.spin(false);
    };

    this.userPivilegeFeatures = null;

    this.createPopup = function (obj) {

        if (typeof obj.header === 'undefined') {
            obj.header = "";
        }

        if (typeof obj.footer === 'undefined') {
            obj.footer = "";
        }

        if (typeof obj.body === 'undefined') {
            obj.body = "";
        }

        if (typeof obj.closeButtonPos === 'undefined') {
            obj.closeButtonPos = "";
        }

        var closebtn = "";
        if (obj.closeButtonPos === 'right') {
            closebtn = '<a href="#" data-rel="back" class="ui-btn ui-corner-all ui-btn-a ui-icon-delete ui-btn-icon-notext ui-btn-right">Close</a>';
        } else if (obj.closeButtonPos === 'left') {
            closebtn = '<a href="#" data-rel="back" class="ui-btn ui-corner-all ui-btn-a ui-icon-delete ui-btn-icon-notext ui-btn-left">Close</a>';
        }


        //var popup = '<div data-role="popup" data-overlay-theme="a" data-tolerance="15"></div>';
        //var popup_title = "Edit record";//come back later
        //var header = '<div data-role="header"><div name="countdown_to_auth">-:--</div><h2>' + popup_title + '</h2></div>';
        //var popup = '<div data-role="popup" data-overlay-theme="a" data-tolerance="15"></div>';
        //var footer = '<div data-role="footer"><div class="ui-content" style="font-size:0.75em;"><div name="attempts_update">'
        //      + ChurchApp.attempts_update + ChurchApp.attempts_update_msg_append + '</div><div name="failed_auth">'
        //    + ChurchApp.failed_auth + ChurchApp.failed_auth_msg_append + '</div></div></div>';

        var markup = '<div data-role="popup" data-theme="a">'
                + closebtn
                + '<div data-role="header">' + obj.header + '</div>'
                + obj.body
                + '<div data-role="header">' + obj.footer + '</div>'
                + '</div>';

        var pup = $(markup)
                .appendTo($.mobile.activePage)
                .popup();

        //var popup = $(popup)
        //      .appendTo($.mobile.activePage)
        //    .popup();

        /*if (obj.header !== "") {
         $(obj.header)
         .appendTo(popup)
         .toolbar()
         //.before(closebtn)
         .after(obj.body);
         }else{
         
         }*/

        /* if (obj.footer !== "") {
         $(obj.footer)
         .appendTo(popup)
         .toolbar();
         }*/

        $(pup).trigger('create');

        $(pup).popup("open");
    };

    this.remoteTextFill = function (obj) {
        ChurchApp.remoteFill(obj, setTextField);
    };

    var setTextField = function (id, value) {
        $("#" + id).val(value);
    };

    /**
     * Populate drop-down list using the remoteFile. 
     * @param {type} obj.url
     * @param {type} obj.ids - array or string of target id or ids. 
     * @returns {undefined}
     */
    this.remoteComboBoxFill = function (obj) {
        ChurchApp.remoteFill(obj, ChurchApp.comboBoxPopulate);
    };

    this.remoteFill = function (obj, func)
    {
        ChurchApp.post(obj.url,
                obj.data,
                function (data) {

                    var json = JSON.parse(data);

                    if (json.status === "success") {
                        if (typeof obj.ids === "string") {
                            obj.ids = obj.ids.split(",");
                        } else if (!ChurchApp.Util.isArray(obj.ids)) {
                            return;
                        }

                        for (var i = 0; i < obj.ids.length; i++) {
                            //ChurchApp.comboBoxPopulate(obj.ids[i], json.data);
                            func(obj.ids[i], json.data);
                        }
                    } else {
                        //ChurchApp.alertResponse(json);
                    }
                },
                function (data, r, error) {

                });

    };

    this.createUnviewableFeaturesJSON = function (group_arr) {

        var unview_feat_json = {};
        for (var i = 0; i < group_arr.length; i++) {
            var group = group_arr[i];
            var name = ChurchApp.Util.replaceAllChar(group.toLowerCase(), " ", "_");
            var norm_group = ChurchApp.Util.replaceAllChar(group, "-", " ");
            norm_group = toSentenceCase(norm_group);

            var priv_feat = ChurchApp.userPivilegeFeatures[name];
            var concat_feat = "";
            for (var k = 0; k < priv_feat.length; k++) {
                if (!priv_feat[k].enabled) {
                    concat_feat += priv_feat[k].feature + ",";
                }
            }
            if (concat_feat.charAt(concat_feat.length - 1) === ',') {
                concat_feat = concat_feat.substring(0, concat_feat.length - 1);
            }
            //var obj = {};

            //obj[group] = concat_feat;
            unview_feat_json[group] = concat_feat;
        }

        return unview_feat_json;
    };

    this.createPrivilegeFeaturesElements = function (is_checked, group) {
        var title;

        switch (group) {
            case "accountant":
                {
                    title = "Accountant Access Control";
                }
                break;
            case "admin":
                {
                    title = "Admin Access Control";
                }
                break;
            case "pastor":
                {
                    title = "Pastor Access Control";
                }
                break;
            case "worker":
                {
                    title = "Worker Access Control";
                }
                break;
            case "men_exco":
                {
                    title = "Men Exco Access Control";
                }
                break;
            case "women_exco":
                {
                    title = "Women Exco Access Control";
                }
                break;
            case "youth_exco":
                {
                    title = "Youth Exco Access Control";
                }
                break;
            case "member":
                {
                    title = "Member Access Control";
                }
                break;
            case "children":
                {
                    title = "Children Access Control";
                }
                break;
        }

        var compFeatures = ChurchApp.userPivilegeFeatures[group];

        if (is_checked) {
            // perform operation for checked
            var content = featureInnerContent(title, group, compFeatures);
            $("#authorization_features").prepend(content).trigger('create');
            //remove previous click events for purpose of overiding when we add below
            for (var i = 0; i < compFeatures.length; i++) {
                $('#authorization_features  [name="' + group + "_" + compFeatures[i].feature + '"]').off('click');
            }

            //add click events.
            for (var i = 0; i < compFeatures.length; i++) {
                $('#authorization_features  [name="'
                        + group + "_" + compFeatures[i].feature +
                        '"]').on('click', {
                    group_name: group,
                }, function (event) {
                    var name = event.data.group_name;
                    var comp_features = ChurchApp.userPivilegeFeatures[name];
                    //enable/disable user features
                    for (var k = 0; k < comp_features.length; k++) {
                        if (name + "_" + comp_features[k].feature === $(this).attr("name")) {
                            comp_features[k].enabled = $(this).is(":checked");
                        }
                    }
                });
            }
        }
        else {
            // perform operation for unchecked
            var div_id = createAuthorizationFeaturesId(group);
            $('#' + div_id).remove();
        }
    };


    createAuthorizationFeaturesId = function (id_suffix) {
        return "authorization_features_" + id_suffix;
    };

    featureInnerContent = function (desc, group, arrFeature) {

        var div_id = createAuthorizationFeaturesId(group);

        var html = '<div data-role="collapsible" id="' + div_id + '" ><h3>' + desc + '</h3>';
        html += '<div data-role="controlgroup">';

        for (var i = 0; i < arrFeature.length; i++) {
            var descNameWithoutSpace = ChurchApp.Util.replaceAllChar(arrFeature[i].feature, " ", "_");
            var id = div_id + "_" + descNameWithoutSpace;
            var checked = "";
            if (arrFeature[i].enabled) {
                checked = "checked";
            }

            html += '<label for="' + id + '">' + arrFeature[i].feature + '</label>';
            html += '<input name="' + group + "_" + arrFeature[i].feature + '" id="' + id + '" ' + checked + ' type="checkbox">';
        }

        html += "</div>";
        html += "</div>";
        return html;
    };

    this.createUserPrivilegeFeatures = function (data) {
        ChurchApp.userPivilegeFeatures = [];
        for (var i = 0; i < data.length; i++) {

            var group = data[i].group;
            var homePage = data[i].homePage;
            var els = $(homePage).find('[data-privilege^="privilege-"]');

            var priv_feat = [];

            els.each(function (index, element) {
                //alert(index +" " +element);
                var attr_value = $(this).attr('data-privilege');
                var feature = normalizeDataPrivilege(attr_value);
                priv_feat[index] = {
                    feature: feature,
                    enabled: true,
                };

                var attr_priv_disabled = $(this).attr('data-privilege-disabled');
                if (attr_priv_disabled === "false"
                        || typeof attr_priv_disabled === "undefined") {
                    priv_feat[index].enabled = true;
                } else {
                    priv_feat[index].enabled = false;
                }
                ChurchApp.userPivilegeFeatures[group] = priv_feat;
            });
        }

    };

    normalizeDataPrivilege = function (attr_value) {
        var str = attr_value.substring("privilege-".length);
        str = ChurchApp.Util.replaceAllChar(str, "-", " ");
        str = toSentenceCase(str);
        return str;
    };

    this.enableFindParishes = true;// controls the finding of parishes based on change event of drop-down list. 

    this.privilegeSet = {
        data: [],
        init: function () {
            ChurchApp.privilegeSet.data = [];
        },
        add: function (name, value) {
            if (typeof ChurchApp.privilegeSet.data[name] === 'undefined'
                    || ChurchApp.privilegeSet.data[name] === null) {
                ChurchApp.privilegeSet.data[name] = value;
            } else {
                ChurchApp.privilegeSet.data[name] += "," + value;
            }
        },
        remove: function (name, value) {
            if (typeof ChurchApp.privilegeSet.data[name] === 'undefined'
                    || ChurchApp.privilegeSet.data[name] === null) {
                return;
            }

            var v = ChurchApp.privilegeSet.data[name].split(",");
            var new_v = "";
            var n = 0;
            for (var i = 0; i < v.length; i++) {
                if (v[i] == value) {
                    continue;
                }
                if (n === 0) {
                    new_v = v[i];
                } else {
                    new_v += "," + v[i];
                }
                n++;
            }

            ChurchApp.privilegeSet.data[name] = new_v;
            if (new_v == "") {
                ChurchApp.privilegeSet.data[name] = null;
            }

        },
        get: function (name) {
            return ChurchApp.privilegeSet.data[name];
        },
    };

    this.adminVerifyCode = {
        username: "",
        email: "",
        code: "",
        init: function () {
            ChurchApp.adminVerifyCode.username = "";
            ChurchApp.adminVerifyCode.email = "";
            ChurchApp.adminVerifyCode.code = "";
        }
    },
    this.authorizeFindUser = {
        searchNext: 1,
        findUser: function (param, searchName, searchLimit) {
            if (param === "next") {
                ChurchApp.authorizeFindUser.searchNext++;
            } else if (param === "previous") {
                if (ChurchApp.authorizeFindUser.searchNext > 1) {
                    ChurchApp.authorizeFindUser.searchNext--;
                }
            } else {
                ChurchApp.authorizeFindUser.searchNext = 1;
            }
            ChurchApp.post("php/FindUsers.php",
                    {
                        name_of_user: searchName,
                        search_limit: searchLimit,
                        next_search: ChurchApp.authorizeFindUser.searchNext,
                    },
                    function (data) {//done
                        var json = JSON.parse(data);
                        if (json.status === "success") {
                            if (json.data.length > 0) {
                                ChurchApp.showAuthorizationFoundUsers(json.data);
                            } else {
                                //decrement searchNext if no user was found of avoid overflow of searchNext
                                if (ChurchApp.authorizeFindUser.searchNext > 1) {
                                    ChurchApp.authorizeFindUser.searchNext--;
                                }
                                ChurchApp.showAuthorizationFoundUsers(json.data);
                            }
                        } else {
                            ChurchApp.alertResponse(json);
                        }

                    },
                    function (data, r, error) {//fail

                    });
        }
    };

    this.parishesOnDivisionChange = function () {

        if (!ChurchApp.enableFindParishes) {
            return;//this action is disabled
        }

        var data = $("#assign-to-parish-form").serialize();

        ChurchApp.post("php/FindParishes.php", data,
                function (data) {
                    alert(data);
                    var json = JSON.parse(data);
                    if (json.status === "success") {
                        ChurchApp.comboBoxPopulate("assign-to-parish-name"
                                , json.data.parish_names
                                , json.data.parish_sn);
                    } else {
                        ChurchApp.alertResponse(json);
                    }
                },
                function (data, r, error) {

                });
    };

    this.SignUpInfo = {
        username: "",
        firstName: "",
        lastName: "",
        email: "",
        stepsAwayFromCompleteSignup: ""
    };

    this.failed_auth = 0;
    this.attempts_update = 0;
    this.attempts_delete = 0;
    this.attempts_delete_msg_append = " attempts delete in current session";
    this.attempts_update_msg_append = " attempts update in current session";
    this.failed_auth_msg_append = " failed authentications in current session";

    this.TablePageObject = function () {
        this.tableData = "";
        this.tableColumns = "";
        this.tableTitle = "";
        this.style = ChurchApp.DisplayStyle.A;
        this.pageId = "";
        this.pageTitle = "";
        this.headerTitle = "";
        this.updateUrl = "#";//the server script file
        this.deleteUrl = "#";//the server script file
        this.inactivityTimeout = 180;//in seconds - usually retrived from user settings
        //more fields may go below

        this.validationRules = {};
    };
    /**
     * Stop the specified event.
     * 
     * @param {type} evt
     * @returns {undefined}
     */
    this.preventDefault = function (evt) {
        if (evt.preventDefault) {
            evt.preventDefault();
        } else {
            evt.returnValue = false; //IE especially IE8 wahala!!!
        }
    };

    this.Util = new function () {
        this.toCommaSeparated = function (arr) {
            if (Object.prototype.toString.call(arr) !== '[object Array]') {
                return arr;//no an array
            }
            var str = "";
            for (var i = 0; i < arr.length; i++) {
                str = i < arr.lenght - 1 ? (arr[i] + ",") : arr[i];
            }

            return str;
        };
        this.isInternalPage = function (page_url) {
            var strUrl = new String(page_url);
            for (var i = strUrl.length - 1; i > -1; i--) {
                if (strUrl.charAt(i) === '\\'
                        || strUrl.charAt(i) === '/'
                        || strUrl.charAt(i) === ':') {
                    return false;
                }
                if (strUrl.charAt(i) === '#' && i < strUrl.length - 1) {
                    return true;
                }
            }

            return false;
        };

        this.getInternalPageName = function (page_url) {
            var strUrl = new String(page_url);
            for (var i = strUrl.length - 1; i > -1; i--) {
                if (strUrl.charAt(i) === '\\'
                        || strUrl.charAt(i) === '/'
                        || strUrl.charAt(i) === ':') {
                    return false;
                }
                if (strUrl.charAt(i) === '#' && i < strUrl.length - 1) {
                    return strUrl.substring(i + 1);
                }
            }

            return false;
        };

        this.endsWithStr = function (container_str, search_str) {

            //check if they are both string
            if (typeof container_str !== "string" ||
                    typeof search_str !== "string") {
                return false;
            }

            var b_len = container_str.length;
            var s_len = search_str.length;

            if (b_len < s_len)
                return false;

            var b = b_len;
            for (var i = s_len - 1; i > -1; i--) {
                --b;
                if (container_str.charAt(b) !== search_str.charAt(i)) {
                    return false;
                }
            }

            return true;
        };

        this.replaceAllChar = function (str, oldChar, newChar) {
            var new_str = "";
            for (var i = 0; i < str.length; i++) {
                var c = str.charAt(i);
                if (c === oldChar) {
                    c = newChar;
                }

                new_str += c;
            }

            return new_str;
        };

        isArray = function (obj) {
            return Object.prototype.toString.call(obj) === '[object Array]';
        };

        this.arrayFind = function (arr, search) {

            if (!isArray(arr)) {
                return false;
            }

            if (typeof search === "undefined") {
                return false;
            }

            for (var i = 0; i < arr.length; i++) {
                if (arr[i] == search) {
                    return true;
                }
            }

            return false;
        };
    };

    this.setScrollFixedNavTop = function (className, num) {
        //number of pixels before modifying styles        
        $(document).on("scroll", function () {
            if ($(this).scrollTop() > num) {
                $("." + className).addClass("fixed-nav-top");
            } else {
                $("." + className).removeClass("fixed-nav-top");
            }
        });
    };

    /**The contract for populating internal page using external file content
     *  in this app is as follows:<br/><br/>
     * 
     * The external content file must be of html extension.<br/>
     * 
     * The file name of the external content file must be the 
     * same as the internal page div id.<br/>
     * 
     * The external content file must have an outer div (div just before the body tag)
     * with a id of this format: load-internal-internal_page_div_id , where
     * internal_page_div_id is the div of the internal page whose content
     * will be replaced (actually populated) by the content of the external file.<br/><br/>
     * 
     * NOTE: The external page outer div must not have any sibling and must have ONLY
     * one attribute and that is the id whose format must be that specified above.
     * 
     * 
     * 
     * @param {type} pageUrl
     * @returns {Boolean}
     */
    this.loadInternalPage = function (pageUrl) {

        //first get the internal page id
        var pageId = this.Util.getInternalPageName(pageUrl);
        if (!pageId) {
            return false;
        }

        $("#" + pageId).load(pageId + ".html #load-internal-" + pageId, function (responseTxt, statusTxt, xhr) {
            if (statusTxt == "success") {
                $("#" + pageId).html($("#load-internal-" + pageId).html());
                $("#" + pageId).trigger('create');
                handleDynamicPageLoad($("#" + pageId));

            }
            if (statusTxt === "error") {
                //alert("Error: " + xhr.status + ": " + xhr.statusText);
            }

        });
    };

    loadHomePageByPriviledge = function (obj) {

        ChurchApp.user = obj.user;

        var homePage = "";
        switch (obj.user.group) {
            case "Super Admin":
                homePage = "home-page-admin.html";//TODO change to home-page-super-admin.html
                break;
            case "Admin":
                homePage = "home-page-admin.html";
                break;
            case "Accountant":
                homePage = "home-page-accountant.html";
                break;
            case "Pastor":
                homePage = "home-page-pastor.html";
                break;
            case "Worker":
                homePage = "home-page-worker.html";
                break;
            case "Men Exco":
                homePage = "home-page-men-exco.html";
                break;
            case "Women Exco":
                homePage = "home-page-women-exco.html";
                break;
            case "Youth Exco":
                homePage = "home-page-youth-exco.html";
                break;
            case "Member":
                homePage = "home-page-member.html";
                break;
            case "Children":
                homePage = "home-page-children.html";
                break;

        }

        //ChurchApp.loadInternalPage(homePage);
        $(":mobile-pagecontainer").pagecontainer("change", homePage, {
            transition: 'slide',
            //changeHash: false,
            // reverse: true,
            showLoadMsg: true
        });

        $(":mobile-pagecontainer").on("pagecontainershow", function (event, ui) {
            //alert("This page was just hidden: " + ui.toPage);//dataUrl
            //alert("absUrl " + ui.absUrl);//dataUrl

            $(".header-parish-name").html((typeof obj.user.parishName === "undefined" ? "" : obj.user.parishName)
                    + "<br/><span style='font-size:0.8em;'>"
                    + (typeof obj.user.parishAddress === "undefined" ? "" : obj.user.parishAddress) + "</span>");

            hideUnauthorizedFeatures(obj.user);

            var s = $(document).find('[data-name="churchapp-news-content"]');//come back - could have more that one elements

            $(s).each(function (index, element) {

                if ($.trim(element.innerHTML).length === 0) {

                    var carousel_html = createCarouselHTML(obj);
                    var news_html = ChurchApp.createNewsHtml(obj.newsContent, ChurchApp.DisplayStyle.A);

                    element.innerHTML = carousel_html + news_html;
                    $(this).trigger('create');
                    initializeSlick(obj);
                }
            });


        });

        //More code may go below like enabling and disabling feature based on user priviledge role

    }

    hideUnauthorizedFeatures = function (user) {
        var unviewables = ""
        try {
            for (var group in JSON.parse(user.unviewableFeaturesJson)) {
                if (group === user.group) {
                    try {
                        var unviewJson = JSON.parse(user.unviewableFeaturesJson);
                        unviewables = unviewJson[group];
                    } catch (e) {
                        continue;
                    }
                    break;
                }
            }
        } catch (e) {
            return; // e.g JSON error
        }


        if (unviewables === "") {
            return;
        }
        unviewables = unviewables.split(',');

        var els = $(document).find('[data-privilege^="privilege-"]');

        //first ensure all is showing
        els.each(function (index, element) {
            $(this).show();
        });

        //reconstruct to the data-privilege value then hide using the value

        for (var i = 0; i < unviewables.length; i++) {
            unviewables[i] = unviewables[i].toLowerCase();
            unviewables[i] = ChurchApp.Util.replaceAllChar(unviewables[i], "  ", " ");
            unviewables[i] = ChurchApp.Util.replaceAllChar(unviewables[i], " ", "-");
            if (unviewables[i].indexOf("privilege-") !== 0) {
                unviewables[i] = "privilege-" + unviewables[i];
                //now hide using the value of data-privilege reconstructed
                $("[data-privilege='" + unviewables[i] + "']").hide();
            }
        }

        //now hide the ones marked 
        /*els.first();
         els.each(function (index, element) {
         for(var i =0; i< unviewables; i++){
         if(unviewables[i].toLowerCase() === element.innerHTML.toLowerCase()){
         $(this).hide();
         break;
         }
         }
         });*/



    };

    this.loginByPriviledge = function (obj) {

        if (obj.user.parishName === null
                || obj.user.parish_name === '') {
            ChurchApp.assignParishToUser(obj);
        } else if (obj.user.verifiedEmail === '0'
                || obj.user.verifiedEmail === 0) {//email not verified
            ChurchApp.verifyUserEmail(obj.user);
        } else {
            loadHomePageByPriviledge(obj);
        }

    };

    initializeSlick = function (obj) {
        var show = 3;
        if (obj.carouselImages.length === 2) {
            show = 2;
        } else if (obj.carouselImages.length === 1) {
            show = 1;
        }
        $('.home-page-carousel').slick({
            dots: true,
            infinite: true,
            //centerMode: true,
            //centerPadding: '60px',
            slidesToShow: show,
            lazyLoad: 'ondemand',
            responsive: [
                {
                    breakpoint: 768,
                    settings: {
                        arrows: false,
                        //centerMode: true,
                        //centerPadding: '40px',
                        slidesToShow: show
                    }
                },
                {
                    breakpoint: 480,
                    settings: {
                        arrows: false,
                        //centerMode: true,
                        //centerPadding: '40px',
                        slidesToShow: 1//yes 1 in this case
                    }
                }
            ]
        });

    };

    this.post = function (url, data, doneCallback, failureCallback) {

        $.ajax({
            type: 'POST', // define the type of HTTP verb we want to use (POST for our form)
            url: url, // the url where we want to POST
            data: data, // our data object
            //dataType: 'json' // what type of data do we expect back from the server
        })
                // using the done promise callback
                .done(function (data) {
                    if (typeof doneCallback === "function") {
                        doneCallback(data);
                    }
                })
                // using the fail promise callback
                .fail(function (data, r, error) {
                    if (typeof failureCallback === "function") {
                        failureCallback(data, r, error);
                    }
                });
    };

    this.postForm = function (form, doneCallback, failureCallback) {

        $.ajax({
            type: 'POST', // define the type of HTTP verb we want to use (POST for our form)
            url: form.action, // the url where we want to POST
            data: $(form).serialize(), // our data object
            //dataType: 'json' // what type of data do we expect back from the server
        })
                // using the done promise callback
                .done(function (data) {
                    if (typeof doneCallback === "function") {
                        doneCallback(data);
                    }
                })
                // using the fail promise callback
                .fail(function (data, r, error) {
                    if (typeof failureCallback === "function") {
                        failureCallback(data, r, error);
                    }
                });
    };

    this.dynamicPageLoadCallback = {};

    /**This function is called when a page is loaded dynamically.
     * This method is the preferred place to code for processing 
     * dynamic pages (such as click events or any event) in this 
     * ChurcApp by passing function to it which will be called 
     * back whenever a dynamic page is loaded.
     * 
     * @param {type} callback
     * @returns {undefined}
     */
    this.onDynamicPageLoaded = function (callbackFunc) {
        this.dynamicPageLoadCallback = callbackFunc;
    };

    handleDynamicPageLoad = function (selector) {
        if (typeof ChurchApp.dynamicPageLoadCallback === "function") {
            ChurchApp.dynamicPageLoadCallback(selector);
        }
    };

    this.cdMin = 3;
    this.cdSec = 0;
    this.cdTimerId = 0;
    this.countDownAuthFinished = false;
    this.inactivity_timeout_msg = "Sorry, your inactivity has timeout.<br/>Operation disabled!";
    this.inactivity_timeout_browser_msg = "Sorry, your inactivity has timeout.Operation disabled!";

    initCountDownToAuth = function (inactivityTimeout) {
        if (!ChurchApp.countDownAuthFinished) {
            calculateCountDownTimeout(inactivityTimeout);
        }
    };

    calculateCountDownTimeout = function (inactivityTimeout) {
        var timeout = Number(inactivityTimeout);
        if (!isNaN(timeout)) {
            ChurchApp.cdMin = parseInt(timeout / 60);
            ChurchApp.cdSec = timeout - ChurchApp.cdMin * 60;
        }
    };

    startCountDownToAuth = function (selector, inactivityTimeout) {
        //first clear previous timer
        if (ChurchApp.cdTimerId !== 0) {
            window.clearInterval(ChurchApp.cdTimerId);
        }

        calculateCountDownTimeout(inactivityTimeout);
        ChurchApp.cdTimerId = 0;
        ChurchApp.countDownAuthFinished = false;
        var countdown = selector.find("[name=countdown_to_auth]");
        ChurchApp.cdTimerId = window.setInterval(function () {
            countdown.html(ChurchApp.cdMin + ":" + (ChurchApp.cdSec < 10 ? "0" : "") + ChurchApp.cdSec);
            if (ChurchApp.cdMin <= 0 && ChurchApp.cdSec <= 0) {
                ChurchApp.countDownAuthFinished = true;
                countdown.attr({style: "color:red;"});
                window.clearInterval(ChurchApp.cdTimerId);
                var obj = $(selector).find("[name=display_msg]");
                obj.html(ChurchApp.inactivity_timeout_msg);
                obj.attr({style: "color:red;"});
                //$(selector).find("[type=submit]").attr("disabled",true);//come back for you later!!!
                return;
            }
            if (ChurchApp.cdSec > 0 && ChurchApp.cdMin >= 0) {
                ChurchApp.cdSec--;
            } else if (ChurchApp.cdSec === 0 && ChurchApp.cdMin > 0) {
                ChurchApp.cdSec = 59;
                ChurchApp.cdMin--;
            }
        }, 1000);
    };

    this.alertResponse = function (json, use_default_msg) {

        if (use_default_msg && json.msg !== '' && json.msg !== 'null' && json.msg !== null) {
            alert(json.msg);
            return;
        }

        if (json.status === "success") {
            alert("The operation was successful.");
        } else if (json.status === "warning") {
            alert("Messsage recieved from the server with warning.");
        } else if (json.status === "ignore") {
            alert("The operation was ignored.");
        } else if (json.status === "error") {
            alert("An error occur!");
        } else if (json.status === "session_not_available") {
            alert("Your session is no longer available.<br/>You may have to login again.");
        }
    };

    this.comboBoxClear = function (id) {
        var d = document.getElementById(id);

        for (var i = 1; i < d.length; i++) {//start from index 1 by default
            d.remove(i);
        }
        $("#" + id).prop("selectedIndex", 0);
        $("#" + id).change();
    };

    this.comboBoxPopulate = function (id, arrText, arrValue) {
        var d = document.getElementById(id);

        var value = d.value;

        var len = d.length;
        for (var i = 1; i < len; i++) {//from from index 1 by default
            d.remove(d.length - 1);//perfect way to remove 
        }

        var found = false;
        for (var i = 0; i < arrText.length; i++) {
            var opt = document.createElement("option");
            opt.text = arrText[i];
            if (typeof arrValue !== "undefined") {
                opt.value = arrValue[i];//come back
            } else {
                opt.value = arrText[i];//come back
            }
            if (opt.value == value) {
                found = true;
            }
            d.add(opt);
        }

        if (found) {
            d.value = value;
        }
    };

    tableActionOptionMenuPopup = function (tablePageObj, index) {

        var markup = '<div data-role="popup" data-theme="a">'
                + '<ul data-role="listview" data-inset="true" style="min-width:13.125em;">'
                + '<li data-role="list-divider">Choose an action</li>'
                + '<li><a href="#">View Only</a></li>'
                + '<li><a href="#">Edit</a></li>'
                + '<li><a href="#">Delete</a></li>'
                + '</ul>'
                + '</div>';

        var pup = $(markup)
                .appendTo($.mobile.activePage)
                .popup();

        $(pup).trigger('create');

        var selecetOpt = {name: ""};

        $(pup).on("popupafterclose", function () {
            //alert("pop up remove to manage DOM size");//may be not removing
            $(this).remove();

            setTimeout(function () {//workaround recommended by jQM to open popup from another popup
                if (selecetOpt.name === 'View Only') {
                    tableViewOnlyPopup(tablePageObj, index);
                } else if (selecetOpt.name === 'Edit') {
                    tableAuthPopup('Edit', tablePageObj, index);
                } else if (selecetOpt.name === 'Delete') {
                    tableAuthPopup('Delete', tablePageObj, index);
                }
            }, 100);

        });

        $(pup).on('click', 'li a', function () {
            selecetOpt.name = $(this).html();
            $(pup).popup("close");
        });

        $(pup).popup("open");

    };

    tableEditPopup = function (tablePageObj, index) {
        //alert("pop  edit");
        var fieldValues = tablePageObj.tableData[index];
        //alert(json.data.table_data[index]);
        var form_html = createFormFieldHTML(tablePageObj.tableColumns,
                tablePageObj.useDefaultUneditables,
                tablePageObj.uneditableColumns,
                fieldValues,
                tablePageObj.updateUrl);

        var popup_title = "Edit record";//come back later
        var closebtn = '<a href="#" data-rel="back" class="ui-btn ui-corner-all ui-btn-a ui-icon-delete ui-btn-icon-notext ui-btn-right">Close</a>',
                header = '<div data-role="header"><div name="countdown_to_auth">-:--</div><h2>' + popup_title + '</h2></div>',
                popup = '<div data-role="popup" data-overlay-theme="a" data-tolerance="15"></div>',
                footer = '<div data-role="footer"><div class="ui-content" style="font-size:0.75em;"><div name="attempts_update">'
                + ChurchApp.attempts_update + ChurchApp.attempts_update_msg_append + '</div><div name="failed_auth">'
                + ChurchApp.failed_auth + ChurchApp.failed_auth_msg_append + '</div></div></div>';

        // Create the popup.
        var popup = $(popup)
                .appendTo($.mobile.activePage)
                .popup();

        $(header)
                .appendTo(popup)
                .toolbar()
                .before(closebtn)
                .after(form_html);

        $(footer)
                .appendTo(popup)
                .toolbar();

        $(popup).trigger('create');

        $(popup).on("popupafteropen", function () {//confirm later
            startCountDownToAuth($(popup), tablePageObj.inactivityTimeout);
        });

        $(popup).on("popupafterclose", function () {//confirm later
            $(this).remove();
            clearInterval(ChurchApp.cdTimerId);
        });

        $(popup).on('keyup', function (evt) {
            initCountDownToAuth(tablePageObj.inactivityTimeout);
        });

        $(popup).on('mousemove', function (evt) {
            initCountDownToAuth(tablePageObj.inactivityTimeout);
        });

        $(popup).find("form").validate({
            //validation rules
            rules: tablePageObj.validationRules,
            //custom validation message
            messages: tablePageObj.validationMessages,
            submitHandler: function (form) {
                if (ChurchApp.countDownAuthFinished) {
                    alert(ChurchApp.inactivity_timeout_browser_msg);
                    return;//do nothing!
                }

                ChurchApp.attempts_update++;

                ChurchApp.postForm(form,
                        function (data) {//done
                            alert(data);
                        },
                        function (data, r, error) {//fail
                            alert("error");//testing!!!
                        });

                $(popup).find("[name=attempts_update]").html(ChurchApp.attempts_update + ChurchApp.attempts_update_msg_append);
            }

        });

        $(popup).popup("open");

    };

    tableDeletePopup = function (tablePageObj, index) {

        var fieldValues = tablePageObj.tableData[index];
        var html = createUneditedFieldHTML(tablePageObj.tableColumns,
                fieldValues,
                tablePageObj.deleteUrl);

        var popup_title = "Delete record";//come back later
        var closebtn = '<a href="#" data-rel="back" class="ui-btn ui-corner-all ui-btn-a ui-icon-delete ui-btn-icon-notext ui-btn-right">Close</a>',
                header = '<div data-role="header"><div name="countdown_to_auth">-:--</div><h2>' + popup_title + '</h2></div>',
                popup = '<div data-role="popup" data-overlay-theme="a" data-tolerance="15"></div>',
                footer = '<div data-role="footer"><div class="ui-content" style="font-size:0.75em;"><div name="attempts_delete">'
                + ChurchApp.attempts_delete + ChurchApp.attempts_delete_msg_append + '</div><div name="failed_auth">'
                + ChurchApp.failed_auth + ChurchApp.failed_auth_msg_append + '</div></div></div>';

        // Create the popup.
        var popup = $(popup)
                .appendTo($.mobile.activePage)
                .popup();


        $(header)
                .appendTo(popup)
                .toolbar()
                .before(closebtn)
                .after(html);

        $(footer)
                .appendTo(popup)
                .toolbar();

        $(popup).trigger('create');

        $(popup).on("popupafteropen", function () {//confirm later
            startCountDownToAuth($(popup), tablePageObj.inactivityTimeout);
        });

        $(popup).on('keyup', function (evt) {
            initCountDownToAuth(tablePageObj.inactivityTimeout);
        });

        $(popup).on('mousemove', function (evt) {
            initCountDownToAuth(tablePageObj.inactivityTimeout);
        });

        $(popup).on("popupafterclose", function () {//confirm later
            $(this).remove();
            clearInterval(ChurchApp.cdTimerId);
        });

        $(popup).on('submit', 'form', function (evt) {
            evt.preventDefault();
            if (ChurchApp.countDownAuthFinished) {
                alert(ChurchApp.inactivity_timeout_browser_msg);
                return;//do nothing!
            }

            ChurchApp.attempts_delete++;

            alert($(this).serialize());


            ChurchApp.postForm(this,
                    function (data) {//done
                        alert(data);
                    },
                    function (data, r, error) {//fail
                        alert("error");//testing!!!
                    });

            $(popup).find("[name=attempts_delete]").html(ChurchApp.attempts_delete + ChurchApp.attempts_delete_msg_append);

        });

        $(popup).popup("open");
    };

    tableViewOnlyPopup = function (tablePageObj, index) {
        var fieldValues = tablePageObj.tableData[index];
        var html = createUneditedFieldHTML(tablePageObj.tableColumns,
                fieldValues, '');
        var popup_title = "View Only";//come back later
        var closebtn = '<a href="#" data-rel="back" class="ui-btn ui-corner-all ui-btn-a ui-icon-delete ui-btn-icon-notext ui-btn-right">Close</a>',
                header = '<div data-role="header"><h2>' + popup_title + '</h2></div>',
                popup = '<div data-role="popup" data-overlay-theme="a" data-tolerance="15"></div>';

        // Create the popup.
        var popup = $(popup)
                .appendTo($.mobile.activePage)
                .popup();

        $(header)
                .appendTo(popup)
                .toolbar()
                .before(closebtn)
                .after(html);

        $(popup).trigger('create');

        $(popup).on("popupafterclose", function () {//confirm later
            alert("pop up remove to manage DOM size");//may be not removing
            $(this).remove();
        });

        $(popup).popup("open");

    };

    tableAuthPopup = function (action, tablePageObj, index) {

        var auth_form_html = '<div data-role="popup" data-theme="a" class="ui-corner-all">'
                + '<form action="php/AuthRecordsModify.php">'
                + '<div style="padding:10px 20px;">'
                + '<h3>Authentication</h3>'
                + '<p>' + action + ' operation requires authentication...</p>'
                + '<label for="record_modify_username" class="ui-hidden-accessible">Username:</label>'
                + '<input type="text" name="record_modify_username" value="" placeholder="username" data-theme="a" />'
                + '<label for="record_modify_password" class="ui-hidden-accessible">Password:</label>'
                + '<input type="password" name="record_modify_password" value="" placeholder="password" data-theme="a" />'
                + '<p name="auth_feedback" style="color:red;"></p>'
                + '<button type="submit" data-theme="a">OK</button>'
                + '</div>'
                + '</form>'
                + '</div>';

        var popup = $(auth_form_html)
                .appendTo($.mobile.activePage)
                .popup();

        $(popup).trigger('create');
        $(popup).popup("open");

        var result = {
            success: false,
            action: action
        };

        $(popup).on('submit', 'form', function (evt) {
            evt.preventDefault();
            //alert(evt.target);
            ChurchApp.postForm(evt.target,
                    function (data) {
                        var json = JSON.parse(data);
                        if (json.status === "success") {
                            result.success = true;
                            $(popup).popup("close");
                        } else if (json.status === "error") {
                            $('[name=auth_feedback]').html(json.msg);
                            ChurchApp.failed_auth++;
                        } else if (json.status === "session_not_available") {
                            $('[name=auth_feedback]').html("Your session is no longer available!<br/>You may have to login again.");
                            ChurchApp.failed_auth++;
                        } else {
                            ChurchApp.failed_auth++;
                            ChurchApp.alertResponse(json);
                        }

                    },
                    function (data, r, error) {
                        //alert(data);
                    });
        });

        $(popup).on("popupafterclose", function () {
            //alert("pop up remove to manage DOM size");//may be not removing
            $(this).remove();

            if (result.success) {
                setTimeout(function () {//workaround recommended by jQM to open popup from another popup
                    if (result.action === 'Edit') {
                        tableEditPopup(tablePageObj, index);
                    } else if (result.action === 'Delete') {
                        tableDeletePopup(tablePageObj, index);
                    }
                }, 100);
            }

        });

    };

    this.changeAndRenderTablePage = function (tablePageObj) {
        $(":mobile-pagecontainer").pagecontainer("change", "table-view.html", {
            transition: 'slide',
            //changeHash: false,
            // reverse: true,
            showLoadMsg: true
        });
        $(":mobile-pagecontainer").on("pagecontainershow", function (event, ui) {
            ChurchApp.renderTablePage(tablePageObj);
        });



    };

    this.renderTablePage = function (tablePageObj) {
        //data-church-table-name="table-view-page"
        /*$("#" + tablePageObj.pageId)
         .find("[data-church-table-name=table-view-page]").html(tablePageObj.headerTitle);
         
         $("#" + tablePageObj.pageId).attr("data-title", tablePageObj.pageTitle);//not working
         
         $('div[data-role="page"]').bind("pageshow", function () {
         if ($.mobile.activePage.attr("id") === tablePageObj.pageId) {
         document.title = tablePageObj.pageTitle;
         }
         });
         
         var table_html = ChurchApp.createTableHtml(tablePageObj.tableTitle, tablePageObj.tableColumns, tablePageObj.tableData, tablePageObj.style);
         $("#" + tablePageObj.pageId + " .ui-content").html(table_html);
         $("#" + tablePageObj.pageId).trigger('create');
         ChurchApp.handleTableAction(tablePageObj);*/

        $("[data-church-table-name=table-view-page]").html(tablePageObj.headerTitle);

        //$("#" + tablePageObj.pageId).attr("data-title", tablePageObj.pageTitle);//not working

        /*$('div[data-role="page"]').bind("pageshow", function () {
         if ($.mobile.activePage.attr("id") === tablePageObj.pageId) {
         document.title = tablePageObj.pageTitle;
         }
         });*/

        var table_html = ChurchApp.createTableHtml(
                {
                    title: tablePageObj.tableTitle,
                    columns: tablePageObj.tableColumns,
                    data: tablePageObj.tableData,
                    style: tablePageObj.style
                }
        );
        $("#" + tablePageObj.pageId).html(table_html);
        $("#" + tablePageObj.pageId).trigger('create');
        ChurchApp.handleTableAction(tablePageObj);
    };

    this.handleTableAction = function (tablePageObj) {

        //first we need to remove previous dblclick and taphold events 
        //-  important!
        var id_selector = "#" + tablePageObj.pageId + " tr";

        $(id_selector).off('dblclick');
        $(id_selector).off('taphold');

        $(id_selector).on('dblclick', function () {
            tableAction(tablePageObj, $(this), $("#" + tablePageObj.pageId + " tbody"));
        });

        $(id_selector).on('taphold', function () {
            tableAction(tablePageObj, $(this), $("#" + tablePageObj.pageId + " tbody"));
        });

    };

    tableAction = function (tablePageObj, row_wrapper, tbody) {

        var selected_row = $(row_wrapper)[0];

        $(tbody).find("tr").each(function (index, element) {
            if (selected_row === element) {
                tableActionOptionMenuPopup(tablePageObj, index);
            }
        });

    };

    createFormFieldHTML = function (fieldLabels,
            useDefaultUneditables,
            uneditableColumns,
            fieldValues,
            formAction) {

        var html = '<div data-role="main" class="ui-content">'
                + '<h3 name="display_msg">You may edit the selected record.<h3>'
                + '<form action="' + formAction + '">';

        for (var i = 0; i < fieldLabels.length; i++) {
            var data_clear = 'data-clear-btn="true"';
            var readonly_class = "";
            var readonly = "";

            if (needReadonly(fieldLabels[i], useDefaultUneditables, uneditableColumns)) {
                data_clear = ' data-clear-btn="false" ';
                readonly_class = " class='churchapp-input-readonly' ";
                readonly = " readonly ";
            }

            html += '<label for="' + fieldLabels[i] + '" >'
                    + toSentenceCase(fieldLabels[i]) +
                    '</label><input ' + data_clear + ' name="'
                    + fieldLabels[i] + '" '
                    + readonly + readonly_class
                    + ' value="' + fieldValues[i] + '" type="text"/>';
        }
        html += '<input type="submit" value="Update"/></form></div>';
        return html;
    };

    createUneditedFieldHTML = function (fieldLabels, fieldValues, formAction) {

        var html = '<div data-role="main" class="ui-content ui-responsive">';
        if (formAction !== '') {
            html += "<h3 name='display_msg'>Confirm the record you have selected to delete.<h3>";
            html += "<form action='" + formAction + "'>";
        }
        for (var i = 0; i < fieldLabels.length; i++) {
            html += '<label for="' + fieldLabels[i] + '" >'
                    + toSentenceCase(fieldLabels[i]) +
                    '</label><input name="'
                    + fieldLabels[i] + '" value="' + fieldValues[i] + '" readonly type="text"/>';

        }
        if (formAction !== '') {
            html += '<input type="submit" value="Delete"/></form>';
        }
        html += '</div>';
        return html;
    };

    needReadonly = function (fieldName, useDefaultUneditables,
            uneditableColumns) {

        if (ChurchApp.Util.arrayFind(uneditableColumns, fieldName))
            return true;

        var defaultUneditables = ["SN", "ENTRY_USER_ID", "ENTRY_DATETIME"];
        if (useDefaultUneditables
                && ChurchApp.Util.arrayFind(defaultUneditables, fieldName)) {
            return true;
        }

        return false;
    };

    toSentenceCase = function (str) {
        var s = new String(str).toLowerCase();
        s = ChurchApp.Util.replaceAllChar(s, '_', ' ');
        s = s.substring(0, 1).toUpperCase() + s.substring(1);
        return s;
    };

    normalizeTableColumn = function (str) {
        var s = new String(str).toUpperCase();
        s = ChurchApp.Util.replaceAllChar(s, '_', ' ');
        return s;
    };

    this.assignParishToUser = function (obj) {
        //copy the properties to the SignUpInfo
        ChurchApp.SignUpInfo.username = obj.username;
        ChurchApp.SignUpInfo.firstName = obj.firstName;
        ChurchApp.SignUpInfo.lastName = obj.lastName;
        ChurchApp.SignUpInfo.email = obj.email;

        $(":mobile-pagecontainer").pagecontainer("change", "#answer-parish-question-page", {
            transition: 'slide',
            //changeHash: false,
            // reverse: true,
            showLoadMsg: true
        });

        $(document).find("[data-name='hi-user-full-name']").each(function () {
            $(this).html(obj.firstName + " " + obj.lastName);
        });

        var s = $(document).find("[data-name='steps-away-from-sign-up-completion']");
        $(s).each(function (index, element) {
            $(this).show();
            if (obj.stepsAwayFromCompleteSignup === null) {
                $(this).hide();
            } else {
                $(this).html(obj.stepsAwayFromCompleteSignup);
            }
        });
    };

    this.verifyUserEmail = function (user) {
        ChurchApp.SignUpInfo.username = user.username;
        ChurchApp.SignUpInfo.firstName = user.firstName;
        ChurchApp.SignUpInfo.lastName = user.lastName;
        ChurchApp.SignUpInfo.email = user.email;
        var e = $("#email-verification-page [data-role='main']");
        e.html("");//first clear the content
        $(":mobile-pagecontainer").pagecontainer("change", "#email-verification-page", {
            transition: 'slide',
            //changeHash: false,
            // reverse: true,
            showLoadMsg: true
        });

        $.mobile.loading("show", {
            text: "Sending verification email. Please wait... ",
            textVisible: true,
            theme: "a",
            textonly: false,
            html: ""
        });

        ChurchApp.post("php/SendEmailVerify.php",
                {
                    username: user.username,
                    firstName: user.firstName,
                    lastName: user.lastName,
                    email: user.email,
                }, function (data) {

            $.mobile.loading("hide");

            alert(data);

            var json = JSON.parse(data);

            e = $("#email-verification-page [data-role='main']");
            if (json.status === "success") {
                var fullName = json.data.firstName + " " + json.data.lastName;
                var content = '<h2>Email Address Verification</h2>'
                        + '<p>Thanks, <b><span data-name ="hi-user-full-name" style="font-size: large;">' + fullName + '</span></b>, you have reached the final step.</p>'
                        + '<p>We have sent you an email to verify the email address, <strong data-name ="user-email-address">'
                        + json.data.email + '</strong>, you gave us.</p>'
                        + '<p>Please check your email and click a link we have provided to complete your sign-up process.</p>';

                e.html(content);
            } else {
                e.html("<h2>Sorry!</h2>Could not continue process.<br/>" + json.msg);
            }
        }, function (error) {
            $.mobile.loading("hide");
        });
    };

    this.showAuthorizationFoundUsers = function (users_arr) {
        var userHtml = "";
        var foundUsers = users_arr;
        for (var i = 0; i < users_arr.length; i++) {
            userHtml += userFoundHTML(users_arr[i]);
        }
        $("#authorization_autocomplete").slideDown();

        $("#authorization_autocomplete").html(userHtml);
        $("#authorization_autocomplete").trigger('create');
        $("#authorization_autocomplete").listview('refresh');

        $("#authorization_autocomplete li a").off('click');

        $("#authorization_autocomplete li a").on('click', function () {
            var username = $(this).find("[name='username']").val();
            $("#authorization_username").val(username);
            $("#authorization_fullname").val($(this).find("[name='full_name']").val());
            $("#authorization_designation").val($(this).find("[name='designation']").val());

            $("#authorization_autocomplete").slideUp();
            for (var i = 0; i < foundUsers.length; i++) {
                if (foundUsers[i].username === username) {
                    $("#authorization_features").html("");

                    //check block user account
                    if (foundUsers[i].blockedAccount === '0') {
                        $("#authorization_block_user").prop("checked", false).checkboxradio('refresh');
                    } else {
                        $("#authorization_block_user").prop("checked", true).checkboxradio('refresh');
                    }

                    //check/uncheck the unviewable features
                    var unviewable_features_json = foundUsers[i].unviewableFeaturesJson;
                    checkUserPrivilegeFeatureElements(unviewable_features_json);

                    //check/uncheck the privilege
                    var user_groups = foundUsers[i].userGroups;
                    checkUserAssignPrivilegeElements(user_groups);

                    break;
                }
            }
        });
    };

    checkUserPrivilegeFeatureElements = function (unviewable_features_json) {

        if (ChurchApp.userPivilegeFeatures === null) {
            return;
        }

        //first initialize to true
        for (var field_name in ChurchApp.userPivilegeFeatures) {
            var priv_feat = ChurchApp.userPivilegeFeatures[field_name];
            for (var index = 0; index < priv_feat.length; index++) {
                priv_feat[index].enabled = true;
            }
        }

        //convert to json object
        try {
            unviewable_features_json = JSON.parse(unviewable_features_json);
        } catch (e) {
            return;//no json so leave
        }

        for (var group in unviewable_features_json) {

            var norm_group = group.toLowerCase();
            norm_group = ChurchApp.Util.replaceAllChar(norm_group, "  ", " ");
            norm_group = ChurchApp.Util.replaceAllChar(norm_group, " ", "_");
            var priv_feat = ChurchApp.userPivilegeFeatures[norm_group];
            var unviewables_str = unviewable_features_json[group];
            if (typeof unviewables_str === "undefined"
                    || unviewables_str === null
                    || unviewables_str === '') {
                continue;
            }
            var unviewables_arr = unviewables_str.split(',');
            for (var j = 0; j < unviewables_arr.length; j++) {
                for (var k = 0; k < priv_feat.length; k++) {
                    if (priv_feat[k].feature.toLowerCase()
                            === unviewables_arr[j].toLowerCase()) {
                        priv_feat[k].enabled = false;//disable
                        break;
                    }
                }
            }
        }

    };

    checkUserAssignPrivilegeElements = function (user_groups) {
        if (typeof user_groups === "string") {
            user_groups = user_groups.split(',');
        } else if (!ChurchApp.Util.isArray(user_groups)) {
            return;
        }
        //first initialize all to false ie uncheck all
        $("#authorization_admin").prop("checked", false).checkboxradio('refresh');
        $("#authorization_accountant").prop("checked", false).checkboxradio('refresh');
        $("#authorization_worker").prop("checked", false).checkboxradio('refresh');
        $("#authorization_pastor").prop("checked", false).checkboxradio('refresh');
        $("#authorization_men_exco").prop("checked", false).checkboxradio('refresh');
        $("#authorization_women_exco").prop("checked", false).checkboxradio('refresh');
        $("#authorization_youth_exco").prop("checked", false).checkboxradio('refresh');
        $("#authorization_member").prop("checked", false).checkboxradio('refresh');
        $("#authorization_children").prop("checked", false).checkboxradio('refresh');

        if (user_groups.length === 1 && user_groups[0] === "") {
            return;//As far as I am concern, it's equivalent to empty array
        }

        //now check the relevant check box
        for (var i = 0; i < user_groups.length; i++) {
            var group = user_groups[i].toLowerCase();
            group = ChurchApp.Util.replaceAllChar(group, "  ", " ");
            group = ChurchApp.Util.replaceAllChar(group, " ", "_");
            var e = null;
            switch (group) {
                case "admin":
                    {
                        e = document.getElementById("authorization_admin");
                    }
                    break;
                case "accountant":
                    {
                        e = document.getElementById("authorization_accountant");
                    }
                    break;
                case "pastor":
                    {
                        e = document.getElementById("authorization_pastor");
                    }
                    break;
                case "worker":
                    {
                        e = document.getElementById("authorization_worker");
                    }
                    break;
                case "men_exco":
                    {
                        e = document.getElementById("authorization_men_exco");
                    }
                    break;
                case "women_exco":
                    {
                        e = document.getElementById("authorization_women_exco");
                    }
                    break;
                case "youth_exco":
                    {
                        e = document.getElementById("authorization_youth_exco");
                    }
                    break;
                case "member":
                    {
                        e = document.getElementById("authorization_member");
                    }
                    break;
                case "children":
                    {
                        e = document.getElementById("authorization_children");
                    }
                    break;
            }

            if (e !== null) {
                $(e).prop("checked", true).checkboxradio('refresh');
                ChurchApp.createPrivilegeFeaturesElements(true, group);
            }
        }

    };

    userFoundHTML = function (user) {
        var fullName = user.firstName + ' ' + user.lastName;
        return '<li><a href="#">'
                + '<img src="' + user.profilePhotoUrl + '" height="100%">'
                + '<h6 style="margin-top:-5px !important; margin-bottom:-5px !important;">' + fullName + '</h6>'
                + '<p>'
                + 'Age: ' + user.ageRange + '<br/>'
                + user.sex
                + '</p>'
                + '<div style="position:absolute;right:0.75em; top:0.75em;">'
                + user.username
                + '</div>'
                + '<input type="hidden" name="username" value="' + user.username + '"/>'
                + '<input type="hidden" name="full_name" value="' + fullName + '"/>'
                + '<input type="hidden" name="designation" value="' + user.designation + '"/>'
                + '</a>'
                + '<a href="#" data-rel="popup" data-position-to="window" data-transition="pop">User profile</a>'
                + '</li>';
        /*return '<li><a><div class="ui-grid-a">'
         + '<div class="ui-block-a" style="width:80px">'
         + '<img src="' + user.profilePhotoUrl + '" width="60" height="60">'
         + '</div>'
         + '<div class="ui-block-b">'
         
         + '<div class="ui-grid-a">'
         
         + '<div class="ui-block-a">'
         + user.firstName + ' ' + user.lastName
         + '</div>'
         
         + '<div class="ui-block-a">'
         + user.ageRange
         + '</div>'
         
         + '<div class="ui-block-a">'
         + user.sex
         + '</div>'
         
         + '</div>'
         
         + '</div>'
         
         + '</div></a></li>';*/
    };

    this.renderAccountReconcile = function (obj) {

        if (typeof obj.reconcileTransactionButton === "string") {
            obj.reconcileTransactionButton = $("#" + obj.reconcileTransactionButton);
        }
        if (typeof obj.missingTransactionButton === "string") {
            obj.missingTransactionButton = $("#" + obj.missingTransactionButton);
        }
        if (typeof obj.unknownTransactionButton === "string") {
            obj.unknownTransactionButton = $("#" + obj.unknownTransactionButton);
        }
        if (typeof obj.conflictingTransactionButton === "string") {
            obj.conflictingTransactionButton = $("#" + obj.conflictingTransactionButton);
        }

        if (typeof obj.dataRenderDiv === "string") {
            obj.dataRenderDiv = $("#" + obj.conflictingTransactionButton);
        }

        var info_html = accountInfoHTML(obj);

        var main_html = "";

        if (obj.reconcileTransactionButton.is(":checked")) {
            main_html = reconcileTransactionHTML(obj);
        } else if (obj.missingTransactionButton.is(":checked")) {
            main_html = missingTransactionHTML(obj);
        } else if (obj.unknownTransactionButton.is(":checked")) {
            main_html = unknownTransactionHTML(obj);
        } else if (obj.conflictingTransactionButton.is(":checked")) {
            main_html = conflictingTransactionHTML(obj);
        }

        renderAccountReconcileView(obj.dataRenderDiv, info_html, main_html);

        obj.reconcileTransactionButton.on("click", function () {
            main_html = reconcileTransactionHTML(obj);
            renderAccountReconcileView(obj.dataRenderDiv, info_html, main_html);
        });

        obj.missingTransactionButton.on("click", function () {
            main_html = missingTransactionHTML(obj);
            renderAccountReconcileView(obj.dataRenderDiv, info_html, main_html);
        });

        obj.unknownTransactionButton.on("click", function () {
            main_html = unknownTransactionHTML(obj);
            renderAccountReconcileView(obj.dataRenderDiv, info_html, main_html);
        });

        obj.conflictingTransactionButton.on("click", function () {
            main_html = conflictingTransactionHTML(obj);
            renderAccountReconcileView(obj.dataRenderDiv, info_html, main_html);
        });

    };

    renderAccountReconcileView = function (container, info, main) {
        container.html(info + main);
        container.trigger('create');//enhance the view
    };

    accountInfoHTML = function (obj) {
        if (obj.bankName === null
                && obj.accountName === null
                && obj.accountNo === null
                && obj.statementPeriod === null) {
            return "";
        }

        return  '<div class="ui-grid-a">'
                + '<div class="ui-block-a">'
                + '<div class="ui-grid-b">'
                + '<div class="ui-block-a" style="font-family: fantasy; font-size: 12px; font-weight: bold; ">'
                //the bank name
                + obj.bankName
                + '</div>'
                + '<div class="ui-block-b" style="font-size: 12px; font-weight: 400;">'
                //the acc name
                + obj.accountName
                + '</div>'
                + '<div class="ui-block-c" style="font-size: 12px; font-weight: 700;">'
                //the acc no.
                + obj.accountNo
                + '</div>'
                + '</div>'

                + '</div>'
                + '<div class="ui-block-b" style="text-align: center;">'
                //the statement peroid
                + obj.statementPeriod
                + '</div>'
                + '</div>';
    };

    CrDrHTML = function (credit, dedit) {
        return "<div>" + "<span style='color:green; margin-top:0.5em; margin-bottom:0.5em;'>" + credit + "</span>(CR) , "
                + "<span style='color:red; margin-top:0.5em; margin-bottom:0.5em;'>" + dedit + "</span>(DR)" + "</div>";
    };

    twoColumnsGridHTML = function (html1, html2) {
        return '<div class="ui-grid-a ui-responsive">'
                + '<div class="ui-block-a">'
                + html1
                + '</div>'
                + '<div class="ui-block-b">'
                + html2
                + '</div>'
                + '</div>';
    };

    reconcileTransactionHTML = function (obj) {


    };

    missingTransactionHTML = function (obj) {

        //transaction in the banstatement but not in cash book

        var html = '<table data-role="table" data-mode="reflow" class="ui-responsive  table-report-view">';

        html += tableHeadHTML(obj.bankStatementColumns);

        html += "<tbody>";
        var t_cr = 0;
        var t_dr = 0;

        for (var i = 0; i < obj.bankStatementData.length; i++) {

            var bnk_code = obj.bankStatementData[i][1];
            var bnk_dr = obj.bankStatementData[i][3];
            var bnk_cr = obj.bankStatementData[i][4];
            var cb_dr = 0;
            var cb_cr = 0;
            var found = false;
            for (var k = 0; k < obj.cashBookData.length; k++) {
                var cb_code = obj.cashBookData[k][1];

                if (bnk_code === cb_code) {
                    cb_dr = obj.cashBookData[k][3];
                    cb_cr = obj.cashBookData[k][4];
                    found = true;
                    break;
                }
            }

            if (found) {
                t_cr += bnk_cr;
                t_dr += bnk_dr;
                html += tableRowsHTML(obj.bankStatementData[i]);
            }

        }

        html += "</tbody></table>";

        return CrDrHTML(t_cr, t_dr) + html;
    };

    unknownTransactionHTML = function (obj) {

        //transaction in the cash book but not in bank statement

        var html = '<table data-role="table" data-mode="reflow" class="ui-responsive  table-report-view">';

        html += tableHeadHTML(obj.cashBookColumns);

        html += "<tbody>";

        var t_cr = 0;
        var t_dr = 0;

        for (var i = 0; i < obj.cashBookData.length; i++) {

            var cb_code = obj.cashBookData[i][1];
            var cb_dr = obj.cashBookData[i][3];
            var cb_cr = obj.cashBookData[i][4];
            var bnk_dr = 0;
            var bnk_cr = 0;
            var found = false;

            for (var k = 0; k < obj.bankStatementData.length; k++) {

                var bnk_code = obj.bankStatementData[k][1];

                if (bnk_code === cb_code) {
                    bnk_dr = obj.bankStatementData[k][3];
                    bnk_cr = obj.bankStatementData[k][4];
                    found = true;
                    break;
                }
            }

            if (found) {
                t_cr += cb_dr;// t_cr is credit on the bank but debit in cash book - correct!
                t_dr += cb_cr;// t_dr is debit on the bank but credit in cash book - correct!
                html += tableRowsHTML(obj.cashBookData[i]);
            }
        }

        html += "</tbody></table>";

        return CrDrHTML(t_cr, t_dr) + html;
    };

    conflictingTransactionHTML = function (obj) {

        //transaction in the cash book whose tran code matchs one the the bank
        //statement but the figure in the credit/debit column of the cash book 
        //is not equal to the corresponding debit/credit column of the bank statement.

        var cb_html = '<table data-role="table" data-mode="reflow" class="ui-responsive  table-report-view">';
        cb_html += tableHeadHTML(obj.cashBookColumns);
        cb_html += "<tbody>";

        var bk_html = '<table data-role="table" data-mode="reflow" class="ui-responsive  table-report-view">';
        bk_html += tableHeadHTML(obj.bankStatementColumns);
        bk_html += "<tbody>";


        var t_cr = 0;
        var t_dr = 0;

        for (var i = 0; i < obj.bankStatementData.length; i++) {

            var bnk_code = obj.bankStatementData[i][1];
            var bnk_dr = obj.bankStatementData[i][3];
            var bnk_cr = obj.bankStatementData[i][4];

            for (var k = 0; k < obj.cashBookData.length; k++) {
                var cb_code = obj.cashBookData[k][1];
                var cb_dr = obj.cashBookData[k][3];
                var cb_cr = obj.cashBookData[k][4];

                if (bnk_code === cb_code && bnk_dr !== cb_cr) {
                    t_dr += cb_cr;
                    t_cr += cb_dr;
                    cb_html += tableRowsHTML(obj.cashBookData[k]);
                }

                //important
                if (bnk_code === cb_code && bnk_cr !== cb_dr) {
                    t_dr += cb_cr;
                    t_cr += cb_dr;
                    bk_html += tableRowsHTML(obj.bankStatementData[i]);
                }
            }

        }

        cb_html += "</tbody></table>";
        bk_html += "</tbody></table>";

        return CrDrHTML(t_cr, t_dr) + twoColumnsGridHTML(cb_html, bk_html);

    };

    this.DisplayStyle = {A: 1, B: 2, C: 3, D: 4, E: 5};

    this.createTable = function (tableProp) {
        var table_html = ChurchApp.createTableHtml(tableProp);

        var uniqueColIndex = -1;
        if (typeof tableProp.uniqueColumn === "string") {
            //get the index of the unique column
            for (var i = 0; i < tableProp.columns.length; i++) {
                if (tableProp.columns[i] === tableProp.uniqueColumn) {
                    uniqueColIndex = i;
                    break;
                }
            }
        }

        var container = tableProp.container;
        if (typeof tableProp.container === "string") {
            container = $("#" + container);
        }

        var filterForm = "";
        if (typeof tableProp.filterInputId === "string") {
            filterForm = '<form>'
                    + '<input id="' + tableProp.filterInputId + '" data-type="search" placeholder="Filter..." >'
                    + '</form>';
        }

        var delete_view_bottons = "";
        var deleteInput = '<input data-table-button="delete" type="button" value="Delete">';
        var viewInput = '<input data-table-button="view" type="button" value="View">';
        if (tableProp.deleteButton === true
                || tableProp.viewButton === true) {
            delete_view_bottons = '<div class="ui-grid-a">'
                    + '<div class="ui-block-a">'
                    + (tableProp.viewButton === true ? viewInput : deleteInput)
                    + '</div>'
                    + '<div class="ui-block-b">'
                    + (tableProp.viewButton === false ? "" : deleteInput)
                    + '</div>'

                    + '</div>';

        }

        var before_html = "";

        if (filterForm !== "" || delete_view_bottons !== "") {
            before_html = '<div class="ui-grid-b ui-responsive">'
                    + '<div class="ui-block-a">'
                    + filterForm
                    + '</div>'
                    + '<div class="ui-block-b">'
                    + '</div>'
                    + '<div class="ui-block-c">'
                    + delete_view_bottons
                    + '</div>'

                    + '</div>';
        }

        var html = before_html + table_html;
        container.html(html);
        container.trigger('create');

        var extraCols = 0;
        if (tableProp.selectByCheck === true) {
            extraCols++;
        }
        if (typeof tableProp.action === "object") {
            if (tableProp.action.delete === true) {
                extraCols++;
            }
            if (tableProp.action.view === true) {
                extraCols++;
            }
        }

        $(container).find('tbody tr td').each(function (index, e) {
            var col_count = tableProp.data[0].length + extraCols;
            var colIndex = index % col_count;
            var rowIndex = Math.floor(index / col_count);

            //alert("rowIndex = " + rowIndex);

            /*if (tableProp.selectByCheck === true
             && colIndex === 0) {
             $(e).on('click', function () {
             alert("colIndex = " + colIndex);
             });
             }*/

            if (typeof tableProp.action !== "object") {
                return;
            }

            if (tableProp.action.delete === true
                    && tableProp.action.view === true) {
                if (colIndex === col_count - 1) {
                    $(e).on('click', function () {//delete clicked
                        var obj = {};
                        obj.selectedIndices = [rowIndex];
                        obj.selectedRows = [tableProp.data[rowIndex]];
                        if (uniqueColIndex > -1) {
                            obj.selectedUniqueValues = [tableProp.data[rowIndex][uniqueColIndex]];
                        }
                        //uniqueColIndex
                        tableHanlerCallback(obj, tableProp.deleteHandler);
                    });
                } else if (colIndex === col_count - 2) {//view clicked
                    $(e).on('click', function () {
                        var obj = {};
                        obj.selectedIndex = rowIndex;
                        obj.selectedRow = tableProp.data[rowIndex];
                        if (uniqueColIndex > -1) {
                            obj.selectedUniqueValue = tableProp.data[rowIndex][uniqueColIndex];
                        }
                        tableHanlerCallback(obj, tableProp.viewHandler);
                    });
                }
            } else if (tableProp.action.delete === true
                    && colIndex === col_count - 1) {
                $(e).on('click', function () {
                    var obj = {};
                    obj.selectedIndices = [rowIndex];
                    obj.selectedRows = [tableProp.data[rowIndex]];
                    if (uniqueColIndex > -1) {
                        obj.selectedUniqueValues = [tableProp.data[rowIndex][uniqueColIndex]];
                    }
                    tableHanlerCallback(obj, tableProp.deleteHandler);
                });
            } else if (tableProp.action.view === true
                    && colIndex === col_count - 1) {
                $(e).on('click', function () {
                    var obj = {};
                    obj.selectedIndex = rowIndex;
                    obj.selectedRow = tableProp.data[rowIndex];
                    if (uniqueColIndex > -1) {
                        obj.selectedUniqueValue = tableProp.data[rowIndex][uniqueColIndex];
                    }
                    tableHanlerCallback(obj, tableProp.viewHandler);
                });
            }

        });


        $(container).off('click', '[data-table-check="all"]');

        $(container).on('click', '[data-table-check="all"]', {
            toggle: true,
        }, function (event) {
            $(container).find('tbody [data-table-check="checkbox"]').each(function (index, e) {
                e.checked = event.data.toggle;
            });
            event.data.toggle = !event.data.toggle;
        });

        $(container).off('click', '[data-table-button="delete"]');

        $(container).on('click', '[data-table-button="delete"]', function () {
            var obj = {};
            obj.selectedIndices = [];
            obj.selectedRows = [];
            obj.selectedUniqueValues = [];
            $(container).find('tbody [data-table-check="checkbox"]').each(function (index, e) {
                if (e.checked === true) {
                    obj.selectedIndices.push(index);
                    obj.selectedRows.push(tableProp.data[index]);
                    if (uniqueColIndex > -1) {
                        obj.selectedUniqueValues.push(tableProp.data[index][uniqueColIndex]);
                    }
                }
            });

            if (obj.selectedIndices.length > 0) {
                tableHanlerCallback(obj, tableProp.deleteHandler);
            }
        });

        $(container).off('click', '[data-table-button="view"]');

        $(container).on('click', '[data-table-button="view"]', function () {

            $(container).find('tbody [data-table-check="checkbox"]').each(function (index, e) {
                if (e.checked === true) {
                    var obj = {};
                    obj.selectedIndex = index;
                    obj.selectedRow = tableProp.data[index];
                    if (uniqueColIndex > -1) {
                        obj.selectedUniqueValue = tableProp.data[index][uniqueColIndex];
                    }
                    tableHanlerCallback(obj, tableProp.viewHandler);
                    return false;//break from the 'each' function
                }
            });

        });

    };

    tableHanlerCallback = function (obj, callback) {
        if (typeof callback === "function") {
            callback(obj);
        }
    };

    this.createTableHtml = function (tableProp) {
        switch (tableProp.style) {
            case this.DisplayStyle.A:
                return create_Style_A_Table_Html(tableProp);
            case this.DisplayStyle.B:
                return create_Style_B_Table_Html(tableProp);
            case this.DisplayStyle.C:
                return create_Style_C_Table_Html(tableProp);
            case this.DisplayStyle.D:
                return create_Style_D_Table_Html(tableProp);
            case this.DisplayStyle.E:
                return create_Style_E_Table_Html(tableProp);
            default :
                return create_Style_A_Table_Html(tableProp);
        }

        return "";
    };

    function tableHeadHTML(columns) {
        var th = "";
        for (var i = 0; i < columns.length; i++) {
            th += "<th data-priority='" + (i + 1) + "'>" + normalizeTableColumn(columns[i]) + "</th>";
        }

        if (th !== "") {
            return "<thead><tr>" + th + "</tr></thead>";
        }
        return "";
    }

    function tableRowsHTML(row) {
        var tr = "";
        for (var k = 0; k < row.length; k++) {
            tr += "<td>" + row[k] + "</td>";
        }
    }

    function create_Style_A_Table_Html(tableProp) {
        var id_attr = "";
        if (tableProp.Id !== ""
                && tableProp.Id !== null
                && typeof tableProp.Id !== "undefined") {
            id_attr = ' id="' + tableProp.Id + '" ';

        }

        var filter = "";
        if (typeof tableProp.filterInputId === "string") {
            filter = ' data-filter="true" data-input="#' + tableProp.filterInputId + '" ';
        }

        var table_html = '<table data-role="table" ' + id_attr + filter + ' data-mode="reflow" class="ui-responsive  table-report-view">';

        var action_col_span = 0;
        var deleteHTML = "";
        var viewHTML = "";
        var actionColumn = "";

        if (tableProp.action === true) {
            action_col_span = 2;
            actionColumn = "Action";
            viewHTML = "<a href='#'>View</a>";
            deleteHTML = "<a href='#'>Delete</a>";
            tableProp.action = {
                delete: true, //automatic - important!
                view: true, //automatic - important!
            };

        } else if (typeof tableProp.action === "object") {
            action_col_span = 2;

            if (tableProp.action.text === "string") {
                actionColumn = tableProp.action.text;
            } else {
                actionColumn = "Action";
            }

            if (tableProp.action.viewHTML === "string") {
                viewHTML = tableProp.action.viewHTML;
                tableProp.action.view = true;//automatic - important!
            } else {
                viewHTML = "<a href='#'>View</a>";
            }

            if (tableProp.action.deleteHTML === "string") {
                deleteHTML = tableProp.action.deleteHTML;
                tableProp.action.delete = true;//automatic - important!
            } else {
                deleteHTML = "<a href='#'>Delete</a>";
            }


            if (tableProp.action.view !== true) {//yes !== true
                action_col_span--;
            }

            if (tableProp.action.delete !== true) {//yes !== true
                action_col_span--;
            }


        }

        var caption = "";
        var thead = "";
        var tbody = "";

        if (tableProp.title !== ""
                && tableProp.title !== null
                && typeof tableProp.title !== "undefined") {
            caption = "<caption>" + tableProp.title + "</caption>";
        }

        var columns = tableProp.columns;
        var th = "";
        var extra = 0;

        if (tableProp.deleteButton === true
                || tableProp.viewButton === true) {
            tableProp.selectByCheck = true;//automatically this will be true
        }

        if (tableProp.selectByCheck === true) {
            extra = 1;
        }
        for (var i = 0; i < columns.length + extra + action_col_span; i++) {
            if (i === 0 && tableProp.selectByCheck === true) {
                th += "<th data-priority='" + (i + 1) + "'><a data-table-check='all' href='#'>All</a></th>";
                continue;
            }
            if (i === columns.length + extra) {

                th += "<th colspan='" + action_col_span + "' data-priority='" + (i + 1) + "'>" + actionColumn + "</th>";
                break;
            }
            th += "<th data-priority='" + (i + 1) + "'>" + normalizeTableColumn(columns[i - extra]) + "</th>";
        }

        if (th !== "") {
            thead = "<thead><tr>" + th + "</tr></thead>";
        }

        var rows = tableProp.data;

        for (var i = 0; i < rows.length; i++) {
            var td = "";
            for (var k = 0; k < rows[i].length + extra + action_col_span; k++) {
                if (k === 0 && tableProp.selectByCheck === true) {
                    td += "<td><input data-table-check='checkbox' type='checkbox'/></td>";
                    continue;
                }

                if (action_col_span === 1
                        && k === rows[i].length + extra) {
                    var h = tableProp.action.delete === true ? deleteHTML : viewHTML;
                    td += "<td>" + h + "</td>";
                    continue;
                } else if (action_col_span === 2
                        && k === rows[i].length + extra) {
                    td += "<td>" + viewHTML + "</td>";
                    continue;
                } else if (action_col_span === 2
                        && k === rows[i].length + extra + 1) {
                    td += "<td>" + deleteHTML + "</td>";
                    continue;
                }

                td += "<td>" + rows[i][k - extra] + "</td>";
            }
            if (td !== "") {
                tbody += "<tr>" + td + "</tr>";
            }
            if (i === rows.length - 1) {
                tbody = "<tbody>" + tbody + "</tbody>";
            }
        }

        if (thead === "" && tbody === "")
            return "";//no columns and data 

        table_html += caption + thead + tbody + "</table>";

        return table_html;
    }

    function create_Style_B_Table_Html(tableProp) {

        //TODO
    }

    function create_Style_C_Table_Html(tableProp) {
        //TODO

    }

    function create_Style_D_Table_Html(tableProp) {
        //TODO

    }

    function create_Style_E_Table_Html(tableProp) {
        //TODO

    }


    /**
     * The generalNewsObject is a json object (or any javascript object) which holds the list of news group in a json array.<br/>
     * The element of the json array is a news grpup.<br/>
     * The news group is a json array of related news items.<br/>
     * Each news item consist of one or more of the following properties listed below:<br/><br/>
     * 
     * newsId - an id that identifies the news.<br/>
     * newsDate  - the date recorded when the new was posted. e.g Tuesday, October 5, 2010 <br/>
     * newsTime  - the time recorded when the new was posted e.g 12 PM.<br/>
     * newsTitle - the title of the news.<br/>
     * newsImages - a number of image urls seperated by comma. 
     *              if the image is more than one the implemetation may display them in slide. <br/>
     * newsIntro - The news introduction that should be emphasized after title(header)<br/>
     * newsBody - the main news content.<br/>
     * newsLength - Length of the news intro plus news body. Usually used to determine 
     *              if the news body should be followed by a "More>>" link<br/>
     * 
     * @param {type} newsJson
     * @param {type} display_style - the new format style: e.g A,B,C,D,E
     * @returns {string}
     */
    this.createNewsHtml = function (newsJson, display_style) {
        switch (display_style) {
            case this.DisplayStyle.A:
                return create_Style_A_News_Html(newsJson);
            case this.DisplayStyle.B:
                return create_Style_B_News_Html(newsJson);
            case this.DisplayStyle.C:
                return create_Style_C_News_Html(newsJson);
            case this.DisplayStyle.D:
                return create_Style_D_News_Html(newsJson);
            case this.DisplayStyle.E:
                return create_Style_E_News_Html(newsJson);
        }

        return "";
    };

    create_Style_A_News_Html = function (newsObj) {
        if (newsObj.length === 0) {
            return "";
        }
        var newsGroupList = newsObj.newsGroupList;//array of news group objects
        var news_html = '<ul data-role="listview" data-inset="true">';
        for (var i = 0; i < newsGroupList.length; i++) {
            var news_group = newsGroupList[i];
            var title_html = "";
            var time_html = "";
            var body_html = "";
            var image_html = "";
            var intro_html = "";

            for (var k = 0; k < news_group.length; k++) {
                var news_item = news_group[k];
                if (k === 0) {
                    var date_header_html = '<li data-role="list-divider">' + news_item.newsDate + ' <span class="ui-li-count">' + news_group.length + '</span></li>';
                    news_html += date_header_html;
                }

                title_html = '<h2>' + news_item.newsTitle + '</h2>';
                time_html = '<p class="ui-li-aside"><strong>' + news_item.newsTime + '</strong>' + news_item.newsTimeAMPM + '</p>';
                body_html = '<div style="white-space: normal; font-size: 12px;">' + news_item.newsBody + '</div>';

                image_html = '';

                if (news_item.newsImages.length > 0) {
                    var news_image = news_item.newsImages[0];// for now use the first image. come back for slide approach where all the image will be used for slide.
                    image_html = '<img src=' + news_image + ' width="300" height="300" alt="N/A"/>';
                }

                intro_html = "";

                if (news_item.newsIntro !== "") {
                    intro_html = '<p style="white-space: normal;"><strong>' + news_item.newsIntro + '</strong></p>';
                }

                news_html += '<li style="padding: 10px;">';
                news_html += title_html + time_html + image_html + intro_html + body_html;
                news_html += '</li>';
            }


        }

        news_html += '</ul>';

        return news_html;
    };


    function create_Style_B_News_Html(newsJson) {
        //TODO

    }


    function create_Style_C_News_Html(newsJson) {
        //TODO

    }

    function create_Style_D_News_Html(newsJson) {
        //TODO

    }

    function create_Style_E_News_Html(newsJson) {
        //TODO

    }

    function createCarouselHTML(obj) {
        var width = 400;
        if (obj.carouselImages.length === 2) {
            width = 600;
        } else if (obj.carouselImages.length === 1) {
            width = 1200;
        }

        var html = "";
        for (var i = 0; i < obj.carouselImages.length; i++) {
            var img_url = obj.carouselImages[i];
            html += '<div><img src="' + img_url + '" width="' + width + '" height="300" ></img></div>';
        }

        if (html !== "") {
            html = "<div class='home-page-carousel'>" + html + "</div>";
        }

        return html;
    }

    this.inherit = function (proto) {
        function Foo() {
        }
        Foo.prototype = proto;
        return new Foo;
    };

    this.Member = function () {
        this.first_name = "";
        this.last_name = "";
        this.sex = "";
        this.age_range = "";
        this.birthday = "";
        this.address = "";
        this.phone_nos = "";
        this.occupation = "";
        this.nationality = "";
    };

    this.ChurchWorker = function () {
        this.prototype = this.inherit(new this.Member());
    };

    //Paid worker
    this.ChurchStaff = function () {
        this.prototype = this.inherit(new this.ChurchWorker());
    };

    this.Admin = function () {
        this.prototype = this.inherit(new this.Member());

    };

    this.Exco = function () {
        this.prototype = this.inherit(new this.ChurchWorker());
    };

    this.MenExco = function () {
        this.prototype = this.inherit(new this.Exco());
    };

    this.WomenExco = function () {
        this.prototype = this.inherit(new this.Exco());
    };

    this.YouthExco = function () {
        this.prototype = this.inherit(new this.Exco());
    };

    this.Accountant = function () {
        this.prototype = this.inherit(new this.ChurchWorker());
    };

    this.Pastor = function () {
        this.prototype = this.inherit(new this.ChurchWorker());
    };

    this.AssociatePastor = function () {
        this.prototype = this.inherit(new this.Pastor());
    };

    this.AssistancePastor = function () {
        this.prototype = this.inherit(new this.Pastor());
    };

    this.ResidentPastor = function () {
        this.prototype = this.inherit(new this.Pastor());
    };

    this.VicePresident = function () {
        this.prototype = this.inherit(new this.Pastor());
    };

    this.President = function () {
        this.prototype = this.inherit(new this.Pastor());
    };

};


