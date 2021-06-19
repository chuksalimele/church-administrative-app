/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

//var ChurchApp = new ChurchApp();

function onRegisterParishHeadOf(e) {

    switch ($(e).val()) {
        case "":
            {
                $("#register-parish-division-name-cont").hide();
                $("#register-parish-division-name").val("");

                $("#register-parish-under-area-cont").show();
                $("#register-parish-under-zone-cont").show();
                $("#register-parish-under-province-cont").show();
                $("#register-parish-under-region-cont").show();
                $("#register-parish-under-national-cont").show();
            }
            break;
        case "Area":
            {
                $("#register-parish-division-name-cont").show();
                
                $("#register-parish-under-area-cont").show();
                $("#register-parish-under-zone-cont").show();
                $("#register-parish-under-province-cont").show();
                $("#register-parish-under-region-cont").show();
                $("#register-parish-under-national-cont").show();
            }
            break;
        case "Zone":
            {
                $("#register-parish-division-name-cont").show();
                
                $("#register-parish-under-area-cont").hide();
                $("#register-parish-under-area").val("");
                
                $("#register-parish-under-zone-cont").show();                
                $("#register-parish-under-province-cont").show();
                $("#register-parish-under-region-cont").show();
                $("#register-parish-under-national-cont").show();
            }
            break;
        case "Province":
            {
                $("#register-parish-division-name-cont").show();
                
                $("#register-parish-under-area-cont").hide();
                $("#register-parish-under-area").val("");
                
                $("#register-parish-under-zone-cont").hide();
                $("#register-parish-under-zone").val("");
                
                $("#register-parish-under-province-cont").show();                
                $("#register-parish-under-region-cont").show();
                $("#register-parish-under-national-cont").show();
            }
            break;
        case "Region":
            {
                $("#register-parish-division-name-cont").show();
                
                $("#register-parish-under-area-cont").hide();
                $("#register-parish-under-area").val("");
                
                $("#register-parish-under-zone-cont").hide();
                $("#register-parish-under-zone").val("");
                
                $("#register-parish-under-province-cont").hide();
                $("#register-parish-under-province").val("");
                
                $("#register-parish-under-region-cont").show();                
                $("#register-parish-under-national-cont").show();
            }
            break;
        case "National":
            {
                $("#register-parish-division-name-cont").show();
                
                $("#register-parish-under-area-cont").hide();
                $("#register-parish-under-area").val("");
                
                $("#register-parish-under-zone-cont").hide();
                $("#register-parish-under-zone").val("");
                
                $("#register-parish-under-province-cont").hide();
                $("#register-parish-under-province").val("");
                
                $("#register-parish-under-region-cont").hide();
                $("#register-parish-under-region").val("");
                
                $("#register-parish-under-national-cont").show();
            }
            break;
    }
}

function createInnerComponent(e, id_suffix) {
    ChurchApp.createPrivilegeFeaturesElements(e.checked, id_suffix);
}
