<div class="row">

    <!-- Profile Info and Notifications -->
    <div class="col-md-6 col-sm-8 clearfix">

        <ul class="user-info pull-left pull-none-xsm">

            <!-- Profile Info -->
            <li class="profile-info dropdown">
                <!-- add class "pull-right" if you want to place this from right -->
                    <img src="{{ asset('assets/admin/theme/images/thumb-1@2x.png') }}" alt="" class="img-circle" width="44" /> &nbsp;
                    <b>{{isset(Auth::user()->name) && Auth::user()->name != '' ? Auth::user()->name : 'Admin'}}</b>
            </li>
        </ul>
    </div>


    <!-- Raw Links -->
    <div class="col-md-6 col-sm-4 clearfix hidden-xs">
        <ul class="list-inline links-list pull-right">
            <li class="sep"></li>
            <li>
                <a href="javascript:void(0)" onclick="logout();"> Log Out <i class="entypo-logout right"></i> </a>
                <form id="logout-form" action="{{ route('logout-admin') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            </li>
        </ul>
    </div>

</div>
<script>
    function logout() {
       Swal.fire({
             title: 'Are you sure?',
             text: "Do you really want to logout?",
             icon: 'warning',
             showCancelButton: true,
             confirmButtonColor: '#2778c4',
             cancelButtonColor: '#d33',
             confirmButtonText: 'Yes,log me out!'
         }).then((result) => {
             if (result.isConfirmed) {
                 document.getElementById("logout-form").submit()
             }
         })
    }   
 </script>