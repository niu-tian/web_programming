(function(){
"use strict";

  window.addEventListener("load", init);

  let timer = null;
  let text = null;
  let index = 0;
  const punctuations = [".", ",", ":", "!", "?", ";"];

  /**
   * Sets up the response functionality for Start and Stop buttons, and also
   * for size and speed choices.
   */
  function init() {
    id("start").addEventListener("click", startGame);
    id("stop").addEventListener("click", endGame);
    let fontSize = qsa("input[name='size']");
    for (let i = 0; i < fontSize.length; i++) {
      fontSize[i].addEventListener("change", changeSize);
    }
    let speedChoice = id("speed");
    speedChoice.addEventListener("change", changeSpeed);
  }

  /**
   * Disable "start" button and input textarea while enable the "stop" button.
   * Process the input text as an array splited by white space.
   * If the element in the array ends with a punctuation, remove the last punctuation
   * and add the processed element to the array at that index again.
   * Set an interval to display the elements array one at each time.
   */
  function startGame() {
    id("start").disabled = true;
    id("stop").disabled = false;
    qs("textarea").disabled = true;
    text = qs("textarea").value.split(/[ \t\n]+/);
    let i = 0;
    while (i < text.length) {
      if (endWithPunc(text[i])) {
        text[i] = text[i].substr(0, text[i].length - 1);
        text.splice(i, 0, text[i]);
        i++;
      }
      i++;
    }
    let speed = parseInt(id("speed").value);
    timer = setInterval(handleText, speed);
  }

  /**
   * Display each word of input text according to its order.
   * End the game if all words in the input text has been displayed.
   */
  function handleText() {
    if (index < text.length) {
      id("output").innerText = "";
      let curWord = text[index];
      id("output").innerText = curWord;
      index++;
    } else {
      endGame();
    }
  }

  /**
   * Returns true if the given word ends with a punctuation
   * @param {String} word - the word that needs to be checked
   * @returns {Boolean} - whether the word ends with a punctuation
   */
  function endWithPunc(word) {
    return punctuations.indexOf(word[word.length - 1]) > -1;
  }


  /**
   * Clear the animation area and set timer to null when the user decides to stop
   * the game or when the animation finishes. Disable "stop" button and enable
   * "start" button in preparation for a new game.
   */
  function endGame() {
    clearInterval(timer);
    timer = null;
    id("start").disabled = false;
    id("stop").disabled = true;
    text = null;
    index = 0;
    qs("textarea").disabled = false;
    id("output").innerText = "";
  }

  /**
   * Change the font size of the text according to user's choice
   */
  function changeSize() {
    let size = this.value;
    if (size == "medium") {
      id("output").style.fontSize ="36pt";
    } else if (size == "big") {
      id("output").style.fontSize ="48pt";
    } else {
      id("output").style.fontSize ="60pt";
    }
  }

  /**
   * Change the speed of animation during the playing when a different speed is
   * selected.
   */
  function changeSpeed() {
    if (id("start").disabled) {
      clearInterval(timer);
      let speed = parseInt(id("speed").value);
      timer = setInterval(handleText, speed);
    }
  }

  /** --------------------------- Helper Functions ------------------------ */
  /**
   * Returns the DOM object associated with the specified element ID.
   * @param {String} query - element ID
   * @returns {Object} DOM object associated with id
   */
  function id(query) {
    return document.getElementById(query);
  }

  /**
   * Returns the first DOM object that matches the given CSS selector
   * @param {String} query - CSS selector
   * @returns {Object} the first DOM object matching the query
   */
  function qs(query) {
    return document.querySelector(query);
  }

  /**
   * Returns the array of DOM objects that match the given CSS selector
   * @param {String} query - CSS selector
   * @returns {Object[]} array of DOM objects matching the query
   */
  function qsa(query) {
    return document.querySelectorAll(query);
  }
})();