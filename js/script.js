/* ============================================================
   FILE: add.php & edit.php
   FUNCTION: Add Authors (with value memory)
   ============================================================ */
(function () {
  const countInput = document.getElementById("authorCount");
  const wrap = document.getElementById("authorsWrap");

  if (!countInput || !wrap) return;

  // MEMORY for author values (persists even if inputs are removed)
  const authorCache = [];

  function createAuthorRow(index) {
    const row = document.createElement("div");
    row.className = "authorRow";

    const input = document.createElement("input");
    input.className = "input";
    input.type = "text";
    input.name = "authors[]";
    input.placeholder = "Author " + (index + 1);
    input.required = true;

    // restore cached value if exists
    if (authorCache[index]) {
      input.value = authorCache[index];
    }

    // keep cache updated while typing
    input.addEventListener("input", function () {
      authorCache[index] = this.value;
    });

    row.appendChild(input);
    return row;
  }

  function syncAuthors(count) {
    const current = wrap.children.length;

    // ADD fields
    for (let i = current; i < count; i++) {
      wrap.appendChild(createAuthorRow(i));
    }

    // REMOVE fields (DOM only, cache is kept)
    for (let i = current; i > count; i--) {
      wrap.removeChild(wrap.lastElementChild);
    }
  }

  // INITIAL LOAD (important for edit.php)
  [...wrap.querySelectorAll("input")].forEach((input, i) => {
    authorCache[i] = input.value;
    input.addEventListener("input", function () {
      authorCache[i] = this.value;
    });
  });

  syncAuthors(parseInt(countInput.value) || 1);

  countInput.addEventListener("input", function () {
    const count = Math.max(1, parseInt(this.value) || 1);
    syncAuthors(count);
  });
})();




/* ============================================================
   FILE: paperdetails.php
   FUNCTION: Citation Clipboard
   ============================================================ */
(function () {
  function copyFrom(id, buttonEl) {
    var el = document.getElementById(id);
    if (!el) return;

    // Select the text
    el.select();
    el.setSelectionRange(0, 99999); // For mobile devices

    try {
      document.execCommand("copy");
      
      // Save the original text (e.g., "Copy APA")
      var originalText = buttonEl.textContent;
      
      // Change button state
      buttonEl.textContent = "Copied!";
      buttonEl.classList.add("copied-success");
      
      // Revert back after 2 seconds
      setTimeout(function() {
        buttonEl.textContent = originalText;
        buttonEl.classList.remove("copied-success");
      }, 2000);

    } catch (e) {
      console.error("Copying failed", e);
    }
  }

  var a = document.getElementById("copyApa");
  var m = document.getElementById("copyMla");

  if (a) {
    a.addEventListener("click", function () { 
      copyFrom("apaText", a); 
    });
  }
  
  if (m) {
    m.addEventListener("click", function () { 
      copyFrom("mlaText", m); 
    });
  }
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

/* ============================================================
   FILE: index.php & adminindex.php
   FUNCTION: Department Suggestion
   ============================================================ */
(function () {
    const deptInput = document.getElementById("department");
    const suggestBox = document.getElementById("departmentSuggestions");

    if (!deptInput || !suggestBox) return;

    deptInput.addEventListener("input", function () {
        const val = this.value.trim();

        if (val.length < 1) {
            suggestBox.innerHTML = "";
            return;
        }

        // Detect admin or public page
        const path = window.location.pathname.includes("admin")
            ? "suggest.php?term="
            : "includes/suggest.php?term=";

        fetch(path + encodeURIComponent(val) + "&field=department")
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
                        deptInput.value = text;
                        suggestBox.innerHTML = "";
                        deptInput.form.submit();
                    });

                    suggestBox.appendChild(div);
                });
            })
            .catch(err => console.error("Department suggestion error:", err));
    });

    document.addEventListener("click", e => {
        if (!suggestBox.contains(e.target) && e.target !== deptInput) {
            suggestBox.innerHTML = "";
        }
    });
})();

/* ============================================================
   FILE: edit.php
   FUNCTION: Unsaved Changes & Unchanged Update Warning
   ============================================================ */
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('editForm');
        const backBtn = document.getElementById('backBtn');
        
        const getFormString = () => new URLSearchParams(new FormData(form)).toString();
        const initialState = getFormString();

        form.addEventListener('submit', function(event) {
            const currentState = getFormString();
            
            if (initialState === currentState) {
                event.preventDefault();
                alert("No changes detected.");
            }
        });

        backBtn.addEventListener('click', function(event) {
            const currentState = getFormString();
            if (initialState !== currentState) {
                const confirmLeave = confirm("You have unsaved changes. Are you sure you want to go back?");
                if (!confirmLeave) {
                    event.preventDefault();
                }
            }
        });
    });

/* ============================================================
   FILE: paperdetails.php
   FUNCTION: Copy APA and MLA Citation
   ============================================================ */
    (function () {
          function copyFrom(id) {
            var el = document.getElementById(id);
            if (!el) return;
            el.focus();
            el.select();
            try { document.execCommand("copy"); } catch (e) {}
          }

          var a = document.getElementById("copyApa");
          var m = document.getElementById("copyMla");

          if (a) a.addEventListener("click", function () { copyFrom("apaText"); });
          if (m) m.addEventListener("click", function () { copyFrom("mlaText"); });
    })();

/* ============================================================
   FILE: add.php
   FUNCTION: Basic Add Paper Function
   ============================================================ */
    (function () {
      var wrap = document.getElementById("authorsWrap");
      var add = document.getElementById("addAuthor");

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