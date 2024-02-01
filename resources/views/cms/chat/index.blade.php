@extends('layout.app')

@section('breadcrumb')
<div class="row page-titles">
    <div class="col-lg-5 align-self-center">
        <h3>{{$title}}</h3>
    </div>
    <div class="col-lg-7 align-self-center">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{route('backend.dashboard.index')}}">Home</a>
            </li>
            <li class="breadcrumb-item">
                <label class="mb-0">Administrator</label>
            </li>
            <li class="breadcrumb-item">
                <label class="mb-0">Tools</label>
            </li>
            <li class="breadcrumb-item active">
                <strong>{{$title}}</strong>
            </li>
        </ol>
    </div>
</div>
@endsection

@section('content')
<div class="row pt-2">
    <div class="col-12">
        <div class="card">
            <div class="chat-main-box">
                <div class="chat-left-aside">
                    <div class="open-panel"><i class="ti-angle-right"></i></div>
                    <div class="chat-left-inner">
                        <div class="form-material">
                            
                        </div>
                        <ul class="chatonline style-none" style="overflow-y:auto;max-height:400px;">
                            
                        </ul>
                    </div>
                </div>
                <div class="chat-right-aside">
                    <div class="chat-main-header">
                        <div class="p-2 b-b">
                            <h3 class="box-title mb-0">AUZAN MUHAMMAD HANISA</h3>
                        </div>
                    </div>
                    <div class="chat-rbox" style="height:600px">
                        <ul id="chat-list" class="chat-list p-20" wire:poll="mountMessage" style="overflow-y:auto;max-height:600px;">

                            <li class="reverse">
                                <div class="chat-content">
                                    <div class="box bg-light-inverse" style="text-align: left;">hihihi</div>
                                </div>
                                <div class="chat-time">2023-09-06 06:01:03 </div>
                            </li>

                            <li class="reverse">
                                <div class="chat-content">
                                    <div class="box bg-light-inverse" style="text-align: left;">hihihi</div>
                                </div>
                                <div class="chat-time">2023-09-06 07:00:03 </div>
                            </li>

                            <li class="reverse">
                                <div class="chat-content">
                                    <div class="box bg-light-inverse" style="text-align: left;">hihihi</div>
                                </div>
                                <div class="chat-time">2023-09-06 07:00:36 </div>
                            </li>

                            <li class="reverse">
                                <div class="chat-content">
                                    <div class="box bg-light-inverse" style="text-align: left;">hihihi</div>
                                </div>
                                <div class="chat-time">2023-09-06 07:01:07 </div>
                            </li>

                            <li class="reverse">
                                <div class="chat-content">
                                    <div class="box bg-light-inverse" style="text-align: left;">hihihi</div>
                                </div>
                                <div class="chat-time">2023-09-06 08:00:02 </div>
                            </li>

                            <li class="reverse">
                                <div class="chat-content">
                                    <div class="box bg-light-inverse" style="text-align: left;">hihihi</div>
                                </div>
                                <div class="chat-time">2023-09-06 08:00:35 </div>
                            </li>

                            <li class="reverse">
                                <div class="chat-content">
                                    <div class="box bg-light-inverse" style="text-align: left;">hihihi</div>
                                </div>
                                <div class="chat-time">2023-09-06 08:01:05 </div>
                            </li>

                            <li class="reverse">
                                <div class="chat-content">
                                    <div class="box bg-light-inverse" style="text-align: left;">hihihi</div>
                                </div>
                                <div class="chat-time">2023-09-06 09:00:03 </div>
                            </li>

                            <li class="reverse">
                                <div class="chat-content">
                                    <div class="box bg-light-inverse" style="text-align: left;">hihihi</div>
                                </div>
                                <div class="chat-time">2023-09-06 09:00:36 </div>
                            </li>

                            <li class="reverse">
                                <div class="chat-content">
                                    <div class="box bg-light-inverse" style="text-align: left;">hihihi</div>
                                </div>
                                <div class="chat-time">2023-09-06 09:01:06 </div>
                            </li>

                            <li class="reverse">
                                <div class="chat-content">
                                    <div class="box bg-light-inverse" style="text-align: left;">hihihi</div>
                                </div>
                                <div class="chat-time">2023-09-06 10:00:03 </div>
                            </li>

                            <li class="reverse">
                                <div class="chat-content">
                                    <div class="box bg-light-inverse" style="text-align: left;">hihihi</div>
                                </div>
                                <div class="chat-time">2023-09-06 10:00:36 </div>
                            </li>

                            <li class="reverse">
                                <div class="chat-content">
                                    <div class="box bg-light-inverse" style="text-align: left;">hihihi</div>
                                </div>
                                <div class="chat-time">2023-09-06 10:01:07 </div>
                            </li>

                            <li class="reverse">
                                <div class="chat-content">
                                    <div class="box bg-light-inverse" style="text-align: left;">hihihi</div>
                                </div>
                                <div class="chat-time">2023-09-06 11:00:03 </div>
                            </li>

                            <li class="reverse">
                                <div class="chat-content">
                                    <div class="box bg-light-inverse" style="text-align: left;">hihihi</div>
                                </div>
                                <div class="chat-time">2023-09-06 11:00:37 </div>
                            </li>

                            <li class="reverse">
                                <div class="chat-content">
                                    <div class="box bg-light-inverse" style="text-align: left;">hihihi</div>
                                </div>
                                <div class="chat-time">2023-09-06 11:01:07 </div>
                            </li>

                            <li class="reverse">
                                <div class="chat-content">
                                    <div class="box bg-light-inverse" style="text-align: left;">hihihi</div>
                                </div>
                                <div class="chat-time">2023-09-06 12:00:03 </div>
                            </li>

                            <li class="reverse">
                                <div class="chat-content">
                                    <div class="box bg-light-inverse" style="text-align: left;">hihihi</div>
                                </div>
                                <div class="chat-time">2023-09-06 12:00:36 </div>
                            </li>

                            <li class="reverse">
                                <div class="chat-content">
                                    <div class="box bg-light-inverse" style="text-align: left;">hihihi</div>
                                </div>
                                <div class="chat-time">2023-09-06 12:01:06 </div>
                            </li>

                            <li class="reverse">
                                <div class="chat-content">
                                    <div class="box bg-light-inverse" style="text-align: left;">hihihi</div>
                                </div>
                                <div class="chat-time">2023-09-06 13:00:02 </div>
                            </li>

                            <li class="reverse">
                                <div class="chat-content">
                                    <div class="box bg-light-inverse" style="text-align: left;">hihihi</div>
                                </div>
                                <div class="chat-time">2023-09-06 13:00:33 </div>
                            </li>

                            <li class="reverse">
                                <div class="chat-content">
                                    <div class="box bg-light-inverse" style="text-align: left;">hihihi</div>
                                </div>
                                <div class="chat-time">2023-09-06 13:01:03 </div>
                            </li>

                            <li class="reverse">
                                <div class="chat-content">
                                    <div class="box bg-light-inverse" style="text-align: left;">hihihi</div>
                                </div>
                                <div class="chat-time">2023-09-06 14:00:02 </div>
                            </li>

                            <li class="reverse">
                                <div class="chat-content">
                                    <div class="box bg-light-inverse" style="text-align: left;">hihihi</div>
                                </div>
                                <div class="chat-time">2023-09-06 14:00:32 </div>
                            </li>

                            <li class="reverse">
                                <div class="chat-content">
                                    <div class="box bg-light-inverse" style="text-align: left;">hihihi</div>
                                </div>
                                <div class="chat-time">2023-09-06 14:01:05 </div>
                            </li>

                            <li class="reverse">
                                <div class="chat-content">
                                    <div class="box bg-light-inverse" style="text-align: left;">hihihi</div>
                                </div>
                                <div class="chat-time">2023-09-06 15:00:02 </div>
                            </li>

                            <li class="reverse">
                                <div class="chat-content">
                                    <div class="box bg-light-inverse" style="text-align: left;">hihihi</div>
                                </div>
                                <div class="chat-time">2023-09-06 15:00:33 </div>
                            </li>

                            <li class="reverse">
                                <div class="chat-content">
                                    <div class="box bg-light-inverse" style="text-align: left;">hihihi</div>
                                </div>
                                <div class="chat-time">2023-09-06 15:01:03 </div>
                            </li>

                            <li class="reverse">
                                <div class="chat-content">
                                    <div class="box bg-light-inverse" style="text-align: left;">hihihi</div>
                                </div>
                                <div class="chat-time">2023-09-06 16:00:02 </div>
                            </li>
                            <li>
                                <div class="chat-content">
                                    <div class="box bg-light-info">Ggg</div>
                                </div>
                                <div class="chat-time">2023-09-06 16:38:55</div>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body b-t" style="background-color: #d9d9d9;">
                        <div class="row">
                            <div class="col-12">
                                <textarea placeholder="Type your message here" class="form-control b-0" style="max-height:68px;"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

</div>

@endsection

@push('scripts')
<script src="https://js.pusher.com/7.2.0/pusher.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@joeattardi/emoji-button@3.0.3/dist/index.min.js"></script>
<script >
    // Gloabl Chatify variables from PHP to JS
    window.chatify = {
        name: "{{ config('chatify.name') }}",
        sounds: {!! json_encode(config('chatify.sounds')) !!},
        allowedImages: {!! json_encode(config('chatify.attachments.allowed_images')) !!},
        allowedFiles: {!! json_encode(config('chatify.attachments.allowed_files')) !!},
        maxUploadSize: {{ Chatify::getMaxUploadSize() }},
        pusher: {!! json_encode(config('chatify.pusher')) !!},
        pusherAuthEndpoint: '{{route("pusher.auth")}}'
    };
    window.chatify.allAllowedExtensions = chatify.allowedImages.concat(chatify.allowedFiles);
</script>
<script src="{{ asset('js/chatify/utils.js') }}"></script>
<script src="{{ asset('js/chatify/code.js') }}"></script>
@endpush