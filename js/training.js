/** 
 * Javascript for Training module
 * 
 * Author: Greg London
 * 
 */

/**
 * 
 * Function asks user to confirm before deleting training records
 * 
 */
function confirmSubmit() {
    return confirm('Are you sure you want to delete the selected training(s)?');
    
    if(false) {
        return false;
    }
    else
        return true;
    }
    
 /**
  * jQuery for datePicker...
  * 
  */
$(document).ready(function () {
    $( "#datepicker" ).datepicker({
      changeMonth: true,
      changeYear: true 
    });
});