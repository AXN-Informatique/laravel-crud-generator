
<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">{{ trans("{$langBaseKey}.edit_form_title") }}</h3>
    </div>
    <div class="panel-body">
        {!! Form::open(['route' => ["{$routeBaseAlias}.update", $record->id], 'method' => 'PUT']) !!}
            @include($views['form'])

            <div class="form-group">
                {!! Form::button(
                    '<i class="fa fa-check"></i> '.trans("{$langBaseKey}.edit_form_submit"),
                    ['type' => 'submit', 'class' => 'btn btn-info']
                ) !!}
                <a href="{!! route($routeBaseAlias.'.index') !!}" class="btn btn-default">
                    <i class="fa fa-undo"></i> {{ trans("{$langBaseKey}.back_to_list") }}
                </a>
            </div>
        {!! Form::close() !!}
    </div>
</div>
