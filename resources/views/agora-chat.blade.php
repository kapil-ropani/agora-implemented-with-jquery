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
        @if($user && $user->id != Auth::user()->id)
            <div class="container my-5">
                <div class="row">
                    <div class="col">
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-primary mr-2"
                                {{-- onclick="placeCall({{$user->id}}, {{$user->name}})" --}}
                                >
                                Call {{ $user->name }}
                                <span class="badge badge-light" id="onlineStatus">Offline</span>
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

            {{-- <section id="video-container">
                <div id="local-video"></div>
                <div id="remote-video"></div>
                <div class="action-btns">
                    <button type="button" class="btn btn-info" onclick="handleAudioToggle()"> --}}
                        {{-- {{ $mutedAudio ? 'Unmute' : 'Mute' }} --}}
                    {{-- </button>
                    <button type="button" class="btn btn-primary mx-4" onclick="handleVideoToggle()"> --}}
                        {{-- {{ $mutedVideo ? 'ShowVideo' : 'HideVideo' }} --}}
                    {{-- </button>
                    <button type="button" class="btn btn-danger" onclick="endCall()">
                        EndCall
                    </button>
                </div>
            </section> --}}

        @else
           <h5 class="text-center mt-5 badge badge-info text-white p-2 d-block">No target user found!</h5>
        @endif
    </main>
@endsection

@push('custom-scripts')
    {{-- <script type="text/javascript" src="{{ URL::asset ('js/custom-scripts.js') }}"></script> --}}
    <script>
        var state = {
            targetUser: targetUser,
            appId: '33caa7b738a643abbd4c73cd653fc084',
            channelName: channelName ? channelName : `channel_${props.user.id}`,
            isCallInitiator: channelName ? false : true,
            token: null,
            joinSucceed: false,
            peerIds: [],
            isMute: false,
            isHideButtons: false,
            callerId: channelName ? channelName.split('_')[1] : null,
            isAudioCall: isAudioCall ? true : false,
        };

        function initUserOnlineChannel() {
            // this.userOnlineChannel = window.Echo.join("agora-online-channel");
        }

        function initUserOnlineListeners() {
            this.userOnlineChannel.here((users) => {
                this.onlineUsers = users;
            });
            this.userOnlineChannel.joining((user) => {
                // check user availability
                const joiningUserIndex = this.onlineUsers.findIndex(
                    (data) => data.id === user.id
                );
                if (joiningUserIndex < 0) {
                    this.onlineUsers.push(user);
                }
            });
            this.userOnlineChannel.leaving((user) => {
                const leavingUserIndex = this.onlineUsers.findIndex(
                    (data) => data.id === user.id
                );
                this.onlineUsers.splice(leavingUserIndex, 1);
            });
            // listen to incomming call
            this.userOnlineChannel.listen("MakeAgoraCall", ({
                data
            }) => {
                if (parseInt(data.userToCall) === parseInt(this.authuserid)) {
                    const callerIndex = this.onlineUsers.findIndex(
                        (user) => user.id === data.from
                    );
                    this.incomingCaller = this.onlineUsers[callerIndex]["name"];
                    this.incomingCall = true;
                    // the channel that was sent over to the user being called is what
                    // the receiver will use to join the call when accepting the call.
                    this.agoraChannel = data.channelName;
                }
            });
        }

        function endCall() {
            this.localStream.close();
            this.client.leave(
                () => {
                    console.log("Leave channel successfully");
                    this.callPlaced = false;
                },
                (err) => {
                    console.log("Leave channel failed");
            });
    }

    function getUserOnlineStatus(id) {}

        
    </script>
@endpush
