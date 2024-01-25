@php
$flash_message_text = '';
$flash_message_type = 'alert-info';
$flash_message_div_class = 'hidden-elements';

if(Session::get('success')){
    $flash_message_text = Session::get('success');
    $flash_message_type = 'alert-success';
    $flash_message_div_class = '';
}
elseif(Session::get('error')){
    $flash_message_text = Session::get('error');
    $flash_message_type = 'alert-danger';
    $flash_message_div_class = '';
}
elseif(Session::get('warning')){
    $flash_message_text = Session::get('warning');
    $flash_message_type = 'alert-warning';
    $flash_message_div_class = '';
}
elseif(Session::get('info')){
    $flash_message_text = Session::get('info');
    $flash_message_type = 'alert-info';
    $flash_message_div_class = '';
}
@endphp

                @if($flash_message_text !== '')
                <div class="row mt-4 {{$flash_message_div_class}}">
                    <div class="col-lg-12 text-center">
                        <div class="alert {{$flash_message_type}}">
                            <span data-flash="{{$flash_message_type}}">{{$flash_message_text}}</span>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    </div>
                </div>
                @endif
