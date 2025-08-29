let table; // global
document.addEventListener("DOMContentLoaded", function(e) {
  config.colors.borderColor, config.colors.bodyBg, config.colors.headingColor;
  let t = document.querySelector(".datatables-category"),
    r = "app-user-view-account.html",
    n = {
      1: { title: "Active", class: "bg-label-success" },
      0: { title: "Inactive", class: "bg-label-secondary" },
    },
    a = $(".select2"),
    s;
  if (
    a.length
    && (s = a).wrap("<div class=\"position-relative\"></div>").select2({
      placeholder: "Select Country",
      dropdownParent: s.parent(),
    }), t
  ) {
    // clearing pipeline cache & update the data row instantly
    $.fn.dataTable.Api.register("clearPipeline()", function() {
    return this.iterator("table", function(settings) {
      settings.clearCache = true;
    });
  });
    // Caching few pages
    $.fn.dataTable.pipeline = function(opts) {
      var conf = $.extend({
        pages: 5, // number of pages to cache
        url: "", // script url
        data: null, // function or object with parameters to send
        method: "GET", // Ajax HTTP method
        headers: {},
      }, opts);

      var cacheLower = -1, cacheUpper = null, cacheLastRequest = null, cacheLastJson = null;

      return function(request, drawCallback, settings) {
        var ajax = false;
        var requestStart = request.start;
        var requestLength = request.length;
        var requestEnd = requestStart + requestLength;

        if (settings.clearCache) {
          ajax = true;
          settings.clearCache = false;
        } else if (cacheLower < 0 || requestStart < cacheLower || requestEnd > cacheUpper) {
          // request is outside cache → need new block
          ajax = true;
        } else if (
          JSON.stringify(request.order) !== JSON.stringify(cacheLastRequest.order)
          || JSON.stringify(request.columns) !== JSON.stringify(cacheLastRequest.columns)
          || JSON.stringify(request.search) !== JSON.stringify(cacheLastRequest.search)
        ) {
          // search/order changed → clear cache
          ajax = true;
        }

        cacheLastRequest = $.extend(true, {}, request);

        if (ajax) {
          // calculate new cache window starting at current page
          var newCacheStart = Math.floor(requestStart / requestLength) * requestLength;
          cacheLower = newCacheStart;
          cacheUpper = newCacheStart + (requestLength * conf.pages);

          request.start = cacheLower;
          request.length = requestLength * conf.pages; // fetch multiple pages at once

          if ($.isFunction(conf.data)) {
            var d = conf.data(request);
            if (d) $.extend(request, d);
          } else if ($.isPlainObject(conf.data)) {
            $.extend(request, conf.data);
          }

          return $.ajax({
            type: conf.method,
            url: conf.url,
            data: request,
            headers: conf.headers,
            dataType: "json",
            cache: false,
            success: function(json) {
              cacheLastJson = $.extend(true, {}, json);

              // return only the requested slice
              json.data = json.data.slice(requestStart - cacheLower, requestStart - cacheLower + requestLength);
              drawCallback(json);
            },
          });
        } else {
          // serve from cache
          var json = $.extend(true, {}, cacheLastJson);
          json.draw = request.draw;
          json.data = json.data.slice(requestStart - cacheLower, requestStart - cacheLower + requestLength);
          drawCallback(json);
        }
      };
    };

    table = new DataTable(t, {
      processing: true,
      serverSide: true,
      ajax: $.fn.dataTable.pipeline({
        url: "http://127.0.0.1:8000/api/getproduct",
        pages: 5, // cache 5 pages
        type: "Get",
        headers: {
          "Authorization": "Bearer " + apiToken,
          "X-API-Client": userCode,
        },
      }), // Api for gategory data
      columns: [
        { data: "id" },
        { data: "id", orderable: !1, render: DataTable.render.select() },
        { data: "name" },
        { data: "status" },
        { data: "action" },
      ],
      rowId: "id",
      columnDefs: [
        {
          className: "control",
          searchable: false,
          orderable: false,
          responsivePriority: 3,
          targets: 0,
          render: function() {
            return "";
          },
        },
        {
          targets: 1,
          orderable: false,
          searchable: false,
          responsivePriority: 4,
          checkboxes: {
            selectAllRender: "<input type=\"checkbox\" class=\"form-check-input\">",
          },
          render: function() {
            return "<input type=\"checkbox\" class=\"dt-checkboxes form-check-input\">";
          },
        },
        {
          targets: 2,
          responsivePriority: 1,
          render: function(data, type, row) {
            return `<span class="text-capitalized">${row.name}</span>`;
          },
        },
        {
          targets: 3,
          responsivePriority: 2,
          render: function(data, type, row) {
            let status = row.status;
            return `<span class="badge ${n[status]?.class || "bg-secondary"} text-capitalized">
                ${n[status]?.title || "Unknown"}
              </span>`;
          },
        },
        {
          targets: -1, // Last column
          title: "Actions",
          searchable: false,
          orderable: false,
          render: (data, type, row) => `
      <div class="d-flex align-items-center">
        <a href="javascript:;" class="btn btn-icon delete-record">
          <i class="icon-base bx bx-trash icon-md"></i>
        </a>
        <a href="javascript:;" class="btn btn-icon dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
          <i class="icon-base bx bx-dots-vertical-rounded icon-md"></i>
        </a>
        <div class="dropdown-menu dropdown-menu-end m-0">
          <a href="javascript:;" class="dropdown-item edit-record" data-id="${row.id}" data-name="${row.name}" data-status="${row.status}" data-bs-toggle="modal" data-bs-target="#editCategoryModal">
            Edit
          </a>
          <a href="javascript:;" class="dropdown-item">Suspend</a>
        </div>
      </div>
    `,
        },
      ],
      select: { style: "multi", selector: "td:nth-child(2)" },
      order: [[2, "asc"]],
      layout: {
        topStart: {
          rowClass: "row mx-3 my-0 justify-content-between",
          features: [{ pageLength: { menu: [10, 25, 50, 100], text: "_MENU_" } }],
        },
        topEnd: {
          features: [{ search: { placeholder: "Search Category", text: "_INPUT_" } }, {
            buttons: [{
              text:
                "<i class=\"icon-base bx bx-plus icon-sm me-0 me-sm-2\"></i><span class=\"d-none d-sm-inline-block\">Create New Category</span>",
              className: "add-new btn btn-primary",
              attr: { "data-bs-toggle": "offcanvas", "data-bs-target": "#offcanvasNewCategory" },
            }],
          }],
        },
        bottomStart: { rowClass: "row mx-3 justify-content-between", features: ["info"] },
        bottomEnd: { paging: { firstLast: !1 } },
      },
      language: {
        sLengthMenu: "_MENU_",
        search: "",
        searchPlaceholder: "Search Category",
        paginate: {
          next: "<i class=\"icon-base bx bx-chevron-right icon-18px\"></i>",
          previous: "<i class=\"icon-base bx bx-chevron-left icon-18px\"></i>",
        },
      },
      responsive: {
        details: {
          display: DataTable.Responsive.display.modal({
            header: function(e) {
              return "Details of " + e.data().full_name;
            },
          }),
          type: "column",
          renderer: function(e, t, a) {
            var s,
              n,
              o,
              a = a.map(function(e) {
                return "" !== e.title
                  ? `<tr data-dt-row="${e.rowIndex}" data-dt-column="${e.columnIndex}">
                      <td>${e.title}:</td>
                      <td>${e.data}</td>
                    </tr>`
                  : "";
              }).join("");
            return !!a
              && ((s = document.createElement("div")).classList.add("table-responsive"),
                n = document.createElement("table"),
                s.appendChild(n),
                n.classList.add("table"),
                (o = document.createElement("tbody")).innerHTML = a,
                n.appendChild(o),
                s);
          },
        },
      },
    });
    function o(e) {
      let t = document.querySelector(".dtr-expanded");
      (t = e ? e.target.parentElement.closest("tr") : t) && a.row(t).remove().draw();
    }
    function l() {
      var e = document.querySelector(".datatables-category");
      let t = document.querySelector(".dtr-bs-modal");
      e && e.classList.contains("collapsed")
        ? t && t.addEventListener("click", function(e) {
          e.target.parentElement.classList.contains("delete-record") && (o(), e = t.querySelector(".btn-close"))
            && e.click();
        })
        : (e = e?.querySelector("tbody")) && e.addEventListener("click", function(e) {
          e.target.parentElement.classList.contains("delete-record") && o(e);
        });
    }
    l(),
      document.addEventListener("show.bs.modal", function(e) {
        e.target.classList.contains("dtr-bs-modal") && l();
      }),
      document.addEventListener("hide.bs.modal", function(e) {
        e.target.classList.contains("dtr-bs-modal") && l();
      }),
      $(".dt-buttons > .btn-group > button").removeClass("btn-secondary");
  }
  setTimeout(() => {
    [
      { selector: ".dt-buttons .btn", classToRemove: "btn-secondary" },
      { selector: ".dt-search .form-control", classToRemove: "form-control-sm" },
      { selector: ".dt-length .form-select", classToRemove: "form-select-sm", classToAdd: "ms-0" },
      { selector: ".dt-length", classToAdd: "mb-md-6 mb-0" },
      { selector: ".dt-search", classToAdd: "mb-md-6 mb-2" },
      {
        selector: ".dt-layout-end",
        classToRemove: "justify-content-between",
        classToAdd: "d-flex gap-md-4 justify-content-md-between justify-content-center gap-4 flex-wrap mt-0",
      },
      { selector: ".dt-layout-start", classToAdd: "mt-0" },
      { selector: ".dt-buttons", classToAdd: "d-flex gap-4 mb-md-0 mb-6" },
      { selector: ".dt-layout-table", classToRemove: "row mt-2" },
      { selector: ".dt-layout-full", classToRemove: "col-md col-12", classToAdd: "table-responsive" },
    ].forEach(({ selector: e, classToRemove: a, classToAdd: s }) => {
      document.querySelectorAll(e).forEach(t => {
        a && a.split(" ").forEach(e => t.classList.remove(e)), s && s.split(" ").forEach(e => t.classList.add(e));
      });
    });
  }, 100);

  // *** Add new category
  $(document).ready(function() {
    $("#addCategoryForm").on("submit", function(e) {
      e.preventDefault();

      let name = $("#category-name").val();

      $.ajax({
        url: API_URL + "createcatgeory",
        type: "POST",
        headers: {
          "Authorization": "Bearer " + apiToken,
          "X-API-Client": userCode,
        },
        data: { "category-name": name },
        success: function(res) {
          if (res.status === "success") {
            // Show success message
            $("#message").html("<div class=\"alert alert-success\">" + res.message + "</div>");

            // Reset form
            $("#addCategoryForm")[0].reset();

            // Reload DataTable without resetting pagination, using clearPipeline & also updating cache
            table.clearPipeline().draw(false);
          }
        },
        error: function(xhr) {
          let res = xhr.responseJSON;
          $("#message").html("<div class=\"alert alert-danger\">" + res.message + "</div>");
        },
      });
    });
  });

  // *** Edit model
  $(document).on("click", ".edit-record", function() {
    let id = $(this).data("id");
    let name = $(this).data("name");
    let status = $(this).data("status");

    // Fill modal fields
    $("#edit-id").val(id);
    $("#edit-name").val(name);
    $("#edit-status").val(status);

    // Show modal
    $("#editCategoryModal").modal("show");
  });
  $("#editCategoryForm").on("submit", function(e) {
    e.preventDefault();

    let id = $("#edit-id").val();
    let name = $("#edit-name").val();
    let status = $("#edit-status").val();

    $.ajax({
      url: API_URL + `updatecategory/${id}`, // your update API endpoint
      method: "PUT",
      headers: {
        "Authorization": "Bearer " + apiToken,
        "X-API-Client": userCode,
        "Accept": "application/json",
      },
      data: { name, status },
      success: function(res) {
        $("#editCategoryModal").modal("hide");

        let updatedRow = res.data;
        let table = $(".datatables-category").DataTable();

        // Update the current row instantly
        let row = table.row("#" + updatedRow.id);
        if (row.length) {
          let newData = {
            id: updatedRow.id,
            name: updatedRow.name,
            status: updatedRow.status,
            action: `
            <div class="d-flex align-items-center">
              <a href="javascript:;" class="btn btn-icon delete-record">
                <i class="icon-base bx bx-trash icon-md"></i>
              </a>
              <a href="javascript:;" class="btn btn-icon dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                <i class="icon-base bx bx-dots-vertical-rounded icon-md"></i>
              </a>
              <div class="dropdown-menu dropdown-menu-end m-0">
                <a href="javascript:;" class="dropdown-item edit-record" 
                   data-id="${updatedRow.id}" 
                   data-name="${updatedRow.name}" 
                   data-status="${updatedRow.status}">
                  Edit
                </a>
                <a href="javascript:;" class="dropdown-item">Suspend</a>
              </div>
            </div>
          `,
          };

          row.data(newData).invalidate().draw(false); // update UI instantly
        }

        // Force pipeline to reload so future pages fetch fresh data
        table.clearPipeline().draw(false);
      },
      error: function(xhr) {
        alert("Update failed!");
      },
    });
  });
});
