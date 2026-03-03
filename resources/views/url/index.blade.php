@extends('layouts.app')

@section('title', 'Сервис коротких ссылок')

@section('content')
<div class="main-container" id="nprogress-parent">
    <div class="icon-wrapper">
        🔗
    </div>
    <h1 class="page-title">Сервис коротких ссылок</h1>

    <div class="start-page-body">
        <p class="page-subtitle">Создайте короткую ссылку за секунды</p>

        <form id="shortenForm" method="post" action="{{ url('/api/shorten') }}">
            @csrf
            <div class="input-wrapper">
                <div style="min-height: 100px">
                    <label class="form-label" for="urlInput">Введите URL адрес</label>
                    <input type="text" name="url" id="urlInput" class="form-control url-input"
                           autofocus placeholder="https://example.com">
                </div>

                <div class="text-center" style="margin-top: 30px;">
                    <button type="submit" class="btn btn-shorten btn-lg">
                        <span>Сократить ссылку</span>
                    </button>
                </div>
            </div>
        </form>
    </div>

    <div class="result-page-body">
        <p class="page-subtitle">Ваша короткая ссылка готова!</p>
        <div class="input-wrapper">
            <div>
                <label class="form-label" for="originalUrlResult">Оригинальный URL</label>
                <div class="input-group">
                    <input type="text" id="originalUrlResult" class="form-control url-input" readonly>
                    <button class="btn btn-shorten" type="button" onclick="copyField('originalUrlResult', this)" title="Копировать">
                        <span>⧉</span>
                    </button>
                </div>
            </div>
            <div class="row" style="margin-top: 20px;">
                <div class="col-md-6">
                    <label class="form-label" for="shortUrlResult">Короткий URL</label>
                    <div class="input-group">
                        <input type="text" id="shortUrlResult" class="form-control url-input" readonly>
                        <button class="btn btn-shorten" type="button" id="copyBtn" onclick="copyField('shortUrlResult', this)" title="Копировать">
                            <span>⧉</span>
                        </button>
                    </div>
                </div>
                <div class="col-md-6 text-center">
                    <label class="form-label">QR код</label>
                    <div>
                        <img id="qrCodeImage" src="" alt="QR код" class="qr-code-img">
                    </div>
                </div>
            </div>
            <div class="text-center" style="margin-top: 30px;">
                <button class="btn btn-shorten btn-lg" type="button" onclick="goBack()">
                    <span>&#8592; Сократить ещё</span>
                </button>
            </div>
        </div>
    </div>
</div>
@endsection
