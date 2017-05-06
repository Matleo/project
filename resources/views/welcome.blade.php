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

<div class="bg-warning container-fluid" id="cookieWarning">
    <div class="bs-callout bs-danger container">
        <h1 class="icon icon-new"> Cookie hinweis</h1>
        <p>Diese Seite verwendet Cookies. Cookies sind keine Artefakte in Ihrem Browser, welche uns erlauben zu
            erkennen, wenn Sie wieder auf diese Seite kommen.</p>
        <p>Cookies werden auf dieser Seite ausschließlich zur Nutzerwiedererkennung verwendet, nicht für
            Marketingzwecke.</p>
        <div class="pull-right">
            <div class="btn btn-primary" data-dismiss="cookieWarning">Akzeptieren</div>
        </div>
    </div>
</div>


<section id="welcomemsg">
    <div class="container welcome">
        <p class="row">

        <h1>@lang('fields.welcome')</h1>
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

