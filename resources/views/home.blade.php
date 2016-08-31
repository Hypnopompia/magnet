@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">Boards</div>

                <div class="panel-body">
                    @if(!$pinterestLoggedIn)
                    <a href="{{ $pinterestLoginUrl }}">Connect to Pinterest</a>
                    @endif

                    @if($pinterestLoggedIn)
                    <div id="boards">
                    @foreach ($boards as $board)
                        <div class="board">
                            <a href="{{ url('/board', [$board->id]) }}"><img src="{{ $board->imageurl }}" /></a>
                            <a href="{{ url('/board', [$board->id]) }}">{{ $board->name }} ({{ $board->pins_count }})</a>
                        </div>
                    @endforeach
                    </div>

                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
