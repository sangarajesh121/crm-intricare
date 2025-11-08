<div class="row text-center">
    @foreach($contacts as $contact)
    <div class="col-md-6 border-end">
        <img src="{{$contact->profile_image_url}}"  
                class="rounded-circle mb-2" height="70">
        <h6>{{ $contact->name }} <span>({{is_null($contact->merged_into) ? 'Master' : 'Secondary' }} Contact)</span></h6>
        <ul class="list-group">
            <li class="list-group-item">Email : {{ $contact->email }}</li>
            <li class="list-group-item">Phone : {{ $contact->phone }}</li>
            <li class="list-group-item">Gender : {{ $contact->gender }}</li>
        </ul>
        <h6 class="m-3"><b>Custom Fields</b></h6>
        <ul class="list-group">
            @forelse($contact->customFieldValues as $field)
            <li class="list-group-item">{{ $field->customFieldDetail->field_name }} : {{ $field->field_value }} 
                @if($field->field_origin != 'self' && is_null($contact->merged_into))
                <small class="text-warning ml-4">{{$field->field_origin}}</small></li>
                @endif
            @empty
            <li class="list-group-item text-muted">No custom fields</li>
            @endforelse
        </ul>
    </div>
    @endforeach
</div>
