@extends('adminlte::page')

@section('title', 'Edit Category')

@section('css')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.6/cropper.css" integrity="sha256-jKV9n9bkk/CTP8zbtEtnKaKf+ehRovOYeKoyfthwbC8=" crossorigin="anonymous" />
<link rel="stylesheet" href="{{ asset('css/custom.css') }}" />
@stop

@section('content_header')
    <div class="row">
        <div class="col-6">
            <h1>Edit Category</h1>
        </div>
    </div>
@stop

@section('content')

<form action="{{ route('products.update',$product->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <strong>Name*:</strong>
                <input type="text" name="name" class="form-control" placeholder="Name" value="{{old('name', $product->name)}}">
                @if ($errors->has('name'))
                    <span class="text-danger">{{ $errors->first('name') }}</span>
                @endif
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <strong>Description*:</strong>
                <textarea class="form-control" rows="10" name="description" placeholder="Description">{{old('description', $product->description)}}</textarea>
                @if ($errors->has('description'))
                    <span class="text-danger">{{ $errors->first('description') }}</span>
                @endif
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <strong>Category*:</strong>
                <select id="category" name="category" class="form-control">
                    <option value="">Select Category</option>
                </select>
                @if ($errors->has('category'))
                    <span class="text-danger">{{ $errors->first('category') }}</span>
                @endif
            </div>
        </div>
        <div class="col-xs-8 col-sm-8 col-md-8">
            <div class="form-group">
                <strong>Image:</strong>
                <input type="hidden" id="base64Image" name="base64Image">
                <input type="file" name="image" class="form-control image" accept="image/*">
                <span>Note: Minimum image dimension 250X250</span><br>
                @if ($errors->has('image'))
                    <span class="text-danger">{{ $errors->first('image') }}</span>
                @endif
            </div>
        </div>
        <div class="col-xs-4 col-sm-4 col-md-4">
            <img class="img-responsive" id="imagePreview" src="{{asset('uploads/thumbnail/'.$product->image)}}" height="100" width="100">
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12">
            <button type="submit" class="btn btn-primary">Submit</button>
            <a class="btn btn-secondary" href="{{ route('products.index') }}"> Cancel</a>
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
        var category = "{{old('category', $product->category_id)}}";
        var url = "{{ route('get-categories-dropdown') }}";
        setTimeout(function(){ getCategoriesDropDown(url, 'category', category); }, 300);
    });
</script>
@stop