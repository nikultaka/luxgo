$(document).ready(function () {
    userDataTable();
    $("#addNewUser").on("click", function () {
        $("#userModal").modal("show");
    });
    $("#userModal").on("hidden.bs.modal", function () {
        $("#addNewUserForm")[0].reset();
        $("#userHdnID").val("");
        $("#password").rules("add", "required");
        $("#email").rules("add", "required");
        $("#email").css("cursor", "text");
        $("#email").prop("readonly", false);
        $(".modal-title").html("Add new user");
        $("#addUserBtn").html("Add");
        $('.is_block_class').css('display','none');
        $('.is_block_msg').css('display','none');
        
    });

    $('form[id="addNewUserForm"]').validate({
        rules: {
            userName: "required",
            email: {
                required: true,
                email: true,
                remote: {
                    url: BASE_URL + "/" + ADMIN + "/email/exist/or/not",
                    type: "get",
                    data: {
                        userHdnID: function () {
                            return $("#userHdnID").val();
                        },
                    },
                },
            },
            password: {
                required: true,
                minlength: 6,
            },
            phone: {
                required: true,
                minlength: 10,
                maxlength: 10,
            },
            status: "required",
        },
        messages: {
            userName: "This field is required",
            email: {
                required: "Password is required",
                email: "Enter valid email",
                remote: "That email address is already exist.",
            },
            password: {
                required: "Password is required",
                minlength: "Password must be at least 6 characters long",
            },
            phone: {
                required: "This field is required",
                minlength: "Phone number must be at least 10 characters long",
                maxlength: "Phone number must be at least 10 characters long",
            },
            status: "This field is required",
        },
        submitHandler: function (form) {
            var formData = new FormData($("#addNewUserForm")[0]);
            console.log(formData);
            showloader();
            $.ajax({
                url: BASE_URL + "/" + ADMIN + "/manage/users/save",
                type: "post",
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                success: function (responce) {
                    var data = JSON.parse(responce);
                    if (data.status == 1) {
                        $("#userName").val("");
                        $("#email").val("");
                        $("#password").val("");
                        $("#phone").val("");
                        $("#status").val("");
                        $("#userModal").modal("hide");
                        successMsg(data.msg);
                        hideloader();
                        userDataTable();
                    } else if (data.status == 0) {
                        printErrorMsg(data.msg);
                        hideloader();
                    } else {
                        errorMsg(data.msg);
                        hideloader();
                    }
                },
            });
        },
    });
});

function printErrorMsg(msg) {
    $(".print-error-msg").find("ul").html("");
    $(".print-error-msg").css("display", "block");
    $.each(msg, function (key, value) {
        $(".print-error-msg")
            .find("ul")
            .append("<li>" + value + "</li>");
    });
}

function userDataTable() {
    $("#userDataTable").DataTable({
        processing: true,
        serverSide: true,
        bDestroy: true,
        bAutoWidth: false,
        ajax: {
            type: "POST",
            url: BASE_URL + "/" + ADMIN + "/manage/users/dataTable",
            data: {
                _token: $("[name='_token']").val(),
            },
        },
        columns: [
            { data: "name", name: "name" },
            { data: "email", name: "email" },
            { data: "username", name: "username" },
            { data: "phone", name: "phone" },
            { data: "status", name: "status" },
            {
                data: "action",
                name: "action",
                orderable: false,
                searchable: false,
            },
        ],
    });
}

$(document).on("click", ".deleteUser", function () {
    var deleteID = $(this).data("id");
    Swal.fire({
        title: "Are you sure?",
        text: "You won't be able to revert this!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, delete it!",
    }).then((result) => {
        if (result.isConfirmed) {
            showloader();
            $.ajax({
                url: BASE_URL + "/" + ADMIN + "/manage/users/delete",
                type: "POST",
                data: {
                    id: deleteID,
                    _token: $("[name='_token']").val(),
                },
                success: function (response) {
                    var data = JSON.parse(response);
                    if (data.status == 1) {
                        successMsg(data.msg);
                        hideloader();
                        userDataTable();
                    } else {
                        errorMsg(data.msg);
                        hideloader();
                    }
                },
            });
        }
    });
});

$(document).on("click", ".editUser", function () {
    var deleteID = $(this).data("id");
    $.ajax({
        url: BASE_URL + "/" + ADMIN + "/manage/users/edit",
        type: "POST",
        data: {
            id: deleteID,
            _token: $("[name='_token']").val(),
        },
        success: function (response) {
            $("#addUserBtn").html("Update");
            $(".modal-title").html("Update User Data");
            $("#password").prop("required", false);
            $("#email").prop("required", false);
            $('.is_block_class').css('display','')
            var data = JSON.parse(response);
            if (data.status == 1) {
                var result = data.userData;
                $("#userHdnID").val(result.id);
                var hid = $("#userHdnID").val();
                if (hid != "" && hid != null) {
                    $("#password").rules("remove", "required");
                    $("#email").rules("remove", "required");
                }
                $("#userName").val(result.name);
                $("#email").val(result.email).prop("readonly", true);
                $("#email").css("cursor", "not-allowed");
                $("#phone").val(result.phone);
                $('select[name="status"]').val(result.status).trigger("change");
                $('select[name="block"]').val(result.is_block).trigger("change");
                if(result.is_block == 1){
                    $('.is_block_msg').css('display','');
                }
                $("#userModal").modal("show");
            }
        },
    });
});

$(document).on("click", ".blockUser", function () {
    var userID = $(this).data("id");
    Swal.fire({
        title: "Are you sure Want To Block User ?",
        text: "You won't be able to revert this!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, Block it!",
    }).then((result) => {
        if (result.isConfirmed) {
            showloader();
            $.ajax({
                url: BASE_URL + "/" + ADMIN + "/manage/users/blockuser",
                type: "POST",
                data: {
                    id: userID,
                    _token: $("[name='_token']").val(),
                },
                success: function (response) {
                    var data = JSON.parse(response);
                    if (data.status == 1) {
                        successMsg(data.msg);
                        hideloader();
                        userDataTable();
                    } else {
                        errorMsg(data.msg);
                        hideloader();
                    }
                },
            });
        }
    });
});

$(document).on("click", ".resetPassword", function () {
    var userID = $(this).data("id");
    showloader();
    $.ajax({
        url: BASE_URL + "/" + ADMIN + "/manage/users/resetPassword",
        type: "POST",
        data: {
            id: userID,
            _token: $("[name='_token']").val(),
        },
        success: function (response) {
            var data = JSON.parse(response);
            if (data.status == 1) {
                successMsg(data.msg);
                hideloader();
                Swal.fire({
                    title: "Link Sent",
                    text: "Link Sent Successfully To User Email",
                    icon: "success",
                    showConfirmButton: false, //hide OK button
                    timer: 2000
                })
            } else {
                errorMsg(data.msg);
                hideloader();
            }
        },
    });
});



// $('.vcard-button').on('click',function(){
//     var id = $(this).data('id');
//     showloader();
//     $.ajax({
//         url: BASE_URL + "/card/vcard",
//         type: "POST",
//         data: {
//             id: id,
//             _token: $("[name='_token']").val(),
//         },
//         success: function (response) {
//             var data = JSON.parse(response);
//             if (data.status == 0) {
//                 hideloader();
//                 Swal.fire({
//                     title: data.msg,
//                     text: "",
//                     icon: "error",
//                     showConfirmButton: false, //hide OK button
//                     timer: 2000,
//                 });
//             } else {
//                 Swal.fire({
//                     title: "Something Went Wrong !",
//                     text: "",
//                     icon: "error",
//                     showConfirmButton: false, //hide OK button
//                     timer: 2000,
//                 });
//                 hideloader();
//             }
//         },
//     });

// });
