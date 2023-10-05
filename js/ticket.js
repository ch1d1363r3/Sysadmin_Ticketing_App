//################################################################################
// @Name : js/ticket.js
// @Description : script to ticket page
// @call : ticket.php
// @parameters : 
// @Author : Flox
// @Create : 21/09/2020
// @Update : 15/04/2021
// @Version : 3.2.15 p1
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
         $('#myform #modify').click();
        return false;
    }
    return true;
}); 

//update subcat list in category switch case
$('#category').change(function(){ //detect category switch
    //get value
    var CategorySelected = $(this).val();
    //replace subcat field with new associated values
    $.ajax({
        url:"ajax/ticket_subcat_db.php",
        type:"post",
        data: {CategoryId: CategorySelected},
        async:true,
        success: function(result) {
            var data = JSON.parse(result);
            //reset and populate subcat field
            $("#subcat").empty();
            jQuery.each(data, function(index, value){
                $("#subcat").append("<option value='"+value['id']+"'>"+value['name']+"</option>");
            });
        },
        error: function() {
            console.log('ERROR : unable to get subcat for category '+CategorySelected)
        }
    });
    //remove warning label if value is selected
    if(CategorySelected!=0) {$('#warning_empty_category').css('display', 'none');} else {$('#warning_empty_category').css('display', 'inline');}
});	

//datetimepicker default icons 
$.fn.datetimepicker.Constructor.Default = $.extend({}, $.fn.datetimepicker.Constructor.Default, {
    icons: { time: 'fa fa-clock text-info',date: 'fa fa-calendar text-info',up: 'fa fa-arrow-up',down: 'fa fa-arrow-down', previous: 'fa fa-chevron-left',next: 'fa fa-chevron-right',today: 'fa fa-calendar-check-o',clear: 'fa fa-trash',close: 'fa fa-times'} 
});

//datetimepicker date format 
var date = moment($('#date_create').val(), 'DD-MM-YYYY hh:mm:ss').toDate();
$('#date_create').datetimepicker({ date:date, format: 'DD/MM/YYYY HH:mm:ss' });
var date = moment($('#date_res').val(), 'DD-MM-YYYY hh:mm:ss').toDate();
$('#date_res').datetimepicker({ date:date, format: 'DD/MM/YYYY HH:mm:ss'});
var date = moment($('#date_hope').val(), 'DD-MM-YYYY').toDate();
$('#date_hope').datetimepicker({ date:date, format: 'DD/MM/YYYY' });
$('#add_calendar_start').datetimepicker({format: 'DD/MM/YYYY HH:mm:ss'});
$('#add_calendar_end').datetimepicker({format: 'DD/MM/YYYY HH:mm:ss'});
$('#add_reminder').datetimepicker({format: 'DD/MM/YYYY HH:mm:ss'});
var date = moment($('#user_validation_date').val(), 'DD-MM-YYYY').toDate();
$('#user_validation_date').datetimepicker({ date:date, format: 'DD/MM/YYYY' });

//remove warning before technician field is value is detected
$('#technician').change(function(){
    if($(this).val()!=0) {$('#technician_warning').css('display', 'none');}
    if($(this).val()==0) {$('#technician_warning').css('display', '');}
})

//update asset field on user change
if (myform.asset_id != undefined) {
   $('#user').change(function(){ 
        var UserSelected = $(this).val(); //get user id value
        $("#asset_id").empty();
        //replace asset field data with associated data
        $.ajax({
            url:"ajax/ticket_asset_db.php",
            type:"post",
            data: {UserId: UserSelected},
            async:true,
            success: function(result) {
                var data = JSON.parse(result);
                if($.trim(data)) //if data
                {
                  //reset and populate asset field
                    $("#asset_id").empty();
                    jQuery.each(data, function(index, value){
                        if(value['netbios'])
                        {
                            $("#asset_id").append("<option value='"+value['id']+"'>"+value['netbios']+"</option>");
                        }
                    });
                }
            },
            error: function() {
                console.log('ERROR : unable to get asset for selected user '+UserSelected)
            }
        });
    })
}

//field sender service
$('#user').change(function(){ 
    //check right
     var sender_service_field = document.getElementById("sender_service");
     if(sender_service_field)
     {
         var UserIdSelected = $(this).val(); //get user id value
         //update sender service field data of current user
         $.ajax({
             url:"ajax/ticket_sender_service_db.php",
             type:"post",
             data: {UserId: UserIdSelected},
             async:true,
             success: function(result) {
                 var data = JSON.parse(result);
                 if($.trim(data)) //if data
                 {
                     //reset and populate asset field
                     $("#sender_service").empty();
                     jQuery.each(data, function(index, value){
                         if(value['name'])
                         {
                             $("#sender_service").append("<option value='"+value['id']+"'>"+value['name']+"</option>");
                         }
                     });
                 }
             },
             error: function() {
                 console.log('ERROR : unable to get service for selected user '+UserSelected)
             }
         });
     }
 })

//display user validation fields
var user_validation_section = document.getElementById("user_validation_section");
if(user_validation_section){ //check if parameters are enable
    $("#user_validation_section").addClass('d-none');
    //read current state value and display user validation section if state is resolved
    var state = document.getElementById("state");
    if(state.value=='3') {$("#user_validation_section").removeClass('d-none')}
    //on state change to resolved display user validation section
    $('#state').change(function(){
        if($(this).val()=='3') {
            $("#user_validation_section").removeClass('d-none');
            //case type mark to need validation
            var user_validation = document.querySelector('input[name="user_validation"]:checked').value;
            if(user_validation == 1)
            {
                //calculate date to display
                var user_validation_delay_parameters = document.getElementById("user_validation_delay_parameters");
                var user_validation_delay_parameters = new Number(user_validation_delay_parameters.value);
                if(user_validation_delay_parameters != 0)
                {
                    var CurrentDate = new Date();
                    CurrentDate.setDate(CurrentDate.getDate() + user_validation_delay_parameters); 
                    var dd = CurrentDate.getDate();
                    var mm = CurrentDate.getMonth() + 1;
                    var y = CurrentDate.getFullYear();
                    var CalculateDate = dd + '/'+ mm + '/'+ y;
                    document.getElementById("user_validation_date").value = CalculateDate;
                } 
            }
        } else {
            $("#user_validation_section").addClass('d-none')
        }
    })
    //on user validation radio change fill date
    $('input[type=radio][name="user_validation"]').change(function(){
        if($(this).val()=='1') {
           //calculate date to display
            var user_validation_delay_parameters = document.getElementById("user_validation_delay_parameters");
            var user_validation_delay_parameters = new Number(user_validation_delay_parameters.value);
            if(user_validation_delay_parameters != 0)
            {
                var CurrentDate = new Date();
                CurrentDate.setDate(CurrentDate.getDate() + user_validation_delay_parameters); 
                var dd = CurrentDate.getDate();
                var mm = CurrentDate.getMonth() + 1;
                var y = CurrentDate.getFullYear();
                var CalculateDate = dd + '/'+ mm + '/'+ y;
                document.getElementById("user_validation_date").value = CalculateDate;
            } 
        } else {
            document.getElementById("user_validation_date").value = '';
        }
    })
}

//hide button on click
function HideSaveButton() {
    var bottom_button = document.getElementById("bottom_button");
    bottom_button.classList.add("d-none");
    var header_save_button = document.getElementById("header_save_button");
    header_save_button.classList.add("d-none");
}