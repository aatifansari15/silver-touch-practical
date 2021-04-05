@extends('adminlte::page')

@section('title', 'Add Category')

@section('css')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.6/cropper.css" integrity="sha256-jKV9n9bkk/CTP8zbtEtnKaKf+ehRovOYeKoyfthwbC8=" crossorigin="anonymous" />
@stop

@section('content_header')
    <div class="row">
        <div class="col-6">
            <h1>Create New Category</h1>
        </div>
    </div>
@stop

@section('content')

<form action="{{ route('categories.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <strong>Name*:</strong>
                <input type="text" name="name" class="form-control" placeholder="Name" value="{{old('name')}}">
                @if ($errors->has('name'))
                    <span class="text-danger">{{ $errors->first('name') }}</span>
                @endif
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <strong>Parent:</strong>
                <select id="parent_id" name="parent_id" class="form-control">
                    <option value="">Select Parent</option>
                </select>
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <strong>Image*:</strong>
                <input type="hidden" id="base64Image" name="base64Image">
                <input type="file" name="image" class="form-control image" accept="image/*">
                <span>Note: Minimum image dimension 250X250</span><br>
                @if ($errors->has('image'))
                    <span class="text-danger">{{ $errors->first('image') }}</span>
                @endif
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12">
            <button type="submit" class="btn btn-primary">Submit</button>
            <a class="btn btn-secondary" href="{{ route('categories.index') }}"> Cancel</a>
        </div>
    </div>
</form>

@include('popup.cropper')

@endsection

@section('js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.6/cropper.js" integrity="sha256-CgvH7sz3tHhkiVKh05kSUgG97YtzYNnWt6OXcmYzqHY=" crossorigin="anonymous"></script>
<script src="{{ asset('js/custom-cropper.js') }}"></script>
<script src="{{ asset('js/category-dropdown.js') }}"></script>

<script type="text/javascript">
    $(document).ready(function() {
        var parent_id = "{{old('parent_id')}}";
        var url = "{{ route('get-categories-dropdown') }}";
        setTimeout(function(){ getCategoriesDropDown(url, 'parent_id', parent_id); }, 300);
    });
</script>
@stop