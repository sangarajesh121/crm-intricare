<form id="contactForm"
      method="{{ $contact->exists ? 'POST' : 'POST' }}"
      action="{{ $contact->exists ? route('contacts.update', $contact) : route('contacts.store') }}"
      enctype="multipart/form-data">

    @csrf
    @if($contact->exists)
        @method('PUT')
    @endif

    <div class="modal-header">
        <h5 class="modal-title">{{ $contact->exists ? 'Edit Contact' : 'Add Contact' }}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
    </div>

    {{-- WIDER MODAL --}}
    <div class="modal-body container-fluid">
        <div class="row g-3">

            {{-- Hidden field ID --}}

            @if($contact->exists)
                <input type="hidden" name="id" value="{{ $contact->id }}">
            @endif

            {{-- Name --}}
            <div class="col-md-6">
                <label class="form-label">Name <span class="text-danger">*</span></label>
                <input name="name" type="text" class="form-control" 
                       value="{{ old('name', $contact->name) }}" placeholder="Enter full name">
            </div>

            {{-- Email --}}
            <div class="col-md-6">
                <label class="form-label">Email</label>
                <input name="email" type="text" class="form-control" 
                       value="{{ old('email', $contact->email) }}" placeholder="example@email.com">
            </div>

            {{-- Phone --}}
            <div class="col-md-6">
                <label class="form-label">Phone</label>
                <input name="phone" type="text" class="form-control" 
                       value="{{ old('phone', $contact->phone) }}" placeholder="Enter phone number">
            </div>

            {{-- Gender --}}
            <div class="col-md-6">
                <label class="form-label d-block">Gender</label>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="gender" id="male" value="male"
                        {{ old('gender', $contact->gender) == 'male' ? 'checked' : '' }}>
                    <label class="form-check-label" for="male">Male</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="gender" id="female" value="female"
                        {{ old('gender', $contact->gender) == 'female' ? 'checked' : '' }}>
                    <label class="form-check-label" for="female">Female</label>
                </div>
            </div>

            {{-- Profile Image --}}
            <div class="col-md-6">
                <label class="form-label">Profile Image</label>
                <input type="file" name="profile_image_path" class="form-control">
                @if ($contact->profile_image_path)
                    {!! \App\Helpers\CommonHelper::viewuploadedfile($contact->profile_image_path) !!}
                @endif
            </div>

            {{-- Additional File --}}
            <div class="col-md-6">
                <label class="form-label">Additional File</label>
                <input type="file" name="other_doc" class="form-control">
                @if ($contact->other_doc)
                    {!! \App\Helpers\CommonHelper::viewuploadedfile($contact->other_doc) !!}
                @endif
            </div>

            
            {{-- Dynamic Custom Fields --}}
            @include('contacts._custom_field', ['customFields' => $contact->customFieldValues])

        </div>
    </div>

    <div class="modal-footer">
        <button type="button" id="cancelBtn" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary">Save</button>
    </div>
</form>


<script>

    $(document).ready(function(){
        // Prevent normal form submission
        


        $('#contactForm').on('submit', function (e) {
            e.preventDefault();

            let formData = new FormData(this);
            let url = "{{ $contact->exists ? route('contacts.update', $contact) : route('contacts.store') }}";

            $.ajax({
                url: url,  // adjust route name as needed
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function (response) {
                    showToast(response.success === true ? 'success' : 'error' , response.message || 'Contact saved successfully!');
                    $('#contactModal').modal('hide');
                    loadContacts(); // refresh list dynamically
                },
                error: function (xhr) {
                    console.error(xhr);
                    showToast('error', xhr.responseJSON?.message || 'Something went wrong.');
                }
            });
        });
    });
</script>