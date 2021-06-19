/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

var ChurchApp = function () {

    this.Util = new function () {



        this.isMobileDeviceSize = function () {
            return window.screen.width <= 768 || window.screen.height <= 768;
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

    };

    this.createHomePageGridHtml = function (news_id, news_title, news_time_ago, news_content, news_photo) {

    };

};

ChurchApp.prototype.inherit = function (proto) {
    function Foo() {
    }
    Foo.prototype = proto;
    return new Foo;
};

ChurchApp.prototype.Member = function () {
    this.first_name = "chuks";
    this.last_name = "";
    this.sex = "";
    this.age_range = "";
    this.birthday = "";
    this.address = "";
    this.phone_nos = "";
    this.occupation = "";
    this.nationality = "";
};

//Unpaid worker
var cv = new ChurchApp();
ChurchApp.prototype.ChurchWorker = function () {
    this.prototype = cv.inherit(new cv.Member());
};

//Paid worker
ChurchApp.prototype.ChurchStaff = function () {
    this.prototype = cv.inherit(new cv.ChurchWorker());
};

ChurchApp.prototype.Admin = function () {
    this.prototype = cv.inherit(new cv.Member());

    this.adminLeftMenuHTML = function () {
        //Configuration collapsible menu start
        return '<div data-role="collapsible" data-inset="false">'
                + '<h3>Configuration</h3>'

                + '<ul data-role="listview">'

                + '<li><a href="#">Authorization</a></li>'
                + '<li><a href="#">Department</a></li>'
                + '<li><a href="#">Designation</a></li>'

                //Church Structure collapsible menu start
                + '<div data-role="collapsible" data-inset="false" style="margin-right: 0px;margin-left: 0px;">'
                + '<h3>Church Structure</h3>'
                + '<ul data-role="listview">'
                + '<li><a href="#">National</a></li>'
                + '<li><a href="#">Region</a></li>'
                + '<li><a href="#">Province</a></li>'
                + '<li><a href="#">Zone</a></li>'
                + '<li><a href="#">Area</a></li>'
                + '</ul>'
                + '</div>'

                //Monetary collapsible menu start
                + '<div data-role="collapsible" data-inset="false" style="margin-right: 0px;margin-left: 0px;">'
                + '<h3>Monetary</h3>'
                + '<ul data-role="listview">'
                + '<li><a href="#">Bank</a></li>'
                + '<li><a href="#">Denomination</a></li>'
                + '<li><a href="#">Service Income Category</a></li>'
                + '<li><a href="#">Inflow Category</a></li>'
                + '<li><a href="#">Expense Category</a></li>'
                + '</ul>'
                + '</div>'

                //Activities collapsible menu start
                + '<div data-role="collapsible" data-inset="false" style="margin-right: 0px;margin-left: 0px;">'
                + '<h3>Activities </h3>'
                + '<ul data-role="listview">'
                + '<li><a href="#">Service</a></li>'
                + '<li><a href="#">Events</a></li>'
                + '</ul>'
                + '</div>'


                + '</ul>'
                + '</div>'

                + '<div data-role="collapsible" data-inset="false">'
                + '<h3>Entry/Record</h3>'
                + '<ul data-role="listview">'

                + '<li><a href="#">Daily Activities Schedule</a></li>'
                + '<li><a href="#">Membership</a></li>'

                //Income Summary collapsible menu start
                + '<div data-role="collapsible" data-inset="false" style="margin-right: 0px;margin-left: 0px;">'
                + '<h3>Income Summary</h3>'
                + '<ul data-role="listview">'
                + '<li><a href="#">Service Income</a></li>'
                + '<li><a href="#">Daily Income</a></li>'
                + '</ul>'
                + '</div>'


                //Events collapsible menu start
                + '<div data-role="collapsible" data-inset="false" style="margin-right: 0px;margin-left: 0px;">'
                + '<h3>Events</h3>'
                + '<ul data-role="listview">'
                + '<li><a href="#">Birthday</a></li>'
                + '<li><a href="#">Wedding</a></li>'
                + '<li><a href="#">Child Dedication</a></li>'
                + '<li><a href="#">Member Thanksgiving</a></li>'
                + '</ul>'
                + '</div>'


                //Weekly Activities  collapsible menu start
                + '<div data-role="collapsible" data-inset="false" style="margin-right: 0px;margin-left: 0px;">'
                + '<h3>Weekly Activities</h3>'
                + '<ul data-role="listview">'
                + '<li><a href="#">Home Fellowship</a></li>'
                + '<li><a href="#">Digging Deep</a></li>'
                + '<li><a href="#">Faith Clinic</a></li>'
                + '<li><a href="#">Rehearsal</a></li>'
                + '</ul>'
                + '</div>'


                //Monthly Activities  collapsible menu start
                + '<div data-role="collapsible" data-inset="false" style="margin-right: 0px;margin-left: 0px;">'
                + '<h3>Monthly Activities</h3>'
                + '<ul data-role="listview">'
                + '<li><a href="#">General Thanksgiving  </a></li>'
                + '</ul>'
                + '</div>'

                + '</ul>'
                + '</div>'
                ;

    };
};

ChurchApp.prototype.Exco = function () {
    this.prototype = cv.inherit(new cv.ChurchWorker());
};

ChurchApp.prototype.MenExco = function () {
    this.prototype = cv.inherit(new cv.Exco());
};

ChurchApp.prototype.WomenExco = function () {
    this.prototype = cv.inherit(new cv.Exco());
};

ChurchApp.prototype.YouthExco = function () {
    this.prototype = cv.inherit(new cv.Exco());
};

ChurchApp.prototype.Accountant = function () {
    this.prototype = cv.inherit(new cv.ChurchWorker());
};

ChurchApp.prototype.Pastor = function () {
    this.prototype = cv.inherit(new cv.ChurchWorker());
};

ChurchApp.prototype.AssociatePastor = function () {
    this.prototype = cv.inherit(new cv.Pastor());
};

ChurchApp.prototype.AssistancePastor = function () {
    this.prototype = cv.inherit(new cv.Pastor());
};

ChurchApp.prototype.ResidentPastor = function () {
    this.prototype = cv.inherit(new cv.Pastor());
};

ChurchApp.prototype.VicePresident = function () {
    this.prototype = cv.inherit(new cv.Pastor());
};

ChurchApp.prototype.President = function () {
    this.prototype = cv.inherit(new cv.Pastor());
};
