<div class="row text-center">
    @foreach($contacts as $contact)
    <div class="col-md-6 border-end">
        <img src="{{$contact->profile_image_url}}"  
                class="rounded-circle mb-2" height="70">
        <h6>{{ $contact->name }} <span class="text-info">({{is_null($contact->merged_into) ? 'Master' : 'Secondary' }} Contact)</span></h6>
        <ul class="list-group">
            <li class="list-group-item"><b>Email</b> : {{ $contact->email }}</li>
            <li class="list-group-item"><b>Phone</b> : {{ $contact->phone }}</li>
            <li class="list-group-item"><b>Gender</b> : {{ $contact->gender }}</li>
        </ul>
        <h6 class="m-3 text-info"><i>Custom Fields</i></h6>
        <ul class="list-group">
            @forelse($contact->customFieldValues as $field)
            <li class="list-group-item"><b>{{ $field->customFieldDetail->field_name }} - </b>  
                @if($field->customFieldDetail->field_type == 'file' && !empty($field->field_value))
                {!! \App\Helpers\CommonHelper::viewuploadedfile($field->field_value) !!}
                @else
                {{ $field->field_value }} 
                @endif
                @if($field->field_origin != 'self' && is_null($contact->merged_into))
                <small class="text-danger ml-4">{{$field->field_origin}}</small></li>
                @endif
            @empty
            <li class="list-group-item text-muted">No custom fields</li>
            @endforelse
        </ul>
    </div>
    @endforeach
</div>
