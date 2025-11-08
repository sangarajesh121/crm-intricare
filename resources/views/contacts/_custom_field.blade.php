{{-- Dynamic Custom Fields --}}
@if(isset($customFields) && $customFields->count())
    <hr>
    <div class="col-12">
        <h6 class="fw-bold mt-3">Custom Fields</h6>
    </div>

    @foreach($customFields as $field)
        <div class="col-md-6">
            <label class="form-label">{{ $field->customFieldDetail->field_name }}</label>

            @switch($field->customFieldDetail->field_type)
                @case('text')
                    <input type="text" 
                        name="custom_fields[{{ $field->customFieldDetail->id }}]" 
                        class="form-control" 
                        value="{{ old('custom_fields.' . $field->field_name, $field->field_value ?? '') }}"
                        placeholder="Enter {{ strtolower($field->label) }}">
                    @break

                @case('number')
                    <input type="number" 
                        name="custom_fields[{{ $field->customFieldDetail->id }}]" 
                        class="form-control" 
                        value="{{ old('custom_fields.' . $field->customFieldDetail->field_key, $field->field_value ?? '') }}">
                    @break

                @case('date')
                    <input type="date" 
                        name="custom_fields[{{ $field->customFieldDetail->id }}]" 
                        class="form-control" 
                        value="{{ old('custom_fields.' . $field->customFieldDetail->field_key, $field->field_value ?? '') }}">
                    @break

                @case('file')
                    <input type="file" 
                        name="custom_fields[{{ $field->customFieldDetail->id }}]" 
                        class="form-control">

                    @if(!empty($field->field_value))
                        {!! \App\Helpers\CommonHelper::viewuploadedfile($field->field_value) !!}
                    @endif
                    @break
            @endswitch
        </div>
    @endforeach
@endif