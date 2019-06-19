(function ($) {
    var wmBags = {};

    wmBags.pluginPageUrl = $("#pluginPageUrl").data("pluginPageUrl");
    wmBags.adminAjaxUrl = $("#adminAjaxUrl").data("admixAjaxUrl");

    wmBags.acceptPage = ['formList', 'formNew', 'formUpdate',
        'popupList','popupNew','popupUpdate',
        'ticketList','ticketNew','ticketUpdate'];

    wmBags.getPageActive = (page) => {
        var $allPageWraps = $(document).find('.wmAdminWrap');
        var active = false;
        page = typeof page == "string" ? page : false;
        if (page) {
            $allPageWraps.each(function (i, wrap) {
                var $wrap = $(wrap);
                var pageCheck = $wrap.data('pageActive');
                if (page != pageCheck) {
                    $wrap.removeClass('wmActive');
                } else {
                    $wrap.addClass('wmActive');
                }
            });
            return page;
        }

        /*$allPageWraps.each(function (i, wrap) {
            var done = i == $allPageWraps.length - 1;
            var $wrap = $(wrap);
            var acceptPage = wmBags.acceptPage;
            var pageActive = $wrap.data('pageActive');
            if ($wrap.hasClass('wmActive')) {
                $wrap.show();
                active = typeof pageActive == "string" && acceptPage.includes(pageActive) ? pageActive : false;
            } else {
                $wrap.hide();
            }
        });
        return active;*/

        var $adminWrap = $(document).find('.wmAdminWrap');
        if ($adminWrap) {
            var acceptPage = wmBags.acceptPage;
            var pageActive = $adminWrap.data('pageActive');
            pageActive = typeof pageActive == "string" && acceptPage.includes(pageActive) ? pageActive : false;
            return pageActive;
        } else {
            alert("Không tìm thấy trang này");
            return false;
        }
    }

    wmBags.loadDataOnPage = () => {
        var page = wmBags.getPageActive();
        var accept = wmBags.acceptPage;
        page = typeof page == "string" && accept.includes(page) ? page : false;
        if (!page) {
            alert("Không thấy page này");
        } else {
            var fn = page + 'Page';
            wmBags[fn]();
        }
        return false;
    }

    wmBags.ticketListPage = () => {
        var ticketLib = {},
            $detailLeadPopup = $('#detailLeadPopup');
        ticketLib.whenTicketTableDraw = (table) => {
            var $table = $(table),
                $checkall = $table.find('thead th.column-cb.check-column input'),
                $checkOneItems = $table.find('tbody td.check-column input'),
                $body = $table.find('tbody'),
                $trs = $body.find("tr");

            // Setup check all for this table
            $checkall.off('click');
            $checkall.on('click', function (e) {
                var isCheckAll = $checkall.prop('checked');
                $checkOneItems.each(function (i, input) {
                    var $check = $(input),
                        {checked} = input,
                        $tr = $check.closest('tr');

                    if (isCheckAll) {
                        if (!checked && !$tr.hasClass('selected')) {
                            $check.click();
                        }
                    } else {
                        $check.click();
                    }
                });
            })

            // Setup tooltips for this table
            $ticketTable.find('[data-toggle="tooltip"]').tooltip({ boundary: 'window' });

            $trs.each(function (i, tr) {
                var $tr = $(tr),
                    rowData = $ticketDTable.row(i).data(),
                    $toCsNnow = $tr.find('.toCareSoftNow'),
                    $showButtons = $tr.find('.viewDetailTicket');

                $showButtons.each(function (i, button) {
                    ticketLib.viewDetailTicket(button);
                });

                $toCsNnow.off('click')
                $toCsNnow.on('click', function (e) {
                    e.preventDefault(e);
                    var {ticket_id} = rowData;
                    wmBags.jsonTransPortData({type: 'post', data: {action: 'ticketToCareSoftNow', ticket_id: ticket_id}}, (err, res) => {
                        var {success, msg} = typeof res == "object" ? res : {};
                        if (!err && success) {
                            $ticketDTable.draw();
                        } else {
                            alert("Không thể tạo mới ticket caresoft : " + msg);
                        }
                    });
                });
            });

            return false;
        };
        ticketLib.viewDetailTicket = (showButton) => {
            var $showButton = $(showButton);
            $showButton.off('click');
            $showButton.on('click', function (e) {
                e.preventDefault(e);
                var $modalSnapShot = $detailLeadPopup.clone().attr("id",""),
                    $button = $(this),
                    $tr = $button.closest("tr"),
                    rowData = $ticketDTable.row($tr).data(),
                    {name, phone, email,note, created_at, detail} = typeof rowData == "object" ? rowData : {},
                    {href, postTitle} = detail;


                email = email ? email : "";
                note = note ? note : "";
                created_at = created_at ? wmBags.dateTime(created_at).format("L") : "" ;

                $modalSnapShot.on('show.bs.modal', function () {
                    $modalSnapShot.find('.ticketName').text(name);
                    $modalSnapShot.find('.ticketPhone').text(phone);
                    $modalSnapShot.find('.ticketEmail').text(email);
                    $modalSnapShot.find('.ticketNote').text(note);
                    $modalSnapShot.find('.ticketCreated').text(created_at);
                    $modalSnapShot.find('.ticketHrefUrl').attr("href", href);
                    $modalSnapShot.find('.ticketPostTitle').text(postTitle);
                })

                $modalSnapShot.on('hidden.bs.modal', function () {
                    $(this).remove();
                })

                $modalSnapShot.modal('show');
            });
        };
        ticketLib.ticketsFilter = (table) => {
            // Setup filter
            var $table = $(table),
                $filterWrapper = $cardLeadTables.find("#filters_ticket_wrapper");

            // Filter by form
            var $ticketFilterByForm = $filterWrapper.find("#ticketFilterByForm"),
                // formFiterItems = `<option value="">---- Tất Cả Form ----</option>`,
                formFiterItems = ``;
            wmBags.jsonTransPortData({type: 'get',data: {action: "listForm"}}, function (err, res) {
                var {success, data} = typeof res == "object" ? res : {};
                if (!err && success && data) {
                    data.forEach(function (formItem) {
                        var {form_id, name, title} = formItem;
                        formFiterItems += `<option value="${form_id}">${title} - ${name}</option>`;
                    });
                    $ticketFilterByForm.html(formFiterItems);
                    $ticketFilterByForm.selectpicker({
                        selectedTextFormat: 'count > 3',
                        showTick: true,
                        style: 'btn-light',
                        selectAllText: "<span class='text-success'>Tất Cả</span>",
                        deselectAllText: "<span class='text-danger'>Hủy Chọn</span>",
                        noneSelectedText: "Tất Cả",
                        showTick: true,
                        actionsBox :true
                    });
                    $ticketFilterByForm.on('change',function (e) {
                        e.preventDefault();
                        $ticketDTable.draw();
                    });
                } else {
                    console.log("Can't load form filter");
                }
            });

            // Fiter by Caresoft Status
            var careSoftFiterItems = ``,
                $ticketFilterByCareSoft = $filterWrapper.find("#ticketFilterByCareSoftStt");
            careSoftFiterItems += `<option value="">--Care Soft--</option>
                                    <option value="yes">Đã Tạo Ticket</option>
                                    <option value="no">Chưa Có Thông Tin</option>`;
            $ticketFilterByCareSoft.html(careSoftFiterItems);
            $ticketFilterByCareSoft.selectpicker({
                selectedTextFormat: 'count > 3',
                showTick: true,
                style: 'btn-light',
                selectAllText: "<span class='text-success'>Tất Cả</span>",
                deselectAllText: "<span class='text-danger'>Hủy Chọn</span>",
                noneSelectedText: "Tất Cả",
                // showTick: true,
                // actionsBox :true
            });
            $ticketFilterByCareSoft.on('change',function (e) {
                e.preventDefault();
                $ticketDTable.draw();
            });


            // Filter by created_at
            var $createdFilters = $filterWrapper.find("#ticketFilterByDateRange");
            var start = moment().subtract(29, 'days'),
                end = moment();
            function cb(start, end) {
                var startQuery = start._d.toISOString(),
                    endQuery = end._d.toISOString();
                // $('#chartFilterDateRange').parent().find("#dates").val(getDates(start._d, end._d));
                $createdFilters.parent().find("#ticketCreatedStartDate").val(startQuery);
                $createdFilters.parent().find("#ticketCreatedEndDate").val(endQuery);
                $createdFilters.find('span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
                $ticketDTable.draw();
            }
            $createdFilters.daterangepicker({
                startDate: start,
                endDate: end,
                ranges: {
                    'Hôm nay': [moment(), moment()],
                    'Hôm Qua': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    '7 Ngày qua': [moment().subtract(6, 'days'), moment()],
                    '30 Ngày qua': [moment().subtract(29, 'days'), moment()],
                    'Tháng này': [moment().startOf('month'), moment().endOf('month')],
                    'Tháng Trước': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                }
            }, cb);
            cb(start, end);
        }
        ticketLib.chartTicketsFilter = () => {
            var $filterWrapper = $('.cardTicketsChart').find("#filters_chart_ticket_wrapper");

            // Filter by form
            var $ticketFilterByForm = $filterWrapper.find("#chartFilterByForm"),
                // formFiterItems = `<option value="">---- Tất Cả Form ----</option>`,
                formFiterItems = ``;
            wmBags.jsonTransPortData({type: 'get',data: {action: "listForm"}}, function (err, res) {
                var {success, data} = typeof res == "object" ? res : {};
                if (!err && success && data) {
                    data.forEach(function (formItem) {
                        var {form_id, name, title} = formItem;
                        formFiterItems += `<option value="${form_id}">${title} - ${name}</option>`;
                    });
                    $ticketFilterByForm.html(formFiterItems);
                    $ticketFilterByForm.selectpicker({
                        selectedTextFormat: 'count > 3',
                        showTick: true,
                        style: 'btn-light',
                        selectAllText: "<span class='text-success'>Tất Cả</span>",
                        deselectAllText: "<span class='text-danger'>Hủy Chọn</span>",
                        noneSelectedText: "Tất Cả",
                        showTick: true,
                        actionsBox :true
                    });
                    $ticketFilterByForm.on('change',function (e) {
                        e.preventDefault();
                    });
                } else {
                    console.log("Can't load form filter");
                }
            });


            // Filter daterange
            var thisMonth = wmBags.getDaysInMonth(new Date().getMonth(), new Date().getFullYear());
            var start = moment().subtract(29, 'days'),
                end = moment();
            function cb(start, end) {
                var startQuery = start._d.toISOString(),
                    endQuery = end._d.toISOString();
                // $('#chartFilterDateRange').parent().find("#dates").val(getDates(start._d, end._d));
                $('#chartFilterDateRange').parent().find("#startdate").val(startQuery);
                $('#chartFilterDateRange').parent().find("#enddate").val(endQuery);
                $('#chartFilterDateRange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
            }
            $('#chartFilterDateRange').daterangepicker({
                startDate: start,
                endDate: end,
                ranges: {
                    'Hôm nay': [moment(), moment()],
                    'Hôm Qua': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    '7 Ngày qua': [moment().subtract(6, 'days'), moment()],
                    '30 Ngày qua': [moment().subtract(29, 'days'), moment()],
                    'Tháng này': [moment().startOf('month'), moment().endOf('month')],
                    'Tháng Trước': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                }
            }, cb);
            cb(start, end);

            $filterWrapper.off("submit");
            $filterWrapper.on("submit", function (e) {
                e.preventDefault(e);
                ticketLib.chartTickets().draw();
            });
        }
        ticketLib.chartTickets = () => {
            var c = {},
                target = document.querySelector("#ticketChart"),
                $target = $(target);
            c.data = () => {
                var chartFilterData = {};
                $("#filters_chart_ticket_wrapper").find(":input").each(function (i, field) {
                    var {name, type, value} = field;
                    if (["submit","button"].includes(type)) return ;
                    if (name && value) {
                        chartFilterData[name] = $(field).val();
                    }
                });
                var {startdate, enddate} = chartFilterData;
                var dates = getDates(startdate, enddate);
                chartFilterData.dates = dates;
                return chartFilterData;
            }
            c.setup = () => {
                ticketLib.chartTicketsFilter();
            }
            c.start = () => {
                c.setup();
                var ajaxData = c.data(),
                    {dates} = ajaxData;
                ajaxData.action = 'ticketCharts';
                // ajaxData.dates = categories;
                var options = {
                    chart: {
                        height: 350,
                        type: 'area',
                    },
                    dataLabels: {
                        enabled: false
                    },
                    stroke: {
                        width: 7,
                        curve: 'smooth'
                    },
                    tooltip: {
                        x: {
                            format: 'dd/MM/yy'
                        },
                    },
                    series: [{
                        // name: 'Tickets',
                        data: []
                    }],
                    xaxis: {
                        // categories: dates,
                        type: 'datetime',
                    },
                    yaxis: {
                        opposite: true,
                        title: {
                            text: 'Tickets',
                        },
                    },
                    markers: {
                        size: 4,
                        opacity: 0.9,
                        colors: ["#FFA41B"],
                        strokeColor: "#fff",
                        strokeWidth: 2,
                        hover: {
                            size: 7,
                        }
                    }
                };
                var chart = new ApexCharts(target,options);
                chart.render();
                target.chart = chart;
                // Start Charts
                wmBags.jsonTransPortData({type:"get",data: ajaxData}, function (err, res) {
                    var {success, data, type} = typeof res == "object" ? res : {};
                    if (!err && success && data) {
                        chart.updateOptions({
                            series: data
                        });
                    }
                });
            }
            c.draw = () => {
                var newData = c.data(),
                    chart = target.chart;
                newData.action = 'ticketCharts';
                // example of series in another format
                wmBags.jsonTransPortData({type: "get",data: newData}, function (err, res) {
                    var {success, data} = typeof res == "object" ? res : {};
                    if (!err && success && data) {
                        // example of series in another format
                        chart.updateOptions({
                            series: data
                        });
                    } else {
                        console.log("Khong the cap nhat bang");
                    }
                })
            }
            return c;
        }

        // Ticket data table
        var $ticketTable = $('#ticketDTable'),
            $cardLeadTables = $('.cardLeadTables'),
            urlSourceData =  `${wmBags.adminAjaxUrl}`,
            $ticketDTable = $ticketTable.DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                select: {
                    style: 'multi',
                    selector: 'td:first-child input'
                },
                order: [
                    [4, 'desc']
                ],
                dom: 'Blfrtip',
                buttons: [
                    'copyHtml5',
                    'excelHtml5',
                    'csvHtml5',
                    'pdfHtml5'
                ],
                paging: true,
                pageLength: 10,
                pagingType: "full_numbers",
                columnDefs: [
                    {
                        targets: [0,1,2,3,4,5],
                        className: 'manage-column',
                    },
                    {
                        targets: [0,1,2,5],
                        orderable: false,
                    },
                    {
                        targets: [0,2,3,4,5],
                        className: 'text-center',
                    },
                    {
                        targets: [0],
                        className: "text-center manage-column column-cb check-column",
                        id: "cb",
                        width : "40px"
                    },
                    {
                        targets: [1],
                        width : "25%"
                    }
                ],
                ajax: {
                    url : urlSourceData,
                    data: function (d) {
                        d.action =  "ticketsDataTable",
                            d.form_id = $("#ticketFilterByForm").val(),
                            d.caresoft_ticket = $("#ticketFilterByCareSoftStt").val(),
                            d.startdate = $("#ticketCreatedStartDate").val(),
                            d.enddate = $("#ticketCreatedEndDate").val()
                    }
                },
                columns: [
                    {
                        title: `<!--<label class="screen-reader-text" for="cb-select-all">Chọn toàn bộ</label>-->
                        <input id="cb-select-all" type="checkbox">`,
                        name: "ticket_id", data: "ticket_id", class: 'check-column text-center',render: (data, type, row) => {
                            var ticketId = data ,
                                checkHtml = `<label class="screen-reader-text" for="cb-select-${ticketId}">Chọn </label>
                                <input id="cb-select-${ticketId}" type="checkbox" name="tickets[]" value="${ticketId}">`;
                            return checkHtml;
                        }
                    },
                    {
                        title: `<div class="font-weight-bold">
                            <span class="dashicons dashicons-id"></span>  Tên Khách Hàng</div>`,
                        name: "name", data: "name", render: function(data, type, row) {
                            var name = data,
                                nameHtml = `<h6 class="">
                                    <a href="#" class="viewDetailTicket text-brand-blue"><span>${name}</span></a>
                                </h6>`;
                            return nameHtml;
                        }
                    },
                    {
                        title: `<div class="font-weight-bold text-left">
                            <span class="dashicons dashicons-phone"></span>  Số Điện Thoại</div>`,
                        width: 100,
                        name: "phone", data: "phone", render: function (data, type, row) {
                            var phone = data,
                                phoneHtml = `<a href="#" class="viewDetailTicket text-brand-blue"><span>${phone}</span></a>`;
                            return phoneHtml;
                        }
                    },
                    {
                        title: `<div class="font-weight-bold">Tình Trạng CareSoft</div>`,
                        width: 150,
                        name: "caresoft_ticket", data: "caresoft_ticket", render: function (data, type, row) {
                            var caresoftTicket = data && typeof data == "object" ? data : {},
                                {created_at, ticket_id} = caresoftTicket,
                                careSoftSttHtml = `<div class="text-center">`;

                            if (created_at) {
                                var time = created_at ? wmBags.dateTime(created_at).format('FULL') : "";
                                careSoftSttHtml += `<div class="" data-toggle="tooltip" 
                                                        data-placement="bottom" title="Lúc : ${time}">
                                                        <a style="text-decoration: none !important;" class="text-success" target="_blank" href="//web1.caresoft.vn/tmvngocdung#/index?type=ticket&id=${ticket_id}">
                                                            <span class="dashicons dashicons-yes"></span>
                                                            Đã Tạo Ticket
                                                        </a>
                                                    </div>`;
                            } else {
                                careSoftSttHtml += `<div class="text-danger" style="line-height: 20px;">
                                                        <span class="dashicons dashicons-warning"></span>
                                                        <span>Chưa Có</span>
                                                        <a href="#" class="toCareSoftNow text-success" data-toggle="tooltip" 
                                                        data-placement="bottom" title="Đưa lên CareSoft">
                                                            <span class="dashicons dashicons-upload"></span>
                                                        </a>
                                                    </div>`;
                            }
                            careSoftSttHtml += `</div>`;
                            return careSoftSttHtml;
                        }
                    },
                    {
                        title: `<div class="font-weight-bold">
                            <span class="dashicons dashicons-clock"></span> 
                            Ngày Đăng Kí</div>`,width : 180,
                        name: "created_at", data: "created_at", render: function (data, type, row) {
                            var created_at = ["string", "number"].includes(typeof data) ? data : false,
                                date = created_at ? wmBags.dateTime(created_at).format('ll') : "",
                                time = created_at ? wmBags.dateTime(created_at).format('LT') : "",
                                createdAtHtml = `<div class="text-center">
                                    <span data-toggle="tooltip" data-placement="bottom" title="${time}">${date} ${time}</span>
                                </div>`;
                            return createdAtHtml
                        }
                    },
                    {
                        title: `<div class="font-weight-bold text-center">Từ Form</div>`
                        ,className: "text-center", name: "form", data: "form", render: function (form, type, row) {
                            var {name, title} = typeof form == "object" && form instanceof Object ? form : {},
                                formHtml = "";
                            if (name && title) {
                                formHtml = `<a href="#" onclick="return false" class="">
                                <span class="text-brand-blue">${title} - ${name}</span></a>`;
                            }
                            return formHtml;
                        }
                    }
                ],
                drawCallback: function (settings) {
                    var dTable = this,
                        table = dTable[0];
                    ticketLib.whenTicketTableDraw(table);

                    /*dTable.api().columns().every(function () {
                        var column = this,
                            select = $('<select><option value=""></option></select>')
                                .appendTo( $(column.header()).empty() )
                                .on( 'change', function () {
                                    var val = $.fn.dataTable.util.escapeRegex(
                                        $(this).val()
                                    );
                                    column
                                        .search( val ? '^'+val+'$' : '', true, false )
                                        .draw();
                                } );

                        column.data().unique().sort().each( function ( d, j ) {
                            select.append( '<option value="'+d+'">'+d+'</option>' )
                        } );
                    });*/
                },
                initComplete : function (settings, json) {
                    var dTable = this,
                        table = dTable[0];
                    ticketLib.ticketsFilter(table);
                }
            });


        // Ticket Charts
        var data = [],
            date = new Date(),
            month = date.getMonth(),
            years = date.getFullYear(),
            categories = wmBags.getDaysInMonth(4, years),
            ticketChart = "#ticketChart";

        ticketLib.chartTickets().start();
    }



    wmBags.formListPage = () => {
        var $formListTable = $(".wmListFormTable");
        var $tbody = $formListTable.find('tbody');

        // Ajax get all the form
        var options = {
            type: 'get',
            data: {
                action: 'listForm'
            }
        };
        wmBags.jsonTransPortData(options, function (err, res) {
            var forms = false;

            if (!err && res) {
                forms = ['object','array'].includes(typeof res.data) ? res.data : [];
            }
            makeDataToTable(forms);
        });

        function makeDataToTable(forms) {
            forms = typeof forms == 'object' && forms instanceof Array && forms.length > 0 ? forms : [];
            $tbody.html("");
            var pluginPageUrl = wmBags.pluginPageUrl;
            forms.forEach(function (formData) {
                var {form_id, title, directional, to_caresoft_now, created_at, caresoft_id} = formData;
                var shortcode = `[wmForm form_id="${form_id}"]`;
                var tr = `<tr class="trformItem" data-form_id="${form_id}">
                                <th scope="row" class="check-column">			
                                <label class="screen-reader-text" for="cb-select-${form_id}">Chọn ${title}</label>
                                    <input id="cb-select-${form_id}" type="checkbox" name="forms[]" value="${form_id}">
                                    <div class="locked-indicator">
                                        <span class="locked-indicator-icon" aria-hidden="true"></span>
                                        <span class="screen-reader-text">“${title}” đã bị khóa</span>
                                    </div>
                                </th>
                                <td>
                                    <strong><a id="detailFormByTitleBtn" class="row-title" href="#" aria-label="${title}">${title}</a></strong>
                                    <div class="row-actions">
                                        <span class="edit">
                                            <a class="updateFormItem" href="#" aria-label="Sửa “${title}”">Chỉnh sửa</a> | 
                                        </span>
                                        <span class="trash">
                                            <a class="deleteFormItem" href="#" class="submitdelete" aria-label="Bỏ “${title}” vào thùng rác">Xóa</a> | 
                                        </span>
                                    </div>
                                </td>
                                <td>${to_caresoft_now.toUpperCase()}</td>
                                <td>${directional ? directional : "Không"}</td>
                                <td>${new Date(created_at)}</td>
                                <td><code>${shortcode}</code></td>
                            </tr>`;
                $tbody.append(tr);
            });

            var $trs = $tbody.find('tr.trformItem');
            $trs.off('click');
            $trs.on('click', function (e) {
                var target = e.target;
                var $target = $(target);
                var $tr = $(target).closest('tr');
                var form_id = $tr.data('form_id');

                // If this action is update form
                if ($target.hasClass('updateFormItem')) {
                    e.preventDefault(e);
                    window.location = `${pluginPageUrl}&currentPage=formUpdate&form_id=${form_id}` ;
                }

                // If this action is delete form
                if ($target.hasClass("deleteFormItem")) {
                    e.preventDefault(e);

                    var cf = confirm("Bạn có muốn xóa form này ~");
                    if (cf) {
                        var options = {
                            type: "get",
                            data: {
                                action: "deleteForm",
                                form_id: form_id
                            }
                        }

                        wmBags.jsonTransPortData(options, function (err, res) {
                            if (!err && res) {
                                wmBags.formListPage();
                            } else {
                                alert("Không thể xóa form này");
                            }
                        })
                    } else {
                        return false;
                    }
                };
            })
        }

        // Add event new form
        /*$('#newFormBtn').off('click');
        $('#newFormBtn').on('click', function (e) {
            e.preventDefault(e);
            wmBags.getViewHtml('formNew');
        });*/

    }

    wmBags.formNewPage = () => {
        // setup fb-editor
        var $formEditor = $("fb-editor");
        $formEditor.html("");
        var formBuilder = wmBags.formBuilder('fb-editor', {
            disabledActionButtons: [/*'data'*/,'save'],
            /*actionButtons: [
                {
                    id: 'saveFormCustom',
                    className: 'btn btn-primary',
                    label: 'Lưu lại',
                    type: 'button',
                    events: {
                        click: saveCustomForm
                    }
                },
            ],*/
            controlOrder: [
                'text',
                'textarea',
                'checkbox',
                'number',
                'button'
            ],
            formData: [
                {
                    "type": "text",
                    "required": true,
                    "label": "Họ Tên",
                    "placeholder": "Tên của bạn",
                    "className": "form-control",
                    "name": "name",
                    "subtype": "text",
                    "maxlength": "200"
                },
                {
                    "type": "text",
                    "subtype": "tel",
                    "required": true,
                    "label": "Số Điện Thoại",
                    "placeholder": "Số điện thoại của bạn là",
                    "className": "form-control",
                    "name": "phone",
                    "maxlength": "15"
                },
                {
                    "type": "text",
                    "subtype": "email",
                    "label": "Email",
                    "placeholder": "vd : example@email.com",
                    "className": "form-control",
                    "name": "email",
                    "maxlength": "200"
                },
                {
                    "type": "textarea",
                    "required": true,
                    "label": "Lời nhắn",
                    "placeholder": "Bạn quan tâm đến dịch vụ nào của chúng tôi",
                    "className": "form-control",
                    "name": "note",
                    "subtype": "textarea",
                    "maxlength": "500",
                    "rows": "3"
                }
            ]
        });

        // Listen form submit
        var $form = $("#formNewItem");
        $form.on('submit', function (e) {
            e.preventDefault(e);
            var formData = {};
            var $inputs = $form.find(':input');
            $inputs.each(function (i, field) {
                var $field = $(field);
                var {name, type, disabled, value} = field;
                if (['submit', 'button'].includes(type)) return;
                if (name) {
                    formData[name] = $field.val();
                }
            });

            var customFormTemplate = formBuilder.actions.getData();
            formData.form_custom_template = customFormTemplate;

            var a = Object.keys(formData).length > 0 ? true : false;
            if (a) {
                var options = {
                    type: 'post',
                    data: {
                        action: 'newForm',
                        form : formData
                    }
                }
                wmBags.jsonTransPortData(options, function (err, res) {
                    if (!err && res) {
                        var pluginPageUrl = $("#pluginPageUrl").data("pluginPageUrl");
                        window.location = `${pluginPageUrl}&currentPage=formList`;
                    } else {
                        alert("Không thể tạo form này");
                        return false
                    }
                });
            } else {
                alert("Rỗng");
            }
        })

        // Back to List Form
        /*var $backToListFormBtn = $('#backToListFormBtn');
        $backToListFormBtn.on('click', function (e) {
            e.preventDefault(e);
            wmBags.getViewHtml('formList');
        });*/
    };

    wmBags.formUpdatePage = () => {
        // setup fb-editor
        var $formEditor = $("#fb-editor");
        $formEditor.html("");
        var formBuilder = wmBags.formBuilder('fb-editor', {
            disabledActionButtons: ['data','save'],
            controlOrder: [
                'text',
                'textarea',
                'checkbox',
                'number',
                'button'
            ],
        });

        // Load data to form input
        var $form = $("#formUpdateItem");
        var form = $form.get(0);
        var $inputs = $form.find(':input');
        var query = typeof window.location.search == "string" && window.location.search.trim().length > 0 ? wmBags.queryStringToObj(window.location.search) : false;
        if (!query) {
            alert("Không tìm thấy page nài roài");
        } else {
            if (query.hasOwnProperty("form_id") && query.form_id) {
                var {form_id} = query;
                var options = {
                    type: 'get',
                    data: {
                        action: 'readForm',
                        form_id: form_id
                    }
                };
                wmBags.jsonTransPortData(options, function (err, res) {
                    var {success, data} = typeof res == "object" ? res : {};
                    if (!err && success && data) {
                        var insertData = wmBags.insertDataToForm(form, data);
                        if (insertData) {
                            var {form_custom_template} = data;
                            form_custom_template = typeof form_custom_template == "object" && form_custom_template instanceof Array  ? form_custom_template : [];
                            formBuilder.actions.setData(form_custom_template)
                        } else {
                            alert("Không thể thêm dữ liệu vào form");
                        }
                    } else {
                        alert("Không tìm thấy form này");
                    }
                });

                $form.on("submit", function (e) {
                    e.preventDefault(e);
                    var formData = {};
                    $inputs.each(function (i, field) {
                        var {name, type, disabled, value} = field;

                        if (["button", "submit"].includes(type) || disabled) {
                            return
                        }

                        if (name && value) {
                            formData[name] = $(field).val();
                        }
                    });

                    var customFormTemplate = formBuilder.actions.getData();
                    formData.form_custom_template = customFormTemplate;

                    var options = {
                        type: "post",
                        data: {
                            action: "updateForm",
                            form : formData
                        }
                    };
                    wmBags.jsonTransPortData(options, function (err, res) {
                        var {success, data} = typeof res == "object" ? res : {};
                        if (!err && success && data) {
                            var urlRidirect = wmBags.pluginPageUrl;
                            window.location.href = `${urlRidirect}&currentPage=popupList`
                        } else {
                            alert("Cannot update this form");
                        }
                    });
                })
            } else {
                alert("missing required field");
            }
        }
        return false;
    }

    wmBags.popupListPage = () => {
        var $wrap = $(".wmAdminWrap");
        var $table = $wrap.find(".wmListFormTable");
        var $tbody = $table.find('tbody');
        var $trs = $tbody.find('tr.trPopupItem');
        $trs.off('click');
        $trs.on('click', function (e) {
            var target = e.target;
            var $target = $(target);
            var $tr = $(target).closest('tr');
            var popup_id = $tr.data('popup_id');

            // If this action is update form
            if ($target.hasClass('updatePopupItem')) {
                e.preventDefault(e);
                var pluginPageUrl = wmBags.pluginPageUrl,
                    {popup_id} = $(this).closest('tr.trPopupItem').data();
                if (popup_id)
                    window.location = `${pluginPageUrl}&currentPage=popupUpdate&popup_id=${popup_id}`;
                else
                    alert("Không tìm thấy popup này!");
                return false;
            }

            // If this action is delete form
            if ($target.hasClass("deletePopupItem")) {
                e.preventDefault(e);
                var cf = confirm("Bạn có muốn xóa popup này ~");
                if (cf) {
                    var options = {
                        type: "post",
                        data: {
                            action: "deletePopup",
                            popup_id: popup_id
                        }
                    }
                    wmBags.jsonTransPortData(options, function (err, res) {
                        var {success, data} = typeof res == "object" ? res : {};
                        if (!err && success && data) {
                            // wmBags.popupListPage();
                            window.location.reload();
                        } else {
                            alert("Không thể xóa popup này");
                        }
                    })
                } else {
                    return false;
                }
            };
        })

        /*var $updateBtn = $wrap.find(".updatePopupItem");
        $updateBtn.off('click');
        $updateBtn.on('click', function (e) {

        });

        var $deleteBtn = $wrap.find(".deletePopupItem");
        $deleteBtn.off('click');
        $deleteBtn.on('click', function (e) {

        });*/
    }

    wmBags.popupNewPage = () => {
        wmBags.listenUploadMedia();
        var $form = $(".wmAdminWrap").find("form.popupForm");

        $form.off('submit');
        $form.on('submit', function (e) {
            var form = this,
                $form = $(form);

            var submitAction = {
                'popupUpdateItem': updatePopupByForm,
                'popupNewItem' : newPopupByForm
            }

            var formId = $form.attr("id");
            formId = typeof formId == "string" && Object.keys(submitAction).includes(formId) ? formId : false;
            if (formId) {
                e.preventDefault(e);
                var formData = wmBags.getObjFormData(form);
                submitAction[formId](formData);
            }

            // Else return false
            return false;
        });


        function newPopupByForm(data) {
            var options = {
                type: "post",
                data: {
                    action: "newPopup",
                    popup: data
                }
            }
            wmBags.jsonTransPortData(options, function (err, res) {
                var {success, data} = typeof res == "object" ? res : {};
                if (!err && success && data) {
                    var hrefListPopup = wmBags.pluginPageUrl,
                        url = `${hrefListPopup}&currentPage=popupList`;
                    window.location = url;
                } else {
                    alert("Không thể tạo mới popup");
                }
            });
        }
        function updatePopupByForm(data) {
            var {isAuto, delay_show_time} = data;
            if (!isAuto) {
                delete data.delay_show_time;
            }

            var options = {
                type: "post",
                data: {
                    action: "updatePopup",
                    popup: data
                }
            };

            wmBags.jsonTransPortData(options, function (err, res) {
                var {success, data} = typeof res == "object" ? res : {};
                if (!err && success && data) {
                    var hrefListPopup = wmBags.pluginPageUrl,
                        url = `${hrefListPopup}&currentPage=popupList`;
                    window.location = url;
                } else {
                    alert("Không thể cập nhật popup");
                }
            });
        }


        //
        $('body .meta-box-sortables.ui-sortable').sortable();
    }

    wmBags.popupUpdatePage = () => {
        wmBags.popupNewPage();
    };

    wmBags.listenUploadMedia = () => {
        // idElem = typeof idElem == "string" && idElem.trim().length > 0 ? idElem : '.wm_upload_image_button';
        /*
         * Select/Upload image(s) event
         */
        $('body').on('click', '.wm_upload_image_button', function(e){
            e.preventDefault();

            var button = $(this),
                custom_uploader = wp.media({
                    title: 'Background Image',
                    library : {
                        // uncomment the next line if you want to attach image to the current post
                        // uploadedTo : wp.media.view.settings.post.id,
                        type : 'image'
                    },
                    button: {
                        text: 'Use this image' // button label text
                    },
                    multiple: false // for multiple image selection set to true
                }).on('select', function() { // it also has "open" and "close" events
                    var attachment = custom_uploader.state().get('selection').first().toJSON();
                    $(button).removeClass('button')
                        .html('<img class="wm_true_pre_image_upload" src="' + attachment.url + '" style="display:block;width: 100%;margin-bottom: 10px;" />')
                        .next().val(attachment.id).next().show();
                    /* if you sen multiple to true, here is some code for getting the image IDs
                    var attachments = frame.state().get('selection'),
                        attachment_ids = new Array(),
                        i = 0;
                    attachments.each(function(attachment) {
                         attachment_ids[i] = attachment['id'];
                        console.log( attachment );
                        i++;
                    });
                    */
                }).open();
        });

        /*
         * Remove image event
         */
        $('body').on('click', '.wm_remove_image_button', function(){
            $(this).hide().prev().val('').prev().addClass('button').html('Upload image');
            return false;
        });
    }

    wmBags.getObjFormData = (form) => {
        var $form = $(form)
        var $inputs = $form.find(':input'),
            formData = {};

        function get_tinymce_content(id) {
            var content;
            var inputid = id;
            var editor = tinyMCE.get(inputid);
            var textArea = jQuery('textarea#' + inputid);
            if (textArea.length>0 && textArea.is(':visible')) {
                content = textArea.val();
            } else {
                content = editor.getContent();
            }
            return content;
        }

        $inputs.each(function (i, field) {
            var {type, name, disabled} = field;
            if (["submit","button"].includes(type) || disabled) return;

            if (name) {
                if ($(field).hasClass('wp-editor-area')) {
                    try {
                        formData[name] = get_tinymce_content(field.id);
                    } catch (e) {
                        formData[name] = $(field).val()
                    }
                    return;
                }

                if (type == 'checkbox') {
                    formData[name] = $(field).prop("checked");
                    return;
                }

                formData[name] = $(field).val();
            }
        });

        return formData;
    }

    wmBags.insertDataToForm = (form, data) => {
        form = typeof form == 'object' && form.tagName.toLowerCase() == 'form' ? form : false;
        data = typeof data == 'object' && data instanceof Object && Object.keys(data).length > 0 ? data : false;
        // If missing require param

        if (!form && !data) return false;

        var $form = $(form);
        var $inputs = $form.find(':input');
        $inputs.each(function (i, field) {
            var {name, type, value, disabled} = field;

            if (["button","submit"].includes(type) || disabled) return false;
            if (name && data.hasOwnProperty(name)) {
                $(field).val(data[name]);
            }
        });
        return true;
    }

    wmBags.getViewHtml = (view) => {
        var b = wmBags.getPageActive(view);
        if (b) {
            wmBags.loadDataOnPage();
        } else {
            alert("Không tìm thấy page này đâu !");
        }
    }

    wmBags.checkThisPagePlugin = () => {
        var search = typeof window.location.search == "string" && window.location.search.trim().length > 0 ? window.location.search : false;
        if (!search) return false;
        var query = wmBags.queryStringToObj(search);
        if (query.hasOwnProperty('page') && ['webManagerForm','webManagerPopup','webManagerTicket'].includes(query.page))
            return true;
        return false;
    }

    wmBags.init = () => {
        var check = wmBags.checkThisPagePlugin();
        if (check)
            wmBags.loadDataOnPage();
        window.wmBags = wmBags;
        return false;
    }

    wmBags.formBuilder = (id, options = null) => {
        var element = typeof id == "string" && document.getElementById(id) ? document.getElementById(id) : false;
        var options = typeof options == "object" ? options : {};
        if (element)
            return $(element).formBuilder = $(element).formBuilder(options);
        return false;
    }

    wmBags.queryStringToObj = () => {
        var search = location.search.substring(1);
        var x = JSON.parse('{"' + decodeURI(search).replace(/"/g, '\\"').replace(/&/g, '","').replace(/=/g,'":"') + '"}');
        return x;
    }

    /**
     * @function jsonTransPortData
     * @param type
     * @param data
     * @param callback
     */
    wmBags.jsonTransPortData = ({type, data}, callback) => {
        var { protocol,origin, hostname,pathname } = window.location;
        var url = `${origin}/wp-admin/admin-ajax.php`;
        if (hostname == "localhost") {
            url = `${origin}${pathname.replace("admin.php", "admin-ajax.php")}`;
        }
        // var path = '/wp-admin/admin-ajax.php';
        $.ajax({
            type : type, //Phương thức truyền post hoặc get
            dataType : "json", //Dạng dữ liệu trả về xml, json, script, or html
            url : url, //Đường dẫn chứa hàm xử lý dữ liệu. Mặc định của WP như vậy
            data : data,
            success: function(response) {
                callback(false, response);
            },
            error: function( jqXHR, textStatus, errorThrown ){
                console.log(jqXHR)
                //Làm gì đó khi có lỗi xảy ra
                console.log( 'The following error occured: ' + textStatus, errorThrown );
                callback("Có lỗi xảy ra");
            }
        });
    }

    wmBags.dateTime = (dateStr) => {
        dateStr = ['string','number'].includes(typeof dateStr) && Date.parse(dateStr) > 0 ? Date.parse(dateStr) : false;
        var date = dateStr ? new Date(dateStr) : null,
            lib = {},
            viMonthNames = [];

        for (var i = 1;i < 13;i++) {
            viMonthNames.push(`Tháng ${i}`);
        }
        lib.format = (type) => {
            if (!date)
                return null;
            var day = date.getDate(),
                month = date.getMonth(),
                monthName = viMonthNames[month],
                month = month + 1,
                year = date.getFullYear(),
                hours = date.getHours(),
                minutes = date.getMinutes(),
                seconds = date.getSeconds(),
                ampm = hours >= 12 ? 'pm' : 'am';
            ampm = ampm.toUpperCase();
            hours = hours % 12;
            hours = hours ? hours : 12; // the hour '0' should be '12'
            minutes = minutes < 10 ? '0'+minutes : minutes;

            if (type == 'LT')
                date = `${hours}:${minutes} ${ampm}`;
            if (type == 'FULL')
                date = `${month}/${day}/${year} ${hours}:${minutes} ${ampm}`;
            if (type == 'LTS')
                date = `${hours}:${minutes}:${seconds} ${ampm}`;
            if (type == "L")
                date = `${month}/${day}/${year}`;
            if (type == "l")
                date = `${day}/${month}/${year}`;
            if (type == "LL")
                date = `${monthName} ${day}, ${year}`;
            if (type == "ll")
                date = `${day} ${monthName}, ${year}`;
            if (type == "y-m-d") {
                date = `${year}-${month}-${day}`;
            }

            return date;
        }

        return lib;
    }

    wmBags.fullLoading = (add) => {
        var lib = {},
            loading = `<div class="text-center wmfullLoading">
                <div class="spinner-border text-danger" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
            </div>`,
            $fullLoading = $(loading);
        add = typeof add == 'boolean' && add ? add : false;
        lib.thisElement = (element) => {
            if (add) {
                $(element).append($fullLoading);
            } else {
                $(element).find('.wmfullLoading').remove();
            }
        }

        return lib;
    }

    wmBags.getDaysInMonth = (month, year) => {
        var currentDate = new Date();
        // month = month ? month : currentDate.getMonth();
        year = year ? year : currentDate.getFullYear()
        var date = new Date(year, month, 1);
        var days = [];
        while (date.getMonth() === month) {
            var day = new Date(date);
            days.push(day.toISOString());
            date.setDate(date.getDate() + 1);
        }
        return days;
    }


    /**
     *  Return check box
     * */
    function getCheckBox(all) {
        var html = null;
        var input = '<input type="checkbox" class="checkOnlyOne" name="select">';
        if (all) {
            var input = '<input class="checkall" type="checkbox" name="select-all">';
        }
        html = '<label class="option block mn">\n' + input +
            '       <span class="checkbox mn"></span>\n' +
            '   </label>';
        return html;
    }

    Date.prototype.addDays = function(days) {
        var date = new Date(this.valueOf());
        date.setDate(date.getDate() + days);
        return date;
    }
    function getDates(startDate, stopDate) {
        startDate = new Date(startDate);
        stopDate = new Date(stopDate);
        var dateArray = new Array();
        var currentDate = startDate;
        while (currentDate <= stopDate) {
            dateArray.push(new Date(currentDate).toISOString());
            currentDate = currentDate.addDays(1);
        }
        return dateArray;
    }


    $(document).ready(function () {
        wmBags.init();
    });
})(jQuery);