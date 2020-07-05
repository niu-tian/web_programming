(function() {
  "use strict";

  window.addEventListener("load", init);

  const TILE_NUM = 15;
  let emptyX; // the coordinate of x-axis of the empty block
  let emptyY; // the coordinate of y-axis of the empty block
  let empty; // the DOM object of the empty block
  let steps = 0; // record the steps the user made
  let startTime; // record the game start time
  let endTime; // record the game end time

  /**
   * setup the puzzlearea and listen to events happening on the page
   */
  function init() {
    setUpView();
    id("shufflebutton").addEventListener("click", shuffle);
    let tiles = qsa(".tile");
    for (let i = 0; i < TILE_NUM; i++) {
      tiles[i].addEventListener("click", move);
      tiles[i].addEventListener("mouseover", changeApp);
    }
  }

  /**
   * set up the puzzlearea with 15 pieces of tiles
   * record the coordinate of the empty tile
   */
  function setUpView() {
    clearPuzzleArea();
    let row = 0;
    let col = 0;
    for (let i = 0; i < TILE_NUM; i++) {
      let tile = document.createElement("div");
      tile.classList.add("tile");
      tile.id = "square_" + row + "_" + col;
      tile.innerText = i + 1;
      tile.style.backgroundPosition = (-col * 100) + "px " + (-row * 100) + "px";
      id("puzzlearea").appendChild(tile);
      tile.style.left = col * 100 + "px";
      tile.style.top = row * 100 + "px";
      col++;
      if (col == 4) {
        col = 0;
      }
      if (i % 4 == 3) {
        row++;
      }
    }
    empty = document.createElement("div");
    empty.classList.add("tile");
    empty.id = "square_3_3";
    empty.style.background = "none";
    empty.style.borderColor = "white";
    id("puzzlearea").appendChild(empty);
    empty.style.left = "300px";
    empty.style.top = "300px";
    emptyX = 300;
    emptyY = 300;
  }

  /**
   * shuffle the puzzle with algorithm given below
   */
  function shuffle() {
    clearOutput();
    for (let i = 0; i < 1000; i++) {
      let neig = [];
      if (emptyX > 0) {
        neig.push(id("square_" + emptyY / 100 + "_" + (emptyX / 100  - 1)));
      }
      if (emptyY > 0) {
        neig.push(id("square_" + (emptyY / 100 - 1) + "_" + emptyX / 100));
      }
      if (emptyX < 300) {
        neig.push(id("square_" + emptyY / 100 + "_" + (emptyX / 100  + 1)));
      }
      if (emptyY < 300) {
        neig.push(id("square_" + (emptyY / 100 + 1) + "_" + emptyX / 100));
      }
      let choice = Math.floor((Math.random() * neig.length));
      let tile = neig[choice];
      (move.bind(tile))();
    }
    clearTimer();
  }

  /**
   * make the move of the clicked tile if it is movable, then check if the puzzle
   * is solved.
   */
  function move() {
    clearOutput();
    let tmpX = parseInt(this.style.left);
    let tmpY = parseInt(this.style.top);
    if (movable(tmpX, tmpY)) {
      if (steps == 0) {
        startTime =new Date();
      }
      steps += 1;
      this.style.left = emptyX + "px";
      this.style.top = emptyY + "px";
      this.id = "square_" + emptyY / 100 + "_" + emptyX / 100;
      empty.style.left = tmpX + "px";
      empty.style.top = tmpY + "px";
      empty.id = "square_" + tmpY / 100 + "_" + tmpX / 100;
      emptyX = tmpX;
      emptyY = tmpY;
      check();
    }
  }

  /**
   * if the puzzle is solved, report the success, the moves made and time taken
   * to solve the puzzle.
   */
  function check() {
    if (solved()) {
      endTime = new Date();
      let endGame = document.createElement("div");
      let timeDiff = Math.round((endTime - startTime) / 1000);
      endGame.innerText = "Congratulations! You made " + steps + " move(s) in " + timeDiff + " second(s).";
      id("output").appendChild(endGame);
      clearTimer();
    }
  }

  /**
   * check whether the puzzle is solved
   */
  function solved() {
    let row = 0;
    let col = 0;
    for (let i = 0; i < TILE_NUM; i++) {
      let num = parseInt(id("square_" + row + "_" + col).innerText);
      if (num != i + 1) {
        return false;
      }
      col++;
      if (col == 4) {
        col = 0;
      }
      if (i % 4 == 3) {
        row++;
      }
    }
    return true;
  }

  /**
   * when mouseover, if the tile is movable, change its appearance
   */
  function changeApp() {
    let tmpX = parseInt(this.style.left);
    let tmpY = parseInt(this.style.top);
    if (movable(tmpX, tmpY)) {
      this.style.borderColor = "red";
      this.style.color = "red";
    }
    this.addEventListener("mouseleave", changeAppBack);
  }

  /**
   * change the apprearance back to normal when mouseleave
   */
  function changeAppBack() {
    this.style.borderColor = "black";
    this.style.color = "black";
  }

  /**
   * check it the given tile is movable
   * @param {int} tmpX - the x coordinate of the given tile
   * @param {int} tmpY - the y coordinate of the given tile
   * @returns {boolean} - if the given tile is movable, return yes; otherwise, no
   */
  function movable(tmpX, tmpY) {
    if ((Math.abs(tmpX - emptyX) == 100 && Math.abs(tmpY - emptyY) == 0) ||
        (Math.abs(tmpX - emptyX) == 0 && Math.abs(tmpY - emptyY) == 100)) {
      return true;
    } else {
      return false;
    }
  }

  /**
   * clear the puzzle area when the page loads or refresh
   */
  function clearPuzzleArea() {
    clearOutput();
    let tiles = id("puzzlearea");
    while(tiles.firstChild) {
      tiles.removeChild(tiles.firstChild);
    }
  }

  /**
   * clear the output message given by last success of game
   */
  function clearOutput() {
    let message = id("output");
    while (message.firstChild) {
      message.removeChild(message.firstChild);
    }
  }

  /**
   *  clear the steps and timer
   */
  function clearTimer() {
    steps = 0;
    startTime = null;
    endTime = null;
  }

  /* ----------------------------- Helper Functions ------------------------ */
  function id(query) {
    return document.getElementById(query);
  }

  function qs(query) {
    return document.querySelector(query);
  }

  function qsa(query) {
    return document.querySelectorAll(query);
  }
})();