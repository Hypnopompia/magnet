@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">Dashboard</div>

                <div class="panel-body">
                    @if(!$pinterestLoggedIn)
                    <a href="{{ $pinterestLoginUrl }}">Connect to Pinterest</a>
                    @endif

                    @if($pinterestLoggedIn)
                        @foreach ($boards as $board)
                            {{ $board->name }}

                            <div id="wrapper">
                                <div id="columns">
                                @foreach ($board->pins as $pin)
                                    <div class="pin">
                                        <img src="{{ $pin->imageurl }}" />
                                        <p>{{ $pin->note }}</p>
                                    </div>
                                @endforeach
                                </div>
                            </div>

                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
