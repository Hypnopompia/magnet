@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-4 col-md-offset-4">
            <div class="panel panel-default">
                <div class="panel-heading">Boards</div>

                <div class="panel-body">
                    @if(!$pinterestLoggedIn)
                    <p>
                        Sometimes your pins on Pinterest will get removed or reported. That sucks. This site will help you backup your pins in case that happens. Once you connect Magnet to Pinterest using the button below, you don't have to do anything else. We'll look for new boards in your account and import any new pins daily. If you delete something on Pinterest, it won't be deleted here.
                    </p>
                    <p>
                        If you have a lot of boards, it might take several hours to import them. Pinterest places a limit on how fast we can access their servers, so we have to take it slow. You may see your boards show up immediately but with zero pins. Just check back later.
                    </p>
                    <a href="{{ $pinterestLoginUrl }}" class="btn btn-primary">Connect to Pinterest</a>
                    @endif

                    @if($pinterestLoggedIn)
                    <div id="boards">
                    @foreach ($boards as $board)
                        <div class="board media">
                            <a class="media-left" href="{{ url('/board', [$board->id]) }}"><img class="media-object" src="{{ $board->pinterestimage }}" /></a>
                            <div class="media-body">
                                <h4 class="media-heading">
                                    <a href="{{ url('/board', [$board->id]) }}">{{ $board->name }}</a>
                                </h4>

                                @if($board->refreshed_at != null)
                                <p>{{ $board->pins_count }} pins</p>
                                @else
                                <p><i>Import in progress</i></p>
                                @endif

                                {{ $board->description }}
                            </div>
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
