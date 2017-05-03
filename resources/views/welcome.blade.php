@extends('layouts.app')

@section('css')

@endsection

@section('content')

<section id="login_panel" class="container-fluid">
    <div class="row">
        <div class="col-xs-12 col-md-6 col-md-offset-3">
            <div class="panel">
                @include('panels.heading')
                <div class="panel-body">
                    @include('auth.forms.login')
                </div>
            </div>
        </div>
    </div>

</section>

<section id="welcomemsg">
    <div class="container welcome">
        <p class="row">

                <h1>@lang('fields.welcome') - GIT-Test</h1>
                @if(app()->getLocale() == "de")
                <p class="col-xs-12">{{ $welcome->de }}</p>
                @else
                <p class="col-xs-12">{{ $welcome->en }}</p>
                @endif

        </div>
    </div>
</section>

@include('modals.forgot')
@include('modals.register')

@endsection

