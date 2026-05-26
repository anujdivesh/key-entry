
$(document).ready(function(){
    $("#submit").click(function(){
    // AJAX Code To Submit Form.
    $.ajax({
    type: "POST",
    url: "ajaxsubmit.php",
    data: dataString,
    cache: false,
    success: function(result){
    alert(result);
    }
    });
    return false;
    });
});
    