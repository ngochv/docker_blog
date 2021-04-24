@extends('layouts.backend.app')

@section('title','Update Category')

@push('css')
    <style>
        .img img {
            width: 100%;
        }
        .img {
            width: 50%;
        }
    </style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Vertical Layout | With Floating Label -->
    <div class="row clearfix">
        <div class="col-md-6 col-sm-12 col-xs-12 col-md-offset-3">
            <div class="card">
                <div class="header">
                    <h2>
                       UPDATE TAG
                    </h2>
                </div>
                <div class="body">
                    <form action="{{ route('admin.category.update', ['id' => $category->id]) }}" method="POST"  enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="form-group form-float">
                            <div class="form-line">
                                <input type="text" id="name" class="form-control" name="name" required value="{{ old('name') ? old('name') :  $category->name}}">
                                <label class="form-label">Category Name</label>
                            </div>
                        </div>
                        <div class="form-group form-float">
                            <div class="form-line">
                                <input type="file" id="image" class="form-control" name="image">
                            </div>
                        </div>
                        <div class="img">
                            <img src="{{ asset('storage/category/slider/' . $category->image) }}" alt="{{ $category->name }}">
                        </div>
                        <a  class="btn btn-danger m-t-15 waves-effect" href="{{ route('admin.tag.index') }}">BACK</a>
                        <button type="submit" class="btn btn-primary m-t-15 waves-effect">UPDATE</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


@push('js')
@endpush
