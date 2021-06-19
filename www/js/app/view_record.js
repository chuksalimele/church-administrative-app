/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$(document).ready(function () {


    $(document).on("pagecreate", "#table-view-page", function () {
        //alert("demo");

        /*$.ajax({
         type: 'POST', // define the type of HTTP verb we want to use (POST for our form)
         url: 'php/TestTable.php', // the url where we want to POST
         data: "", // our data object
         //dataType: 'json' // what type of data do we expect back from the server
         })
         // using the done promise callback
         .done(function (data) {
         //alert(data);
         var json = JSON.parse(data);
         if (json.status === "success") {
         this.tableData = "";
         
         ChurchApp.renderTablePage({
         tableTitle: json.data.table_title,
         tableColumns: json.data.table_columns,
         tableData: json.data.table_data,
         style: ChurchApp.DisplayStyle.A,
         pageId: "table-view-page-content",
         pageTitle: "Page Title",
         headerTitle: "Page Header Title",
         updateUrl: "#",
         deleteUrl: "#",
         inactivityTimeout: 180,
         validationRules: {
         "COS_1": "required"
         },
         validationMessages: {
         "COS_1": "Please enter this field."
         },
         });
         
         }
         })
         // using the fail promise callback
         .fail(function (data, r, error) {
         
         });*/
    });



});