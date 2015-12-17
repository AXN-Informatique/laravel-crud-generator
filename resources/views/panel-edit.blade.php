
<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">{{ trans( crud_trans_id($crud, 'edit_form_title') ) }}</h3>
    </div>
    <div class="panel-body">
        {!! Form::open(['route' => ["{$crud['section']}.update", $record->id], 'method' => 'PUT']) !!}
            @include( crud_view_name($crud, 'form') )

            <div class="form-group">
                {!! Form::button(
                    '<i class="fa fa-check"></i> '.trans( crud_trans_id($crud, 'edit_form_submit') ),
                    ['type' => 'submit', 'class' => 'btn btn-info']
                ) !!}
                <a href="{!! route($crud['section'].'.index') !!}" class="btn btn-default">
                    <i class="fa fa-undo"></i> {{ trans( crud_trans_id($crud, 'back_to_list') ) }}
                </a>
            </div>
        {!! Form::close() !!}
    </div>
</div>
