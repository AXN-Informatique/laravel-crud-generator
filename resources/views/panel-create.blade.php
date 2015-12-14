
<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">{{ crud()->trans($section, 'create_form_title') }}</h3>
    </div>
    <div class="panel-body">
        {!! Form::open(['route' => "$section.store"]) !!}
            @include(crud()->viewName($section, 'form'))

            <div class="form-group">
                {!! Form::button(
                    '<i class="fa fa-plus"></i> '.crud()->trans($section, 'create_form_submit'),
                    ['type' => 'submit', 'class' => 'btn btn-success']
                ) !!}
            </div>
        {!! Form::close() !!}
    </div>
</div>
