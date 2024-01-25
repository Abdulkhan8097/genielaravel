@php
$copyright_date = date('Y');

@endphp
<!doctype html>
<html lang="en">

<head>
    <title>@yield('title', 'AMC DRM Panel')</title>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="baseurl" content="{{env('APP_URL')}}">
    <meta name="csrf-token" content="{{csrf_token()}}">
    <meta name="robots" content="noindex, nofollow">
    <link rel="shortcut icon" href="https://samco1-zonsoftwaresolut.netdna-ssl.com/images/nuovo/ico/favicon.png">

    @section('custom_meta_tags')
    @show

    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Heebo:wght@100;300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="{{asset('css/style.css')}}">
    <link href="{{asset('css/datepicker.css')}}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" type="text/css" href="{{asset('css/custom.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('css/sweetalert.css')}}">
    <style type="text/css">
        
        .index-mainbox-right{
            padding-right: 15px;
            padding-left: 15px;
        }
        .footer .index-mainbox-right{
            padding-right: 15px;
            padding-left: 15px;
        }
    </style>
    @section('custom_head_tags')
    @show

</head>

<body class="dashboard m-page--fluid">
    <!-- begin:: Main Container -->
    <div class="index-mainbox">
        <div class="">
            <!-- begin:: Aside Menu -->
            <div class="add-sender-domain-modal-main"></div>
            
            <!-- end:: Aside Menu -->
            <!-- begin:: Header -->
            <div class="index-mainbox-right">

                @include('flash-message')

                <div class="row">
                    <div class="col-md-12"></div>
                </div>

                @section('content')
                @show

            </div>
            <!-- end:: Header-->

        </div>
    </div>
    <!-- end:: Main Container -->

    <!-- begin:: Footer -->
    <div class="footer">
        <div class="">
            <div class="index-mainbox-right">
                <div class="footer-text text-center"> Copyright &copy; {{$copyright_date}} RankMF All rights reserved. </div>
            </div>
        </div>
    </div>
    <!-- end:: Footer-->
    <div id="m_scroll_top" class="m-scroll-top">
        <i class="icons arrow-up"></i>
    </div>
    <div class="loader">
        <div class="loaders-design">
            Processing...
            <div class="spinner-border text-primary" role="status">
                <span class="sr-only">Loading...</span>
            </div>
        </div>
    </div>

    @section('custom_after_footer_html')
    @show

    <form action="javascript:void(0);" method="post" target="_blank" id="frm_export_data">@csrf</form>
    <script src="{{asset('js/jquery.min.js')}}"></script>
    <script src="{{asset('js/bootstrap-datepicker.js')}}"></script>
    <script src="{{asset('js/custom.js?v=1.1')}}"></script>
    <script src="{{asset('js/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('js/bootstrap.min.js')}}"></script>
    <script src="{{asset('js/sweetalert.min.js')}}"></script>
    <!--script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script-->
    <script src="{{asset('js/sweetalert.min.js')}}"></script>
    <script src="{{asset('js/jquery.slimscroll.min.js')}}"></script>

    @section('custom_scripts')
    @show

</body>

</html>
