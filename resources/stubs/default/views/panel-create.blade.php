
<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">{{ trans("{$langBaseKey}.create_form_title") ) }}</h3>
    </div>
    <div class="panel-body">
        {!! Form::open(['route' => "{$routeBaseAlias}.store"]) !!}
            @include($views['form'])

            <div class="form-group">
                {!! Form::button(
                    '<i class="fa fa-plus"></i> '.trans("{$langBaseKey}.create_form_submit"),
                    ['type' => 'submit', 'class' => 'btn btn-success']
                ) !!}
            </div>
        {!! Form::close() !!}
    </div>
</div>
