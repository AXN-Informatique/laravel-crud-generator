
<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">{{ trans( crud_trans_id($crud, 'create_form_title') ) }}</h3>
    </div>
    <div class="panel-body">
        {!! Form::open(['route' => "{$crud['section']}.store"]) !!}
            @include( crud_view_name($crud, 'form') )

            <div class="form-group">
                {!! Form::button(
                    '<i class="fa fa-plus"></i> '.trans( crud_trans_id($crud, 'create_form_submit') ),
                    ['type' => 'submit', 'class' => 'btn btn-success']
                ) !!}
            </div>
        {!! Form::close() !!}
    </div>
</div>
