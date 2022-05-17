$(document).ready(function () {
    readData();
    $('#roleModelBtn').click(function () {
        $('#roleModal').modal('show');

    });
    $('#roleModal').on('hidden.bs.modal', function () {
        $('#Roleform')[0].reset();
        $('#submitbtn').html('Add');
        $('#hid').val('');
        $('.print-error-msg').hide();
        $('#roleModalLabel').html('Add Role');
        $('label[class="error"]').remove();
        $("#Roleform").removeClass("was-validated");
        $('select').removeClass('error');
    });
    $('form[id="Roleform"]').validate({
        rules: {
          name: 'required',
        },
        messages: {
          name: 'Role is required',
        },
        submitHandler: function(form) {
            showloader();
            $.ajax({
                type: 'POST',
                url: BASE_URL + '/' + ADMIN + '/role/add',
                data: $('#Roleform').serialize(),
                success: function (responce) {
                    var data = JSON.parse(responce);
                    if (data.status == 1) {
                        $('#hid').val('');
                        $('#name').val('');
                        $('#roleModal').modal('hide');
                        successMsg(data.msg);
                        readData();
                        hideloader();
                    } else if (data.status == 0) {
                        printErrorMsg(data.error)
                        hideloader();
                    }
                    else {
                        errorMsg(data.msg);
                        hideloader();
                    }


                }

            });

        }
      });

});
function printErrorMsg(msg) {
    $(".print-error-msg").find("ul").html('');
    $(".print-error-msg").css('display', 'block');
    $.each(msg, function (key, value) {
        $(".print-error-msg").find("ul").append('<li>' + value + '</li>');
    });
}

function readData() {
    $('#roleTable').DataTable({

        processing: true,

        "bDestroy": true,
        "bAutoWidth": false,


        serverSide: true,

        "ajax": {
            type: 'POST',
            url: BASE_URL + '/' + ADMIN + '/role/datatable',
            data: {
                "_token": $("[name='_token']").val(),

            },

        },

        columns: [

            { data: 'id', name: 'id' },

            { data: 'name', name: 'name' },

            { data: 'status', name: 'status' },

            { data: 'action', name: 'action', orderable: false, searchable: false },

        ]

    });
}

function delete_role(id) {
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            showloader();
            $.ajax({
                url: BASE_URL + '/' + ADMIN + '/role/delete',
                type: 'POST',
                data: {
                    'id': id,
                    "_token": $("[name='_token']").val(),
                },
                success: function (response) {
                    var data = JSON.parse(response);
                    if (data.status == 1) {
                        successMsg(data.msg);
                        readData();
                        hideloader();
                    } else {
                        errorMsg(data.msg);
                        hideloader();
                    }
                }
            });
        }
    });



}
function edit_role(id) {
    showloader();
    $.ajax({
        url: BASE_URL + '/' + ADMIN + '/role/edit',
        type: 'POST',
        data: {
            "_token": $("[name='_token']").val(),
            'id': id,
        },
        success: function (responce) {
            $('#hid').val();
            var data = JSON.parse(responce);
            if (data.status == 1) {
                var result = data.user;
                $('#roleModal').modal('show');
                $('#submitbtn').html('Update');
                $('#roleModalLabel').html('Update Role');
                $('#hid').val(result.id);
                $('#name').val(result.name);
                $('select[name="status"]').val(result.status).trigger("change");
                hideloader();
            }

        }

    });

}
