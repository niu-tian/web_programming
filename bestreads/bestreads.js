(function() {
  "use strict";

  const BASE_URL = "http://localhost:8888/hw6/bestreads.php";

  window.addEventListener("load", init);

  function init() {
    displayAll();
  }

  function displayAll() {
    id("allbooks").style.display = "block";
    id("singlebook").style.display = "none";
    let url = BASE_URL + "?mode=books";
    fetch(url, {mode: "cors"})
      .then(checkStatus)
      .then(displayAllSuccess)
      .then(chooseBook)
      .catch(console.log);
  }

  function displayAllSuccess(response) {
    let parser = new DOMParser();
    let xmlDoc = parser.parseFromString(response, "text/xml");
    let title = xmlDoc.getElementsByTagName("title");
    let folder = xmlDoc.getElementsByTagName("folder");
    for (let i = 0; i < title.length; i++) {
      let bookname = document.createElement("p");
      bookname.innerText = title[i].textContent;
      bookname.value = folder[i].textContent;
      bookname.classList.add("bookname");
      let bookcover = document.createElement("img");
      bookcover.src = "./books/" + folder[i].textContent + "/cover.jpg";
      bookcover.alt = "book cover";
      bookcover.value = folder[i].textContent;
      bookcover.classList.add("bookcover");
      let cover_name = document.createElement("div");
      cover_name.appendChild(bookcover);
      cover_name.appendChild(bookname);
      id("allbooks").appendChild(cover_name);
    }
  }

  function chooseBook() {
    id("back").addEventListener("click", displayAll);
    let covers = qsa(".bookcover");
    for (let i = 0; i < covers.length; i++) {
      covers[i].addEventListener("click", displayBook);
    }
    let names = qsa(".bookname");
    for (let i = 0; i < names.length; i++) {
      names[i].addEventListener("click", displayBook);
    }
  }

  function displayBook() {
    clearAllBooks();
    id("allbooks").style.display = "none";
    id("singlebook").style.display = "block";
    let bookname = this.value;
    id("cover").src = "./books/" + bookname + "/cover.jpg";
    fetchInfo(bookname);
    fetchDesc(bookname);
    fetchReviews(bookname);
  }

  function fetchInfo(bookname) {
    let url = BASE_URL + "?mode=info&title=" + bookname;
    fetch(url, {mode: "cors"})
      .then(checkStatus)
      .then(JSON.parse)
      .then(handleInfo)
      .catch(console.log);
  }

  function handleInfo(response) {
    id("title").innerText = response["title"];
    id("author").innerText = response["author"];
    id("stars").innerText = response["stars"];
  }

  function fetchDesc(bookname) {
    let url = BASE_URL + "?mode=description&title=" + bookname;
    fetch(url, {mode: "cors"})
      .then(checkStatus)
      .then(handleDesc)
      .catch(console.log);
  }

  function handleDesc(response) {
    id("description").innerText = response;
  }

  function fetchReviews(bookname) {
    let url = BASE_URL + "?mode=reviews&title=" + bookname;
    fetch(url, {mode: "cors"})
      .then(checkStatus)
      .then(handleReviews)
      .catch(console.log);
  }

  function handleReviews(response) {
    id("reviews").innerHTML = response;
  }

  function clearAllBooks() {
    id("allbooks").innerHTML = "";
  }

  function checkStatus(response) {
    if (response.status >= 200 && response.status < 300 ||
        response.status == 0) {
      return response.text();
    } else {
      return Promise.reject(new Error(response.status + ": " +
                                      response.statusText));
    }
  }
  /* ------------------------ Helper Functions ---------------------------- */
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