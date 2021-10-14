@extends('layouts.app')
@push('custom-styles')
    {{-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.97.8/css/materialize.min.css" integrity="sha512-17AHGe9uFHHt+QaRYieK7bTdMMHBMi8PeWG99Mf/xEcfBLDCn0Gze8Xcx1KoSZxDnv+KnCC+os/vuQ7jrF/nkw==" crossorigin="anonymous" referrerpolicy="no-referrer" /> --}}
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
            <div class="modal fade bd-example-modal-lg" id="modal-video" tabindex="-1" role="dialog"
                aria-labelledby="myLargeModalLabel" aria-hidden="true">
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.28.0/moment.min.js"
        integrity="sha512-Q1f3TS3vSt1jQ8AwP2OuenztnLU6LwxgyyYOG1jgMW/cbEMHps/3wjvnl1P3WTrF3chJUWEoxDUEjMxDV8pujg=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="{{ asset('/js/firebase.js') }}"></script>
    <script src="{{ asset('/js/global-functions.js') }}"></script>
    
    {{-- It's a library that helps me to create sigleton pattern in browser apps --}}
    <script src="{{ asset('/js/light-event-bus.min.js') }}"></script>
    <script src="{{ asset('/js/AgoraRTCSDK-3.6.5.js') }}"></script>
    <script>
        $(document).ready(() => {
            var locationParams = location.pathname.split('/');
            var targetUserId = locationParams[locationParams.length - 1];
            var option = {
                mode: "rtc",
                codec: "vp8",
                channel_name: `channel_${targetUserId}`,
                appID: "{{ env('AGORA_APP_ID') }}",
                uid: "{{ Auth::user()->id }}",
                role: "host",
                token: '00633caa7b738a643abbd4c73cd653fc084IABJL1xjdIPsetN7Q0hTUInJNGGTIWExPd94R2VjCPLVfUKGHJgNvtUaCgDeOAAAWGJpYQAA'
            };

            var resolutions = [{
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

            function handleEvents(rtc) {
                // Occurs when an error message is reported and requires error handling.
                rtc.client.on("error", (err) => {
                    console.log(err)
                })
                // Occurs when the peer user leaves the channel; for example, the peer user calls Client.leave.
                rtc.client.on("peer-leave", function(evt) {
                    var id = evt.uid;
                    console.log("id", evt)
                    let streams = rtc.remoteStreams.filter(e => id !== e.getId())
                    let peerStream = rtc.remoteStreams.find(e => id === e.getId())
                    if (peerStream && peerStream.isPlaying()) {
                        peerStream.stop()
                    }
                    rtc.remoteStreams = streams
                    if (id !== rtc.params.uid) {
                        console.log("removied id");
                        // removeView(id)
                    }
                    // Toast.notice("peer leave")
                    console.log("peer-leave", id)
                })
                // Occurs when the local stream is published.
                rtc.client.on("stream-published", function(evt) {
                    // Toast.notice("stream published success")
                    console.log("stream-published")
                })
                // Occurs when the remote stream is added.
                rtc.client.on("stream-added", function(evt) {
                    var remoteStream = evt.stream
                    var id = remoteStream.getId()
                    // Toast.info("stream-added uid: " + id)
                    if (id !== rtc.params.uid) {
                        rtc.client.subscribe(remoteStream, function(err) {
                            console.log("stream subscribe failed", err)
                        })
                    }
                    console.log("stream-added remote-uid: ", id)
                })
                // Occurs when a user subscribes to a remote stream.
                rtc.client.on("stream-subscribed", function(evt) {
                    var remoteStream = evt.stream
                    var id = remoteStream.getId()
                    rtc.remoteStreams.push(remoteStream)
                    // addView(id)
                    $('#modal-video').modal('show');
                    remoteStream.play("remote-video");
                    // Toast.info("stream-subscribed remote-uid: " + id)
                    console.log("stream-subscribed remote-uid: ", id)
                })
                // Occurs when the remote stream is removed; for example, a peer user calls Client.unpublish.
                rtc.client.on("stream-removed", function(evt) {
                    var remoteStream = evt.stream
                    var id = remoteStream.getId()
                    // Toast.info("stream-removed uid: " + id)
                    if (remoteStream.isPlaying()) {
                        remoteStream.stop()
                    }
                    rtc.remoteStreams = rtc.remoteStreams.filter(function(stream) {
                        return stream.getId() !== id
                    })
                    // removeView(id)
                    console.log("stream-removed remote-uid: ", id)
                })
                rtc.client.on("onTokenPrivilegeWillExpire", function() {
                    // After requesting a new token
                    // rtc.client.renewToken(token);
                    // Toast.info("onTokenPrivilegeWillExpire")
                    console.log("onTokenPrivilegeWillExpire")
                })
                rtc.client.on("onTokenPrivilegeDidExpire", function() {
                    // After requesting a new token
                    // client.renewToken(token);
                    // Toast.info("onTokenPrivilegeDidExpire")
                    console.log("onTokenPrivilegeDidExpire")
                })
            }

            // console.log("agora sdk version: " + AgoraRTC.VERSION + " compatible: " + AgoraRTC.checkSystemRequirements());
            // _firestore.collection("users").get().then((querySnapshot) => {
            //     querySnapshot.forEach((doc) => {
            //         console.log(doc.data());
            //     });
            // });

            /**
             * rtc: rtc object
             * option: {
             *  mode: string, "live" | "rtc"
             *  codec: string, "h264" | "vp8"
             *  appID: string
             *  channel: string, channel name
             *  uid: number
             *  token; string,
             * }
             **/
            function join(rtc, option) {
                if (rtc.joined) {
                    Toast.error("Your already joined")
                    return;
                }

                /**
                 * A class defining the properties of the config parameter in the createClient method.
                 * Note:
                 *    Ensure that you do not leave mode and codec as empty.
                 *    Ensure that you set these properties before calling Client.join.
                 *  You could find more detail here. https://docs.agora.io/en/Video/API%20Reference/web/interfaces/agorartc.clientconfig.html
                 **/
                rtc.client = AgoraRTC.createClient({
                    mode: option.mode,
                    codec: option.codec
                })

                rtc.params = option

                // handle AgoraRTC client event
                handleEvents(rtc)

                // init client
                console.log(rtc.client)
                rtc.client.init(option.appID, function() {
                    console.log("init success")

                    /**
                     * Joins an AgoraRTC Channel
                     * This method joins an AgoraRTC channel.
                     * Parameters
                     * tokenOrKey: string | null
                     *    Low security requirements: Pass null as the parameter value.
                     *    High security requirements: Pass the string of the Token or Channel Key as the parameter value. See Use Security Keys for details.
                     *  channel: string
                     *    A string that provides a unique channel name for the Agora session. The length must be within 64 bytes. Supported character scopes:
                     *    26 lowercase English letters a-z
                     *    26 uppercase English letters A-Z
                     *    10 numbers 0-9
                     *    Space
                     *    "!", "#", "$", "%", "&", "(", ")", "+", "-", ":", ";", "<", "=", ".", ">", "?", "@", "[", "]", "^", "_", "{", "}", "|", "~", ","
                     *  uid: number | null
                     *    The user ID, an integer. Ensure this ID is unique. If you set the uid to null, the server assigns one and returns it in the onSuccess callback.
                     *   Note:
                     *      All users in the same channel should have the same type (number or string) of uid.
                     *      If you use a number as the user ID, it should be a 32-bit unsigned integer with a value ranging from 0 to (232-1).
                     **/
                    rtc.client.join(option.token ? option.token : null, option.channel_name, option.uid ? +
                        option
                        .uid : null,
                        function(uid) {
                            // Toast.notice("join channel: " + option.channel_name + " success, uid: " + uid)
                            console.log("join channel: " + option.channel_name + " success, uid: " +
                                uid)
                            rtc.joined = true

                            rtc.params.uid = uid

                            // create local stream
                            rtc.localStream = AgoraRTC.createStream({
                                streamID: rtc.params.uid,
                                audio: true,
                                video: true,
                                screen: false,
                                microphoneId: option.microphoneId,
                                cameraId: option.cameraId
                            })

                            // initialize local stream. Callback function executed after intitialization is done
                            rtc.localStream.init(function() {
                                console.log("init local stream success")
                                // play stream with html element id "local-video"
                                rtc.localStream.play("local-video")

                                // publish local stream
                                publish(rtc)
                            }, function(err) {
                                // Toast.error(
                                //     "stream init failed, please open console see more detail"
                                // )
                                console.error("init local stream failed ", err)
                            })
                        },
                        function(err) {
                            // Toast.error("client join failed, please open console see more detail")
                            console.error("client join failed", err)
                        })
                }, (err) => {
                    // Toast.error("client init failed, please open console see more detail")
                    console.error(err)
                })
            }

            function publish(rtc) {
                if (!rtc.client) {
                    Toast.error("Please Join Room First")
                    return
                }
                if (rtc.published) {
                    Toast.error("Your already published")
                    return
                }

                var oldState = rtc.published

                // publish localStream
                rtc.client.publish(rtc.localStream, function(err) {
                    rtc.published = oldState
                    console.log("publish failed")
                    // Toast.error("publish failed")
                    console.error(err)
                })
                // Toast.info("publish")
                console.log("Published")
                rtc.published = true
            }

            function unpublish(rtc) {
                if (!rtc.client) {
                    console.error("Please Join Room First")

                    return
                }
                if (!rtc.published) {
                    console.error("Your didn't publish")
                    return
                }
                var oldState = rtc.published
                rtc.client.unpublish(rtc.localStream, function(err) {
                    rtc.published = oldState
                    console.log("unpublish failed")
                    // Toast.error("unpublish failed")
                    console.error(err)
                })
                // Toast.info("unpublish")
                console.log("unpulish")
                rtc.published = false
            }

            function leave(rtc) {
                if (!rtc.client) {
                    Toast.error("Please Join First!")
                    return
                }
                if (!rtc.joined) {
                    Toast.error("You are not in channel")
                    return
                }
                /**
                 * Leaves an AgoraRTC Channel
                 * This method enables a user to leave a channel.
                 **/
                rtc.client.leave(function() {
                    // stop stream
                    if (rtc.localStream.isPlaying()) {
                        rtc.localStream.stop()
                    }
                    // close stream
                    rtc.localStream.close()
                    for (let i = 0; i < rtc.remoteStreams.length; i++) {
                        var stream = rtc.remoteStreams.shift()
                        var id = stream.getId()
                        if (stream.isPlaying()) {
                            stream.stop()
                        }
                        removeView(id)
                    }
                    rtc.localStream = null
                    rtc.remoteStreams = []
                    rtc.client = null
                    console.log("client leaves channel success")
                    rtc.published = false
                    rtc.joined = false
                    // Toast.notice("leave success")
                }, function(err) {
                    console.log("channel leave failed")
                    Toast.error("leave success")
                    console.error(err)
                })
            }

            $("button.call-type").click(async function(e) {
                const callType = $(this).data('call-type');
                if (parseInt(callType)) {
                    // initiate audio call
                } else {
                    // initiate video call
                    var fields = ["appID", "channel"];
                    // $(this).on("click", async function(e) {
                        console.log("join video call")
                        // e.preventDefault();
                        // var params = serializeformData(); // Data is feteched and serilized from the form element.

                        // if (validator(params, fields)) {
                            // let response = await fetch(
                            //     'https://retrocubedev.com/dev/american_matrimony/public/api/agora/token', {
                            //         method: 'POST',
                            //         headers: {
                            //             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                            //                 'content'),
                            //             'token': 'api.Pd*!(5675',
                            //             'user-token': '{{ Auth::user()->token }}'
                            //         },
                            //         body: JSON.stringify(option)
                            //     });

                            // let tokenResponse = await response.json();

                            // console.log("token", tokenResponse)
                            // console.log(rtc.client)
                            join(rtc, option)
                        // }
                    // })
                }
            });
        });
    </script>
@endpush
