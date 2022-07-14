@if(Session::has('success'))
    <div class="alert alert-success alert-dismissible">
      <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
      <i class="icon fas fa-check"></i> <b>Success</b>, {{Session::get('success')}}
    </div>
@endif
@if(Session::has('info'))
    <div class="alert alert-info alert-dismissible">
      <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
      <h5><i class="icon fas fa-check"></i> Success!</h5>
      {{Session::get('info')}}
    </div>
@endif
@if(Session::has('warning'))
    <div class="alert alert-warning alert-dismissible">
      <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
      <h5><i class="icon fas fa-check"></i> Warning!</h5>
      {{Session::get('warning')}}
    </div>
@endif
@if(Session::has('fail'))
    
    <div class="alert alert-danger alert-dismissible">
      <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
      <i class="icon fas fa-check"></i> Fail!, {{Session::get('fail')}}
    </div>
@endif