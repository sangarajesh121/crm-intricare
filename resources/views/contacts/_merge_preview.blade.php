<form id="mergeConfirmForm">
    @csrf
    <div class="row text-center">
        @foreach($contacts as $contact)
        <div class="col-md-6 border-end">
            <img src="{{$contact->profile_image_url}}"  
                 class="rounded-circle mb-2" width="70">
            <h6>{{ $contact->name }}</h6>
            <p class="text-muted">{{ $contact->email }}</p>
            <p class="text-muted">{{ $contact->phone }}</p>
            <div class="form-check d-flex justify-content-center">
                <input class="form-check-input" type="radio" 
                       name="master_id" value="{{ $contact->id }}" required>
                <label class="form-check-label ms-2">Set as Master</label>
            </div>
        </div>
        @endforeach
    </div>

    <input type="hidden" name="contact_ids"  value="{{ $contacts->pluck('id')->implode(',') }}">
    <div class="text-end mt-3">
        <button type="submit" class="btn btn-success">Confirm Merge</button>
    </div>
</form>
