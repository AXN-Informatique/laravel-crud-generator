@extends('layouts.app', [
    'title' => trans( crud_trans_id($crud, !empty($record) ? 'edit_title' : 'list_title') )
])

@section('content')
    <div class="row">
        <div class="col-xs-12 {!! $crud['creatable'] || !empty($record) ? 'col-md-8' : 'col-md-12' !!}">
            @include( crud_view_name($crud, 'panel-list') )
        </div>

        @if (!empty($record))
            <div class="col-xs-12 col-md-4">
                @include( crud_view_name($crud, 'panel-edit') )
            </div>
        @elseif ($crud['creatable'])
            <div class="col-xs-12 col-md-4">
                @include( crud_view_name($crud, 'panel-create') )
            </div>
        @endif
    </div>
@endsection
