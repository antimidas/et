function SmileIT(e, t, o) {
    document.forms[t].elements[o].value = document.forms[t].elements[o].value + " " + e + " ", document.forms[t].elements[o].focus()
}

function PopMoreSmiles(e, t) {
    link = "moresmiles.php?form=" + e + "&text=" + t, newWin = window.open(link, "moresmile", "height=500,width=450,resizable=no,scrollbars=1"), window.focus && newWin.focus()
}

function PopCustomSmiles(e, t) {
    link = "moresmilies_custom.php?form=" + e + "&text=" + t, newWin = window.open(link, "moresmilecustom", "height=600,width=400,resizable=yes,scrollbars=1"), window.focus && newWin.focus()
}

function PopStaffSmiles(e, t) {
    link = "staff_smilies.php?form=" + e + "&text=" + t, newWin = window.open(link, "staffsmile", "height=600,width=600,resizable=yes,scrollbars=1"), window.focus && newWin.focus()
}

function popUp(URL) {
    day = new Date, id = day.getTime(), eval("page" + id + " = window.open(URL, '" + id + "', 'toolbar=0,scrollbars=1,location=0,statusbar=0,menubar=0,resizable=1,width=740,height=380,left = 340,top = 280');")
}

function mysubmit() {
    0 == $("input[type='text'][name='shbox_text']").val().length && (alert("Shout cannot be empty"), exit()), setTimeout("document.shbox.reset()", 100)
}

function staff_mysubmit() {
    0 == $("input[type='text'][name='staff_shbox_text']").val().length && (alert("Shout cannot be empty"), exit()), setTimeout("document.staff_shbox.reset()", 100)
}

function confirm_delete() {
    confirm("Are you sure you want to do this ?") && confirm("Are you 100% sure ?") && (alert("Your are sure!"), self.location.href = "./shoutbox.php?delall")
}

function private_reply(e, t, o) {
    document.forms[t].elements[o].value = "/private " + e + " ", document.forms[t].elements[o].focus()
}