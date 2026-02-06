/* ============================================================
   FILE: add.php & edit.php
   FUNCTION: Add Authors
   ============================================================ */
(function () {
  const countInput = document.getElementById("authorCount");
  const wrap = document.getElementById("authorsWrap");

  if (!countInput || !wrap) return;

  countInput.addEventListener("input", function () {
    const count = parseInt(this.value) || 0;
    wrap.innerHTML = "";

    for (let i = 1; i <= count; i++) {
      const row = document.createElement("div");
      row.className = "authorRow";

      const input = document.createElement("input");
      input.className = "input";
      input.type = "text";
      input.name = "authors[]";
      input.placeholder = "Author " + i;
      input.required = true;

      row.appendChild(input);
      wrap.appendChild(row);
    }
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
