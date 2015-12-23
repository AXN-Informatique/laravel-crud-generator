@extends('layouts.app', [
    'title' => $title,
])

@section('content')
    <div class="row">
        <div class="col-xs-12 {!! $options['creatable'] || !empty($record) ? 'col-md-8' : 'col-md-12' !!}">
            @include($views['panel-list'])
        </div>

        @if (!empty($record))
            <div class="col-xs-12 col-md-4">
                @include($views['panel-edit'])
            </div>
        @elseif ($options['creatable'])
            <div class="col-xs-12 col-md-4">
                @include($views['panel-create'])
            </div>
        @endif
    </div>
@endsection
