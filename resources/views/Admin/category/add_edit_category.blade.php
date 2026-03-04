@extends('layouts.app')

<style>
    :root {
        --primary: #8b5a2b;
        --primary-dark: #6d451f;
        --primary-light: #a5693b;
        --gray-dark: #2c3e50;
        --gray-medium: #34495e;
        --gray-light: #f8f9fa;
    }

    .container {
        max-width: 800px;
        margin: 40px auto 60px;
        padding: 0 15px;
    }

    .form-section {
        background: linear-gradient(145deg, #ffffff, #f8f9fa);
        padding: 30px;
        border-radius: 15px;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.05);
        transition: transform 0.25s ease, box-shadow 0.25s ease;
    }

    .form-section:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.08);
    }

    .form-section h4 {
        margin-bottom: 25px;
        color: var(--gray-dark);
        font-weight: 600;
        font-size: 24px;
        border-bottom: 2px solid rgba(139, 90, 43, 0.15);
        padding-bottom: 12px;
    }

    .form-label {
        font-weight: 500;
        color: var(--gray-medium);
        font-size: 14px;
        margin-bottom: 8px;
        display: block;
    }

    .form-control,
    .form-select {
        border-radius: 8px;
        border: 1px solid #dcdcdc;
        padding: 10px 15px;
        font-size: 14px;
        transition: border-color 0.3s ease, box-shadow 0.3s ease;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: var(--primary);
        box-shadow: 0 0 8px rgba(139, 90, 43, 0.2);
        outline: none;
    }

    .is-invalid {
        border-color: #dc3545;
    }

    .invalid-feedback {
        color: #dc3545;
        font-size: 12.5px;
        margin-top: 5px;
    }

    .current-icon-preview {
        max-width: 120px;
        max-height: 120px;
        object-fit: contain;
        border-radius: 8px;
        border: 1px solid #e0e0e0;
        background: #fff;
        padding: 8px;
        margin: 12px 0;
    }

    .btn-custom-primary {
        color: white;
        border: none;
        padding: 10px 28px;
        border-radius: 8px;
        font-weight: 500;
        font-size: 14px;
        transition: all 0.3s ease;
    }

    .btn-custom-primary:hover {
        background: linear-gradient(90deg, var(--primary-light), var(--primary));
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(139, 90, 43, 0.25);
    }

    .btn-custom-secondary {
        background-color: #6c757d;
        color: white;
        border: none;
        padding: 10px 28px;
        border-radius: 8px;
        font-weight: 500;
        font-size: 14px;
        transition: all 0.3s ease;
    }

    .btn-custom-secondary:hover {
        background-color: #5a6268;
        transform: translateY(-2px);
    }

    .alert-danger {
        border-radius: 8px;
        margin-bottom: 1.5rem;
    }

    .d-flex.gap-3 {
        gap: 1rem !important;
    }

    @media (max-width: 768px) {
        .form-section {
            padding: 24px 20px;
        }

        .form-section h4 {
            font-size: 21px;
        }

        .btn-custom-primary,
        .btn-custom-secondary {
            padding: 10px 24px;
            font-size: 13.5px;
            width: 100%;
        }

        .d-flex.gap-3 {
            flex-direction: column;
        }
    }

    .btn-custom-primary {
        background-color: #00a975 !important;
    }
</style>


@section('content')
<div class="container">
    <div class="form-section">
        <h4>{{ isset($data) ? 'Edit Category' : 'Add New Category' }}</h4>

        @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Please correct the following:</strong>
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        <form action="{{ isset($data) 
            ? url('_admin/secure/categories/edit/' . $data->id) 
            : url('_admin/secure/categories/add') }}"
            method="POST" enctype="multipart/form-data">

            @csrf

            @if(isset($data))
         
            <input type="hidden" name="id" value="{{ $data->id }}">
            @endif

            <!-- Category Name -->
            <div class="row mb-3">
                <div class="col-md-8 col-lg-6">
                    <label for="name" class="form-label">Category Name <span class="text-danger">*</span></label>
                    <input
                        type="text"
                        name="name"
                        id="name"
                        class="form-control @error('name') is-invalid @enderror"
                        value="{{ old('name', isset($data) ? $data->categorie : '') }}"
                        placeholder="e.g. Sedan, SUV, Hatchback"
                        autofocus>
                    @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Icon / Image Upload -->
            <div class="row mb-4">
                <div class="col-md-8 col-lg-6">
                    <label for="icon" class="form-label">Category Icon / Image</label>

                    @if(isset($data) && $data->category_icon)
                    <div>
                        <p class="small text-muted mb-2">Current icon:</p>
                        <img
                            src="{{ asset('cloud/'.$data->category_icon) }}"
                            alt="Current category icon"
                            class="current-icon-preview">
                    </div>
                    @endif

                    <input
                        type="file"
                        name="icon"
                        id="icon"
                        class="form-control @error('icon') is-invalid @enderror"
                        accept="image/png,image/jpeg,image/webp,image/svg+xml">
                    <small class="form-text text-muted mt-2 d-block">
                        Recommended: 128×128 px or larger • PNG, JPG, WebP, SVG
                    </small>
                    @error('icon')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="d-flex gap-3 mt-4 pt-3">
                <button type="submit" class="btn btn-custom-primary">
                    {{ isset($data) ? 'Update Category' : 'Add Category' }}
                </button>

                <a href="{{ url('_admin/secure/cars/categories') }}" class="text-decoration-none">
                    <button type="button" class="btn btn-custom-secondary">Cancel</button>
                </a>
            </div>
        </form>
    </div>
</div>
@endsection