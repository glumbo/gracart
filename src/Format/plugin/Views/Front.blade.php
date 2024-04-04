@extends($gc_templatePath.'.layout')

@section('main')
    {{-- Content --}}
@endsection

@section('breadcrumb')
    <div class="breadcrumbs">
        <ol class="breadcrumb">
          <li><a href="{{ gc_route('home') }}">{{ gc_language_render('front.home') }}</a></li>
          <li class="active">{{ $title ?? '' }}</li>
        </ol>
      </div>
@endsection

@push('styles')
      {{-- style css --}}
@endpush

@push('scripts')
      {{-- script --}}
@endpush