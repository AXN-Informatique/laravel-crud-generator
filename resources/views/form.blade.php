
<div class="form-group">
    {!! Form::label('libelle', "Intitulé") !!}
    {!! Form::text('libelle', old('libelle', !empty($record) ? $record->libelle : ''), ['class' => 'form-control']) !!}
</div>
