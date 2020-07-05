(function() {
  "use strict";

  window.addEventListener("load", init);

  let timer = null;

  /**
   * Sets up the response functionality for Roll and Stop buttons.
   */
  function init() {
    id("roll").addEventListener("click", startGame);
    id("stop").addEventListener("click", endGame);
  }

  /**
  * Allow a user to start rolling after the dice type has been chosen and
  * dice count has been inputted.
  */
  function startGame() {
    let diceCount = parseInt(id("count").value);
    // check whether the input is valid, alert if not valid
    if (diceCount == null || diceCount < 0) {
      alert("Invalid input");
    } else {
      clearDice();
      id("results").classList.add("hidden");
      id("roll-sum").innerTex = "";
      createDice(diceCount);
      rollDice();
    }
  }

  /**
   * Remove the dices created by last rolling to prepare for a new round.
   */
  function clearDice() {
    let allDices = qsa(".die");
    for (let i = 0; i < allDices.length; i++) {
      allDices[i].remove();
    }
  }

  /**
   * Add dices of user-specified amount to the dice-area.
   * @param {int} diceCount - user-specified number of dices
   */
  function createDice(diceCount) {
    id("dice-area").classList.remove("hidden");
    for (let i = 0; i < diceCount; i++) {
      let dice = document.createElement("div");
      dice.classList.add("die");
      id("dice-area").appendChild(dice);
    }
  }

  /**
   * Create a timer and have it randomly change the top number of each dice
   * every 200ms.
   */
  function rollDice() {
    timer = setInterval(generateNum, 200);
  }

  /**
   * Assign a random number from 1 to n, where n is the number of sides of each
   * dice chosen by the user, to each dice.
   */
  function generateNum() {
    let diceType = parseInt(id("dice-sides").value);
    id("roll").disabled = true;
    id("stop").disabled = false;
    let allDices = qsa(".die");
    for (let i = 0; i < allDices.length; i++) {
      allDices[i].innerText = Math.floor(Math.random() * diceType) + 1;
    }
  }

  /**
   * When the user clicks "Stop" button, rolling process stops.
   * Disable Stop button and enable Roll button.
   * Clear the interval and set timer to null.
   * The number on each dice is summed and displayed below the dice area.
   */
  function endGame() {
    let allDices = qsa(".die");
    let sum = 0;
    for (let i = 0; i < allDices.length; i++) {
      sum += parseInt(allDices[i].innerText);
    }
    id("results").classList.remove("hidden");
    id("roll-sum").innerText = sum;
    id("roll").disabled = false;
    id("stop").disabled = true;
    clearInterval(timer);
    timer = null;
  }

  /* -------------------------- helper functions ---------------------------- */
  /**
   * Returns the DOM object associated with the specified element ID.
   * @param {String} query - element ID
   * @returns {Object} DOM object associated with id
   */
  function id(query) {
    return document.getElementById(query);
  }

  /**
   * Returns the array of DOM objects that match the given CSS selector
   * @param {String} query - CSS selector
   * @returns {Object[]} array of DOM objects matching the query
   */
  function qsa(query) {
    return document.querySelectorAll(query);
  }

  /**
   * Returns the first DOM object that matches the given CSS selector
   * @param {String} query - CSS selector
   * @returns {Object} the first DOM object matching the query
   */
  function qs(query) {
    return document.querySelector(query);
  }
})();