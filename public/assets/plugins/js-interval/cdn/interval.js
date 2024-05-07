class Interval {
  constructor(data) {
    this.counter = data.counter;

    this.delay = data.delay;

    this.duration = data.duration;

    this.pausedTime = null;

    this.endTime = null;

    this.time = null;

    if (typeof data.onStart === "function") {
      this.onStart = data.onStart;
    } else {
      this.onStart = function () {};
    }

    if (typeof data.onEnd === "function") {
      this.onEnd = data.onEnd;
    } else {
      this.onEnd = function () {};
    }

    if (typeof data.onPaused === "function") {
      this.onPaused = data.onPaused;
    } else {
      this.onPaused = function () {};
    }

    if (typeof data.onReset === "function") {
      this.onReset = data.onReset;
    } else {
      this.onReset = function () {};
    }

    if (typeof data.onRestart === "function") {
      this.onRestart = data.onRestart;
    } else {
      this.onRestart = function () {};
    }
  }

  timer(thisClass) {
    let currentTime = null;

    let endTime = thisClass.endTime + thisClass.delay; // To avoid the 1 second error.

    if (thisClass.pausedTime == null) {
      currentTime = new Date(); // Get the current time.
    } else {
      currentTime = Date.parse(thisClass.pausedTime); // Get the paused time

      thisClass.endTime = Math.abs(
        thisClass.endTime + Date.parse(new Date()) - currentTime
      );

      thisClass.pausedTime = null;
    }

    if (endTime > currentTime) {
      let timeleft = Math.abs(endTime - currentTime) / 1000;

      let result = parseInt(timeleft / 60) + ":" + parseInt(timeleft % 60); //formart seconds into 00:00

      // console.log(result);
      document.querySelector(thisClass.counter).innerHTML = result;
    } else {
      clearInterval(thisClass.time);

      thisClass.onEnd(); // Method to run when interval ended.
    }
  }

  start() {
    this.endTime = Math.abs(Date.parse(new Date()) + this.duration * 60 * 1000);

    this.time = setInterval(this.timer, this.delay, this);

    this.onStart(); // Method to run when interval started.
  }

  pause() {
    clearInterval(this.time);

    this.pausedTime = new Date();

    this.onPaused(); // Method to run when interval stopped.
  }

  reset() {
    clearInterval(this.time);

    this.time = null;

    this.endTime = Math.abs(Date.parse(new Date()) + 1000);

    this.time = setInterval(this.timer, this.delay, this);

    this.onReset(); // Method to run when interval reset.
  }

  restart() {
    clearInterval(this.time);

    this.time = null;

    this.endTime = Math.abs(Date.parse(new Date()) + this.duration * 60 * 1000);

    this.time = setInterval(this.timer, this.delay, this);

    this.onRestart(); // Method to run when interval restarted.
  }
}
