
@extends('../layout')
@section('title', 'Update Profile')
@section('breadcrumb_heading', 'Profile >> Update')

@section('content')
<div class="row">
  <div class="col-lg-12">
    <div class="mt-2">
      <form action="{{url('update-profile')}}" method="post" onsubmit="return ValidateForm();">
        @csrf
        <div class="row border-bottom">
          <div class="col-lg-12 col-md-12 col-sm-12">Change your password.</div><!--/.col-lg-12 col-md-12 col-sm-12-->
        </div><!--/.row-->
        <div class="row">
          <div class="col-lg-12 col-md-12 col-sm-12">&nbsp;</div><!--/.col-lg-12 col-md-12 col-sm-12-->
        </div><!--/.row-->
        <div class="row">
          <div class="col-lg-12 col-md-12 col-sm-12">
            <div class="row form-inline">
              <div class="col-md-4 col-sm-4">
                <div class="form-group">
                  <label>Password</label>
                  <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password"  autocomplete="off">
                  <span class="invalid-feedback" style="color:red;" role="alert">{{$errors->first('password')}}</span>
                </div><!--/.form-group-->
              </div><!--/.col-md-4 col-sm-4-->
            </div><!--/.row form-inline-->
            <div class="row form-inline"><!--/.col-->
              <div class="col-md-4 col-sm-4">
                <div class="form-group">
                  <label>Confirm Password</label>
                  <input id="password-confirm" type="password" class="form-control" name="password_confirmation"  autocomplete="off">
                  <span class="invalid-feedback" style="color:red;" role="alert"></span>
                </div><!--/.form-group-->
              </div><!--/.col-md-4 col-sm-4-->
            </div><!--/.row form-inline-->
          </div><!--/.col-lg-12 col-md-12 col-sm-12-->
        </div><!--/.row-->
        <div class="row">
          <div class="col-lg-12 col-md-12 col-sm-12">
            <button type="submit" class="btn btn-primary">Submit</button>
          </div><!--/.col-lg-12 col-md-12 col-sm-12-->
        </div><!--/.row-->
      </form><!--/.form-->
    </div><!--/.mt-2-->
  </div><!--/.col-lg-12-->
</div><!--/.row mt-4-->

@endsection

@section('custom_scripts')

<script>

function ValidateForm()
{
    var input_password = $('#password').val();
    var confirm_password = $('#password-confirm').val();
    var err_flag = false;
    if(input_password == '')
    {
        err_flag = true;
        $("#password").next("span").text("The password field should not be empty.");
    }
    else if(confirm_password == '')
    {
        err_flag = true;
        $("#password-confirm").next("span").text("The confirm password field should not be empty.");
    }
    else if(input_password != confirm_password)
    {
        err_flag = true;
        $("#password-confirm").next("span").text("The password confirmation does not match.");
    }
    else if(input_password.length < 8)
    {
        err_flag = true;
        $("#password").next("span").text("The password must be at least 8 characters.");
    }
    else
    {
        $("#password").next("span").html('');
        $("#password-confirm").next("span").html('');
    }

    if(!err_flag){
      return confirm('Are you sure?');
   }
   else{
      return false;
   }

}
  </script>
@endsection
