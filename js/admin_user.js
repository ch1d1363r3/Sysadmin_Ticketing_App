//################################################################################
// @Name : js/admin_user.js
// @Description : script to admin user page
// @call : admin/user.php
// @parameters : 
// @Author : Flox
// @Create : 13/07/2021
// @Update : 13/07/2021
// @Version : 3.2.14
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
         $('#1 #modify').click();
         $('#1 #add').click();
        return false;
    }
    return true;
}); 