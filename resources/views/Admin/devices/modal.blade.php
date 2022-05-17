<div class="modal fade" id="devicesModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Add New Device</h4>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger print-error-msg" style="display:none;">
                    <ul></ul>
                </div>
                <form id="addNewDevicesForm" name="addNewDevicesForm" onsubmit="return false" autocomplete="off">
                    {{ csrf_field() }}
                    <input type="hidden" id="devicesHdnID" name="devicesHdnID" value="">
                    <div class="form-group row">
                        <div class="col-sm-3">
                            <label>Device Id :</label>
                        </div>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" name="deviceid" id="deviceid" placeholder="Enter Device Id" style="margin-left: -40px;  width: 108%;">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-3">
                            <label>Device Name :</label>
                        </div>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" name="deviceName" id="deviceName" placeholder="Enter Device Name" style="margin-left: -40px;  width: 108%;">
                        </div>
                    </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary" id="addDevicesBtn">Add</button>
            </div>
            </form>
        </div>
    </div>
</div>