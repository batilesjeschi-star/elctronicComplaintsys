@extends('layouts.app')
@section('title', 'File a Complaint')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card p-4">
                <h4 class="mb-3"><i class="bi bi-megaphone"></i> File a New Complaint / Report</h4>

                <form method="POST" action="{{ route('complaints.store') }}" enctype="multipart/form-data">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label">Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" value="{{ old('title') }}" class="form-control" maxlength="255" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Category <span class="text-danger">*</span></label>
                        <select name="category" class="form-select" required>
                            <option value="">-- Select category --</option>
                            @foreach (\App\Models\Complaint::CATEGORIES as $category)
                                <option value="{{ $category }}" @selected(old('category') == $category)>{{ $category }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description <span class="text-danger">*</span></label>
                        <textarea name="description" class="form-control" rows="4" required>{{ old('description') }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Location / Address <span class="text-danger">*</span></label>
                        <input type="text" name="location" value="{{ old('location') }}" class="form-control" placeholder="e.g. Rizal Street, Purok 3" required>
                    </div>

                    <div class="row mb-3">
                        <div class="col">
                            <label class="form-label">Latitude (optional)</label>
                            <input type="text" name="latitude" id="latitude" value="{{ old('latitude') }}" class="form-control" placeholder="e.g. 14.5995">
                        </div>
                        <div class="col">
                            <label class="form-label">Longitude (optional)</label>
                            <input type="text" name="longitude" id="longitude" value="{{ old('longitude') }}" class="form-control" placeholder="e.g. 120.9842">
                        </div>
                        <div class="col-auto d-flex align-items-end">
                            <button type="button" id="useLocationBtn" class="btn btn-outline-secondary">
                                <i class="bi bi-geo-alt"></i> Use My Location
                            </button>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Upload Photo(s) — JPEG/PNG, up to 5 images</label>
                        <input type="file" name="images[]" id="images" class="form-control" accept="image/png, image/jpeg" multiple>
                        <div class="form-text">Each photo must be 4MB or smaller.</div>
                        <!-- Image preview thumbnails before upload -->
                        <div id="imagePreview" class="d-flex flex-wrap gap-2 mt-2"></div>
                    </div>

                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-send"></i> Submit Complaint
                    </button>
                    <a href="{{ route('complaints.index') }}" class="btn btn-outline-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    // Preview selected images before the form is submitted
    document.getElementById('images').addEventListener('change', function (e) {
        const preview = document.getElementById('imagePreview');
        preview.innerHTML = '';
        Array.from(e.target.files).forEach(file => {
            const reader = new FileReader();
            reader.onload = function (ev) {
                const img = document.createElement('img');
                img.src = ev.target.result;
                img.style.width = '90px';
                img.style.height = '90px';
                img.style.objectFit = 'cover';
                img.classList.add('rounded', 'border');
                preview.appendChild(img);
            };
            reader.readAsDataURL(file);
        });
    });

    // Optional: auto-fill GPS coordinates using the browser's geolocation API
    document.getElementById('useLocationBtn').addEventListener('click', function () {
        if (!navigator.geolocation) {
            alert('Geolocation is not supported by your browser.');
            return;
        }
        navigator.geolocation.getCurrentPosition(function (position) {
            document.getElementById('latitude').value = position.coords.latitude.toFixed(7);
            document.getElementById('longitude').value = position.coords.longitude.toFixed(7);
        }, function () {
            alert('Unable to retrieve your location.');
        });
    });
</script>
@endpush
