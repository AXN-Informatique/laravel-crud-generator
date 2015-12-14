
<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">{{ crud()->trans($section, 'edit_form_title') }}</h3>
    </div>
    <div class="panel-body">
        {!! Form::open(['route' => ["$section.update", $record->id], 'method' => 'PUT']) !!}
            @include(crud()->viewName($section, 'form'))

            <div class="form-group">
                {!! Form::button(
                    '<i class="fa fa-check"></i> '.crud()->trans($section, 'edit_form_submit'),
                    ['type' => 'submit', 'class' => 'btn btn-info']
                ) !!}
                <a href="{!! route($section.'.index') !!}" class="btn btn-default"><i class="fa fa-undo"></i> {{ crud()->trans($section, 'back_to_list') }}</a>
            </div>
        {!! Form::close() !!}
    </div>
</div>
