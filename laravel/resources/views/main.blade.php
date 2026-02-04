@extends('layouts.shell')

@section('title', 'GrapeSEED Brochure Main')

@section('content')
    <header class="mb-10 max-w-5xl mx-auto">
        <h1 class="text-3xl font-bold text-primary dark:text-purple-400 mb-2">GrapeSEED Brochure Main Page</h1>
        <p class="text-gray-600 dark:text-gray-300 mb-8">필요한 브로셔를 신청하세요. 신청하신 브로셔는 최대 3일 이내에 발송됩니다.</p>
    </header>
    <div class="max-w-5xl mx-auto flex flex-col sm:flex-row flex-wrap gap-4">
        <a href="{{ url('requestbrochure') }}" class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-primary hover:bg-primary/90 text-white font-semibold rounded-lg transition-colors shadow-sm">
            <span class="material-icons text-xl">description</span>
            브로셔 신청
        </a>
        <a href="{{ url('requestbrochure-list') }}" class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-primary hover:bg-primary/90 text-white font-semibold rounded-lg transition-colors shadow-sm">
            <span class="material-icons text-xl">history</span>
            브로셔 송장조회
        </a>
        <a href="{{ url('admin/login') }}" class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-primary hover:bg-primary/90 text-white font-semibold rounded-lg transition-colors shadow-sm">
            <span class="material-icons text-xl">settings</span>
            관리자 페이지
        </a>
    </div>
@endsection
