/**
 * collector.js
 *
 * Creator: Camdyn Rasque
 * Date: August 17th, 2021
 *
 * Description: This script will collect all of the static, performance, and
 * activity data for a user visiting a webpage
 *
 * Note: You may modify this file as much as you like, so long as you do not 
 * remove any of the metrics that it collects.
 */


/**
 * I've created one singular data object to hold everything for you for ease of
 * use. I've exported it so you can import it to your own modules for some
 * cleaner code if you like. I've initialized everything to null or empty
 * arrays so that you can see what data is available to you easily. If anything
 * is still null when you try to read it, then either that event hasn't
 * occurred yet or that data isn't available in the user's browser (e.g. the 
 * connection object inside the static object is a chromium feature I believe).
 * I implore you to be curious, poke around, and look up any metrics that you
 * are unfamiliar with.
 * 
 * Note: The static and performance objects have a "ready" key, this is just to
 * let you know that those objects have been set and are ready to be sent.
 * Activity doesn't have one since it's continuous
 */
export const data = {
  static: {
    sess_id: null,
    userAgent: null,
    language: null,
    acceptsCookies: null,
    screenDimensions: {
      inner: {
        innerWidth: null,
        innerHeight: null
      },
      outer: {
        outerWidth: null,
        outerHeight: null
      }
    },
    connection: {
      downlink: null,
      effectiveType: null,
      rtt: null,
      saveData: null
    },
    ready: false
  },
  performance: {
    sess_id: null,
    startTime: null,
    fetchStart: null,
    requestStart: null,
    responseStart: null,
    responseEnd: null,
    domInteractive: null,
    domContentLoadedEventStart: null,
    domContentLoadedEventEnd: null,
    domComplete: null,
    loadEventStart: null,
    loadEventEnd: null,
    duration: null,
    transferSize: null,
    decodedBodySize: null,
    ready: false
  },
  activity: {
    mousePosition: [],
    mouseClicks: [],
    keystrokes: {
      keydown: [],
      keyup: []
    },
    timing: {
      sess_id : localStorage.getItem('session-id'),
      activityType : "timing",
      activityInfo : {
        pageEnter: null,
        pageLeave: null,
        currPage: null
      }
    }
  }
};

var dataQueue = [];
var static_url = "https://darrenwu.xyz/api/static";
var performance_url = "https://darrenwu.xyz/api/performance";
var activity_url = "https://darrenwu.xyz/api/activity";

let headers = {
  type: 'application/json'
};

// https://usefulangle.com/post/334/javascript-implement-analytics-sessions
var current_ts = Math.floor(Date.now() / 1000);

if(localStorage.getItem('session-id') === null || localStorage.getItem('expiry-ts') === null || current_ts > parseInt(localStorage.getItem('expiry-ts'), 10)) {
	var session_id = [(Math.floor(Math.random() * (99999999 - 11111111 + 1)) + 11111111), Date.now()].join('');
	localStorage.setItem('session-id', session_id);
}

localStorage.setItem('expiry-ts', current_ts + 1800);

// Get the current time as soon as this script loads for an accurate page enter
data.activity.timing.pageEnter = new Date().getTime();
// Get the URL path as well since that does not require the page to load
data.activity.timing.currPage = window.location.pathname;
// Right before the user leaves the page, capture the time and store it
window.addEventListener('beforeunload', () => {
  data.activity.timing.pageLeave = new Date().getTime();

  let blob = new Blob([JSON.stringify(data.activity.timing)], headers);
  while(!navigator.sendBeacon(activity_url, blob)){}
});

/**
 * Collects all of the static data outlined in the data object above
 */
function collectStaticData() {
  data.static.sess_id = localStorage.getItem('session-id');
  data.static.userAgent = navigator.userAgent;
  data.static.language = navigator.language;
  data.static.acceptsCookies = navigator.cookieEnabled;
  data.static.screenDimensions = {
    inner: {
      innerWidth: window.innerWidth,
      innerHeight: window.innerHeight
    },
    outer: {
      outerWidth: window.outerWidth,
      outerHeight: window.outerHeight
    }
  };
  if (navigator.connection) {
    data.static.connection = {
      downlink: navigator.connection.downlink,
      effectiveType: navigator.connection.effectiveType,
      rtt: navigator.connection.rtt,
      saveData: navigator.connection.saveData
    };
  }
  data.static.ready = true;

  let blob = new Blob([JSON.stringify(data.static)], headers);
  while(!navigator.sendBeacon(static_url, blob)){}
}

/**
 * Collects all of the performance data outlined in the data object above
 */
function collectPerformanceData() {
  let perf = performance.getEntriesByType('navigation')[0];
  // Safari doesn't support the new PerformanceNavigationTiming API yet
  perf = performance.timing || perf;
  // Call this method every 250ms to check if loading has finished
  if (perf.loadEventEnd == 0) {
    setTimeout(collectPerformanceData, 250);
  } else {
    data.performance.sess_id = localStorage.getItem('session-id');
    data.performance.startTime = perf.startTime;
    data.performance.fetchStart = perf.fetchStart;
    data.performance.requestStart = perf.requestStart;
    data.performance.responseStart = perf.responseStart;
    data.performance.responseEnd = perf.responseEnd;
    data.performance.domInteractive = perf.domInteractive;
    data.performance.domContentLoadedEventStart = perf.domContentLoadedEventStart;
    data.performance.domContentLoadedEventEnd = perf.domContentLoadedEventEnd;
    data.performance.domComplete = perf.domComplete;
    data.performance.loadEventStart = perf.loadEventStart;
    data.performance.loadEventEnd = perf.loadEventEnd;
    data.performance.duration = data.performance.loadEventEnd - data.performance.fetchStart;
    data.performance.transferSize = perf.transferSize;
    data.performance.decodedBodySize = perf.decodedBodySize;
    data.performance.ready = true;
  }

  let blob = new Blob([JSON.stringify(data.performance)], headers);
  while(!navigator.sendBeacon(performance_url, blob)){}
}

/**
 * Binds all of the event listeners for mouse clicks and keystrokes
 */
function bindActivityEvents() {
  let mousemoveEvents = 0;

  // Record every 10th mouse coordinate inside the window (there will be a lot)
  window.addEventListener('mousemove', e => {
    mousemoveEvents += 1;
    if (mousemoveEvents % 10 != 0) return;
    let newMouseMove = {
      sess_id : localStorage.getItem('session-id'),
      activityType: "mouse_position",
      activityInfo: {
        clientX: e.clientX,
        clientY: e.clientY,
        layerX: e.layerX,
        layerY: e.layerY,
        offsetX: e.offsetX,
        offsetY: e.offsetY,
        pageX: e.pageX,
        pageY: e.pageY,
        screenX: e.screenX,
        screenY: e.screenY,
        x: e.x,
        y: e.y,
      },
      altKey: e.altKey,
      ctrlKey: e.ctrlKey,
      shiftKey: e.shiftKey,
      timestamp: e.timeStamp
    };
    data.activity.mousePosition.push(newMouseMove);
    dataQueue.push(newMouseMove);
  });

  // Record all mouse clicks inside the window
  window.addEventListener('click', e => {
    let newClick = {
      sess_id : localStorage.getItem('session-id'),
      activityType: "mouse_click",
      activityInfo: {
        clientX: e.clientX,
        clientY: e.clientY,
        layerX: e.layerX,
        layerY: e.layerY,
        offsetX: e.offsetX,
        offsetY: e.offsetY,
        pageX: e.pageX,
        pageY: e.pageY,
        screenX: e.screenX,
        screenY: e.screenY,
        x: e.x,
        y: e.y,
      },
      altKey: e.altKey,
      ctrlKey: e.ctrlKey,
      shiftKey: e.shiftKey,
      timestamp: e.timeStamp
    };
    data.activity.mouseClicks.push(newClick);
    dataQueue.push(newClick);
  });

  // Record all keydowns inside the window
  window.addEventListener('keydown', e => {
    let newKeydown = {
      sess_id : localStorage.getItem('session-id'),
      activityType : "key_down",
      activityInfo: {
        key: e.key,
        code: e.code
      },
      altKey: e.altKey,
      ctrlKey: e.ctrlKey,
      shiftKey: e.shiftKey,
      timestamp: e.timeStamp
    };
    data.activity.keystrokes.keydown.push(newKeydown);
    dataQueue.push(newKeydown);
  });

  // Record all keyups inside the window
  window.addEventListener('keyup', e => {
    let newKeyup = {
      sess_id : localStorage.getItem('session-id'),
      activityType : "key_up",
      activityInfo: {
        key: e.key,
        code: e.code
      },
      altKey: e.altKey,
      ctrlKey: e.ctrlKey,
      shiftKey: e.shiftKey,
      timestamp: e.timeStamp
    };
    data.activity.keystrokes.keyup.push(newKeyup);
    dataQueue.push(newKeyup);
  });
}

function sendInfoToREST(){
  while(dataQueue.length){
    var dataToSend = dataQueue.shift();
    let blob = new Blob([JSON.stringify(dataToSend)], headers);
    while(!navigator.sendBeacon(activity_url, blob)){}
  }
  setTimeout(sendInfoToREST, 500);
}

/**
 * The "initialize" function here begins the collector program by calling all
 * of the necessary methods. Organizing the code this way makes sure that
 * nothing runs before it is ready to run.
 */
function init() {
  collectStaticData();
  collectPerformanceData();
  bindActivityEvents();
  sendInfoToREST();
}

// The initilize function will run once the DOM has been parsed which gives
// some time for things to load
window.addEventListener('DOMContentLoaded', init);
