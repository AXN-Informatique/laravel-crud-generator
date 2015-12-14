@extends('layouts.app', [
    'title' => crud()->trans($section, !empty($record) ? 'edit_title' : 'list_title')
])

@section('content')
    <div class="row">
        <div class="col-xs-12 {!! $options['creatable'] || !empty($record) ? 'col-md-8' : 'col-md-12' !!}">
            @include(crud()->viewName($section, 'panel-list'))
        </div>

        @if (!empty($record))
            <div class="col-xs-12 col-md-4">
                @include(crud()->viewName($section, 'panel-edit'))
            </div>
        @elseif ($options['creatable'])
            <div class="col-xs-12 col-md-4">
                @include(crud()->viewName($section, 'panel-create'))
            </div>
        @endif
    </div>
@endsection
