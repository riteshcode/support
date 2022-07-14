<x-app-layout>
	<!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0 text-dark">{{ __('Users') }}</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Dashboard v2</li>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">

        <!-- Main row -->
        <div class="row">
          <!-- Left col -->
          <div class="col-md-12">

            <!-- TABLE: LATEST ORDERS -->
            <div class="card">
            <div class="card-header">
              <h3 class="card-title">
                <button type="button" class="btn btn-info" data-toggle="modal" data-target="#modal-default">
                    Add user
                </button>
              </h3>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
              <table id="example1" class="table table-bordered table-striped">
                <thead>
                <tr>
                  <th class="text-center">{{__('Sl No.')}}</th>
                  <th class="text-center">{{__('User name')}}</th>
                  <th class="text-center">{{__('Email-ID')}}</th>
                  <th class="text-center">{{__('User type') }}</th>
                  <th class="text-center">{{__('Status') }}</th>
                  <th class="text-center">{{__('Action') }}</th>
                </tr>
                </thead>
                <tbody>
                @if(!empty($userlist))
                	@foreach($userlist as $key=>$user)

	                <tr>
	                  <td class="text-center"> {{ $key+1 }} </td>
	                  <td class="text-center"> {{ $user->name}} </td>
	                  <td class="text-center"> {{ $user->email }} </td>
	                  <td class="text-center">
	                  	@if($user->roles)
	                  	<span class="badge badge-info text-uppercase"> {{ $user->roles[0]->name }} </span>
	                  	 @endif </td>
	                  <td class="text-center">
	                  	<span class="badge {{ ($user->status == 'active')?'badge-success':'badge-danger' }} "> {{ $user->status}} </span>
	                  </td>

	                  <td class="text-center">
	                  	<a href="{{ url('admin/user/'.$user->id.'/edit') }}" class="btn btn-sm btn-warning">
	                  		<i class="fa fa-eye" aria-hidden="true"></i>
	                  	</a>
	                  </td>
	                </tr>

            		@endforeach
            	@endif
                </tbody>
              </table>
            </div>
            <!-- /.card-body -->
          </div>
            <!-- /.card -->
          </div>
          <!-- /.col -->
        </div>
        <!-- /.row -->
      </div><!--/. container-fluid -->
    </section>
    <!-- /.content -->
    <div class="modal fade" id="modal-default">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h4 class="modal-title">Add User</h4>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <form action="{{ route('user.store') }}" method="POST">
                @csrf
                <div class="form-group">
                  <label>User Name</label>
                  <input type="text" name="username" value="" class="form-control" placeholder="Enter username" required="">
                </div>
                <div class="form-group">
                  <label>Email</label>
                  <input type="email" name="email" value="" class="form-control" placeholder="Enter email" required="">
                </div>
                
                <div class="form-group">
                  <label>Password</label>
                  <input type="password" name="password" value="" class="form-control" placeholder="Enter password" required="">
                </div>
                @if(!empty($role_list))
                  <div class="form-group">
                    <label>Role</label>
                    <select name="role" class="form-control" required="">
                      <option>Select Role</option>
                      @foreach($role_list as $role)
                        <option value="{{ $role->name }}">{{ $role->display_name }}</option>
                      @endforeach
                    </select>
                  </div>
                @endif

                <div class="form-group">
                  <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                  <button type="submit" class="btn btn-primary">Save</button>
                </div>

              </form>

            </div>
          </div>
          <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
      </div>
      <!-- /.modal -->

</x-app-layout>
