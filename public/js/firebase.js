let timeAtAppOpening = moment(new Date()).format('YYYY-MM-DD HH:mm:ssZZ');
firebase.initializeApp({
    apiKey: "AIzaSyAeujgfj14n12N4EdXKvF8aBIy28xm8FXg",
    authDomain: "american-matrimony-5251e.firebaseapp.com",
    projectId: "american-matrimony-5251e",
    storageBucket: "american-matrimony-5251e.appspot.com",
    messagingSenderId: "926257858316",
    appId: "1:926257858316:web:32acf2d76d6f60369aab29",
    measurementId: "G-0LWKL3T0RQ"
});

const firestore = firebase.firestore();

const setUserOnlineStatus = (id, isUserOnline = false) => {
  firestore()
    .collection('users')
    .doc(id.toString())
    .set({
      isUserOnline,
    })
    .then(() => {
      console.log('Online status added!');
    })
    .catch((err) => console.log('error online status : ', err));
};

const getUserOnlineStatus = (id) =>
  firestore().collection('users').doc(id.toString()).get();

const callListener = (id) =>
  firestore()
    .collection('signaling')
    .doc(id.toString())
    .onSnapshot(({_data}) => {
      if (_data) {
        const {channelName, user, isAudioCall, createdAt} = _data;
        if (moment(createdAt).isAfter(moment(timeAtAppOpening)))
          EventBusSingleton.publish('showCallingModal', {
            channelName,
            user,
            isAudioCall,
          });
      }
    });

const signalUser = (callObj) => {
  firestore()
    .collection('signaling')
    .doc(callObj.target_user_id.toString())
    .set({
      ...callObj,
      createdAt: moment(new Date()).format('YYYY-MM-DD HH:mm:ssZZ').toString(),
      rand: Math.random(100),
    })
    .then(() => {
      console.log('signaling status added!');
    })
    .catch((err) => console.log('error adding signaling status : ', err));
};

const addCallStatus = (
  callerId,
  receiverId,
  isCallAccepted = false,
  isCallRejected = false,
) => {
  firestore()
    .collection('calls')
    .doc(
      +callerId > +receiverId
        ? `${callerId}_${receiverId}`
        : `${receiverId}_${callerId}`,
    )
    .set({
      isCallAccepted,
      isCallRejected,
      rand: Math.random(100),
    })
    .then(() => {
      console.log('Call status added!');
    })
    .catch((err) => console.log('error adding call status : ', err));
};

const listenCallStatus = (callerId, receiverId, cb) => {
  firestore()
    .collection('calls')
    .doc(
      +callerId > +receiverId
        ? `${callerId}_${receiverId}`
        : `${receiverId}_${callerId}`,
    )
    .onSnapshot(({_data, _metadata}) => {
      // checking all this metadata stuff to check if it is cached data
      if (_metadata && _metadata._metadata && _metadata._metadata[0] == false) {
        if (_data) {
          cb(_data);
        }
      }
    });
};