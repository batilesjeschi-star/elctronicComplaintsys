@extends('layouts.app')

@section('title', 'Report a Problem')

@section('content')

    <div class="mb-4">
        <h1 class="h3 fw-semibold mb-1">Report a Problem</h1>
        <p class="text-muted mb-0">Give as much detail as you can &mdash; photos and an exact location help barangay staff respond faster.</p>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card card-ecs">
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('complaints.store') }}" enctype="multipart/form-data" novalidate>
                        @csrf

                        <div class="mb-3">
                            <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" id="title" value="{{ old('title') }}"
                                   class="form-control @error('title') is-invalid @enderror"
                                   placeholder="e.g. Large pothole near the barangay hall" required>
                            @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label for="category" class="form-label">Category <span class="text-danger">*</span></label>
                            <select name="category" id="category" class="form-select @error('category') is-invalid @enderror" required>
                                <option value="" disabled {{ old('category') ? '' : 'selected' }}>Choose a category</option>
                                @foreach (\App\Models\Complaint::CATEGORIES as $value => $label)
                                    <option value="{{ $value }}" {{ old('category') === $value ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('category')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                            <textarea name="description" id="description" rows="4"
                                      class="form-control @error('description') is-invalid @enderror"
                                      placeholder="Describe what's happening, since when, and how it affects the community." required>{{ old('description') }}</textarea>
                            @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label for="location" class="form-label">Location / Address <span class="text-danger">*</span></label>
                            <input type="text" name="location" id="location" value="{{ old('location') }}"
                                   class="form-control @error('location') is-invalid @enderror"
                                   placeholder="e.g. Purok 3, near Sto. Niño Street corner" required>
                            @error('location')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-4 p-3 bg-ecs-tint rounded-3">
                            <label class="form-label fw-semibold mb-2">
                                <i class="bi bi-geo-alt me-1"></i> GPS Coordinates <span class="text-muted fw-normal">(optional)</span>
                            </label>
                            <div class="row g-2 mb-2">
                                <div class="col-sm-6">
                                    <input type="text" inputmode="decimal" name="latitude" id="latitude" value="{{ old('latitude') }}"
                                           class="form-control @error('latitude') is-invalid @enderror" placeholder="Latitude">
                                    @error('latitude')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-sm-6">
                                    <input type="text" inputmode="decimal" name="longitude" id="longitude" value="{{ old('longitude') }}"
                                           class="form-control @error('longitude') is-invalid @enderror" placeholder="Longitude">
                                    @error('longitude')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <button type="button" id="useLocationBtn" class="btn btn-sm btn-outline-ecs">
                                <i class="bi bi-crosshair"></i> Use My Current Location
                            </button>
                            <div id="locationStatus" class="form-text mb-0"></div>
                        </div>

                        <div class="mb-4">
                            <label for="images" class="form-label">Photos <span class="text-muted fw-normal">(optional, up to 5, JPEG/PNG)</span></label>
                            <input type="file" name="images[]" id="images" accept="image/png, image/jpeg"
                                   class="form-control @error('images') is-invalid @enderror
                                          @error('images.*') is-invalid @enderror" multiple>
                            @error('images')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            @error('images.*')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror

                            <div id="imagePreview" class="row row-cols-3 row-cols-md-5 g-2 mt-1"></div>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">Cancel</a>
                            <button type="submit" class="btn btn-ecs px-4">
                                <i class="bi bi-send-check me-1"></i> Submit Report
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
<script>
    // Live thumbnail preview of selected photos before they are uploaded.
    document.getElementById('images').addEventListener('change', function (event) {
        const preview = document.getElementById('imagePreview');
        preview.innerHTML = '';

        Array.from(event.target.files).slice(0, 5).forEach(function (file) {
            if (!file.type.startsWith('image/')) return;

            const reader = new FileReader();
            reader.onload = function (e) {
                const col = document.createElement('div');
                col.className = 'col';
                col.innerHTML = '<img src="' + e.target.result + '" class="img-thumbnail" style="aspect-ratio: 1 / 1; object-fit: cover;" alt="Photo preview">';
                preview.appendChild(col);
            };
            reader.readAsDataURL(file);
        });
    });

    // Optional one-click GPS capture using the browser's Geolocation API.
    document.getElementById('useLocationBtn').addEventListener('click', function () {
        const status = document.getElementById('locationStatus');

        if (!navigator.geolocation) {
            status.textContent = 'Geolocation is not supported by your browser. You can leave this blank.';
            return;
        }

        status.textContent = 'Getting your location...';

        navigator.geolocation.getCurrentPosition(function (position) {
            document.getElementById('latitude').value = position.coords.latitude.toFixed(7);
            document.getElementById('longitude').value = position.coords.longitude.toFixed(7);
            status.textContent = 'Location captured successfully.';
        }, function () {
            status.textContent = 'Unable to retrieve your location. You can leave this blank or enter it manually.';
        });
    });
</script>
@endsection
