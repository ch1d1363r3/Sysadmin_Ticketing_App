//################################################################################
// @Name : js/parameters.js
// @Description : script to parameters page
// @call : ./admin/parameters.php
// @parameters : 
// @Author : Flox
// @Create : 06/11/2020
// @Update : 06/11/2020
// @Version : 3.2.6
//################################################################################

//CTRL+S to save ticket 
$(document).keydown(function(e) {
    var key = undefined;
    var possible = [ e.key, e.keyIdentifier, e.keyCode, e.which ];
    while (key === undefined && possible.length > 0)
    {
        key = possible.pop();
    }
    if (key && (key == '115' || key == '83' ) && (e.ctrlKey || e.metaKey) && !(e.altKey))
    {
        e.preventDefault();
         $('#general_form #submit_general').click();
         $('#connector_form #submit_connector').click();
         $('#function_form #submit_function').click();
        return false;
    }
    return true;
}); 