$(document).ready(function () {
    devicesDataTable();
    $("#addNewDevices").on("click", function () {
        $("#devicesModal").modal("show");
    });
    $("#devicesModal").on("hidden.bs.modal", function () {
        $("#addNewDevicesForm")[0].reset();
        $("#devicesHdnID").val("");
        $(".modal-title").html("Add new devices");
        $("#addDevicesBtn").html("Add");
        
    });

    $('form[id="addNewDevicesForm"]').validate({
        rules: {
            devicesName: "required",
            status: "required",
        },
        messages: {
            devicesName: "This field is required",
            status: "This field is required",
        },
        submitHandler: function (form) {
            var formData = new FormData($("#addNewDevicesForm")[0]);
            console.log(formData);
            showloader();
            $.ajax({
                url: BASE_URL + "/" + ADMIN + "/devices/save",
                type: "post",
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                success: function (responce) {
                    var data = JSON.parse(responce);
                    if (data.status == 1) {
                        $("#deviceid").val("");
                        $("#deviceName").val("");
                        $("#devicesModal").modal("hide");
                        successMsg(data.msg);
                        hideloader();
                        devicesDataTable();
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

function devicesDataTable() {
    $("#devicesDataTable").DataTable({
        processing: true,
        serverSide: true,
        bDestroy: true,
        bAutoWidth: false,
        ajax: {
            type: "POST",
            url: BASE_URL + "/" + ADMIN + "/devices/dataTable",
            data: {
                _token: $("[name='_token']").val(),
            },
        },
        columns: [
            { data: "device_id", name: "deviceName" },
            { data: "device_name", name: "deviceid" },
            {
                data: "action",
                name: "action",
                orderable: false,
                searchable: false,
            },
        ],
    });
}

$(document).on("click", ".deleteDevices", function () {
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
                url: BASE_URL + "/" + ADMIN + "/devices/delete",
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
                        devicesDataTable();
                    } else {
                        errorMsg(data.msg);
                        hideloader();
                    }
                },
            });
        }
    });
});

$(document).on("click", ".editDevices", function () {
    var deleteID = $(this).data("id");
    $.ajax({
        url: BASE_URL + "/" + ADMIN + "/devices/edit",
        type: "POST",
        data: {
            id: deleteID,
            _token: $("[name='_token']").val(),
        },
        success: function (response) {
            $("#addDevicesBtn").html("Update");
            $(".modal-title").html("Update Device Data");
            var data = JSON.parse(response);
            if (data.status == 1) {
                var result = data.devicesData;
                $("#devicesHdnID").val(result.id);
                $("#deviceid").val(result.device_id);
                $("#deviceName").val(result.device_name);
                $("#devicesModal").modal("show");
            }
        },
    });
});



