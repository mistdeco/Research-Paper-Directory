/* ============================================================
   FILE: add.php & edit.php
   FUNCTION: Add Authors
   ============================================================ */
(function () {
  var wrap = document.getElementById("authorsWrap");
  var add = document.getElementById("addAuthor");
  
  // Guard clause for edit.php logic
  if (!add) return;

  function makeRow(value) {
    var row = document.createElement("div");
    row.className = "authorRow";

    var input = document.createElement("input");
    input.className = "input";
    input.type = "text";
    input.name = "authors[]";
    input.value = value || "";

    var del = document.createElement("button");
    del.className = "btn";
    del.type = "button";
    del.textContent = "-";
    del.addEventListener("click", function () {
      row.parentNode.removeChild(row);
    });

    row.appendChild(input);
    row.appendChild(del);
    return row;
  }

  add.addEventListener("click", function () {
    wrap.appendChild(makeRow(""));
  });
})();


/* ============================================================
   FILE: paperdetails.php
   FUNCTION: Citation Clipboard
   ============================================================ */
(function () {
  function copyFrom(id) {
    var el = document.getElementById(id);
    if (!el) return;
    el.focus();
    el.select();
    try { 
      document.execCommand("copy"); 
    } catch (e) {
      console.error("Copying failed", e);
    }
  }

  var a = document.getElementById("copyApa");
  var m = document.getElementById("copyMla");

  if (a) a.addEventListener("click", function () { copyFrom("apaText"); });
  if (m) m.addEventListener("click", function () { copyFrom("mlaText"); });
})();


/* ============================================================
   FILE: index.php & adminindex.php
   FUNCTION: Search Suggestion
   ============================================================ */
(function () {
    const searchInput = document.getElementById("query");
    const suggestBox = document.getElementById("searchSuggestions");

    if (!searchInput || !suggestBox) return;

    searchInput.addEventListener("input", function () {
        const val = this.value.trim();

        if (val.length < 1) {
            suggestBox.innerHTML = "";
            return;
        }

        // Detect admin or public page
        const path = window.location.pathname.includes("admin")
            ? "suggest.php?term="
            : "includes/suggest.php?term=";

        fetch(path + encodeURIComponent(val))
            .then(res => res.json())
            .then(data => {
                suggestBox.innerHTML = "";

                if (!Array.isArray(data) || data.length === 0) return;

                data.forEach(text => {
                    const div = document.createElement("div");
                    div.className = "suggestion-item";

                    const regex = new RegExp(`(${val})`, "gi");
                    div.innerHTML = text.replace(regex, "<strong>$1</strong>");

                    div.addEventListener("click", () => {
                        searchInput.value = text;
                        suggestBox.innerHTML = "";
                        searchInput.form.submit();
                    });

                    suggestBox.appendChild(div);
                });
            })
            .catch(err => console.error("Suggestion error:", err));
    });

    document.addEventListener("click", e => {
        if (!suggestBox.contains(e.target) && e.target !== searchInput) {
            suggestBox.innerHTML = "";
        }
    });
})();
