<?php use Illuminate\Contracts\Pagination\Paginator; ?>

@if ($list->isEmpty())
    <div class="well">
        <p>{{ trans( crud_trans_id($crud, 'empty') ) }}</p>
    </div>
@else
    <ul class="list-group {!! $crud['sortable'] && empty($record) ? 'sortable' : '' !!}">

        @foreach ($list as $item)
            <li class="list-group-item clearfix {!! !empty($record) && $record->id == $item->id ? 'editing' : '' !!}" data-sort="{{ $item->id }}">

                <div class="pull-left">
                    @if ($crud['sortable'] && empty($record))
                        <i class="fa fa-arrows-v"></i>&nbsp;
                    @endif

                    @if ($crud['contentEditable'] && empty($record))
                        <span contenteditable="true" data-key="{{ $item->id }}">{{ $item->libelle }}</span>
                    @else
                        <span>{{ $item->libelle }}</span>
                    @endif
                </div>

                @if (empty($record))
                    <div class="pull-right">
                        @if ($crud['activatable'])
                            @if ($item->actif)
                                {!! Form::open(['url' => route("{$crud['section']}.disable", ['id' => $item->id]), 'class' => 'form-inline', 'style' => 'display:inline']) !!}
                                    {!! Form::button('<i class="fa fa-check"></i><span class="hidden-xs hidden-sm"> Actif</span>', [
                                        'type'        => 'submit',
                                        'class'       => 'btn btn-success btn-xs',
                                        'title'       => trans( crud_trans_id($crud, 'disable_tooltip'), ['name' => $item->libelle] ),
                                        'data-toggle' => 'tooltip'
                                    ]) !!}
                                {!! Form::close() !!}
                            @else
                                {!! Form::open(['url' => route("{$crud['section']}.enable", ['id' => $item->id]), 'class' => 'form-inline', 'style' => 'display:inline']) !!}
                                    {!! Form::button('<i class="fa fa-close"></i><span class="hidden-xs hidden-sm"> Inactif</span>', [
                                        'type'        => 'submit',
                                        'class'       => 'btn btn-warning btn-xs',
                                        'title'       => trans( crud_trans_id($crud, 'enable_tooltip'), ['name' => $item->libelle] ),
                                        'data-toggle' => 'tooltip'
                                    ]) !!}
                                {!! Form::close() !!}
                            @endif
                        @endif

                        @if ($crud['editable'])
                            <a href="{!! route($crud['section'].'.edit', ['id' => $item->id] + ($list instanceof Paginator && $list->hasPages() ? ['page' => Input::get('page')] : [])) !!}"
                               class="btn btn-info btn-xs"
                               title="{{ trans( crud_trans_id($crud, 'edit_tooltip'), ['name' => $item->libelle] ) }}"
                               data-toggle="tooltip">

                                <i class="fa fa-pencil"></i><span class="hidden-xs hidden-sm"> Modifier</span>
                            </a>
                        @endif

                        @if ($crud['destroyable'])
                            {!! Form::open(['route' => ["{$crud['section']}.destroy", $item->id], 'method' => 'DELETE', 'class' => 'form-inline', 'style' => 'display:inline']) !!}
                                {!! Form::button('<i class="fa fa-trash"></i><span class="hidden-xs hidden-sm"> Supprimer</span>', [
                                    'type'         => 'submit',
                                    'class'        => 'btn btn-danger btn-xs',
                                    'title'        => trans( crud_trans_id($crud, 'destroy_tooltip'), ['name' => $item->libelle] ),
                                    'data-confirm' => trans( crud_trans_id($crud, 'destroy_confirm'), ['name' => $item->libelle] ),
                                    'data-toggle'  => 'tooltip'
                                ]) !!}
                            {!! Form::close() !!}
                        @endif
                    </div>
                @endif

            </li>
        @endforeach

    </ul>

    @if ($list instanceof Paginator && $list->hasPages())
        <div class="pull-right">
            {!! $list->render() !!}
        </div>
    @endif
@endif
