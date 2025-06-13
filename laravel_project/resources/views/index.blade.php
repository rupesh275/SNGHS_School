@extends('render')
@section('metaTitle')
    <meta content="{{ $title }}" name="description" />
    <title>{{ $title }}</title>
@endsection
@section('cssLinks')
@endsection
@section('myCSS')
@endsection
@section('pageTitle')
    {{-- <div class="row">
        <div class="col-12">
            <div class="page-title-box d-flex align-items-center justify-content-between">
                <h4 class="page-title mb-0 font-size-18 text-dark">{{ $pageTitle }}</h4>
                {{-- {{ print_r($Metadata) }} --}}
            {{-- </div> --}}
            {{-- <ol class="breadcrumb p-0 mt-2">
                <li class="breadcrumb-item"><a href="#">Admin</a></li>
                <li class="breadcrumb-item active"> {{ $pageTitle }}</li>
            </ol> --}}
        {{-- </div> --}}
    {{-- </div> --}}
@endsection
@section('cardHeading')
@endsection
@section('cardBody')
      <!-- main-content -->
      <div class="main-content app-content">

        <!-- container -->
        <div class="main-container container-fluid">

            <!-- breadcrumb -->
            <div class="breadcrumb-header justify-content-between">
                <div>
                    <h4 class="content-title mb-2">Hi {{ ucfirst(Session::get('userName'))}}, welcome back! </h4>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a   href="javascript:void(0);">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Project</li>
                        </ol>
                    </nav>
                </div>
                <div class="d-flex my-auto">
                    {{-- <div class=" d-flex right-page">
                        <div class="d-flex justify-content-center me-5">
                            <div class="">
                                <span class="d-block">
                                    <span class="label ">EXPENSES</span>
                                </span>
                                <span class="value">
                                    $53,000
                                </span>
                            </div>
                            <div class="ms-3 mt-2">
                                <span class="sparkline_bar"></span>
                            </div>
                        </div>
                        <div class="d-flex justify-content-center">
                            <div class="">
                                <span class="d-block">
                                    <span class="label">PROFIT</span>
                                </span>
                                <span class="value">
                                    $34,000
                                </span>
                            </div>
                            <div class="ms-3 mt-2">
                                <span class="sparkline_bar31"></span>
                            </div>
                        </div>
                    </div> --}}
                </div>
            </div>
            <!-- /breadcrumb -->

            <!-- main-content-body -->
            <div class="main-content-body">
               

            </div>
            <!-- /row -->
        </div>
        <!-- /container -->
    </div>
    <!-- /main-content -->
@endsection
@section('jsLinks')
@endsection
@section('myJS')
@endsection
