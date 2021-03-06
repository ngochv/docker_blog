@extends('layouts.backend.app')

@section('title','Category Add')

@push('css')
@endpush

@section('content')
<div class="container-fluid">
    <!-- Vertical Layout | With Floating Label -->
    <div class="row clearfix">
        <div class="col-md-6 col-sm-12 col-xs-12 col-md-offset-3">
            <div class="card">
                <div class="header">
                    <h2>
                       ADD NEW CATEGORY
                    </h2>
                </div>
                <div class="body">
                    <form action="{{ route('admin.category.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group form-float">
                            <div class="form-line">
                                <input type="text" id="name" class="form-control" name="name" required>
                                <label class="form-label">Category Name</label>
                            </div>
                        </div>
                        <div class="form-group form-float">
                            <div class="form-line">
                                <input type="file" id="image" class="form-control" name="image" required>
                            </div>
                        </div>

                        <a  class="btn btn-danger m-t-15 waves-effect" href="{{ route('admin.category.index') }}">BACK</a>
                        <button type="submit" class="btn btn-primary m-t-15 waves-effect">SUBMIT</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


@push('js')
@endpush
