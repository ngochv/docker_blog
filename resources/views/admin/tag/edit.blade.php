@extends('layouts.backend.app')

@section('title','Tag list')

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
                       UPDATE TAG
                    </h2>
                </div>
                <div class="body">
                    <form action="{{ route('admin.tag.update', ['id' => $tag->id]) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="form-group form-float">
                            <div class="form-line">
                                <input type="text" id="name" class="form-control" name="name" required value="{{ $tag->name }}">
                                <label class="form-label">Tag Name</label>
                            </div>
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
