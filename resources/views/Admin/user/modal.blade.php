 <div class="modal fade" id="userModal">
     <div class="modal-dialog">
         <div class="modal-content">

             <div class="modal-header">
                 <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                 <h4 class="modal-title">Add New User</h4>
             </div>

             <div class="modal-body">
                 <div class="alert alert-danger print-error-msg" style="display:none;">
                     <ul></ul>
                 </div>
                 <form id="addNewUserForm" name="addNewUserForm" onsubmit="return false" autocomplete="off">
                     {{ csrf_field() }}
                     <input type="hidden" id="userHdnID" name="userHdnID" value="">
                     <div class="form-group row">
                         <div class="col-sm-2">
                             <label>Name :</label>
                         </div>
                         <div class="col-sm-10">
                             <input type="text" class="form-control" name="userName" id="userName"
                                 placeholder="Enter Full Name" required>
                         </div>
                     </div>
                     <div class="form-group row">
                         <div class="col-sm-2">
                             <label>Email :</label>
                         </div>
                         <div class="col-sm-10">
                             <input type="text" class="form-control" name="email" id="email" placeholder="Enter Email"
                                 required>

                         </div>
                     </div>
                     <div class="form-group row">
                         <div class="col-sm-2">
                             <label>Password :</label>
                         </div>
                         <div class="col-sm-10 password">
                             <input type="password" class="form-control " name="password" id="password"
                                 placeholder="Enter password" required />

                         </div>
                     </div>
                     <div class="form-group row">
                         <div class="col-sm-2">
                             <label for="phone">Phone :</label>
                         </div>
                         <div class="col-sm-10">
                             <input type="text" class="form-control " id="phone" name="phone"
                                 onkeypress="return event.charCode >= 48 && event.charCode <= 57"
                                 placeholder="Enter phone number" required />
                         </div>
                     </div>
                     <div class="form-group row">
                         <div class="col-sm-2">
                             <label for="status">Status :</label>
                         </div>
                         <div class="col-sm-10">
                             <select class="form-control" id="status" name="status">
                                 <option value="1" selected>Active</option>
                                 <option value="0">Inactive</option>
                             </select>
                         </div>
                     </div>
                     <div class="form-group row is_block_msg" style="display: none">
                        <div class="col-sm-2">
                        </div>
                        <div class="col-sm-10">
                            <span class="text-danger">User is Blocked.</span>
                        </div>
                    </div>
                     <div class="form-group row is_block_class" style="display: none">
                        <div class="col-sm-2">
                            <label for="Block">Is Block :</label>
                        </div>
                        <div class="col-sm-10">
                            <select class="form-control" id="block" name="block">
                                <option value="1">Yes</option>
                                <option value="0">No</option>
                            </select>
                        </div>
                    </div>
             </div>

             <div class="modal-footer">
                 <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                 <button type="submit" class="btn btn-primary" id="addUserBtn">Add</button>
             </div>
             </form>

         </div>
     </div>
 </div>
