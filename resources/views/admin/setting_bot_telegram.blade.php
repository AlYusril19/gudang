@extends('layouts.app_sneat')

@section('content')
<div class="row justify-content-center">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="mb-0">Pengaturan BOT Telegram</h5>
                    <small class="text-muted float-end">Data Bot Telegram</small>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('setting.store') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group mt-3">TOKEN BOT : {{ \Str::mask(settings('token_bot'), '*',0 , 10) }}</div>
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="token_bot">TOKEN BOT</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="token_bot" name="token_bot" placeholder="123456:AbCdE123" nullable>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="test_chat">Chat ID</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="test_chat" name="test_chat" placeholder="12345678" required>
                            </div>
                        </div>
                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary me-2">Kirim</button>
                            {{-- <button type="reset" class="btn btn-outline-secondary">Batal</button> --}}
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
