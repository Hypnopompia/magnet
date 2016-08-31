@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <a href="{{ url('home') }}">Boards</a>
                    |
                    {{ $board->name }}
                </div>

                <div class="panel-body">
                    <div id="cards">
                    @foreach ($board->pins as $pin)
                        <div class="card" style="opacity: 0;">
                            <a href="{{ $pin->url }}" target="_blank"><img src="{{ $pin->imageurl }}" /></a>
                            <p>{{ $pin->note }}</p>
                        </div>
                    @endforeach
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
