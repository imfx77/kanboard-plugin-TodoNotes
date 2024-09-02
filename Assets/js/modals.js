/**
 * @author  Im[F(x)]
 */

// console.log('define _TodoNotes_Modals_');
//////////////////////////////////////////////////
class _TodoNotes_Modals_ {

//---------------------------------------------
// Notes related modal dialogs
//---------------------------------------------

//------------------------------------------------
static MoveNoteToArchive(project_id, user_id, id) {
    $("#dialogMoveNoteToArchive").removeClass( 'hideMe' );
    $("#dialogMoveNoteToArchive").dialog({
        resizable: false,
        height: "auto",
        modal: true,
        buttons: [
            {
                text : _TodoNotes_Translations_.GetTranslationExportToJS('TodoNotes__JS_DIALOG_ARCHIVE_BTN'),
                click: function() {
                    _TodoNotes_Requests_.MoveNoteToArchive(project_id, user_id, id);
                    $( this ).dialog( "close" );
                    _TodoNotes_Requests_.RefreshNotes(project_id, user_id);
                    _TodoNotes_Requests_.RefreshTabs(user_id);
                },
            },
            {
                text : _TodoNotes_Translations_.GetTranslationExportToJS('TodoNotes__JS_DIALOG_CANCEL_BTN'),
                click: function() { $( this ).dialog( "close" ); }
            },
        ]
    });
}

//------------------------------------------------
static DeleteNote(project_id, user_id, id, isArchive) {
    $("#dialogDeleteNote").removeClass( 'hideMe' );
    $("#dialogDeleteNote").dialog({
        resizable: false,
        height: "auto",
        modal: true,
        buttons: [
            {
                text : _TodoNotes_Translations_.GetTranslationExportToJS('TodoNotes__JS_DIALOG_DELETE_BTN'),
                click: function() {
                    if (isArchive) {
                        _TodoNotes_Requests_.DeleteNoteFromArchive(project_id, user_id, id);
                    } else {
                        _TodoNotes_Requests_.DeleteNote(project_id, user_id, id);
                    }
                    $( this ).dialog( "close" );
                    _TodoNotes_Requests_.RefreshNotes(project_id, user_id);
                    _TodoNotes_Requests_.RefreshTabs(user_id);
                },
            },
            {
                text : _TodoNotes_Translations_.GetTranslationExportToJS('TodoNotes__JS_DIALOG_CANCEL_BTN'),
                click: function() { $( this ).dialog( "close" ); }
            },
        ]
    });
}

//------------------------------------------------
static DeleteAllDoneNotes(project_id, user_id) {
    $("#dialogDeleteAllDone").removeClass( 'hideMe' );
    $("#dialogDeleteAllDone").dialog({
        resizable: false,
        height: "auto",
        modal: true,
        buttons: [
            {
                text : _TodoNotes_Translations_.GetTranslationExportToJS('TodoNotes__JS_DIALOG_DELETE_BTN'),
                click: function() {
                    _TodoNotes_Requests_.DeleteAllDoneNotes(project_id, user_id);
                    $( this ).dialog( "close" );
                    _TodoNotes_Requests_.RefreshNotes(project_id, user_id);
                    _TodoNotes_Requests_.RefreshTabs(user_id);
                },
            },
            {
                text : _TodoNotes_Translations_.GetTranslationExportToJS('TodoNotes__JS_DIALOG_CANCEL_BTN'),
                click: function() { $( this ).dialog( "close" ); }
            },
        ]
    });
}

//------------------------------------------------
static TransferNote(project_id, user_id, id) {
    $("#dialogTransferNote-P" + project_id).removeClass( 'hideMe' );
    $("#dialogTransferNote-P" + project_id).dialog({
        resizable: false,
        height: "auto",
        modal: true,
        buttons: [
            {
                text : _TodoNotes_Translations_.GetTranslationExportToJS('TodoNotes__JS_DIALOG_MOVE_BTN'),
                click : function() {
                    const target_project_id = $("#listNoteProject-P" + project_id + " option:selected").val();
                    _TodoNotes_Requests_.TransferNote(project_id, user_id, id, target_project_id);
                    $( this ).dialog( "close" );
                    _TodoNotes_Requests_.RefreshNotes(project_id, user_id);
                    _TodoNotes_Requests_.RefreshTabs(user_id);
                },
            },
            {
                text : _TodoNotes_Translations_.GetTranslationExportToJS('TodoNotes__JS_DIALOG_CANCEL_BTN'),
                click : function() {
                    $( this ).dialog( "close" );
                }
            },
        ]
    });
    return false;
}

//------------------------------------------------
static CreateTaskFromNote(project_id, user_id, id, is_active, title, description, category_id) {
    $.ajaxSetup ({
        cache: false
    });
    $("#dialogCreateTaskParams").removeClass( 'hideMe' );
    $("#deadloading").addClass( 'hideMe' );
    $("#listCatCreateTask-P" + project_id).val(category_id).change();
    $("#dialogCreateTaskFromNote-P" + project_id).removeClass( 'hideMe' );
    $("#dialogCreateTaskFromNote-P" + project_id).dialog({
        resizable: false,
        height: "auto",
        modal: true,
        buttons: [
            {
                text : _TodoNotes_Translations_.GetTranslationExportToJS('TodoNotes__JS_DIALOG_CREATE_BTN'),
                click: function() {
                    const categoryCreateTask = $("#listCatCreateTask-P" + project_id + " option:selected").val();
                    const columnCreateTask = $("#listColCreateTask-P" + project_id + " option:selected").val();
                    const swimlaneCreateTask = $("#listSwimCreateTask-P" + project_id + " option:selected").val();
                    const removeNote = $("#removeNote-P" + project_id).is(':checked');

                    const loadUrl = '/?controller=TodoNotesController&action=CreateTaskFromNote&plugin=TodoNotes'
                                + '&project_custom_id=' + project_id
                                + '&user_id=' + user_id
                                + '&task_title=' + encodeURIComponent(title)
                                + '&task_description=' + encodeURIComponent(description)
                                + '&category_id=' + categoryCreateTask
                                + '&column_id=' + columnCreateTask
                                + '&swimlane_id=' + swimlaneCreateTask;

                    $("#dialogCreateTaskFromNote-P" + project_id).dialog({
                        title: _TodoNotes_Translations_.GetTranslationExportToJS('TodoNotes__JS_DIALOG_RESULT_TITLE'),
                        buttons: [
                            {
                                text : _TodoNotes_Translations_.GetTranslationExportToJS('TodoNotes__JS_DIALOG_CLOSE_BTN'),
                                click: function() { $( this ).dialog( "close" ); }
                            },
                        ]
                    });
                    $("#dialogCreateTaskParams").addClass( 'hideMe' );
                    $("#deadloading").removeClass( 'hideMe' );
                    $("#deadloading").html(_TodoNotes_Translations_.msgLoadingSpinner);
                    $("#deadloading").load(loadUrl);
                    if (removeNote) {
                        _TodoNotes_Requests_.DeleteNote(project_id, user_id, id);
                        _TodoNotes_Requests_.RefreshNotes(project_id, user_id);
                        _TodoNotes_Requests_.RefreshTabs(user_id);
                    }
                },
            },
            {
                text : _TodoNotes_Translations_.GetTranslationExportToJS('TodoNotes__JS_DIALOG_CANCEL_BTN'),
                click: function() { $( this ).dialog( "close" ); }
            },
        ]
    });
    return false;
}

//---------------------------------------------
// Notifications modal dialogs & handlers
//---------------------------------------------

//---------------------------------------------
// Return a formated string from a date Object mimicking PHP's date() functionality
static #FormatDate(format, date){
	if(!date || date === "")
	{
		date = new Date();
	}
	else if(typeof(date) !== 'object')
	{
		date = new Date(date.replace(/-/g,"/")); // attempt to convert string to date object
	}

	var string = '',
		mo = date.getMonth(),   // month (0-11)
		m1 = mo+1,			    // month (1-12)
		dow = date.getDay(),    // day of week (0-6)
		d = date.getDate(),     // day of the month (1-31)
		y = date.getFullYear(), // 1999 or 2003
		h = date.getHours(),    // hour (0-23)
		mi = date.getMinutes(), // minute (0-59)
		s = date.getSeconds();  // seconds (0-59)

	for (var i = 0, len = format.length; i < len; i++) {
		switch(format[i])
		{
			case 'j': // Day of the month without leading zeros  (1 to 31)
				string+= d;
				break;

			case 'd': // Day of the month, 2 digits with leading zeros (01 to 31)
				string+= (d < 10) ? "0"+d : d;
				break;

			case 'l': // (lowercase 'L') A full textual representation of the day of the week
				var days = Array("Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday");
				string+= days[dow];
				break;

			case 'w': // Numeric representation of the day of the week (0=Sunday,1=Monday,...6=Saturday)
				string+= dow;
				break;

			case 'D': // A textual representation of a day, three letters
				days = Array("Sun","Mon","Tue","Wed","Thr","Fri","Sat");
				string+= days[dow];
				break;

			case 'm': // Numeric representation of a month, with leading zeros (01 to 12)
				string+= (m1 < 10) ? "0"+m1 : m1;
				break;

			case 'n': // Numeric representation of a month, without leading zeros (1 to 12)
				string+= m1;
				break;

			case 'F': // A full textual representation of a month, such as January or March
				var months = Array("January","February","March","April","May","June","July","August","September","October","November","December");
				string+= months[mo];
				break;

			case 'M': // A short textual representation of a month, three letters (Jan - Dec)
				months = Array("Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec");
				string+= months[mo];
				break;

			case 'Y': // A full numeric representation of a year, 4 digits (1999 OR 2003)
				string+= y;
				break;

			case 'y': // A two digit representation of a year (99 OR 03)
				string+= y.toString().slice(-2);
				break;

			case 'H': // 24-hour format of an hour with leading zeros (00 to 23)
				string+= (h < 10) ? "0"+h : h;
				break;

			case 'g': // 12-hour format of an hour without leading zeros (1 to 12)
				var hour = (h===0) ? 12 : h;
				string+= (hour > 12) ? hour -12 : hour;
				break;

			case 'h': // 12-hour format of an hour with leading zeros (01 to 12)
				hour = (h===0) ? 12 : h;
				hour = ( hour > 12) ? hour -12 : hour;
				string+= (hour < 10) ? "0"+hour : hour;
				break;

			case 'a': // Lowercase Ante meridiem and Post meridiem (am or pm)
				string+= (h < 12) ? "am" : "pm";
				break;

			case 'i': // Minutes with leading zeros (00 to 59)
				string+= (mi < 10) ? "0"+mi : mi;
				break;

			case 's': // Seconds, with leading zeros (00 to 59)
				string+= (s < 10) ? "0"+s : s;
				break;

			case 'c': // ISO 8601 date (eg: 2012-11-20T18:05:54.944Z)
				string+= date.toISOString();
				break;

			default:
				string+= format[i];
		}
	}

	return string;
}

//------------------------------------------------
static #UpdateNotificationsSetupPostponeTime() {
    // validate numeric input
    const postponeValue = parseInt($("#postpone_value_NotificationsSetup").val());
    if (postponeValue < 1) {
        setTimeout(function() {
            $("#postpone_value_NotificationsSetup").val(1).change();
        }, 50);
        return;
    }

    const datetimeFormat = $("#dialogNotificationsSetup").attr('data-datetime-format');

    const postponeBase = parseInt($("#postpone_base_NotificationsSetup").val());
    const localTimeOffset = parseInt($("#refProjectId").attr('data-local-time-offset'));
    let postponeTime = (postponeBase > 0) ? new Date(postponeBase * 1000) : new Date(Date.now() + localTimeOffset * 1000);

    $("#form-alert_time_NotificationsSetup").attr('placeholder', _TodoNotes_Modals_.#FormatDate(datetimeFormat, postponeTime));

    const postponeType = parseInt($("#postpone_type_NotificationsSetup").val());
    switch(postponeType)
    {
        case 1: // seconds
            postponeTime.setSeconds(postponeTime.getSeconds() + postponeValue);
            break;
        case 2: // minutes
            postponeTime.setMinutes(postponeTime.getMinutes() + postponeValue);
            break;
        case 3: // hours
            postponeTime.setHours(postponeTime.getHours() + postponeValue);
            break;
        case 4: // days
            postponeTime.setDate(postponeTime.getDate() + postponeValue);
            break;
        case 5: // months
            {
                const currentDay = postponeTime.getDate();
                postponeTime.setMonth(postponeTime.getMonth() + postponeValue);
                let newDay = postponeTime.getDate();
                // correct back over-projected dates
                while (newDay >= 1 && newDay <= 3 && newDay < currentDay) {
                    postponeTime.setDate(postponeTime.getDate() - 1);
                    newDay = postponeTime.getDate();
                }
            }
            break;
        case 6: // years
            postponeTime.setFullYear(postponeTime.getFullYear() + postponeValue);
            break;
    }

    $("#postpone_time_NotificationsSetup").text(_TodoNotes_Modals_.#FormatDate(datetimeFormat, postponeTime));
}

//------------------------------------------------
static #UpdateNotificationsSetupAlertOptions() {
    // alert before radio buttons
    const alert_before = $("#alert_before_NotificationsSetup").is(':checked');
    $("#alert_before1day_NotificationsSetup").prop('disabled', !alert_before);
    $("#alert_before1hour_NotificationsSetup").prop('disabled', !alert_before);

    // alert after radio buttons
    const alert_after = $("#alert_after_NotificationsSetup").is(':checked');
    $("#alert_after1day_NotificationsSetup").prop('disabled', !alert_after);
    $("#alert_after1hour_NotificationsSetup").prop('disabled', !alert_after);
}

//------------------------------------------------
static #NOTIFICATIONS_OPTIONS_FLAG_ALERT_MAIL            = 0x00000001;    // 0000-0000-0000-0000 0000-0000-0000-0001
static #NOTIFICATIONS_OPTIONS_FLAG_ALERT_WEBPN           = 0x00000002;    // 0000-0000-0000-0000 0000-0000-0000-0010
static #NOTIFICATIONS_OPTIONS_FLAG_ALERT_BEFORE1DAY      = 0x00000004;    // 0000-0000-0000-0000 0000-0000-0000-0100
static #NOTIFICATIONS_OPTIONS_FLAG_ALERT_BEFORE1HOUR     = 0x00000008;    // 0000-0000-0000-0000 0000-0000-0000-1000
static #NOTIFICATIONS_OPTIONS_FLAG_ALERT_AFTER1DAY       = 0x00000010;    // 0000-0000-0000-0000 0000-0000-0001-0000
static #NOTIFICATIONS_OPTIONS_FLAG_ALERT_AFTER1HOUR      = 0x00000020;    // 0000-0000-0000-0000 0000-0000-0010-0000

static #NOTIFICATIONS_OPTIONS_FLAG_POSTPONE              = 0x00010000;    // 0000-0000-0000-0001 0000-0000-0000-0000
static #NOTIFICATIONS_OPTIONS_MASK_POSTPONE_TYPE         = 0x000E0000;    // 0000-0000-0000-1110 0000-0000-0000-0000
static #NOTIFICATIONS_OPTIONS_MASK_POSTPONE_VALUE        = 0xFFF00000;    // 1111-1111-1111-0000 0000-0000-0000-0000
static #NOTIFICATIONS_OPTIONS_MASK_POSTPONE_TYPE_IX      = 17;
static #NOTIFICATIONS_OPTIONS_MASK_POSTPONE_VALUE_IX     = 20;

//------------------------------------------------
static #NotificationsOptionsToBitflags() {
        let notification_options_bitflags = 0;

        if ($("#alert_mail_NotificationsSetup").is(':checked')) {
            notification_options_bitflags |= _TodoNotes_Modals_.#NOTIFICATIONS_OPTIONS_FLAG_ALERT_MAIL;
        }
        if ($("#alert_webpn_NotificationsSetup").is(':checked')) {
            notification_options_bitflags |= _TodoNotes_Modals_.#NOTIFICATIONS_OPTIONS_FLAG_ALERT_WEBPN;
        }

        const alert_before = $("#alert_before_NotificationsSetup").is(':checked');
        if (alert_before && $("#alert_before1day_NotificationsSetup").is(':checked')) {
            notification_options_bitflags |= _TodoNotes_Modals_.#NOTIFICATIONS_OPTIONS_FLAG_ALERT_BEFORE1DAY;
        }
        if (alert_before && $("#alert_before1hour_NotificationsSetup").is(':checked')) {
            notification_options_bitflags |= _TodoNotes_Modals_.#NOTIFICATIONS_OPTIONS_FLAG_ALERT_BEFORE1HOUR;
        }

        const alert_after = $("#alert_after_NotificationsSetup").is(':checked');
        if (alert_after && $("#alert_after1day_NotificationsSetup").is(':checked')) {
            notification_options_bitflags |= _TodoNotes_Modals_.#NOTIFICATIONS_OPTIONS_FLAG_ALERT_AFTER1DAY;
        }
        if (alert_after && $("#alert_after1hour_NotificationsSetup").is(':checked')) {
            notification_options_bitflags |= _TodoNotes_Modals_.#NOTIFICATIONS_OPTIONS_FLAG_ALERT_AFTER1HOUR;
        }

        if ($("#postpone_NotificationsSetup").is(':checked')) {
            notification_options_bitflags |= _TodoNotes_Modals_.#NOTIFICATIONS_OPTIONS_FLAG_POSTPONE;
        }
        const postponeType = parseInt($("#postpone_type_NotificationsSetup").val());
        notification_options_bitflags |= (_TodoNotes_Modals_.#NOTIFICATIONS_OPTIONS_MASK_POSTPONE_TYPE & (postponeType << _TodoNotes_Modals_.#NOTIFICATIONS_OPTIONS_MASK_POSTPONE_TYPE_IX));
        const postponeValue = parseInt($("#postpone_value_NotificationsSetup").val());
        notification_options_bitflags |= (_TodoNotes_Modals_.#NOTIFICATIONS_OPTIONS_MASK_POSTPONE_VALUE & (postponeValue << _TodoNotes_Modals_.#NOTIFICATIONS_OPTIONS_MASK_POSTPONE_VALUE_IX));

        return notification_options_bitflags;
}

//------------------------------------------------
static #NotificationsOptionsFromBitflags(notification_options_bitflags) {
    // init defaults
    $("#alert_mail_NotificationsSetup").prop('checked', false);
    $("#alert_webpn_NotificationsSetup").prop('checked', false);
    $("#alert_before_NotificationsSetup").prop('checked', false);
    $("#alert_before1day_NotificationsSetup").prop('disabled', true).prop('checked', true);
    $("#alert_before1hour_NotificationsSetup").prop('disabled', true).prop('checked', false);
    $("#alert_after_NotificationsSetup").prop('checked', false);
    $("#alert_after1day_NotificationsSetup").prop('disabled', true).prop('checked', true);
    $("#alert_after1hour_NotificationsSetup").prop('disabled', true).prop('checked', false);
    
    $("#form-alert_time_NotificationsSetup").prop('disabled', false);
    $("#postpone_NotificationsSetup").prop('checked', false);
    $("#postpone_options_NotificationsSetup").removeClass('hideMe');
    $("#postpone_type_NotificationsSetup").val(4).trigger('change'); // days
    $("#postpone_value_NotificationsSetup").val(1).trigger('change');

    // exit when no stored options
    if (notification_options_bitflags <= 0) return;

    if (notification_options_bitflags & _TodoNotes_Modals_.#NOTIFICATIONS_OPTIONS_FLAG_ALERT_MAIL) {
        $("#alert_mail_NotificationsSetup").trigger('click');
    }
    if (notification_options_bitflags & _TodoNotes_Modals_.#NOTIFICATIONS_OPTIONS_FLAG_ALERT_WEBPN) {
        $("#alert_webpn_NotificationsSetup").trigger('click');
    }

    if (notification_options_bitflags & _TodoNotes_Modals_.#NOTIFICATIONS_OPTIONS_FLAG_ALERT_BEFORE1DAY) {
        $("#alert_before_NotificationsSetup").trigger("click");
        $("#alert_before1day_NotificationsSetup").trigger("click");
    }
    if (notification_options_bitflags & _TodoNotes_Modals_.#NOTIFICATIONS_OPTIONS_FLAG_ALERT_BEFORE1HOUR) {
        $("#alert_before_NotificationsSetup").trigger("click");
        $("#alert_before1hour_NotificationsSetup").trigger("click");
    }

    if (notification_options_bitflags & _TodoNotes_Modals_.#NOTIFICATIONS_OPTIONS_FLAG_ALERT_AFTER1DAY) {
        $("#alert_after_NotificationsSetup").trigger("click");
        $("#alert_after1day_NotificationsSetup").trigger("click");
    }
    if (notification_options_bitflags & _TodoNotes_Modals_.#NOTIFICATIONS_OPTIONS_FLAG_ALERT_AFTER1HOUR) {
        $("#alert_after_NotificationsSetup").trigger("click");
        $("#alert_after1hour_NotificationsSetup").trigger("click");
    }

    if (notification_options_bitflags & _TodoNotes_Modals_.#NOTIFICATIONS_OPTIONS_FLAG_POSTPONE) {
        $("#postpone_NotificationsSetup").trigger("click");
    }
    $("#postpone_type_NotificationsSetup")
        .val((notification_options_bitflags & _TodoNotes_Modals_.#NOTIFICATIONS_OPTIONS_MASK_POSTPONE_TYPE) >> _TodoNotes_Modals_.#NOTIFICATIONS_OPTIONS_MASK_POSTPONE_TYPE_IX)
        .trigger('change');
    $("#postpone_value_NotificationsSetup")
        .val((notification_options_bitflags & _TodoNotes_Modals_.#NOTIFICATIONS_OPTIONS_MASK_POSTPONE_VALUE) >> _TodoNotes_Modals_.#NOTIFICATIONS_OPTIONS_MASK_POSTPONE_VALUE_IX)
        .trigger('change');
}

//------------------------------------------------
static #NotificationsSetupHandlers() {
    // postpone number changed
    $("[id^=postpone_value_NotificationsSetup]").on("change", function () {
        _TodoNotes_Modals_.#UpdateNotificationsSetupPostponeTime();
    });

    // postpone type changed
    $("[id^=postpone_type_NotificationsSetup]").on("change", function () {
        _TodoNotes_Modals_.#UpdateNotificationsSetupPostponeTime();
    });

    // postpone checkbox changed
    $("[id^=postpone_NotificationsSetup]").on("change", function () {
        const postpone = $(this).is(':checked');
        $("#form-alert_time_NotificationsSetup").prop('disabled', postpone);
    });

    // alert time input changed
    $("[id^=form-alert_time_NotificationsSetup]").on("change", function () {
        $("#postpone_options_NotificationsSetup").addClass('hideMe');
    });

    // remind before/after checkboxes
    $("[id^=alert_before_NotificationsSetup]").on("change", function () {
        _TodoNotes_Modals_.#UpdateNotificationsSetupAlertOptions();
    });
    $("[id^=alert_after_NotificationsSetup]").on("change", function () {
        _TodoNotes_Modals_.#UpdateNotificationsSetupAlertOptions();
    });
}

//------------------------------------------------
static NotificationsSetup(project_id, id, user_id, notifications_alert_timestring, notifications_alert_timestamp, notification_options_bitflags) {
    $.ajaxSetup ({
        cache: false
    });

    $("#note_title_NotificationsSetup").text($("#noteTitleLabel-P" + project_id + "-" + id).text());
    $("#form-alert_time_NotificationsSetup").val(notifications_alert_timestring);
    $("#postpone_base_NotificationsSetup").val(notifications_alert_timestamp);
    _TodoNotes_Modals_.#UpdateNotificationsSetupPostponeTime();
    _TodoNotes_Modals_.#NotificationsOptionsFromBitflags(notification_options_bitflags);

    $("#dialogNotificationsSetup").removeClass( 'hideMe' );
    $("#dialogNotificationsSetup").dialog({
        resizable: false,
        width: "auto",
        height: "auto",
        modal: true,
        buttons: [
            {
                text : _TodoNotes_Translations_.GetTranslationExportToJS('TodoNotes__JS_DIALOG_SET_BTN'),
                click: function() {
                    const postpone = $("#postpone_NotificationsSetup").is(':checked');
                    const new_notifications_alert_timestring = $("#form-alert_time_NotificationsSetup").val();
                    const postpone_notifications_alert_timestring = $("#postpone_time_NotificationsSetup").text();
                    _TodoNotes_Requests_.UpdateNoteNotificationsAlertTimeAndOptions(project_id, user_id, id,
                        postpone ? postpone_notifications_alert_timestring : new_notifications_alert_timestring,
                        _TodoNotes_Modals_.#NotificationsOptionsToBitflags());
                    $( this ).dialog( "close" );
                }
            },
            {
                text : _TodoNotes_Translations_.GetTranslationExportToJS('TodoNotes__JS_DIALOG_RESET_BTN'),
                click: function() {
                    _TodoNotes_Requests_.UpdateNoteNotificationsAlertTimeAndOptions(project_id, user_id, id, '', 0); // empty timestring, empty options
                    $( this ).dialog( "close" );
                }
            },
            {
                text : _TodoNotes_Translations_.GetTranslationExportToJS('TodoNotes__JS_DIALOG_CANCEL_BTN'),
                click: function() { $( this ).dialog( "close" ); }
            },
            // {
            //     text : 'Test',
            //     click: function() {
            //         _TodoNotes_Requests_.TestNoteNotifications(project_id, user_id, id);
            //         $( this ).dialog( "close" );
            //     }
            // },
        ]
    });
    return false;
}

//---------------------------------------------
// Stats & Report modal dialogs
//---------------------------------------------

//------------------------------------------------
static Stats(project_id, user_id) {
    $.ajaxSetup ({
        cache: false
    });
    const loadUrl = '/?controller=TodoNotesController&action=ShowStats&plugin=TodoNotes'
                + '&project_custom_id=' + project_id
                + '&user_id=' + user_id;
    $("#dialogStatsInside").html(_TodoNotes_Translations_.msgLoadingSpinner);
    $("#dialogStatsInside").load(loadUrl,
        function() {
            _TodoNotes_Stats_.prepareDocument();
        });

    $("#dialogStats").removeClass( 'hideMe' );
    $("#dialogStats").dialog({
        resizable: false,
        height: "auto",
        modal: true,
        buttons: [
            {
                text : _TodoNotes_Translations_.GetTranslationExportToJS('TodoNotes__JS_DIALOG_CLOSE_BTN'),
                click: function() { $( this ).dialog( "close" ); }
            },
        ]
    });
}

//------------------------------------------------
static Report(project_id, user_id) {
    $.ajaxSetup ({
        cache: false
    });
    $("#dialogReport-P" + project_id).removeClass( 'hideMe' );
    $("#dialogReport-P" + project_id).dialog({
        resizable: false,
        height: "auto",
        modal: true,
        buttons: [
            {
                text : _TodoNotes_Translations_.GetTranslationExportToJS('TodoNotes__JS_DIALOG_CREATE_BTN'),
                click: function() {
                    const category = $("#catReport-P" + project_id + " option:selected").text();
                    const loadUrl = "/?controller=TodoNotesController&action=ShowReport&plugin=TodoNotes"
                                + "&project_custom_id=" + project_id
                                + "&user_id=" + user_id
                                + "&category=" + encodeURIComponent(category);
                    $("#result" + project_id).html(_TodoNotes_Translations_.msgLoadingSpinner);
                    $("#result" + project_id).load(loadUrl,
                        function() {
                            _TodoNotes_Report_.prepareDocument();
                        });
                    $( this ).dialog( "close" );
                }
            },
            {
                text : _TodoNotes_Translations_.GetTranslationExportToJS('TodoNotes__JS_DIALOG_CANCEL_BTN'),
                click: function() { $( this ).dialog( "close" ); }
            },
        ]
    });
    return true;
}

//---------------------------------------------
// Dashboard system modal dialogs
//---------------------------------------------

//------------------------------------------------
static ReindexNotesAndLists(user_id,) {
    $("#dialogReindexNotesAndLists").removeClass( 'hideMe' );
    $("#dialogReindexNotesAndLists").dialog({
        resizable: false,
        height: "auto",
        modal: true,
        buttons: [
            {
                text : _TodoNotes_Translations_.GetTranslationExportToJS('TodoNotes__JS_DIALOG_REINDEX_BTN'),
                click : function() {
                    _TodoNotes_Requests_.ReindexNotesAndLists(user_id);
                    $( this ).dialog( "close" );
                },
            },
            {
                text : _TodoNotes_Translations_.GetTranslationExportToJS('TodoNotes__JS_DIALOG_CANCEL_BTN'),
                click : function() {
                    $( this ).dialog( "close" );
                }
            },
        ]
    });
    return false;
}

//---------------------------------------------
// Lists related modal dialogs
//---------------------------------------------

//------------------------------------------------
static CreateCustomNoteList(user_id) {
    $("#dialogCreateCustomNoteList").removeClass( 'hideMe' );
    $("#dialogCreateCustomNoteList").dialog({
        resizable: false,
        height: "auto",
        modal: true,
        buttons: [
            {
                text : _TodoNotes_Translations_.GetTranslationExportToJS('TodoNotes__JS_DIALOG_CREATE_BTN'),
                click : function() {
                    const custom_note_list_name = $("#nameCreateCustomNoteList").val().trim();
                    const custom_note_list_is_global = $("#globalCreateCustomNoteList").is(':checked');
                    _TodoNotes_Requests_.CreateCustomNoteList(user_id, custom_note_list_name, custom_note_list_is_global);
                    $( this ).dialog( "close" );
                },
            },
            {
                text : _TodoNotes_Translations_.GetTranslationExportToJS('TodoNotes__JS_DIALOG_CANCEL_BTN'),
                click : function() {
                    $( this ).dialog( "close" );
                }
            },
        ]
    });
    return false;
}

//------------------------------------------------
static RenameCustomNoteList(user_id, project_id, default_name) {
    $("#nameRenameCustomNoteList").val(default_name);
    $("#dialogRenameCustomNoteList").removeClass( 'hideMe' );
    $("#dialogRenameCustomNoteList").dialog({
        resizable: false,
        height: "auto",
        modal: true,
        buttons: [
            {
                text : _TodoNotes_Translations_.GetTranslationExportToJS('TodoNotes__JS_DIALOG_RENAME_BTN'),
                click : function() {
                    const custom_note_list_name = $("#nameRenameCustomNoteList").val().trim();
                    _TodoNotes_Requests_.RenameCustomNoteList(user_id, project_id, custom_note_list_name);
                    $( this ).dialog( "close" );
                },
            },
            {
                text : _TodoNotes_Translations_.GetTranslationExportToJS('TodoNotes__JS_DIALOG_CANCEL_BTN'),
                click : function() {
                    $( this ).dialog( "close" );
                }
            },
        ]
    });
    return false;
}

//------------------------------------------------
static DeleteCustomNoteList(user_id, project_id) {
    $("#dialogDeleteCustomNoteList").removeClass( 'hideMe' );
    $("#dialogDeleteCustomNoteList").dialog({
        resizable: false,
        height: "auto",
        modal: true,
        buttons: [
            {
                text : _TodoNotes_Translations_.GetTranslationExportToJS('TodoNotes__JS_DIALOG_DELETE_BTN'),
                click : function() {
                    _TodoNotes_Requests_.DeleteCustomNoteList(user_id, project_id);
                    $( this ).dialog( "close" );
                },
            },
            {
                text : _TodoNotes_Translations_.GetTranslationExportToJS('TodoNotes__JS_DIALOG_CANCEL_BTN'),
                click : function() {
                    $( this ).dialog( "close" );
                }
            },
        ]
    });
    return false;
}

//------------------------------------------------
static ReorderCustomNoteList(user_id, order) {
    $("#dialogReorderCustomNoteList").removeClass( 'hideMe' );
    $("#dialogReorderCustomNoteList").dialog({
        resizable: false,
        height: "auto",
        modal: true,
        buttons: [
            {
                text : _TodoNotes_Translations_.GetTranslationExportToJS('TodoNotes__JS_DIALOG_REORDER_BTN'),
                click : function() {
                    _TodoNotes_Requests_.UpdateCustomNoteListsPositions(user_id, order);
                    $( this ).dialog( "close" );
                },
            },
            {
                text : _TodoNotes_Translations_.GetTranslationExportToJS('TodoNotes__JS_DIALOG_CANCEL_BTN'),
                click : function() {
                    $( this ).dialog( "close" );
                    _TodoNotes_Requests_.RefreshTabs(user_id);
                }
            },
        ]
    });
    return false;
}

//------------------------------------------------
static AttachAllHandlers() {
    _TodoNotes_Modals_.#NotificationsSetupHandlers();
}

//------------------------------------------------

} // class _TodoNotes_Modals_

//////////////////////////////////////////////////
$(function() {
    _TodoNotes_Modals_.AttachAllHandlers();
});

//////////////////////////////////////////////////
