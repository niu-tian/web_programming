(function() {
  window.addEventListener("load", init);

  const BASE_URL = "http://numbersapi.com/";

  function init() {
    let btn = qsa("button");
    for (let i = 0; i < btn.length; i++) {
      btn[i].addEventListener("click", handleClick);
    }
  }

  function handleClick() {
    let choice = this.id;
    let num = qs("input").value;
    if (choice == "fetch-random-num" || !num) {
      num = "random";
    }
    let url = BASE_URL + num;
    fetch(url, {mode: 'cors'})
      .then(checkStatus)
      .then(display)
      .catch(console.log);
  }

  function display(response) {
    id("output").innerText = response;
  }


  /* ------------------------------ helper functions ------------------------ */
  function id(query) {
    return document.getElementById(query);
  }

  function qs(query) {
    return document.querySelector(query);
  }

  function qsa(query) {
    return document.querySelectorAll(query);
  }

  function checkStatus(response) {
    if (response.status >= 200 && response.status < 300 || response.status == 0) {
      return response.text();
    } else {
      return Promise.reject(new Error(response.status + ": " + response.statusText));
    }
  }

})();