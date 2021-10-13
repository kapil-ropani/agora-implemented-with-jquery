@extends('layouts.app')
@push('custom-styles')
    <style scoped>
        main {
            margin-top: 50px;
        }

        #video-container {
            width: 700px;
            height: 500px;
            max-width: 90vw;
            max-height: 50vh;
            margin: 0 auto;
            border: 1px solid #099dfd;
            position: relative;
            box-shadow: 1px 1px 11px #9e9e9e;
            background-color: #fff;
        }

        #local-video {
            width: 30%;
            height: 30%;
            position: absolute;
            left: 10px;
            bottom: 10px;
            border: 1px solid #fff;
            border-radius: 6px;
            z-index: 2;
            cursor: pointer;
        }

        #remote-video {
            width: 100%;
            height: 100%;
            position: absolute;
            left: 0;
            right: 0;
            bottom: 0;
            top: 0;
            z-index: 1;
            margin: 0;
            padding: 0;
            cursor: pointer;
        }

        .action-btns {
            position: absolute;
            bottom: 20px;
            left: 50%;
            margin-left: -50px;
            z-index: 3;
            display: flex;
            flex-direction: row;
            flex-wrap: wrap;
        }

        #login-form {
            margin-top: 100px;
        }

    </style>
@endpush
@section('content')
    {{-- <agora-chat :allusers="{{ $users }}" authuserid="{{ auth()->id() }}" authuser="{{ auth()->user()->name }}"
        agora_id="{{ env('AGORA_APP_ID') }}" /> --}}
    <main>
        <div class="container">
            <div class="row">
                <div class="col-12 text-center">
                    <img src="{{ asset('img\agora-logo.svg') }}" alt="Agora Logo" class="img-fuild" />
                </div>
            </div>
        </div>
        @if ($user && $user->id != Auth::user()->id)
            <div class="container my-5">
                <div class="row">
                    <div class="col">
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-primary mr-2" {{-- onclick="placeCall({{$user->id}}, {{$user->name}})" --}}>
                                Call {{ $user->name }}
                                <span class="badge badge-light" id="onlineStatus">Offline</span>
                                <div class="col">
                                    {{-- 1 = Audio Call --}}
                                    <button data-call-type="1" class="call-type btn btn-info btn-sm">Audio Call</button>
                                    {{-- 0 = Video Call --}}
                                    <button data-call-type="0" class="call-type btn btn-danger btn-sm">Video Call</button>
                                </div>
                            </button>
                        </div>
                    </div>
                </div>
                <!-- Incoming Call  -->
                {{-- <div class="row my-5" >
                    <div class="col-12">
                        <p>
                            Incoming Call From <strong>{{ "incomingCaller" }}</strong>
                        </p>
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-danger" data-dismiss="modal" onclick="declineCall()">
                                Decline
                            </button>
                            <button type="button" class="btn btn-success ml-5" onclick="acceptCall()">
                                Accept
                            </button>
                        </div>
                    </div>
                </div> --}}
                <!-- End of Incoming Call  -->
            </div>
            <div class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <section id="video-container">
                            <div id="local-video"></div>
                            <div id="remote-video"></div>
                            <div class="action-btns">
                                <button type="button" class="btn btn-info" onclick="handleAudioToggle()"> --}}
                                    {{-- {{ $mutedAudio ? 'Unmute' : 'Mute' }} --}}
                                </button>
                                <button type="button" class="btn btn-primary mx-4" onclick="handleVideoToggle()">
                                    {{-- {{ $mutedVideo ? 'ShowVideo' : 'HideVideo' }} --}}
                                </button>
                                <button type="button" class="btn btn-danger" onclick="endCall()">
                                    EndCall
                                </button>
                            </div>
                        </section>
                    </div>
                </div>
            </div>
        @else
            <h5 class="text-center mt-5 badge badge-info text-white p-2 d-block">No target user found!</h5>
        @endif
    </main>
@endsection

@push('custom-scripts')
    <script src="https://www.gstatic.com/firebasejs/8.10.0/firebase-app.js"></script>
    <script src="https://www.gstatic.com/firebasejs/8.10.0/firebase-firestore.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.28.0/moment.min.js" integrity="sha512-Q1f3TS3vSt1jQ8AwP2OuenztnLU6LwxgyyYOG1jgMW/cbEMHps/3wjvnl1P3WTrF3chJUWEoxDUEjMxDV8pujg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="{{ asset('/js/firebase.js') }}"></script>
    {{-- It's a library that helps me to create sigleton pattern in browser apps --}}
    <script src="{{ asset('/js/light-event-bus.min.js') }}"></script>
    <script src="{{ asset('/js/AgoraRTC_N-4.7.1.js') }}"></script>
    <script>
        $(document).ready(() => {
            var resolutions = [
                {
                    name: "default",
                    value: "default",
                },
                {
                    name: "480p",
                    value: "480p",
                },
                {
                    name: "720p",
                    value: "720p",
                },
                {
                    name: "1080p",
                    value: "1080p"
                }
            ];

            var rtc = {
                client: null,
                joined: false,
                published: false,
                localStream: null,
                remoteStreams: [],
                params: {}
            };
            // console.log("agora sdk version: " + AgoraRTC.VERSION + " compatible: " + AgoraRTC.checkSystemRequirements());
            // _firestore.collection("users").get().then((querySnapshot) => {
            //     querySnapshot.forEach((doc) => {
            //         console.log(doc.data());
            //     });
            // });

            $("button.call-type").click(function(e) {
                const callType = $(this).data('call-type');
                if (parseInt(callType)) {
                    // initiate audio call
                } else {
                    // initiate video call
                }
            });
        });
    </script>
@endpush
