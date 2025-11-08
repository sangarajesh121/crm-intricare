@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h4 class="mb-3">Contacts</h4>

    
    <!-- Filter Bar -->
    <div class="card mb-3 shadow-sm">
        <div class="card-body py-2">
            <div class="row align-items-center g-2">

                <!-- Search -->
                <div class="col-md-6 col-sm-12">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" id="searchInput" class="form-control border-start-0" placeholder="Search by Name / Email ">
                    </div>
                </div>


                 <!-- Gender Filter -->
                <div class="col-md-2 col-sm-6">
                    <select id="genderFilter" class="form-select">
                        <option value="">Gender</option>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                        <option value="other">Other</option>
                    </select>
                </div>

                <!-- Add Contact -->
                <div class="col-md-3 col-sm-12 ms-auto text-md-end">
                    <button class="btn btn-primary w-100 float-right" id="addContactBtn">
                        <i class="bi bi-plus-lg"></i> Add New Contact
                    </button>
                </div>
            </div>
        </div>
    </div>


    
    <div id="contactList">
        <!-- User list will load here via AJAX -->
    </div>
</div>


{{-- Custom field Modal --}}

@include('custom-fields._form')


<!-- Modal -->
<div class="modal fade" id="contactModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" id="contactModalContent">
            <!-- Dynamic form will load here -->
        </div>
    </div>
</div>

@endsection



@push('scripts')
<script>

function loadContacts(page = 1) {
    search = $('#searchInput').val();
    gender = $('#genderFilter').val();
    $.get("{{ route('contacts.list') }}", { page: page, search: search, gender: gender }, function(data) {
        $('#contactList').html(data);
    });
}
    
$(document).ready(function() {

    loadContacts();

    // Pagination click (AJAX)
    $(document).on('click', '.pagination a', function(e) {
        e.preventDefault();
        const page = $(this).attr('href').split('page=')[1];
        loadContacts(page);
    });

    // Search
    $('#searchInput').on('keyup', function() {
        // alert("here");
        loadContacts();
    });


    // Gender
    $('#genderFilter').on('change', function() {
        loadContacts();
    });

    // Open Add Modal
    $('#addContactBtn').click(function() {
        $.get("{{ route('contacts.create') }}", function(data) {
            $('#contactModalContent').html(data);
            $('#contactModal').modal('show');
        });
    });


    // Delete
    $(document).on('click', '.deleteContact', function() {
        if (!confirm('Delete this contact?')) return;

        const id = $(this).data('id');

        $.ajax({
            url: `/contacts/${id}`,
            method: 'DELETE',
            data: { _token: '{{ csrf_token() }}' },
            success: function() {
                loadContacts();
            }
        });
    });
});
</script>
@endpush
