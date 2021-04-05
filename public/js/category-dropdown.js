function getCategoriesDropDown(url, input_id ,selected_id){

    $.ajax({
        type: "get",
        url: url,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        dataType: 'json',
        success: function (data) {
            //var label = capitalizeFirstLetter(input_id);
            //$('#'+input_id).empty();
            //$("#"+input_id).append('<option value="">Select '+label+'</option>');
            $("#"+input_id).append(data.categories);
            if($("#"+input_id+" option[value='"+selected_id+"']").length > 0){
                $('#'+input_id).val(selected_id);
            }
        },
        error: function (data) {
        }
    }); 
}

function capitalizeFirstLetter(string) {
  return string.charAt(0).toUpperCase() + string.slice(1);
}
