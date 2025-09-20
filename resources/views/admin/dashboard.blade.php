@extends('layouts.admin')
@section('title')
{{__('messages.Home')}}
@endsection

@section('contentheader')
{{__('messages.Home')}}
@endsection

@section('contentheaderlink')
<a href="{{ route('admin.dashboard') }}"> {{__('messages.Home')}} </a>
@endsection

@section('contentheaderactive')
{{__('messages.Show')}}
@endsection



@section('content')
<div class="row" style="background-image: url({{ asset('assets/admin/imgs/dash.jpg') }}) ;background-size:cover;background-repeate:ni-repeate; min-height:600px;">

</div>


@endsection



