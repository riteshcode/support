<x-app-layout>
	<!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0 text-dark">{{ __('Permissions') }}</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">{{ __('Permissons') }}</li>
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
              <button type="button" class="btn btn-info" data-toggle="modal" data-target="#modal-default">
                  Add Permission
              </button>

            </div>
            <!-- /.card-header -->
            <div class="card-body">
              @include('layouts.common.alertMsg')
              <table id="example1" class="table table-bordered table-striped">
                <thead>
                <tr>
                  <th class="text-center">{{__('Sl No.')}}</th>
                  <th class="text-center">{{__('Permission name')}}</th>
                  <th class="text-center">{{__('Action') }}</th>
                </tr>
                </thead>
                <tbody>
                @if(!empty($permissionList))
                	@foreach($permissionList as $key=>$data)

	                <tr>
	                  <td class="text-center"> {{ $key+1 }} </td>
	                  <td class="text-center"> {{ $data->name}} </td>
	                  <td class="text-center">
	                  	
                      <form action=" {{ route('permission.destroy', $data->id) }}" method="POST">
                        @csrf
                        {{method_field('DELETE')}}
                        
                        <a href="javascript:void(0);" class="btn btn-warning btn-sm" onclick="editFunction('{{ json_encode($data)}}')" ><i class="fa fa-eye" aria-hidden="true"></i></a>
                        
                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm ('Are you sure you want to delete')"><i class="fa fa-trash" aria-hidden="true"></i></button> 
                        
                      </form>
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
              <h4 class="modal-title">Add Permission</h4>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <form action="{{ route('permission.store') }}" method="POST">
                @csrf
                <div class="form-group">
                  <label>Permission Name</label>
                  <textarea name="permission_name" rows="5" class="form-control" placeholder="Enter permission(user,role,etc..) for multiple" required=""></textarea>
                </div>
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


    <div class="modal fade" id="edit-modal-default">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h4 class="modal-title">Edit Permission</h4>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <form id="update-role-url" action="{{ url('permission') }}" method="POST">
                @csrf
                {{method_field('PUT')}}
                <div class="form-group">
                  <label>Permission Name</label>
                  <input type="text" name="permission_name" id="edit_role_name" value="" class="form-control" placeholder="Enter permission" required="">
                </div>
                <div class="form-group">
                  <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                  <button type="submit" class="btn btn-primary">Update</button>
                </div>

              </form>

            </div>
          </div>
          <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
      </div>
      <!-- /.modal -->

<script type="text/javascript">

  function editFunction(htmlid){
      var detail = JSON.parse(htmlid);
      $('#edit_role_name').val(detail.name);
      $('#edit_update_id').val(detail.id);
      var url = `{{ url('admin/permission/`+detail.id+`') }}`;
      $('#update-role-url').attr('action',url);
      $('#edit-modal-default').modal('show');
  }    

</script>

</x-app-layout>