$(document).ready(function () {
    let url = "" + location;
    const id = url.split("/").pop();
    $("#devicesdetailsHdnID").val("");
    $("#devicesdetailsHdnID").val(id);

    $('form[id="addNewDevicesdetailsForm"]').validate({
        rules: {
            userName: "required",
            email: "required",
            // password: "required",
        },
        messages: {
            userName: "Name is required",
            email: "Email is required",
            // password: "Password is required",
        },
        submitHandler: function (form) {
            var formData = new FormData($("#addNewDevicesdetailsForm")[0]);
            showloader();
            $.ajax({
                url: BASE_URL + "/" + ADMIN + "/devices/save_devicedetails",
                type: "post",
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                success: function (responce) {
                    var data = JSON.parse(responce);
                    if (data.status == 1) {
                        $("#password").val("");
                        Swal.fire({
                            title: data.msg,
                            text: "",
                            icon: "success",
                            showConfirmButton: false, //hide OK button
                            timer: 2000,
                        });
                        hideloader();
                    } else if (data.status == 0) {
                        Swal.fire({
                            title: data.msg,
                            text: "",
                            icon: "error",
                            showConfirmButton: false, //hide OK button
                            timer: 2000,
                        });
                        hideloader();
                    } else if (data.status == 2) {
                        hideloader();
                        Swal.fire({
                            title: data.msg,
                            text: "",
                            icon: "error",
                            showConfirmButton: false, //hide OK button
                            timer: 2000,
                        });
                    } else if (data.status == 3) {
                        hideloader();
                        console.log(data.msg);
                        Swal.fire({
                            title: data.msg,
                            text: "",
                            icon: "error",
                            showConfirmButton: false, //hide OK button
                            timer: 2000,
                        });
                    } else {
                        Swal.fire({
                            title: data.msg,
                            text: "",
                            icon: "error",
                            showConfirmButton: false, //hide OK button
                            timer: 2000,
                        });
                        hideloader();
                    }
                },
            });
        },
    });

    $("#sendInfo-button").on("click", function () {
        var email = $(this).data("email");
        $("#sendInfo").modal("show");
        $('#hdnmail').val('');
        $('#hdnmail').val(email);

        $("#sendInfo").on("hidden.bs.modal", function () {
            $("#addonbutton").css("display", "block");
            $("#div2").css("display", "none");
            $('#name').val('');
            $('#email').val('');
            $('#phonenumber').val('');
            $('#job').val('');
            $('#company').val('');
            $('#note').val('');
        });


        $('form[id="sendDetails"]').validate({
            rules: {
              name: 'required',
              email: {
                required: true,
                email: true
            },
              phonenumber: 'required',
            },
            messages: {
              name: 'Name is required',
              messages: {       
                required: "Email is required",  
                email: "Please enter a valid email address"
            },
            phonenumber: 'Phone Number is required',
            },
            submitHandler: function(form) {
                showloader();
                $.ajax({
                    type: 'POST',
                    url: BASE_URL + '/senddata/mail',
                    data: $('#sendDetails').serialize(),
                    success: function (responce) {
                        var data = JSON.parse(responce);
                        if (data.status == 1) {
                            $('#sendInfo').modal('hide');
                            Swal.fire({
                                title: data.msg,
                                text: "",
                                icon: "success",
                                showConfirmButton: false, //hide OK button
                                timer: 2000,
                            });
                            $('#name').val('');
                            $('#email').val('');
                            $('#phonenumber').val('');
                            $('#job').val('');
                            $('#company').val('');
                            $('#note').val('');
                            hideloader();
                        } else if (data.status == 0) {
                            $('#sendInfo').modal('hide');
                            Swal.fire({
                                title: data.msg,
                                text: "",
                                icon: "error",
                                showConfirmButton: false, //hide OK button
                                timer: 2000,
                            });
                            hideloader();
                        }
                        else {
                            $('#sendInfo').modal('hide');
                            Swal.fire({
                                title: data.msg,
                                text: "",
                                icon: "error",
                                showConfirmButton: false, //hide OK button
                                timer: 2000,
                            });
                            hideloader(); 
                        }
                    }
                });
            }
          });
    });

    $("#addmore-button").on("click", function () {
        $("#addonbutton").css("display", "none");
        $("#div2").css("display", "block");
    });
});
